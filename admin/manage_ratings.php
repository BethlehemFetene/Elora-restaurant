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
    <title>Manage Reviews — Elora Restaurant</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <?php include('admin_navbar.php'); ?>

    <div style="max-width:1000px;margin:56px auto;padding:0 40px;">

        <span style="font-size:11px;letter-spacing:0.24em;text-transform:uppercase;color:#c0392b;font-weight:600;display:block;margin-bottom:8px;">Admin Panel</span>
        <h1 style="font-family:Georgia,serif;font-size:34px;color:white;font-weight:400;margin-bottom:32px;">Manage Reviews</h1>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:36px;">
            <div style="background:#0f2027;border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:22px 20px;border-left:3px solid #c0392b;">
                <div style="font-family:Georgia,serif;font-size:30px;color:white;line-height:1;"><?php echo $stats['total']; ?></div>
                <div style="font-size:11px;letter-spacing:0.16em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-top:8px;">Total Reviews</div>
            </div>
            <div style="background:#0f2027;border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:22px 20px;border-left:3px solid #c0392b;">
                <div style="font-family:Georgia,serif;font-size:30px;color:white;line-height:1;"><?php echo $stats['total'] > 0 ? round($stats['avg_r'], 1) : '—'; ?> <span style="font-size:18px;color:#c0392b;">★</span></div>
                <div style="font-size:11px;letter-spacing:0.16em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-top:8px;">Average Rating</div>
            </div>
        </div>

        <!-- Table -->
        <div style="background:#0f2027;border:1px solid rgba(255,255,255,0.08);border-radius:6px;overflow:hidden;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:700px;">
                <thead>
                    <tr style="background:#13262f;">
                        <?php foreach (['ID', 'Customer', 'Food', 'Rating', 'Review', 'Action'] as $h): ?>
                            <th style="padding:14px 18px;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,0.4);text-align:left;border-bottom:1px solid rgba(255,255,255,0.08);font-weight:600;"><?php echo $h; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="6" style="padding:32px;text-align:center;color:rgba(255,255,255,0.25);font-style:italic;">No reviews yet.</td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            <td style="padding:13px 18px;font-size:13px;color:rgba(255,255,255,0.5);"><?php echo $row['id']; ?></td>
                            <td style="padding:13px 18px;font-size:13px;color:rgba(255,255,255,0.75);"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td style="padding:13px 18px;font-size:13px;color:rgba(255,255,255,0.75);"><?php echo htmlspecialchars($row['food_name']); ?></td>
                            <td style="padding:13px 18px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span style="color:<?php echo $i <= $row['rating'] ? '#c0392b' : 'rgba(255,255,255,0.2)'; ?>;font-size:15px;">★</span>
                                <?php endfor; ?>
                            </td>
                            <td style="padding:13px 18px;font-size:13px;color:rgba(255,255,255,0.65);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($row['review']); ?></td>
                            <td style="padding:13px 18px;">
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this review?')" style="border:1px solid rgba(192,57,43,0.4);color:#c0392b;background:transparent;padding:6px 14px;border-radius:3px;font-size:12px;text-decoration:none;letter-spacing:0.06em;transition:all 0.2s;" onmouseover="this.style.background='#c0392b';this.style.color='white';" onmouseout="this.style.background='transparent';this.style.color='#c0392b';">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>