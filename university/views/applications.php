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