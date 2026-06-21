<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<script>
    if(localStorage.getItem('theme') === 'light'){
        document.documentElement.classList.add('light-mode');
    }
</script>
<nav>

    <div class="logo">Elora Restaurant</div>

    <ul>

        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="ratings.php">Ratings</a></li>

        <li>
            <a href="cart.php">Cart
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

        <li><button onclick="toggleDarkMode()" id="theme-btn">☀️ Light</button></li>

    </ul>

</nav>
