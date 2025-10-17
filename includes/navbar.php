<?php
// includes/navbar.php
$user = $user ?? null; // ensure $user is set
?>
<nav class="navbar">
    <div class="nav-left">
        <a href="/forumx/pages/home.php" class="nav-brand">ForumX</a>
    </div>
    <div class="nav-right">
        <?php if ($user): ?>
            <a href="/forumx/pages/create_blog.php">New Post</a>
            <a href="/forumx/pages/logout.php">Logout</a>
        <?php else: ?>
            <a href="/forumx/pages/login.php">Login</a>
            <a href="/forumx/pages/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
