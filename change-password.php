<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming password change logic is here, such as form handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $username = $_SESSION['username']; // Assuming the username is stored in session
    $newPassword = $_POST['new_password'];

    // Hash the new password and update in database
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashedPassword, $username);
    if ($stmt->execute()) {
        // Log the password change action
        $action = "Changed password";
        $status = "Success";

        // Insert into activity_logs
        $activityStmt = $conn->prepare("INSERT INTO activity_logs (username, action, created_at, status) VALUES (?, ?, NOW(), ?)");
        $activityStmt->bind_param("sss", $username, $action, $status);
        $activityStmt->execute();

        echo "Password changed successfully!";
    } else {
        echo "Error changing password.";
    }
}
?>
