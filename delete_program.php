<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['program_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing program ID']);
    exit();
}

// Delete program
if ($dashboard->deleteProgram($data['program_id'])) {
    echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete program']);
} 