<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About ForumX</title>
  <link rel="stylesheet" href="../assets/css/about.css">
</head>
<body>

  <header class="about-header">
    <div class="logo">ForumX</div>
    <nav class="nav-links">
      <a href="about.php" class="nav-btn active">About us</a>
      <a href="create_blog.php" class="nav-btn">Write</a>
      <a href="register.php" class="nav-btn filled get-started">Get Started</a>
    </nav>
  </header>

  <main class="about-container">
    <section class="about-content">
      <h1>About ForumX</h1>
      <p>
        ForumX is a simple blogging platform to share stories, tutorials, ideas, 
        and experiences with a friendly community.
      </p><br>

      <h3>What you can do</h3>
      <ul>
        <li>Create, edit, and delete your blog posts</li>
        <li>Upload images to enhance your posts</li>
        <li>View posts from others and browse by topic</li>
      </ul><br>

      <a href="create_blog.php" class="create-post-link">Create your first post</a>
    </section>

    <img src="../assets/images/pen.png" alt="Pen" class="pen-img">
  </main>

  <footer class="footer">
    © 2025 ForumX — A community to share ideas.
  </footer>

</body>
</html>
