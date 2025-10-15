<?php
// pages/view_blog.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

$user = current_user();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) die("Invalid post id");

// fetch post with author username
$stmt = $conn->prepare("SELECT blogPost.*, user.username FROM blogPost JOIN user ON blogPost.user_id = user.id WHERE blogPost.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) die("Post not found");

include __DIR__ . '/../includes/header.php';
?>
<article>
  <h1><?php echo htmlspecialchars($post['title']); ?></h1>
  <p><strong>By:</strong> <?php echo htmlspecialchars($post['username']); ?> | <small><?php echo htmlspecialchars($post['created_at']); ?></small></p>
  <hr>
  <div><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
  <hr>

  <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
    <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>" style="padding:6px 10px;background:#007bff;color:#fff;border-radius:4px;text-decoration:none">✏️ Edit</a>

    <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline;margin-left:8px" onsubmit="return confirm('Delete this post?');">
      <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <button type="submit" style="padding:6px 10px;background:#dc3545;color:#fff;border:none;border-radius:4px;cursor:pointer">🗑️ Delete</button>
    </form>
  <?php endif; ?>
</article>

<?php include __DIR__ . '/../includes/footer.php'; ?>
