<?php
// includes/sidebar.php
$user = $user ?? null;
?>
<aside class="sidebar">
    <div class="profile-card">
        <h3>About ForumX</h3>
        <p>A minimal writing platform for sharing ideas. Create an account and start publishing.</p>
        <?php if (!$user): ?>
            <p><a class="button" href="/forumx/pages/register.php">Join ForumX</a></p>
        <?php else: ?>
            <p><a class="button" href="/forumx/pages/create_blog.php">Write a story</a></p>
        <?php endif; ?>
    </div>
</aside>
