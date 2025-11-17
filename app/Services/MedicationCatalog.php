<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MedicationCatalog
{
    protected Collection $catalog;

    protected array $stopWords = [
        'obat',
        'apa',
        'gimana',
        'bagaimana',
        'seperti',
        'untuk',
        'yang',
        'dan',
        'cara',
        'minum',
        'gambarnya',
        'gimana',
    ];

    public function __construct()
    {
        $this->catalog = collect(config('medications.catalog', []))
            ->map(function (array $item) {
                $item['keywords'] = array_values(array_unique(array_filter(array_merge(
                    $item['keywords'] ?? [],
                    $item['symptoms'] ?? [],
                    $item['aliases'] ?? [],
                    [$item['name'] ?? '', $item['category'] ?? '', $item['form'] ?? '']
                ))));

                return $item;
            });
    }

    public function matchByQuery(?string $query): ?array
    {
        if (! $query) {
            return null;
        }

        $normalizedQuery = Str::lower($query);

        $match = $this->catalog
            ->map(function (array $item) use ($normalizedQuery) {
                $score = 0;
                $matchedFragments = [];

                foreach ($item['keywords'] as $keyword) {
                    $keyword = Str::lower($keyword);

                    if ($keyword === '') {
                        continue;
                    }

                    if (Str::contains($normalizedQuery, $keyword)) {
                        $score += 3;
                        continue;
                    }

                    $fragments = preg_split('/[\s,-]+/', $keyword) ?: [];

                    foreach ($fragments as $fragment) {
                        $fragment = trim($fragment);

                        if (Str::length($fragment) < 4) {
                            continue;
                        }

                        if ($this->isStopWord($fragment)) {
                            continue;
                        }

                        if (isset($matchedFragments[$fragment])) {
                            continue;
                        }

                        if (Str::contains($normalizedQuery, $fragment)) {
                            $matchedFragments[$fragment] = true;
                            $score += 1;
                        }
                    }
                }

                $item['score'] = $score;

                return $item;
            })
            ->sortByDesc('score')
            ->first(function (array $item) {
                $threshold = $item['match_threshold'] ?? 3;

                return $item['score'] >= $threshold;
            });

        return $match ? Arr::except($match, ['score']) : null;
    }

    public function formatDetails(array $medication): array
    {
        return [
            'name' => $medication['name'] ?? '',
            'category' => $medication['category'] ?? '',
            'form' => $medication['form'] ?? '',
            'stock_status' => $medication['stock_status'] ?? '',
            'how_it_works' => $medication['how_it_works'] ?? '',
            'dosage' => $medication['dosage'] ?? [],
            'composition' => $medication['composition'] ?? '',
            'warnings' => $medication['warnings'] ?? [],
            'image_url' => $this->buildImageUrl($medication['image'] ?? null),
            'dataset_image_url' => $this->buildDatasetImageUrl($medication),
        ];
    }

    protected function buildImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset($path);
    }

    protected function buildDatasetImageUrl(array $medication): ?string
    {
        if (empty($medication['slug']) || empty($medication['dataset_image'])) {
            return null;
        }

        if (! Route::has('medications.dataset.show')) {
            return null;
        }

        return route('medications.dataset.show', ['slug' => $medication['slug']]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->catalog->first(function (array $item) use ($slug) {
            return ($item['slug'] ?? null) === $slug;
        });
    }

    protected function isStopWord(string $word): bool
    {
        return in_array($word, $this->stopWords, true);
    }
}
