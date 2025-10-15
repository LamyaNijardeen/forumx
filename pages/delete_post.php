<?php
// pages/delete_post.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method Not Allowed");
}

$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    http_response_code(400);
    die("Invalid CSRF token");
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
if ($post_id <= 0) die("Invalid post id");

$user = current_user();
if (!$user) {
    http_response_code(403);
    die("Not authenticated");
}

// fetch owner
$stmt = $conn->prepare("SELECT user_id FROM blogPost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    die("Post not found");
}

if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    die("You are not authorized to delete this post");
}

$del = $conn->prepare("DELETE FROM blogPost WHERE id = ?");
$del->bind_param('i', $post_id);
if ($del->execute()) {
    header('Location: /forumx/pages/home.php?deleted=1');
    exit;
} else {
    http_response_code(500);
    die("Failed to delete post");
}
