<?php
// includes/sidebar.php
$user = $user ?? null;
?>
<aside class="sidebar">
    <div class="profile-card">
        <h3>About ForumX</h3>
        <p>A minimal writing platform for sharing ideas. Create an account and start publishing.</p>
        <?php if (!$user): ?>
            <p><a class="button" href="/pages/register.php">Join ForumX</a></p>
        <?php else: ?>
            <p><a class="button" href="/pages/create_blog.php">Write a story</a></p>
        <?php endif; ?>
    </div>
        <!--DB_HOST=sql310.infinityfree.com
DB_USER=if0_40133767
DB_PASS=_*93yMs0
DB_NAME=if0_40133767_forumx_database-->
</aside>
