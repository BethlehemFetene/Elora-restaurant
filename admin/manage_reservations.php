<?php

include("../db/connection.php");

?>

<!DOCTYPE html>

<html>

<head>

    <title>Manage Reservations</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="container">

        <h1>Manage Reservations</h1>

        <table border="1" cellpadding="10" width="100%">

            <tr>

                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>Guests</th>

            </tr>

            <?php

            $query = "SELECT * FROM reservations";

            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <tr>

                    <td><?php echo $row['id']; ?></td>

                    <td><?php echo $row['customer_name']; ?></td>

                    <td><?php echo $row['phone']; ?></td>

                    <td><?php echo $row['reservation_date']; ?></td>

                    <td><?php echo $row['reservation_time']; ?></td>

                    <td><?php echo $row['guests']; ?></td>

                </tr>

            <?php } ?>

        </table>

    </div>

</body>

</html>