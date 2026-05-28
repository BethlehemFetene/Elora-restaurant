<?php

session_start();

include("db/connection.php");

if (isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "INSERT INTO users(full_name, email, password)
              VALUES('$fullname','$email','$password')";

    mysqli_query($conn, $query);

    header("Location:login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>
</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST">

            <h1>Register</h1>

            <input type="text" name="fullname" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="register">Register</button>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
