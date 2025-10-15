<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/csrf.php';

/**
 * Get current user from session (or null)
 * Returns associative array: id, username, email, role
 */
function current_user(): ?array {
    global $conn;
    if (!isset($_SESSION['user_id'])) return null;

    // cache limited user info in session for performance
    if (isset($_SESSION['current_user'])) return $_SESSION['current_user'];

    $uid = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT id, username, email, role FROM `user` WHERE id = ?");
    if (!$stmt) return null;
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc() ?: null;
    $stmt->close();

    if ($user) {
        $_SESSION['current_user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        return $_SESSION['current_user'];
    }
    return null;
}

/**
 * Return true if user is logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Require login (redirects to login page)
 */
function require_login(): void {
    if (!is_logged_in()) {
        // include next param so user returns after login
        $next = $_SERVER['REQUEST_URI'] ?? '/forumx/pages/home.php';
        header('Location: /forumx/pages/login.php?next=' . urlencode($next));
        exit;
    }
}

/**
 * Return true if current user is admin
 */
function is_admin(): bool {
    $u = current_user();
    return $u && isset($u['role']) && $u['role'] === 'admin';
}

/**
 * Logout helper
 */
function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
