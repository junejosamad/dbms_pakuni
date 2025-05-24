<?php
$pageTitle = 'Universities - PakUni';
$currentPage = 'universities';
require_once 'includes/templates/header.php';
?>

<main class="main-content">
    <section class="universities-header">
        <h1>Universities in Pakistan</h1>
        <p>Explore top universities and their programs</p>
        
        <div class="search-filters">
            <div class="search-box">
                <input type="text" id="university-search" placeholder="Search universities...">
                <i class="fas fa-search"></i>
            </div>
            
            <div class="filters">
                <select id="location-filter">
                    <option value="">All Locations</option>
                    <option value="punjab">Punjab</option>
                    <option value="sindh">Sindh</option>
                    <option value="kpk">KPK</option>
                    <option value="balochistan">Balochistan</option>
                </select>
                
                <select id="type-filter">
                    <option value="">All Types</option>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
            </div>
        </div>
    </section>

    <section class="universities-grid">
        <?php
        try {
            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT * FROM university_profiles ORDER BY name ASC";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($universities) > 0) {
                foreach ($universities as $university):
                ?>
                <div class="university-card">
                    <div class="university-image">
                        <img src="<?php echo htmlspecialchars($university['logo_url'] ?? 'images/default-university.png'); ?>" 
                             alt="<?php echo htmlspecialchars($university['name']); ?>">
                    </div>
                    <div class="university-info">
                        <h3><?php echo htmlspecialchars($university['name']); ?></h3>
                        <p class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($university['location']); ?>
                        </p>
                        <p class="type">
                            <i class="fas fa-university"></i>
                            <?php echo htmlspecialchars($university['type']); ?> University
                        </p>
                        <p class="description">
                            <?php echo htmlspecialchars(substr($university['description'], 0, 150)) . '...'; ?>
                        </p>
                        <div class="university-actions">
                            <a href="university-details.php?id=<?php echo $university['id']; ?>" class="btn btn-primary">View Details</a>
                            <?php if ($isLoggedIn && $userRole === 'student'): ?>
                                <a href="apply.php?university=<?php echo $university['id']; ?>" class="btn btn-secondary">Apply Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach;
            } else {
                echo '<p class="no-universities">No universities found.</p>';
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo '<p class="error-message">Unable to load universities. Please try again later.</p>';
        }
        ?>
    </section>
</main>

<?php require_once 'includes/templates/footer.php'; ?> 