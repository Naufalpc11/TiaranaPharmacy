<?php

namespace Database\Seeders;

use App\Models\AboutContactDetail;
use App\Models\AboutHistoryStat;
use App\Models\AboutMission;
use App\Models\AboutPageSetting;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'hero_title' => 'Tentang Kami',
            'hero_subtitle' => 'Apotek Tiarana Farma adalah apotek terpercaya yang menyediakan layanan kesehatan berkualitas dengan dukungan tim profesional dan fasilitas modern.',
            'hero_primary_button_text' => 'Lokasi',
            'hero_primary_button_url' => '#lokasi',
            'hero_secondary_button_text' => 'Hubungi Kami',
            'hero_secondary_button_url' => '/contact',
            'vision_title' => 'Visi',
            'vision_text' => 'Menjadi apotek terpercaya berbasis komunitas yang mengutamakan pelayanan tatap muka yang aman, ramah, dan akurat bagi keluarga Indonesia.',
            'mission_title' => 'Misi',
            'history_title' => 'Sejarah Kami',
            'history_description' => implode(PHP_EOL . PHP_EOL, [
                'Berdiri pada 2021 di Balikpapan, Tiarana Farma lahir dari misi sederhana: memudahkan akses obat yang aman dan terjangkau.',
                'Dari sebuah apotek kecil kami bertumbuh menjadi layanan farmasi modern yang mengedepankan konsultasi tatap muka, stok terkurasi, dan proses pembelian yang mudah.',
                'Hingga kini, kami telah melayani lebih dari 5.000 pelanggan dan terus berinovasi demi kesehatan keluarga Indonesia.',
            ]),
            'team_title' => 'Apoteker Kami',
            'team_intro' => 'Kenali apoteker penanggung jawab kami di balik layanan Tiarana Farma.',
            'pharmacist_name' => 'apt. Titik Tresnowati, S. Si',
            'pharmacist_role' => 'Apoteker Penanggung Jawab',
            'pharmacist_stra' => '19880824/STRA-YYYY/2023',
            'pharmacist_sipa' => '19880824/SIPA-XX/2023',
            'pharmacist_schedule' => 'Senin-Sabtu, 17.00-22.00 WITA',
            'pharmacist_badges' => [
                'STRA & SIPA terverifikasi',
                'On-the-job pengalaman',
            ],
            'pharmacist_photo_alt' => 'Foto Apoteker Penanggung Jawab',
            'location_title' => 'Lokasi Kami',
            'location_map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid',
        ];

        AboutPageSetting::query()->firstOrCreate([], $settings)->update($settings);

        $missions = [
            [
                'title' => 'Pelayanan tatap muka yang ramah',
                'description' => 'Memprioritaskan konsultasi langsung dengan apoteker, memastikan kebutuhan pasien, dan pendampingan penggunaan obat di lokasi apotek.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Mutu, keaslian, dan kepatuhan',
                'description' => 'Mengelola obat sesuai standar praktik kefarmasian dan regulasi BPOM untuk menjamin keamanan pasien.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Pengadaan obat melalui PBF resmi',
                'description' => 'Seluruh pengadaan dilakukan dari Pedagang Besar Farmasi (PBF) resmi dengan kepatuhan CDOB.',
                'sort_order' => 3,
            ],
        ];

        foreach ($missions as $mission) {
            AboutMission::query()->updateOrCreate(
                ['title' => $mission['title']],
                [
                    'description' => $mission['description'],
                    'sort_order' => $mission['sort_order'],
                ],
            );
        }

        $historyStats = [
            [
                'icon' => 'fa-regular fa-calendar-check',
                'value' => '2021',
                'label' => 'Mulai beroperasi',
                'sort_order' => 1,
            ],
            [
                'icon' => 'fa-solid fa-people-group',
                'value' => '5.000+',
                'label' => 'Pelanggan dilayani',
                'sort_order' => 2,
            ],
            [
                'icon' => 'fa-solid fa-pills',
                'value' => '200+',
                'label' => 'Produk tersedia',
                'sort_order' => 3,
            ],
        ];

        foreach ($historyStats as $stat) {
            AboutHistoryStat::query()->updateOrCreate(
                ['label' => $stat['label']],
                [
                    'icon' => $stat['icon'],
                    'value' => $stat['value'],
                    'sort_order' => $stat['sort_order'],
                ],
            );
        }

        $contactDetails = [
            [
                'icon' => 'fa-solid fa-map-location-dot',
                'title' => 'Alamat',
                'lines' => [
                    'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12',
                    'Sepinggan, Balikpapan, Kalimantan Timur 76116',
                ],
                'copy_text' => 'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12, Sepinggan, Balikpapan, Kalimantan Timur 76116',
                'sort_order' => 1,
            ],
            [
                'icon' => 'fa-solid fa-phone',
                'title' => 'Telepon',
                'lines' => ['0821-2000-3948'],
                'copy_text' => '0821-2000-3948',
                'sort_order' => 2,
            ],
            [
                'icon' => 'fa-solid fa-clock',
                'title' => 'Jam Operasional',
                'lines' => [
                    'Senin-Sabtu: 08.00-21.00 WITA',
                    'Minggu: 09.00-20.00 WITA',
                ],
                'copy_text' => "Senin-Sabtu: 08.00-21.00 WITA\nMinggu: 09.00-20.00 WITA",
                'sort_order' => 3,
            ],
        ];

        foreach ($contactDetails as $detail) {
            AboutContactDetail::query()->updateOrCreate(
                ['title' => $detail['title']],
                [
                    'icon' => $detail['icon'],
                    'lines' => $detail['lines'],
                    'copy_text' => $detail['copy_text'],
                    'sort_order' => $detail['sort_order'],
                ]
            );
        }
    }
}
