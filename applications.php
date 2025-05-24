<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get application stats
$stats = $dashboard->getDashboardStats() ?? [
    'total_applications' => 156,
    'pending_review' => 42,
    'accepted' => 89,
    'active_programs' => 25
];

// Get all applications
$applications = $dashboard->getRecentApplications(100) ?? []; // Get up to 100 applications

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'University Representative';
$university_info = $dashboard->getUniversityInfo();
if (!$university_info) {
    $university_info = [
        'name' => 'Your University',
        'representative_name' => $user_name
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .applications-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .application-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-card i {
            font-size: 2em;
            color: #2196F3;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 0.9em;
            color: #666;
        }

        .stat-info p {
            margin: 5px 0 0;
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }

        .application-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .application-filters select,
        .application-filters input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }

        .application-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .application-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .student-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .application-details {
            display: flex;
            gap: 20px;
        }

        .program-name,
        .application-date {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .application-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }

        .application-status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .application-status.accepted {
            background: #d4edda;
            color: #155724;
        }

        .application-status.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .application-actions {
            display: flex;
            gap: 10px;
        }

        .view-application,
        .accept-application,
        .reject-application {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .view-application {
            background: #2196F3;
            color: white;
            text-decoration: none;
        }

        .view-application:hover {
            background: #1976D2;
        }

        .accept-application {
            background: #4CAF50;
            color: white;
        }

        .accept-application:hover {
            background: #388E3C;
        }

        .reject-application {
            background: #f44336;
            color: white;
        }

        .reject-application:hover {
            background: #d32f2f;
        }

        .no-applications {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .no-applications i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .no-applications p {
            color: #666;
            font-size: 1.1em;
            margin: 0;
        }

        .success-message {
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            margin-top: 10px;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1>PakUni</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="universities.php">Universities</a></li>
            <li><a href="university-dashboard.php">Dashboard</a></li>
            <li><a href="logout.php" class="btn-login">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p>University Representative</p>
                <p class="university-name"><?php echo htmlspecialchars($university_info['name']); ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="university-dashboard.php"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="applications.php" class="active"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li><a href="document-verification.php"><i class="fas fa-file-upload"></i> Document Verification</a></li>
                    <li><a href="deadlines.php"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="programs.php"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="applications-container">
            <div class="page-header">
                <h2>Student Applications</h2>
                <div class="application-filters">
                    <select id="application-status">
                        <option value="all">All Applications</option>
                        <option value="pending">Pending Review</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <input type="text" placeholder="Search by student name..." id="application-search">
                </div>
            </div>

            <div class="application-stats">
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

            <div class="application-list">
                <?php if (empty($applications)): ?>
                    <div class="no-applications">
                        <i class="fas fa-file-alt"></i>
                        <p>No applications found</p>
                    </div>
                <?php else: 
                    foreach ($applications as $application): ?>
                    <div class="application-card" data-application-id="<?php echo $application['id']; ?>" data-status="<?php echo strtolower($application['status']); ?>">
                        <div class="student-info">
                            <img src="https://via.placeholder.com/50" alt="Student Photo">
                            <div>
                                <h4><?php echo htmlspecialchars($application['student_name']); ?></h4>
                                <p><?php echo htmlspecialchars($application['program_name']); ?></p>
                            </div>
                        </div>
                        <div class="application-details">
                            <div class="application-date">
                                <i class="fas fa-calendar"></i>
                                <span>Submitted: <?php echo date('M d, Y', strtotime($application['submission_date'])); ?></span>
                            </div>
                            <div class="application-status <?php echo strtolower($application['status']); ?>">
                                <i class="fas fa-circle"></i>
                                <span><?php echo $application['status']; ?></span>
                            </div>
                        </div>
                        <div class="application-actions">
                            <a href="view_application.php?id=<?php echo $application['id']; ?>" class="view-application">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if (strtolower($application['status']) === 'pending'): ?>
                            <button class="accept-application" data-status="accepted">
                                <i class="fas fa-check"></i> Accept
                            </button>
                            <button class="reject-application" data-status="rejected">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach;
                endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const applicationStatus = document.getElementById('application-status');
            const applicationSearch = document.getElementById('application-search');
            const applicationList = document.querySelector('.application-list');

            // Filter applications based on status and search
            function filterApplications() {
                const status = applicationStatus.value;
                const search = applicationSearch.value.toLowerCase();
                const applications = applicationList.querySelectorAll('.application-card');

                applications.forEach(app => {
                    const studentName = app.querySelector('.student-info h4').textContent.toLowerCase();
                    const appStatus = app.getAttribute('data-status');
                    
                    const statusMatch = status === 'all' || appStatus === status;
                    const searchMatch = studentName.includes(search);

                    app.style.display = statusMatch && searchMatch ? 'flex' : 'none';
                });
            }

            applicationStatus.addEventListener('change', filterApplications);
            applicationSearch.addEventListener('input', filterApplications);

            // Application status updates
            applicationList.addEventListener('click', async (e) => {
                const acceptBtn = e.target.closest('.accept-application');
                const rejectBtn = e.target.closest('.reject-application');
                
                if (acceptBtn || rejectBtn) {
                    const applicationCard = e.target.closest('.application-card');
                    const applicationId = applicationCard.getAttribute('data-application-id');
                    const status = acceptBtn ? 'accepted' : 'rejected';

                    try {
                        const response = await fetch('update_application_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                application_id: applicationId,
                                status: status
                            })
                        });

                        if (response.ok) {
                            applicationCard.setAttribute('data-status', status);
                            
                            // Update status display
                            const statusElement = applicationCard.querySelector('.application-status');
                            statusElement.className = `application-status ${status}`;
                            statusElement.querySelector('span').textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            
                            // Hide action buttons
                            applicationCard.querySelector('.application-actions').innerHTML = `
                                <a href="view_application.php?id=${applicationId}" class="view-application">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            `;
                            
                            // Show success message
                            const message = document.createElement('div');
                            message.className = 'success-message';
                            message.textContent = `Application ${status} successfully`;
                            applicationCard.appendChild(message);
                            
                            setTimeout(() => message.remove(), 3000);

                            // Update stats
                            location.reload();
                        }
                    } catch (error) {
                        console.error('Error updating application status:', error);
                    }
                }
            });
        });
    </script>
</body>
</html> 
=======
<?php
require_once __DIR__ . '/../../includes/templates/header.php';
require_once __DIR__ . '/../../includes/functions/auth.php';

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'University Applications';

// Get university ID
$userId = $_SESSION['user_id'];
$db = new Database();
$universityId = null;

try {
    $stmt = $db->prepare("SELECT id FROM university_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $universityId = $stmt->fetchColumn();
} catch (PDOException $e) {
    $errors[] = "Error fetching university ID: " . $e->getMessage();
}

// Handle application status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    try {
        $stmt = $db->prepare("UPDATE applications SET status = ? WHERE id = ? AND university_id = ?");
        $stmt->execute([$_POST['status'], $_POST['application_id'], $universityId]);
        $success[] = "Application status updated successfully!";
    } catch (PDOException $e) {
        $errors[] = "Error updating application status: " . $e->getMessage();
    }
}

// Fetch applications
$applications = [];
if ($universityId) {
    try {
        $stmt = $db->prepare("
            SELECT a.*, s.name as student_name, s.email as student_email, p.program_name
            FROM applications a
            JOIN students s ON a.student_id = s.id
            JOIN university_programs p ON a.program_id = p.id
            WHERE a.university_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$universityId]);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors[] = "Error fetching applications: " . $e->getMessage();
    }
}
?>

<div class="dashboard-container">
    <div class="sidebar">
        <ul>
            <li><a href="<?php echo $baseUrl; ?>/university/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/applications" class="active"><i class="fas fa-file-alt"></i> Applications</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/deadlines"><i class="fas fa-calendar-alt"></i> Manage Deadlines</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/profile"><i class="fas fa-university"></i> University Profile</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/programs"><i class="fas fa-graduation-cap"></i> Programs</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Applications</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-messages">
                <?php foreach ($success as $message): ?>
                    <div class="success"><?php echo htmlspecialchars($message); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="applications-list">
            <?php if (empty($applications)): ?>
                <p>No applications found.</p>
            <?php else: ?>
                <?php foreach ($applications as $application): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <h3><?php echo htmlspecialchars($application['student_name']); ?></h3>
                            <span class="status <?php echo strtolower($application['status']); ?>">
                                <?php echo htmlspecialchars($application['status']); ?>
                            </span>
                        </div>
                        <div class="application-details">
                            <p><strong>Program:</strong> <?php echo htmlspecialchars($application['program_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($application['student_email']); ?></p>
                            <p><strong>Applied:</strong> <?php echo date('F j, Y', strtotime($application['created_at'])); ?></p>
                        </div>
                        <?php if ($application['status'] === 'pending'): ?>
                            <div class="application-actions">
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="btn-accept">Accept</button>
                                </form>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn-reject">Reject</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/templates/footer.php'; ?> 
