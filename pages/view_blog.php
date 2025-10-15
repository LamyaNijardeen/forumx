<?php
// pages/view_blog.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    echo "Invalid post ID.";
    exit;
}

// Fetch blog post
$stmt = $conn->prepare("SELECT b.*, u.username FROM blogPost b JOIN user u ON b.user_id = u.id WHERE b.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    echo "Blog not found.";
    exit;
}

$user = current_user();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($post['title']); ?></title>
<link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<p>By <?php echo htmlspecialchars($post['username']); ?> | Created at: <?php echo $post['created_at']; ?></p>
<div>
    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
</div>

<?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
    <p>
        <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>">Edit</a> |
        <form action="/forumx/pages/delete_post.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <button type="submit">Delete</button>
        </form>
    </p>
<?php endif; ?>

<p><a href="/forumx/pages/home.php">Back to Home</a></p>
</body>
</html>
