<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>

    <div class="logo">
        Restaurant RMS
    </div>

    <ul>

        <li><a href="index.php">Home</a></li>

        <li><a href="menu.php">Menu</a></li>

        <li><a href="reservation.php">Reservation</a></li>

        <li><a href="ratings.php">Ratings</a></li>

        <?php if (isset($_SESSION['logged_in'])): ?>

            <li><a href="logout.php">Logout</a></li>

        <?php else: ?>

            <li><a href="register.php">Register</a></li>

            <li><a href="login.php">Login</a></li>

        <?php endif; ?>

    </ul>

</nav>