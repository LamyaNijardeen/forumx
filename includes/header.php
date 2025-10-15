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
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0}
    header{background:#f7f7f7;padding:12px 16px;border-bottom:1px solid #ddd}
    nav{display:flex;align-items:center;gap:12px}
    .nav-right{margin-left:auto}
    a{color:#007bff;text-decoration:none}
    a:hover{text-decoration:underline}
    .container{max-width:900px;margin:20px auto;padding:0 16px}
  </style>
</head>
<body>
<header>
  <nav class="container">
    <a href="/forumx/pages/home.php"><strong>ForumX</strong></a>
    <a href="/forumx/pages/home.php">Home</a>
    <?php if ($user): ?>
      <a href="/forumx/pages/create_blog.php">Create Post</a>
    <?php endif; ?>

    <div class="nav-right">
      <?php if ($user): ?>
        Hello, <strong><?php echo htmlspecialchars($user['username']); ?></strong>
        | <a href="/forumx/pages/logout.php">Logout</a>
      <?php else: ?>
        <a href="/forumx/pages/register.php">Register</a> | <a href="/forumx/pages/login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
<main class="container">
