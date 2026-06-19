<?php

session_start();
include("../db/connection.php");

if (!isset($_SESSION['logged_in'])) {
    header("Location:../login.php");
    exit;
}

// Delete review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM ratings WHERE id=$id");
    header("Location:manage_ratings.php");
    exit;
}

// Fetch all reviews
$query  = "SELECT * FROM ratings ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Stats
$stats_q = "SELECT AVG(rating) as avg_r, COUNT(*) as total FROM ratings";
$stats_r = mysqli_query($conn, $stats_q);
$stats   = mysqli_fetch_assoc($stats_r);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="../assets/js/main.js"></script>
</head>

<body>

    <div class="container">

        <div class="admin-header">
            <h1><i class="fas fa-star"></i> Manage Reviews</h1>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <!-- Stats -->
        <div class="seating-stats" style="margin-bottom:30px;">
            <div class="stat-pill">
                <span class="stat-num"><?php echo $stats['total']; ?></span>
                <span class="stat-label">Total Reviews</span>
            </div>
            <div class="stat-pill stat-available">
                <span class="stat-num"><?php echo $stats['total'] > 0 ? round($stats['avg_r'], 1) : '—'; ?></span>
                <span class="stat-label">Avg Rating</span>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Food</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="7" class="empty-row">No reviews yet.</td></tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?php echo $i <= $row['rating'] ? 'fas fa-star star-filled-sm' : 'far fa-star'; ?>" style="color:<?php echo $i <= $row['rating'] ? '#f59e0b' : '#ccc'; ?>;font-size:0.85rem;"></i>
                            <?php endfor; ?>
                        </td>
                        <td class="special-req-cell"><?php echo htmlspecialchars($row['review']); ?></td>
                        <td><?php echo isset($row['created_at']) && $row['created_at'] ? date('M j, Y', strtotime($row['created_at'])) : '—'; ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" title="Delete"
                               onclick="return confirm('Delete this review?')">
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
