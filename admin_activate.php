<?php
session_start();
require 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

// Handle activation request
if (isset($_GET['activate']) && isset($_GET['email'])) {
    $email = $_GET['email'];
    
    // Update user status to active
    $stmt = $pdo->prepare("UPDATE user SET status = 'active' WHERE email = ?");
    if ($stmt->execute([$email])) {
        $_SESSION['admin_message'] = "User account ($email) has been activated successfully.";
    } else {
        $_SESSION['admin_error'] = "Failed to activate user account.";
    }
    
    header('Location: admin_activate.php');
    exit();
}

// Get all inactive users
$stmt = $pdo->prepare("SELECT * FROM user WHERE status = 'inactive' ORDER BY id DESC");
$stmt->execute();
$inactive_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Activate Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 1000px;
            margin-top: 50px;
        }
        .table-responsive {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Account Activation Dashboard</h2>
        
        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['admin_message']; 
                    unset($_SESSION['admin_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['admin_error']; 
                    unset($_SESSION['admin_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($inactive_users) > 0): ?>
                        <?php foreach ($inactive_users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="admin_activate.php?activate=1&email=<?php echo urlencode($user['email']); ?>" 
                                       class="btn btn-primary btn-sm" 
                                       onclick="return confirm('Are you sure you want to activate this account?');">
                                        Activate
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No pending activations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>