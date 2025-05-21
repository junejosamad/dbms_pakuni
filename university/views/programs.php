<?php
require_once __DIR__ . '/../../includes/templates/header.php';
require_once __DIR__ . '/../../includes/functions/auth.php';

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'Manage Programs';

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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_program'])) {
        try {
            $stmt = $db->prepare("
                INSERT INTO university_programs 
                (university_id, program_name, tuition_fee, ranking, admission_deadline) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $universityId,
                $_POST['program_name'],
                $_POST['tuition_fee'],
                $_POST['ranking'],
                $_POST['admission_deadline']
            ]);
            $success[] = "Program added successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error adding program: " . $e->getMessage();
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
        $stmt = $db->prepare("SELECT * FROM university_programs WHERE university_id = ? ORDER BY program_name");
        $stmt->execute([$universityId]);
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
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
            <form method="POST" class="add-program-form">
                <div class="form-group">
                    <label for="program_name">Program Name</label>
                    <input type="text" id="program_name" name="program_name" required>
                </div>
                <div class="form-group">
                    <label for="tuition_fee">Tuition Fee</label>
                    <input type="number" id="tuition_fee" name="tuition_fee" required>
                </div>
                <div class="form-group">
                    <label for="ranking">Ranking</label>
                    <input type="number" id="ranking" name="ranking" required>
                </div>
                <div class="form-group">
                    <label for="admission_deadline">Admission Deadline</label>
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
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/templates/footer.php'; ?> 