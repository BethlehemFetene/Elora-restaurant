<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($recipient, $subject, $plainBody, $htmlBody = '') {
    $config = include __DIR__ . '/../config/mail_config.php';
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];
        
        
        // Use the dedicated sender email (can be different from SMTP username)
        $mail->setFrom($config['sender_email'], $config['sender_name']);
        $mail->addAddress($recipient);
        
        $mail->isHTML(!empty($htmlBody));
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody ?: $plainBody;
        $mail->AltBody = $plainBody;
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent'];
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}
?>