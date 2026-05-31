<?php

include("db/connection.php");

$success = false;

$today    = date('Y-m-d');
$maxDate  = date('Y-m-d', strtotime('+1 month'));

if (isset($_POST['submit'])) {

    $name   = $_POST['name'];
    $phone  = $_POST['phone'];
    $date   = $_POST['date'];
    $time   = $_POST['time'];
    $guests = $_POST['guests'];

    $query = "INSERT INTO reservations(customer_name, phone, reservation_date, reservation_time, guests)
              VALUES('$name','$phone','$date','$time','$guests')";

    mysqli_query($conn, $query);

    $success = true;
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Reservation</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST" onsubmit="return validateReservation()">

            <h1>Reserve A Table</h1>

            <input type="text" id="name" name="name" placeholder="Enter Name" required>

            <input type="tel"
                   id="phone"
                   name="phone"
                   placeholder="+251XXXXXXXXX"
                   pattern="\+251[79][0-9]{8}"
                   maxlength="13"
                   required>

            <input type="date"
                   id="res-date"
                   name="date"
                   min="<?php echo $today; ?>"
                   max="<?php echo $maxDate; ?>"
                   required>

            <input type="time"
                   name="time"
                   min="08:00"
                   max="21:00"
                   required>

            <input type="number" name="guests" placeholder="Number of Guests" min="1" required>

            <button type="submit" name="submit">Reserve</button>

            <?php if($success): ?>
                <p class="success-msg">Reservation submitted successfully!</p>
            <?php endif; ?>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>
