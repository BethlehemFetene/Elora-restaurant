<?php

include("db/connection.php");

$id = intval($_GET['id']);

$query = "SELECT * FROM menu_items WHERE id='$id'";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {

    $customer_name = $_POST['customer_name'];

    $quantity = $_POST['quantity'];

    $food_name = $row['food_name'];

    $price = $row['price'];

    $total_price = $price * $quantity;

    $query2 = "INSERT INTO orders(customer_name,food_name,quantity,total_price)

               VALUES('$customer_name','$food_name','$quantity','$total_price')";

    mysqli_query($conn, $query2);

    echo "<script>alert('Order Placed Successfully')</script>";
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Order Food</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <div class="card">

            <img src="assets/images/<?php echo trim($row['image']); ?>"
                class="food-image">

            <div class="card-content">

                <h2><?php echo $row['food_name']; ?></h2>

                <p><?php echo $row['description']; ?></p>

                <p class="price">

                    <?php echo $row['price']; ?> ETB

                </p>

            </div>

        </div>

        <br><br>

        <form method="POST">

            <h1>Place Your Order</h1>

            <input type="text"
                name="customer_name"
                placeholder="Your Name"
                required>

            <input type="number"
                name="quantity"
                placeholder="Quantity"
                required>

            <button type="submit" name="submit">

                Confirm Order

            </button>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>