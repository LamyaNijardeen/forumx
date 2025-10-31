<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// CSRF check
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    exit('Invalid CSRF token');
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
if ($post_id <= 0) { http_response_code(400); exit('Invalid post id'); }

// fetch post owner and image path
$stmt = $conn->prepare("SELECT user_id, image_path FROM blogPost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) { http_response_code(404); exit('Post not found'); }

// authorization
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    exit('Forbidden');
}

// delete DB row
$del = $conn->prepare("DELETE FROM blogPost WHERE id = ?");
$del->bind_param('i', $post_id);
if ($del->execute()) {
    // remove image file if exists
    if (!empty($post['image_path'])) {
        $path = __DIR__ . '/../assets/images/' . $post['image_path'];
        if (file_exists($path)) @unlink($path);
    }
    header('Location: /home.php?deleted=1');
    exit;
} else {
    http_response_code(500);
    exit('Failed to delete');
}
$del->close();
