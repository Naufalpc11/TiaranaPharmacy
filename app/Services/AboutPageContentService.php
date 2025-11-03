<?php

namespace App\Services;

use App\Models\AboutContactDetail;
use App\Models\AboutHistoryStat;
use App\Models\AboutMission;
use App\Models\AboutPageSetting;
use Illuminate\Support\Facades\Storage;

class AboutPageContentService
{
    private const DEFAULT_HERO = [
        'title' => 'Tentang Kami',
        'subtitle' => 'Apotek Tiarana Farma adalah apotek terpercaya yang menyediakan layanan kesehatan berkualitas dengan dukungan tim profesional dan fasilitas modern.',
        'primary_button_text' => 'Lokasi',
        'primary_button_url' => '#lokasi',
        'secondary_button_text' => 'Hubungi Kami',
        'secondary_button_url' => '/contact',
    ];

    private const DEFAULT_VISION = [
        'title' => 'Visi',
        'text' => '"Menjadi apotek terpercaya berbasis komunitas yang mengutamakan pelayanan tatap muka yang aman, ramah, dan akurat bagi keluarga Indonesia."',
    ];

    private const DEFAULT_MISSION_TITLE = 'Misi';

    private const DEFAULT_MISSIONS = [
        [
            'title' => 'Pelayanan tatap muka yang ramah',
            'description' => 'Memprioritaskan konsultasi langsung dengan apoteker, memastikan kebutuhan pasien, dan pendampingan penggunaan obat di lokasi apotek.',
        ],
        [
            'title' => 'Mutu, keaslian, dan kepatuhan',
            'description' => 'Mengelola obat sesuai standar praktik kefarmasian dan regulasi BPOM untuk menjamin keamanan pasien.',
        ],
        [
            'title' => 'Pengadaan obat melalui PBF resmi',
            'description' => 'Seluruh pengadaan dilakukan dari Pedagang Besar Farmasi (PBF) resmi dengan kepatuhan CDOB.',
        ],
    ];

    private const DEFAULT_HISTORY = [
        'title' => 'Sejarah Kami',
        'description' => 'Berdiri pada 2021 di Balikpapan, Tiarana Farma lahir dari misi sederhana: memudahkan akses obat yang aman dan terjangkau. Dari sebuah apotek kecil kami bertumbuh menjadi layanan farmasi modern yang mengedepankan konsultasi tatap muka, stok terkurasi, dan proses pembelian yang mudah. Hingga kini, kami telah melayani lebih dari 5.000 pelanggan dan terus berinovasi demi kesehatan keluarga Indonesia.',
        'image_path' => null,
        'stats' => [
            [
                'icon' => 'fa-regular fa-calendar-check',
                'value' => '2021',
                'label' => 'Mulai beroperasi',
                'icon_image_url' => null,
            ],
            [
                'icon' => 'fa-solid fa-people-group',
                'value' => '5.000+',
                'label' => 'Pelanggan dilayani',
                'icon_image_url' => null,
            ],
            [
                'icon' => 'fa-solid fa-pills',
                'value' => '200+',
                'label' => 'Produk tersedia',
                'icon_image_url' => null,
            ],
        ],
    ];

    private const DEFAULT_TEAM = [
        'title' => 'Apoteker Kami',
        'intro' => 'Kenali apoteker penanggung jawab kami di balik layanan Tiarana Farma.',
        'pharmacist' => [
            'name' => 'apt. Titik Tresnowati, S. Si',
            'role' => 'Apoteker Penanggung Jawab',
            'stra' => '19880824/STRA-YYYY/2023',
            'sipa' => '19880824/SIPA-XX/2023',
            'schedule' => 'Senin-Sabtu, 17.00-22.00 WITA',
            'badges' => [
                'STRA & SIPA terverifikasi',
                'On-the-job pengalaman',
            ],
            'photo_path' => null,
            'photo_alt' => 'Foto Apoteker Penanggung Jawab',
        ],
    ];

    private const DEFAULT_LOCATION = [
        'title' => 'Lokasi Kami',
        'intro' => null,
        'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid',
        'contact_details' => [
            [
                'icon' => 'fa-solid fa-map-location-dot',
                'title' => 'Alamat',
                'lines' => [
                    'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12',
                    'Sepinggan, Balikpapan, Kalimantan Timur 76116',
                ],
                'copyText' => 'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12, Sepinggan, Balikpapan, Kalimantan Timur 76116',
                'icon_image_url' => null,
            ],
            [
                'icon' => 'fa-solid fa-phone',
                'title' => 'Telepon',
                'lines' => ['0821-2000-3948'],
                'copyText' => '0821-2000-3948',
                'icon_image_url' => null,
            ],
            [
                'icon' => 'fa-solid fa-clock',
                'title' => 'Jam Operasional',
                'lines' => [
                    'Senin-Sabtu: 08.00-21.00 WITA',
                    'Minggu: 09.00-20.00 WITA',
                ],
                'copyText' => "Senin-Sabtu: 08.00-21.00 WITA\nMinggu: 09.00-20.00 WITA",
                'icon_image_url' => null,
            ],
        ],
    ];

    public function get(): array
    {
        $settings = AboutPageSetting::query()->first();

        [$hero, $vision, $missionTitle, $history, $team, $location] = $this->resolveSectionValues($settings);

        $missions = AboutMission::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (AboutMission $mission) => [
                'title' => $mission->title,
                'description' => $mission->description,
            ])
            ->all();

        if (empty($missions)) {
            $missions = self::DEFAULT_MISSIONS;
        }

        $stats = AboutHistoryStat::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (AboutHistoryStat $stat) => [
                'icon' => $stat->icon,
                'iconImageUrl' => $stat->icon_image_path ? Storage::url($stat->icon_image_path) : null,
                'value' => $stat->value,
                'label' => $stat->label,
            ])
            ->all();

        if (empty($stats)) {
            $stats = array_map(
                fn (array $stat) => [
                    'icon' => $stat['icon'] ?? null,
                    'iconImageUrl' => $stat['icon_image_url'] ?? null,
                    'value' => $stat['value'] ?? '',
                    'label' => $stat['label'] ?? '',
                ],
                self::DEFAULT_HISTORY['stats']
            );
        }

        $contactDetails = AboutContactDetail::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (AboutContactDetail $detail) => [
                'icon' => $detail->icon,
                'iconImageUrl' => $detail->icon_image_path ? Storage::url($detail->icon_image_path) : null,
                'title' => $detail->title,
                'lines' => array_values($detail->lines ?? []),
                'copyText' => $detail->copy_text,
            ])
            ->all();

        if (empty($contactDetails)) {
            $contactDetails = array_map(
                fn (array $detail) => [
                    'icon' => $detail['icon'] ?? null,
                    'iconImageUrl' => $detail['icon_image_url'] ?? null,
                    'title' => $detail['title'] ?? '',
                    'lines' => array_values($detail['lines'] ?? []),
                    'copyText' => $detail['copyText'] ?? null,
                ],
                self::DEFAULT_LOCATION['contact_details']
            );
        }

        return [
            'hero' => $hero,
            'vision' => $vision,
            'mission' => [
                'title' => $missionTitle,
                'items' => $missions,
            ],
            'history' => [
                'title' => $history['title'],
                'description' => $history['description'],
                'imageUrl' => $history['image_url'],
                'stats' => $stats,
            ],
            'team' => [
                'title' => $team['title'],
                'intro' => $team['intro'],
                'pharmacist' => $team['pharmacist'],
            ],
            'location' => [
                'title' => $location['title'],
                'intro' => $location['intro'],
                'mapEmbedUrl' => $location['map_embed_url'],
                'contactDetails' => $contactDetails,
            ],
        ];
    }

    private function resolveSectionValues(?AboutPageSetting $settings): array
    {
        $hero = [
            'title' => $settings?->hero_title ?? self::DEFAULT_HERO['title'],
            'subtitle' => $settings?->hero_subtitle ?? self::DEFAULT_HERO['subtitle'],
            'backgroundImageUrl' => $settings?->hero_background_image_path
                ? Storage::url($settings->hero_background_image_path)
                : null,
            'primaryButtonText' => $settings?->hero_primary_button_text ?? self::DEFAULT_HERO['primary_button_text'],
            'primaryButtonUrl' => $settings?->hero_primary_button_url ?? self::DEFAULT_HERO['primary_button_url'],
            'secondaryButtonText' => $settings?->hero_secondary_button_text ?? self::DEFAULT_HERO['secondary_button_text'],
            'secondaryButtonUrl' => $settings?->hero_secondary_button_url ?? self::DEFAULT_HERO['secondary_button_url'],
        ];

        $vision = [
            'title' => $settings?->vision_title ?? self::DEFAULT_VISION['title'],
            'text' => $settings?->vision_text ?? self::DEFAULT_VISION['text'],
        ];

        $missionTitle = $settings?->mission_title ?? self::DEFAULT_MISSION_TITLE;

        $history = [
            'title' => $settings?->history_title ?? self::DEFAULT_HISTORY['title'],
            'description' => $settings?->history_description ?? self::DEFAULT_HISTORY['description'],
            'image_url' => $settings?->history_image_path
                ? Storage::url($settings->history_image_path)
                : null,
        ];

        $pharmacistBadges = $settings?->pharmacist_badges ?? self::DEFAULT_TEAM['pharmacist']['badges'];
        if (! is_array($pharmacistBadges)) {
            $pharmacistBadges = [];
        }

        $team = [
            'title' => $settings?->team_title ?? self::DEFAULT_TEAM['title'],
            'intro' => $settings?->team_intro ?? self::DEFAULT_TEAM['intro'],
            'pharmacist' => [
                'name' => $settings?->pharmacist_name ?? self::DEFAULT_TEAM['pharmacist']['name'],
                'role' => $settings?->pharmacist_role ?? self::DEFAULT_TEAM['pharmacist']['role'],
                'stra' => $settings?->pharmacist_stra ?? self::DEFAULT_TEAM['pharmacist']['stra'],
                'sipa' => $settings?->pharmacist_sipa ?? self::DEFAULT_TEAM['pharmacist']['sipa'],
                'schedule' => $settings?->pharmacist_schedule ?? self::DEFAULT_TEAM['pharmacist']['schedule'],
                'badges' => array_values(array_filter($pharmacistBadges)),
                'photoUrl' => $settings?->pharmacist_photo_path
                    ? Storage::url($settings->pharmacist_photo_path)
                    : null,
                'photoAlt' => $settings?->pharmacist_photo_alt ?? self::DEFAULT_TEAM['pharmacist']['photo_alt'],
            ],
        ];

        $location = [
            'title' => $settings?->location_title ?? self::DEFAULT_LOCATION['title'],
            'intro' => $settings?->location_intro ?? self::DEFAULT_LOCATION['intro'],
            'map_embed_url' => $settings?->location_map_embed_url ?? self::DEFAULT_LOCATION['map_embed_url'],
        ];

        return [$hero, $vision, $missionTitle, $history, $team, $location];
    }
}
