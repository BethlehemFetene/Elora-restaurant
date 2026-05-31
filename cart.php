<?php

session_start();
include("db/connection.php");

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Add item to cart
if(isset($_GET['add'])){
    $id   = intval($_GET['add']);
    $q    = "SELECT * FROM menu_items WHERE id='$id'";
    $r    = mysqli_query($conn, $q);
    $item = mysqli_fetch_assoc($r);

    if($item){
        $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
        if($totalItems >= 5){
            $_SESSION['cart_error'] = "You can only order up to 5 items per day.";
        } else {
            if(isset($_SESSION['cart'][$id])){
                if($_SESSION['cart'][$id]['quantity'] < 5 && $totalItems < 5){
                    $_SESSION['cart'][$id]['quantity']++;
                }
            } else {
                $_SESSION['cart'][$id] = [
                    'food_name' => $item['food_name'],
                    'price'     => $item['price'],
                    'image'     => $item['image'],
                    'quantity'  => 1
                ];
            }
        }
    }
    header("Location:cart.php");
    exit;
}

// Remove item
if(isset($_GET['remove'])){
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location:cart.php");
    exit;
}

// Update quantity
if(isset($_POST['update'])){
    foreach($_POST['qty'] as $id => $qty){
        $id  = intval($id);
        $qty = intval($qty);
        if($qty <= 0){
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }
    $total = array_sum(array_column($_SESSION['cart'], 'quantity'));
    if($total > 5){
        $_SESSION['cart_error'] = "You can only order up to 5 items per day.";
    }
    header("Location:cart.php");
    exit;
}

// Place order
$success = false;
if(isset($_POST['place_order'])){
    $customer_name = $_POST['customer_name'];
    $totalItems    = array_sum(array_column($_SESSION['cart'], 'quantity'));

    if($totalItems > 5){
        $_SESSION['cart_error'] = "You can only order up to 5 items per day.";
        header("Location:cart.php");
        exit;
    }

    foreach($_SESSION['cart'] as $id => $item){
        $food_name   = $item['food_name'];
        $quantity    = $item['quantity'];
        $total_price = $item['price'] * $quantity;
        $q = "INSERT INTO orders(customer_name, food_name, quantity, total_price)
              VALUES('$customer_name','$food_name','$quantity','$total_price')";
        mysqli_query($conn, $q);
    }

    $_SESSION['cart'] = [];
    $success = true;
}

$cart      = $_SESSION['cart'];
$cartError = isset($_SESSION['cart_error']) ? $_SESSION['cart_error'] : '';
unset($_SESSION['cart_error']);

$grandTotal = 0;
foreach($cart as $item){
    $grandTotal += $item['price'] * $item['quantity'];
}
$totalItems = array_sum(array_column($cart, 'quantity'));
$remaining  = 5 - $totalItems;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js"></script>
</head>
<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <h1 class="cart-title">🛒 Your Cart</h1>

        <?php if($cartError): ?>
            <p class="error-msg"><?php echo $cartError; ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p class="success-msg">✅ Your order has been placed successfully! We'll have it ready for you.</p>
        <?php endif; ?>

        <?php if(empty($cart)): ?>

            <div class="empty-cart-box">
                <div class="empty-cart-icon">🍽️</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything yet.</p>
                <a href="menu.php"><button>Browse Menu</button></a>
            </div>

        <?php else: ?>

            <div class="cart-layout">

                <!-- Cart Items -->
                <div class="cart-items">

                    <div class="cart-items-header">
                        <span>Items (<?php echo $totalItems; ?>/5 daily limit)</span>
                        <span><?php echo $remaining; ?> slot<?php echo $remaining != 1 ? 's' : ''; ?> remaining</span>
                    </div>

                    <form method="POST" id="cart-form">

                        <?php foreach($cart as $id => $item): ?>

                        <div class="cart-item">

                            <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>">

                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
                                <p class="cart-item-price"><?php echo number_format($item['price'], 2); ?> ETB each</p>
                            </div>

                            <div class="cart-item-controls">
                                <input type="number"
                                       name="qty[<?php echo $id; ?>]"
                                       value="<?php echo $item['quantity']; ?>"
                                       min="1" max="5"
                                       class="qty-input">
                                <p class="cart-item-subtotal"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ETB</p>
                                <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">✕ Remove</a>
                            </div>

                        </div>

                        <?php endforeach; ?>

                        <button type="submit" name="update" class="update-btn">Update Cart</button>

                    </form>

                </div>

                <!-- Order Summary -->
                <div class="cart-summary">

                    <h2>Order Summary</h2>

                    <div class="summary-row">
                        <span>Items (<?php echo $totalItems; ?>)</span>
                        <span><?php echo number_format($grandTotal, 2); ?> ETB</span>
                    </div>

                    <div class="summary-row">
                        <span>Daily Limit</span>
                        <span><?php echo $totalItems; ?>/5</span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span><?php echo number_format($grandTotal, 2); ?> ETB</span>
                    </div>

                    <form method="POST">
                        <input type="text" name="customer_name" placeholder="Your Name" required>
                        <button type="submit" name="place_order" class="order-btn">Confirm Order</button>
                    </form>

                    <a href="menu.php" class="continue-shopping">← Continue Shopping</a>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <?php include("includes/footer.php"); ?>

</body>
</html>
