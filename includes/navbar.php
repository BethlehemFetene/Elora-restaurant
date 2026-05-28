<?php if(!isset($_SESSION)) session_start(); ?>
<nav>

    <div class="logo">
        Restaurant RMS
    </div>

    <ul>

        <li><a href="index.php">Home</a></li>

        <li><a href="menu.php">Menu</a></li>

        <li><a href="reservation.php">Reservation</a></li>

        <li><a href="ratings.php">Ratings</a></li>

        <li>
            <a href="cart.php">🛒 Cart
                <?php
                $cartCount = 0;
                if(isset($_SESSION['cart'])){
                    $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
                }
                if($cartCount > 0) echo '<span class="cart-badge">' . $cartCount . '</span>';
                ?>
            </a>
        </li>

        <?php if (isset($_SESSION['logged_in'])): ?>

            <li><a href="logout.php">Logout</a></li>

        <?php else: ?>

            <li><a href="register.php">Register</a></li>

            <li><a href="login.php">Login</a></li>

        <?php endif; ?>

        <li><button onclick="toggleDarkMode()" id="theme-btn">🌙 Dark</button></li>

    </ul>

</nav>
