<?php
// pages/create_blog.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token.";
    } else {

        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $errors[] = "Title and content are required.";
        }

        // Handle image upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $errors[] = "Only JPG, PNG, GIF files are allowed.";
            } else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $target = __DIR__ . '/../assets/images/' . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $image_path = $filename;
                } else {
                    $errors[] = "Failed to upload image.";
                }
            }
        }

        // Insert into DB if no errors
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user['id'], $title, $content, $image_path);
            if ($stmt->execute()) {
                header("Location: /forumx/pages/home.php?success=1");
                exit;
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
        }
    }
}

?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php //include __DIR__ . '/../includes/navbar.php'; ?>

<div class="layout">
  <div class="main">
    <h1>Create a New Blog</h1>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

      <label>Title:</label>
      <input type="text" name="title" required><br><br>

      <label>Content:</label>
      <textarea name="content" rows="10" required></textarea><br><br>

      <label>Upload Image (optional):</label>
      <input type="file" name="image" accept="image/*"><br><br>

      <button type="submit">Publish</button><br><br>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
