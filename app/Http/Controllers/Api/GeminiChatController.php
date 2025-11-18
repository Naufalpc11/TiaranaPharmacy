<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\MedicationChatService;
use App\Support\AgeRange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GeminiChatController extends Controller
{
    public function __construct(protected MedicationChatService $medicationChatService)
    {
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
            'messages.*.metadata' => ['nullable', 'array'],
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

        $messagesCollection = collect($validated['messages']);
        $latestUserMessage = $this->extractLatestUserMessage($messagesCollection);

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user?->id,
                'title' => $latestUserMessage ? Str::limit($latestUserMessage['content'], 80) : 'Percakapan baru',
                'model' => $model,
                'last_interacted_at' => now(),
            ]);
        }

        $assistantReply = null;
        $messageMetadata = [];
        $medicationDecision = $this->medicationChatService->handle($messagesCollection);

        if ($medicationDecision) {
            [$assistantReply, $messageMetadata] = $this->handleMedicationDecision($medicationDecision, $model);
        }

        if (! $assistantReply) {
            $payload = $this->buildGeminiPayload($validated['messages']);
            $responseData = $this->callGemini($payload, $model);

            $assistantReply = data_get($responseData, 'candidates.0.content.parts.0.text');

            if (! is_string($assistantReply) || trim($assistantReply) === '') {
                report(new \RuntimeException('Gemini tidak mengembalikan respons yang valid.'));

                abort(Response::HTTP_BAD_GATEWAY, 'Maaf, asisten sedang sibuk. Coba beberapa saat lagi.');
            }

            $assistantReply = $this->sanitizeReply($assistantReply);
            $messageMetadata = ['model' => $model];
        }

        if ($conversation && $latestUserMessage) {
            $conversation->messages()->create([
                'role' => 'user',
                'content' => $latestUserMessage['content'],
                'metadata' => $latestUserMessage['metadata'] ?? null,
            ]);
        }

        if ($conversation) {
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $assistantReply,
                'metadata' => $this->prepareStoredMetadata($messageMetadata),
            ]);

            $conversation->forceFill([
                'model' => $model,
                'last_interacted_at' => now(),
            ]);

            if ($latestUserMessage && ! $conversation->title) {
                $conversation->title = Str::limit($latestUserMessage['content'], 80);
            }

            $conversation->save();
        }

        return response()->json([
            'data' => [
                'reply' => $assistantReply,
                'message' => [
                    'content' => $assistantReply,
                    'metadata' => $messageMetadata ?: null,
                ],
                'conversation_id' => $conversation?->id,
            ],
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

    protected function handleMedicationDecision(array $decision, string $model): array
    {
        $type = $decision['type'] ?? null;

        if (! $type) {
            return [null, []];
        }

        if (in_array($type, ['ask_age', 'ask_age_followup'], true)) {
            $reply = trim((string) ($decision['reply'] ?? ''));

            if ($reply === '') {
                return [null, []];
            }

            $metadata = ($decision['metadata'] ?? []) + [
                'intent' => 'ask_age',
                'source' => 'medication_flow',
            ];

            return [$reply, $metadata];
        }

        if ($type === 'no_match') {
            $reply = trim((string) ($decision['reply'] ?? 'Maaf, dataset obat belum sesuai dengan permintaan tersebut.'));

            $metadata = ($decision['metadata'] ?? []) + [
                'intent' => 'medication_not_found',
                'source' => 'medication_flow',
            ];

            return [$reply, $metadata];
        }

        if ($type === 'recommendation') {
            $reply = $this->buildMedicationRecommendationReply($decision, $model);

            $metadata = [
                'intent' => 'medication_recommendation',
                'symptom_label' => $decision['symptom_label'] ?? null,
                'age_label' => $decision['age_label'] ?? null,
                'medication' => $decision['formatted_medication'] ?? null,
                'source' => 'medication_flow',
                'model' => $model,
            ];

            return [$reply, $metadata];
        }

        return [null, []];
    }

    protected function buildMedicationRecommendationReply(array $decision, string $model): string
    {
        $medication = $decision['formatted_medication'] ?? [];
        $symptom = $decision['symptom_label'] ?? ($medication['category'] ?? 'keluhan pengguna');
        $ageLabel = $decision['age_label'] ?? ($medication['age_group'] ?? null);
        $ageDescription = AgeRange::describe($ageLabel);
        $userQuery = $decision['user_query'] ?? '';
        $name = $medication['name'] ?? 'obat rekomendasi';
        $function = $medication['category'] ?? '';
        $form = $medication['form'] ?? 'sediaan';
        $notes = trim((string) ($medication['notes'] ?? ''));
        $stockStatus = $medication['stock_status'] ?? '';

        $notesText = $notes !== '' ? $notes : 'Tidak ada catatan khusus dari admin.';
        $stockText = $stockStatus !== '' ? $stockStatus : 'Hubungi apoteker kami untuk memastikan ketersediaannya.';

        $prompt = <<<PROMPT
Kamu adalah apoteker virtual Tiarana Pharmacy.
Pengguna bertanya: {$userQuery}
Keluhan utama: {$symptom}
Rentang usia pasien: {$ageDescription}
Rekomendasi internal:
- Nama obat: {$name}
- Fungsi utama: {$function}
- Bentuk sediaan: {$form}
- Catatan admin: {$notesText}
- Status stok: {$stockText}

Susun jawaban maksimal 4 paragraf pendek atau bullet yang mencakup:
1. Mengapa obat tersebut cocok untuk keluhan dan usia tersebut.
2. Cara pakai secara umum (ingatkan untuk mengikuti aturan pakai di kemasan serta konsultasi apoteker).
3. Peringatan kapan harus ke dokter atau rumah sakit.
Gunakan bahasa Indonesia yang ramah dan akhiri dengan ajakan untuk menghubungi apoteker bila masih ragu.
PROMPT;

        $payload = $this->buildGeminiPayload([
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ]);

        try {
            $responseData = $this->callGemini($payload, $model);
            $reply = data_get($responseData, 'candidates.0.content.parts.0.text');

            if (is_string($reply) && trim($reply) !== '') {
                return $this->sanitizeReply($reply);
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return $this->buildFallbackMedicationReply($medication, $symptom, $ageLabel);
    }

    protected function buildFallbackMedicationReply(array $medication, ?string $symptom, ?string $ageLabel): string
    {
        $name = $medication['name'] ?? 'obat rekomendasi';
        $form = $medication['form'] ?? 'sediaan';
        $function = $medication['category'] ?? 'penanganan keluhan';
        $symptomText = $symptom ?: $function;
        $ageText = AgeRange::describe($ageLabel);

        return sprintf(
            'Untuk keluhan %s pada %s, kami menyarankan %s (%s) yang berfungsi sebagai %s. Ikuti petunjuk di kemasan dan konsultasikan kembali dengan apoteker jika gejala tidak membaik atau justru memburuk.',
            Str::lower($symptomText),
            Str::lower($ageText),
            $name,
            $form,
            Str::lower($function ?: 'obat pendukung keluhan tersebut')
        );
    }

    protected function prepareStoredMetadata(?array $metadata): ?array
    {
        if (! $metadata) {
            return null;
        }

        $filtered = [];

        foreach ($metadata as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                if (empty($value)) {
                    continue;
                }

                $filtered[$key] = $value;
                continue;
            }

            if ($value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered ?: null;
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

            abort(Response::HTTP_BAD_GATEWAY, 'Tidak dapat menghubungi layanan AI saat ini.');
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
}
