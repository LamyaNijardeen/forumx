<?php
//  Include configuration and authentication files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

//  Get currently logged-in user (if any)
$user = current_user();

//  Redirect logged-in users to home page
if ($user) {
    header('Location: ../pages/home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to ForumX</title>

  <!--  Link to CSS and favicon -->
  <link rel="stylesheet" href="../assets/css/entry.css">
  <link rel="icon" type="image/png" href="../assets/images/favicon.png">
</head>
<body>

<!-- HEADER SECTION -->
<header>
  <div class="left-section">
    <h1 class="logo">ForumX</h1>
  </div>

  <!--  Navigation Links -->
  <nav>
    <a href="../pages/about.php">About us</a>
    <a href="../pages/register.php">Write</a>
    <a href="../pages/register.php" class="active">Get Started</a>
  </nav>

  <!--  Hamburger menu for mobile -->
  <div class="hamburger" id="hamburger">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <a href="../pages/about.php">About us</a>
  <a href="../pages/register.php">Write</a>
  <a href="../pages/register.php">Get Started</a>
  <a href="../pages/login.php">Log In</a>
</div>

<!-- HERO SECTION (Main welcome area) -->
<main class="hero">
  <div class="hero-content">
    <h2>Welcome to</h2>
    <h1 class="brand">ForumX</h1>
    <p class="desc">Join a growing community of thinkers, writers, and learners</p>

    <!--  Call to action buttons -->
    <div class="cta">
      <a href="../pages/register.php" class="link-main">Get Started — Create an account  </a>
      <a href="../pages/login.php" class="link-sub">Log in</a>
    </div>

    <!--  Divider text -->
    <div class="divider">
      <span>Publish</span>
      <span>Engage</span>
      <span>Learn</span>
      <span>Grow Together</span>
    </div>
  </div>

  <!--  Hero image -->
  <div class="hero-image">
    <img src="../assets/images/pen.png" alt="ForumX pen">
  </div>
</main>

<!-- FOOTER SECTION -->
<footer class="footer">
  <p>© 2025 ForumX — A community to share ideas.</p>
</footer>

<!-- SCRIPT: Mobile menu toggle -->
<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('active');
});
</script>

</body>
</html>
