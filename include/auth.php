<?php
/**
 * Authentication & authorization helpers for 56Food.
 * 
 * Usage:
 *   require_once __DIR__ . '/conn.php';
 *   require_once __DIR__ . '/auth.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensure the current visitor is logged in.
 * If not, redirect to login page.
 */
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        // Optionally remember the page they were trying to access
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
        header("Location: /56Food/login.php?redirect={$redirect}");
        exit();
    }
}

/**
 * Ensure the current user is an authenticated admin.
 * If not, redirect to login or home.
 */
function require_admin(): void
{
    if (empty($_SESSION['user_id'])) {
        header("Location: /56Food/login.php");
        exit();
    }

    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        // Not authorized for admin area
        header("Location: /56Food/index.php");
        exit();
    }
}

/**
 * Check if the current user is an admin.
 */
function is_admin(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

