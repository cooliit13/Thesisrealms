<?php
session_start();
require '../vendor/autoload.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "thesis_db");
    
    if ($conn->connect_error) {
        throw new Exception("DB Connection failed: " . $conn->connect_error);
    }

    $response = [];

    // 1. Thesis Status Distribution (Pie Chart)
    $statusQuery = "SELECT status, COUNT(*) as count FROM uploads GROUP BY status";
    $result = $conn->query($statusQuery);
    while ($row = $result->fetch_assoc()) {
        $response['statusLabels'][] = $row['status'];
        $response['statusData'][] = $row['count'];
    }
    $result->free();

    // 2. Monthly Submissions (Bar Chart)
    $monthlyQuery = "SELECT 
        DATE_FORMAT(upload_date, '%Y-%m') as month, 
        COUNT(*) as count 
        FROM uploads 
        WHERE upload_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(upload_date, '%Y-%m')
        ORDER BY month ASC";
    
    $result = $conn->query($monthlyQuery);
    $monthlyLabels = [];
    $monthlyCounts = [];
    while ($row = $result->fetch_assoc()) {
        $monthlyLabels[] = date('M Y', strtotime($row['month'] . '-01'));
        $monthlyCounts[] = $row['count'];
    }
    $result->free();

    // Fill any missing months
    $allMonths = [];
    for ($i = 5; $i >= 0; $i--) {
        $allMonths[] = date('M Y', strtotime("-$i months"));
    }

    $response['monthlyLabels'] = $allMonths;
    $response['monthlyData'] = array_map(function($month) use ($monthlyLabels, $monthlyCounts) {
        $index = array_search($month, $monthlyLabels);
        return ($index !== false) ? $monthlyCounts[$index] : 0;
    }, $allMonths);

    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>