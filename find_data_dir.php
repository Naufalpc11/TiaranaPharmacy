<?php

try {
    $host = '127.0.0.1';
    $port = '3306';
    $username = 'admin_tiarana';
    $password = 'Tiarana_1774';
    
    echo "Connecting to MySQL...\n";
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Getting MySQL data directory...\n\n";
    $result = $pdo->query("SHOW VARIABLES LIKE 'datadir'")->fetch(PDO::FETCH_ASSOC);
    
    echo "Data Directory: " . $result['Value'] . "\n";
    echo "\nDatabase directory: " . $result['Value'] . "tiaranapharmacy\\\n";
    echo "\n⚠️  You need to manually delete the tiaranapharmacy folder and recreate it.\n";
    echo "\nSteps:\n";
    echo "1. Stop MySQL/MariaDB service\n";
    echo "2. Delete folder: " . $result['Value'] . "tiaranapharmacy\n";
    echo "3. Start MySQL/MariaDB service\n";
    echo "4. Run: php recreate_db.php\n";
    echo "5. Run: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
