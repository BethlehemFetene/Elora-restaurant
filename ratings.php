<?php

include("db/connection.php");

if (isset($_POST['submit'])) {

    $customer_name = $_POST['customer_name'];
    $food_name = $_POST['food_name'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $query = "INSERT INTO ratings(customer_name, food_name, rating, review)
              VALUES('$customer_name','$food_name','$rating','$review')";

    mysqli_query($conn, $query);

    echo "<script>alert('Rating Submitted')</script>";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Ratings</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <form method="POST">

            <h1>Food Ratings</h1>

            <input type="text" name="customer_name" placeholder="Your Name">

            <input type="text" name="food_name" placeholder="Food Name">

            <select name="rating">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
            </select>

            <textarea name="review" placeholder="Write Review"></textarea>

            <button type="submit" name="submit">Submit Rating</button>

        </form>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>