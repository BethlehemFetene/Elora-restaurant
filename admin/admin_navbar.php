<?php $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="admin-nav">
    <div class="admin-nav-logo">
        <a href="../index.php">Elora Restaurant</a>
        <span>Admin</span>
    </div>
    <ul class="admin-nav-links">
        <li><a href="dashboard.php" class="<?php echo $current === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="add_menu.php" class="<?php echo $current === 'add_menu.php' ? 'active' : ''; ?>">Add Item</a></li>
        <li><a href="manage_menu.php" class="<?php echo in_array($current, ['manage_menu.php', 'edit_menu.php']) ? 'active' : ''; ?>">Menu</a></li>
        <li><a href="manage_orders.php" class="<?php echo $current === 'manage_orders.php' ? 'active' : ''; ?>">Orders</a></li>
        <li><a href="manage_reservations.php" class="<?php echo $current === 'manage_reservations.php' ? 'active' : ''; ?>">Reservations</a></li>
        <li><a href="manage_ratings.php" class="<?php echo $current === 'manage_ratings.php' ? 'active' : ''; ?>">Reviews</a></li>
    </ul>
    <a href="../logout.php" class="admin-nav-logout">Logout</a>
</nav>
