<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Debug logging
error_log("Programs page accessed");
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Ensure only university users can access this page
require_auth();
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

// Initialize dashboard
$dashboard = new UniversityDashboard($userId);
$universityId = $dashboard->getUniversityId();
error_log("University ID from database: " . $universityId);

// If no university profile, show message and do not allow adding programs
if (!$universityId) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Programs - PakUni</title><link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="css/dashboard.css"><link rel="stylesheet" href="css/university-dashboard.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head><body><nav class="navbar"><div class="logo"><h1><a href="index.php">PakUni</a></h1></div><ul class="nav-links"><li><a href="index.php">Home</a></li><li><a href="universities.php">Universities</a></li><li><a href="university-dashboard.php">Dashboard</a></li><li><a href="logout.php" class="btn-login">Logout</a></li></ul></nav><div class="dashboard-container"><aside class="sidebar"><div class="user-profile"><img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic"><h3>' . htmlspecialchars($_SESSION['user_name'] ?? 'University Representative') . '</h3><p>University Representative</p></div><nav class="sidebar-nav"><ul><li><a href="university-dashboard.php"><i class="fas fa-home"></i> Overview</a></li><li><a href="applications.php"><i class="fas fa-file-alt"></i> Applications</a></li><li><a href="document-verification.php"><i class="fas fa-file-upload"></i> Document Verification</a></li><li><a href="deadlines.php"><i class="fas fa-calendar"></i> Manage Deadlines</a></li><li><a href="programs.php" class="active"><i class="fas fa-graduation-cap"></i> Programs</a></li><li><a href="university-profile.php"><i class="fas fa-university"></i> University Profile</a></li><li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li></ul></nav></aside><main class="main-content"><h1>Manage Programs</h1><div class="error-messages"><div class="error">You must complete your university profile before you can add programs.</div></div></main></div></body></html>';
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");
    
    if (isset($_POST['add_program'])) {
        try {
            // Debug log
            error_log("Form submitted with data: " . print_r($_POST, true));
            
            // Prepare program data
            $program_data = [
                'name' => trim($_POST['name']),
                'tuition_fee' => floatval($_POST['tuition_fee'] ?? 0),
                'ranking' => intval($_POST['ranking'] ?? 0),
                'admission_deadline' => $_POST['admission_deadline']
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
$programs = $dashboard->getUniversityPrograms() ?? [];
error_log("Fetched " . count($programs) . " programs");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1><a href="index.php">PakUni</a></h1>
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
                <h3><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'University Representative'); ?></h3>
                <p>University Representative</p>
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

        <main class="main-content">
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
                        <label for="tuition_fee">Tuition Fee</label>
                        <input type="number" id="tuition_fee" name="tuition_fee" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="ranking">Ranking</label>
                        <input type="number" id="ranking" name="ranking" min="0">
                    </div>
                    <div class="form-group">
                        <label for="admission_deadline">Admission Deadline *</label>
                        <input type="date" id="admission_deadline" name="admission_deadline" required>
                    </div>
                    <button type="submit" name="add_program" class="btn-add">Add Program</button>
                </form>
            </div>

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
                                        <input type="text" name="program_name" value="<?php echo htmlspecialchars($program['program_name']); ?>" required>
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
        </main>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html> 