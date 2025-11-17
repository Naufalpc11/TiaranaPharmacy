<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\MedicationCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GeminiChatController extends Controller
{
    protected MedicationCatalog $medicationCatalog;

    public function __construct(MedicationCatalog $medicationCatalog)
    {
        $this->medicationCatalog = $medicationCatalog;
    }
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Login dibutuhkan untuk melihat riwayat percakapan.');
        }

        $conversations = $user->chatConversations()
            ->latest('last_interacted_at')
            ->latest('updated_at')
            ->get()
            ->map(fn (ChatConversation $conversation) => [
                'id' => $conversation->id,
                'title' => $conversation->title ?? 'Percakapan tanpa judul',
                'last_interacted_at' => optional($conversation->last_interacted_at ?? $conversation->updated_at)->toIso8601String(),
                'model' => $conversation->model,
            ]);

        return response()->json([
            'data' => $conversations,
        ]);
    }

    public function show(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->assertOwner($request, $conversation);

        $conversation->load([
            'messages' => fn ($query) => $query->orderBy('created_at'),
        ]);

        return response()->json([
            'data' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'model' => $conversation->model,
                'messages' => $conversation->messages->map(fn (ChatMessage $message) => [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'metadata' => $message->metadata,
                    'created_at' => optional($message->created_at)->toIso8601String(),
                ]),
            ],
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'in:user,assistant,system'],
            'messages.*.content' => ['required', 'string'],
            'conversation_id' => ['nullable', 'integer', 'exists:chat_conversations,id'],
        ]);

        $user = $request->user();

        $conversation = null;
        $model = config('services.gemini.model', 'gemini-1_5-flash');

        if ($conversationId = $validated['conversation_id'] ?? null) {
            /** @var ChatConversation|null $conversation */
            $conversation = ChatConversation::query()->find($conversationId);

            if (! $conversation) {
                abort(Response::HTTP_NOT_FOUND, 'Percakapan tidak ditemukan.');
            }

            if ($conversation->user_id && (! $user || $conversation->user_id !== $user->id)) {
                abort(Response::HTTP_FORBIDDEN, 'Percakapan tidak ditemukan atau bukan milik Anda.');
            }
        }

        $latestUserMessage = $this->extractLatestUserMessage(collect($validated['messages']));

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user?->id,
                'title' => $latestUserMessage ? Str::limit($latestUserMessage['content'], 80) : 'Percakapan baru',
                'model' => $model,
                'last_interacted_at' => now(),
            ]);
        }

        $ruleBasedResponse = $this->handleStockIntent($latestUserMessage)
            ?? $this->handleMedicationIntent($latestUserMessage);

        if ($ruleBasedResponse) {
            $payload = $this->persistAndTransformResponse(
                $conversation,
                $latestUserMessage,
                $ruleBasedResponse['content'],
                $ruleBasedResponse['metadata'] ?? [],
                $model
            );

            return response()->json([
                'data' => $payload,
            ]);
        }

        $payload = $this->buildGeminiPayload($validated['messages']);
        $responseData = $this->callGemini($payload, $model);

        $assistantReply = data_get($responseData, 'candidates.0.content.parts.0.text');

        if (! is_string($assistantReply) || trim($assistantReply) === '') {
            report(new \RuntimeException('Gemini tidak mengembalikan respons yang valid.'));

            abort(Response::HTTP_BAD_GATEWAY, 'Maaf, asisten sedang sibuk. Coba beberapa saat lagi.');
        }

        $assistantReply = $this->sanitizeReply($assistantReply);

        $payload = $this->persistAndTransformResponse(
            $conversation,
            $latestUserMessage,
            $assistantReply,
            [],
            $model
        );

        return response()->json([
            'data' => $payload,
        ]);
    }

    public function destroy(Request $request, ChatConversation $conversation): JsonResponse
    {
        $user = $request->user();

        if ($conversation->user_id && (! $user || $conversation->user_id !== $user->id)) {
            abort(Response::HTTP_FORBIDDEN, 'Percakapan tidak ditemukan atau bukan milik Anda.');
        }

        $conversation->delete();

        return response()->json([
            'message' => 'Percakapan dihapus.',
        ]);
    }

    protected function callGemini(array $payload, string $model): array
    {
        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Gemini API belum dikonfigurasi.');
        }

        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $apiKey
        );

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);
        } catch (\Throwable $exception) {
            report($exception);

            abort(Response::HTTP_BAD_GATEWAY, 'Tidak dapat menghubungi layanan Gemini saat ini.');
        }

        if ($response->failed()) {
            report(new \RuntimeException('Gemini API error: '.$response->body()));

            abort(Response::HTTP_BAD_GATEWAY, 'Terjadi kesalahan saat menghubungkan ke Gemini.');
        }

        return $response->json() ?? [];
    }

    protected function buildGeminiPayload(array $messages): array
    {
        $contents = collect($messages)
            ->map(function (array $message) {
                $role = $message['role'] === 'assistant' ? 'model' : $message['role'];

                return [
                    'role' => $role,
                    'parts' => [
                        ['text' => $message['content']],
                    ],
                ];
            })
            ->all();

        $payload = [
            'contents' => $contents,
        ];

        if ($systemInstruction = config('services.gemini.system_instruction')) {
            $payload['systemInstruction'] = [
                'role' => 'system',
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        if ($safetySettings = config('services.gemini.safety_settings')) {
            $payload['safetySettings'] = $safetySettings;
        }

        return $payload;
    }

    protected function extractLatestUserMessage(Collection $messages): ?array
    {
        /** @var array|null $message */
        $message = $messages->reverse()->first(fn (array $item) => $item['role'] === 'user');

        return $message;
    }

    protected function assertOwner(Request $request, ChatConversation $conversation): void
    {
        $user = $request->user();

        if (! $user || $conversation->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Percakapan tidak ditemukan atau bukan milik Anda.');
        }
    }

    protected function sanitizeReply(string $text): string
    {
        $patterns = [
            '/\*\*(.*?)\*\*/s',
            '/__(.*?)__/s',
        ];

        $cleaned = preg_replace($patterns, '$1', $text) ?? $text;

        return str_replace(['**', '__'], '', $cleaned);
    }

    protected function handleStockIntent(?array $latestUserMessage): ?array
    {
        if (! $latestUserMessage) {
            return null;
        }

        $text = Str::lower($latestUserMessage['content'] ?? '');
        $keywords = [
            'stok',
            'stock',
            'tersedia',
            'ketersediaan',
            'ready stock',
            'ada obat apa',
            'obat apa saja',
            'obat tersedia',
            'ketersediaan obat',
            'available medicine',
            'medicine list',
        ];

        $isStockQuestion = collect($keywords)->contains(function (string $keyword) use ($text) {
            return Str::contains($text, $keyword);
        });

        if (! $isStockQuestion) {
            return null;
        }

        return [
            'content' => 'Untuk memastikan ketersediaan stok terbaru, silakan hubungi apoteker kami melalui halaman Kontak. Tim kami akan melakukan pengecekan fisik sebelum memberikan rekomendasi pembelian.',
            'metadata' => [
                'intent' => 'contact-pharmacist',
                'cta' => $this->buildContactCta(),
                'source' => 'contact-policy',
            ],
        ];
    }

    protected function handleMedicationIntent(?array $latestUserMessage): ?array
    {
        if (! $latestUserMessage) {
            return null;
        }

        $medication = $this->medicationCatalog->matchByQuery($latestUserMessage['content'] ?? '');

        if (! $medication) {
            return null;
        }

        $details = $this->medicationCatalog->formatDetails($medication);

        return [
            'content' => $this->buildMedicationReplyText($details),
            'metadata' => [
                'intent' => 'medication-recommendation',
                'medication' => $details,
                'cta' => $this->buildContactCta(),
                'source' => 'catalog',
            ],
        ];
    }

    protected function buildMedicationReplyText(array $details): string
    {
        $parts = [];
        $parts[] = sprintf(
            '%s adalah %s berbentuk %s. %s',
            $details['name'],
            Str::lower($details['category']),
            $details['form'],
            $details['how_it_works']
        );

        if (! empty($details['dosage'])) {
            $dosageLines = collect($details['dosage'])
                ->map(fn ($value, $key) => sprintf('- %s: %s', $key, $value))
                ->implode("\n");
            $parts[] = "Aturan pakai:\n{$dosageLines}";
        }

        if (! empty($details['warnings'])) {
            $warningLines = collect($details['warnings'])
                ->map(fn (string $warning) => '- '.$warning)
                ->implode("\n");
            $parts[] = "Perhatian:\n{$warningLines}";
        }

        $parts[] = 'Untuk pembelian dan konfirmasi stok, hubungi apoteker melalui tombol Kontak.';

        return implode("\n\n", array_filter($parts));
    }

    protected function buildContactCta(): array
    {
        return [
            'label' => 'Hubungi Apoteker',
            'url' => url('/contact'),
        ];
    }

    protected function persistAndTransformResponse(
        ChatConversation $conversation,
        ?array $latestUserMessage,
        string $assistantReply,
        array $metadata,
        string $model
    ): array {
        if ($latestUserMessage) {
            $conversation->messages()->create([
                'role' => 'user',
                'content' => $latestUserMessage['content'],
            ]);
        }

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $assistantReply,
            'metadata' => array_merge(['model' => $model], $metadata ?: []),
        ]);

        $conversation->forceFill([
            'model' => $model,
            'last_interacted_at' => now(),
        ]);

        if ($latestUserMessage && ! $conversation->title) {
            $conversation->title = Str::limit($latestUserMessage['content'], 80);
        }

        $conversation->save();

        return [
            'reply' => $assistantReply,
            'message' => [
                'content' => $assistantReply,
                'metadata' => $metadata,
            ],
            'conversation_id' => $conversation->id,
        ];
    }
}
