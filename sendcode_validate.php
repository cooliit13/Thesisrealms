<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'] ?? null;
    $code = $_POST['code'] ?? '';

    if (!$email) {
        $_SESSION['error'] = "Session expired. Please request a new code.";
        header("Location: forgot-password.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT reset_code FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['reset_code'] == $code) {
        $_SESSION['verified'] = true;
        $_SESSION['code_success'] = true; // Flag for success notification
        header("Location: new-password.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid code. Please try again.";
        header("Location: send-code.php");
        exit();
    }
}
?>