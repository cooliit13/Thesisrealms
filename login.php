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
    <meta charset="UTF-8">
    <title>Login - COT Thesis Realms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #E3DCD9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); }
        }

        .container {
            display: flex;
            background: #fff;
            width: 900px;
            max-width: 95%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .left {
            flex: 1;
            background: #581413;
            color: #FFFFFF;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .left img.logo { width: 150px; margin-bottom: 20px; }
        .left h1 { font-size: 36px; margin-bottom: 10px; }
        .left p { line-height: 1.6; }

        .right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #000000;
        }

        .right h2 { font-size: 28px; font-weight: bold; margin-bottom: 20px; }

        form input[type="text"], form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0;
            color: #000000;
        }

        .remember input { margin-right: 6px; }
        .forgot-link, .signup-text a {
            color: #D97706;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover, .signup-text a:hover {
            color: #b45309;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #581413;
            color: #FFFFFF;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        button[type="submit"]::before {
            content: '';
            background: url('assets/images/login-icon.png') no-repeat center center;
            background-size: 20px;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            display: inline-block;
        }

        .google-btn {
            width: 100%;
            margin-top: 10px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 1px solid #333;
            border-radius: 8px;
            text-decoration: none;
            color: #000000;
            background: #fff;
        }

        .google-btn img { width: 20px; margin-right: 10px; }
        .g-recaptcha { margin: 10px 0; }

        .divider {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: #ccc;
        }

        .divider span { padding: 0 10px; color: #999; }

        .signup-text {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .alert-error {
            padding: 10px;
            background-color: #f8d7da;
            color: #581413;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
        }

        .alert-success {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
        }

        @media screen and (max-width: 768px) {
            .container { flex-direction: column; }
            .left, .right { width: 100%; padding: 30px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="assets/images/COTLOGO.png" alt="Logo" class="logo">
            <h1>WELCOME</h1>
            <p>BukSU COT: Capstone Repository</p>
            <p>Your gateway to academic excellence.</p>
        </div>
        <div class="right">
            <h2 style="text-align: center;">Sign in</h2>

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
    <div class="remember">
      <label>
        <input type="checkbox" name="remember" value="1"> Remember me
      </label>
      <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
    </div>
    <?php if ($siteKey): ?>
      <div class="g-recaptcha mb-3" data-sitekey="<?= $siteKey ?>"></div>
    <?php endif; ?>
                <button type="submit">Sign in</button>

                <div class="divider"><span>or</span></div>

                <a class="google-btn" href="googleAuth/google-login.php">
                    <img src="assets/images/google.png" alt="Google Logo"> Sign in with Google
                </a>

                <div class="signup-text">Don't have an account? <a href="signup.php">Sign Up</a></div>
            </form>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href) {
                    e.preventDefault();
                    document.body.style.animation = 'fadeOut 0.4s forwards';
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                }
            });
        });
    </script>
</body>
</html>
