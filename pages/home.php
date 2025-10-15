<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
//session_start();
$user = current_user(); // returns logged-in user info or null

// fetch all blogs with author info
$query = "SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, user.username 
          FROM blogPost
          JOIN user ON blogPost.user_id = user.id
          ORDER BY blogPost.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - ForumX Blogs</title>
</head>
<body>
<h1>ForumX Blogs</h1>

<?php if ($user): ?>
    <p>Welcome, <?php echo htmlspecialchars($user['username']); ?>! <a href="/forumx/pages/create_blog.php">Create Blog</a> | <a href="/forumx/pages/logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="/forumx/pages/login.php">Login</a> | <a href="/forumx/pages/register.php">Register</a></p>
<?php endif; ?>

<hr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><a href="/forumx/pages/view_blog.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
            <p>By <strong><?php echo htmlspecialchars($row['username']); ?></strong> on <?php echo $row['created_at']; ?></p>
            <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 200))); ?>...</p>
            <a href="/forumx/pages/view_blog.php?id=<?php echo $row['id']; ?>">Read More</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No blogs yet. <?php if ($user) echo 'Be the first to <a href="/forumx/pages/create_blog.php">create one</a>!'; ?></p>
<?php endif; ?>
</body>
</html>
