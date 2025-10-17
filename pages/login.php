<?php
// pages/login.php
session_start();

require_once '../includes/config.php';  // DB connection
require_once '../includes/csrf.php';     // CSRF token functions
require_once '../includes/auth.php';     // Authentication helpers (is_logged_in, current_user)

// If already logged in, redirect to home
if (is_logged_in()) {
    header('Location: home.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($posted_token)) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required.";
        } else {
            // Fetch user by email
            $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: home.php');
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="auth-container">
    <h2>Login to ForumX</h2>

    <?php 
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo '<p style="color:red;">' . htmlspecialchars($e) . '</p>';
        }
    }
    ?>

    <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include '../includes/footer.php'; ?>
