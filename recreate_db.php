<?php

try {
    $host = '127.0.0.1';
    $port = '3306';
    $username = 'admin_tiarana';
    $password = 'Tiarana_1774';
    $dbname = 'tiaranapharmacy';
    
    echo "Connecting to MySQL...\n";
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Dropping database if exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    
    echo "Creating fresh database...\n";
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "✓ Database recreated successfully!\n";
    echo "\nNow run: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
