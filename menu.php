<?php include("db/connection.php"); ?>

<!DOCTYPE html>
<html?>

    <head>

        <title>Menu</title>

        <link rel="stylesheet" href="assets/css/style.css">

    </head>

    <body>

        <?php include("includes/navbar.php"); ?>

        <div class="container">

            <h1>Our Full Menu</h1>

            <div class="cards">

                <?php
                $query = "SELECT * FROM menu_items";

                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {

                ?>

                    <div class="card">

                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd">

                        <h2><?php echo $row['food_name']; ?></h2>

                        <p><?php echo $row['description']; ?></p>

                        <p><?php echo $row['price']; ?> ETB</p>

                        <a href="order.php?food=<?php echo $row['food_name']; ?>">
                            <button>Order Now</button>
                        </a>

                    </div>

                <?php } ?>

            </div>

        </div>

        <?php include("includes/footer.php"); ?>

    </body>

    </html>