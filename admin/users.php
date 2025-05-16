<?php
session_start();
$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$result = $conn->query("SELECT * FROM user");

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Delete user from the database
    $delete_stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: users.php"); // Redirect to refresh the page after deletion
    exit();
}

if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Get the user email by ID
    $stmt = $conn->prepare("SELECT email, role FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $resultUser = $stmt->get_result();
    $targetUser = $resultUser->fetch_assoc();
    $stmt->close();

    // Prevent changing own role if logged-in user is admin
    if ($targetUser['email'] === $_SESSION['user_email']) {
        $_SESSION['error'] = "You cannot change your own role.";
        header("Location: users.php");
        exit;
    }

    // Only allow roles Admin or Teacher
    if (!in_array($new_role, ['admin', 'teacher'])) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: users.php");
        exit;
    }

    // Update user role
    $update_stmt = $conn->prepare("UPDATE user SET role = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_role, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Log activity
    $adminUsername = $_SESSION['username'] ?? 'admin';
    $action = "Changed role of user ID $user_id to '$new_role'";
    $status = "Success";
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (username, action, status) VALUES (?, ?, ?)");
    $log_stmt->bind_param("sss", $adminUsername, $action, $status);
    $log_stmt->execute();
    $log_stmt->close();

    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- DataTables v2.3.0 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="style.css" />
    <title>Admin Dashboard | Thesis Realm</title>
</head>
<body>
    <div class="app-container">
        <!-- Top Bar -->
        <div class="top-bar">
            <h5 class="mb-0">Thesis Realm Admin</h5>
            <div class="user-profile">
                <img src="../assets/images/464677697_444110865091918_7101498701914949461_n.jpg" alt="Admin User" />
                <span>Admin User</span>
            </div>
        </div>

        <div class="main-wrapper">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo text-center py-3">
                        <img
                            src="../assets/images/COTLOGO.png"
                            alt="Thesis Realm Logo"
                            class="img-fluid"
                            style="max-width: 150px"
                        />
                        <h6 class="mt-2 text-center">Thesis Realm Admin</h6>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="users.php"><i class="fas fa-users"></i> User Management</a>
                    <a href="#"><i class="fas fa-file-alt"></i> Thesis Management</a>
                    <a href="submissions.php"><i class="fas fa-tasks"></i> Submissions</a>
                    <a href="#"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
                    <a href="#"><i class="fas fa-cog"></i> System Settings</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Help Center</a>
                    <div class="mt-auto p-3">
                        <a href="\Sagayoc\login.php" class="btn btn-sm btn-danger w-100">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-area">
                <div class="container mt-5">
                    <h4>User Management</h4>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <table id="usersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['id']) . "</td>
            <td>" . htmlspecialchars($row['firstname']) . "</td>
            <td>" . htmlspecialchars($row['lastname']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . (strtolower(trim($row['role'])) === 'admin' ? 'Admin' : 'Teacher') . "</td>
            <td>
                <form method='POST' style='display:inline-block;'>
                    <input type='hidden' name='user_id' value='" . htmlspecialchars($row['id']) . "' />
                    <button type='submit' name='delete_user' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</button>
                </form>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
}
?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- DataTables v2.3.0 JS -->
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Initialize the DataTable with Bootstrap 5 integration
        let table = new DataTable('#usersTable', {
            layout: {
                topStart: 'search',
                topEnd: 'pageLength',
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });
    </script>

    <script src="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
