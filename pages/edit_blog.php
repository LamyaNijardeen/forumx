<?php
// pages/edit_blog.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
$user = current_user();
$errors = array();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    http_response_code(400);
    exit('Invalid id');
}

// fetch post
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ?");
if ($stmt === false) {
    exit('DB prepare error: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (! $post) {
    http_response_code(404);
    exit('Post not found');
}

// ownership check
if ($post['user_id'] != $user['id'] && !is_admin()) {
    http_response_code(403);
    exit('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (! validate_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $title = trim((string)($_POST['title'] ?? ''));
        $content = trim((string)($_POST['content'] ?? ''));

        if ($title === '') {
            $errors[] = 'Title required.';
        }
        if ($content === '') {
            $errors[] = 'Content required.';
        }

        // optional new image upload -> replace previous image_path if uploaded
        $image_path = $post['image_path'] ?? null;
        if (! empty($_FILES['image']['name'])) {
            $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif');
            $fileType = $_FILES['image']['type'] ?? '';
            if (! array_key_exists($fileType, $allowed)) {
                $errors[] = 'Only JPG, PNG, GIF allowed.';
            } else {
                $ext = $allowed[$fileType];
                $filename = uniqid('img_', true) . '.' . $ext;
                $targetDir = __DIR__ . '/../assets/images/';
                if (! is_dir($targetDir)) {
                    if (! mkdir($targetDir, 0755, true)) {
                        $errors[] = 'Failed to create image directory.';
                    }
                }
                if (empty($errors)) {
                    $targetPath = $targetDir . $filename;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        // delete old image if exists
                        if (! empty($post['image_path']) && file_exists($targetDir . $post['image_path'])) {
                            @unlink($targetDir . $post['image_path']);
                        }
                        $image_path = $filename;
                    } else {
                        $errors[] = 'Failed to upload new image.';
                    }
                }
            }
        }

        if (empty($errors)) {
            $upd = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, image_path = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            if ($upd === false) {
                $errors[] = 'DB prepare error: ' . htmlspecialchars($conn->error);
            } else {
                $upd->bind_param('sssi', $title, $content, $image_path, $post_id);
                if ($upd->execute()) {
                    $upd->close();
                    header('Location: /forumx/pages/view_blog.php?id=' . (int)$post_id);
                    exit;
                } else {
                    $errors[] = 'DB error: ' . htmlspecialchars($conn->error);
                    $upd->close();
                }
            }
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="layout">
  <div class="main">
    <h1>Edit Post</h1>

    <?php
    if (! empty($errors)) {
        echo '<div class="errors">';
        foreach ($errors as $e) {
            echo '<p>' . htmlspecialchars($e) . '</p>';
        }
        echo '</div>';
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

      <label for="title">Title</label><br>
      <input id="title" type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required style="width:100%;padding:8px;margin-bottom:8px;">

      <label for="content">Content</label><br>
      <textarea id="content" name="content" rows="10" required style="width:100%;padding:8px;margin-bottom:8px;"><?php echo htmlspecialchars($post['content']); ?></textarea>

      <?php if (! empty($post['image_path'])) { ?>
        <p>Current image:</p>
        <img src="/forumx/assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="" style="max-width:200px;border-radius:6px;">
      <?php } ?>

      <label for="image">Replace image (optional)</label><br>
      <input id="image" type="file" name="image" accept="image/*"><br><br>

      <button type="submit">Update</button>
    </form>
  </div>

  <aside class="sidebar">
    <?php
    if (file_exists(__DIR__ . '/../includes/sidebar.php')) {
        include __DIR__ . '/../includes/sidebar.php';
    } else {
        // fallback simple sidebar
        echo '<div class="profile-card"><h3>About ForumX</h3><p>Share ideas and stories.</p></div>';
    }
    ?>
  </aside>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
