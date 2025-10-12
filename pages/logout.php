<?php
// pages/logout.php
session_start();

// Remove all session data
$_SESSION = [];

// If using cookies for session, remove cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Redirect to home
header('Location: /forumx/pages/home.php');
exit;
