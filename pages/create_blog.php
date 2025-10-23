<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '') $errors[] = 'Title is required.';
        if ($content === '') $errors[] = 'Content is required.';

        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $fileType = $_FILES['image']['type'] ?? '';
            if (!array_key_exists($fileType, $allowed)) {
                $errors[] = 'Only JPG, PNG, GIF are allowed.';
            } else {
                $ext = $allowed[$fileType];
                $filename = uniqid('img_', true) . '.' . $ext;
                $targetDir = __DIR__ . '/../assets/images/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $targetPath = $targetDir . $filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $errors[] = 'Failed to upload image.';
                } else {
                    $image_path = $filename;
                }
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isss', $user['id'], $title, $content, $image_path);
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                $stmt->close();
                header('Location: /forumx/pages/view_blog.php?id=' . (int)$newId);
                exit;
            } else {
                $errors[] = 'Database error: ' . htmlspecialchars($conn->error);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ForumX | Create Blog</title>
  <link rel="stylesheet" href="/forumx/assets/css/create_blog.css">
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="left-section">
      <div class="logo">ForumX</div>
      <nav>
        <a href="home.php">Home</a>
        <a href="about.php">About us</a>
        <a href="create_blog.php" class="active">Write</a>
        <a href="profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
      </nav>
    </div>

    <div class="user-info">
      Hello, <?php echo htmlspecialchars($user['username']); ?>
      <div class="separator"></div>
      <a class="logout-btn" href="logout.php">Logout</a>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main>
    <div class="layout">
    <div class="form-section">
      <h2>Create Blog...</h2>

      <?php if ($errors): ?>
        <div class="error-box">
          <?php foreach ($errors as $e): ?>
            <p><?php echo htmlspecialchars($e); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

        <label for="content">Content</label>
        <textarea name="content" id="content" rows="10" required></textarea>

        <label for="image">Image <span>(optional)</span></label>
        <input type="file" name="image" id="image" accept="image/*">

        <button type="submit" class="publish-btn">Publish</button>
      </form>
    </div>

    <aside class="sidebar">
      <img src="/forumx/assets/images/pen.png" alt="Pen icon">
      <p>A minimal writing platform for sharing ideas</p>
      <p>Create an account and start publishing</p>
      <p>Search your interest by topics</p>
      <p>Learn together with the bright minds</p>
      <p>Share your knowledge — we are here to see</p>
      <br>
      <p><strong>Start Today</strong></p>
      <a href="create_blog.php">Write a story</a>
      <p style="margin-top:1rem; font-size:0.85rem; color:#888;">© 2025 ForumX</p>
    </aside>
          </div>
  </main>

  <!-- FOOTER -->
  <footer>© 2025 ForumX — A community to share ideas.</footer>

</body>
</html>
