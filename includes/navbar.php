<?php
// includes/navbar.php
$user = $user ?? null; // ensure $user is set
?>
<nav class="navbar">
    <div class="nav-left">
        <a href="/../pages/home.php" class="nav-brand">ForumX</a>
    </div>
    <div class="nav-right">
        <?php if ($user): ?>
            <a href="/../pages/create_blog.php">New Post</a>
            <a href="/../pages/logout.php">Logout</a>
        <?php else: ?>
            <a href="/../pages/login.php">Login</a>
            <a href="/../pages/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
