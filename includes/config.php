<?php
// includes/config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Load .env-like file (simple key=val parser). Put .env in project root.
 * Example .env:
 * DB_HOST=localhost
 * DB_USER=root
 * DB_PASS=
 * DB_NAME=forumx_db
 */
$envPath = __DIR__ . '/../.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if ($line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}

// Fallback defaults
$dbHost = $env['DB_HOST'] ?? 'localhost';
$dbUser = $env['DB_USER'] ?? 'root';
$dbPass = $env['DB_PASS'] ?? '';
$dbName = $env['DB_NAME'] ?? 'forumx_db';

// create mysqli connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    // In dev show error; in prod you would log and show a generic message
    die('DB Connection failed: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset('utf8mb4');

/**
 * Small helper to prepare & run SQL safely when needed.
 * Returns mysqli_stmt or false.
 */
function db_prepare($sql) {
    global $conn;
    return $conn->prepare($sql);
}
