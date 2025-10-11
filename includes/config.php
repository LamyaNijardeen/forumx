<?php
// simple loader for .env (INI style)
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die(".env file not found. Create one at project root.");
}
$env = parse_ini_file($envPath);

// fallback defaults
$host = $env['DB_HOST'] ?? 'localhost';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$db   = $env['DB_NAME'] ?? 'forumx_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
// You can use $conn for queries
