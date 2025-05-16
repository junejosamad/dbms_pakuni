<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function requireRole($role) {
    requireLogin();
    if (getUserRole() !== $role) {
        header("Location: unauthorized.php");
        exit();
    }
}

function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
}

function clearUserSession() {
    session_unset();
    session_destroy();
}
?> 