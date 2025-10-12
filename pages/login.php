<?php
// pages/login.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to home
if (is_logged_in()) {
    header('Location: /forumx/pages/home.php');
    exit;
}

$errors = [];
$old = ['email' => ''];

// handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = htmlspecialchars($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }
    if ($password === '') {
        $errors[] = "Enter your password.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, password FROM `user` WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row && password_verify($password, $row['password'])) {
            // successful login
            session_regenerate_id(true); // prevent session fixation
            $_SESSION['user_id'] = $row['id'];
            // clear cached current_user if any
            unset($_SESSION['current_user']);

            // redirect to next if provided (safe redirect)
            $next = '/forumx/pages/home.php';
            if (!empty($_GET['next'])) {
                $u = $_GET['next'];
                // defend against open redirect: only allow paths on same host starting with /forumx
                if (strpos($u, '/forumx') === 0) $next = $u;
            }
            header('Location: ' . $next);
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>
<main style="max-width:600px;margin:1.5rem auto;padding:1rem;">
  <h1>Login — ForumX</h1>

  <?php if (!empty($errors)): ?>
    <div style="background:#ffe6e6;padding:10px;border:1px solid #ffb3b3;">
      <ul style="margin:0;padding-left:18px;"><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
    </div>
  <?php endif; ?>

  <form action="" method="POST" style="margin-top:12px;">
    <label>Email<br>
      <input type="email" name="email" value="<?php echo $old['email']; ?>" required style="width:100%;padding:8px;margin-top:6px;">
    </label><br><br>

    <label>Password<br>
      <input type="password" name="password" required style="width:100%;padding:8px;margin-top:6px;">
    </label><br><br>

    <button type="submit" style="padding:10px 16px;">Login</button>
  </form>
  <p style="margin-top:12px;">Don't have an account? <a href="/forumx/pages/register.php">Register</a></p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
