<?php
session_start();

require_once __DIR__ . "/include/conn.php";
require_once __DIR__ . "/include/activity_log.php";

// ================================
// Log logout activity BEFORE session destroy
// ================================
if (!empty($_SESSION['user_id'])) {
    log_activity(
        $conn,
        $_SESSION['user_id'],
        'LOGOUT',
        'User logged out of the system'
    );
}

// ================================
// Clear session data
// ================================
$_SESSION = [];

// ================================
// Destroy session
// ================================
if (session_id() !== '') {
    session_destroy();
}

// ================================
// Remove session cookie (security best practice)
// ================================
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ================================
// Redirect to login page
// ================================
header("Location: login.php");
exit;
