<?php

session_start();

include("db/connection.php");

if (isset($_POST['login'])) {

    $email = $_POST['email'];

    $password = $_POST['password'];

    $query = "SELECT * FROM users 
              WHERE email='$email' 
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $_SESSION['user'] = $email;

        $_SESSION['logged_in'] = true;

        header("Location:index.php");
        exit;
    } else {

        echo "<script>alert('Invalid Email or Password')</script>";
    }
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

        <form method="POST">

            <h1>Login</h1>

            <input type="email"
                name="email"
                placeholder="Enter Email"
                required>

            <input type="password"
                name="password"
                placeholder="Enter Password"
                required>

            <button type="submit" name="login">

                Login

            </button>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>