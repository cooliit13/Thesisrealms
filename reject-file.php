<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming admin rejects a file here
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_file'])) {
    $adminUsername = $_SESSION['username']; // Assuming admin is logged in
    $fileId = $_POST['file_id']; // The ID of the file being rejected
    $fileName = $_POST['file_name']; // The name of the file

    // Update file status in the database (mark as rejected)
    $stmt = $conn->prepare("UPDATE files SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    if ($stmt->execute()) {
        // Log the file rejection
        $action = "Rejected a file: " . $fileName;
        $status = "Rejected"; // Or use another status if desired

        // Insert into activity_logs
        $activityStmt = $conn->prepare("INSERT INTO activity_logs (username, action, created_at, status) VALUES (?, ?, NOW(), ?)");
        $activityStmt->bind_param("sss", $adminUsername, $action, $status);
        $activityStmt->execute();

        echo "File rejected successfully!";
    } else {
        echo "Error rejecting file.";
    }
}
?>
