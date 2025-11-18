<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
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
        'tolong',
        'dong',
        'saja',
        'aja',
        'ya',
        'kah',
    ];

    public function __construct()
    {
        $this->catalog = $this->buildConfigCatalog()
            ->merge($this->buildDatasetCatalog())
            ->values();
    }

    protected function buildConfigCatalog(): Collection
    {
        return collect(config('medications.catalog', []))
            ->map(function (array $item) {
                $item['keywords'] = array_values(array_unique(array_filter(array_merge(
                    $item['keywords'] ?? [],
                    $item['symptoms'] ?? [],
                    $item['aliases'] ?? [],
                    [$item['name'] ?? '', $item['category'] ?? '', $item['form'] ?? '']
                ))));

                $item['age_group'] = $item['age_group'] ?? $this->inferAgeGroupFromLabels([
                    $item['name'] ?? '',
                    $item['category'] ?? '',
                    $item['form'] ?? '',
                ]);

                $item['source'] = 'config';

                return $item;
            });
    }

    protected function buildDatasetCatalog(): Collection
    {
        $datasetPath = resource_path('images/dataset');

        if (! File::isDirectory($datasetPath)) {
            return collect();
        }

        return collect(File::files($datasetPath))
            ->map(function (\SplFileInfo $file) {
                $extension = $file->getExtension();
                $baseName = $file->getBasename($extension ? '.'.$extension : '');
                $parts = preg_split('/_+/', $baseName, 3);

                $category = trim($parts[0] ?? '');
                $product = trim($parts[1] ?? $category);
                $form = trim($parts[2] ?? '');

                $keywords = $this->buildKeywordSet([$category, $product, $form]);
                $slug = Str::slug($baseName);

                return [
                    'slug' => $slug ?: Str::slug($product.$file->getFilename()),
                    'name' => $product ?: $category,
                    'category' => $category,
                    'form' => $form,
                    'dataset_image' => $file->getFilename(),
                    'keywords' => $keywords,
                    'symptoms' => array_filter([$category]),
                    'age_group' => $this->inferAgeGroupFromLabels([$category, $product, $form]),
                    'match_threshold' => 2,
                    'source' => 'dataset',
                ];
            })
            ->filter(fn (array $item) => $item['dataset_image'] ?? false);
    }

    protected function buildKeywordSet(array $parts): array
    {
        return array_values(array_unique(array_filter(
            collect($parts)
                ->flatMap(function ($part) {
                    return preg_split('/[\s,]+/', Str::lower($part ?? '')) ?: [];
                })
                ->filter(function ($token) {
                    return $token !== '' && ! $this->isStopWord($token);
                })
                ->all()
        )));
    }

    public function matchByQuery(?string $query, array $filters = []): ?array
    {
        $match = $this->matchCandidates($query, $filters, 1)->first();

        return $match ? Arr::except($match, ['score']) : null;
    }

    public function matchCandidates(?string $query, array $filters = [], int $limit = 3): Collection
    {
        $normalizedQuery = Str::lower($query ?? '');
        $tokens = $this->tokenizeQuery($normalizedQuery);
        $strictDemam = $this->isStrictDemamQuery($tokens);

        return $this->catalog
            ->map(function (array $item) use ($normalizedQuery, $filters, $strictDemam) {
                $score = 0;
                $matchedFragments = [];

                if ($normalizedQuery !== '') {
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

                            if (Str::length($fragment) < 3) {
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
                }

                $score += $this->scoreFilters($item, $filters);

                $categoryText = Str::lower($item['category'] ?? '');

                if ($strictDemam && Str::contains($categoryText, ['batuk', 'pilek'])) {
                    $item['score'] = -999;

                    return $item;
                }

                $item['score'] = $score;

                return $item;
            })
            ->filter(function (array $item) {
                $threshold = $item['match_threshold'] ?? 3;

                return ($item['score'] ?? 0) >= $threshold;
            })
            ->sortByDesc('score')
            ->values()
            ->take($limit)
            ->map(fn (array $item) => Arr::except($item, ['score']));
    }

    public function matchImageCandidates(?string $query, array $filters = [], int $limit = 4): Collection
    {
        return $this->matchCandidates($query, $filters, $limit);
    }

    protected function scoreFilters(array $item, array $filters): int
    {
        $score = 0;
        $category = Str::lower($item['category'] ?? '');
        $form = Str::lower($item['form'] ?? '');
        $name = Str::lower($item['name'] ?? '');
        $ageGroup = Str::lower($item['age_group'] ?? '');

        if ($symptom = Str::lower($filters['symptom'] ?? '')) {
            $score += Str::contains($category, $symptom) ? 5 : -3;
        }

        if ($preferredForm = Str::lower($filters['form'] ?? '')) {
            $score += Str::contains($form, $preferredForm) ? 5 : -2;
        }

        if ($product = Str::lower($filters['product'] ?? '')) {
            $score += Str::contains($name, $product) ? 4 : 0;
        }

        if ($requestedAge = Str::lower($filters['age_group'] ?? '')) {
            if ($ageGroup && $requestedAge !== '' && $ageGroup === $requestedAge) {
                $score += 5;
            } elseif ($requestedAge !== '') {
                $score -= 3;
            }
        }

        return $score;
    }

    public function formatDetails(array $medication): array
    {
        return [
            'name' => $medication['name'] ?? '',
            'category' => $medication['category'] ?? '',
            'form' => $medication['form'] ?? '',
            'stock_status' => $medication['stock_status'] ?? '',
            'how_it_works' => $medication['how_it_works'] ?? $this->buildGenericDescription($medication),
            'dosage' => $medication['dosage'] ?? [],
            'composition' => $medication['composition'] ?? '',
            'warnings' => $medication['warnings'] ?? [],
            'image_url' => $this->buildImageUrl($medication['image'] ?? null),
            'dataset_image_url' => $this->buildDatasetImageUrl($medication),
            'symptoms' => $medication['symptoms'] ?? [],
            'age_group' => $medication['age_group'] ?? null,
            'source' => $medication['source'] ?? 'config',
        ];
    }

    protected function buildGenericDescription(array $medication): string
    {
        $category = Str::lower($medication['category'] ?? 'obat');
        $form = $medication['form'] ?? 'sediaan';

        return sprintf(
            'Produk ini digunakan untuk membantu keluhan %s dalam bentuk %s. Ikuti aturan pakai pada kemasan dan konsultasikan dengan apoteker kami bila ragu.',
            $category ?: 'tertentu',
            Str::lower($form)
        );
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

        return route('medications.dataset.show', ['slug' => $medication['slug']], false);
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

    public function availableForms(string $symptom): array
    {
        $keyword = Str::lower($symptom);

        return $this->catalog
            ->filter(function (array $item) use ($keyword) {
                return Str::contains(Str::lower($item['category'] ?? ''), $keyword);
            })
            ->map(function (array $item) {
                return Str::lower($item['form'] ?? '');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function tokenizeQuery(string $query): array
    {
        return array_values(array_filter(preg_split('/[\s,]+/', $query)));
    }

    protected function isStrictDemamQuery(array $tokens): bool
    {
        if (! in_array('demam', $tokens, true)) {
            return false;
        }

        foreach (['batuk', 'pilek'] as $term) {
            if (in_array($term, $tokens, true)) {
                return false;
            }
        }

        return true;
    }

    protected function inferAgeGroupFromLabels(array $labels): ?string
    {
        $text = Str::lower(implode(' ', $labels));

        if ($text === '') {
            return null;
        }

        if (Str::contains($text, ['bayi', 'infant', '0-1', '0-2'])) {
            return 'bayi';
        }

        if (Str::contains($text, ['anak', 'junior', '2-12', '2-4', '4-6', '6-12'])) {
            return 'anak';
        }

        if (preg_match('/(\d{1,2})\s*-\s*(\d{1,2})\s*tahun/', $text, $matches)) {
            $to = (int) ($matches[2] ?? 0);

            if ($to > 0 && $to <= 12) {
                return 'anak';
            }
        }

        if (preg_match('/(\d{1,2})\s*tahun ke atas/', $text, $matches)) {
            $from = (int) ($matches[1] ?? 0);

            if ($from >= 12) {
                return 'dewasa';
            }
        }

        return 'dewasa';
    }
}
