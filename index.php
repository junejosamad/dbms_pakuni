<?php
$pageTitle = 'Home - PakUni';
$currentPage = 'home';

// Initialize database connection
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

require_once 'includes/templates/header.php';
?>

<main class="main-content">
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to PakUni</h1>
            <p>Your gateway to higher education in Pakistan</p>
            <?php if (!$isLoggedIn): ?>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="universities.php" class="btn btn-secondary">Explore Universities</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <h2>Why Choose PakUni?</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <i class="fas fa-university"></i>
                <h3>Top Universities</h3>
                <p>Access to Pakistan's leading universities and their programs</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Easy Application</h3>
                <p>Streamlined application process for multiple universities</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Track Progress</h3>
                <p>Monitor your application status in real-time</p>
            </div>
        </div>
    </section>

    <section class="universities-preview">
        <h2>Featured Universities</h2>
        <div class="university-grid">
            <?php
            try {
                // Get featured universities
                $query = "SELECT * FROM university_profiles WHERE is_featured = 1 LIMIT 3";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($universities) > 0) {
                    foreach ($universities as $university):
                    ?>
                    <div class="university-card">
                        <img src="<?php echo htmlspecialchars($university['logo_url'] ?? 'images/default-university.png'); ?>" alt="<?php echo htmlspecialchars($university['name']); ?>">
                        <h3><?php echo htmlspecialchars($university['name']); ?></h3>
                        <p><?php echo htmlspecialchars($university['description']); ?></p>
                        <a href="university-details.php?id=<?php echo $university['id']; ?>" class="btn btn-outline">Learn More</a>
                    </div>
                    <?php endforeach;
                } else {
                    echo '<p class="no-universities">No featured universities available at the moment.</p>';
                }
            } catch (PDOException $e) {
                // Log the error but don't display it to users
                error_log("Database error: " . $e->getMessage());
                echo '<p class="error-message">Unable to load featured universities. Please try again later.</p>';
            }
            ?>
        </div>
    </section>
</main>

<?php require_once 'includes/templates/footer.php'; ?> 