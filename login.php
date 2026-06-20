<?php

session_start();

require_once "db/connection.php";
require_once "includes/auth.php";

$success_message = '';
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success_message = "Registration successful! Please log in with your credentials.";
}
$reset_success = '';
if (isset($_GET['reset']) && $_GET['reset'] == 'success') {
    $reset_success = "Your password has been reset successfully. Please log in with your new password.";
}
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['logged_in'] = true;
            
            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    $error = "Invalid email or password.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login — Elora Restaurant</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<nav class="main-nav">
    <a href="index.php" class="nav-logo">Elora Restaurant</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#menu">Menu</a></li>
        <li><a href="index.php#reservation">Reservation</a></li>
        <li><a href="index.php#ratings">Ratings</a></li>
        <li><a href="cart.php">Cart</a></li>
    </ul>
    <a href="index.php#reservation" class="nav-cta">Book a Table</a>
</nav>

<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to your account</p>

        <?php if($success_message): ?>
            <div class="success-msg"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if($reset_success): ?>
            <div class="success-msg"><?php echo $reset_success; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email address" required>
            <input type="password" name="password" placeholder="Password" required>
            <a href="forgot_password.php" class="auth-link">Forgot Password?</a>
            <button type="submit" name="login">Login</button>
            <p class="auth-switch">Don't have an account? <a href="index.php#register">Register</a></p>
        </form>
    </div>
</div>

<footer><p><span>Elora Restaurant</span> &nbsp;&mdash;&nbsp; All Rights Reserved &copy; 2026</p></footer>

</body>
</html>