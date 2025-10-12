<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>ForumX</title>
  <link rel="stylesheet" href="/forumx/assets/css/style.css">
  <style>
    nav{padding:10px;display:flex;gap:12px;align-items:center}
    nav a{text-decoration:none}
    .nav-right{margin-left:auto}
  </style>
</head>
<body>
<nav>
  <a href="/forumx/">Home</a>
  <?php if ($user): ?>
    <a href="/forumx/pages/create_blog.php">Create Post</a>
  <?php endif; ?>

  <div class="nav-right">
    <?php if ($user): ?>
      Hello, <strong><?php echo htmlspecialchars($user['username']); ?></strong>
      | <a href="/forumx/pages/logout.php">Logout</a>
    <?php else: ?>
      <a href="/forumx/pages/register.php">Register</a> |
      <a href="/forumx/pages/login.php">Login</a>
    <?php endif; ?>
  </div>
</nav>
<hr/>
