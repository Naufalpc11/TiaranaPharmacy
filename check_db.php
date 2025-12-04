<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking database connection...\n";
    
    $tables = DB::select('SHOW TABLES');
    
    echo "\nTables found in database:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    
    echo "\nChecking table engines:\n";
    $engines = DB::select("
        SELECT TABLE_NAME, ENGINE 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = 'tiaranapharmacy'
    ");
    
    foreach ($engines as $engine) {
        echo "- {$engine->TABLE_NAME}: {$engine->ENGINE}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
