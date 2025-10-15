<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';

//session_start();
$user = current_user(); // current logged-in user

// Get post id from query string
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    echo "Invalid blog ID.";
    exit;
}

// Fetch blog post with author info
$stmt = $conn->prepare("
    SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, blogPost.updated_at, blogPost.user_id, user.username
    FROM blogPost
    JOIN user ON blogPost.user_id = user.id
    WHERE blogPost.id = ?
");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    echo "Blog post not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> - ForumX</title>
    <link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="blog-full">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p>By <?php echo htmlspecialchars($post['username']); ?> | Created at: <?php echo $post['created_at']; ?>
            <?php if ($post['updated_at'] != $post['created_at']) echo " | Updated: " . $post['updated_at']; ?>
        </p>
        <div class="blog-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
            <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>">Edit</a>
            <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
                <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <button type="submit">Delete</button>
            </form>
        <?php endif; ?>

        <p><a href="/forumx/pages/home.php">Back to Home</a></p>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
