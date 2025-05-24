<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Drop tables in reverse order of dependencies
    $tables = [
        'documents',
        'applications',
        'programs',
        'university_profiles',
        'students',
        'universities',
        'users'
    ];

    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
        echo "Dropped table $table<br>";
    }

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "All tables dropped successfully";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 