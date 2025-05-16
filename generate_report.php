<?php
require_once __DIR__ . '/../vendor/autoload.php'; // adjust path if needed
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use Mpdf\Mpdf;

// Fetch logs
$query = "SELECT username, action, status, created_at FROM activity_logs ORDER BY created_at DESC";
$result = $conn->query($query);

$logoPath = '../assets/images/COTLOGO.png';
$now = date("F j, Y, g:i a");

// Begin HTML
$html = '
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 80px; }
        .footer { position: fixed; bottom: 10px; font-size: 10px; width: 100%; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <img src="' . $logoPath . '" class="logo" alt="Logo">
        <h2>Thesis Realm – User Activity Report</h2>
        <p>Generated on: ' . $now . '</p>
    </div>
    <table>
        <tr>
            <th>Username</th>
            <th>Action</th>
            <th>Status</th>
            <th>Date & Time</th>
        </tr>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['username']) . '</td>
        <td>' . htmlspecialchars($row['action']) . '</td>
        <td>' . htmlspecialchars($row['status']) . '</td>
        <td>' . date("M d, Y h:i A", strtotime($row['created_at'])) . '</td>
    </tr>';
}

$html .= '</table>
    <div class="footer">© ' . date("Y") . ' Thesis Realm. All rights reserved.</div>
</body>
</html>';

// Generate PDF
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);

$filename = "activity_report_" . date("Ymd_His") . ".pdf";
$mpdf->Output($filename, 'D'); // D = force download
?>
