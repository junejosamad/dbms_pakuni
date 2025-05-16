<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universities - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/universities.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1>PakUni</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="universities.php" class="active">Universities</a></li>
            <li><a href="admissions.php">Admissions</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
    </nav>

    <main class="universities-container">
        <div class="search-filters">
            <div class="search-box">
                <input type="text" placeholder="Search universities...">
                <i class="fas fa-search"></i>
            </div>
            <div class="filters">
                <select id="province">
                    <option value="">All Provinces</option>
                    <option value="punjab">Punjab</option>
                    <option value="sindh">Sindh</option>
                    <option value="kpk">Khyber Pakhtunkhwa</option>
                    <option value="balochistan">Balochistan</option>
                    <option value="gilgit">Gilgit-Baltistan</option>
                </select>
                <select id="program">
                    <option value="">All Programs</option>
                    <option value="engineering">Engineering</option>
                    <option value="medical">Medical</option>
                    <option value="business">Business</option>
                    <option value="arts">Arts & Humanities</option>
                    <option value="sciences">Natural Sciences</option>
                </select>
                <select id="level">
                    <option value="">All Levels</option>
                    <option value="bachelors">Bachelor's</option>
                    <option value="masters">Master's</option>
                    <option value="phd">PhD</option>
                </select>
            </div>
        </div>

        <div class="universities-grid">
            <div class="university-card">
                <img src="https://via.placeholder.com/150" alt="University Logo">
                <div class="university-info">
                    <h3>Lahore University of Management Sciences</h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Lahore, Punjab</p>
                    <p class="description">A leading private research university offering programs in business, science, and humanities.</p>
                    <div class="programs">
                        <span class="program-tag">Business</span>
                        <span class="program-tag">Computer Science</span>
                        <span class="program-tag">Economics</span>
                    </div>
                    <div class="university-actions">
                        <a href="apply.php" class="btn-apply">Apply Now</a>
                        <a href="university-details.php" class="btn-details">View Details</a>
                    </div>
                </div>
            </div>

            <div class="university-card">
                <img src="https://via.placeholder.com/150" alt="University Logo">
                <div class="university-info">
                    <h3>University of Engineering and Technology</h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Lahore, Punjab</p>
                    <p class="description">Premier engineering institution offering various engineering and technology programs.</p>
                    <div class="programs">
                        <span class="program-tag">Engineering</span>
                        <span class="program-tag">Technology</span>
                        <span class="program-tag">Architecture</span>
                    </div>
                    <div class="university-actions">
                        <a href="apply.php" class="btn-apply">Apply Now</a>
                        <a href="university-details.php" class="btn-details">View Details</a>
                    </div>
                </div>
            </div>

            <div class="university-card">
                <img src="https://via.placeholder.com/150" alt="University Logo">
                <div class="university-info">
                    <h3>Quaid-i-Azam University</h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Islamabad</p>
                    <p class="description">Leading public research university offering programs in natural and social sciences.</p>
                    <div class="programs">
                        <span class="program-tag">Natural Sciences</span>
                        <span class="program-tag">Social Sciences</span>
                        <span class="program-tag">Humanities</span>
                    </div>
                    <div class="university-actions">
                        <a href="apply.php" class="btn-apply">Apply Now</a>
                        <a href="university-details.php" class="btn-details">View Details</a>
                    </div>
                </div>
            </div>

            <div class="university-card">
                <img src="https://via.placeholder.com/150" alt="University Logo">
                <div class="university-info">
                    <h3>University of Karachi</h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Karachi, Sindh</p>
                    <p class="description">One of the largest public universities in Pakistan with diverse academic programs.</p>
                    <div class="programs">
                        <span class="program-tag">Medicine</span>
                        <span class="program-tag">Law</span>
                        <span class="program-tag">Arts</span>
                    </div>
                    <div class="university-actions">
                        <a href="apply.php" class="btn-apply">Apply Now</a>
                        <a href="university-details.php" class="btn-details">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="pagination">
            <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
            <span class="page-numbers">1 2 3 ... 10</span>
            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
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

    <script src="js/universities.js"></script>
</body>
</html> 