<?php
require_once 'config/session.php';
require_once 'includes/functions/auth.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
if (!is_authenticated() || get_user_role() !== 'university') {
    header("Location: login.php");
    exit();
}

$dashboard = new UniversityDashboard($_SESSION['user_id']);
$universities = $dashboard->getAllUniversities();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Profiles - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .university-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .university-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.2s;
        }

        .university-card:hover {
            transform: translateY(-5px);
        }

        .university-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .university-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .university-info h3 {
            margin: 0;
            color: #333;
        }

        .university-info p {
            margin: 5px 0;
            color: #666;
        }

        .university-details {
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #555;
        }

        .detail-item i {
            width: 20px;
            margin-right: 10px;
            color: #007bff;
        }

        .university-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background-color: #ffebee;
            color: #c62828;
        }

        .search-bar {
            padding: 20px;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .filters {
            display: flex;
            gap: 15px;
            padding: 20px;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-container">
        <?php include 'includes/university-sidebar.php'; ?>

        <div class="main-content">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search universities..." onkeyup="filterUniversities()">
            </div>

            <div class="filters">
                <div class="filter-group">
                    <select id="statusFilter" onchange="filterUniversities()">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select id="sortBy" onchange="filterUniversities()">
                        <option value="name">Sort by Name</option>
                        <option value="programs">Sort by Programs</option>
                        <option value="students">Sort by Students</option>
                    </select>
                </div>
            </div>

            <div class="university-grid" id="universityGrid">
                <?php if (empty($universities)): ?>
                    <div class="empty-state">
                        <i class="fas fa-university"></i>
                        <h3>No Universities Found</h3>
                        <p>There are no universities registered in the system yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($universities as $university): ?>
                        <div class="university-card">
                            <div class="university-header">
                                <img src="<?php echo htmlspecialchars($university['logo_url'] ?? 'images/default-university.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($university['name']); ?>" 
                                     class="university-logo">
                                <div class="university-info">
                                    <h3><?php echo htmlspecialchars($university['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($university['location']); ?></p>
                                </div>
                            </div>
                            <div class="university-details">
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span>Representative: <?php echo htmlspecialchars($university['representative_name']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Programs: <?php echo $university['total_programs']; ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>Students: <?php echo $university['total_students']; ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo htmlspecialchars($university['email']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($university['phone']); ?></span>
                                </div>
                                <span class="university-status <?php echo $university['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $university['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filterUniversities() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const sortBy = document.getElementById('sortBy').value;
            const cards = document.querySelectorAll('.university-card');

            cards.forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const status = card.querySelector('.university-status').textContent.toLowerCase();
                const isVisible = name.includes(searchInput) && 
                                (statusFilter === 'all' || status === statusFilter);
                card.style.display = isVisible ? 'block' : 'none';
            });

            // Sort universities
            const grid = document.getElementById('universityGrid');
            const sortedCards = Array.from(cards).sort((a, b) => {
                if (sortBy === 'name') {
                    return a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent);
                } else if (sortBy === 'programs') {
                    const aPrograms = parseInt(a.querySelector('.detail-item:nth-child(2) span').textContent.split(':')[1]);
                    const bPrograms = parseInt(b.querySelector('.detail-item:nth-child(2) span').textContent.split(':')[1]);
                    return bPrograms - aPrograms;
                } else if (sortBy === 'students') {
                    const aStudents = parseInt(a.querySelector('.detail-item:nth-child(3) span').textContent.split(':')[1]);
                    const bStudents = parseInt(b.querySelector('.detail-item:nth-child(3) span').textContent.split(':')[1]);
                    return bStudents - aStudents;
                }
            });

            sortedCards.forEach(card => grid.appendChild(card));
        }
    </script>
</body>
</html> 