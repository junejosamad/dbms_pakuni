<?php
require_once 'config/database.php';

try {
    // Initialize database connection
    $database = new Database();
    $pdo = $database->getConnection();

    // Create users table first
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('student', 'university', 'admin') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Users table created successfully<br>";

    // Create university_profiles table
    $sql = "CREATE TABLE IF NOT EXISTS university_profiles (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        location VARCHAR(255),
        representative_name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        logo_url VARCHAR(255),
        is_active BOOLEAN DEFAULT TRUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    echo "University profiles table created successfully<br>";

    // Create students table
    $sql = "CREATE TABLE IF NOT EXISTS students (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        date_of_birth DATE,
        gender ENUM('male', 'female', 'other'),
        phone VARCHAR(20),
        address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    echo "Students table created successfully<br>";

    // Create universities table
    $sql = "CREATE TABLE IF NOT EXISTS universities (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        address TEXT,
        phone VARCHAR(20),
        email VARCHAR(100),
        website VARCHAR(100),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    echo "Universities table created successfully<br>";

    // Create programs table
    $query = "CREATE TABLE IF NOT EXISTS programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        university_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        duration VARCHAR(50) NOT NULL,
        degree_type VARCHAR(50) NOT NULL,
        admission_deadline DATE NOT NULL,
        requirements TEXT,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES universities(id)
    )";
    
    $pdo->exec($query);
    echo "Programs table created successfully<br>";

    // Create applications table
    $sql = "CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT NOT NULL,
        program_id INT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id),
        FOREIGN KEY (program_id) REFERENCES programs(id)
    )";
    $pdo->exec($sql);
    echo "Applications table created successfully<br>";

    // Create documents table
    $sql = "CREATE TABLE IF NOT EXISTS documents (
        id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT NOT NULL,
        application_id INT NOT NULL,
        document_type VARCHAR(50) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        verification_notes TEXT,
        verified_by INT,
        verification_date DATETIME,
        FOREIGN KEY (student_id) REFERENCES students(id),
        FOREIGN KEY (application_id) REFERENCES applications(id),
        FOREIGN KEY (verified_by) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    echo "Documents table created successfully<br>";

    // Create university_programs table
    $sql = "CREATE TABLE IF NOT EXISTS university_programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        university_id INT NOT NULL,
        program_name VARCHAR(255) NOT NULL,
        description TEXT,
        duration INT,
        degree_type VARCHAR(50),
        admission_deadline DATE,
        status VARCHAR(20) DEFAULT 'active',
        requirements TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES universities(id)
    )";

    $pdo->exec($sql);
    echo "Table university_programs created successfully<br>";

} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 