<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Insert test user
    $sql = "INSERT INTO users (name, email, password, role) VALUES 
            ('Test University', 'test@university.edu', :password, 'university')";
    $stmt = $pdo->prepare($sql);
    $password = password_hash('test123', PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user_id = $pdo->lastInsertId();

    // Insert university profile
    $sql = "INSERT INTO university_profiles (user_id, name, location, representative_name, email, phone, is_active) 
            VALUES (:user_id, 'Test University', 'Test Location', 'Test Representative', 'test@university.edu', '1234567890', 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    echo "Test data inserted successfully";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 