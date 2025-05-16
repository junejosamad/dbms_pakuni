<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Documents - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/documents.css">
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

    <main class="documents-container">
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

        <div class="documents-content">
            <div class="documents-header">
                <h2>My Documents</h2>
                <button class="btn-upload">Upload New Document</button>
            </div>

            <div class="documents-grid">
                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="document-info">
                        <h3>Matric Certificate</h3>
                        <p>Uploaded: 15 Jan 2024</p>
                        <p>Status: <span class="status verified">Verified</span></p>
                    </div>
                    <div class="document-actions">
                        <button class="btn-view">View</button>
                        <button class="btn-delete">Delete</button>
                    </div>
                </div>

                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="document-info">
                        <h3>Intermediate Certificate</h3>
                        <p>Uploaded: 20 Jan 2024</p>
                        <p>Status: <span class="status pending">Pending</span></p>
                    </div>
                    <div class="document-actions">
                        <button class="btn-view">View</button>
                        <button class="btn-delete">Delete</button>
                    </div>
                </div>

                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-image"></i>
                    </div>
                    <div class="document-info">
                        <h3>CNIC</h3>
                        <p>Uploaded: 25 Jan 2024</p>
                        <p>Status: <span class="status verified">Verified</span></p>
                    </div>
                    <div class="document-actions">
                        <button class="btn-view">View</button>
                        <button class="btn-delete">Delete</button>
                    </div>
                </div>
            </div>

            <div class="upload-instructions">
                <h3>Document Requirements</h3>
                <ul>
                    <li>All documents must be in PDF or image format (JPG, PNG)</li>
                    <li>Maximum file size: 5MB</li>
                    <li>Documents must be clear and legible</li>
                    <li>All certificates must be attested</li>
                </ul>
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

    <script src="js/documents.js"></script>
</body>
</html> 