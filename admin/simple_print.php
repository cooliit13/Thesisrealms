<?php
require_once __DIR__ . '/../vendor/autoload.php';
// or 'libs/mpdf/vendor/autoload.php' if downloaded manually

$mpdf = new \Mpdf\Mpdf();

$conn = new mysqli("localhost", "root", "", "thesis_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT username, action, created_at, status FROM activity_logs ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);

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
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Thesis Realm - Recent Activity Report</h2>
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
$mpdf->Output('recent_activity.pdf', \Mpdf\Output\Destination::INLINE); // Use DOWNLOAD instead of INLINE to force download
?>
