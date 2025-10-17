<?php
// pages/view_blog.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) { http_response_code(400); exit("Invalid ID"); }

$stmt = $conn->prepare("SELECT b.*, u.username FROM blogPost b JOIN user u ON b.user_id = u.id WHERE b.id=?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) { http_response_code(404); exit("Post not found."); }
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="layout">
  <div class="main">
    <article class="card">
      <h1><?php echo htmlspecialchars($post['title']); ?></h1>
      <div class="meta">By <?php echo htmlspecialchars($post['username']); ?> · <?php echo htmlspecialchars($post['created_at']); ?></div>

      <?php if (!empty($post['image_path'])): ?>
        <img src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="" style="max-width:100%;margin:15px 0;">
      <?php endif; ?>

      <div class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>

      <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
        <div style="margin-top:10px;">
          <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>">Edit</a>
          <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline;margin-left:8px" onsubmit="return confirm('Delete this post?');">
            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <button type="submit" style="background:none;border:none;color:#d9534f;cursor:pointer">Delete</button>
          </form>
        </div>
      <?php endif; ?>
    </article>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
