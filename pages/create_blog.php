<?php
// pages/create_blog.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($token)) {
        $errors[] = "Invalid request (CSRF).";
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '') $errors[] = "Title required.";
        if ($content === '') $errors[] = "Content required.";

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
            $uid = $user['id'];
            $stmt->bind_param('iss', $uid, $title, $content);
            if ($stmt->execute()) {
                $new_id = $stmt->insert_id;
                header("Location: /forumx/pages/view_blog.php?id=" . $new_id);
                exit;
            } else {
                $errors[] = "DB error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h1>Create Post</h1>

<?php if (!empty($errors)): ?>
  <div style="background:#ffe6e6;padding:10px;border:1px solid #ffb3b3;margin-bottom:12px">
    <ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
  </div>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <label>Title<br><input type="text" name="title" style="width:100%;padding:8px;margin-top:6px" required></label><br><br>
  <label>Content (Markdown/plain HTML will be escaped on view)<br>
    <textarea name="content" rows="10" style="width:100%;padding:8px;margin-top:6px" required></textarea>
  </label><br><br>
  <button type="submit" style="padding:8px 12px">Create</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
