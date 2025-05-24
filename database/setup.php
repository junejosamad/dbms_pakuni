<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Create database connection without database name
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!\n";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
?> 