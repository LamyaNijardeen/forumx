<?php
// includes/navbar.php
$user = $user ?? null; // in case $user is not set
?>
<nav style="background:#333;color:#fff;padding:10px;">
    <a href="/forumx/pages/home.php" style="color:#fff;margin-right:10px;">ForumX</a>
    <?php if ($user): ?>
        <a href="/forumx/pages/create_blog.php" style="color:#fff;margin-right:10px;">New Post</a>
        <a href="/forumx/pages/logout.php" style="color:#fff;">Logout</a>
    <?php else: ?>
        <a href="/forumx/pages/login.php" style="color:#fff;margin-right:10px;">Login</a>
        <a href="/forumx/pages/register.php" style="color:#fff;">Register</a>
    <?php endif; ?>
</nav>
