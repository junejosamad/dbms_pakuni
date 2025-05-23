<?php
require_once __DIR__ . '/../../includes/templates/header.php';
require_once __DIR__ . '/../../includes/functions/auth.php';

// Debug logging
error_log("Programs page accessed");
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'Manage Programs';

// Get university ID
$userId = $_SESSION['user_id'] ?? null;
error_log("User ID from session: " . $userId);

if (!$userId) {
    error_log("No user ID found in session");
    header("Location: login.php");
    exit();
}

$db = new Database();
$universityId = null;

try {
    $stmt = $db->prepare("SELECT id FROM universities WHERE user_id = ?");
    $stmt->execute([$userId]);
    $universityId = $stmt->fetchColumn();
    error_log("University ID from database: " . $universityId);
} catch (PDOException $e) {
    error_log("Error fetching university ID: " . $e->getMessage());
    $errors[] = "Error fetching university ID: " . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");
    
    if (isset($_POST['add_program'])) {
        error_log("Add program form submitted");
        try {
            // Validate required fields
            $required_fields = ['name', 'degree_type', 'duration', 'admission_deadline'];
            $missing_fields = [];
            
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $missing_fields[] = $field;
                }
            }
            
            if (!empty($missing_fields)) {
                error_log("Missing required fields: " . implode(', ', $missing_fields));
                $errors[] = "Please fill in all required fields: " . implode(', ', $missing_fields);
            } else {
                // Initialize dashboard
                $dashboard = new UniversityDashboard($_SESSION['user_id']);
                
                // Prepare program data
                $program_data = [
                    'name' => trim($_POST['name']),
                    'degree_type' => trim($_POST['degree_type']),
                    'duration' => (int)$_POST['duration'],
                    'admission_deadline' => $_POST['admission_deadline'],
                    'description' => trim($_POST['description'] ?? ''),
                    'requirements' => trim($_POST['requirements'] ?? ''),
                    'status' => 'active'
                ];
                
                error_log("Prepared program data: " . print_r($program_data, true));
                
                // Add program
                if ($dashboard->addProgram($program_data)) {
                    $success[] = "Program added successfully!";
                    error_log("Program added successfully");
                    // Redirect to prevent form resubmission
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $errors[] = "Failed to add program. Please check the error logs for details.";
                    error_log("Failed to add program");
                }
            }
        } catch (Exception $e) {
            error_log("Error adding program: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $errors[] = "An error occurred while adding the program.";
        }
    } elseif (isset($_POST['update_program'])) {
        try {
            $stmt = $db->prepare("
                UPDATE university_programs 
                SET program_name = ?, tuition_fee = ?, ranking = ?, admission_deadline = ?
                WHERE id = ? AND university_id = ?
            ");
            $stmt->execute([
                $_POST['program_name'],
                $_POST['tuition_fee'],
                $_POST['ranking'],
                $_POST['admission_deadline'],
                $_POST['program_id'],
                $universityId
            ]);
            $success[] = "Program updated successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error updating program: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_program'])) {
        try {
            $stmt = $db->prepare("DELETE FROM university_programs WHERE id = ? AND university_id = ?");
            $stmt->execute([$_POST['program_id'], $universityId]);
            $success[] = "Program deleted successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error deleting program: " . $e->getMessage();
        }
    }
}

// Fetch programs
$programs = [];
if ($universityId) {
    try {
        $stmt = $db->prepare("SELECT * FROM programs WHERE university_id = ? ORDER BY name");
        $stmt->execute([$universityId]);
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Fetched " . count($programs) . " programs");
    } catch (PDOException $e) {
        error_log("Error fetching programs: " . $e->getMessage());
        $errors[] = "Error fetching programs: " . $e->getMessage();
    }
}
?>

<div class="dashboard-container">
    <div class="sidebar">
        <ul>
            <li><a href="<?php echo $baseUrl; ?>/university/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/applications"><i class="fas fa-file-alt"></i> Applications</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/deadlines"><i class="fas fa-calendar-alt"></i> Manage Deadlines</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/profile"><i class="fas fa-university"></i> University Profile</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/programs" class="active"><i class="fas fa-graduation-cap"></i> Programs</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Manage Programs</h1>
        
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

        <div class="programs-section">
            <h2>Add New Program</h2>
            <form method="POST" class="add-program-form" id="addProgramForm">
                <div class="form-group">
                    <label for="name">Program Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="degree_type">Degree Type *</label>
                    <select id="degree_type" name="degree_type" required>
                        <option value="">Select Degree Type</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Graduate">Graduate</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (years) *</label>
                    <input type="number" id="duration" name="duration" min="1" max="8" required>
                </div>
                <div class="form-group">
                    <label for="admission_deadline">Admission Deadline *</label>
                    <input type="date" id="admission_deadline" name="admission_deadline" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="requirements">Requirements</label>
                    <textarea id="requirements" name="requirements" rows="4"></textarea>
                </div>
                <button type="submit" name="add_program" class="btn-add">Add Program</button>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addProgramForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(form);
                
                // Show loading state
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = 'Adding Program...';
                
                // Submit form
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Check if the response contains an error message
                    if (html.includes('error-message')) {
                        throw new Error('Server returned an error');
                    }
                    // Reload the page to show the new program
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.textContent = 'An error occurred while submitting the form. Please try again.';
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
            });
        });
        </script>

        <div class="programs-list-section">
            <h2>Existing Programs</h2>
            <?php if (empty($programs)): ?>
                <p>No programs found. Add a program above.</p>
            <?php else: ?>
                <div class="programs-grid">
                    <?php foreach ($programs as $program): ?>
                        <div class="program-card">
                            <form method="POST" class="program-form">
                                <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                                <div class="form-group">
                                    <label>Program Name</label>
                                    <input type="text" name="program_name" value="<?php echo htmlspecialchars($program['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tuition Fee</label>
                                    <input type="number" name="tuition_fee" value="<?php echo htmlspecialchars($program['tuition_fee']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Ranking</label>
                                    <input type="number" name="ranking" value="<?php echo htmlspecialchars($program['ranking']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Admission Deadline</label>
                                    <input type="date" name="admission_deadline" value="<?php echo $program['admission_deadline']; ?>" required>
                                </div>
                                <div class="program-actions">
                                    <button type="submit" name="update_program" class="btn-update">Update</button>
                                    <button type="submit" name="delete_program" class="btn-delete" onclick="return confirm('Are you sure you want to delete this program?')">Delete</button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/templates/footer.php'; ?> 