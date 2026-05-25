<?php

include("db/connection.php");

if (isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "INSERT INTO users(fullname,email,password)
              VALUES('$fullname','$email','$password')";

    mysqli_query($conn, $query);

    echo "Registration Successful";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <form method="POST">

        <h1>Register</h1>

        <input type="text" name="fullname" placeholder="Full Name">

        <input type="email" name="email" placeholder="Email">

        <input type="password" name="password" placeholder="Password">

        <button type="submit" name="register">Register</button>

    </form>

</body>

</html>