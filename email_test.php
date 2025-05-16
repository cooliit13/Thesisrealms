<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if not using Composer

// Function to send email
function sendTestEmail() {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                  // Set the SMTP server to Gmail
        $mail->SMTPAuth   = true;                              // Enable SMTP authentication
        $mail->Username   = 'cooliit13@gmail.com';            // SMTP username (your Gmail address)
        $mail->Password   = 'wymg wtxx ypvq ukia';             // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // Enable TLS encryption
        $mail->Port       = 587;                               // TCP port to connect to (587 for TLS)

        //Recipients
        $mail->setFrom('cooliit13@gmail.com', 'Test Sender');
        $mail->addAddress('jejebahian12@gmail.com');      // Recipient email

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Test Email from PHP';
        $mail->Body    = '<h1>Test Email</h1><p>This is a test email sent from PHP using PHPMailer.</p>';

        // Send email
        if ($mail->send()) {
            echo 'Test email sent successfully!';
        } else {
            echo 'Failed to send test email.';
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Run the function
sendTestEmail();
?>
