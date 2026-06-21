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
    <title>Forgot Password — Elora Restaurant</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<nav class="main-nav">
    <a href="index.php" class="nav-logo">Elora Restaurant</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#menu">Menu</a></li>
        <li><a href="index.php#reservation">Reservation</a></li>
        <li><a href="cart.php">Cart</a></li>
    </ul>
    <a href="index.php#reservation" class="nav-cta">Book a Table</a>
</nav>

<div class="auth-page">
    <div class="auth-card">
        <?php if($otp_sent): ?>
            <h1 class="auth-title">Enter OTP</h1>
            <p class="auth-subtitle">Check your email for the 6-digit code</p>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
            <form method="POST" action="verify_otp.php">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_for_otp); ?>">
                <input type="text" name="otp" placeholder="6-digit OTP" required maxlength="6" pattern="[0-9]{6}">
                <button type="submit">Verify OTP</button>
                <p class="auth-switch"><a href="forgot_password.php">Request new OTP</a></p>
            </form>
        <?php else: ?>
            <h1 class="auth-title">Forgot Password</h1>
            <p class="auth-subtitle">Enter your email to receive a reset code</p>
            <?php if($message) echo "<div class='success-msg'>$message</div>"; ?>
            <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Your registered email" required>
                <button type="submit" name="submit">Send OTP</button>
                <p class="auth-switch"><a href="login.php">Back to Login</a></p>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer><p><span>Elora Restaurant</span> &nbsp;&mdash;&nbsp; All Rights Reserved &copy; 2026</p></footer>

</body>
</html>