<?php
$envPath = __DIR__ . '/../.env';
if(!file_exists($envPath)) {
    die(".env file not found!");
}

$env = parse_ini_file($envPath);

$host = $env['DB_HOST'] ?? 'localhost';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$db   = $env['DB_NAME'] ?? 'forumx_db';

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
