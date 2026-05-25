<?php

include("db/connection.php");

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];

    $query = "INSERT INTO reservations(customer_name, phone, reservation_date, reservation_time, guests)
              VALUES('$name','$phone','$date','$time','$guests')";

    mysqli_query($conn, $query);

    echo "<script>alert('Reservation Successful')</script>";
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Reservation</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST" onsubmit="return validateReservation()">

            <h1>Reserve A Table</h1>

            <input type="text" id="name" name="name" placeholder="Enter Name">

            <input type="text" name="phone" placeholder="Phone Number">

            <input type="date" name="date">
            <input type="time" name="time">

            <input type="number" name="guests" placeholder="Number of Guests">

            <button type="submit" name="submit">Reserve</button>

        </form>

    </div>

    <script src="assets/js/main.js"></script>

    <?php include("includes/footer.php"); ?>

</body>

</html>