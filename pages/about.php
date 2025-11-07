<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About ForumX</title>
  <link rel="stylesheet" href="../assets/css/about.css" />
  <link rel="icon" type="image/png" href="../assets/images/favicon.png" />
</head>
<body>

<!-- HEADER -->
<header>
  <div class="left-section">
    <div class="logo">ForumX</div>
    <nav>
      <a href="../pages/home.php">Home</a>
      <a href="../pages/about.php" class="active">About us</a>
      <a href="../pages/create_blog.php">Write</a>
      <?php if ($user): ?>
        <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
      <?php else: ?>
        <a href="../pages/register.php">Get Started</a>
      <?php endif; ?>
    </nav>
  </div>

  <?php if ($user): ?>
  <div class="user-info">
    Hello, <?php echo htmlspecialchars($user['username']); ?>
    <div class="separator"></div>
    <a class="logout-btn" href="../pages/logout.php">Logout</a>
  </div>
  <?php endif; ?>

  <!-- Hamburger Icon  -->
  <div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<!--  MOBILE MENU  -->
<div class="mobile-menu" id="mobileMenu">
  <a href="../pages/home.php">Home</a>
  <a href="../pages/about.php" class="active">About us</a>
  <a href="../pages/create_blog.php">Write</a>
  <?php if ($user): ?>
    <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
    <a href="../pages/logout.php">Logout</a>
  <?php else: ?>
    <a href="../pages/register.php">Get Started</a>
  <?php endif; ?>
</div>

<!-- MAIN CONTENT -->
<main class="about-container">
  <section class="about-content">
    <h1>About ForumX</h1>
    <p>
      ForumX is a simple blogging platform to share stories, tutorials, ideas,
      and experiences with a friendly community.
    </p><br>

    <h3>What you can do</h3>
    <ul>
      <li>Create, edit, and delete your blog posts</li>
      <li>Upload images to enhance your posts</li>
      <li>View posts from others and browse by topic</li>
    </ul><br>
    <a href="../pages/create_blog.php" class="create-post-link">Create your first post</a>
  </section>

  <img src="../assets/images/pen.png" alt="Pen" class="pen-img">
</main>

<!-- FOOTER -->
<footer>
  © 2025 ForumX — A community to share ideas.
</footer>

<!--  SCRIPT  -->
<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    hamburger.classList.toggle('open');
  });
</script>

</body>
</html>
