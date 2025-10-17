<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php'; // DB connection

/**
 * Check if a user is logged in.
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Return current logged-in user info as array, or null if not logged in.
 */
function current_user(): ?array {
    global $conn;
    if (!is_logged_in()) return null;

    $stmt = $conn->prepare("SELECT id, username, email, role FROM user WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}

/**
 * Middleware: require login to access page.
 * Redirects to login.php if not logged in.
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Check if current user is admin.
 */
function is_admin(): bool {
    $user = current_user();
    return $user && $user['role'] === 'admin';
}
