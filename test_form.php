<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        echo "Hello, " . htmlspecialchars($name);
    } else {
        echo "Name empty!";
    }
} else {
    echo "Use the form.";
}
