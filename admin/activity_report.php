<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed for your mPDF installation
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if admin
$userId = $_SESSION['user_id'];
$checkAdmin = $conn->prepare("SELECT role FROM users WHERE id = ? AND role = 'admin'");
$checkAdmin->bind_param("i", $userId);
$checkAdmin->execute();
$checkAdmin->store_result();

if ($checkAdmin->num_rows === 0) {
    header("Location:\Sagayoc\admin\activity_report.php");
    exit();
}
$checkAdmin->close();

// Get filter parameters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$actionType = isset($_GET['action_type']) ? $_GET['action_type'] : 'all';
$userRole = isset($_GET['role']) ? $_GET['role'] : 'all';

// Build the query with filters
$query = "
    SELECT 
        al.id, 
        al.username, 
        al.action, 
        al.action_type,
        al.created_at, 
        al.status,
        u.role
    FROM 
        activity_logs al
    LEFT JOIN 
        users u ON al.user_id = u.id
    WHERE 
        DATE(al.created_at) BETWEEN ? AND ?";

$params = [$startDate, $endDate];
$types = "ss";

if ($actionType != 'all') {
    $query .= " AND al.action_type = ?";
    $params[] = $actionType;
    $types .= "s";
}

if ($userRole != 'all') {
    $query .= " AND u.role = ?";
    $params[] = $userRole;
    $types .= "s";
}

$query .= " ORDER BY al.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Create new mPDF document
$mpdf = new \Mpdf\Mpdf([
    'margin_top' => 15,
    'margin_bottom' => 15,
    'margin_left' => 15,
    'margin_right' => 15
]);

// Set document information
$mpdf->SetTitle('Activity Report');
$mpdf->SetAuthor('Thesis Realm Admin');
$mpdf->SetCreator('Thesis Realm System');

// Add a header
$header = '
<table width="100%" style="border-bottom: 1px solid #000000; vertical-align: bottom; font-size: 9pt; color: #000000;">
    <tr>
        <td width="33%" style="text-align: left;">
            <img src="../assets/images/COTLOGO.png" style="height: 50px;">
        </td>
        <td width="33%" style="text-align: center; font-weight: bold; font-size: 14pt;">
            Thesis Realm Activity Report
        </td>
        <td width="33%" style="text-align: right;">
            Generated: ' . date('Y-m-d H:i:s') . '
        </td>
    </tr>
</table>';

$mpdf->SetHTMLHeader($header);

// Add a footer
$footer = '
<table width="100%" style="vertical-align: bottom; font-size: 8pt; color: #000000; border-top: 1px solid #000000;">
    <tr>
        <td width="33%" style="text-align: left;">Thesis Realm System</td>
        <td width="33%" style="text-align: center;">Page {PAGENO} of {nbpg}</td>
        <td width="33%" style="text-align: right;">Confidential</td>
    </tr>
</table>';

$mpdf->SetHTMLFooter($footer);

// Calculate statistics
$totalActivities = $result->num_rows;

// Reset result pointer
$result->data_seek(0);

// Count by type
$loginCount = 0;
$uploadCount = 0;
$adminCount = 0;
$accountCount = 0;

while ($row = $result->fetch_assoc()) {
    switch ($row['action_type']) {
        case 'login': $loginCount++; break;
        case 'upload': $uploadCount++; break;
        case 'admin': $adminCount++; break;
        case 'account': $accountCount++; break;
    }
}
// Reset result pointer
$result->data_seek(0);

// Build HTML content
$html = '
<style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    h1 { font-size: 16pt; text-align: center; color: #333; margin-bottom: 8px; }
    h2 { font-size: 13pt; color: #444; margin-top: 16px; }
    .filters { margin-bottom: 15px; }
    table.details { border-collapse: collapse; width: 100%; margin-top: 20px; }
    table.details th { background-color: #4a6da7; color: white; padding: 8px; font-size: 11pt; text-align: left; }
    table.details td { border: 1px solid #ddd; padding: 6px; font-size: 10pt; }
    table.details tr:nth-child(even) { background-color: #f9f9f9; }
    .stats-table { width: 50%; margin-bottom: 20px; }
    .stats-table td { padding: 5px; }
    .date-range { text-align: center; margin-bottom: 20px; color: #666; }
    .login { color: #198754; }
    .upload { color: #0d6efd; }
    .admin { color: #dc3545; }
    .account { color: #ffc107; }
</style>

<h1>System Activity Report</h1>
<div class="date-range">Date Range: ' . $startDate . ' to ' . $endDate . '</div>

<h2>Applied Filters</h2>
<table class="filters">
    <tr>
        <td width="120">Action Type:</td>
        <td>' . ucfirst($actionType) . '</td>
    </tr>
    <tr>
        <td>User Role:</td>
        <td>' . ucfirst($userRole) . '</td>
    </tr>
</table>

<h2>Activity Statistics</h2>
<table class="stats-table">
    <tr>
        <td width="150">Total Activities:</td>
        <td><strong>' . $totalActivities . '</strong></td>
    </tr>
    <tr>
        <td>Login Activities:</td>
        <td><strong class="login">' . $loginCount . '</strong></td>
    </tr>
    <tr>
        <td>Upload Activities:</td>
        <td><strong class="upload">' . $uploadCount . '</strong></td>
    </tr>
    <tr>
        <td>Admin Actions:</td>
        <td><strong class="admin">' . $adminCount . '</strong></td>
    </tr>
    <tr>
        <td>Account Changes:</td>
        <td><strong class="account">' . $accountCount . '</strong></td>
    </tr>
</table>

<h2>Activity Details</h2>
<table class="details">
    <thead>
        <tr>
            <th>Username</th>
            <th>Action</th>
            <th>Date & Time</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine class for action type
        $actionClass = '';
        if (isset($row['action_type'])) {
            $actionClass = $row['action_type'];
        }
        
        $html .= '
        <tr>
            <td>' . htmlspecialchars($row['username']) . '</td>
            <td class="' . $actionClass . '">' . htmlspecialchars($row['action']) . '</td>
            <td>' . date("Y-m-d H:i", strtotime($row['created_at'])) . '</td>
            <td>' . htmlspecialchars($row['role'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($row['status']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" style="text-align: center;">No activity records found</td></tr>';
}

$html .= '
    </tbody>
</table>';

// Write HTML to the PDF
$mpdf->WriteHTML($html);

// Output the PDF
$mpdf->Output('activity_report_' . date('Y-m-d') . '.pdf', 'I');