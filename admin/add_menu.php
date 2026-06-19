<?php

include("../db/connection.php");

if(isset($_POST['submit'])){

    $food_name = $_POST['food_name'];

    $description = $_POST['description'];

    $price = $_POST['price'];

    $image = $_POST['image'];

    $query = "INSERT INTO menu_items(food_name,description,price,image)

              VALUES('$food_name','$description','$price','$image')";

    mysqli_query($conn, $query);

    header("Location:dashboard.php");

}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Add Menu Item</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="admin-hero">

    <div class="admin-overlay">

        <h1>Add New Menu Item</h1>

        <p>Create beautiful food listings for customers</p>

    </div>

</div>

<div class="container">

    <form method="POST" class="menu-form">

        <input type="text"
               name="food_name"
               placeholder="Food Name"
               required>

        <textarea
               name="description"
               placeholder="Food Description"
               required></textarea>

        <input type="number"
               name="price"
               placeholder="Price"
               required>

        <input type="text"
               name="image"
               placeholder="Image Name e.g burger.jpg"
               required>

        <button type="submit" name="submit">

            Add Menu Item

        </button>

    </form>

</div>

</body>

</html>