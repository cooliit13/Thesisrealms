<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = $_POST['token'];

    // Validate email and token
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ? AND verification_token = ? AND status = 'inactive'");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();

    if ($user) {
        // Activate account
        $stmt = $pdo->prepare("UPDATE user SET status = 'active' WHERE email = ?");
        if ($stmt->execute([$email])) {
            $_SESSION['success'] = "Your account has been activated successfully. You can now login.";
        } else {
            $_SESSION['error'] = "Account activation failed. Please try again or contact admin.";
        }
    } else {
        $_SESSION['error'] = "Invalid activation link or account already activated.";
    }

    header("Location: login.php");
    exit();
}

// Check if email and token are provided in URL
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
} else {
    $_SESSION['error'] = "Invalid activation link.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .activation-container {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #D97706;
            border-color: #D97706;
        }
        .btn-primary:hover {
            background-color: #B45309;
            border-color: #B45309;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="activation-container">
            <h2 class="text-center mb-4">Account Activation</h2>
            
            <p>Click the button below to activate your account:</p>
            
            <form method="POST" action="activate.php">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Activate My Account</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php">Return to Login</a>
            </div>
        </div>
    </div>
</body>
</html>