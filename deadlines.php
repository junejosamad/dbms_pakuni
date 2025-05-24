<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get program deadlines
$program_deadlines = $dashboard->getProgramDeadlines() ?? [
    [
        'id' => 1,
        'program_name' => 'Bachelor of Computer Science',
        'degree_level' => 'Undergraduate',
        'admission_deadline' => '2024-08-15'
    ],
    [
        'id' => 2,
        'program_name' => 'Master of Business Administration',
        'degree_level' => 'Graduate',
        'admission_deadline' => '2024-07-30'
    ],
    [
        'id' => 3,
        'program_name' => 'PhD in Electrical Engineering',
        'degree_level' => 'PhD',
        'admission_deadline' => '2024-09-01'
    ],
    [
        'id' => 4,
        'program_name' => 'Bachelor of Architecture',
        'degree_level' => 'Undergraduate',
        'admission_deadline' => '2024-08-20'
    ],
    [
        'id' => 5,
        'program_name' => 'Master of Data Science',
        'degree_level' => 'Graduate',
        'admission_deadline' => '2024-07-15'
    ]
];

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
    <title>Manage Deadlines - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .deadlines-container {
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

        .deadline-filters {
            display: flex;
            gap: 15px;
        }

        .deadline-filters select,
        .deadline-filters input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }

        .deadline-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .deadline-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .program-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .program-info img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .program-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .program-details h4 {
            margin: 0;
            color: #333;
        }

        .program-details p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }

        .deadline-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .deadline-date {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .deadline-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }

        .deadline-status.active {
            background: #d4edda;
            color: #155724;
        }

        .deadline-status.passed {
            background: #f8d7da;
            color: #721c24;
        }

        .deadline-actions {
            display: flex;
            gap: 10px;
        }

        .update-deadline {
            padding: 8px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .update-deadline:hover {
            background: #1976D2;
        }

        .deadline-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9em;
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

        .no-deadlines {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .no-deadlines i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .no-deadlines p {
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
                    <li><a href="document-verification.php"><i class="fas fa-file-upload"></i> Document Verification</a></li>
                    <li><a href="deadlines.php" class="active"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="programs.php"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="deadlines-container">
            <div class="page-header">
                <h2>Program Deadlines</h2>
                <div class="deadline-filters">
                    <select id="deadline-status">
                        <option value="all">All Deadlines</option>
                        <option value="active">Active</option>
                        <option value="passed">Passed</option>
                    </select>
                    <input type="text" placeholder="Search by program name..." id="deadline-search">
                </div>
            </div>

            <div class="deadline-list">
                <?php if (empty($program_deadlines)): ?>
                    <div class="no-deadlines">
                        <i class="fas fa-calendar-times"></i>
                        <p>No program deadlines found</p>
                    </div>
                <?php else: 
                    foreach ($program_deadlines as $program): 
                        $is_passed = strtotime($program['admission_deadline']) < time();
                ?>
                    <div class="deadline-card" data-program-id="<?php echo $program['id']; ?>" data-status="<?php echo $is_passed ? 'passed' : 'active'; ?>">
                        <div class="program-info">
                            <img src="https://via.placeholder.com/50" alt="Program Image">
                            <div class="program-details">
                                <h4><?php echo htmlspecialchars($program['program_name']); ?></h4>
                                <p><?php echo htmlspecialchars($program['degree_level']); ?></p>
                            </div>
                        </div>
                        <div class="deadline-info">
                            <div class="deadline-date">
                                <i class="fas fa-calendar"></i>
                                <span>Deadline: <?php echo date('F j, Y', strtotime($program['admission_deadline'])); ?></span>
                            </div>
                            <div class="deadline-status <?php echo $is_passed ? 'passed' : 'active'; ?>">
                                <i class="fas fa-circle"></i>
                                <span><?php echo $is_passed ? 'Passed' : 'Active'; ?></span>
                            </div>
                        </div>
                        <div class="deadline-actions">
                            <input type="date" class="deadline-input" value="<?php echo $program['admission_deadline']; ?>">
                            <button class="update-deadline">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </div>
                <?php endforeach;
                endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deadlineStatus = document.getElementById('deadline-status');
            const deadlineSearch = document.getElementById('deadline-search');
            const deadlineList = document.querySelector('.deadline-list');

            // Filter deadlines based on status and search
            function filterDeadlines() {
                const status = deadlineStatus.value;
                const search = deadlineSearch.value.toLowerCase();
                const deadlines = deadlineList.querySelectorAll('.deadline-card');

                deadlines.forEach(deadline => {
                    const programName = deadline.querySelector('.program-details h4').textContent.toLowerCase();
                    const deadlineStatus = deadline.getAttribute('data-status');
                    
                    const statusMatch = status === 'all' || deadlineStatus === status;
                    const searchMatch = programName.includes(search);

                    deadline.style.display = statusMatch && searchMatch ? 'flex' : 'none';
                });
            }

            deadlineStatus.addEventListener('change', filterDeadlines);
            deadlineSearch.addEventListener('input', filterDeadlines);

            // Deadline updates
            deadlineList.addEventListener('click', async (e) => {
                const updateBtn = e.target.closest('.update-deadline');
                if (updateBtn) {
                    const deadlineCard = e.target.closest('.deadline-card');
                    const programId = deadlineCard.getAttribute('data-program-id');
                    const newDeadline = deadlineCard.querySelector('.deadline-input').value;

                    try {
                        const response = await fetch('update_program_deadline.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                program_id: programId,
                                deadline: newDeadline
                            })
                        });

                        if (response.ok) {
                            // Update deadline display
                            const dateElement = deadlineCard.querySelector('.deadline-date span');
                            dateElement.textContent = `Deadline: ${new Date(newDeadline).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}`;
                            
                            // Update status
                            const isPassed = new Date(newDeadline) < new Date();
                            deadlineCard.setAttribute('data-status', isPassed ? 'passed' : 'active');
                            
                            const statusElement = deadlineCard.querySelector('.deadline-status');
                            statusElement.className = `deadline-status ${isPassed ? 'passed' : 'active'}`;
                            statusElement.querySelector('span').textContent = isPassed ? 'Passed' : 'Active';
                            
                            // Show success message
                            const message = document.createElement('div');
                            message.className = 'success-message';
                            message.textContent = 'Deadline updated successfully';
                            deadlineCard.appendChild(message);
                            
                            setTimeout(() => message.remove(), 3000);
                        }
                    } catch (error) {
                        console.error('Error updating deadline:', error);
                    }
                }
            });
        });
    </script>
</body>
</html> 