<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Logout')");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

session_destroy();
header("Location: index.html");
exit;
?>