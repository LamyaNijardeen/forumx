<?php
// pages/edit_blog.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

$user = current_user();
$errors = [];

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    die("Invalid post ID.");
}

// fetch post
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) die("Post not found.");
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    die("You are not authorized to edit this post.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($token)) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title === '') $errors[] = "Title required.";
        if ($content === '') $errors[] = "Content required.";

        if (empty($errors)) {
            $upd = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->bind_param('ssi', $title, $content, $post_id);
            if ($upd->execute()) {
                header("Location: /forumx/pages/view_blog.php?id=" . $post_id);
                exit;
            } else {
                $errors[] = "DB error: " . $upd->error;
            }
            $upd->close();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<h1>Edit Post</h1>

<?php if (!empty($errors)): ?>
  <div style="background:#ffe6e6;padding:10px;border:1px solid #ffb3b3;margin-bottom:12px">
    <ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
  </div>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <label>Title<br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" style="width:100%;padding:8px;margin-top:6px" required>
  </label><br><br>

  <label>Content<br>
    <textarea name="content" rows="10" style="width:100%;padding:8px;margin-top:6px" required><?php echo htmlspecialchars($post['content']); ?></textarea>
  </label><br><br>

  <button type="submit" style="padding:8px 12px">Update</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
