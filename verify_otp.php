<?php
session_start();
require_once "db/connection.php";
require_once "includes/auth.php";

$error = '';
$email = trim($_POST['email'] ?? $_SESSION['reset_email'] ?? '');
$otp = trim($_POST['otp'] ?? '');

// If still no email, show error
if (empty($email)) {
    die("Email not found. Please go back and request OTP again.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($otp)) {
    // Case-insensitive email match, OTP match, not used, not expired
    $stmt = $conn->prepare("SELECT id FROM password_resets WHERE LOWER(email) = LOWER(?) AND otp_code = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Mark OTP as used
        $update = $conn->prepare("UPDATE password_resets SET used = 1 WHERE LOWER(email) = LOWER(?) AND otp_code = ?");
        $update->bind_param("ss", $email, $otp);
        $update->execute();
        
        $_SESSION['reset_verified_email'] = $email;
        unset($_SESSION['reset_email']);
        
        header("Location: reset_password_form.php");
        exit;
    } else {
        // DEBUG: Check if OTP exists but expired or used
        $check = $conn->prepare("SELECT used, expires_at, NOW() as current_time FROM password_resets WHERE LOWER(email) = LOWER(?) AND otp_code = ?");
        $check->bind_param("ss", $email, $otp);
        $check->execute();
        $chk = $check->get_result()->fetch_assoc();
        if ($chk) {
            if ($chk['used']) $error = "OTP already used. Please request a new one.";
            elseif ($chk['expires_at'] < date('Y-m-d H:i:s')) $error = "OTP expired (valid until " . $chk['expires_at'] . "). Request a new one.";
            else $error = "Unknown error. Please try again.";
        } else {
            $error = "Invalid OTP. Please check and try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP — Elora Restaurant</title>
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
        <h1 class="auth-title">Verify OTP</h1>
        <p class="auth-subtitle">Enter the 6-digit code sent to your email</p>
        <?php if($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="text" name="otp" placeholder="6-digit OTP" required maxlength="6" pattern="[0-9]{6}">
            <button type="submit">Verify &amp; Continue</button>
            <p class="auth-switch"><a href="forgot_password.php">Request new OTP</a></p>
        </form>
    </div>
</div>

<footer><p><span>Elora Restaurant</span> &nbsp;&mdash;&nbsp; All Rights Reserved &copy; 2026</p></footer>

</body>
</html>