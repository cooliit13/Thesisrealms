<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid security token. Please try again.";
        header('Location: login.php');
        exit();
    }

    // reCAPTCHA Verification
    if (!isset($_POST['g-recaptcha-response'])) {
        $_SESSION['error'] = "Please complete the reCAPTCHA.";
        header('Location: login.php');
        exit();
    }

    $recaptcha_secret = '6LcISyorAAAAAF7zBIpsz4d9HLgZkaaAg0oMiJhY';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        $_SESSION['error'] = "reCAPTCHA verification failed. Please try again.";
        header('Location: login.php');
        exit();
    }

    // Input Validation
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please fill in both username and password.";
        header('Location: login.php');
        exit();
    }

    // Database Query
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
    if ($user['status'] !== 'active') {
        $_SESSION['error'] = "Your account is not activated. Please check your email to activate your account.";
        header('Location: login.php');
        exit();
    }

        // Update user login tracking
        try {
            $updateQuery = "UPDATE user SET 
                          last_login = NOW(), 
                          is_online = 1,
                          login_count = IFNULL(login_count, 0) + 1
                          WHERE id = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$user['id']]);
        } catch (PDOException $e) {
            error_log("Login tracking error: " . $e->getMessage());
        }

        // Set comprehensive session variables
        $_SESSION = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'user_email' => $user['email'] ?? '', // For account page compatibility
            'role' => $user['role'] ?? 'user',
            'full_name' => $user['full_name'] ?? '',
            'logged_in' => true, // Explicit login flag
            'last_activity' => time()
        ];

        // Configure session cookie properly for all PHP versions
        $cookieParams = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            time() + 86400 * 30, // 30 days expiration
            '/', // Path
            $_SERVER['HTTP_HOST'], // Domain
            isset($_SERVER['HTTPS']), // Secure
            true // HttpOnly
        );

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Log successful login
        try {
            $logStmt = $pdo->prepare("INSERT INTO activity_logs 
                                    (user_id, username, action, status, created_at) 
                                    VALUES (?, ?, ?, ?, NOW())");
            $logStmt->execute([
                $user['id'],
                $user['username'],
                'User Login',
                'success'
            ]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }

        // ✅ Redirect based on role
        switch (strtolower($_SESSION['role'])) {
            case 'admin':
                $redirectPath = '/Sagayoc/admin/index.php';
                break;
            case 'teacher':
            case 'student':
            default:
                $redirectPath = '/Sagayoc/dashboards/dashboard.php';
                break;
        }

        header("Location: $redirectPath");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password";

        // Log failed login attempt
        if ($user) {
            try {
                $logStmt = $pdo->prepare("INSERT INTO activity_logs 
                                         (user_id, username, action, status, created_at) 
                                         VALUES (?, ?, ?, ?, NOW())");
                $logStmt->execute([
                    $user['id'],
                    $user['username'],
                    'Failed Login Attempt',
                    'failed'
                ]);
            } catch (PDOException $e) {
                error_log("Failed login log error: " . $e->getMessage());
            }
        }

        header('Location: login.php');
        exit();
    }
} else {
    // Invalid request method
    header('Location: login.php');
    exit();
}
?>
