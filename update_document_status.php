<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($input['document_id']) && isset($input['status'])) {
        $document_id = $input['document_id'];
        $status = $input['status'];
        $notes = $input['notes'] ?? '';

        if ($dashboard->updateDocumentStatus($document_id, $status, $notes)) {
            $response['success'] = true;
            $response['message'] = 'Document status updated successfully';
        } else {
            $response['message'] = 'Failed to update document status';
        }
    } else {
        $response['message'] = 'Missing required parameters';
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response); 