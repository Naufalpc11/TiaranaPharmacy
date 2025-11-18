<?php

namespace App\Services;

use App\Support\AgeRange;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MedicationChatService
{
    public function __construct(protected MedicationCatalog $medicationCatalog)
    {
    }

    public function handle(Collection $messages): ?array
    {
        $latestUserMessage = $this->extractLatestUserMessage($messages);

        if (! $latestUserMessage) {
            return null;
        }

        $ageMatch = AgeRange::detectFromText($latestUserMessage['content'] ?? '');
        $pendingContext = $this->extractPendingContext($messages, $latestUserMessage);

        if ($pendingContext) {
            return $this->handlePendingContext($pendingContext, $latestUserMessage, $ageMatch);
        }

        if (! $this->isMedicationRequest($latestUserMessage['content'])) {
            return null;
        }

        $candidates = $this->findDatasetCandidates($latestUserMessage['content'], $ageMatch['range'] ?? null);

        if ($candidates->isEmpty()) {
            return null;
        }

        if (! empty($ageMatch['range'])) {
            $medication = $this->pickMedicationForAge($candidates, $ageMatch['range']);

            if (! $medication) {
                return [
                    'type' => 'no_match',
                    'reply' => 'Maaf, belum ada gambar obat yang sesuai dengan rentang usia tersebut di dataset kami.',
                    'metadata' => [
                        'intent' => 'medication_not_found',
                    ],
                ];
            }

            return $this->buildRecommendationDecision(
                $medication,
                $latestUserMessage,
                $ageMatch['range'],
                $candidates->first()['category'] ?? null
            );
        }

        $symptomLabel = $candidates->first()['category'] ?? 'keluhan Anda';

        return [
            'type' => 'ask_age',
            'reply' => sprintf(
                'Untuk merekomendasikan obat %s yang tepat, boleh info usia pasiennya? (contoh: bayi, 3 tahun, 8 tahun, dewasa)',
                Str::lower($symptomLabel)
            ),
            'metadata' => [
                'intent' => 'ask_age',
                'symptom_label' => $symptomLabel,
                'candidate_slugs' => $candidates->pluck('slug')->filter()->values()->all(),
                'pending_query' => $latestUserMessage['content'],
            ],
        ];
    }

    protected function extractLatestUserMessage(Collection $messages): ?array
    {
        return $messages->reverse()->first(function (array $message) {
            return ($message['role'] ?? null) === 'user';
        });
    }

    protected function extractPendingContext(Collection $messages, array $latestUserMessage): ?array
    {
        $ordered = $messages->values();
        $latestIndex = null;

        for ($i = $ordered->count() - 1; $i >= 0; $i--) {
            $message = $ordered[$i];

            if (($message['role'] ?? null) !== 'user') {
                continue;
            }

            $latestIndex = $i;
            break;
        }

        if ($latestIndex === null) {
            return null;
        }

        for ($cursor = $latestIndex - 1; $cursor >= 0; $cursor--) {
            $message = $ordered[$cursor];

            if (($message['role'] ?? null) === 'user') {
                break;
            }

            $intent = data_get($message, 'metadata.intent');

            if (($message['role'] ?? null) === 'assistant' && $intent === 'ask_age') {
                $metadata = $message['metadata'] ?? [];

                return $metadata + ['intent' => $intent];
            }
        }

        return null;
    }

    protected function handlePendingContext(array $pendingContext, array $latestUserMessage, ?array $ageMatch): array
    {
        if (empty($ageMatch['range'])) {
            return [
                'type' => 'ask_age_followup',
                'reply' => 'Terima kasih. Supaya rekomendasi obatnya pas, boleh disebutkan usia pasiennya? Misal: bayi, 3 tahun, 8 tahun, atau dewasa.',
                'metadata' => [
                    'intent' => 'ask_age',
                    'symptom_label' => $pendingContext['symptom_label'] ?? null,
                    'candidate_slugs' => $pendingContext['candidate_slugs'] ?? [],
                    'pending_query' => $pendingContext['pending_query'] ?? $latestUserMessage['content'],
                ],
            ];
        }

        $candidateSlugs = collect($pendingContext['candidate_slugs'] ?? [])
            ->filter()
            ->take(5);

        $candidates = $candidateSlugs
            ->map(function ($slug) {
                return $slug ? $this->medicationCatalog->findBySlug($slug) : null;
            })
            ->filter();

        if ($candidates->isEmpty() && ($pendingContext['pending_query'] ?? false)) {
            $candidates = $this->findDatasetCandidates(
                $pendingContext['pending_query'],
                $ageMatch['range']
            );
        }

        $medication = $this->pickMedicationForAge($candidates, $ageMatch['range']);

        if (! $medication) {
            return [
                'type' => 'no_match',
                'reply' => 'Saya belum menemukan gambar obat yang cocok dengan usia tersebut. Coba pilih kategori lain atau hubungi apoteker kami.',
                'metadata' => [
                    'intent' => 'medication_not_found',
                ],
            ];
        }

        return $this->buildRecommendationDecision(
            $medication,
            $latestUserMessage,
            $ageMatch['range'],
            $pendingContext['symptom_label'] ?? ($medication['category'] ?? null)
        );
    }

    protected function findDatasetCandidates(string $query, ?string $ageRange): Collection
    {
        $filters = [];

        if ($ageRange) {
            $filters['age_group'] = $ageRange;
        }

        return $this->medicationCatalog
            ->matchCandidates($query, $filters, 5)
            ->filter(function (array $item) {
                return ! empty($item['dataset_image']) || ! empty($item['dataset_image_url']);
            })
            ->values();
    }

    protected function pickMedicationForAge(Collection $candidates, ?string $ageRange): ?array
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        if (! $ageRange) {
            return $candidates->first();
        }

        $match = $candidates->first(function (array $item) use ($ageRange) {
            return AgeRange::isCompatible($item['age_group'] ?? null, $ageRange);
        });

        if ($match) {
            return $match;
        }

        return $candidates->first(function (array $item) {
            return empty($item['age_group']);
        });
    }

    protected function buildRecommendationDecision(array $medication, array $latestUserMessage, ?string $ageLabel, ?string $symptomLabel): array
    {
        return [
            'type' => 'recommendation',
            'medication' => $medication,
            'formatted_medication' => $this->medicationCatalog->formatDetails($medication),
            'age_label' => $ageLabel ?: ($medication['age_group'] ?? null),
            'symptom_label' => $symptomLabel ?? ($medication['category'] ?? null),
            'user_query' => $latestUserMessage['content'],
        ];
    }

    protected function isMedicationRequest(?string $text): bool
    {
        $content = Str::lower($text ?? '');

        if ($content === '') {
            return false;
        }

        if (Str::contains($content, ['obat', 'obatnya', 'obat apa'])) {
            return true;
        }

        return Str::contains($content, [
            'minum apa',
            'apa ya',
            'konsumsi apa',
            'rekomendasi',
        ]);
    }
}
