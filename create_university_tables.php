<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Create university_profiles table
    $sql = "CREATE TABLE IF NOT EXISTS university_profiles (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        representative_name VARCHAR(100) NOT NULL,
        location VARCHAR(255),
        address TEXT,
        phone VARCHAR(20),
        email VARCHAR(100),
        website VARCHAR(200),
        description TEXT,
        logo_url VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "University profiles table created successfully<br>";

    // Create university_programs table
    $sql = "CREATE TABLE IF NOT EXISTS university_programs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        university_id INT NOT NULL,
        program_name VARCHAR(200) NOT NULL,
        description TEXT,
        duration VARCHAR(50),
        degree_type VARCHAR(100),
        admission_deadline DATE,
        requirements TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES university_profiles(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "University programs table created successfully<br>";

    // Create applications table
    $sql = "CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT NOT NULL,
        university_id INT NOT NULL,
        program_id INT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (university_id) REFERENCES university_profiles(id) ON DELETE CASCADE,
        FOREIGN KEY (program_id) REFERENCES university_programs(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Applications table created successfully<br>";

    // Create documents table
    $sql = "CREATE TABLE IF NOT EXISTS documents (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        document_type VARCHAR(50) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        verification_notes TEXT,
        verified_by INT,
        verified_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
        FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Documents table created successfully<br>";

    echo "All university-related tables have been created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 