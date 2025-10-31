<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

$user = current_user();

// Redirect guest to entry page
if (!$user) {
    header('Location: entry.php');
    exit;
}

// Handle search
$q = trim((string)($_GET['q'] ?? ''));

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

<link rel="stylesheet" href="/assets/css/home.css">

<header>
  <div class="left-section">
    <div class="logo">ForumX</div>
    <nav>
      <a href="home.php" class="active">Home</a>
      <a href="about.php">About us</a>
      <a href="create_blog.php">Write</a>
      <a href="profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
    </nav>
  </div>
  <div class="user-info">
    Hello, <?php echo htmlspecialchars($user['username']); ?>
    <div class="separator"></div>
    <a class="logout-btn" href="logout.php">Logout</a>
  </div>
</header>
  <!-- Search Bar -->
  <div class="search-container">
    <form method="GET" action="">
      <input type="text" name="q" placeholder="Search by topic..." value="<?php echo htmlspecialchars($q); ?>" >
    <button type="submit" title="Search">🔍</button>
    </form>
  </div>
<main>
  <div class="layout">
    <!-- Main Blog Feed -->
    <div class="main">
      <?php if (empty($posts)): ?>
        <div class="card"><p>No posts found. <a href="create_blog.php">Create one now</a>.</p></div>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
          <article class="card">
            <?php if (!empty($post['image_path'])): ?>
              <div class="image-wrapper">
                <img src="/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Blog image">
              </div>
            <?php endif; ?>

            <h2>
              <a href="view_blog.php?id=<?php echo (int)$post['id']; ?>">
                <?php echo htmlspecialchars($post['title']); ?>
              </a>
            </h2>
            <div class="meta">
              By <?php echo htmlspecialchars($post['username']); ?> &nbsp;&nbsp;
              <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
            </div>
            <p class="excerpt">
              <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 180))); ?>...
            </p>
            <div class="card-actions">
              <a href="view_blog.php?id=<?php echo (int)$post['id']; ?>">Read more</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
      <img src="/assets/images/pen.png" alt="Pen icon">
      <p>A minimal writing platform for sharing ideas</p>
      <p>Create an account and start publishing</p>
      <p>Search your interest by topics</p>
      <p>Learn together with the bright minds</p>
      <p>Share your knowledge — we are here to see</p>
      <br>
      <p><strong>Start Today</strong></p>
      <a href="create_blog.php">Write a story</a>
      <p style="margin-top:1rem; font-size:0.85rem; color:#888;">© 2025 ForumX</p>
    </aside>
  </div>
</main>

<footer>
  © 2025 ForumX — A community to share ideas.
</footer>

<!-- Font Awesome for search icon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
