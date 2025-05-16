<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">:
    <link rel="stylesheet" href="css/settings.css">
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

    <main class="settings-container">
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

        <div class="settings-content">
            <h2>Account Settings</h2>
            
            <div class="settings-section">
                <h3>Security</h3>
                <form class="settings-form">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword">
                    </div>
                    <button type="submit" class="btn-save">Update Password</button>
                </form>
            </div>

            <div class="settings-section">
                <h3>Notifications</h3>
                <div class="notification-settings">
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" checked>
                            Email Notifications
                        </label>
                        <p>Receive updates about your applications via email</p>
                    </div>
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" checked>
                            SMS Notifications
                        </label>
                        <p>Receive important updates via SMS</p>
                    </div>
                    <div class="setting-item">
                        <label>
                            <input type="checkbox">
                            Marketing Emails
                        </label>
                        <p>Receive promotional emails about new universities and programs</p>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h3>Privacy</h3>
                <div class="privacy-settings">
                    <div class="setting-item">
                        <label>
                            <input type="checkbox" checked>
                            Show Profile to Universities
                        </label>
                        <p>Allow universities to view your profile information</p>
                    </div>
                    <div class="setting-item">
                        <label>
                            <input type="checkbox">
                            Show Applications to Other Users
                        </label>
                        <p>Allow other users to see your application status</p>
                    </div>
                </div>
            </div>

            <div class="settings-section danger-zone">
                <h3>Danger Zone</h3>
                <div class="danger-actions">
                    <button class="btn-delete-account">Delete Account</button>
                    <p>This action cannot be undone. All your data will be permanently deleted.</p>
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

    <script src="js/settings.js"></script>
</body>
</html> 