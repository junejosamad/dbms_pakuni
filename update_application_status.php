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
if (!isset($data['application_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$application_id = $data['application_id'];
$status = $data['status'];

// Validate status
$valid_statuses = ['pending', 'accepted', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update application status
$success = $dashboard->updateApplicationStatus($application_id, $status);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Application status updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update application status']);
} 