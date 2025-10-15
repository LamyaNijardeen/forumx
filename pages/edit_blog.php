<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/config.php';

require_login();
$user = current_user();

// Get post ID from GET
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) { 
    http_response_code(400);
    echo "Invalid post ID.";
    exit;
}

// Fetch post
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) { 
    http_response_code(404);
    echo "Post not found.";
    exit;
}

// Ownership check
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    echo "You are not authorized to edit this post.";
    exit;
}

// Initialize error array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title)) $errors[] = "Title cannot be empty.";
        if (empty($content)) $errors[] = "Content cannot be empty.";

        if (empty($errors)) {
            $upd = $conn->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ?");
            $upd->bind_param('ssi', $title, $content, $post_id);
            if ($upd->execute()) {
                header("Location: view_blog.php?id=$post_id&updated=1");
                exit;
            } else {
                $errors[] = "Failed to update the post.";
            }
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Edit Blog Post</h2>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
    </div>
<?php endif; ?>

<form action="edit_blog.php?id=<?php echo $post_id; ?>" method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <button type="submit">Update Blog</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
