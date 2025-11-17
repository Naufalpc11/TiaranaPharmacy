<?php

return [
    'catalog' => [
        [
            'slug' => 'woods-peppermint-expectorant',
            'name' => 'Woods Peppermint Obat Batuk Expectorant',
            'aliases' => ['woods peppermint', 'woods sirup', 'wood cough syrup'],
            'category' => 'Obat batuk berdahak',
            'form' => 'Sirup 100 ml',
            'symptoms' => ['batuk berdahak', 'tenggorokan gatal', 'batuk produktif'],
            'dosage' => [
                'Dewasa' => '3x sehari 1 sendok makan (15 ml).',
                'Anak 6-12 tahun' => '3x sehari 1/2 sendok makan (7,5 ml).',
            ],
            'how_it_works' => 'Kombinasi mentol dan ekspektoran membantu mengencerkan dahak sekaligus menenangkan tenggorokan.',
            'composition' => 'Glycyrrhizae Radix 8 mg, Ammonium Chloride 60 mg, Menthol 0,7 mg per 5 ml.',
            'warnings' => [
                'Tidak dianjurkan untuk batuk kering atau anak < 6 tahun.',
                'Hentikan penggunaan bila batuk lebih dari 3 hari atau disertai demam tinggi.',
            ],
            'stock_status' => 'Ready stock',
            'image' => '/storage/medications/woods-peppermint.jpg',
            'keywords' => ['sirup batuk berdahak', 'obat dahak', 'obat batuk berdahak sirup'],
            'match_threshold' => 4,
        ],
        [
            'slug' => 'paracetamol-500',
            'name' => 'Paracetamol Tablet 500 mg',
            'aliases' => ['parasetamol', 'acetaminophen', 'panadol'],
            'category' => 'Pereda demam dan nyeri ringan',
            'form' => 'Strip isi 10 tablet',
            'symptoms' => ['demam', 'sakit kepala', 'nyeri otot ringan'],
            'dosage' => [
                'Dewasa' => '1 tablet setiap 4-6 jam bila perlu. Maks 8 tablet per hari.',
                'Anak 6-12 tahun' => '1/2 tablet setiap 4-6 jam. Maks 4 tablet per hari.',
            ],
            'how_it_works' => 'Menekan pusat regulasi panas di hipotalamus sehingga menurunkan demam dan meredakan nyeri.',
            'composition' => 'Paracetamol 500 mg per tablet.',
            'warnings' => [
                'Hindari penggunaan bersamaan dengan alkohol atau obat yang mengandung paracetamol lain.',
                'Segera konsultasikan jika nyeri tidak reda dalam 5 hari atau demam > 3 hari.',
            ],
            'stock_status' => 'Ready stock',
            'image' => '/storage/medications/paracetamol-500.jpg',
            'keywords' => ['obat demam', 'penurun panas', 'analgesik'],
            'match_threshold' => 4,
        ],
        [
            'slug' => 'oralit',
            'name' => 'Oralit Larutan Elektrolit',
            'aliases' => ['oral rehydration salt', 'larutan elektrolit'],
            'category' => 'Pengganti cairan saat diare',
            'form' => 'Sachet 27,9 g',
            'symptoms' => ['diare', 'dehidrasi ringan'],
            'dosage' => [
                'Dewasa dan anak > 1 tahun' => 'Larutkan 1 sachet dalam 200 ml air matang, minum sedikit-sedikit tiap habis buang air.',
            ],
            'how_it_works' => 'Kombinasi elektrolit dan glukosa menggantikan cairan serta mineral yang hilang karena diare atau muntah.',
            'composition' => 'Glukosa anhidrat 13,5 g; Natrium klorida 2,6 g; Kalium klorida 1,5 g; Natrium sitrat 2,9 g.',
            'warnings' => [
                'Gunakan air matang dan habiskan dalam 24 jam.',
                'Segera rujuk ke fasilitas kesehatan bila diare berdarah atau tanda dehidrasi berat.',
            ],
            'stock_status' => 'Ready stock',
            'image' => '/storage/medications/oralit.jpg',
            'keywords' => ['larutan oralit', 'obat diare', 'cairan elektrolit'],
            'match_threshold' => 3,
        ],
    ],
];
