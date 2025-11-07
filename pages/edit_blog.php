<?php
//  INCLUDE REQUIRED FILES 
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// ENSURE USER IS LOGGED IN
if (!isset($_SESSION['user_id'])) {
  header("Location: ../pages/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user = current_user($conn, $user_id);

// VALIDATE BLOG ID 
if (!isset($_GET['id'])) {
  header("Location: ../pages/home.php");
  exit;
}

$blog_id = (int)$_GET['id'];

// FUNCTION: FETCH BLOG BY ID 
function getBlogById($conn, $id) {
  $stmt = $conn->prepare("SELECT * FROM blogpost WHERE id = ?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $blog = $res->fetch_assoc();
  $stmt->close();
  return $blog ?: null;
}

// FETCH SELECTED BLOG 
$blog = getBlogById($conn, $blog_id);

// VERIFY PERMISSION (USER OWNS POST)
if (!$blog || $blog['user_id'] != $user_id) {
  header("Location: ../pages/home.php");
  exit;
}

// HANDLE POST REQUEST (FORM SUBMIT)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);

  //  VALIDATE INPUT 
  if (!empty($title) && !empty($content)) {
    //  UPDATE BLOG ENTRY 
    $stmt = $conn->prepare("UPDATE blogpost SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $blog_id);
    $stmt->execute();
    $stmt->close();

    //  REDIRECT AFTER SUCCESS 
    header("Location: ../pages/profile.php?user_id=" . $user_id);
    exit;
  } else {
    $error = "Title and content cannot be empty.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Blog - ForumX</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--CSS & FAVICON -->
  <link rel="stylesheet" href="../assets/css/edit_blog.css">
  <link rel="icon" type="image/png" href="../assets/images/favicon.png">
</head>
<body>

<!-- HEADER & NAVIGATION  -->
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

  <!-- HAMBURGER MENU (MOBILE) -->
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</header>

<!--  MOBILE NAVIGATION MENU  -->
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
      <h2>Edit Blog</h2>

      <!--  ERROR MESSAGE -->
      <?php if (isset($error)): ?>
        <div class="error-box">
          <p><?php echo htmlspecialchars($error); ?></p>
        </div>
      <?php endif; ?>

      <!--  EDIT FORM  -->
      <form method="POST">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>

        <label for="content">Content</label>
        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($blog['content']); ?></textarea>

        <button type="submit" class="publish-btn">Save Changes</button>
      </form>
    </div>
  </div>
</main>

<!--  FOOTER -->
<footer>© 2025 ForumX — A community to share ideas.</footer>

<!-- JS FOR HAMBURGER MENU -->
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
