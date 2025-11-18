<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$catalog=app(App\Services\MedicationCatalog::class);
$med=$catalog->matchByQuery('lacto b');
var_dump($med['slug'] ?? null);
var_dump($catalog->formatDetails($med));
