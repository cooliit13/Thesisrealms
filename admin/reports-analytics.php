<?php
session_start();  // Start the session at the top
require '../vendor/autoload.php'; // Adjust path if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Capstone Repository</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-container">
    <!-- Top Bar -->
    <div class="top-bar">
        <h5 class="mb-0">Thesis Realm Admin</h5>
        <div class="user-profile">
            <img src="../assets/images/464677697_444110865091918_7101498701914949461_n.jpg" alt="Admin User">
            <span>Admin User</span>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper d-flex" style="height: 100vh; overflow: hidden;">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar" style="width: 250px; flex-shrink: 0;">
            <div class="sidebar-header">
                <div class="sidebar-logo text-center py-3">
                    <img src="../assets/images/COTLOGO.png" alt="Thesis Realm Logo" class="img-fluid" style="max-width: 150px;">
                    <h6 class="mt-2 text-center">Capstone Repository Admin</h6>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="users.php"><i class="fas fa-users"></i> User Management</a>
          <a href="capstone-management.php" style="font-size: 0.9em;"><i class="fas fa-file-alt"></i> Capstone Management</a>
          <a href="submissions.php"><i class="fas fa-tasks"></i> Submissions</a>
          <a href="reports-analytics.php"  class="active"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
                <div class="mt-auto p-3">
                    <a href="\Sagayoc\login.php" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content p-4 flex-grow-1 overflow-auto">
            <h3>Reports & Analytics</h3>
            <hr>
            <!-- You can now add new content here -->
            <div class="row mb-4">
    <!-- Thesis Status Pie Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Thesis Submission Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Submissions Bar Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Monthly Submissions</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlySubmissionsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    $.ajax({
        url: 'get_chart_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // 1. Thesis Status Pie Chart
            new Chart(
                document.getElementById('statusPieChart').getContext('2d'),
                {
                    type: 'pie',
                    data: {
                        labels: data.statusLabels,
                        datasets: [{
                            data: data.statusData,
                            backgroundColor: [
                                ' #ffc107', // Pending
                                'rgb(3, 93, 0)', // Approved
                                'rgb(125, 4, 4)', // Rejected
                                ' #17a2b8'  // Other
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                }
            );

            // 2. Monthly Submissions Bar Chart
            new Chart(
                document.getElementById('monthlySubmissionsChart').getContext('2d'),
                {
                    type: 'bar',
                    data: {
                        labels: data.monthlyLabels,
                        datasets: [{
                            label: 'Submissions',
                            data: data.monthlyData,
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                }
            );
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert("Failed to load chart data. Check console for details.");
        }
    });
});
</script>

</body>
</html>

