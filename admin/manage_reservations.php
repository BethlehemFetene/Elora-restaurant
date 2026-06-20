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
    <title>Manage Reservations — Elora Restaurant</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <?php include('admin_navbar.php'); ?>

    <div style="max-width:1300px;margin:56px auto;padding:0 40px;">

        <span style="font-size:11px;letter-spacing:0.24em;text-transform:uppercase;color:#c0392b;font-weight:600;display:block;margin-bottom:8px;">Admin Panel</span>
        <h1 style="font-family:Georgia,serif;font-size:34px;color:white;font-weight:400;margin-bottom:32px;">Manage Reservations</h1>

        <!-- Filter tabs -->
        <div style="display:flex;gap:8px;margin-bottom:28px;flex-wrap:wrap;">
            <?php
            $tabs = ['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];
            foreach ($tabs as $val => $label):
                $isActive = $filter === $val;
                $activeStyle = $isActive ? 'background:#c0392b;border-color:#c0392b;color:white;' : 'background:transparent;border-color:rgba(255,255,255,0.15);color:rgba(255,255,255,0.6);';
                $href = $val ? '?status=' . $val : 'manage_reservations.php';
            ?>
                <a href="<?php echo $href; ?>" style="padding:8px 20px;border-radius:4px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;border:1px solid;transition:all 0.2s;<?php echo $activeStyle; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Table -->
        <div style="background:#0f2027;border:1px solid rgba(255,255,255,0.08);border-radius:6px;overflow:hidden;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:1000px;">
                <thead>
                    <tr style="background:#13262f;">
                        <?php foreach (['ID', 'Name', 'Phone', 'Date', 'Time', 'Guests', 'Table', 'Occasion', 'Special Request', 'Status', 'Actions'] as $h): ?>
                            <th style="padding:14px 16px;font-size:11px;letter-spacing:0.16em;text-transform:uppercase;color:rgba(255,255,255,0.4);text-align:left;border-bottom:1px solid rgba(255,255,255,0.08);font-weight:600;white-space:nowrap;"><?php echo $h; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="11" style="padding:32px;text-align:center;color:rgba(255,255,255,0.25);font-style:italic;">No reservations found.</td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $status = isset($row['status']) ? $row['status'] : 'pending';
                        $statusStyles = [
                            'pending'   => 'background:rgba(192,57,43,0.15);color:#e74c3c;',
                            'confirmed' => 'background:rgba(39,174,96,0.15);color:#2ecc71;',
                            'cancelled' => 'background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.4);',
                        ];
                        $badgeStyle = isset($statusStyles[$status]) ? $statusStyles[$status] : $statusStyles['pending'];
                        $occ = isset($row['occasion']) ? $row['occasion'] : 'none';
                        $occ_icons = ['birthday' => '🎂', 'anniversary' => '💍', 'date_night' => '🌹', 'business' => '💼', 'graduation' => '🎓', 'other' => '✨', 'none' => '—'];
                    ?>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.4);"><?php echo $row['id']; ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.8);white-space:nowrap;"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.65);"><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.72);white-space:nowrap;"><?php echo $row['reservation_date']; ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.72);white-space:nowrap;"><?php echo date('g:i A', strtotime($row['reservation_time'])); ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.72);text-align:center;"><?php echo $row['guests']; ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.65);white-space:nowrap;">
                                <?php if (isset($row['table_number']) && $row['table_number']): ?>
                                    T<?php echo $row['table_number']; ?> (<?php echo $row['capacity']; ?>p, <?php echo $row['location']; ?>)
                                    <?php else: ?>—<?php endif; ?>
                            </td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.65);"><?php echo isset($occ_icons[$occ]) ? $occ_icons[$occ] . ' ' . ucfirst(str_replace('_', ' ', $occ)) : '—'; ?></td>
                            <td style="padding:13px 16px;font-size:13px;color:rgba(255,255,255,0.55);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo isset($row['special_request']) && $row['special_request'] ? htmlspecialchars($row['special_request']) : '—'; ?></td>
                            <td style="padding:13px 16px;">
                                <span style="<?php echo $badgeStyle; ?>padding:4px 12px;border-radius:20px;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;font-weight:600;"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td style="padding:13px 16px;">
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <?php if ($status === 'pending'): ?>
                                        <a href="?confirm=<?php echo $row['id']; ?>" style="border:1px solid rgba(39,174,96,0.4);color:#2ecc71;background:transparent;padding:5px 12px;border-radius:3px;font-size:12px;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background='#27ae60';this.style.color='white';" onmouseout="this.style.background='transparent';this.style.color='#2ecc71';">&#10003;</a>
                                        <a href="?cancel=<?php echo $row['id']; ?>" style="border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.5);background:transparent;padding:5px 12px;border-radius:3px;font-size:12px;text-decoration:none;" onmouseover="this.style.borderColor='white';this.style.color='white';" onmouseout="this.style.borderColor='rgba(255,255,255,0.15)';this.style.color='rgba(255,255,255,0.5)';">&#10007;</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" style="border:1px solid rgba(192,57,43,0.35);color:#c0392b;background:transparent;padding:5px 12px;border-radius:3px;font-size:12px;text-decoration:none;" onmouseover="this.style.background='#c0392b';this.style.color='white';" onmouseout="this.style.background='transparent';this.style.color='#c0392b';">&#128465;</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>