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
if (!isset($data['program_id']) || !isset($data['deadline'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

// Validate deadline format
if (!strtotime($data['deadline'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid deadline format']);
    exit();
}

// Update program deadline
if ($dashboard->updateProgramDeadline($data['program_id'], $data['deadline'])) {
    echo json_encode(['success' => true, 'message' => 'Deadline updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update deadline']);
} 