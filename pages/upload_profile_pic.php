<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
if (!$user) {
  header("Location: login.php");
  exit;
}

// CSRF validation
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
  die("CSRF token invalid");
}

// Check upload
if (!empty($_FILES['profile_image']['name'])) {
  $file = $_FILES['profile_image'];
  $allowed = ['jpg', 'jpeg', 'png', 'gif'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

  if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
    $newName = 'profile_' . $user['id'] . '_' . time() . '.' . $ext;
    $target = __DIR__ . '/../assets/profile_photos/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $target)) {
      $stmt = $conn->prepare("UPDATE user SET profile_photo = ? WHERE id = ?");
      $stmt->bind_param("si", $newName, $user['id']);
      $stmt->execute();
      $stmt->close();
    }
  }
}

header("Location: profile.php?user_id=" . $user['id']);
exit;
?>
