<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("db/connection.php");

$success = false;
$error   = '';
$dbReady = true;

$tablesCheck = mysqli_query($conn, "SHOW TABLES LIKE 'restaurant_tables'");
if (!$tablesCheck || mysqli_num_rows($tablesCheck) === 0) {
    $dbReady = false;
}

$today   = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+1 month'));

$avail_date   = isset($_POST['avail_date']) ? $_POST['avail_date'] : '';
$avail_time   = isset($_POST['avail_time']) ? $_POST['avail_time'] : '';
$avail_guests = isset($_POST['avail_guests']) ? intval($_POST['avail_guests']) : 0;
$checked      = isset($_POST['check_availability']);

$available_tables = [];

function tableIsFree($conn, $tableId, $date, $timeStart, $timeEnd)
{
    $tableId = intval($tableId);
    $date    = mysqli_real_escape_string($conn, $date);

    $q = "SELECT COUNT(*) AS cnt FROM reservations
          WHERE table_id = $tableId
          AND reservation_date = '$date'
          AND status != 'cancelled'
          AND reservation_time < '$timeEnd'
          AND ADDTIME(reservation_time, '02:00:00') > '$timeStart'";

    $r = mysqli_query($conn, $q);
    if (!$r) {
        return false;
    }

    $row = mysqli_fetch_assoc($r);
    return (int) $row['cnt'] === 0;
}

if ($dbReady && $checked && $avail_date && $avail_time && $avail_guests > 0) {
    $time_start = date('H:i:s', strtotime($avail_time));
    $time_end   = date('H:i:s', strtotime($avail_time . ' +2 hours'));

    $q = "SELECT * FROM restaurant_tables
          WHERE is_active = 1 AND capacity >= $avail_guests
          ORDER BY capacity ASC, table_number ASC";
    $r = mysqli_query($conn, $q);

    if ($r) {
        while ($table = mysqli_fetch_assoc($r)) {
            if (tableIsFree($conn, $table['id'], $avail_date, $time_start, $time_end)) {
                $available_tables[] = $table;
            }
        }
    }
}

if ($dbReady && isset($_POST['submit_reservation'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $date     = mysqli_real_escape_string($conn, $_POST['date']);
    $time     = mysqli_real_escape_string($conn, $_POST['time']);
    $guests   = intval($_POST['guests']);
    $table_id = intval($_POST['table_id']);
    $occasion = mysqli_real_escape_string($conn, $_POST['occasion']);
    $special  = mysqli_real_escape_string($conn, $_POST['special_request']);

    $time_start = date('H:i:s', strtotime($time));
    $time_end   = date('H:i:s', strtotime($time . ' +2 hours'));

    if ($guests < 1 || $table_id < 1) {
        $error = 'Please check availability and choose a valid party size.';
    } elseif (!tableIsFree($conn, $table_id, $date, $time_start, $time_end)) {
        $error = 'Sorry, that table was just booked. Please check availability again.';
    } else {
        $query = "INSERT INTO reservations(customer_name, phone, reservation_date, reservation_time, guests, table_id, occasion, special_request, status)
                  VALUES('$name','$phone','$date','$time','$guests','$table_id','$occasion','$special','pending')";
        if (mysqli_query($conn, $query)) {
            header("Location: index.php?reserved=1");
            exit;
        } else {
            $error = 'Could not save reservation. Run the upgrade section in restaurant_db.sql if you have not yet.';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reserve A Table</title>
    <link rel="stylesheet" href="style.css">
    <script src="assets/js/main.js"></script>
</head>
<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container reservation-page">

        <?php if (!$dbReady): ?>
            <div class="reservation-alert reservation-alert-error">
                <strong>Database setup needed.</strong>
                In phpMyAdmin, open database <code>restaurant_db</code> and run the SQL from
                <code>restaurant_db.sql</code> (tables + upgrade section at the bottom).
            </div>
        <?php endif; ?>

        <!-- Step 1: Check availability -->
        <?php if (!($checked && !empty($available_tables))): ?>
            <section class="reservation-card">
                <h1>Reserve A Table</h1>
                <p class="reservation-lead">Choose your date, time, and how many people are coming. We will check table availability for you.</p>

                <form method="POST" class="reservation-check-form">
                    <div class="reservation-fields">
                        <label for="avail_date">Date</label>
                        <input type="date"
                               id="avail_date"
                               name="avail_date"
                               min="<?php echo $today; ?>"
                               max="<?php echo $maxDate; ?>"
                               value="<?php echo htmlspecialchars($avail_date); ?>"
                               required
                               <?php echo $dbReady ? '' : 'disabled'; ?>>

                        <label for="avail_time">Time</label>
                        <input type="time"
                               id="avail_time"
                               name="avail_time"
                               min="08:00"
                               max="21:00"
                               value="<?php echo htmlspecialchars($avail_time); ?>"
                               required
                               <?php echo $dbReady ? '' : 'disabled'; ?>>

                        <label for="avail_guests">How many people?</label>
                        <input type="number"
                               id="avail_guests"
                               name="avail_guests"
                               min="1"
                               max="8"
                               placeholder="e.g. 4"
                               value="<?php echo $avail_guests > 0 ? $avail_guests : ''; ?>"
                               required
                               <?php echo $dbReady ? '' : 'disabled'; ?>>
                    </div>

                    <button type="submit" name="check_availability" class="btn-reservation-primary" <?php echo $dbReady ? '' : 'disabled'; ?>>
                        Check Availability
                    </button>
                </form>
            </section>
        <?php endif; ?>

        <?php if ($checked && $dbReady): ?>
            <?php if (!empty($available_tables)): ?>
                <section class="reservation-card reservation-available">
                    <p class="availability-ok">
                        <?php echo count($available_tables); ?> table<?php echo count($available_tables) > 1 ? 's' : ''; ?>
                        available for <strong><?php echo $avail_guests; ?></strong>
                        guest<?php echo $avail_guests > 1 ? 's' : ''; ?>
                        on <?php echo date('M j, Y', strtotime($avail_date)); ?>
                        at <?php echo date('g:i A', strtotime($avail_time)); ?>.
                    </p>

                    <form method="POST" class="reservation-book-form" onsubmit="return validateReservation()">
                        <input type="hidden" name="date" value="<?php echo htmlspecialchars($avail_date); ?>">
                        <input type="hidden" name="time" value="<?php echo htmlspecialchars($avail_time); ?>">
                        <input type="hidden" name="guests" value="<?php echo $avail_guests; ?>">

                        <h2>Complete your reservation</h2>

                        <?php if (count($available_tables) === 1): ?>
                            <input type="hidden" name="table_id" value="<?php echo (int) $available_tables[0]['id']; ?>">
                            <p class="table-pick-note">
                                Table T<?php echo (int) $available_tables[0]['table_number']; ?>
                                (up to <?php echo (int) $available_tables[0]['capacity']; ?> seats)
                            </p>
                        <?php else: ?>
                            <label for="table_id">Choose a table</label>
                            <select id="table_id" name="table_id" required>
                                <?php foreach ($available_tables as $t): ?>
                                    <option value="<?php echo (int) $t['id']; ?>">
                                        Table T<?php echo (int) $t['table_number']; ?> — <?php echo (int) $t['capacity']; ?> seats (<?php echo htmlspecialchars($t['location']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" required>

                        <label for="phone">Phone</label>
                        <input type="tel"
                               id="phone"
                               name="phone"
                               placeholder="+251XXXXXXXXX"
                               pattern="(\+251[79][0-9]{8}|0[79][0-9]{8})"
                               maxlength="13"
                               required>

                        <label for="occasion">Occasion (optional)</label>
                        <select id="occasion" name="occasion" onchange="toggleOccasionNote(this.value)">
                            <option value="none">No special occasion</option>
                            <option value="birthday">Birthday</option>
                            <option value="anniversary">Anniversary</option>
                            <option value="date_night">Date night</option>
                            <option value="business">Business dinner</option>
                            <option value="graduation">Graduation</option>
                            <option value="other">Other celebration</option>
                        </select>

                        <div class="occasion-hint" id="occasion-note" style="display:none;">
                            <span id="occasion-hint-text"></span>
                        </div>

                        <label for="special_request">Special requests</label>
                        <textarea id="special_request"
                                  name="special_request"
                                  rows="3"
                                  placeholder="e.g. Birthday cake, balloons, high chair, allergies..."></textarea>

                        <button type="submit" name="submit_reservation" class="btn-reservation-primary">
                            Confirm Reservation
                        </button>
                    </form>
                </section>
            <?php else: ?>
                <section class="reservation-card reservation-unavailable">
                    <p class="availability-no">
                        No tables are free for <strong><?php echo $avail_guests; ?></strong>
                        guest<?php echo $avail_guests > 1 ? 's' : ''; ?>
                        at that date and time. Try another time or a smaller/larger party size.
                    </p>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($success): ?>
            <section class="reservation-card reservation-success">
                <p class="success-msg">Reservation submitted! We will confirm shortly (status: pending).</p>
                <a href="reservation.php" class="btn-reservation-link">Make another reservation</a>
            </section>
        <?php endif; ?>

        <?php if ($error): ?>
            <section class="reservation-card">
                <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
            </section>
        <?php endif; ?>

    </div>

    <?php include("includes/footer.php"); ?>

</body>
</html>
