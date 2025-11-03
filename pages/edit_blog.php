<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();
$errors = [];

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    exit('Invalid id');
}

$stmt = $conn->prepare("SELECT * FROM blogpost WHERE id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    exit('Post not found');
}

if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    exit('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $image_path = $post['image_path'];

        if ($title === '') $errors[] = 'Title required.';
        if ($content === '') $errors[] = 'Content required.';

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $fileType = $_FILES['image']['type'] ?? '';
            if (!array_key_exists($fileType, $allowed)) {
                $errors[] = 'Only JPG, PNG, GIF allowed.';
            } else {
                $ext = $allowed[$fileType];
                $filename = uniqid('img_', true) . '.' . $ext;
                $targetDir = __DIR__ . '/../assets/images/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $targetPath = $targetDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    if (!empty($post['image_path']) && file_exists($targetDir . $post['image_path'])) {
                        @unlink($targetDir . $post['image_path']);
                    }
                    $image_path = $filename;
                } else {
                    $errors[] = 'Failed to upload new image.';
                }
            }
        }

        if (empty($errors)) {
            $upd = $conn->prepare("UPDATE blogpost SET title=?, content=?, image_path=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
            $upd->bind_param('sssi', $title, $content, $image_path, $post_id);
            if ($upd->execute()) {
                header('Location: ../pages/view_blog.php?id=' . (int)$post_id);
                exit;
            } else {
                $errors[] = 'DB error: ' . htmlspecialchars($conn->error);
            }
            $upd->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ForumX | Create Blog</title>
<link rel="stylesheet" href="../assets/css/edit_blog.css">
<link rel="icon" type="image/png" href="../assets/images/favicon.png">
</head>
<body>
  <!-- HEADER -->
  <header>
    <div class="left-section">
      <div class="logo">ForumX</div>
      <nav>
        <a href="../pages/home.php">Home</a>
        <a href="../pages/about.php">About us</a>
        <a href="../pages/create_blog.php" class="active">Write</a>
        <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
      </nav>
    </div>

    <div class="user-info">
      Hello, <?php echo htmlspecialchars($user['username']); ?>
      <div class="separator"></div>
      <a class="logout-btn" href="../pages/logout.php">Logout</a>
    </div>
  </header>
<div class="edit-container">
    <h1>Edit Blog...</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']); ?>">

        <label for="content">Content</label>
        <textarea id="content" name="content" rows="6"><?= htmlspecialchars($post['content']); ?></textarea>

        <?php if (!empty($post['image_path'])): ?>
            <p>Current image:</p>
            <img src="../assets/images/<?= htmlspecialchars($post['image_path']); ?>" alt="Current image" class="current-img">
        <?php endif; ?>

        <label for="image">Replace image (optional)</label>
        <input type="file" id="image" name="image" accept="image/*">

    <br><button type="submit" class="update-btn">Update</button>
    </form>
</div>

  <!-- FOOTER -->
  <footer>© 2025 ForumX — A community to share ideas.</footer>
</body>
</html>
