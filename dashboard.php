<?php

session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1>PakUni</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="universities.php">Universities</a></li>
            <li><a href="admissions.php">Admissions</a></li>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
                <li><a href="register.php" class="btn-register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p><?php echo htmlspecialchars($user_role); ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="#applications"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="#documents"><i class="fas fa-folder"></i> Documents</a></li>
                    <li><a href="#profile"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <div class="dashboard-content">
            <div class="dashboard-header">
                <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
          
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../includes/functions/auth.php';
require_once __DIR__ . '/../../includes/classes/University.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
echo "Session data: <pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'University Dashboard';
$baseUrl = '/pakuni'; // Set the base URL for the application

try {
    // Initialize University class
    $university = new University($_SESSION['user_id']);

    // Get dashboard statistics
    $stats = $university->getDashboardStats();

    // Get recent applications
    $recentApplications = $university->getRecentApplications(5);
} catch (Exception $e) {
    $error = $e->getMessage();
    echo "Error: " . $error;
    $stats = [
        'total_applications' => 0,
        'pending_review' => 0,
        'accepted' => 0,
        'active_programs' => 0
    ];
    $recentApplications = [];
}

// Add additional styles
$additionalStyles = ['/css/university-dashboard.css'];

// Include header
require_once __DIR__ . '/../../includes/templates/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <ul>
            <li><a href="<?php echo $baseUrl; ?>/university/dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/applications"><i class="fas fa-file-alt"></i> Applications</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/deadlines"><i class="fas fa-calendar-alt"></i> Manage Deadlines</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/profile"><i class="fas fa-university"></i> University Profile</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/programs"><i class="fas fa-graduation-cap"></i> Programs</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Welcome to Your Dashboard</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <h3>Total Applications</h3>
                <p><?php echo $stats['total_applications']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>Pending Applications</h3>
                <p><?php echo $stats['pending_review']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Accepted Applications</h3>
                <p><?php echo $stats['accepted']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Active Programs</h3>
                <p><?php echo $stats['active_programs']; ?></p>
            </div>
        </div>

        <div class="recent-applications">
            <h2>Recent Applications</h2>
            <?php if (empty($recentApplications)): ?>
                <p class="no-data">No recent applications found.</p>
            <?php else: ?>
                <div class="application-list">
                    <?php foreach ($recentApplications as $application): ?>
                        <div class="application-card">
                            <div class="student-info">
                                <h4><?php echo htmlspecialchars($application['student_name']); ?></h4>
                                <p><?php echo htmlspecialchars($application['program_name']); ?></p>
                                <small>Applied: <?php echo date('M d, Y', strtotime($application['submitted_at'])); ?></small>
                            </div>
                            <div class="application-status <?php echo strtolower($application['status']); ?>">
                                <span><?php echo htmlspecialchars($application['status']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/templates/footer.php';
?> 