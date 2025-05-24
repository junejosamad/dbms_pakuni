<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions/auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Get the requested URL from the query parameter
    $redirect_url = $_GET['redirect'] ?? '';
    
    // Validate and sanitize the redirect URL
    $redirect_url = filter_var($redirect_url, FILTER_SANITIZE_URL);
    
    // Ensure the redirect URL is within our domain
    if (strpos($redirect_url, 'http') === 0) {
        $redirect_url = '';
    }
    
    // Redirect based on user role and requested URL
    switch($_SESSION['user_role']) {
        case 'student':
            if (empty($redirect_url)) {
                $redirect_url = 'dashboard.php';
            }
            if (strpos($redirect_url, 'university/dashboard') !== false) {
                header("Location: dashboard.php");
            } else {
                header("Location: " . $redirect_url);
            }
            break;
        case 'university':
            // Always redirect to university dashboard by default
            if (empty($redirect_url) || strpos($redirect_url, 'dashboard.php') !== false) {
                header("Location: university-dashboard.php");
            } else {
                header("Location: " . $redirect_url);
            }
            break;
        case 'admin':
            if (empty($redirect_url)) {
                $redirect_url = 'admin/dashboard.php';
            }
            if (strpos($redirect_url, 'admin/') === false) {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: " . $redirect_url);
            }
            break;
        default:
            header("Location: " . ($redirect_url ?: 'index.php'));
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $result = login_user($email, $password);
        
        if ($result['success']) {
            // Get the requested URL from the query parameter
            $redirect_url = $_GET['redirect'] ?? '';
            
            // Validate and sanitize the redirect URL
            $redirect_url = filter_var($redirect_url, FILTER_SANITIZE_URL);
            
            // Ensure the redirect URL is within our domain
            if (strpos($redirect_url, 'http') === 0) {
                $redirect_url = '';
            }
            
            // Redirect based on role and requested URL
            switch($result['user']['role']) {
                case 'student':
                    if (empty($redirect_url)) {
                        $redirect_url = 'dashboard.php';
                    }
                    if (strpos($redirect_url, 'university/dashboard') !== false) {
                        header("Location: dashboard.php");
                    } else {
                        header("Location: " . $redirect_url);
                    }
                    break;
                case 'university':
                    // Always redirect to university dashboard by default
                    if (empty($redirect_url) || strpos($redirect_url, 'dashboard.php') !== false) {
                        header("Location: university-dashboard.php");
                    } else {
                        header("Location: " . $redirect_url);
                    }
                    break;
                case 'admin':
                    if (empty($redirect_url)) {
                        $redirect_url = 'admin/dashboard.php';
                    }
                    if (strpos($redirect_url, 'admin/') === false) {
                        header("Location: admin/dashboard.php");
                    } else {
                        header("Location: " . $redirect_url);
                    }
                    break;
                default:
                    header("Location: " . ($redirect_url ?: 'index.php'));
            }
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1><a href="index.php">PakUni</a></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="universities.php">Universities</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
    </nav>

    <main class="auth-container">
        <div class="auth-box">
            <h2>Welcome Back</h2>
            <p class="auth-subtitle">Login to your PakUni account</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="auth-button">Login</button>
                
                <div class="auth-divider">
                    <span>or</span>
                </div>
                
                <div class="social-login">
                    <button type="button" class="social-button google">
                        <i class="fab fa-google"></i> Continue with Google
                    </button>
                    <button type="button" class="social-button facebook">
                        <i class="fab fa-facebook-f"></i> Continue with Facebook
                    </button>
                </div>
            </form>
            
            <p class="auth-footer">
                Don't have an account? <a href="register.php">Register now</a>
            </p>
        </div>
    </main>

    <section class="featured-universities">
        <div class="university-banner">
            <img src="https://placehold.co/1200x400/4a90e2/ffffff?text=Top+Universities+in+Pakistan" alt="Featured Universities" class="university-image">
            <div class="university-overlay">
                <h2>Top Universities in Pakistan</h2>
                <p>Discover leading institutions for your academic journey</p>
            </div>
        </div>
        <div class="university-list">
            <div class="university-card">
                <img src="https://placehold.co/400x300/4a90e2/ffffff?text=LUMS" alt="LUMS">
                <h3>LUMS</h3>
                <p>Lahore University of Management Sciences</p>
            </div>
            <div class="university-card">
                <img src="https://placehold.co/400x300/4a90e2/ffffff?text=NUST" alt="NUST">
                <h3>NUST</h3>
                <p>National University of Sciences and Technology</p>
            </div>
            <div class="university-card">
                <img src="https://placehold.co/400x300/4a90e2/ffffff?text=AKU" alt="AKU">
                <h3>AKU</h3>
                <p>Aga Khan University</p>
            </div>
        </div>
    </section>

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

    <script src="js/auth.js"></script>
</body>
</html> 