<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1>PakUni</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="universities.php">Universities</a></li>
            <li><a href="admissions.php">Admissions</a></li>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
                <li><a href="register.php" class="btn-register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p><?php echo htmlspecialchars($user_role); ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="#applications"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="#documents"><i class="fas fa-folder"></i> Documents</a></li>
                    <li><a href="#profile"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <div class="dashboard-content">
            <div class="dashboard-header">
                <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-university"></i>
                    <div class="stat-info">
                        <h3>Applications</h3>
                        <p>5 Active</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-info">
                        <h3>Accepted</h3>
                        <p>2 Offers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <p>3 Applications</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-info">
                        <h3>Deadlines</h3>
                        <p>2 Upcoming</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <section class="recent-applications">
                    <h3>Recent Applications</h3>
                    <div class="application-list">
                        <div class="application-card">
                            <div class="university-info">
                                <img src="https://via.placeholder.com/50" alt="University Logo">
                                <div>
                                    <h4>University of Engineering and Technology</h4>
                                    <p>Computer Science</p>
                                </div>
                            </div>
                            <div class="application-status pending">
                                <span>Pending Review</span>
                            </div>
                        </div>
                        <div class="application-card">
                            <div class="university-info">
                                <img src="https://via.placeholder.com/50" alt="University Logo">
                                <div>
                                    <h4>Lahore University of Management Sciences</h4>
                                    <p>Business Administration</p>
                                </div>
                            </div>
                            <div class="application-status accepted">
                                <span>Accepted</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="upcoming-deadlines">
                    <h3>Upcoming Deadlines</h3>
                    <div class="deadline-list">
                        <div class="deadline-card">
                            <div class="deadline-info">
                                <h4>University of Karachi</h4>
                                <p>Bachelor's in Computer Science</p>
                            </div>
                            <div class="deadline-date">
                                <i class="fas fa-calendar"></i>
                                <span>March 15, 2024</span>
                            </div>
                        </div>
                        <div class="deadline-card">
                            <div class="deadline-info">
                                <h4>Quaid-i-Azam University</h4>
                                <p>Master's in Data Science</p>
                            </div>
                            <div class="deadline-date">
                                <i class="fas fa-calendar"></i>
                                <span>March 20, 2024</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About PakUni</h3>
                <p>Simplifying university applications for students across Pakistan</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@pakuni.com</p>
                <p>Phone: +92 300 1234567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 PakUni. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/dashboard.js"></script>
</body>
</html> 