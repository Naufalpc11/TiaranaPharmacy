<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Dropping corrupt tables...\n\n";
    
    $tables = DB::select('SHOW TABLES');
    $droppedCount = 0;
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "Dropping table: $tableName\n";
        try {
            DB::statement("DROP TABLE IF EXISTS `$tableName`");
            $droppedCount++;
        } catch (\Exception $e) {
            echo "  Warning: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nTotal tables dropped: $droppedCount\n";
    echo "\nNow run: php artisan migrate\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
