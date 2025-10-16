<?php
// pages/about.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section style="padding:18px;background:#fff;border-radius:8px;border:1px solid #eee;">
  <h1>About ForumX</h1>
  <p>ForumX is a simple, clean blogging platform inspired by Medium. It’s a place to share stories, tutorials, ideas, and experiences with a friendly community.</p>

  <h3>What you can do</h3>
  <ul>
    <li>Create, edit, and delete your blog posts</li>
    <li>Upload images to enhance your posts</li>
    <li>View posts from others and browse by author</li>
  </ul>

  <h3>About the Developer</h3>
  <p>ForumX was built as a learning project to practice full-stack web development using PHP, MySQL and frontend technologies.</p>

  <p><a class="button" href="/forumx/pages/create_blog.php">Create your first post</a></p>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
