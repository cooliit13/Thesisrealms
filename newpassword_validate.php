<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'] ?? null;
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$email) {
        $_SESSION['error'] = "Session expired. Please request another reset.";
        header("Location: forgot-password.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: newpassword.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header("Location: newpassword.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);

    unset($_SESSION['email']);
    $_SESSION['success'] = "Password changed successfully. Please log in.";
    header("Location: login.php");
    exit();
}
?>
