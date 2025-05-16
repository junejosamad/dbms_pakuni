<?php
require_once 'config/database.php';
require_once 'config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!in_array($role, ['student', 'university'])) {
        $error = "Invalid role selected";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":email", $email);
        $check_stmt->execute();

        if ($check_stmt->fetch()) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":role", $role);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
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
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
    </nav>

    <main class="auth-container">
        <div class="auth-box">
            <h2>Create Account</h2>
            <p class="auth-subtitle">Join PakUni to start your journey</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" class="auth-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="role">I am a:</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="university">University Representative</option>
                    </select>
                </div>
                
                <button type="submit" class="auth-button">Register</button>
                
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
                Already have an account? <a href="login.php">Login now</a>
            </p>
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

    <script src="js/auth.js"></script>
</body>
</html> 