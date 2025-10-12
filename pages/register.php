<?php
// pages/register.php
session_start();
require_once __DIR__ . '/../includes/config.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: /forumx/pages/home.php');
    exit;
}

// --- CSRF helpers ---
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Initialize variables
$errors = [];
$old = ['username'=>'', 'email'=>''];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    $posted_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $posted_csrf)) {
        $errors[] = "Invalid request (CSRF token mismatch). Please refresh the page and try again.";
    }

    // Gather and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $old['username'] = htmlspecialchars($username);
    $old['email'] = htmlspecialchars($email);

    // Basic validations
    if ($username === '' || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }

    // If no validation errors so far, check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM `user` WHERE email = ?");
        if (!$stmt) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "An account with that email already exists.";
            }
            $stmt->close();
        }
    }

    // Insert user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $ins = $conn->prepare("INSERT INTO `user` (username, email, password, role) VALUES (?, ?, ?, 'user')");
        if (!$ins) {
            $errors[] = "Database error (insert): " . $conn->error;
        } else {
            $ins->bind_param('sss', $username, $email, $hashed);
            if ($ins->execute()) {
                // registration success - regenerate CSRF, optionally auto-login or redirect to login
                unset($_SESSION['csrf_token']);
                // Redirect to login with success message
                header('Location: /forumx/pages/login.php?registered=1');
                exit;
            } else {
                $errors[] = "Failed to create account. Please try again later.";
            }
            $ins->close();
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width:800px;margin:1.5rem auto;padding:1rem;">
  <h1>Register — ForumX</h1>

  <?php if (!empty($errors)): ?>
    <div style="background:#ffe6e6;border:1px solid #ffb3b3;padding:12px;margin-bottom:12px;">
      <ul style="margin:0;padding-left:18px">
        <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form id="registerForm" action="" method="POST" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <label>Username<br>
      <input type="text" name="username" value="<?php echo $old['username']; ?>" required minlength="3" style="width:100%;padding:8px;margin-top:6px;">
    </label>
    <br><br>

    <label>Email<br>
      <input type="email" name="email" value="<?php echo $old['email']; ?>" required style="width:100%;padding:8px;margin-top:6px;">
    </label>
    <br><br>

    <label>Password<br>
      <input type="password" name="password" required minlength="6" style="width:100%;padding:8px;margin-top:6px;">
    </label>
    <br><br>

    <label>Confirm Password<br>
      <input type="password" name="password_confirm" required minlength="6" style="width:100%;padding:8px;margin-top:6px;">
    </label>
    <br><br>

    <button type="submit" style="padding:10px 16px;">Create account</button>
  </form>

  <p style="margin-top:12px;">Already have an account? <a href="/forumx/pages/login.php">Login here</a>.</p>
</main>

<script>
// Small client-side validation to improve UX
document.getElementById('registerForm').addEventListener('submit', function(e){
  const pw = this.password.value;
  const pw2 = this.password_confirm.value;
  if (pw.length < 6) {
    alert('Password must be at least 6 characters.');
    e.preventDefault();
    return;
  }
  if (pw !== pw2) {
    alert('Passwords do not match.');
    e.preventDefault();
    return;
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
