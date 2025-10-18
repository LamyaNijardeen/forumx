<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();

// if logged in, skip entry and go to home
if ($user) {
    header('Location: home.php');
    exit;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="entry-hero">
  <div class="entry-inner">
    <h1>Welcome to ForumX</h1>
    <p>Join a growing community of thinkers, writers, and learners.</p>

    <div class="entry-cta">
      <a class="button" href="/forumx/pages/register.php">Get Started — Create an account</a>
      <a class="ghost" href="/forumx/pages/login.php">Sign in</a>
    </div>
  </div>
</section>

<section class="entry-features fx-container">
  <div class="feature">
    <h3>Publish ideas</h3>
    <p>Turn your thoughts into stories and share them with others.</p>
  </div>
  <div class="feature">
    <h3>Engage & Learn</h3>
    <p>Discover new perspectives and insights from other creators.</p>
  </div>
  <div class="feature">
    <h3>Grow Together</h3>
    <p>Build your writing journey with a like-minded community.</p>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
