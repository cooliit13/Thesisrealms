<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - COT Thesis Realms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #E3DCD9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            background: #fff;
            width: 900px;
            max-width: 95%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            animation: slideIn 0.7s ease forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
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

        .left img.logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .left h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .left p {
            line-height: 1.6;
        }

        .right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #000000;
        }

        .right h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
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
        }

        .signup-text {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .signup-text a {
            color: #D97706;
            text-decoration: none;
        }

        .alert-error {
            padding: 10px;
            background-color: #f8d7da;
            color: #581413;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left, .right {
                flex: none;
                width: 100%;
                padding: 30px;
            }
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
             <h2 style="text-align: center;">Create an Account</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="signup_validate.php" method="POST">
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Buksu Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Re-enter Password" required>

                <button type="submit">Sign Up</button>

                <div class="signup-text">Already have an account? <a href="login.php">Sign In</a></div>
            </form>
        </div>
    </div>
</body>
</html>
