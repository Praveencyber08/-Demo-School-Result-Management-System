<?php
require 'config.php';

if (isset($_SESSION['student_user_id'])) {
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Student Logout')");
    $stmt->bind_param("i", $_SESSION['student_user_id']);
    $stmt->execute();
}

session_destroy();
header("Location: student_login.php");
exit;
?>