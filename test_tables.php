<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing direct table access...\n\n";
    
    // Test each table
    $tables = ['users', 'sessions', 'cache', 'migrations'];
    
    foreach ($tables as $table) {
        echo "Testing table: $table\n";
        try {
            $result = DB::select("SELECT * FROM `$table` LIMIT 1");
            echo "  ✓ OK - Can read from $table\n";
        } catch (\Exception $e) {
            echo "  ✗ ERROR - " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    echo "\nChecking MySQL version and settings:\n";
    $version = DB::select("SELECT VERSION() as version");
    echo "MySQL Version: " . $version[0]->version . "\n";
    
    echo "\nChecking table status:\n";
    $status = DB::select("SHOW TABLE STATUS FROM tiaranapharmacy");
    foreach ($status as $s) {
        if (in_array($s->Name, $tables)) {
            echo "- {$s->Name}: Engine={$s->Engine}, Rows={$s->Rows}, Comment={$s->Comment}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
