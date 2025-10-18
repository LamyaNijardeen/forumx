<?php
// pages/home.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();

// if guest, redirect to entry page
if (!$user) {
    header('Location: entry.php');
    exit;
}

// handle search
$q = trim((string)($_GET['q'] ?? ''));

// if search term present, search by title
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.username, b.user_id
                            FROM blogPost b
                            JOIN user u ON b.user_id = u.id
                            WHERE b.title LIKE ?
                            ORDER BY b.created_at DESC");
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    $posts = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $sql = "SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.username, b.user_id
            FROM blogPost b
            JOIN user u ON b.user_id = u.id
            ORDER BY b.created_at DESC";
    $res = $conn->query($sql);
    $posts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="home-hero">
  <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
  <p>Read, write, and share your ideas on ForumX.</p>
</section>

<div class="layout">
  <div class="main">
    <?php if (empty($posts)): ?>
      <div class="card"><p>No posts found. <a href="create_blog.php">Create one now</a>.</p></div>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
        <article class="card">
          <?php if (!empty($post['image_path'])): ?>
            <img class="card-image" src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
          <?php endif; ?>

          <h2><a href="view_blog.php?id=<?php echo (int)$post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
          <div class="meta">
            By <a href="profile.php?user_id=<?php echo (int)$post['user_id']; ?>"><?php echo htmlspecialchars($post['username']); ?></a> · 
            <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
          </div>
          <div class="excerpt"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 220))); ?>...</div>
          <div class="card-actions">
            <a href="view_blog.php?id=<?php echo (int)$post['id']; ?>">Read more</a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
