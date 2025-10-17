<?php
// pages/create_blog.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        // Validate inputs
        $title = trim((string)($_POST['title'] ?? ''));
        $content = trim((string)($_POST['content'] ?? ''));

        if ($title === '') $errors[] = 'Title is required.';
        if ($content === '') $errors[] = 'Content is required.';

        // Image upload (optional)
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $fileType = $_FILES['image']['type'] ?? '';
            if (!array_key_exists($fileType, $allowed)) {
                $errors[] = 'Only JPG, PNG, GIF are allowed for images.';
            } else {
                $ext = $allowed[$fileType];
                $filename = uniqid('img_', true) . '.' . $ext;
                $targetDir = __DIR__ . '/../assets/images/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $targetPath = $targetDir . $filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $errors[] = 'Failed to move uploaded image.';
                } else {
                    $image_path = $filename;
                }
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isss', $user['id'], $title, $content, $image_path);
            if ($stmt->execute()) {
                $success = 'Post created successfully.';
                // redirect to the post view
                $newId = $stmt->insert_id;
                $stmt->close();
                header('Location: /forumx/pages/view_blog.php?id=' . (int)$newId);
                exit;
            } else {
                $errors[] = 'DB error: ' . htmlspecialchars($conn->error);
            }
            $stmt->close();
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout">
  <div class="main">
    <h1>Create Post</h1>

    <?php if ($errors): ?>
      <div class="errors">
        <?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

      <label>Title</label><br>
      <input type="text" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required style="width:100%;padding:8px;margin-bottom:8px;">

      <label>Content</label><br>
      <textarea name="content" rows="10" required style="width:100%;padding:8px;margin-bottom:8px;"><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>

      <label>Image (optional)</label><br>
      <input type="file" name="image" accept="image/*"><br><br>

      <button type="submit">Publish</button>
    </form>
  </div>

  <aside class="sidebar">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
  </aside>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
