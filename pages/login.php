<?php
// pages/login.php
session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if logged in
if (is_logged_in()) {
    header('Location: ../pages/home.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($posted_token)) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors[] = "Email and password are required.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: ../pages/home.php');
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }
}
?>

<link rel="stylesheet" href="../assets/css/login.css">
<link rel="icon" type="image/png" href="../assets/images/favicon.png">
<header class="login-header">
  <div class="logo">ForumX</div>
  <a href="../pages/about.php" class="about-btn">About us</a>
</header>

<main class="login-container">
  <h1 class="title">Login to <span>ForumX</span></h1>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <ul>
        <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="" method="POST" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" class="submit-btn">Login</button>
  </form>

  <p class="register-text">Don’t have an account? <a href="../pages/register.php">Register here</a></p>

  <img src="../assets/images/pen.png" alt="Pen" class="pen-img">
</main>

<footer class="footer">
  © 2025 ForumX — A community to share ideas.
</footer>

<script>
document.querySelector('form').addEventListener('submit', e => {
  const email = e.target.email.value.trim();
  const password = e.target.password.value.trim();
  if (!email || !password) {
    alert('Please fill in both fields.');
    e.preventDefault();
  }
});
</script>
