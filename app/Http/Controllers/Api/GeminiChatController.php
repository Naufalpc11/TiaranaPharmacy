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

        $ruleBasedResponse = $this->handleMedicationIntent($conversation, $latestUserMessage);

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

    protected function handleMedicationIntent(ChatConversation $conversation, ?array $latestUserMessage): ?array
    {
        if (! $latestUserMessage) {
            return null;
        }

        $content = trim($latestUserMessage['content'] ?? '');

        if ($content === '') {
            return null;
        }

        $state = $this->extractConversationState($conversation);

        if (($state['pending'] ?? null) !== null) {
            return $this->handlePendingState($state, $content);
        }

        $analysis = $this->analyzeMedicationQuery($content);

        if ($analysis['needs_age']) {
            $newState = [
                'pending' => 'age',
                'topic' => 'demam',
                'base_query' => $content,
            ];

            return $this->respondWithClarification(
                'Untuk demam usia berapa? Sebutkan usia pasien (contoh: 1 tahun, 5 tahun, 25 tahun).',
                [],
                $newState
            );
        }

        if ($analysis['needs_symptom']) {
            $newState = [
                'pending' => 'symptom',
                'topic' => 'batuk',
                'base_query' => $content,
            ];

            return $this->respondWithClarification(
                'Batuknya kering atau berdahak? Sebutkan jenis batuknya.',
                ['batuk kering', 'batuk berdahak'],
                $newState
            );
        }

        if ($analysis['needs_form']) {
            $forms = $this->medicationCatalog->availableForms($analysis['symptom']);

            if (count($forms) <= 1) {
                $analysis['form'] = $forms[0] ?? null;
            } else {
                $newState = [
                    'pending' => 'form',
                    'topic' => $analysis['symptom'],
                    'symptom' => $analysis['symptom'],
                    'base_query' => $content,
                    'forms' => $forms,
                ];

                return $this->respondWithClarification(
                    'Ingin bentuk obat apa? (sirup atau tablet)',
                    $forms,
                    $newState
                );
            }
        }

        $filters = array_filter([
            'symptom' => $analysis['symptom'] ?? null,
            'form' => $analysis['form'] ?? null,
            'age_group' => $analysis['age_group'] ?? null,
            'product' => $analysis['product'] ?? null,
        ]);

        $candidates = $this->medicationCatalog->matchCandidates($content, $filters, 4);

        if ($candidates->count() > 1 && empty($analysis['symptom']) && empty($filters['form'])) {
            $newState = [
                'pending' => 'variant',
                'base_query' => $content,
                'options' => $candidates->all(),
            ];

            $optionsText = $candidates
                ->map(fn (array $item) => trim(sprintf('%s - %s (%s)', $item['name'] ?? 'Produk', $item['category'] ?? '', $item['form'] ?? '')))
                ->implode("\n- ");

            return [
                'content' => "Ada beberapa pilihan yang sesuai:\n- {$optionsText}\nSebutkan nama atau bentuk yang Anda maksud.",
                'metadata' => [
                    'intent' => 'clarification',
                    'state' => $newState,
                ],
            ];
        }

        $medication = $candidates->first();

        if (! $medication) {
            return null;
        }

        return $this->buildMedicationResponsePayload($medication);
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

        $retryDelayMs = 600;
        $deadline = microtime(true) + 10;

        while (true) {
            try {
                $response = Http::timeout(20)
                    ->acceptJson()
                    ->asJson()
                    ->post($endpoint, $payload);
            } catch (\Throwable $exception) {
                if (microtime(true) >= $deadline) {
                    report($exception);

                    abort(Response::HTTP_BAD_GATEWAY, 'Tidak dapat menghubungi layanan Gemini saat ini.');
                }

                usleep($retryDelayMs * 1000);
                continue;
            }

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $shouldRetry = in_array($response->status(), [429, 500, 502, 503, 504], true);

            if (! $shouldRetry || microtime(true) >= $deadline) {
                report(new \RuntimeException('Gemini API error: '.$response->body()));

                abort(Response::HTTP_BAD_GATEWAY, 'Terjadi kesalahan saat menghubungkan ke Gemini.');
            }

            usleep($retryDelayMs * 1000);
        }
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
    protected function handlePendingState(array $state, string $content): ?array
    {
        $pending = $state['pending'] ?? null;
        $normalized = Str::lower($content);

        if ($pending === 'age') {
            $ageGroup = $this->detectAgeGroup($normalized);

            if (! $ageGroup) {
                return $this->respondWithClarification(
                    'Saya belum menangkap usianya. Sebutkan usia pasien, misalnya "1 tahun", "5 tahun", atau "20 tahun".',
                    [],
                    $state
                );
            }

            $state['age_group'] = $ageGroup;
            $state['pending'] = null;

            return $this->buildMedicationResponseFromState($state);
        }

        if ($pending === 'symptom') {
            $symptom = $this->detectSymptom($normalized);

            if (! $symptom || $symptom === 'batuk') {
                return $this->respondWithClarification(
                    'Saya belum menangkap jenis batuknya. Batuk kering atau berdahak?',
                    ['batuk kering', 'batuk berdahak'],
                    $state
                );
            }

            $state['symptom'] = $symptom;
            $state['pending'] = 'form';

            $forms = $state['forms'] ?? $this->medicationCatalog->availableForms($symptom);

            if (count($forms) <= 1) {
                $state['pending'] = null;
                $state['form'] = $forms[0] ?? null;

                return $this->buildMedicationResponseFromState($state);
            }

            $state['forms'] = $forms;

            return $this->respondWithClarification(
                'Ingin bentuk sirup atau tablet?',
                $forms,
                $state
            );
        }

        if ($pending === 'form') {
            $form = $this->detectForm($normalized);

            if (! $form) {
                $options = $state['forms'] ?? ['sirup', 'tablet'];

                return $this->respondWithClarification(
                    'Silakan pilih bentuk obatnya (contoh: sirup atau tablet).',
                    $options,
                    $state
                );
            }

            $state['form'] = $form;
            $state['pending'] = null;

            return $this->buildMedicationResponseFromState($state);
        }

        if ($pending === 'variant') {
            return $this->handleVariantSelection($state, $normalized);
        }

        return null;
    }

    protected function buildMedicationResponseFromState(array $state): ?array
    {
        $filters = array_filter([
            'symptom' => $state['symptom'] ?? null,
            'form' => $state['form'] ?? null,
            'age_group' => $state['age_group'] ?? null,
        ]);

        $medication = $this->medicationCatalog->matchByQuery($state['base_query'] ?? null, $filters);

        if (! $medication) {
            return [
                'content' => 'Maaf, kombinasi tersebut belum tersedia di dataset kami. Saya tetap bisa memberikan saran umum atau Anda dapat menghubungi apoteker melalui halaman Kontak.',
                'metadata' => [
                    'intent' => 'medication-recommendation',
                    'cta' => $this->buildContactCta(),
                    'state' => ['pending' => null],
                ],
            ];
        }

        return $this->buildMedicationResponsePayload($medication, ['pending' => null]);
    }

    protected function buildMedicationResponsePayload(array $medication, array $state = []): array
    {
        $details = $this->medicationCatalog->formatDetails($medication);

        return [
            'content' => $this->buildMedicationReplyText($details),
            'metadata' => [
                'intent' => 'medication-recommendation',
                'medication' => $details,
                'cta' => $this->buildContactCta(),
                'source' => 'catalog',
                'state' => $state ?: ['pending' => null],
            ],
        ];
    }

    protected function respondWithClarification(string $message, array $options, array $state): array
    {
        $optionsText = implode(', ', array_unique(array_filter($options)));

        return [
            'content' => $optionsText ? "{$message}\nPilihan: {$optionsText}" : $message,
            'metadata' => [
                'intent' => 'clarification',
                'state' => $state,
            ],
        ];
    }

    protected function analyzeMedicationQuery(string $content): array
    {
        $symptom = $this->detectSymptom($content);
        $form = $this->detectForm($content);
        $ageGroup = $this->detectAgeGroup($content);

        $needsSymptom = false;
        $needsForm = false;

        if (! $symptom && Str::contains(Str::lower($content), 'batuk')) {
            $needsSymptom = true;
        }

        if ($symptom && Str::contains($symptom, 'batuk') && ! $form) {
            $needsForm = true;
        }

        return [
            'symptom' => $symptom,
            'form' => $form,
            'needs_symptom' => $needsSymptom,
            'needs_form' => $needsForm,
            'needs_age' => ($symptom === 'demam' && ! $ageGroup && Str::contains(Str::lower($content), 'demam')),
            'age_group' => $ageGroup,
        ];
    }

    protected function detectSymptom(string $content): ?string
    {
        $text = Str::lower($content);
        $map = [
            'batuk berdahak' => ['batuk berdahak', 'berdahak', 'dahak'],
            'batuk kering' => ['batuk kering', 'kering', 'keringnya'],
            'demam' => ['demam', 'panas', 'suhu tinggi'],
            'flu' => ['flu', 'pilek'],
        ];

        foreach ($map as $symptom => $keywords) {
            foreach ($keywords as $keyword) {
                if ($keyword !== '' && Str::contains($text, $keyword)) {
                    return $symptom;
                }
            }
        }

        return Str::contains($text, 'batuk') ? 'batuk' : null;
    }

    protected function detectAgeGroup(string $content): ?string
    {
        $text = Str::lower($content);
        $ageMatches = [];

        if (preg_match('/(\d{1,3})\s*tahun/', $text, $ageMatches)) {
            $age = (int) $ageMatches[1];
        } elseif (preg_match('/(\d{1,3})\s*th/', $text, $ageMatches)) {
            $age = (int) $ageMatches[1];
        } else {
            $digits = (int) filter_var($text, FILTER_SANITIZE_NUMBER_INT);
            $age = $digits > 0 ? $digits : null;
        }

        if ($age === null) {
            return null;
        }

        if ($age < 2) {
            return 'bayi';
        }

        if ($age >= 2 && $age <= 11) {
            return 'anak';
        }

        return 'dewasa';
    }

    protected function detectForm(string $content): ?string
    {
        $text = Str::lower($content);
        $map = [
            'sirup' => ['sirup', 'syrup'],
            'tablet' => ['tablet', 'tab', 'pil', 'caplet'],
            'kapsul' => ['kapsul', 'capsul', 'capsule'],
        ];

        foreach ($map as $form => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    return $form;
                }
            }
        }

        return null;
    }

    protected function handleVariantSelection(array $state, string $answer): ?array
    {
        $options = $state['options'] ?? [];

        foreach ($options as $option) {
            $name = Str::lower($option['name'] ?? '');
            $category = Str::lower($option['category'] ?? '');
            $form = Str::lower($option['form'] ?? '');

            if (
                ($name && Str::contains($answer, $name)) ||
                ($category && Str::contains($answer, $category)) ||
                ($form && Str::contains($answer, $form))
            ) {
                return $this->buildMedicationResponsePayload($option, ['pending' => null]);
            }
        }

        $choices = collect($options)
            ->map(fn (array $item) => trim(sprintf('%s (%s)', $item['name'] ?? 'Produk', $item['category'] ?? '')))
            ->implode(', ');

        return [
            'content' => "Saya belum yakin varian mana yang dimaksud. Sebutkan salah satu dari: {$choices}.",
            'metadata' => [
                'intent' => 'clarification',
                'state' => $state,
            ],
        ];
    }

    protected function extractConversationState(ChatConversation $conversation): array
    {
        if (! $conversation->exists) {
            return [];
        }

        $lastAssistant = $conversation->messages()
            ->where('role', 'assistant')
            ->latest()
            ->first();

        if (! $lastAssistant) {
            return [];
        }

        $metadata = $lastAssistant->metadata;

        if (! is_array($metadata)) {
            return [];
        }

        return $metadata['state'] ?? [];
    }
}
