<?php
// REQUIRED FILES 
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

//  ENSURE USER IS LOGGED IN 
require_login();
$user = current_user();

//  ALLOW ONLY POST REQUESTS 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

//  CSRF PROTECTION 
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    http_response_code(400); // Bad Request
    exit('Invalid CSRF token');
}

// VALIDATE POST ID
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    exit('Invalid post id');
}

//  FETCH POST OWNER AND IMAGE PATH 
$stmt = $conn->prepare("SELECT user_id, image_path FROM blogpost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

//  CHECK IF POST EXISTS
if (!$post) {
    http_response_code(404); // Not Found
    exit('Post not found');
}

//  AUTHORIZATION CHECK
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403); // Forbidden
    exit('Forbidden');
}

// DELETE POST FROM DATABASE 
$del = $conn->prepare("DELETE FROM blogpost WHERE id = ?");
$del->bind_param('i', $post_id);

if ($del->execute()) {
    // DELETE IMAGE FILE IF EXISTS 
    if (!empty($post['image_path'])) {
        $path = __DIR__ . '/../assets/images/' . $post['image_path'];
        if (file_exists($path)) @unlink($path);
    }

    // REDIRECT AFTER SUCCESSFUL DELETION 
    header('Location: ../pages/home.php?deleted=1');
    exit;
} else {
    // ERROR HANDLING
    http_response_code(500); // Internal Server Error
    exit('Failed to delete');
}

$del->close();
