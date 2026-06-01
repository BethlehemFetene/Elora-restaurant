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
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="assets/js/main.js"></script>
</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST">
        <h1>Register</h1>
        <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <button type="submit" name="register">Register</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
