<?php
session_start();
include("../db/connection.php");
if (!isset($_SESSION['logged_in'])) {
    header("Location:../login.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu — Elora Restaurant</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <span class="admin-page-label">Menu Management</span>
            <h1>Manage Menu Items</h1>
        </div>
        <a href="add_menu.php" class="btn-back" style="margin-top:8px;">Add New Item</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dish</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result || mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="6" class="empty-row">No menu items found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo (int) $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo number_format($row['price'], 2); ?> ETB</td>
                            <td><?php echo htmlspecialchars($row['image']); ?></td>
                            <td>
                                <a class="action-btn" href="edit_menu.php?id=<?php echo (int) $row['id']; ?>">Edit</a>
                                <a class="delete-btn" href="delete_menu.php?id=<?php echo (int) $row['id']; ?>" onclick="return confirm('Delete this menu item?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
