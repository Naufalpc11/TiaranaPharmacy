<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route untuk halaman utama
Route::get('/', function () {
    return Inertia::render('Home');
});

// Route untuk halaman About Us
Route::get('/about-us', function () {
    return Inertia::render('AboutUs');
});

// Route untuk halaman Contact
Route::get('/contact', function () {
    return Inertia::render('Contact');
});

// Route untuk halaman Artikel
Route::get('/artikel', function () {
    return Inertia::render('Artikel');
});

Route::get('/artikel/{slug}', function (string $slug) {
    $articles = [
        'amoksisilin-kapan-perlu-kapan-tidak' => [
            'title' => 'Amoksisilin: Kapan Perlu, Kapan Tidak',
            'date' => '2025-08-12',
            'heroImage' => '/images/articles/amoksisilin.jpg',
            'introduction' => [
                'Amoksisilin adalah antibiotik golongan penisilin yang efektif melawan banyak infeksi bakteri. Tapi - dan ini penting - antibiotik tidak bekerja untuk penyakit yang disebabkan virus seperti flu, pilek, atau sebagian besar sakit tenggorokan. Menggunakan antibiotik saat tidak perlu tidak akan mempercepat sembuh dan justru bisa menimbulkan efek samping serta mempercepat terjadinya resistensi antibiotik.',
            ],
            'sections' => [
                [
                    'title' => 'Mengapa bijak memakai antibiotik itu penting?',
                    'body' => [
                        ['type' => 'paragraph', 'text' => 'WHO baru-baru ini menegaskan bahwa 1 dari 6 infeksi bakteri di dunia sudah resisten terhadap pengobatan antibiotik. Penyalahgunaan dan penggunaan berlebihan antibiotik adalah pemicunya.'],
                    ],
                ],
                [
                    'title' => 'Amoksisilin dapat diresepkan dokter untuk infeksi bakteri tertentu, misalnya:',
                    'body' => [
                        ['type' => 'paragraph', 'text' => 'Amoksisilin dapat diresepkan dokter untuk infeksi bakteri tertentu, misalnya:'],
                        [
                            'type' => 'list',
                            'ordered' => false,
                            'items' => [
                                'Radang tenggorok bakteri (strep throat) setelah tes cepat/radang tenggorok kultur positif. Antibiotik memang diperlukan bila hasil tes positif; jangan mengobati sakit tenggorok virus dengan antibiotik.',
                                'Sinusitis bakteri akut dengan ciri khas: gejala >10 hari tanpa membaik, demam tinggi & nyeri wajah purulen >= 3 hari, atau "double-sickening" (awal membaik lalu memburuk lagi). Kriteria ini membantu membedakan dari sinusitis virus yang tidak butuh antibiotik.',
                                'Otitis media (infeksi telinga tengah), infeksi saluran napas bawah bakteri, infeksi kulit tertentu, atau infeksi gigi - sesuai penilaian klinis dokter dan hasil pemeriksaan. (Prinsip umum: pastikan ada bukti kuat infeksi bakteri, bukan virus.)',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Tanda-tanda yang Perlu Dievaluasi Dokter',
                    'body' => [
                        ['type' => 'paragraph', 'text' => 'Segera konsultasi bila Anda mengalami salah satu dari berikut:'],
                        [
                            'type' => 'list',
                            'ordered' => false,
                            'items' => [
                                'Demam tinggi, sesak napas, nyeri telinga hebat, nyeri wajah purulen, atau gejala >10 hari tanpa membaik.',
                                'Radang tenggorok disertai pembesaran kelenjar leher, demam, tidak ada batuk - dokter dapat menilai dan melakukan tes strep bila perlu.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Cara Pakai yang Benar',
                    'body' => [
                         ['type' => 'paragraph', 'text' => 'Gunakan hanya dengan resep dokter dan ikuti aturan pakai dengan tepat. Di Indonesia, amoksisilin termasuk obat keras - pembeliannya harus di apotek dengan resep.'],
                        [
                            'type' => 'list',
                            'ordered' => true,
                            'items' => [
                                'Ikuti jadwal minum sesuai anjuran dokter dan jangan menggandakan dosis bila lupa.',
                                'Habiskan sesuai durasi resep (jangan berhenti mendadak tanpa saran tenaga kesehatan), meski gejala terasa membaik. Ini membantu mencegah kambuh dan resistensi.',
                                'Jangan berbagi obat, menyimpan sisa untuk sakit lain, atau mengulang resep lama tanpa evaluasi.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Efek Samping yang Perlu Diwaspadai',
                    'body' => [
                        ['type' => 'paragraph', 'text' => 'Efek yang umum: mual, diare, ruam, pusing, infeksi jamur.'],
                        ['type' => 'paragraph', 'text' => 'Efek yang jarang tetapi serius: alergi berat (anafilaksis) dan infeksi Clostridioides difficile yang menimbulkan diare berat - butuh pertolongan medis segera. Jika Anda mengalami diare berat atau darah/lendir pada tinja selama atau setelah minum antibiotik, segera hubungi tenaga kesehatan.'],
                    ],
                ],
                [
                    'title' => 'Peringatan & Interaksi Obat',
                    'body' => [
                        ['type' => 'paragraph', 'text' => 'Sebelum minum amoksisilin, beri tahu dokter/apoteker bila Anda:'],
                        [
                            'type' => 'list',
                            'ordered' => false,
                            'items' => [
                                'Alergi penisilin atau pernah mengalami reaksi alergi obat.',
                                'Memiliki penyakit ginjal atau sedang hamil/menyusui (umumnya aman bila diresepkan, tetapi tetap perlu penilaian).',
                                'Mengonsumsi obat tertentu - misalnya methotrexate (risiko peningkatan toksisitas) atau pengencer darah seperti warfarin (kadang dapat memengaruhi INR, perlu pemantauan).',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    abort_unless(isset($articles[$slug]), 404);

    return Inertia::render('ArticleDetail', [
        'article' => $articles[$slug],
    ]);
});

