<?php

session_start();

include("../db/connection.php");

if(!isset($_SESSION['logged_in'])){

    header("Location:../login.php");

}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container">

    <h1>Admin Dashboard</h1>

    <br>

    <a href="add_menu.php">
        <button>Add Menu Item</button>
    </a>

    <a href="manage_orders.php">
        <button>Manage Orders</button>
    </a>

    <a href="manage_reservations.php">
        <button>Manage Reservations</button>
    </a>

    <br><br>

    <h2>Menu Items</h2>

    <table border="1" cellpadding="10" width="100%">

        <tr>

            <th>ID</th>
            <th>Food Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Price</th>
            <th>Action</th>

        </tr>

        <?php

        $query = "SELECT * FROM menu_items";

        $result = mysqli_query($conn, $query);

        while($row = mysqli_fetch_assoc($result)){

        ?>

        <tr>

            <td><?php echo $row['id']; ?></td>

            <td><?php echo $row['food_name']; ?></td>

            <td><?php echo $row['description']; ?></td>

            <td>

                <img src="../assets/images/<?php echo $row['image']; ?>"
                     width="80">

            </td>

            <td><?php echo $row['price']; ?> ETB</td>

            <td>

                <a href="delete_menu.php?id=<?php echo $row['id']; ?>">

                    <button>Delete</button>

                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>

</html>