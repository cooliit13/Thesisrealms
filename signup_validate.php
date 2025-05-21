<?php
session_start();
require 'includes/db.php';

// Include the email sending function
function sendVerificationEmail($recipient, $subject, $message, $recipientName = '') {
    
    
    require_once 'vendor/autoload.php'; 
    
    // Load environment variables
    // Default values
    $mailHost = 'smtp.example.com';  
    $mailUsername = 'your_email@example.com'; 
    $mailPassword = 'your_password'; 
    $mailPort = 587;
    $mailFromAddress = 'noreply@cotrepository.com'; 
    $mailFromName = 'COT Repository';
    
    // Try to load from .env 
    try {
        if (file_exists(__DIR__ . '/.env')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            
            // Override with .env values if they exist
            $mailHost = $_ENV['MAIL_HOST'] ?? $mailHost;
            $mailUsername = $_ENV['MAIL_USERNAME'] ?? $mailUsername;
            $mailPassword = $_ENV['MAIL_PASSWORD'] ?? $mailPassword;
            $mailPort = $_ENV['MAIL_PORT'] ?? $mailPort;
            $mailFromAddress = $_ENV['MAIL_FROM_EMAIL'] ?? $mailFromAddress;
            $mailFromName = $_ENV['MAIL_FROM_NAME'] ?? $mailFromName;
        }
    } catch (Exception $e) {
        // Just log the error and continue with default values
        error_log("Error loading .env file: " . $e->getMessage());
    }
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $mailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $mailUsername;
        $mail->Password = $mailPassword;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailPort;
        
        // Recipients
        $mail->setFrom($mailFromAddress, $mailFromName);
        $mail->addAddress($recipient, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message));
        
        $mail->send();
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: signup.php');
        exit();
    }

    // Restrict email to @buksu.edu.ph domain
    if (!preg_match('/^[\w.+-]+@buksu\.edu\.ph$/', $email)) {
        $_SESSION['error'] = "Only Buksu Teachers email addresses are allowed! Go to Users dashboard if you want to access files.";
        header('Location: signup.php');
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username already exists.";
        header('Location: signup.php');
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email is already registered.";
        header('Location: signup.php');
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate email verification token
    $verification_token = bin2hex(random_bytes(16));

    // Default values
    $status = 'inactive';
    $role = 'Teacher'; // Force all signups via this form to be Teacher

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO user (firstname, lastname, username, email, password, status, verification_token, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword, $status, $verification_token, $role])) {
        // Get site URL from .env or define it here
        $siteUrl = isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : 'http://localhost/Sagayoc';
        
        // Create activation link
        $activation_link = $siteUrl . "/activate.php?email=" . urlencode($email) . "&token=" . urlencode($verification_token);
        
        // Create email message
        $subject = "Activate Your COT Repository Account";
        $message = "
        <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;'>
            <h2 style='color: #581413;'>COT Repository Activation</h2>
            <p>Hi <strong>$firstname</strong>,</p>
            <p>Congratulations on creating your account! Please activate your account by clicking the button below:</p>
            <a href='$activation_link' style='display:inline-block;background-color:#D97706;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;margin-top:15px;'>Activate Account</a>
            <p>If you didn't sign up, please ignore this email.</p>
            <p>Regards,<br>BukSU College of Technologies</p>
        </div>
        ";
        
        // Send email using the function
        $mail_result = sendVerificationEmail($email, $subject, $message, "$firstname $lastname");
        
        if ($mail_result['success']) {
            $_SESSION['success'] = "Account created successfully. Please check your email to activate your account.";
        } else {
            // Log the email error
            error_log("Failed to send verification email to $email: " . $mail_result['message']);
            
            // Store verification info in session for easier testing
            $_SESSION['activation_email'] = $email;
            $_SESSION['activation_token'] = $verification_token;
            
            $_SESSION['success'] = "Account created successfully. Please check your email to activate your account. If you don't receive the email, please contact admin.";
        }
        
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Account creation failed. Please try again later.";
        header("Location: signup.php");
        exit();
    }
}