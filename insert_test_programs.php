<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // For testing purposes, we'll use a hardcoded university ID
    $university_id = 1; // Assuming this is your university's ID

    // Sample programs data
    $programs = [
        [
            'name' => 'Bachelor of Computer Science',
            'description' => 'A comprehensive program covering programming, algorithms, and software development.',
            'duration' => '4 years',
            'degree_type' => 'Undergraduate',
            'admission_deadline' => '2024-08-15',
            'status' => 'active'
        ],
        [
            'name' => 'Master of Business Administration',
            'description' => 'Advanced business management program with focus on leadership and strategy.',
            'duration' => '2 years',
            'degree_type' => 'Graduate',
            'admission_deadline' => '2024-07-30',
            'status' => 'active'
        ],
        [
            'name' => 'PhD in Electrical Engineering',
            'description' => 'Research-focused program in electrical engineering and power systems.',
            'duration' => '4 years',
            'degree_type' => 'PhD',
            'admission_deadline' => '2024-09-01',
            'status' => 'active'
        ],
        [
            'name' => 'Bachelor of Architecture',
            'description' => 'Comprehensive architecture program with focus on design and construction.',
            'duration' => '5 years',
            'degree_type' => 'Undergraduate',
            'admission_deadline' => '2024-08-20',
            'status' => 'active'
        ],
        [
            'name' => 'Master of Data Science',
            'description' => 'Advanced program in data analysis, machine learning, and big data.',
            'duration' => '2 years',
            'degree_type' => 'Graduate',
            'admission_deadline' => '2024-07-25',
            'status' => 'active'
        ]
    ];

    // Insert programs
    $query = "INSERT INTO programs (
        university_id, name, description, duration, 
        degree_type, admission_deadline, status
    ) VALUES (
        :university_id, :name, :description, :duration,
        :degree_type, :admission_deadline, :status
    )";

    $stmt = $pdo->prepare($query);

    foreach ($programs as $program) {
        $stmt->bindParam(':university_id', $university_id);
        $stmt->bindParam(':name', $program['name']);
        $stmt->bindParam(':description', $program['description']);
        $stmt->bindParam(':duration', $program['duration']);
        $stmt->bindParam(':degree_type', $program['degree_type']);
        $stmt->bindParam(':admission_deadline', $program['admission_deadline']);
        $stmt->bindParam(':status', $program['status']);
        
        $stmt->execute();
    }

    echo "Sample programs added successfully!";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 