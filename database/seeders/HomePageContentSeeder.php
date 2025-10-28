<?php

namespace Database\Seeders;

use App\Models\HomeAboutFeature;
use App\Models\HomeFeatureHighlight;
use App\Models\HomePageSetting;
use App\Models\HomeService;
use App\Models\PartnerLogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HomePageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroImagePath = $this->ensurePublicAsset(
            'images/Interior.jpg',
            'home/hero/hero-default.jpg'
        );

        $aboutImagePath = $this->ensurePublicAsset(
            'images/Interior.jpg',
            'home/about/about-default.jpg'
        );

        $serviceImagePath = $this->ensurePublicAsset(
            'images/Interior.jpg',
            'home/services/service-default.jpg'
        );

        $partnerLogos = [
            'AJM' => 'images/PBFLLogo/AJM.png',
            'APL' => 'images/PBFLLogo/APL.png',
            'BSP' => 'images/PBFLLogo/BSP.png',
            'CSF' => 'images/PBFLLogo/CSF.png',
            'Edi Hari Syam' => 'images/PBFLLogo/EdiHariSyam.png',
            'Elka Alkesindo' => 'images/PBFLLogo/ElkaAlkesindo.png',
            'Ferto Mulia Pratama' => 'images/PBFLLogo/FertoMuliaPratama.png',
            'Hidup Bahagia' => 'images/PBFLLogo/HidupBahagia.png',
            'Kimia Farma' => 'images/PBFLLogo/KimiaFarma.png',
            'Lenko Surya Perkasa' => 'images/PBFLLogo/LenkoSuryaPerkasa.png',
            'Marga Nusantara Jaya' => 'images/PBFLLogo/MargaNusantaraJaya.png',
            'MPI' => 'images/PBFLLogo/MPI.png',
            'PIM' => 'images/PBFLLogo/PIM.png',
            'Sapta Sari' => 'images/PBFLLogo/SaptaSari.png',
            'Satrindo Multi Sukses' => 'images/PBFLLogo/SatrindoMultiSukses.png',
            'Tempo Scan' => 'images/PBFLLogo/TempoScan.png',
        ];

        $mappedPartnerLogos = [];

        foreach ($partnerLogos as $name => $source) {
            $destination = 'home/partners/' . basename($source);
            $storedPath = $this->ensurePublicAsset($source, $destination);
            if ($storedPath) {
                $mappedPartnerLogos[] = [
                    'name' => $name,
                    'image_path' => $storedPath,
                ];
            }
        }

        HomePageSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'hero_title' => 'TIARANA FARMA',
                'hero_subtitle_primary' => 'Melayani Dengan Sepenuh Hati',
                'hero_subtitle_secondary' => 'Berdiri Sejak 2021',
                'hero_background_image_path' => $heroImagePath,
                'about_title' => 'Tentang Kami',
                'about_description' => 'Apotek Tiarana Farma telah melayani masyarakat sejak tahun 2021 dengan komitmen untuk menyediakan layanan kesehatan terbaik dan produk berkualitas. Dengan tim apoteker profesional, kami siap membantu Anda dengan konsultasi kesehatan dan informasi penggunaan obat yang tepat.',
                'about_image_path' => $aboutImagePath,
            ]
        );

        $featureHighlights = [
            [
                'title' => 'Resep & Non-Resep',
                'icon' => 'fas fa-pills',
                'description' => 'Layanan obat resep dan non-resep dengan konsultasi farmasi profesional',
                'sort_order' => 1,
            ],
            [
                'title' => 'Jam Operasional',
                'icon' => 'fas fa-clock',
                'description' => 'Buka setiap hari dari pukul 08:00 - 22:00 WITA',
                'sort_order' => 2,
            ],
            [
                'title' => 'Produk Terjamin',
                'icon' => 'fas fa-shield-alt',
                'description' => 'Keaslian dan kualitas produk terjamin dengan izin resmi BPOM',
                'sort_order' => 3,
            ],
        ];

        foreach ($featureHighlights as $highlight) {
            HomeFeatureHighlight::query()->updateOrCreate(
                ['title' => $highlight['title']],
                Arr::except($highlight, ['title'])
            );
        }

        $aboutFeatures = [
            [
                'title' => 'Apoteker Berpengalaman',
                'icon' => 'fas fa-certificate',
                'sort_order' => 1,
            ],
            [
                'title' => 'Produk Berkualitas',
                'icon' => 'fas fa-check-circle',
                'sort_order' => 2,
            ],
            [
                'title' => 'Pelayanan Ramah',
                'icon' => 'fas fa-heart',
                'sort_order' => 3,
            ],
        ];

        foreach ($aboutFeatures as $feature) {
            HomeAboutFeature::query()->updateOrCreate(
                ['title' => $feature['title']],
                Arr::except($feature, ['title'])
            );
        }

        $services = [
            [
                'title' => 'Layanan Resep',
                'icon' => 'fas fa-prescription-bottle-alt',
                'description' => 'Kami menyediakan layanan resep dokter dengan standar tinggi dan penuh ketelitian. Apoteker profesional kami akan memastikan setiap resep diproses dengan tepat dan aman, disertai dengan konsultasi mengenai penggunaan obat yang benar.',
                'items' => [
                    'Pelayanan resep dokter cepat dan akurat',
                    'Konsultasi penggunaan obat dengan apoteker',
                    'Pemeriksaan interaksi obat',
                    'Informasi efek samping dan cara penggunaan',
                ],
                'image_path' => $serviceImagePath,
                'sort_order' => 1,
            ],
            [
                'title' => 'Konsultasi Kesehatan',
                'icon' => 'fas fa-notes-medical',
                'description' => 'Dapatkan konsultasi kesehatan gratis dengan apoteker berpengalaman kami. Kami siap membantu Anda dengan berbagai pertanyaan seputar kesehatan dan penggunaan obat yang tepat.',
                'items' => [
                    'Konsultasi gratis dengan apoteker',
                    'Informasi penggunaan obat yang aman',
                    'Pemeriksaan kesehatan dasar',
                    'Edukasi kesehatan',
                ],
                'image_path' => $serviceImagePath,
                'sort_order' => 2,
            ],
            [
                'title' => 'Pemeriksaan Kesehatan',
                'icon' => 'fas fa-heartbeat',
                'description' => 'Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.',
                'items' => [
                    'Cek tekanan darah',
                    'Pemeriksaan gula darah',
                    'Pemeriksaan Kolestrol dan Asam Urat',
                    'Konsultasi hasil pemeriksaan',
                ],
                'image_path' => $serviceImagePath,
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $service) {
            HomeService::query()->updateOrCreate(
                ['title' => $service['title']],
                Arr::except($service, ['title'])
            );
        }

        foreach ($mappedPartnerLogos as $index => $logo) {
            PartnerLogo::query()->updateOrCreate(
                ['name' => $logo['name']],
                [
                    'image_path' => $logo['image_path'],
                    'sort_order' => $index + 1,
                ]
            );
        }
    }

    private function ensurePublicAsset(string $relativeSourcePath, string $relativeDestinationPath): ?string
    {
        $source = resource_path($relativeSourcePath);

        if (! File::exists($source)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($relativeDestinationPath)) {
            $disk->put($relativeDestinationPath, File::get($source));
        }

        return $relativeDestinationPath;
    }
}
