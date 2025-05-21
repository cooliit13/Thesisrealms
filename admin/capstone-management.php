<?php
session_start();
require '../includes/db.php'; // Connect to your database
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
          <a href="capstone-management.php" style="font-size: 0.9em; " class="active"><i class="fas fa-file-alt"></i> Capstone Management</a>
          <a href="submissions.php"><i class="fas fa-tasks"></i> Submissions</a>
          <a href="reports-analytics.php"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
                <div class="mt-auto p-3">
                    <a href="/Sagayoc/login.php" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-grow-1 p-4 overflow-auto">
            <h3 class="mb-4">Approved Capstone/Thesis Files</h3>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>File Name</th>
                        <th>Uploaded By</th>
                        <th>Upload Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM uploads WHERE status = 'Approved'";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "<tr><td colspan='5'>Error: " . mysqli_error($conn) . "</td></tr>";
                } else {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['filename']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['uploaded_by']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['upload_date']) . "</td>";
                        echo "<td><a href='delete-thesis.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this file?\")'>Delete</a></td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
