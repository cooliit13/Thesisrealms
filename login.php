<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env from current directory
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - College of Technologies Thesis Realms</title>
  <link rel="stylesheet" href="/bootstrap-5.3.3-dist/bootstrap.min.css" />
  <link rel="stylesheet" href="styles/login-styles.css" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div class="container mt-5">
  <img src="assets\images\COTLOGO.png" alt="Logo" class="logo mb-3">
  <p>Welcome to Cot Thesis Realms! Please Login</p>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <form action="login_validate.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required />
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required />

    <?php if ($siteKey): ?>
      <div class="g-recaptcha mb-3" data-sitekey="<?= $siteKey ?>"></div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
  </form>

  <a href="googleAuth/google-login.php" class="btn btn-outline-danger w-100">
    <i class="mdi mdi-google me-2"></i> Sign up with Google
  </a>

  <p>Don't have an account? <a href="signup.php">Sign up</a></p>
  <p>Forgot password? <a href="forgot-password.php">Click here</a></p>
</div>

</body>
</html>
