<?php
// pages/home.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();

$sql = "SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.username, b.user_id
        FROM blogPost b
        JOIN user u ON b.user_id = u.id
        ORDER BY b.created_at DESC";
$res = $conn->query($sql);
$posts = [];
if ($res) while ($r = $res->fetch_assoc()) $posts[] = $r;
?>

<?php include __DIR__ . '/../includes/header.php'; ?>


<div class="layout">
  <div class="main">
    <h1>Recent Posts</h1>
    <?php if (empty($posts)): ?>
      <div class="card"><p>No posts yet. <?php if ($user) echo '<a href="/forumx/pages/create_blog.php">Create the first post</a>.'; ?></p></div>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
        <article class="card">
          <?php if (!empty($post['image_path'])): ?>
            <img src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="" style="max-width:100%;margin:10px 0;">
          <?php endif; ?>
          <h2><a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
          <div class="meta">By <?php echo htmlspecialchars($post['username']); ?> · <?php echo htmlspecialchars($post['created_at']); ?></div>
          <div class="excerpt"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 220))); ?>...</div>
          <div style="margin-top:10px;">
            <a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>">Read more</a>
            <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
              <span style="margin-left:10px;"><a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>">Edit</a></span>
              <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline;margin-left:8px" onsubmit="return confirm('Delete this post?');">
                <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <button type="submit" style="background:none;border:none;color:#d9534f;cursor:pointer">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
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
