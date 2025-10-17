<?php
// pages/profile.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
$profile_id = (int)($_GET['user_id'] ?? 0);

// Fetch user info
$stmt = $conn->prepare("SELECT id, username, email FROM user WHERE id = ?");
$stmt->bind_param('i', $profile_id);
$stmt->execute();
$res = $stmt->get_result();
$profile_user = $res->fetch_assoc();
$stmt->close();

if (!$profile_user) {
    echo "User not found.";
    exit;
}

// Fetch posts by this user
$stmt = $conn->prepare("SELECT id, title, content, created_at, image_path FROM blogPost WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $profile_id);
$stmt->execute();
$res = $stmt->get_result();
$posts = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout">
  <div class="main">
    <h2>Posts by <?php echo htmlspecialchars($profile_user['username']); ?></h2>

    <?php if (empty($posts)): ?>
      <div class="card"><p>No posts yet.</p></div>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
        <article class="card">
          <?php if (!empty($post['image_path'])): ?>
            <img class="card-image" src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
          <?php endif; ?>
          <h3><a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
          <div class="meta"><?php echo htmlspecialchars($post['created_at']); ?></div>
          <div class="excerpt"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 220))); ?>...</div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <aside class="sidebar">
    <div class="profile-card">
      <h3>User Info</h3>
      <p>Username: <?php echo htmlspecialchars($profile_user['username']); ?></p>
      <p>Email: <?php echo htmlspecialchars($profile_user['email']); ?></p>
      <?php if ($user && $user['id'] === $profile_user['id']): ?>
        <p><a href="/forumx/pages/create_blog.php">Create a new post</a></p>
      <?php endif; ?>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
