<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';

//session_start();
require_login(); // redirect if not logged in
$user = current_user(); // current user info

// Get post id
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    echo "Invalid blog ID.";
    exit;
}

// Fetch blog post
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
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

// Only author or admin can edit
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    echo "You are not authorized to edit this post.";
    exit;
}

// Handle POST update
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($token)) {
        $errors[] = "Invalid request.";
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $errors[] = "Title and content cannot be empty.";
        } else {
            $update = $conn->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ?");
            $update->bind_param('ssi', $title, $content, $post_id);
            if ($update->execute()) {
                header("Location: /forumx/pages/view_blog.php?id=" . $post_id);
                exit;
            } else {
                $errors[] = "Failed to update blog post.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog - ForumX</title>
    <link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="edit-blog-form">
        <h1>Edit Blog Post</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err) echo "<p>" . htmlspecialchars($err) . "</p>"; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <button type="submit">Update Blog</button>
        </form>

        <p><a href="/forumx/pages/view_blog.php?id=<?php echo $post['id']; ?>">Back to Blog</a></p>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
