<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ForumX</title>
  <link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <div style="display:flex;align-items:center;gap:8px;">
      <a class="site-brand" href="/forumx/pages/home.php">ForumX</a>
      <nav style="display:flex;align-items:center;">
        <a href="/forumx/pages/home.php">Home</a>
        <a href="/forumx/pages/about.php">About</a>
      </nav>
    </div>

    <div class="nav-right">
      <?php if ($user): ?>
        <a class="button" href="/forumx/pages/create_blog.php">Create Post</a>
        <span style="color:#333;">Hello, <strong><?php echo htmlspecialchars($user['username']); ?></strong></span>
        <a href="/forumx/pages/logout.php" style="margin-left:8px;">Logout</a>
      <?php else: ?>
        <a class="ghost" href="/forumx/pages/register.php">Get started</a>
        <a href="/forumx/pages/login.php" style="margin-left:8px;">Sign in</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
