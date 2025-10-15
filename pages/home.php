<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
//session_start();
$user = current_user(); // returns logged-in user info or null

// Fetch all blogs with author info
$query = "
    SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, user.username, blogPost.user_id
    FROM blogPost
    JOIN user ON blogPost.user_id = user.id
    ORDER BY blogPost.created_at DESC
";
$result = $conn->query($query);
$blogs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ForumX - Home</title>
    <link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <h1>All Blogs</h1>

    <?php if (empty($blogs)): ?>
        <p>No blogs found.</p>
    <?php else: ?>
        <div class="blog-list">
            <?php foreach ($blogs as $post): ?>
                <div class="blog-card">
                    <h2><a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </a></h2>
                    <p>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></p>
                    <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>...</p>
                    <a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>">Read more</a>

                    <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
                        <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>">Edit</a>
                        <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
                            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
