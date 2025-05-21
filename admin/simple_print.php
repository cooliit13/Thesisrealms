<?php
require_once __DIR__ . '/../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

$conn = new mysqli("localhost", "root", "", "thesis_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT username, action, created_at, status FROM activity_logs ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);

// Set HTML Header
$mpdf->SetHTMLHeader('
<div style="font-size: 14pt; font-weight: bold; text-align: center; border-bottom: 1px solid #000; margin-bottom: 20px; padding-bottom: 8px;">
    Thesis Realm System - Activity Report
</div>
');

// Set HTML Footer
$mpdf->SetHTMLFooter('
<div style="border-top: 1px solid #000; font-size: 10pt; padding-top: 5px; display: flex; justify-content: space-between;">
    <span style="text-align: left;">{DATE j-m-Y}</span>
    <span style="text-align: center; flex: 1;">Page {PAGENO} of {nbpg}</span>
    <span style="text-align: right;">Thesis Realm</span>
</div>
');

// Start buffering HTML content
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Action</th>
                <th>Time & Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['action']) ?></td>
                        <td><?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No recent activity found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$html = ob_get_clean();
$mpdf->WriteHTML($html);
$mpdf->Output('recent_activity.pdf', \Mpdf\Output\Destination::INLINE);
?>
