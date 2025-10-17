<?php
// pages/view_blog.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
$post_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.id AS user_id, u.username
                        FROM blogPost b
                        JOIN user u ON b.user_id = u.id
                        WHERE b.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "Post not found.";
    exit;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout">
  <div class="main">
    <article class="card">
      <?php if (!empty($post['image_path'])): ?>
        <img class="card-image" src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
      <?php endif; ?>

      <h2><?php echo htmlspecialchars($post['title']); ?></h2>
      <div class="meta">
        By <a href="/forumx/pages/profile.php?user_id=<?php echo $post['user_id']; ?>">
          <?php echo htmlspecialchars($post['username']); ?>
        </a> · <?php echo htmlspecialchars($post['created_at']); ?>
      </div>
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

  <aside class="sidebar">
    <div class="profile-card">
      <h3>About ForumX</h3>
      <p>A minimal writing platform for sharing ideas. Create an account and start publishing.</p>
      <?php if (!$user): ?>
        <p><a class="button" href="/forumx/pages/register.php">Join ForumX</a></p>
      <?php else: ?>
        <p><a class="button" href="/forumx/pages/create_blog.php">Write a story</a></p>
      <?php endif; ?>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
