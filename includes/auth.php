<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        // redirect to login and stop
        header('Location: /forumx/pages/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function current_user() {
    global $conn;
    if (!is_logged_in()) return null;
    $uid = intval($_SESSION['user_id']);
    // Optionally cache in session
    if (isset($_SESSION['current_user'])) return $_SESSION['current_user'];

    $stmt = $conn->prepare("SELECT id, username, email, role FROM `user` WHERE id = ?");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc() ?: null;
    $stmt->close();

    if ($user) {
        // store limited info in session to reduce DB hits
        $_SESSION['current_user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }
    return $_SESSION['current_user'] ?? null;
}
