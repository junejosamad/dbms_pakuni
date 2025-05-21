<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_authenticated() {
    return isset($_SESSION['user_id']);
}

function require_auth() {
    if (!is_authenticated()) {
        header("Location: login.php");
        exit();
    }
}

function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function require_role($role) {
    require_auth();
    if (get_user_role() !== $role) {
        header("Location: unauthorized.php");
        exit();
    }
}

function set_user_session($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
}

function clear_user_session() {
    session_unset();
    session_destroy();
}
?> 