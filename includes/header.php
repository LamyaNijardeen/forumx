<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';

$user = current_user();
$searchQuery = htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ForumX</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="fx-header" role="banner">
  <div class="fx-container fx-header-inner">
    <!-- Brand / Home -->
    <div class="fx-brand">
      <?php if ($user): ?>
        <a href="/pages/home.php">Home</a>
      <?php else: ?>
        <a href="/pages/entry.php">ForumX</a>
      <?php endif; ?>
    </div>

    <!-- Search (only visible if logged in) -->
    <?php if ($user): ?>
      <div class="fx-search">
        <form action="/pages/home.php" method="GET" role="search" class="fx-search-form">
          <input type="text" name="q" placeholder="Search posts by title..." value="<?php echo $searchQuery; ?>" aria-label="Search posts by title">
          <button type="submit">Search</button>
        </form>
      </div>
    <?php endif; ?>

    <!-- Nav links -->
    <nav class="fx-nav" role="navigation">
      <?php if ($user): ?>
        <div class="fx-nav-left">
          <a href="/pages/about.php">About</a>
          <a href="/pages/create_blog.php">Write</a>
        </div>
        <div class="fx-nav-right">
          <span class="fx-hello">Hello, <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong></span>
          <a href="/pages/logout.php" class="fx-logout">Logout</a>
        </div>
      <?php else: ?>
        <div class="fx-nav-left">
          <a href="/pages/about.php">About</a>
          <a href="/pages/register.php">Write</a>
        </div>
        <div class="fx-nav-right">
          <a href="/pages/register.php" class="fx-cta">Get Started</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="fx-main fx-container" role="main">
