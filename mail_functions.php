<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/mail_config.php';

function sendRejectionEmail($to, $filename, $reason) {
    global $config;
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Thesis Submission Rejected - Thesis Realm';

        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width:600px;margin:auto;border:1px solid #ddd;padding:20px;'>
                <h2 style='color:#721c24;background:#f8d7da;padding:10px;text-align:center;'>Thesis Submission Rejected</h2>
                <p>Dear User,</p>
                <p>We regret to inform you that your thesis submission <strong>\"$filename\"</strong> has been rejected.</p>
                <p><strong>Reason for rejection:</strong></p>
                <p>$reason</p>
                <p>Please review the submission guidelines and consider uploading a revised version.</p>
                <p>Best regards,<br>Thesis Realm Administration</p>
                <hr>
                <p style='font-size:12px;color:#999;text-align:center;'>This is an automated email. Do not reply.</p>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
