<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming admin approves a file here
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_file'])) {
    $adminUsername = $_SESSION['username']; // Assuming admin is logged in
    $fileId = $_POST['file_id']; // The ID of the file being approved
    $fileName = $_POST['file_name']; // The name of the file

    // Update file status in the database (mark as approved)
    $stmt = $conn->prepare("UPDATE files SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    if ($stmt->execute()) {
        // Log the file approval
        $action = "Approved a file: " . $fileName;
        $status = "Success";

        // Insert into activity_logs
        $activityStmt = $conn->prepare("INSERT INTO activity_logs (username, action, created_at, status) VALUES (?, ?, NOW(), ?)");
        $activityStmt->bind_param("sss", $adminUsername, $action, $status);
        $activityStmt->execute();

        echo "File approved successfully!";
    } else {
        echo "Error approving file.";
    }
}
?>
