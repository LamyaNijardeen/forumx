<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();

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
    <link rel="stylesheet" href="../assets/css/entry.css">
</head>
<body>

<header class="navbar">
    <div class="nav-left">
        <h1 class="logo">ForumX</h1>
    </div> 
    <div class="nav-right">
        <a href="../pages/about.php" class="nav-btn">About us</a>
        <a href="../pages/register.php" class="nav-btn">Write</a>
        <a href="../pages/register.php" class="nav-btn filled get-started">Get Started</a>
    </div>
</header>

<main class="hero">
    <div class="hero-content">
        <h2>Welcome to</h2>
        <h1 class="brand">ForumX</h1>
        <p class="desc">Join a growing community of thinkers, writers, and learners</p>
        <div class="cta">
            <a href="../pages/register.php" class="link-main">Get Started — Create an account</a>
            <a href="../pages/login.php" class="link-sub">Log in</a>
        </div>

        <div class="divider">
            <span>Publish</span>
            <span>Engage</span>
            <span>Learn</span>
            <span>Grow Together</span>
        </div>
    </div>

    <div class="hero-image">
        <img src="../assets/images/pen.png" alt="ForumX pen">
    </div>
</main>

<footer class="footer">
    <p>© 2025 ForumX — A community to share ideas.</p>
</footer>

</body>
</html>
