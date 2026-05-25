<?php

include("../db/connection.php");

?>

<!DOCTYPE html>

<html>

<head>

    <title>Manage Orders</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="container">

        <h1>Manage Orders</h1>

        <table border="1" cellpadding="10" width="100%">

            <tr>

                <th>ID</th>
                <th>Customer</th>
                <th>Food</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Date</th>

            </tr>

            <?php

            $query = "SELECT * FROM orders";

            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <tr>

                    <td><?php echo $row['id']; ?></td>

                    <td><?php echo $row['customer_name']; ?></td>

                    <td><?php echo $row['food_name']; ?></td>

                    <td><?php echo $row['quantity']; ?></td>

                    <td><?php echo $row['total_price']; ?></td>

                    <td><?php echo $row['order_date']; ?></td>

                </tr>

            <?php } ?>

        </table>

    </div>

</body>

</html>