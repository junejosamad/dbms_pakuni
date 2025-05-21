<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get dashboard data with default values
$stats = $dashboard->getDashboardStats() ?? [
    'total_applications' => 0,
    'pending_review' => 0,
    'accepted' => 0,
    'active_programs' => 0
];

$recent_applications = $dashboard->getRecentApplications() ?? [];
$program_deadlines = $dashboard->getProgramDeadlines() ?? [];
$university_info = $dashboard->getUniversityInfo() ?? [
    'name' => 'Your University',
    'representative_name' => $_SESSION['user_name'] ?? 'University Representative'
];

// Handle application status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'update_status':
            if (isset($_POST['application_id']) && isset($_POST['status'])) {
                if ($dashboard->updateApplicationStatus($_POST['application_id'], $_POST['status'])) {
                    $response['success'] = true;
                    $response['message'] = 'Application status updated successfully';
                } else {
                    $response['message'] = 'Failed to update application status';
                }
            }
            break;
            
        case 'update_deadline':
            if (isset($_POST['program_id']) && isset($_POST['deadline'])) {
                if ($dashboard->updateProgramDeadline($_POST['program_id'], $_POST['deadline'])) {
                    $response['success'] = true;
                    $response['message'] = 'Program deadline updated successfully';
                } else {
                    $response['message'] = 'Failed to update program deadline';
                }
            }
            break;
    }
    
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'University Representative';
$university_name = $university_info['name'] ?? 'Your University';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Dashboard - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
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
            <li><a href="university-dashboard.php" class="active">Dashboard</a></li>
            <li><a href="logout.php" class="btn-login">Logout</a></li>
        </ul>
    </nav>

    <main class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p>University Representative</p>
                <p class="university-name"><?php echo htmlspecialchars($university_name); ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="#applications"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li><a href="#deadlines"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="#programs"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <div class="dashboard-content">
            <div class="dashboard-header">
                <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search applications...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>Total Applications</h3>
                        <p><?php echo $stats['total_applications']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-info">
                        <h3>Pending Review</h3>
                        <p><?php echo $stats['pending_review']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-info">
                        <h3>Accepted</h3>
                        <p><?php echo $stats['accepted']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="stat-info">
                        <h3>Active Programs</h3>
                        <p><?php echo $stats['active_programs']; ?></p>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <section class="recent-applications">
                    <h3>Recent Applications</h3>
                    <div class="application-list">
                        <?php foreach ($recent_applications as $application): ?>
                        <div class="application-card" data-application-id="<?php echo $application['id']; ?>">
                            <div class="university-info">
                                <img src="https://via.placeholder.com/50" alt="Student Photo">
                                <div>
                                    <h4><?php echo htmlspecialchars($application['student_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($application['program_name']); ?></p>
                                </div>
                            </div>
                            <div class="application-actions">
                                <div class="application-status <?php echo strtolower($application['status']); ?>">
                                    <span><?php echo $application['status']; ?></span>
                                </div>
                                <div class="status-actions">
                                    <button class="status-update" data-status="Accepted">Accept</button>
                                    <button class="status-update" data-status="Rejected">Reject</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="upcoming-deadlines">
                    <h3>Program Deadlines</h3>
                    <div class="deadline-list">
                        <?php foreach ($program_deadlines as $program): ?>
                        <div class="deadline-card" data-program-id="<?php echo $program['id']; ?>">
                            <div class="deadline-info">
                                <h4><?php echo htmlspecialchars($program['program_name']); ?></h4>
                                <p>Admission Deadline</p>
                            </div>
                            <div class="deadline-date">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo date('F j, Y', strtotime($program['admission_deadline'])); ?></span>
                            </div>
                            <div class="deadline-actions">
                                <input type="date" class="deadline-input" value="<?php echo $program['admission_deadline']; ?>">
                                <button class="update-deadline">Update</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="deadline-form">
                        <h4>Add New Deadline</h4>
                        <form action="manage-deadlines.php" method="POST">
                            <select name="program" required>
                                <option value="">Select Program</option>
                                <option value="cs">Computer Science</option>
                                <option value="ba">Business Administration</option>
                                <option value="ee">Electrical Engineering</option>
                            </select>
                            <input type="date" name="deadline" required>
                            <button type="submit" class="btn-primary">Set Deadline</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About PakUni</h3>
                <p>Simplifying university applications for students across Pakistan</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@pakuni.com</p>
                <p>Phone: +92 300 1234567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 PakUni. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/university-dashboard.js"></script>
</body>
</html> 