<?php
session_start();
require_once 'university-dashboard-functions.php';

// Check if user is not logged in or not a university representative
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'university') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get search term from query parameter
$searchTerm = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($searchTerm)) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Search applications
$results = $dashboard->searchApplications($searchTerm);

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($results); 