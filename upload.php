<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the file is being uploaded via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $username = $_SESSION['username']; // Assuming the username is stored in session
    $fileName = $_FILES['file_upload']['name'];
    $fileTmpName = $_FILES['file_upload']['tmp_name'];
    $uploadPath = "uploads/" . $fileName;

    // Move the uploaded file to the server
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        // File uploaded successfully, log the action
        $action = "Uploaded a file: " . $fileName;
        $status = "Success";
        
        // Insert into activity_logs
        $activityStmt = $conn->prepare("INSERT INTO activity_logs (username, action, created_at, status) VALUES (?, ?, NOW(), ?)");
        $activityStmt->bind_param("sss", $username, $action, $status);
        $activityStmt->execute();
        
        echo "File uploaded successfully!";
    } else {
        echo "Failed to upload file.";
    }
}
?>
