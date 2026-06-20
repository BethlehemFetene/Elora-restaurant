<?php
session_start();
include("db/connection.php");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Menu</title>

    <link rel="stylesheet" href="style.css">
    <script src="assets/js/main.js"></script>
</head>

<body class="menu-page">

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <div class="section-header">
            <span class="section-label">Our Menu</span>
            <h2>Every Dish, a Story</h2>
        </div>

        <div class="cards">

            <?php
            $query = "SELECT * FROM menu_items";

            $result = mysqli_query($conn, $query);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {

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

            <?php }
            } ?>

        </div>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>