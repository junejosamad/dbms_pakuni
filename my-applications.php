<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/my-applications.css">
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
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
    </nav>

    <main class="applications-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
                <h3>John Doe</h3>
                <p>Student</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="my-applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="documents.php"><i class="fas fa-folder"></i> Documents</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <div class="applications-content">
            <div class="applications-header">
                <h2>My Applications</h2>
                <div class="application-filters">
                    <select id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select id="dateFilter">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="applications-list">
                <div class="application-card">
                    <div class="application-header">
                        <h3>Lahore University of Management Sciences</h3>
                        <span class="status pending">Pending</span>
                    </div>
                    <div class="application-details">
                        <p><strong>Program:</strong> Bachelor of Science in Computer Science</p>
                        <p><strong>Application Date:</strong> January 15, 2024</p>
                        <p><strong>Last Updated:</strong> January 20, 2024</p>
                    </div>
                    <div class="application-progress">
                        <div class="progress-bar">
                            <div class="progress" style="width: 75%"></div>
                        </div>
                        <p>75% Complete</p>
                    </div>
                    <div class="application-actions">
                        <button class="btn-continue">Continue Application</button>
                        <button class="btn-view">View Details</button>
                    </div>
                </div>

                <div class="application-card">
                    <div class="application-header">
                        <h3>University of Engineering and Technology</h3>
                        <span class="status accepted">Accepted</span>
                    </div>
                    <div class="application-details">
                        <p><strong>Program:</strong> Bachelor of Engineering in Electrical</p>
                        <p><strong>Application Date:</strong> December 10, 2023</p>
                        <p><strong>Last Updated:</strong> January 5, 2024</p>
                    </div>
                    <div class="application-progress">
                        <div class="progress-bar">
                            <div class="progress" style="width: 100%"></div>
                        </div>
                        <p>100% Complete</p>
                    </div>
                    <div class="application-actions">
                        <button class="btn-view">View Details</button>
                        <button class="btn-download">Download Offer Letter</button>
                    </div>
                </div>

                <div class="application-card">
                    <div class="application-header">
                        <h3>Quaid-i-Azam University</h3>
                        <span class="status rejected">Rejected</span>
                    </div>
                    <div class="application-details">
                        <p><strong>Program:</strong> Master of Science in Physics</p>
                        <p><strong>Application Date:</strong> November 20, 2023</p>
                        <p><strong>Last Updated:</strong> December 15, 2023</p>
                    </div>
                    <div class="application-progress">
                        <div class="progress-bar">
                            <div class="progress" style="width: 100%"></div>
                        </div>
                        <p>100% Complete</p>
                    </div>
                    <div class="application-actions">
                        <button class="btn-view">View Details</button>
                        <button class="btn-reapply">Reapply</button>
                    </div>
                </div>
            </div>

            <div class="application-stats">
                <div class="stat-card">
                    <h4>Total Applications</h4>
                    <p class="stat-number">3</p>
                </div>
                <div class="stat-card">
                    <h4>Pending</h4>
                    <p class="stat-number">1</p>
                </div>
                <div class="stat-card">
                    <h4>Accepted</h4>
                    <p class="stat-number">1</p>
                </div>
                <div class="stat-card">
                    <h4>Rejected</h4>
                    <p class="stat-number">1</p>
                </div>
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

    <script src="js/my-applications.js"></script>
</body>
</html> 