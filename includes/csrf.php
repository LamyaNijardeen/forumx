<?php
// includes/csrf.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Return a CSRF token (creates one if missing).
 * Stored in session.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a posted token.
 */
function validate_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}
