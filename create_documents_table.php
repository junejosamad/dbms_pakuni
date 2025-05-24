<?php
require_once 'config/database.php';

try {
    // Initialize database connection
    $database = new Database();
    $pdo = $database->getConnection();

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
    echo "Documents table created successfully";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 