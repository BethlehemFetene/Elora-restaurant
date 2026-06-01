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

    <title>Login</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <?php if($success_message): ?>
        <div class="success-msg" style="margin-bottom: 20px;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <h1>Login</h1>
        <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
        <?php if($reset_success): ?>
            <div class="success-msg"><?php echo $reset_success; ?></div>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>