<?php
require_once __DIR__ . '/../../includes/templates/header.php';
require_once __DIR__ . '/../../includes/functions/auth.php';

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'Manage Deadlines';

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
    if (isset($_POST['update_deadline'])) {
        try {
            $stmt = $db->prepare("UPDATE university_programs SET admission_deadline = ? WHERE id = ? AND university_id = ?");
            $stmt->execute([$_POST['deadline'], $_POST['program_id'], $universityId]);
            $success[] = "Deadline updated successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error updating deadline: " . $e->getMessage();
        }
    } elseif (isset($_POST['add_program'])) {
        try {
            $stmt = $db->prepare("INSERT INTO university_programs (university_id, program_name, tuition_fee, ranking, admission_deadline) VALUES (?, ?, ?, ?, ?)");
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
            <li><a href="<?php echo $baseUrl; ?>/university/deadlines" class="active"><i class="fas fa-calendar-alt"></i> Manage Deadlines</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/profile"><i class="fas fa-university"></i> University Profile</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/programs"><i class="fas fa-graduation-cap"></i> Programs</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Manage Program Deadlines</h1>
        
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

        <div class="deadlines-section">
            <h2>Update Existing Program Deadlines</h2>
            <?php if (empty($programs)): ?>
                <p>No programs found. Add a program below.</p>
            <?php else: ?>
                <div class="programs-list">
                    <?php foreach ($programs as $program): ?>
                        <div class="program-card">
                            <form method="POST" class="deadline-form">
                                <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                                <div class="form-group">
                                    <label><?php echo htmlspecialchars($program['program_name']); ?></label>
                                    <input type="date" name="deadline" value="<?php echo $program['admission_deadline']; ?>" required>
                                </div>
                                <button type="submit" name="update_deadline" class="btn-update">Update Deadline</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="add-program-section">
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
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/templates/footer.php'; ?> 