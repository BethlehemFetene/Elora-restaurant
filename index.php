<?php
session_start();
include("db/connection.php");

// Handle reservation
$res_success = false;
$res_error   = '';
$dbReady     = true;
$tablesCheck = mysqli_query($conn, "SHOW TABLES LIKE 'restaurant_tables'");
if (!$tablesCheck || mysqli_num_rows($tablesCheck) === 0) $dbReady = false;
$today   = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+1 month'));
$avail_date   = isset($_POST['avail_date'])   ? $_POST['avail_date']       : '';
$avail_time   = isset($_POST['avail_time'])   ? $_POST['avail_time']       : '';
$avail_guests = isset($_POST['avail_guests']) ? intval($_POST['avail_guests']) : 0;
$checked      = isset($_POST['check_availability']);
$available_tables = [];
$show_reserved_toast = isset($_GET['reserved']) && $_GET['reserved'] === '1';

function tableIsFree($conn,$tableId,$date,$timeStart,$timeEnd){
    $tableId=intval($tableId);$date=mysqli_real_escape_string($conn,$date);
    $q="SELECT COUNT(*) AS cnt FROM reservations WHERE table_id=$tableId AND reservation_date='$date' AND status!='cancelled' AND reservation_time<'$timeEnd' AND ADDTIME(reservation_time,'02:00:00')>'$timeStart'";
    $r=mysqli_query($conn,$q);if(!$r)return false;
    $row=mysqli_fetch_assoc($r);return(int)$row['cnt']===0;
}
if($dbReady&&$checked&&$avail_date&&$avail_time&&$avail_guests>0){
    $ts=date('H:i:s',strtotime($avail_time));$te=date('H:i:s',strtotime($avail_time.' +2 hours'));
    $r=mysqli_query($conn,"SELECT * FROM restaurant_tables WHERE is_active=1 AND capacity>=$avail_guests ORDER BY capacity ASC,table_number ASC");
    if($r){while($t=mysqli_fetch_assoc($r)){if(tableIsFree($conn,$t['id'],$avail_date,$ts,$te))$available_tables[]=$t;}}
}
if($dbReady&&isset($_POST['submit_reservation'])){
    $name=mysqli_real_escape_string($conn,$_POST['name']);
    $phone=mysqli_real_escape_string($conn,$_POST['phone']);
    $date=mysqli_real_escape_string($conn,$_POST['date']);
    $time=mysqli_real_escape_string($conn,$_POST['time']);
    $guests=intval($_POST['guests']);$table_id=intval($_POST['table_id']);
    $occasion=mysqli_real_escape_string($conn,$_POST['occasion']);
    $special=mysqli_real_escape_string($conn,$_POST['special_request']);
    $ts=date('H:i:s',strtotime($time));$te=date('H:i:s',strtotime($time.' +2 hours'));
    if($guests<1||$table_id<1){$res_error='Please check availability first.';}
    elseif(!tableIsFree($conn,$table_id,$date,$ts,$te)){$res_error='That table was just booked. Please check again.';}
    else{
        $q="INSERT INTO reservations(customer_name,phone,reservation_date,reservation_time,guests,table_id,occasion,special_request,status) VALUES('$name','$phone','$date','$time','$guests','$table_id','$occasion','$special','pending')";
        if(mysqli_query($conn,$q)){header("Location: index.php?reserved=1");exit;} else{$res_error='Could not save reservation.';}
    }
}

// Handle rating
$rating_success = false;
$food_result = mysqli_query($conn, "SELECT DISTINCT food_name FROM menu_items ORDER BY food_name");
$reviews_result = mysqli_query($conn, "SELECT * FROM ratings ORDER BY id DESC");
if(isset($_POST['submit_rating'])){
    $cn=mysqli_real_escape_string($conn,$_POST['customer_name']);
    $fn=mysqli_real_escape_string($conn,$_POST['food_name']);
    $rt=intval($_POST['rating']);
    $rv=mysqli_real_escape_string($conn,$_POST['review']);
    if($rt>=1&&$rt<=5){mysqli_query($conn,"INSERT INTO ratings(customer_name,food_name,rating,review) VALUES('$cn','$fn',$rt,'$rv')");$rating_success=true;}
    $reviews_result = mysqli_query($conn, "SELECT * FROM ratings ORDER BY id DESC");
}

// Handle register
$reg_error = '';
$reg_success = false;
if(isset($_POST['register'])){
    require_once "includes/auth.php";
    $fullname=$_POST['fullname'];$email=$_POST['email'];$password=$_POST['password'];
    if(strlen($password)<6){$reg_error="Password must be at least 6 characters.";}
    else{
        $hashed=password_hash($password,PASSWORD_DEFAULT);
        $check=$conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email);$check->execute();$check->store_result();
        if($check->num_rows>0){$reg_error="Email already registered.";}
        else{
            $stmt=$conn->prepare("INSERT INTO users(fullname,email,password,role) VALUES(?,?,?,'customer')");
            $stmt->bind_param("sss",$fullname,$email,$hashed);
            if($stmt->execute())$reg_success=true;else $reg_error="Registration failed.";
        }
    }
}

// Handle login
$login_error = '';
if(isset($_POST['login'])){
    require_once "includes/auth.php";
    $email=trim($_POST['email']);$password=$_POST['password'];
    $stmt=$conn->prepare("SELECT id,fullname,email,password,role FROM users WHERE email=?");
    $stmt->bind_param("s",$email);$stmt->execute();$result=$stmt->get_result();
    if($row=$result->fetch_assoc()){
        if(password_verify($password,$row['password'])){
            session_regenerate_id(true);
            $_SESSION['user_id']=$row['id'];$_SESSION['fullname']=$row['fullname'];
            $_SESSION['email']=$row['email'];$_SESSION['role']=$row['role'];$_SESSION['logged_in']=true;
            if($row['role']==='admin')header("Location:admin/dashboard.php");
            else header("Location:index.php");
            exit;
        }
    }
    $login_error="Invalid email or password.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Elora Restaurant</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/elora.css">
    <script src="assets/js/main.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="main-nav">
    <div class="nav-logo">Elora Restaurant</div>
    <ul class="nav-links">
        <li><a href="#hero">Home</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#reservation">Reservation</a></li>
        <li><a href="#ratings">Ratings</a></li>
        <li>
            <a href="cart.php">Cart
                <?php
                $cartCount=0;
                if(isset($_SESSION['cart']))$cartCount=array_sum(array_column($_SESSION['cart'],'quantity'));
                if($cartCount>0)echo '<span class="cart-badge">'.$cartCount.'</span>';
                ?>
            </a>
        </li>
        <?php if(isset($_SESSION['logged_in'])): ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="#register">Register</a></li>
            <li><a href="#login">Login</a></li>
        <?php endif; ?>
    </ul>
    <a href="#reservation" class="nav-cta">Book a Table</a>
</nav>

<!-- Hero -->
<section class="elora-hero" id="hero">
    <div class="elora-hero-content">
        <span class="elora-welcome-label">Welcome To</span>
        <h1 class="elora-hero-title">Elora Restaurant</h1>
        <p class="elora-hero-subtitle">The finest dining experience in Addis Ababa. International cuisine, elegant atmosphere, and unforgettable moments await you.</p>
        <a href="#reservation" class="elora-cta-btn">Book a Table</a>
    </div>
</section>

<!-- About Strip -->
<div class="about-strip">
    <div class="about-strip-inner">
        <div class="about-strip-item"><span class="about-strip-num">12+</span><span class="about-strip-label">Tables Available</span></div>
        <div class="about-strip-divider"></div>
        <div class="about-strip-item"><span class="about-strip-num">30+</span><span class="about-strip-label">Signature Dishes</span></div>
        <div class="about-strip-divider"></div>
        <div class="about-strip-item"><span class="about-strip-num">100%</span><span class="about-strip-label">Fresh Ingredients</span></div>
        <div class="about-strip-divider"></div>
        <div class="about-strip-item"><span class="about-strip-num">5&#9733;</span><span class="about-strip-label">Guest Experience</span></div>
    </div>
</div>

<!-- Menu Section -->
<section id="menu" class="elora-section elora-section-dark">
    <div class="elora-container">
        <div class="elora-section-header">
            <span class="elora-label">Taste &amp; Discover</span>
            <h2>Featured Dishes</h2>
        </div>
        <div class="cards">
            <?php
            $query = "SELECT * FROM menu_items";
            $result = mysqli_query($conn, $query);
            if($result){ while($row = mysqli_fetch_assoc($result)){ ?>
            <div class="card">
                <div class="card-img-wrap">
                    <img src="assets/images/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>">
                </div>
                <div class="card-body">
                    <h2><?php echo htmlspecialchars($row['food_name']); ?></h2>
                    <p class="card-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                    <div class="card-footer">
                        <span class="card-price"><?php echo number_format($row['price'],2); ?> <small>ETB</small></span>
                        <a href="cart.php?add=<?php echo $row['id']; ?>"><button>+ Add</button></a>
                    </div>
                </div>
            </div>
            <?php }} ?>
        </div>
    </div>
</section>

<!-- Reservation Section -->
<section id="reservation" class="elora-section elora-section-darker">
    <div class="elora-container elora-narrow">
        <div class="reservation-modal">
            <div class="reservation-panel">
                <?php if($show_reserved_toast): ?>
                    <div class="toast toast-visible">
                        Reservation submitted successfully! We will confirm shortly.
                    </div>
                <?php endif; ?>
                <div class="elora-section-header">
                    <span class="elora-label">Reserve Your Spot</span>
                    <h2>Book a Table</h2>
                </div>

                <?php if($res_error): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($res_error); ?></p>
                <?php endif; ?>

        <?php if(!($checked && !empty($available_tables))): ?>
        <form method="POST" class="elora-form">
            <label>Date</label>
            <input type="date" name="avail_date" min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>" value="<?php echo htmlspecialchars($avail_date); ?>" required>
            <label>Time</label>
            <input type="time" name="avail_time" min="08:00" max="21:00" value="<?php echo htmlspecialchars($avail_time); ?>" required>
            <label>Number of Guests</label>
            <input type="number" name="avail_guests" min="1" max="8" placeholder="e.g. 4" value="<?php echo $avail_guests>0?$avail_guests:''; ?>" required>
            <button type="submit" name="check_availability">Check Availability</button>
        </form>
        <?php endif; ?>

        <?php if($checked && $dbReady): ?>
            <?php if(!empty($available_tables)): ?>
            <p class="availability-ok"><?php echo count($available_tables); ?> table(s) available for <?php echo $avail_guests; ?> guest(s) on <?php echo date('M j, Y',strtotime($avail_date)); ?> at <?php echo date('g:i A',strtotime($avail_time)); ?>.</p>
            <form method="POST" class="elora-form" onsubmit="return validateReservation()">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($avail_date); ?>">
                <input type="hidden" name="time" value="<?php echo htmlspecialchars($avail_time); ?>">
                <input type="hidden" name="guests" value="<?php echo $avail_guests; ?>">
                <?php if(count($available_tables)===1): ?>
                    <input type="hidden" name="table_id" value="<?php echo (int)$available_tables[0]['id']; ?>">
                <?php else: ?>
                    <label>Choose a Table</label>
                    <select name="table_id" id="table_id" required>
                        <?php foreach($available_tables as $t): ?>
                        <option value="<?php echo (int)$t['id']; ?>">Table T<?php echo (int)$t['table_number']; ?> — <?php echo (int)$t['capacity']; ?> seats (<?php echo htmlspecialchars($t['location']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <label>Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your name" required>
                <label>Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="+251XXXXXXXXX" pattern="(\+251[79][0-9]{8}|0[79][0-9]{8})" maxlength="13" required>
                <label>Occasion (optional)</label>
                <select name="occasion" onchange="toggleOccasionNote(this.value)">
                    <option value="none">No special occasion</option>
                    <option value="birthday">Birthday</option>
                    <option value="anniversary">Anniversary</option>
                    <option value="date_night">Date night</option>
                    <option value="business">Business dinner</option>
                    <option value="graduation">Graduation</option>
                    <option value="other">Other celebration</option>
                </select>
                <div class="occasion-hint" id="occasion-note" style="display:none;"><span id="occasion-hint-text"></span></div>
                <label>Special Requests</label>
                <textarea name="special_request" rows="3" placeholder="Allergies, decorations, etc."></textarea>
                <button type="submit" name="submit_reservation">Confirm Reservation</button>
            </form>
            <?php else: ?>
            <p class="availability-no">No tables available for that time. Try a different time or party size.</p>
            <?php endif; ?>
        <?php endif; ?>
            </div>
            <div class="reservation-media">
                <img src="assets/images/diningtable.webp" alt="Dining room table">
            </div>
        </div>
    </div>
</section>

<!-- Ratings Section -->
<section id="ratings" class="elora-section elora-section-dark">
    <div class="elora-container elora-narrow">
        <div class="elora-section-header">
            <span class="elora-label">Share Your Experience</span>
            <h2>Reviews &amp; Ratings</h2>
        </div>

        <?php if($rating_success): ?>
            <p class="success-msg">Thank you! Your review has been submitted.</p>
        <?php endif; ?>

        <form method="POST" class="elora-form">
            <label>Your Name</label>
            <input type="text" name="customer_name" placeholder="Your name" required>
            <label>Food Item</label>
            <select name="food_name" required>
                <option value="">Select a dish...</option>
                <?php if($food_result){ mysqli_data_seek($food_result,0); while($f=mysqli_fetch_assoc($food_result)){ echo '<option value="'.htmlspecialchars($f['food_name']).'">'.htmlspecialchars($f['food_name']).'</option>'; }} ?>
            </select>
            <label>Rating</label>
            <select name="rating" required>
                <option value="">Select rating...</option>
                <?php for($i=5;$i>=1;$i--):?><option value="<?php echo $i;?>"><?php echo $i;?> star<?php echo $i>1?'s':'';?></option><?php endfor;?>
            </select>
            <label>Your Review</label>
            <textarea name="review" rows="4" placeholder="Tell us about your experience..." required></textarea>
            <button type="submit" name="submit_rating">Submit Review</button>
        </form>

        <div style="margin-top:48px;">
            <?php if($reviews_result && mysqli_num_rows($reviews_result)>0): ?>
                <?php while($rev=mysqli_fetch_assoc($reviews_result)): ?>
                <div class="elora-review-card">
                    <div class="elora-review-top">
                        <div class="elora-review-avatar"><?php echo strtoupper(substr($rev['customer_name'],0,1)); ?></div>
                        <div>
                            <strong><?php echo htmlspecialchars($rev['customer_name']); ?></strong>
                            <span class="elora-review-food"><?php echo htmlspecialchars($rev['food_name']); ?></span>
                        </div>
                        <div class="elora-review-stars">
                            <?php for($i=1;$i<=5;$i++) echo $i<=$rev['rating']?'★':'☆'; ?>
                        </div>
                    </div>
                    <p class="elora-review-text"><?php echo nl2br(htmlspecialchars($rev['review'])); ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:rgba(255,255,255,0.4); text-align:center;">No reviews yet. Be the first!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Login Section -->
<section id="login" class="elora-section elora-section-dark">
    <div class="elora-container elora-narrow">
        <div class="elora-section-header">
            <span class="elora-label">Welcome Back</span>
            <h2>Login</h2>
        </div>
        <?php if($login_error): ?>
            <p class="error-msg"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>
        <form method="POST" class="elora-form">
            <label>Email</label>
            <input type="email" name="email" placeholder="Your email" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Your password" required>
            <button type="submit" name="login">Login</button>
            <p style="text-align:center; margin-top:14px; color:rgba(255,255,255,0.4); font-size:13px;">
                <a href="forgot_password.php" style="color:#c0392b;">Forgot Password?</a>
            </p>
            <p style="text-align:center; margin-top:8px; color:rgba(255,255,255,0.4); font-size:13px;">Don't have an account? <a href="#register" style="color:#c0392b;" onclick="showRegisterSection(); return false;">Register</a></p>
        </form>
    </div>
</section>

<!-- Register Section -->
<section id="register" class="elora-section elora-section-darker">
    <div class="elora-container elora-narrow">
        <div class="elora-section-header">
            <span class="elora-label">Join Us</span>
            <h2>Create an Account</h2>
        </div>
        <?php if($reg_success): ?>
            <p class="success-msg">Registration successful! <a href="#login" style="color:#c0392b;">Login here</a></p>
        <?php endif; ?>
        <?php if($reg_error): ?>
            <p class="error-msg"><?php echo htmlspecialchars($reg_error); ?></p>
        <?php endif; ?>
        <form method="POST" class="elora-form">
            <label>Full Name</label>
            <input type="text" name="fullname" placeholder="Your full name" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="Your email" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Min 6 characters" required>
            <button type="submit" name="register">Register</button>
            <p style="text-align:center; margin-top:14px; color:rgba(255,255,255,0.4); font-size:13px;">Already have an account? <a href="#login" style="color:#c0392b;">Login</a></p>
        </form>
    </div>
</section>

<!-- FOOTER -->
<footer style="
  background: #071218;
  background-image: linear-gradient(
    rgba(7,18,24,0.92), 
    rgba(7,18,24,0.92)
  ), url('assets/images/restaurant-banner.jpg') center/cover no-repeat;
  padding: 80px 64px 0;
  margin-top: 0;
  border-top: 1px solid rgba(255,255,255,0.06);
">

  <!-- 4 column grid -->
  <div style="
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr 1fr;
    gap: 48px;
    padding-bottom: 64px;
  ">

    <!-- Column 1: FIND US -->
    <div>
      <p style="
        font-size: 11px;
        letter-spacing: 0.26em;
        text-transform: uppercase;
        color: #c0392b;
        font-weight: 700;
        margin-bottom: 16px;
      ">Find Us</p>
      <div style="
        width: 24px;
        height: 2px;
        background: #c0392b;
        margin-bottom: 22px;
      "></div>
      <div style="
        display: flex;
        gap: 12px;
        align-items: flex-start;
        color: rgba(255,255,255,0.65);
        font-size: 14px;
        line-height: 1.75;
      ">
        <span style="color:#c0392b;margin-top:3px;">📍</span>
        <span>Addis Ababa, Ethiopia</span>
      </div>
    </div>

    <!-- Column 2: BROWSE (contact) -->
    <div>
      <p style="
        font-size: 11px;
        letter-spacing: 0.26em;
        text-transform: uppercase;
        color: #c0392b;
        font-weight: 700;
        margin-bottom: 16px;
      ">Browse</p>
      <div style="
        width: 24px;
        height: 2px;
        background: #c0392b;
        margin-bottom: 22px;
      "></div>

      <div style="
        display:flex;
        flex-direction:column;
        gap:14px;
      ">
        <a href="mailto:Bethlehemfetene@gmail.com" style="
          display: flex;
          align-items: center;
          gap: 10px;
          color: rgba(255,255,255,0.65);
          text-decoration: none;
          font-size: 14px;
          transition: color 0.2s;
        " onmouseover="this.style.color='white'" 
           onmouseout="this.style.color='rgba(255,255,255,0.65)'">
          <span style="color:#c0392b;">✉</span>
          Bethlehemfetene@gmail.com
        </a>

        <a href="tel:+251993796677" style="
          display: flex;
          align-items: center;
          gap: 10px;
          color: rgba(255,255,255,0.65);
          text-decoration: none;
          font-size: 14px;
          transition: color 0.2s;
        " onmouseover="this.style.color='white'" 
           onmouseout="this.style.color='rgba(255,255,255,0.65)'">
          <span style="color:#c0392b;">📞</span>
          +251 993 796 677
        </a>
      </div>
    </div>

    <!-- Column 3: LINKS -->
    <div>
      <p style="
        font-size: 11px;
        letter-spacing: 0.26em;
        text-transform: uppercase;
        color: #c0392b;
        font-weight: 700;
        margin-bottom: 16px;
      ">Links</p>
      <div style="
        width: 24px;
        height: 2px;
        background: #c0392b;
        margin-bottom: 22px;
      "></div>

      <div style="
        display: flex;
        flex-direction: column;
        gap: 14px;
      ">
        <?php
        $links = [
          'Home'         => '#hero',
          'Menu'         => '#menu',
          'Reservation'  => '#',
          'Ratings'      => '#ratings',
          'Cart'         => 'cart.php',
        ];
        foreach($links as $label => $href):
          $onclick = $label === 'Reservation' 
            ? ' onclick="openModal(\'reservation\')"' 
            : '';
        ?>
        <a href="<?php echo $href; ?>"
           <?php echo $onclick; ?>
           style="
             display: flex;
             align-items: center;
             gap: 10px;
             color: rgba(255,255,255,0.65);
             text-decoration: none;
             font-size: 14px;
             transition: color 0.2s;
           "
           onmouseover="this.style.color='white'"
           onmouseout="this.style.color='rgba(255,255,255,0.65)'">
          <span style="color:#c0392b;font-size:12px;">➜</span>
          <?php echo $label; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Column 4: CONNECT (social) -->
    <div>
      <p style="
        font-size: 11px;
        letter-spacing: 0.26em;
        text-transform: uppercase;
        color: #c0392b;
        font-weight: 700;
        margin-bottom: 16px;
      ">Connect</p>
      <div style="
        width: 24px;
        height: 2px;
        background: #c0392b;
        margin-bottom: 22px;
      "></div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">

        <!-- Instagram -->
        <a href="https://instagram.com/be_tiy" 
           target="_blank"
           title="@be_tiy"
           style="
             width: 42px;
             height: 42px;
             background: #c0392b;
             border-radius: 6px;
             display: flex;
             align-items: center;
             justify-content: center;
             color: white;
             text-decoration: none;
             font-size: 18px;
             transition: background 0.2s;
           "
           onmouseover="this.style.background='#e74c3c'"
           onmouseout="this.style.background='#c0392b'">
          &#x1F4F7;
        </a>

        <!-- Telegram -->
        <a href="https://t.me/bethlehemf"
           target="_blank"
           title="@bethlehemf"
           style="
             width: 42px;
             height: 42px;
             background: #c0392b;
             border-radius: 6px;
             display: flex;
             align-items: center;
             justify-content: center;
             color: white;
             text-decoration: none;
             font-size: 18px;
             transition: background 0.2s;
           "
           onmouseover="this.style.background='#e74c3c'"
           onmouseout="this.style.background='#c0392b'">
          &#x2708;
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/251993796677"
           target="_blank"
           title="WhatsApp"
           style="
             width: 42px;
             height: 42px;
             background: #c0392b;
             border-radius: 6px;
             display: flex;
             align-items: center;
             justify-content: center;
             color: white;
             text-decoration: none;
             font-size: 18px;
             transition: background 0.2s;
           "
           onmouseover="this.style.background='#e74c3c'"
           onmouseout="this.style.background='#c0392b'">
          &#x1F4AC;
        </a>

      </div>
    </div>

  </div><!-- end grid -->

  <!-- Bottom bar -->
  <div style="
    border-top: 1px solid rgba(255,255,255,0.07);
    padding: 22px 0;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  ">
    <p style="
      font-size: 13px;
      color: rgba(255,255,255,0.35);
      margin: 0;
    ">
      All Rights Reserved &copy; <?php echo date('Y'); ?>. 
      Elora Restaurant
    </p>
    <p style="
      font-size: 13px;
      color: rgba(255,255,255,0.25);
      margin: 0;
    ">
      Addis Ababa, Ethiopia
    </p>
  </div>

</footer>

</body>
</html>
