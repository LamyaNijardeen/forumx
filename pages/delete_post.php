<?php
// pages/delete_post.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// CSRF check
$posted_token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($posted_token)) {
    http_response_code(400);
    echo "Invalid CSRF token";
    exit;
}

// Post ID check
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    echo "Invalid post ID.";
    exit;
}

$user = current_user();

// Fetch post
$stmt = $conn->prepare("SELECT user_id FROM blogPost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    echo "Post not found.";
    exit;
}

// Authorization
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    echo "You are not authorized to delete this post.";
    exit;
}

// Delete post
$del = $conn->prepare("DELETE FROM blogPost WHERE id = ?");
$del->bind_param('i', $post_id);
if ($del->execute()) {
    header('Location: /forumx/pages/home.php?deleted=1');
    exit;
} else {
    http_response_code(500);
    echo "Failed to delete post.";
    exit;
}
