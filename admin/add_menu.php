<?php

include("../db/connection.php");

if(isset($_POST['submit'])){

    $food_name = $_POST['food_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../assets/images/" . $image);

    $query = "INSERT INTO menu_items(food_name, description, price, image)
              VALUES('$food_name','$description','$price','$image')";

    mysqli_query($conn, $query);

    header("Location:dashboard.php");
    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Add Menu</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js"></script>

</head>

<body>

<div class="container">

<form method="POST" enctype="multipart/form-data">

    <h1>Add Menu Item</h1>

    <input type="text"
           name="food_name"
           placeholder="Food Name"
           required>

    <textarea name="description"
              placeholder="Description"
              required></textarea>

    <input type="number"
           name="price"
           placeholder="Price"
           required>

    <input type="file"
           name="image"
           accept="image/*"
           required>

    <button type="submit" name="submit">

        Add Food

    </button>

</form>

</div>

</body>

</html>
