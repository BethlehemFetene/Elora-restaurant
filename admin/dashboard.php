<?php

session_start();

include("../db/connection.php");

if (!isset($_SESSION['logged_in'])) {

    header("Location:../login.php");
    exit;
}

// KPI cards
$menu_count = 0;
$orders_count = 0;
$reservations_total = 0;
$reservations_pending = 0;
$tables_total = 0;
$ratings_total = 0;
$ratings_avg = 0;

$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM menu_items");
if ($q) {
    $menu_count = (int) mysqli_fetch_assoc($q)['c'];
}

$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM orders");
if ($q) {
    $orders_count = (int) mysqli_fetch_assoc($q)['c'];
}

$q = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending FROM reservations");
if ($q) {
    $r = mysqli_fetch_assoc($q);
    $reservations_total = (int) $r['total'];
    $reservations_pending = (int) $r['pending'];
}

$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM restaurant_tables WHERE is_active = 1");
if ($q) {
    $tables_total = (int) mysqli_fetch_assoc($q)['c'];
}

$q = mysqli_query($conn, "SELECT COUNT(*) as total, AVG(rating) as avg_r FROM ratings");
if ($q) {
    $r = mysqli_fetch_assoc($q);
    $ratings_total = (int) $r['total'];
    $ratings_avg = $ratings_total > 0 ? round((float) $r['avg_r'], 1) : 0;
}

$recent_reservations = mysqli_query(
    $conn,
    "SELECT customer_name, reservation_date, reservation_time, guests, status
     FROM reservations
     ORDER BY id DESC
     LIMIT 5"
);

$recent_reviews = mysqli_query(
    $conn,
    "SELECT customer_name, food_name, rating, created_at
     FROM ratings
     ORDER BY id DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container">

        <div class="admin-header">
            <h1><i class="fas fa-gauge-high"></i> Admin Dashboard</h1>
            <a href="../logout.php" class="btn-back"><i class="fas fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="dashboard-kpis">
            <div class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">Menu Items</span>
                <span class="dashboard-kpi-value"><?php echo $menu_count; ?></span>
            </div>
            <div class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">Orders</span>
                <span class="dashboard-kpi-value"><?php echo $orders_count; ?></span>
            </div>
            <div class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">Reservations</span>
                <span class="dashboard-kpi-value"><?php echo $reservations_total; ?></span>
                <span class="dashboard-kpi-note"><?php echo $reservations_pending; ?> pending</span>
            </div>
            <div class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">Active Tables</span>
                <span class="dashboard-kpi-value"><?php echo $tables_total; ?></span>
            </div>
            <div class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">Reviews</span>
                <span class="dashboard-kpi-value"><?php echo $ratings_total; ?></span>
                <span class="dashboard-kpi-note">Avg <?php echo $ratings_total > 0 ? $ratings_avg : '—'; ?>★</span>
            </div>
        </div>

        <div class="admin-modules-grid">
            <a class="admin-module-card" href="add_menu.php">
                <i class="fas fa-plus"></i>
                <h3>Add Menu Item</h3>
                <p>Create a new dish with description, price, and image.</p>
            </a>
            <a class="admin-module-card" href="manage_orders.php">
                <i class="fas fa-receipt"></i>
                <h3>Manage Orders</h3>
                <p>View customer orders and track order activity.</p>
            </a>
            <a class="admin-module-card" href="manage_reservations.php">
                <i class="fas fa-calendar-check"></i>
                <h3>Manage Reservations</h3>
                <p>Confirm, cancel, and monitor upcoming bookings.</p>
            </a>
            <a class="admin-module-card" href="manage_ratings.php">
                <i class="fas fa-star"></i>
                <h3>Manage Reviews</h3>
                <p>Review customer feedback and remove inappropriate posts.</p>
            </a>
        </div>

        <div class="dashboard-panels">
            <div class="dashboard-panel">
                <h2><i class="fas fa-clock"></i> Recent Reservations</h2>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Guests</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_reservations && mysqli_num_rows($recent_reservations) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($recent_reservations)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                                        <td><?php echo date('g:i A', strtotime($row['reservation_time'])); ?></td>
                                        <td><?php echo (int) $row['guests']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($row['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="empty-row">No reservations yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-panel">
                <h2><i class="fas fa-comments"></i> Recent Reviews</h2>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Food</th>
                                <th>Rating</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_reviews && mysqli_num_rows($recent_reviews) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($recent_reviews)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                                        <td><?php echo (int) $row['rating']; ?>★</td>
                                        <td><?php echo isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '—'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="empty-row">No reviews yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>