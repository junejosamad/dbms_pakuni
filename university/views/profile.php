<?php
require_once __DIR__ . '/../../includes/templates/header.php';
require_once __DIR__ . '/../../includes/functions/auth.php';

// Ensure only university users can access this page
require_role('university');

$pageTitle = 'University Profile';

// Get university ID and profile
$userId = $_SESSION['user_id'];
$db = new Database();
$profile = null;

try {
    $stmt = $db->prepare("
        SELECT up.*, u.email as contact_email
        FROM university_profiles up
        JOIN users u ON up.user_id = u.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error fetching profile: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $db->prepare("
            UPDATE university_profiles 
            SET name = ?, location = ?, contact_email = ?, website_url = ?, 
                description = ?, founded_year = ?, total_students = ?, accreditation = ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            $_POST['name'],
            $_POST['location'],
            $_POST['contact_email'],
            $_POST['website_url'],
            $_POST['description'],
            $_POST['founded_year'],
            $_POST['total_students'],
            $_POST['accreditation'],
            $userId
        ]);
        $success[] = "Profile updated successfully!";
        
        // Refresh profile data
        $stmt = $db->prepare("
            SELECT up.*, u.email as contact_email
            FROM university_profiles up
            JOIN users u ON up.user_id = u.id
            WHERE up.user_id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors[] = "Error updating profile: " . $e->getMessage();
    }
}
?>

<div class="dashboard-container">
    <div class="sidebar">
        <ul>
            <li><a href="<?php echo $baseUrl; ?>/university/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/applications"><i class="fas fa-file-alt"></i> Applications</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/deadlines"><i class="fas fa-calendar-alt"></i> Manage Deadlines</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/profile" class="active"><i class="fas fa-university"></i> University Profile</a></li>
            <li><a href="<?php echo $baseUrl; ?>/university/programs"><i class="fas fa-graduation-cap"></i> Programs</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>University Profile</h1>
        
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

        <div class="profile-section">
            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="name">University Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($profile['contact_email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="website_url">Website URL</label>
                    <input type="url" id="website_url" name="website_url" value="<?php echo htmlspecialchars($profile['website_url'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="founded_year">Founded Year</label>
                    <input type="number" id="founded_year" name="founded_year" value="<?php echo htmlspecialchars($profile['founded_year'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="total_students">Total Students</label>
                    <input type="number" id="total_students" name="total_students" value="<?php echo htmlspecialchars($profile['total_students'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="accreditation">Accreditation</label>
                    <input type="text" id="accreditation" name="accreditation" value="<?php echo htmlspecialchars($profile['accreditation'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn-update">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/templates/footer.php'; ?> 