<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

// Check if user is logged in
$isLoggedIn = is_authenticated();
$userRole = get_user_role();
$userName = $_SESSION['user_name'] ?? '';

// Define base URL
$baseUrl = '/pakuni';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'PakUni'; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/dashboard.css">
    <?php if ($userRole === 'university'): ?>
        <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/university-dashboard.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if (isset($additionalStyles)): ?>
        <?php foreach ($additionalStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo $baseUrl . $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1><a href="<?php echo $baseUrl; ?>/index.php">PakUni</a></h1>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $baseUrl; ?>/index.php" <?php echo $currentPage === 'home' ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="<?php echo $baseUrl; ?>/universities.php" <?php echo $currentPage === 'universities' ? 'class="active"' : ''; ?>>Universities</a></li>
            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'university'): ?>
                    <li><a href="<?php echo $baseUrl; ?>/university-dashboard.php" <?php echo $currentPage === 'dashboard' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $baseUrl; ?>/dashboard.php" <?php echo $currentPage === 'dashboard' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <?php endif; ?>
                <li class="user-menu">
                    <a href="#" class="user-menu-trigger">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($userName); ?>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="user-dropdown">
                        <li><a href="<?php echo $baseUrl; ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="<?php echo $baseUrl; ?>/login.php" class="btn-login">Login</a></li>
                <li><a href="<?php echo $baseUrl; ?>/register.php" class="btn-register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav> 