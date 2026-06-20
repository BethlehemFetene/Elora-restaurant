<?php

session_start();

require_once "db/connection.php";
require_once "includes/auth.php";

if (isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'customer'; // default
    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);
        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $error = "Registration failed. Try again.";
        }
    }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register — Elora Restaurant</title>
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
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join us for exclusive dining</p>

        <?php if(isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email address" required>
            <input type="password" name="password" placeholder="Password (min 6 chars)" required>
            <button type="submit" name="register">Register</button>
            <p class="auth-switch">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</div>

<footer><p><span>Elora Restaurant</span> &nbsp;&mdash;&nbsp; All Rights Reserved &copy; 2026</p></footer>

</body>
</html>
