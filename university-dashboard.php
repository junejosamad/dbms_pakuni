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
    'total_applications' => 156,
    'pending_review' => 42,
    'accepted' => 89,
    'active_programs' => 25
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
    <style>
        /* Dashboard Sections */
        .dashboard-sections {
            padding: 20px;
        }

        .dashboard-section {
            display: none;
        }

        .dashboard-section.active {
            display: block;
        }

        /* Document Verification Styles */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .document-filters {
            display: flex;
            gap: 10px;
        }

        .document-filters select,
        .document-filters input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .document-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .document-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .document-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .document-details {
            display: flex;
            gap: 20px;
        }

        .document-type,
        .document-date {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .document-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }

        .verification-actions {
            display: flex;
            gap: 10px;
        }

        .verify-document,
        .reject-document {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .verify-document {
            background: #4CAF50;
            color: white;
        }

        .reject-document {
            background: #f44336;
            color: white;
        }

        .verification-notes textarea {
            width: 250px;
            height: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        .success-message {
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            margin-top: 10px;
        }

        /* Application Status Styles */
        .application-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .application-status.pending {
            background: #ffd700;
            color: #000;
        }

        .application-status.accepted {
            background: #4CAF50;
            color: white;
        }

        .application-status.rejected {
            background: #f44336;
            color: white;
        }

        /* Deadline Styles */
        .deadline-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }

        .deadline-actions {
            display: flex;
            gap: 10px;
        }

        .deadline-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .update-deadline {
            padding: 8px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Document Verification Styles */
        .document-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .document-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }

        .document-status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .document-status.verified {
            background: #d4edda;
            color: #155724;
        }

        .document-status.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .document-status i {
            font-size: 0.8em;
        }

        .view-document {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .view-document:hover {
            background: #1976D2;
        }

        .verification-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .verify-document,
        .reject-document {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .verify-document {
            background: #4CAF50;
            color: white;
        }

        .verify-document:hover {
            background: #388E3C;
        }

        .reject-document {
            background: #f44336;
            color: white;
        }

        .reject-document:hover {
            background: #d32f2f;
        }

        .verification-notes {
            margin-top: 10px;
        }

        .verification-notes textarea {
            width: 100%;
            min-height: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
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
                    <li><a href="applications.php"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li><a href="document-verification.php" class="nav-link"><i class="fas fa-file-upload"></i> Document Verification</a></li>
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

            <div class="dashboard-sections">
                <section id="overview" class="dashboard-section active">
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
                </section>

                <section id="documents" class="dashboard-section">
                    <div class="section-header">
                        <h2>Document Verification</h2>
                        <div class="document-filters">
                            <select id="document-status">
                                <option value="all">All Documents</option>
                                <option value="pending">Pending Verification</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <input type="text" placeholder="Search by student name..." id="document-search">
                        </div>
                    </div>

                    <?php
                    $document_stats = $dashboard->getDocumentStats();
                    ?>
                    <div class="document-stats">
                        <div class="stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="stat-info">
                                <h3>Total Documents</h3>
                                <p><?php echo $document_stats['total_documents']; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div class="stat-info">
                                <h3>Pending</h3>
                                <p><?php echo $document_stats['pending_documents']; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="stat-info">
                                <h3>Verified</h3>
                                <p><?php echo $document_stats['verified_documents']; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-times-circle"></i>
                            <div class="stat-info">
                                <h3>Rejected</h3>
                                <p><?php echo $document_stats['rejected_documents']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="document-list">
                        <?php
                        $documents = $dashboard->getPendingDocuments() ?? [];
                        foreach ($documents as $document):
                        ?>
                        <div class="document-card" data-document-id="<?php echo $document['id']; ?>" data-status="<?php echo $document['status']; ?>">
                            <div class="document-info">
                                <div class="student-info">
                                    <img src="https://via.placeholder.com/50" alt="Student Photo">
                                    <div>
                                        <h4><?php echo htmlspecialchars($document['student_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($document['program_name']); ?></p>
                                    </div>
                                </div>
                                <div class="document-details">
                                    <div class="document-type">
                                        <i class="fas fa-file-pdf"></i>
                                        <span><?php echo htmlspecialchars($document['document_type']); ?></span>
                                    </div>
                                    <div class="document-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Submitted: <?php echo date('M d, Y', strtotime($document['submission_date'])); ?></span>
                                    </div>
                                    <div class="document-status <?php echo $document['status']; ?>">
                                        <i class="fas fa-circle"></i>
                                        <span><?php echo ucfirst($document['status']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="document-actions">
                                <a href="<?php echo htmlspecialchars($document['file_path']); ?>" class="view-document" target="_blank">
                                    <i class="fas fa-eye"></i> View Document
                                </a>
                                <?php if ($document['status'] === 'pending'): ?>
                                <div class="verification-actions">
                                    <button class="verify-document" data-status="verified">
                                        <i class="fas fa-check"></i> Verify
                                    </button>
                                    <button class="reject-document" data-status="rejected">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                                <div class="verification-notes">
                                    <textarea placeholder="Add verification notes..."></textarea>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section id="deadlines" class="dashboard-section">
                    <h2>Program Deadlines</h2>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabs = document.querySelectorAll('.sidebar-nav a:not(.nav-link)');
            const sections = document.querySelectorAll('.dashboard-section');

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent default anchor behavior
                    
                    // Remove active class from all tabs and sections
                    tabs.forEach(t => t.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));

                    // Add active class to clicked tab
                    tab.classList.add('active');

                    // Show corresponding section
                    const sectionId = tab.getAttribute('href').substring(1); // Remove the # from href
                    const targetSection = document.getElementById(sectionId);
                    
                    // Only proceed if the section exists
                    if (targetSection) {
                        targetSection.classList.add('active');
                    } else {
                        console.warn(`Section with ID "${sectionId}" not found`);
                    }
                });
            });

            // Document verification functionality
            const documentStatus = document.getElementById('document-status');
            const documentSearch = document.getElementById('document-search');
            const documentList = document.querySelector('.document-list');

            if (documentStatus && documentSearch && documentList) {
                // Filter documents based on status and search
                function filterDocuments() {
                    const status = documentStatus.value;
                    const search = documentSearch.value.toLowerCase();
                    const documents = documentList.querySelectorAll('.document-card');

                    documents.forEach(doc => {
                        const studentName = doc.querySelector('.student-info h4').textContent.toLowerCase();
                        const docStatus = doc.getAttribute('data-status');
                        
                        const statusMatch = status === 'all' || docStatus === status;
                        const searchMatch = studentName.includes(search);

                        doc.style.display = statusMatch && searchMatch ? 'flex' : 'none';
                    });
                }

                documentStatus.addEventListener('change', filterDocuments);
                documentSearch.addEventListener('input', filterDocuments);

                // Document verification actions
                documentList.addEventListener('click', async (e) => {
                    const verifyBtn = e.target.closest('.verify-document');
                    const rejectBtn = e.target.closest('.reject-document');
                    
                    if (verifyBtn || rejectBtn) {
                        const documentCard = e.target.closest('.document-card');
                        const documentId = documentCard.getAttribute('data-document-id');
                        const status = verifyBtn ? 'verified' : 'rejected';
                        const notes = documentCard.querySelector('.verification-notes textarea')?.value || '';

                        try {
                            const response = await fetch('update_document_status.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    document_id: documentId,
                                    status: status,
                                    notes: notes
                                })
                            });

                            if (response.ok) {
                                documentCard.setAttribute('data-status', status);
                                
                                // Update status display
                                const statusElement = documentCard.querySelector('.document-status');
                                if (statusElement) {
                                    statusElement.className = `document-status ${status}`;
                                    const statusSpan = statusElement.querySelector('span');
                                    if (statusSpan) {
                                        statusSpan.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                                    }
                                }
                                
                                // Hide verification actions and notes
                                const verificationActions = documentCard.querySelector('.verification-actions');
                                const verificationNotes = documentCard.querySelector('.verification-notes');
                                if (verificationActions) verificationActions.style.display = 'none';
                                if (verificationNotes) verificationNotes.style.display = 'none';
                                
                                // Show success message
                                const message = document.createElement('div');
                                message.className = 'success-message';
                                message.textContent = `Document ${status} successfully`;
                                documentCard.appendChild(message);
                                
                                setTimeout(() => message.remove(), 3000);

                                // Update stats
                                location.reload();
                            }
                        } catch (error) {
                            console.error('Error updating document status:', error);
                        }
                    }
                });
            }

            // Application status updates
            const applicationList = document.querySelector('.application-list');
            applicationList.addEventListener('click', async (e) => {
                const statusBtn = e.target.closest('.status-update');
                if (statusBtn) {
                    const applicationCard = e.target.closest('.application-card');
                    const applicationId = applicationCard.getAttribute('data-application-id');
                    const newStatus = statusBtn.getAttribute('data-status');

                    try {
                        const response = await fetch('update_application_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                application_id: applicationId,
                                status: newStatus
                            })
                        });

                        if (response.ok) {
                            const statusElement = applicationCard.querySelector('.application-status');
                            statusElement.className = `application-status ${newStatus.toLowerCase()}`