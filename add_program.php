<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions/auth.php';
require_once 'university-dashboard-functions.php';

// Custom error logging function
function custom_log($message) {
    $log_file = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Debug information
custom_log("Add Program - User ID: " . $_SESSION['user_id']);
custom_log("Add Program - User Role: " . $_SESSION['user_role']);

$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Debug university ID
$university_id = $dashboard->getUniversityId();
custom_log("Add Program - University ID: " . ($university_id ?? 'null'));

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

    try {
        // Debug log
        custom_log("Form submitted with POST data: " . print_r($_POST, true));
        custom_log("Session data: " . print_r($_SESSION, true));

        // Validate required fields
        $required_fields = ['name', 'degree_type', 'duration', 'admission_deadline'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missing_fields[] = $field;
                custom_log("Missing field: " . $field . " value: " . (isset($_POST[$field]) ? $_POST[$field] : 'not set'));
            }
        }

        if (!empty($missing_fields)) {
            $response['message'] = 'Please fill in all required fields: ' . implode(', ', $missing_fields);
            custom_log("Validation failed: " . $response['message']);
        } else {
            // Prepare program data
            $program_data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'duration' => $_POST['duration'],
                'degree_type' => $_POST['degree_type'],
                'admission_deadline' => $_POST['admission_deadline'],
                'requirements' => $_POST['requirements'] ?? '',
                'status' => isset($_POST['is_active']) ? 'active' : 'inactive'
            ];

            // Debug log
            custom_log("Prepared program data: " . print_r($program_data, true));

            // Add program
            if ($dashboard->addProgram($program_data)) {
                $response['success'] = true;
                $response['message'] = 'Program added successfully';
                custom_log("Program added successfully");
            } else {
                $response['message'] = 'Failed to add program. Please check the error logs for details.';
                custom_log("Failed to add program");
            }
        }
    } catch (Exception $e) {
        custom_log("Error in form submission: " . $e->getMessage());
        custom_log("Stack trace: " . $e->getTraceAsString());
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }

    // If AJAX request, return JSON response
    if (isset($_POST['ajax'])) {
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
    <title>Add Program - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .add-program-container {
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
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>PakUni</h2>
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

        <main class="add-program-container">
            <div class="page-header">
                <h2>Add New Program</h2>
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

            <form class="program-form" method="POST" id="addProgramForm">
                <div class="form-group">
                    <label for="name">Program Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="degree_type">Degree Level *</label>
                        <select id="degree_type" name="degree_type" required>
                            <option value="">Select Degree Level</option>
                            <option value="Undergraduate">Undergraduate</option>
                            <option value="Graduate">Graduate</option>
                            <option value="PhD">PhD</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration *</label>
                        <input type="text" id="duration" name="duration" placeholder="e.g., 4 years" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admission_deadline">Admission Deadline *</label>
                    <input type="date" id="admission_deadline" name="admission_deadline" required>
                </div>

                <div class="form-group">
                    <label for="description">Program Description</label>
                    <textarea id="description" name="description" placeholder="Enter program description..."></textarea>
                </div>

                <div class="form-group">
                    <label for="requirements">Program Requirements</label>
                    <textarea id="requirements" name="requirements" placeholder="Enter program requirements..."></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" checked>
                        <label for="is_active">Active Program</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Add Program
                </button>
            </form>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addProgramForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Remove any existing messages
                const existingMessages = form.querySelectorAll('.error-message, .success-message');
                existingMessages.forEach(msg => msg.remove());
                
                // Create FormData object
                const formData = new FormData(form);
                
                // Add AJAX header
                formData.append('ajax', '1');
                
                // Log form data for debugging
                console.log('Submitting form data:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // Show loading state
                const submitBtn = form.querySelector('.submit-btn');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Program...';
                submitBtn.disabled = true;
                
                // Send form data using fetch
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Error parsing JSON:', text);
                            throw new Error('Invalid JSON response');
                        }
                    });
                })
                .then(data => {
                    console.log('Response data:', data);
                    
                    // Create message element
                    const message = document.createElement('div');
                    message.className = data.success ? 'success-message' : 'error-message';
                    message.innerHTML = `<i class="fas fa-${data.success ? 'check' : 'exclamation'}-circle"></i> ${data.message}`;
                    
                    // Insert message at the top of the form
                    form.insertBefore(message, form.firstChild);
                    
                    if (data.success) {
                        // Reset form on success
                        form.reset();
                    }
                    
                    // Remove message after 5 seconds
                    setTimeout(() => message.remove(), 5000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    const message = document.createElement('div');
                    message.className = 'error-message';
                    message.innerHTML = `<i class="fas fa-exclamation-circle"></i> An error occurred while submitting the form: ${error.message}`;
                    form.insertBefore(message, form.firstChild);
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>
</html> 