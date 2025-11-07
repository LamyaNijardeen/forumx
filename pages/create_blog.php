<?php
// REQUIRED FILES 
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// ACCESS CONTROL 
require_login();
$user = current_user();

//  INITIALIZE VARIABLES 
$errors = [];
$success = '';

// FORM SUBMISSION HANDLING 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //  CSRF Token Validation 
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        //  Form Data Sanitization 
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        //  Basic Validation 
        if ($title === '') $errors[] = 'Title is required.';
        if ($content === '') $errors[] = 'Content is required.';

        //  IMAGE UPLOAD HANDLING 
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $fileType = $_FILES['image']['type'] ?? '';

            // Check valid file type
            if (!array_key_exists($fileType, $allowed)) {
                $errors[] = 'Only JPG, PNG, GIF are allowed.';
            } else {
                $ext = $allowed[$fileType];
                $filename = uniqid('img_', true) . '.' . $ext;
                $targetDir = __DIR__ . '/../assets/images/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $targetPath = $targetDir . $filename;

                // Move file to uploads folder
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $errors[] = 'Failed to upload image.';
                } else {
                    $image_path = $filename;
                }
            }
        }

        //  DATABASE INSERTION 
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO blogpost (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isss', $user['id'], $title, $content, $image_path);

            // On success — redirect to the new blog page
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                $stmt->close();
                header('Location: ../pages/view_blog.php?id=' . (int)$newId);
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/create_blog.css">
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

  <!-- Hamburger Menu Icon -->
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</header>

<!-- MOBILE MENU-->
<div class="mobile-menu" id="mobileMenu">
  <a href="../pages/home.php">Home</a>
  <a href="../pages/about.php">About us</a>
  <a href="../pages/create_blog.php" class="active">Write</a>
  <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>">My Profile</a>
  <a href="../pages/logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<main class="create-container">
  <div class="layout">
    <div class="form-section">
      <h2>Create Blog</h2>

      <!-- Error Messages -->
      <?php if ($errors): ?>
        <div class="error-box">
          <?php foreach ($errors as $e): ?>
            <p><?php echo htmlspecialchars($e); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Blog Creation Form -->
      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

        <label for="content">Content</label>
        <textarea name="content" id="content" rows="10" required></textarea>

        <label for="image">Image <span>(optional)</span></label>
        <input type="file" name="image" id="image" accept="image/*"><br>

        <button type="submit" class="publish-btn">Publish</button>
      </form>
    </div>

    <!-- Sidebar Info -->
    <aside class="sidebar">
      <img src="../assets/images/pen.png" alt="Pen icon">
      <p>A minimal writing platform for sharing ideas</p>
      <p>Create an account and start publishing</p>
      <p>Search your interest by topics</p>
      <p>Learn together with the bright minds</p>
      <p>Share your knowledge — we are here to see</p>
      <br>
      <p><strong>Start Today</strong></p>
      <a href="create_blog.php">Write a story</a>
    </aside>
  </div>
</main>

<!--FOOTER -->
<footer>© 2025 ForumX — A community to share ideas.</footer>

<!--  SCRIPT  -->
<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    hamburger.classList.toggle('open');
  });
</script>

</body>
</html>
