<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

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
$search = trim($_GET['q'] ?? '');
if ($search) {
    $query = "SELECT id, title, created_at, image_path 
              FROM blogPost 
              WHERE user_id = ? AND title LIKE CONCAT('%', ?, '%') 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $profile_id, $search);
} else {
    $query = "SELECT id, title, created_at, image_path 
              FROM blogPost 
              WHERE user_id = ? 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $profile_id);
}
$stmt->execute();
$res = $stmt->get_result();
$posts = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($profile_user['username']); ?> - ForumX</title>
  <link rel="stylesheet" href="/assets/css/profile.css">
</head>
<body>

  <header>
    <div class="left-section">
      <div class="logo">ForumX</div>
      <nav>
        <a href="home.php" class="nav-btn">Home</a>
        <a href="about.php" class="nav-btn">About</a>
        <a href="create_blog.php" class="nav-btn">Write</a>
        <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="nav-btn active">My Profile</a>
      </nav>
    </div>
    <div class="user-info">
      <span>Hello, <?php echo htmlspecialchars($user['username']); ?></span>
      <div class="separator"></div>
      <a href="/pages/logout.php" class="logout-btn">Logout</a>
    </div>
  </header>

  <div class="search-container">
    <form method="get" action="">
      <input type="hidden" name="user_id" value="<?php echo $profile_id; ?>">
      <input type="text" name="q" placeholder="Search by topic..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
      <button type="submit">🔍</button>
    </form>
  </div>

  <main class="profile-content">
    <h2><?php echo htmlspecialchars($profile_user['username']); ?>’s Blogs</h2>

    <?php if (empty($posts)): ?>
      <p class="no-posts">No posts yet.</p>
    <?php else: ?>
      <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
          <a href="view_blog.php?id=<?php echo $post['id']; ?>" class="blog-link">
            <div class="blog-card">
              <?php if (!empty($post['image_path'])): ?>
                <div class="blog-image">
                  <img src="/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Blog Image">
                </div>
              <?php endif; ?>

              <div class="blog-details">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <div class="meta">
                  <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                  <span class="time"><?php echo date('H:i', strtotime($post['created_at'])); ?></span>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer class="footer">
    © 2025 ForumX — A community to share ideas.
  </footer>

</body>
</html>
