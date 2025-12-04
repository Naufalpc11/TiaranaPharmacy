<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\BugReport;
use App\Models\ChatConversation;
use App\Models\HomePageSetting;
use App\Models\AboutPageSetting;

echo "====================================\n";
echo "  PENGECEKAN SISTEM TIARANA PHARMACY\n";
echo "====================================\n\n";

$errors = [];
$warnings = [];

// 1. CEK DATABASE CONNECTION
echo "1. Cek Koneksi Database...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Koneksi database berhasil\n";
} catch (\Exception $e) {
    $errors[] = "Database connection failed: " . $e->getMessage();
    echo "   ✗ Koneksi database gagal\n";
}

// 2. CEK TABEL-TABEL UTAMA
echo "\n2. Cek Tabel Database...\n";
$tables = [
    'users', 'sessions', 'articles', 'contact_messages', 'bug_reports',
    'chat_conversations', 'chat_messages', 'home_page_settings', 
    'about_page_settings', 'medication_assets', 'partner_logos'
];

foreach ($tables as $table) {
    try {
        DB::table($table)->limit(1)->get();
        echo "   ✓ Tabel '$table' OK\n";
    } catch (\Exception $e) {
        $errors[] = "Table $table: " . $e->getMessage();
        echo "   ✗ Tabel '$table' error\n";
    }
}

// 3. CEK ADMIN USER
echo "\n3. Cek Admin User...\n";
try {
    $admin = User::where('is_admin', true)->first();
    if ($admin) {
        echo "   ✓ Admin user ditemukan: {$admin->email}\n";
    } else {
        $warnings[] = "Tidak ada admin user";
        echo "   ⚠ Tidak ada admin user\n";
    }
} catch (\Exception $e) {
    $errors[] = "Admin check: " . $e->getMessage();
    echo "   ✗ Error saat cek admin\n";
}

// 4. CEK GEMINI API KEY
echo "\n4. Cek Konfigurasi API...\n";
$geminiKey = env('GEMINI_API_KEY');
if ($geminiKey && $geminiKey !== '') {
    echo "   ✓ Gemini API Key tersedia\n";
} else {
    $warnings[] = "Gemini API Key tidak ditemukan";
    echo "   ⚠ Gemini API Key tidak ditemukan\n";
}

// 5. CEK STORAGE
echo "\n5. Cek Storage & Permissions...\n";
$storageDirs = [
    'storage/app/public',
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views'
];

foreach ($storageDirs as $dir) {
    if (is_dir(base_path($dir)) && is_writable(base_path($dir))) {
        echo "   ✓ Directory '$dir' writable\n";
    } else {
        $warnings[] = "Directory $dir not writable";
        echo "   ⚠ Directory '$dir' not writable\n";
    }
}

// 6. CEK MODELS & RELATIONSHIPS
echo "\n6. Cek Models...\n";
try {
    // Test Article model
    Article::query()->limit(1)->get();
    echo "   ✓ Article model OK\n";
    
    // Test ContactMessage model
    ContactMessage::query()->limit(1)->get();
    echo "   ✓ ContactMessage model OK\n";
    
    // Test BugReport model
    BugReport::query()->limit(1)->get();
    echo "   ✓ BugReport model OK\n";
    
    // Test ChatConversation model
    ChatConversation::query()->limit(1)->get();
    echo "   ✓ ChatConversation model OK\n";
    
} catch (\Exception $e) {
    $errors[] = "Model test: " . $e->getMessage();
    echo "   ✗ Error saat test models\n";
}

// 7. CEK ROUTES
echo "\n7. Cek Routes...\n";
try {
    $routes = Route::getRoutes();
    $publicRoutes = ['/', 'about-us', 'contact', 'artikel', 'chatbot'];
    
    foreach ($publicRoutes as $route) {
        if ($routes->getByName($route) || $routes->getByAction($route)) {
            echo "   ✓ Route '$route' terdaftar\n";
        }
    }
    
    echo "   ✓ Total routes: " . count($routes) . "\n";
} catch (\Exception $e) {
    $errors[] = "Routes check: " . $e->getMessage();
    echo "   ✗ Error saat cek routes\n";
}

// SUMMARY
echo "\n====================================\n";
echo "  RINGKASAN\n";
echo "====================================\n";

if (empty($errors) && empty($warnings)) {
    echo "✅ SEMUA PENGECEKAN BERHASIL!\n";
    echo "   Sistem siap digunakan tanpa masalah.\n";
} else {
    if (!empty($errors)) {
        echo "\n❌ ERRORS DITEMUKAN:\n";
        foreach ($errors as $i => $error) {
            echo "   " . ($i + 1) . ". $error\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "\n⚠️  WARNINGS:\n";
        foreach ($warnings as $i => $warning) {
            echo "   " . ($i + 1) . ". $warning\n";
        }
    }
}

echo "\n====================================\n";
