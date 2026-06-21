<?php
session_start();
include("../db/connection.php");
if (!isset($_SESSION['logged_in'])) {
    header("Location:../login.php"); exit;
}
$result = mysqli_query($conn, 
  "SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Orders — Elora Restaurant</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div style="max-width:1100px;margin:56px auto;padding:0 40px;">
  <span style="font-size:11px;letter-spacing:0.24em;
    text-transform:uppercase;color:#c0392b;font-weight:600;
    display:block;margin-bottom:8px;">Admin Panel</span>
  <h1 style="font-family:Georgia,serif;font-size:34px;
    color:white;font-weight:400;margin-bottom:32px;">
    Manage Orders</h1>

  <div style="background:#0f2027;border:1px solid 
    rgba(255,255,255,0.08);border-radius:6px;
    overflow:hidden;overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;
      min-width:700px;">
      <thead>
        <tr style="background:#13262f;">
          <?php foreach(['ID','Customer','Food',
            'Quantity','Total','Date'] as $h): ?>
          <th style="padding:14px 18px;font-size:11px;
            letter-spacing:0.18em;text-transform:uppercase;
            color:rgba(255,255,255,0.4);text-align:left;
            border-bottom:1px solid rgba(255,255,255,0.08);
            font-weight:600;"><?php echo $h; ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if(mysqli_num_rows($result)==0): ?>
        <tr><td colspan="6" style="padding:32px;
          text-align:center;color:rgba(255,255,255,0.25);
          font-style:italic;">No orders yet.</td></tr>
        <?php endif; ?>
        <?php while($row=mysqli_fetch_assoc($result)): ?>
        <tr onmouseover="this.style.background=
          'rgba(255,255,255,0.02)'" 
          onmouseout="this.style.background='transparent'">
          <td style="padding:13px 18px;font-size:13px;
            color:rgba(255,255,255,0.4);">
            <?php echo $row['id']; ?></td>
          <td style="padding:13px 18px;font-size:13px;
            color:rgba(255,255,255,0.8);">
            <?php echo htmlspecialchars($row['customer_name']); ?>
          </td>
          <td style="padding:13px 18px;font-size:13px;
            color:rgba(255,255,255,0.75);">
            <?php echo htmlspecialchars($row['food_name']); ?>
          </td>
          <td style="padding:13px 18px;font-size:13px;
            color:rgba(255,255,255,0.72);text-align:center;">
            <?php echo $row['quantity']; ?></td>
          <td style="padding:13px 18px;font-size:14px;
            color:#c0392b;font-weight:600;">
            <?php echo number_format($row['total_price'],2); ?> ETB
          </td>
          <td style="padding:13px 18px;font-size:13px;
            color:rgba(255,255,255,0.5);">
            <?php echo $row['order_date']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
