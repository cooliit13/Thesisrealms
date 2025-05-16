<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=activity_log_'.date('Y-m-d').'.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['ID', 'Username', 'Role', 'Action Type', 'Action Details', 'IP Address', 'Timestamp']);

// Get all activity logs
$query = "SELECT * FROM activity_logs ORDER BY created_at DESC";
$result = $conn->query($query);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['username'],
        $row['user_role'],
        formatActionType($row['action_type']),
        $row['action_details'],
        $row['ip_address'],
        $row['created_at']
    ]);
}

fclose($output);
exit();

// Helper function (same as in activity_log.php)
function formatActionType($actionType) {
    $formatted = [
        'login' => 'Login',
        'logout' => 'Logout',
        'upload' => 'File Upload',
        'password_change' => 'Password Change',
        'approval' => 'Approval',
        'rejection' => 'Rejection',
        'user_create' => 'User Created',
        'user_delete' => 'User Deleted',
        'user_update' => 'User Updated'
    ];
    return $formatted[$actionType] ?? ucfirst(str_replace('_', ' ', $actionType));
}
?>