<?php 
session_start();
require 'includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_code= rand (100000, 999999);
        $update = $pdo ->prepare("UPDATE user SET reset_code = ? WHERE email = ?");
        $update->execute([$reset_code, $email]);
        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail ->Username='cooliit13@gmail.com'; // SMTP username
            $mail->Password = 'wymg wtxx ypvq ukia'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            $mail->setFrom('cooliit13@gmail.com', 'Armando B. Sagayoc Jr');  // Sender's email and name
            $mail->addAddress($email, 'User'); // Add a recipient
            $mail ->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "
            <div style='background color-white; padding: 20px; font-family: Arial, sans-serif, border-radius: 5px;'>
                <h2>Password Reset Code</h2>
                <p> Hello, Use the code below to reset your password! $reset_code</p>
                <p>If you did not request this, please ignore this email.</p>";
                $mail->AltBody="Hello user! use the code to reset: {$reset_code}";
                $mail->send();

                $_SESSION['email_sent'] = true;
                $_SESSION['success'] = "A verification code has been sent to your email";
                header('Location: send-code.php');
    

        // Code to send the reset password link to the user's email
        $_SESSION['success'] = "A reset password link has been sent to your email.";
        header('Location: send-code.php');
        exit();

    }
    catch (Exception $e) {
            $_SESSION['error'] = "Failed to send email. Please try again later.";
            header('Location: forgot-password.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found in our records.";
        header('Location: forgot-password.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>login</title>
        <link rel="stylesheet"href="styles/forgotpass-styles.css">
        <meta charset="utf-8">
    </head>
    <body>
        <div class="container">
            <div class="card">
            <img src="images/COTLOGO.png" alt="Logo" class="logo">
            <h2>Please enter your Email to continue.</h2>
                    <?php if (isset($_SESSION['error'])):?>
                            <div class= "alert aler-danger" role="alert">
                                <?=$_SESSION['error'];unset($_SESSION['success']);?>
                        </div>
                        <?php endif;?>
                        <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_SESSION['success']; unset ($_SESSION['success']);?>
                    </div>
                    <?php endif; ?>
                    <form action = "forgot-password.php" method = 'POST'>
                        <input type="email" placeholder="Enter Email" name="email">
                        <button type="submit"><a>Send Code</a></button>
                       
                    </form>
            </div>
        </div>
    </body>
</html>

