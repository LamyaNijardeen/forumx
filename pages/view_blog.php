<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
$post_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT b.id, b.title, b.content, b.created_at, b.image_path, u.id AS user_id, u.username 
                        FROM blogPost b 
                        JOIN user u ON b.user_id = u.id 
                        WHERE b.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "Post not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ForumX | View Blog</title>
    <link rel="stylesheet" href="/forumx/assets/css/view_blog.css">
</head>
<body>

    <!-- NAVBAR -->
<header>
  <div class="left-section">
    <div class="logo">ForumX</div>
    <nav>
      <a href="home.php">Home</a>
      <a href="about.php">About us</a>
      <a href="create_blog.php">Write</a>
    </nav>
  </div>

  <div class="user-info">
    Hello, <?php echo htmlspecialchars($user['username']); ?>
    <div class="separator"></div>
    <a class="logout-btn" href="logout.php">Logout</a>
  </div>
</header>

    <!-- MAIN BLOG CONTENT -->
<div class="view-container">
    <div class="post-card">
        <?php if (!empty($post['image_path'])): ?>
            <div class="post-image">
                <img src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image">
            </div>
        <?php endif; ?> <!-- 👈 Removed the placeholder completely -->

        <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>

        <div class="post-meta">
            By <span class="author"><?php echo htmlspecialchars($post['username']); ?></span>
            <span class="dot">|</span>
            <span class="date"><?php echo htmlspecialchars(date("F j, Y", strtotime($post['created_at']))); ?></span>
        </div>

        <p class="post-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

        <?php if ($user && ($user['id'] == $post['user_id'] || is_admin())): ?>
            <div class="post-actions">
                <a href="/forumx/pages/edit_blog.php?id=<?php echo $post['id']; ?>" class="edit-btn">Edit</a>
                <form action="/forumx/pages/delete_post.php" method="POST" class="delete-form" onsubmit="return confirm('Delete this post?');">
                    <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

    <!-- FOOTER -->
<footer>
  © 2025 ForumX — A community to share ideas.
</footer>

</body>
</html>
