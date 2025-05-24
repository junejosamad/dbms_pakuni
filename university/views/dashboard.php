<?php
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