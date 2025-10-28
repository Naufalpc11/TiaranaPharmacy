<?php

namespace App\Services;

use App\Models\HomeAboutFeature;
use App\Models\HomeFeatureHighlight;
use App\Models\HomePageSetting;
use App\Models\HomeService;
use App\Models\PartnerLogo;
use Illuminate\Support\Facades\Storage;

class HomePageContentService
{
    private const DEFAULT_HERO = [
        'title' => 'TIARANA FARMA',
        'subtitle_primary' => 'Melayani Dengan Sepenuh Hati',
        'subtitle_secondary' => 'Berdiri Sejak 2021',
    ];

    private const DEFAULT_FEATURE_HIGHLIGHTS = [
        [
            'icon' => 'fas fa-pills',
            'icon_image_url' => null,
            'title' => 'Resep & Non-Resep',
            'description' => 'Layanan obat resep dan non-resep dengan konsultasi farmasi profesional',
        ],
        [
            'icon' => 'fas fa-clock',
            'icon_image_url' => null,
            'title' => 'Jam Operasional',
            'description' => 'Buka setiap hari dari pukul 08:00 - 22:00 WITA',
        ],
        [
            'icon' => 'fas fa-shield-alt',
            'icon_image_url' => null,
            'title' => 'Produk Terjamin',
            'description' => 'Keaslian dan kualitas produk terjamin dengan izin resmi BPOM',
        ],
    ];

    private const DEFAULT_ABOUT = [
        'title' => 'Tentang Kami',
        'description' => 'Apotek Tiarana Farma telah melayani masyarakat sejak tahun 2021 dengan komitmen untuk menyediakan layanan kesehatan terbaik dan produk berkualitas. Dengan tim apoteker profesional, kami siap membantu Anda dengan konsultasi kesehatan dan informasi penggunaan obat yang tepat.',
        'features' => [
            [
                'icon' => 'fas fa-certificate',
                'icon_image_url' => null,
                'title' => 'Apoteker Berpengalaman',
            ],
            [
                'icon' => 'fas fa-check-circle',
                'icon_image_url' => null,
                'title' => 'Produk Berkualitas',
            ],
            [
                'icon' => 'fas fa-heart',
                'icon_image_url' => null,
                'title' => 'Pelayanan Ramah',
            ],
        ],
    ];

    private const DEFAULT_SERVICES = [
        [
            'icon' => 'fas fa-prescription-bottle-alt',
            'icon_image_url' => null,
            'title' => 'Layanan Resep',
            'description' => 'Kami menyediakan layanan resep dokter dengan standar tinggi dan penuh ketelitian. Apoteker profesional kami akan memastikan setiap resep diproses dengan tepat dan aman, disertai dengan konsultasi mengenai penggunaan obat yang benar.',
            'items' => [
                'Pelayanan resep dokter cepat dan akurat',
                'Konsultasi penggunaan obat dengan apoteker',
                'Pemeriksaan interaksi obat',
                'Informasi efek samping dan cara penggunaan',
            ],
            'image_class' => 'service-image-resep',
            'reverse' => false,
        ],
        [
            'icon' => 'fas fa-notes-medical',
            'icon_image_url' => null,
            'title' => 'Konsultasi Kesehatan',
            'description' => 'Dapatkan konsultasi kesehatan gratis dengan apoteker berpengalaman kami. Kami siap membantu Anda dengan berbagai pertanyaan seputar kesehatan dan penggunaan obat yang tepat.',
            'items' => [
                'Konsultasi gratis dengan apoteker',
                'Informasi penggunaan obat yang aman',
                'Pemeriksaan kesehatan dasar',
                'Edukasi kesehatan',
            ],
            'image_class' => 'service-image-konsultasi',
            'reverse' => true,
        ],
        [
            'icon' => 'fas fa-heartbeat',
            'icon_image_url' => null,
            'title' => 'Pemeriksaan Kesehatan',
            'description' => 'Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.',
            'items' => [
                'Cek tekanan darah',
                'Pemeriksaan gula darah',
                'Pemeriksaan Kolestrol dan Asam Urat',
                'Konsultasi hasil pemeriksaan',
            ],
            'image_class' => 'service-image-pemeriksaan',
            'reverse' => false,
        ],
    ];

    public function get(): array
    {
        $setting = HomePageSetting::query()->first();

        $hero = [
            'title' => $setting?->hero_title ?? self::DEFAULT_HERO['title'],
            'subtitle_primary' => $setting?->hero_subtitle_primary ?? self::DEFAULT_HERO['subtitle_primary'],
            'subtitle_secondary' => $setting?->hero_subtitle_secondary ?? self::DEFAULT_HERO['subtitle_secondary'],
            'background_image_url' => $setting?->hero_background_image_path
                ? Storage::url($setting->hero_background_image_path)
                : null,
        ];

        $featureHighlights = HomeFeatureHighlight::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (HomeFeatureHighlight $feature) => [
                'icon' => $feature->icon,
                'title' => $feature->title,
                'description' => $feature->description,
                'icon_image_url' => $feature->icon_image_path ? Storage::url($feature->icon_image_path) : null,
            ])
            ->all();

        if (empty($featureHighlights)) {
            $featureHighlights = self::DEFAULT_FEATURE_HIGHLIGHTS;
        }

        $aboutFeatures = HomeAboutFeature::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (HomeAboutFeature $feature) => [
                'icon' => $feature->icon,
                'title' => $feature->title,
                'icon_image_url' => $feature->icon_image_path ? Storage::url($feature->icon_image_path) : null,
            ])
            ->all();

        $about = [
            'title' => $setting?->about_title ?? self::DEFAULT_ABOUT['title'],
            'description' => $setting?->about_description ?? self::DEFAULT_ABOUT['description'],
            'image_url' => $setting?->about_image_path ? Storage::url($setting->about_image_path) : null,
            'features' => ! empty($aboutFeatures) ? $aboutFeatures : self::DEFAULT_ABOUT['features'],
        ];

        $services = HomeService::query()
            ->orderBy('sort_order')
            ->get()
            ->map(function (HomeService $service, int $index) {
                return [
                    'icon' => $service->icon,
                    'title' => $service->title,
                    'description' => $service->description,
                    'items' => array_values($service->items ?? []),
                    'image_url' => $service->image_path ? Storage::url($service->image_path) : null,
                    'image_class' => null,
                    'icon_image_url' => $service->icon_image_path ? Storage::url($service->icon_image_path) : null,
                    'reverse' => $index % 2 === 1,
                ];
            })
            ->all();

        if (empty($services)) {
            $services = self::DEFAULT_SERVICES;
        }

        $partnerLogos = PartnerLogo::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PartnerLogo $logo) => [
                'name' => $logo->name,
                'src' => $logo->image_path ? Storage::url($logo->image_path) : null,
            ])
            ->filter(fn (array $logo) => filled($logo['name']) || filled($logo['src']))
            ->values()
            ->all();

        return [
            'hero' => $hero,
            'featureHighlights' => $featureHighlights,
            'about' => $about,
            'services' => $services,
            'partnerLogos' => $partnerLogos,
        ];
    }
}
