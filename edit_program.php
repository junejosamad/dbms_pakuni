<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get program ID from URL
$program_id = $_GET['id'] ?? null;
if (!$program_id) {
    header('Location: programs.php');
    exit();
}

// Get program details
$program = $dashboard->getProgramDetails($program_id);
if (!$program) {
    header('Location: programs.php');
    exit();
}

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'University Representative';
$university_info = $dashboard->getUniversityInfo();
if (!$university_info) {
    $university_info = [
        'name' => 'Your University',
        'representative_name' => $user_name
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    // Validate required fields
    $required_fields = ['program_name', 'degree_level', 'duration', 'available_seats', 'fee_per_semester', 'admission_deadline'];
    $missing_fields = array_filter($required_fields, function($field) {
        return empty($_POST[$field]);
    });

    if (!empty($missing_fields)) {
        $response['message'] = 'Please fill in all required fields';
    } else {
        // Prepare program data
        $program_data = [
            'program_name' => $_POST['program_name'],
            'degree_level' => $_POST['degree_level'],
            'duration' => (int)$_POST['duration'],
            'available_seats' => (int)$_POST['available_seats'],
            'fee_per_semester' => (float)$_POST['fee_per_semester'],
            'admission_deadline' => $_POST['admission_deadline'],
            'description' => $_POST['description'] ?? '',
            'requirements' => $_POST['requirements'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Update program
        if ($dashboard->updateProgram($program_id, $program_data)) {
            $response['success'] = true;
            $response['message'] = 'Program updated successfully';
            // Refresh program data
            $program = $dashboard->getProgramDetails($program_id);
        } else {
            $response['message'] = 'Failed to update program';
        }
    }

    // If AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .edit-program-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .back-btn {
            padding: 8px 15px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background: #5a6268;
        }

        .program-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .submit-btn {
            padding: 12px 24px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background: #1976D2;
        }

        .error-message {
            color: #dc3545;
            margin-top: 5px;
            font-size: 0.9em;
        }

        .success-message {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
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
                    <li><a href="deadlines.php"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="programs.php" class="active"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="edit-program-container">
            <div class="page-header">
                <h2>Edit Program</h2>
                <a href="programs.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Programs
                </a>
            </div>

            <?php if (isset($response) && $response['success']): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $response['message']; ?>
                </div>
            <?php endif; ?>

            <form class="program-form" method="POST" id="editProgramForm">
                <div class="form-group">
                    <label for="program_name">Program Name *</label>
                    <input type="text" id="program_name" name="program_name" value="<?php echo htmlspecialchars($program['program_name']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="degree_level">Degree Level *</label>
                        <select id="degree_level" name="degree_level" required>
                            <option value="">Select Degree Level</option>
                            <option value="undergraduate" <?php echo $program['degree_level'] === 'undergraduate' ? 'selected' : ''; ?>>Undergraduate</option>
                            <option value="graduate" <?php echo $program['degree_level'] === 'graduate' ? 'selected' : ''; ?>>Graduate</option>
                            <option value="phd" <?php echo $program['degree_level'] === 'phd' ? 'selected' : ''; ?>>PhD</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (years) *</label>
                        <input type="number" id="duration" name="duration" min="1" max="8" value="<?php echo htmlspecialchars($program['duration']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="available_seats">Available Seats *</label>
                        <input type="number" id="available_seats" name="available_seats" min="1" value="<?php echo htmlspecialchars($program['available_seats']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="fee_per_semester">Fee per Semester (Rs.) *</label>
                        <input type="number" id="fee_per_semester" name="fee_per_semester" min="0" step="0.01" value="<?php echo htmlspecialchars($program['fee_per_semester']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admission_deadline">Admission Deadline *</label>
                    <input type="date" id="admission_deadline" name="admission_deadline" value="<?php echo htmlspecialchars($program['admission_deadline']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Program Description</label>
                    <textarea id="description" name="description" placeholder="Enter program description..."><?php echo htmlspecialchars($program['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="requirements">Admission Requirements</label>
                    <textarea id="requirements" name="requirements" placeholder="Enter admission requirements..."><?php echo htmlspecialchars($program['requirements']); ?></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo $program['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Active Program</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editProgramForm');
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                try {
                    const formData = new FormData(form);
                    const response = await fetch('edit_program.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Show success message
                        const message = document.createElement('div');
                        message.className = 'success-message';
                        message.innerHTML = `<i class="fas fa-check-circle"></i> ${result.message}`;
                        form.insertBefore(message, form.firstChild);
                        
                        // Remove success message after 3 seconds
                        setTimeout(() => message.remove(), 3000);
                    } else {
                        // Show error message
                        const message = document.createElement('div');
                        message.className = 'error-message';
                        message.textContent = result.message;
                        form.insertBefore(message, form.firstChild);
                        
                        // Remove error message after 3 seconds
                        setTimeout(() => message.remove(), 3000);
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                }
            });
        });
    </script>
</body>
</html> 