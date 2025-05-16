<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if not logged in
  header("Location: ../login.php");
  exit();
}

// Get the active user from session
$activeUser = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$profileImage = '../assets/images/default_avatar.png'; // fallback image

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT profile_picture FROM user WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($fetchedImage);
if ($stmt->fetch() && !empty($fetchedImage)) {
    $profileImage = '../uploads/profile_pictures/' . $fetchedImage;
}
$stmt->close();

}

// Function to count records safely
function getCount($conn, $query) {
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

$activeUsers = getCount($conn, "SELECT COUNT(*) AS count FROM users WHERE status='active'");
$thesisSubmitted = getCount($conn, "SELECT COUNT(*) AS count FROM uploads");
$approvedTheses = getCount($conn, "SELECT COUNT(*) AS count FROM uploads WHERE status='approved'");
$pendingReviews = getCount($conn, "SELECT COUNT(*) AS count FROM uploads WHERE status='pending'");

$recentActivityQuery = "SELECT id, username, action, created_at, status FROM activity_logs ORDER BY created_at DESC LIMIT 10";

$recentActivityResult = $conn->query($recentActivityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <title>Admin Dashboard | Thesis Realm</title>
</head>
<body>
  <div class="app-container">
    <div class="top-bar">
      <h5 class="mb-0">Thesis Realm Admin</h5>
      <div class="user-profile">
        <img src="<?= $profileImage ?>" alt="Admin User" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
        <span><?= htmlspecialchars($activeUser); ?></span>
      </div>
    </div>

    <div class="main-wrapper">
      <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
          <div class="sidebar-logo text-center py-3">
            <img src="../assets/images/COTLOGO.png" alt="Thesis Realm Logo" class="img-fluid" style="max-width: 150px;">
            <h6 class="mt-2 text-center">Thesis Realm Admin</h6>
          </div>
        </div>
        <div class="sidebar-menu">
          <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="users.php"><i class="fas fa-users"></i> User Management</a>
          <a href="#"><i class="fas fa-file-alt"></i> Thesis Management</a>
          <a href="submissions.php"><i class="fas fa-tasks"></i> Submissions</a>
          <a href="#"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
          <a href="#"><i class="fas fa-cog"></i> System Settings</a>
          <a href="#"><i class="fas fa-question-circle"></i> Help Center</a>
          <div class="mt-auto p-3">
            <a href="\Sagayoc\login.php" class="btn btn-sm btn-danger w-100"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>

      <div class="content-area">
        <div class="stat-cards-row">
          <div class="stat-card-col">
            <div class="stat-card">
              <div class="stat-icon"><i class="fas fa-users"></i></div>
              <div class="stat-number"><?= $activeUsers ?></div>
              <div class="stat-label">Active Users</div>
            </div>
          </div>
          <div class="stat-card-col">
            <div class="stat-card">
              <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
              <div class="stat-number"><?= $thesisSubmitted ?></div>
              <div class="stat-label">Thesis Submitted</div>
            </div>
          </div>
          <div class="stat-card-col">
            <div class="stat-card">
              <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
              <div class="stat-number"><?= $approvedTheses ?></div>
              <div class="stat-label">Approved Theses</div>
            </div>
          </div>
          <div class="stat-card-col">
            <div class="stat-card">
              <div class="stat-icon"><i class="fas fa-clock"></i></div>
              <div class="stat-number"><?= $pendingReviews ?></div>
              <div class="stat-label">Pending Reviews</div>
            </div>
          </div>
        </div>

        <div class="container mt-5">
          <h4>Recent Activity</h4>

          <!-- PDF Download Button -->
          <a href="simple_print.php" class="btn btn-primary mb-3">
            <i class="fas fa-download"></i> Download PDF Report
          </a>

          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Username</th>
                <th>Action</th>
                <th>Time & Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recentActivityResult && $recentActivityResult->num_rows > 0): ?>
                <?php while ($row = $recentActivityResult->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center">No recent activity found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
