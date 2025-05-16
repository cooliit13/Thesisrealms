<?php
// Start secure session
session_start([
    'cookie_lifetime' => 86400 * 30, // 30 days
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

// Enhanced login check
if (!isset($_SESSION['logged_in']) && !isset($_SESSION['google_email'])) {
    header("Location: ../login.php");
    exit();
}

require '../includes/db.php';

// Determine which email to use
$email = $_SESSION['user_email'] ?? $_SESSION['google_email'] ?? null;
if (!$email) {
    header("Location: ../login.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Handle Google users
if (isset($_SESSION['google_email']) && !$user) {
    $user = [
        'email' => $_SESSION['google_email'],
        'username' => $_SESSION['google_name'] ?? 'Google User',
        'profile_picture' => $_SESSION['google_picture'] ?? null
    ];
    
    // Auto-create user if not exists
    try {
        $insertStmt = $pdo->prepare("INSERT INTO user 
            (email, username, profile_picture, login_method) 
            VALUES (?, ?, ?, 'google')");
        $insertStmt->execute([
            $_SESSION['google_email'],
            $_SESSION['google_name'] ?? 'Google User',
            $_SESSION['google_picture'] ?? null
        ]);
        $user['id'] = $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Failed to create Google user: " . $e->getMessage());
    }
}

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"]) && !isset($_SESSION['google_email'])) {
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . basename($_FILES["profile_picture"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            $updateStmt = $pdo->prepare("UPDATE user SET profile_picture = ? WHERE email = ?");
            if ($updateStmt->execute([$fileName, $email])) {
                $_SESSION['success'] = "Profile picture updated successfully!";
                header("Location: accounts.php");
                exit();
            }
        }
        $_SESSION['error'] = "Failed to upload image.";
    } else {
        $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_password"]) && !isset($_SESSION['google_email'])) {
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword === $confirmPassword) {
        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters long.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePassword = $pdo->prepare("UPDATE user SET password = ? WHERE email = ?");
            if ($updatePassword->execute([$hashedPassword, $email])) {
                $_SESSION['success'] = "Password changed successfully!";
                header("Location: accounts.php");
                exit();
            }
        }
    } else {
        $_SESSION['error'] = "Passwords do not match.";
    }
}

// Handle logout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    if (isset($_SESSION['google_email'])) {
        require_once '../vendor/autoload.php';
        $client = new Google_Client();
        $client->setAuthConfig('../client_secret.json');
        $client->revokeToken();
    }
    
    $_SESSION = [];
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Account</title>
    <link rel="stylesheet" href="/Sagayoc/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/accounts-styles.css?v=1.2">
</head>
<body>

<!-- Display alerts -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3">
    <?= htmlspecialchars($_SESSION['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['error']); endif; ?>

<header>
<h5>
            <img src="../assets/images/COTLOGO.png" alt="Logo" style="vertical-align: middle; height: 40px; padding-left: 10px; margin-top: 10px;">
            College of Technologies Thesis Realm
        </h5>
    <nav>
    <ul>
                <li><a href="../dashboards/dashboard.php"><i class=""></i> HOME</a></li>
                <li><a href="../dashboards/about.php"><i class=""></i> ABOUT</a></li>
                <li><a href="../dashboards/files.php"><i class=""></i> FILES</a></li>
                <li><a href="../dashboards/accounts.php"><i class=""></i> ACCOUNT</a></li>
            </ul>
    </nav>
</header>

<div class="card p-3 shadow-sm" style="max-width: 450px; margin: 100px auto; border-radius: 10px;">
    <div class="card-body p-2">
        <!-- Profile Section -->
        <div class="d-flex align-items-center mb-3">
            <div class="me-3">
                <?php
                $profilePic = $_SESSION['google_picture'] ?? 
                            (!empty($user['profile_picture']) ? 
                                (filter_var($user['profile_picture'], FILTER_VALIDATE_URL) ? 
                                    $user['profile_picture'] : 
                                    '/Sagayoc/uploads/' . $user['profile_picture']) : 
                            '/Sagayoc/assets/images/default.png');
                
                // Force HTTPS for external images
                if (strpos($profilePic, 'http://') === 0) {
                    $profilePic = str_replace('http://', 'https://', $profilePic);
                }
                ?>
                <img src="<?= htmlspecialchars($profilePic) ?>" 
                     class="rounded-circle profile-picture"
                     onerror="this.src='/Sagayoc/assets/images/default.png'"
                     alt="Profile">
            </div>
            
            <?php if (!isset($_SESSION['google_email'])): ?>
            <div class="flex-grow-1">
                <form action="accounts.php" method="POST" enctype="multipart/form-data">
                    <div class="input-group input-group-sm mb-2">
                        <input type="file" name="profile_picture" class="form-control form-control-sm" id="profileUpload" accept="image/*" style="display: none;">
                        <label for="profileUpload" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-upload me-1"></i>Change
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- User Info -->
        <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-user text-muted me-2" style="width: 20px;"></i>
                <div>
                    <small class="text-muted">Username</small>
                    <p class="mb-0 fw-bold">
                        <?= htmlspecialchars($_SESSION['google_name'] ?? $user['username'] ?? 'Not set') ?>
                    </p>
                </div>
            </div>
            
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-envelope text-muted me-2" style="width: 20px;"></i>
                <div>
                    <small class="text-muted">Email</small>
                    <p class="mb-0 fw-bold"><?= htmlspecialchars($email) ?></p>
                </div>
            </div>
            
            <?php if (isset($_SESSION['google_email'])): ?>
            <div class="d-flex align-items-center mb-2">
                <i class="fab fa-google text-muted me-2" style="width: 20px;"></i>
                <div>
                    <small class="text-muted">Login Method</small>
                    <p class="mb-0 fw-bold">Google Account</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_SESSION['google_email'])): ?>
        <div class="mb-3">
            <h6 class="card-title mb-2"><i class="fas fa-key me-1"></i>Change Password</h6>
            <form action="accounts.php" method="POST">
                <div class="mb-2">
                    <input type="password" name="new_password" class="form-control form-control-sm" placeholder="New password" required minlength="8">
                </div>
                <div class="mb-2">
                    <input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="Confirm password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-lock me-1"></i>Update
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div>
            <form action="accounts.php" method="POST">
                <button type="submit" name="logout" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-1"></i>Log Out
                </button>
            </form>
        </div>
    </div>
</div>

<footer class="text-center mt-5">
    &copy; 2025 Bukidnon State University – College of Technologies. All rights reserved. 
</footer>

<script src="/Sagayoc/bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        new bootstrap.Alert(alert).close();
    });
}, 5000);
</script>
</body>
</html>
