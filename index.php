<?php
session_start();
include("db/connection.php");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Restaurant Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="assets/js/main.js"></script>

</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <section class="hero">
        <h1>Welcome To Our Restaurant</h1>
    </section>

    <div class="container">
        <h1>Featured Foods</h1>

        <div class="cards">

            <?php

            $query = "SELECT * FROM menu_items";

            $result = mysqli_query($conn, $query);

            if ($result) { while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <div class="card">

                    <div class="card-img-wrap">
                        <img src="assets/images/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>">
                    </div>

                    <div class="card-body">
                        <h2><?php echo htmlspecialchars($row['food_name']); ?></h2>
                        <p class="card-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="card-footer">
                            <span class="card-price"><?php echo number_format($row['price'], 2); ?> <small>ETB</small></span>
                            <a href="cart.php?add=<?php echo $row['id']; ?>">
                                <button>+ Add</button>
                            </a>
                        </div>
                    </div>

                </div>

            <?php }} ?>

        </div>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>