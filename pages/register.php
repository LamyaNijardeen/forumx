<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Redirect if logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/home.php');
    exit;
}

// CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$errors = [];
$old = ['username'=>'', 'email'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $posted_csrf)) {
        $errors[] = "Invalid request. Please refresh the page.";
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $old['username'] = htmlspecialchars($username);
    $old['email'] = htmlspecialchars($email);

    if ($username === '' || strlen($username) < 3)
        $errors[] = "Username must be at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Please enter a valid email.";
    if (strlen($password) < 6)
        $errors[] = "Password must be at least 6 characters.";
    if ($password !== $password_confirm)
        $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM `user` WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = "Email already registered.";
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $ins = $conn->prepare("INSERT INTO `user` (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $ins->bind_param("sss", $username, $email, $hashed);
        if ($ins->execute()) {
            unset($_SESSION['csrf_token']);
            header('Location: ../pages/login.php?registered=1');
            exit;
        } else $errors[] = "Something went wrong. Try again later.";
        $ins->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — ForumX</title>
<link rel="stylesheet" href="../assets/css/register.css">
<link rel="icon" type="image/png" href="../assets/images/favicon.png">
</head>
<body>

<header class="register-header">
  <h1 class="logo">ForumX</h1>
</header>

<main class="register-container">
  <h2 class="title">Register— <span>ForumX</span></h2>

  <?php if (!empty($errors)): ?>
  <div class="error-box">
    <ul>
      <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
    </ul>
  </div>
  <?php endif; ?>
  <form action="" method="POST" id="registerForm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <label>Username</label>
    <input type="text" name="username" value="<?= $old['username'] ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= $old['email'] ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm Password</label>
    <input type="password" name="password_confirm" required>

    <button type="submit" class="submit-btn">Create account</button>
  </form>

  <p class="login-text">Already have an account? <a href="../pages/login.php">Login here.</a></p>

  <img src="../assets/images/pen.png" alt="Pen" class="pen-img">
</main>

<footer class="footer">
  <p>© 2025 ForumX — A community to share ideas.</p>
</footer>

<script>
document.getElementById('registerForm').addEventListener('submit', e => {
  const pw = e.target.password.value, pw2 = e.target.password_confirm.value;
  if (pw.length < 6) { alert('Password must be at least 6 characters.'); e.preventDefault(); }
  else if (pw !== pw2) { alert('Passwords do not match.'); e.preventDefault(); }
});
</script>

</body>
</html>
