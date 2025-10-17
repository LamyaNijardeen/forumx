<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForumX</title>
    <link rel="stylesheet" href="/forumx/assets/css/style.css">
</head>
<body>
    <header class="medium-header">
        <div class="nav-container">
            <div class="logo">
                <a href="/forumx/pages/home.php">Forum<span>X</span></a>
            </div>
            <nav class="nav-links">
                <a href="/forumx/pages/home.php">Home</a>
                <a href="/forumx/pages/create_blog.php">Write</a>
                <a href="/forumx/pages/about.php">About</a>
                <a href="/forumx/pages/logout.php">Logout</a>
            </nav>
        </div>
    </header>
