<?php
require_once __DIR__ . '/../includes/auth.php'; // use your existing auth functions
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login(); // redirect to login if not logged in
$user = current_user(); // current logged-in user
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid request.";
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $errors[] = "Title and Content are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->bind_param('iss', $user['id'], $title, $content);

            if ($stmt->execute()) {
                $success = "Blog created successfully!";
            } else {
                $errors[] = "Failed to create blog.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Blog</title>
</head>
<body>
<h2>Create a New Blog</h2>

<?php
if ($errors) {
    foreach ($errors as $err) {
        echo "<p style='color:red;'>$err</p>";
    }
}
if ($success) {
    echo "<p style='color:green;'>$success</p>";
}
?>

<form action="" method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="10" cols="70" required></textarea><br><br>

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <button type="submit">Publish Blog</button>
</form>

<p><a href="/forumx/pages/home.php">Back to Home</a></p>
</body>
</html>
