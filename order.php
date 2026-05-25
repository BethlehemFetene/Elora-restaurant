<?php

include("db/connection.php");

if (isset($_POST['submit'])) {

    $customer_name = $_POST['customer_name'];
    $food_name = $_POST['food_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $total_price = $quantity * $price;

    $query = "INSERT INTO orders(customer_name, food_name, quantity, total_price)
              VALUES('$customer_name','$food_name','$quantity','$total_price')";

    mysqli_query($conn, $query);

    echo "<script>alert('Order Submitted Successfully')</script>";
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Order Food</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST">

            <h1>Order Food</h1>

            <input type="text" name="customer_name" placeholder="Enter Your Name" required>

            <input type="text" name="food_name" value="<?php echo $_GET['food']; ?>" readonly>

            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" name="price" placeholder="Price" required>

            <button type="submit" name="submit">Submit Order</button>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>