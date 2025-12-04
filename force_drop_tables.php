<?php

try {
    $host = '127.0.0.1';
    $port = '3306';
    $username = 'admin_tiarana';
    $password = 'Tiarana_1774';
    $dbname = 'tiaranapharmacy';
    
    echo "Connecting to MySQL...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Getting list of tables...\n";
    $tables = $pdo->query("SHOW FULL TABLES")->fetchAll(PDO::FETCH_NUM);
    
    echo "Dropping all tables and views...\n\n";
    
    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    foreach ($tables as $table) {
        $tableName = $table[0];
        $tableType = $table[1];
        
        echo "Dropping $tableType: $tableName\n";
        
        try {
            if ($tableType === 'VIEW') {
                $pdo->exec("DROP VIEW IF EXISTS `$tableName`");
            } else {
                $pdo->exec("DROP TABLE IF EXISTS `$tableName`");
            }
            echo "  ✓ Dropped $tableName\n";
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n✓ All tables dropped!\n";
    echo "\nNow run: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
