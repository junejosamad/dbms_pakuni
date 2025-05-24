<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get document stats - use default values if no university profile exists
$document_stats = [
    'total_documents' => 187,
    'pending_documents' => 45,
    'verified_documents' => 128,
    'rejected_documents' => 14
];

// Get pending documents - use empty array if no university profile exists
$documents = [];

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'University Representative';
$university_info = [
    'name' => 'Your University',
    'representative_name' => $user_name
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .document-verification-container {
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

        .document-stats {
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

        .document-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .document-filters select,
        .document-filters input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }

        .document-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .document-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .document-info {
            display: flex;
            align-items: center;
            gap: 20px;
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

        .document-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
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
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }

        .no-documents {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .no-documents i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .no-documents p {
            color: #666;
            font-size: 1.1em;
            margin: 0;
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
                    <li><a href="applications.php"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li><a href="document-verification.php" class="active"><i class="fas fa-file-upload"></i> Document Verification</a></li>
                    <li><a href="deadlines.php"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="programs.php"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="document-verification-container">
            <div class="page-header">
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

            <div class="document-stats">
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="stat-info">
                        <h3>Total Documents</h3>
                        <p>187</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <p>45</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-info">
                        <h3>Verified</h3>
                        <p>128</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-times-circle"></i>
                    <div class="stat-info">
                        <h3>Rejected</h3>
                        <p>14</p>
                    </div>
                </div>
            </div>

            <div class="document-list">
                <?php 
                if (empty($documents)): 
                ?>
                    <div class="no-documents">
                        <i class="fas fa-file-alt"></i>
                        <p>No documents pending verification</p>
                    </div>
                <?php else: 
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
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const documentStatus = document.getElementById('document-status');
            const documentSearch = document.getElementById('document-search');
            const documentList = document.querySelector('.document-list');

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
                    const notes = documentCard.querySelector('.verification-notes textarea').value;

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
                            documentCard.querySelector('.verification-actions').style.display = 'none';
                            documentCard.querySelector('.verification-notes').style.display = 'none';
                            
                            // Update status display
                            const statusElement = documentCard.querySelector('.document-status');
                            statusElement.className = `document-status ${status}`;
                            statusElement.querySelector('span').textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            
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
        });
    </script>
</body>
</html> 