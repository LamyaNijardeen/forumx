<?php
session_start();
include __DIR__ . '/../includes/header.php';
$registered = isset($_GET['registered']) ? true : false;
?>
<main style="max-width:700px;margin:1.5rem auto;padding:1rem;">
  <h1>Login — ForumX</h1>
  <?php if ($registered): ?>
    <div style="background:#e6ffea;border:1px solid #b3f0c9;padding:12px;margin-bottom:12px;">Registration successful. Please login.</div>
  <?php endif; ?>
  <p>(Login form will be implemented tomorrow.)</p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
