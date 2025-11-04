<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Capacity (MB)
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk menghitung persentase penggunaan basis data. Atur nilai
    | ini melalui variabel lingkungan DB_MAX_SIZE_MB sesuai batas kapasitas
    | server Anda.
    |
    */
    'max_size_mb' => (int) env('DB_MAX_SIZE_MB', 1024),

    /*
    |--------------------------------------------------------------------------
    | Threshold Persentase Notifikasi
    |--------------------------------------------------------------------------
    |
    | Ketika persentase penggunaan melebihi nilai ini, admin akan menerima
    | notifikasi di panel Filament.
    |
    */
    'threshold_percent' => (float) env('DB_USAGE_THRESHOLD_PERCENT', 90),
];
