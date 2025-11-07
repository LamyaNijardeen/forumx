<?php
// Include essential backend files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// Verify login status
$user = current_user();
if (!$user) {
    header('Location: ../pages/entry.php');
    exit;
}

// Handle search query
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    // If search keyword is entered, fetch matching posts
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.username, u.profile_photo, b.user_id
        FROM blogpost b
        JOIN user u ON b.user_id = u.id
        WHERE b.title LIKE ?
        ORDER BY b.created_at DESC
    ");
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    $posts = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Otherwise, fetch all posts
    $sql = "
        SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.username,u.profile_photo, b.user_id
        FROM blogpost b
        JOIN user u ON b.user_id = u.id
        ORDER BY b.created_at DESC
    ";
    $res = $conn->query($sql);
    $posts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!--  STYLES & FAVICON -->
<link rel="stylesheet" href="../assets/css/home.css">
<link rel="icon" type="image/png" href="../assets/images/favicon.png">

<!--  HEADER -->
<header>
  <div class="left-section">
    <div class="logo">ForumX</div>

    <!-- Navigation -->
    <nav>
      <a href="../pages/home.php" class="active">Home</a>
      <a href="../pages/about.php">About us</a>
      <a href="../pages/create_blog.php">Write</a>
      <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
    </nav>
  </div>

  <!--  User info and logout -->
  <div class="user-info">
    Hello, <?php echo htmlspecialchars($user['username']); ?>
    <div class="separator"></div>
    <a class="logout-btn" href="../pages/logout.php">Logout</a>
  </div>

  <!--  Hamburger menu for mobile -->
  <div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<!--  MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <a href="../pages/home.php" class="active">Home</a>
  <a href="../pages/about.php">About us</a>
  <a href="../pages/create_blog.php">Write</a>
  <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
  <a href="../pages/logout.php">Logout</a>
</div>

<!--  SEARCH BAR -->
<div class="search-container">
  <form method="GET" action="">
    <input type="text" name="q" placeholder="Search by topic..." value="<?php echo htmlspecialchars($q); ?>">
    <button type="submit" title="Search">🔍</button>
  </form>
</div>

<!--  TITLE SECTION -->
<div class="whats-new">
  <h2>What's New</h2>
</div>

<!--  MAIN CONTENT -->
<main>
  <div class="layout">
    <div class="main blog-grid">

      <!--  If no posts -->
      <?php if (empty($posts)): ?>
        <div class="card"><p>No posts found. <a href="../pages/create_blog.php">Create one now</a>.</p></div>
      <?php else: ?>

        <!--  Display posts -->
        <?php foreach ($posts as $post): ?>
          <article class="blog-card">

            <!-- Blog image -->
            <?php if (!empty($post['image_path'])): ?>
              <img src="../assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Blog image">
            <?php endif; ?>

            <!-- Blog content -->
            <div class="blog-card-content">
              <h2>
                <a href="../pages/view_blog.php?id=<?php echo (int)$post['id']; ?>">
                  <?php echo htmlspecialchars($post['title']); ?>
                </a>
              </h2>

              <!-- Metadata -->
               <div class="meta">
  <img src="../assets/profile_photos/<?php echo htmlspecialchars($post['profile_photo'] ?: 'default.png'); ?>" 
       alt="Profile Photo" class="profile-mini">
  By <?php echo htmlspecialchars($post['username']); ?> —
  <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
</div>


              <!-- Preview text -->
              <?php
                $words = explode(' ', strip_tags($post['content']));
                $preview = implode(' ', array_slice($words, 0, 2));
              ?>
              <p><?php echo htmlspecialchars($preview); ?>...</p>

              <a href="../pages/view_blog.php?id=<?php echo (int)$post['id']; ?>" class="read-more">Read more →</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!--  SIDEBAR -->
    <aside class="sidebar">
      <img src="../assets/images/pen.png" alt="Pen icon">
      <p>A minimal writing platform for sharing ideas</p>
      <p>Create an account and start publishing</p>
      <p>Search your interest by topics</p>
      <p>Learn together with bright minds</p>
      <p>Share your knowledge — we are here to see</p>
      <br>
      <p><strong>Start Today</strong></p>
      <a href="../pages/create_blog.php">Write a story</a>
      <p style="margin-top:1rem; font-size:0.85rem; color:#888;">© 2025 ForumX</p>
    </aside>
  </div>
</main>

<!--  FOOTER -->
<footer>
  © 2025 ForumX — A community to share ideas.
</footer>

<!--  MOBILE MENU SCRIPT -->
<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    hamburger.classList.toggle('open');
  });
</script>
