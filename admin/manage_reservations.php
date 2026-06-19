<?php

session_start();
include("../db/connection.php");

if (!isset($_SESSION['logged_in'])) {
    header("Location:../login.php");
    exit;
}

// Handle status updates
if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    mysqli_query($conn, "UPDATE reservations SET status='confirmed' WHERE id=$id");
    header("Location:manage_reservations.php");
    exit;
}

if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    mysqli_query($conn, "UPDATE reservations SET status='cancelled' WHERE id=$id");
    header("Location:manage_reservations.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM reservations WHERE id=$id");
    header("Location:manage_reservations.php");
    exit;
}

// Filter
$filter = isset($_GET['status']) ? $_GET['status'] : '';
$where  = '';
if ($filter && in_array($filter, ['pending', 'confirmed', 'cancelled'])) {
    $where = "WHERE r.status = '$filter'";
}

$query = "SELECT r.*, t.table_number, t.capacity, t.location
          FROM reservations r
          LEFT JOIN restaurant_tables t ON r.table_id = t.id
          $where
          ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="../assets/js/main.js"></script>
</head>

<body>

    <div class="container">

        <div class="admin-header">
            <h1><i class="fas fa-calendar-check"></i> Manage Reservations</h1>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <!-- Filters -->
        <div class="admin-filters">
            <a href="manage_reservations.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">All</a>
            <a href="?status=pending" class="filter-btn filter-pending <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="?status=confirmed" class="filter-btn filter-confirmed <?php echo $filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
            <a href="?status=cancelled" class="filter-btn filter-cancelled <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Table</th>
                        <th>Occasion</th>
                        <th>Special Request</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="11" class="empty-row">No reservations found.</td></tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="status-<?php echo isset($row['status']) ? $row['status'] : 'pending'; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo $row['reservation_date']; ?></td>
                        <td><?php echo date('g:i A', strtotime($row['reservation_time'])); ?></td>
                        <td><?php echo $row['guests']; ?></td>
                        <td>
                            <?php if (isset($row['table_number']) && $row['table_number']): ?>
                                T<?php echo $row['table_number']; ?> (<?php echo $row['capacity']; ?>p, <?php echo $row['location']; ?>)
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $occ = isset($row['occasion']) ? $row['occasion'] : 'none';
                            $occ_icons = ['birthday'=>'🎂','anniversary'=>'💍','date_night'=>'🌹','business'=>'💼','graduation'=>'🎓','other'=>'✨','none'=>'—'];
                            echo isset($occ_icons[$occ]) ? $occ_icons[$occ] . ' ' . ucfirst(str_replace('_',' ',$occ)) : '—';
                            ?>
                        </td>
                        <td class="special-req-cell">
                            <?php echo isset($row['special_request']) && $row['special_request'] ? htmlspecialchars($row['special_request']) : '—'; ?>
                        </td>
                        <td>
                            <?php
                            $status = isset($row['status']) ? $row['status'] : 'pending';
                            $status_class = 'status-badge status-' . $status;
                            ?>
                            <span class="<?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        <td class="actions-cell">
                            <?php if ($status === 'pending'): ?>
                                <a href="?confirm=<?php echo $row['id']; ?>" class="action-btn confirm-btn" title="Confirm">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="?cancel=<?php echo $row['id']; ?>" class="action-btn cancel-btn" title="Cancel">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" title="Delete"
                               onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>