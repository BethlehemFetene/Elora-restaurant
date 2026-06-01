<?php
require_once "db/connection.php";
require_once "includes/auth.php";
require_once "includes/mail_helper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';
$otp_sent = false;
$email_for_otp = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    
    // Check if email exists (case-insensitive)
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = "No account found with that email address.";
    } else {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete old requests
        $delete = $conn->prepare("DELETE FROM password_resets WHERE LOWER(email) = LOWER(?)");
        $delete->bind_param("s", $email);
        $delete->execute();
        
        // Insert new OTP with MySQL expiration (10 minutes from now)
        $insert = $conn->prepare("INSERT INTO password_resets (email, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        $insert->bind_param("ss", $email, $otp);
        $insert->execute();
        
        // Email content
        $subject = "Password Reset OTP";
        $plainBody = "Hello,\n\nYour OTP for password reset is: $otp\n\nThis OTP expires in 10 minutes.\n\nIf you didn't request this, ignore this email.";
        $htmlBody = "<p>Hello,</p><p>Your OTP for password reset is: <strong style='font-size:24px;'>$otp</strong></p><p>This OTP expires in 10 minutes.</p><p>If you didn't request this, ignore this email.</p>";
        
        $emailResult = sendMail($email, $subject, $plainBody, $htmlBody);
        
        if ($emailResult['success']) {
            $_SESSION['reset_email'] = $email; // store for later
            $message = "A 6-digit OTP has been sent to your email address. It expires in 10 minutes.";
            $otp_sent = true;
            $email_for_otp = $email;
        } else {
            $error = "Failed to send OTP. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include("includes/navbar.php"); ?>
<div class="container">
    <?php if($otp_sent): ?>
        <form method="POST" action="verify_otp.php">
            <h1>Enter OTP</h1>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_for_otp); ?>">
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6" pattern="[0-9]{6}">
            <button type="submit">Verify OTP</button>
            <p><a href="forgot_password.php">Request new OTP</a></p>
        </form>
    <?php else: ?>
        <form method="POST">
            <h1>Forgot Password</h1>
            <?php if($message) echo "<div class='success-msg'>$message</div>"; ?>
            <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
            <input type="email" name="email" placeholder="Your Registered Email" required>
            <button type="submit" name="submit">Send OTP</button>
            <p><a href="login.php">Back to Login</a></p>
        </form>
    <?php endif; ?>
</div>
<?php include("includes/footer.php"); ?>
</body>
</html>