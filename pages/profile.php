<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
$profile_id = (int)($_GET['user_id'] ?? 0);

// Fetch user info (include profile_photo)
$stmt = $conn->prepare("SELECT id, username, email, profile_photo FROM user WHERE id = ?");
$stmt->bind_param('i', $profile_id);
$stmt->execute();
$res = $stmt->get_result();
$profile_user = $res->fetch_assoc();
$stmt->close();

if (!$profile_user) {
    echo "User not found.";
    exit;
}

// Fetch all blog posts by this user
$search = trim($_GET['q'] ?? '');
if ($search) {
    $query = "SELECT id, title, created_at, image_path FROM blogpost WHERE user_id = ? AND title LIKE CONCAT('%', ?, '%') ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $profile_id, $search);
} else {
    $query = "SELECT id, title, created_at, image_path FROM blogpost WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $profile_id);
}
$stmt->execute();
$res = $stmt->get_result();
$posts = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['username']); ?> - ForumX</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
</head>
<body>

<!-- Header -->
<header>
    <div class="left-section">
        <div class="logo">ForumX</div>
        <nav>
            <a href="../pages/home.php" class="nav-btn">Home</a>
            <a href="../pages/about.php" class="nav-btn">About us</a>
            <a href="../pages/create_blog.php" class="nav-btn">Write</a>
            <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>" class="nav-btn active">My Profile</a>
        </nav>
    </div>
    <div class="user-info">
        Hello, <?php echo htmlspecialchars($user['username'] ?? ''); ?>
        <div class="separator"></div>
        <a href="../pages/logout.php" class="logout-btn">Logout</a>
    </div>
    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="../pages/home.php">Home</a>
    <a href="../pages/about.php">About us</a>
    <a href="../pages/create_blog.php">Write</a>
    <a href="../pages/profile.php?user_id=<?php echo $user['id']; ?>" class="active">My Profile</a>
    <a href="../pages/logout.php">Logout</a>
</div>

<!-- Search Bar -->
<div class="search-container">
    <form method="get" action="">
        <input type="hidden" name="user_id" value="<?php echo $profile_id; ?>">
        <input type="text" name="q" placeholder="Search by topic..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        <button type="submit">🔍</button>
    </form>
</div>

<!-- Profile Section -->
<main class="profile-content">
    <h2><?php echo htmlspecialchars($profile_user['username']); ?>’s Blogs</h2>

    <!-- Profile Picture -->
    <div class="profile-picture-section">
        <div class="profile-picture">
            <img src="../assets/profile_photos/<?php echo htmlspecialchars($profile_user['profile_photo'] ?: 'default.png'); ?>" alt="Profile Picture">
        </div>

        <!-- Change Profile Button -->
        <?php if ($user && $user['id'] === $profile_user['id']): ?>
           <div class="upload-box">          
        <form action="../pages/upload_profile_pic.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="file" name="profile_image" accept="image/*" required>
            <button type="submit">Change Profile Picture</button></div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Blog Posts -->
    <?php if (empty($posts)): ?>
        <p class="no-posts">No posts yet.</p>
    <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
                <a href="../pages/view_blog.php?id=<?php echo $post['id']; ?>" class="blog-link">
                    <div class="blog-card">
                        <?php if (!empty($post['image_path'])): ?>
                        <div class="blog-image">
                            <img src="../assets/images/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Blog Image">
                        </div>
                        <?php endif; ?>
                        <div class="blog-details">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="meta">
                                <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                <span class="time"><?php echo date('H:i', strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer class="footer">
    © 2025 ForumX — A community to share ideas.
</footer>

<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    hamburger.classList.toggle('open');
});
</script>

</body>
</html>
