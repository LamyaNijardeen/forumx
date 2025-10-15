<?php
// pages/edit_blog.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

require_login();
$user = current_user();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    echo "Invalid post ID.";
    exit;
}

// Fetch post
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
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

// Ownership check
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    echo "You are not authorized to edit this post.";
    exit;
}

// Initialize errors
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid request.";
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        // Simple validation
        if (empty($title)) $errors[] = "Title is required.";
        if (empty($content)) $errors[] = "Content is required.";

        if (empty($errors)) {
            // Update the blog
            $upd = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->bind_param('ssi', $title, $content, $post_id);
            if ($upd->execute()) {
                header("Location: /forumx/pages/view_blog.php?id=$post_id&updated=1");
                exit;
            } else {
                $errors[] = "Failed to update blog.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Blog</title>
<link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
<h1>Edit Blog</h1>

<?php if ($errors): ?>
  <ul style="color:red;">
  <?php foreach ($errors as $e): ?>
    <li><?php echo htmlspecialchars($e); ?></li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form action="" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="10" cols="50" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

    <button type="submit">Update Blog</button>
</form>

<p><a href="/forumx/pages/view_blog.php?id=<?php echo $post_id; ?>">Back to Blog</a></p>
</body>
</html>
