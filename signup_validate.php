<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: signup.php');
        exit();
    }

    // Restrict email to @buksu.edu.ph domain
    if (!preg_match('/^[\w.+-]+@buksu\.edu\.ph$/', $email)) {
        $_SESSION['error'] = "Only Buksu Teachers email addresses are allowed! Go to Users dashboard if you want to access files.";
        header('Location: signup.php');
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username already exists.";
        header('Location: signup.php');
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email is already registered.";
        header('Location: signup.php');
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO user (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");

    if ($stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword])) {
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "Your account has been created. You are now logged in.";
        header('Location: login.php');
        exit();
    } else {
        echo "There is an error.";
        exit();
    }
}
