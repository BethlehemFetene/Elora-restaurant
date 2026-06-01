<?php
session_start();
require_once "db/connection.php";
require_once "includes/auth.php";

// Check if user has verified OTP
if (!isset($_SESSION['reset_verified_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_verified_email'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $new_password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // 1. Validate length
    if (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } 
    // 2. Check confirmation
    elseif ($new_password !== $confirm) {
        $error = "Passwords do not match.";
    } 
    else {
        // 3. Fetch current password hash from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $current_hash = $user['password'];
        
        // 4. Check if new password is same as old
        if (password_verify($new_password, $current_hash)) {
            $error = "New password cannot be the same as your current password. Please choose a different one.";
        } else {
            // 5. Hash new password and update
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $hashed, $email);
            if ($update->execute()) {
                // Clear session and redirect to login
                unset($_SESSION['reset_verified_email']);
                session_destroy(); // optional, but good practice
                header("Location: login.php?reset=success");
                exit;
            } else {
                $error = "Failed to update password. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include("includes/navbar.php"); ?>
<div class="container">
    <form method="POST">
        <h1>Set New Password</h1>
        <?php if($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <input type="password" name="password" placeholder="New Password (min 6 chars)" required minlength="6">
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit" name="reset">Change Password</button>
    </form>
</div>
<?php include("includes/footer.php"); ?>
</body>
</html>