<?php

session_start();
include("db/connection.php");

$success = false;

// Fetch food items for the dropdown
$food_query = "SELECT DISTINCT food_name FROM menu_items ORDER BY food_name";
$food_result = mysqli_query($conn, $food_query);

// Handle rating submission
if (isset($_POST['submit_rating'])) {

    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $food_name = mysqli_real_escape_string($conn, $_POST['food_name']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review = mysqli_real_escape_string($conn, $_POST['review']);

    if ($rating >= 1 && $rating <= 5) {
        $query = "INSERT INTO ratings(customer_name, food_name, rating, review)
                  VALUES('$customer_name','$food_name', $rating, '$review')";
        mysqli_query($conn, $query);
        $success = true;
    }
}

// Fetch reviews
$reviews_query = "SELECT * FROM ratings ORDER BY id DESC";
$reviews_result = mysqli_query($conn, $reviews_query);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Reviews & Ratings</title>
    <meta name="description" content="Read reviews and rate our food. Share your dining experience with others.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="assets/js/main.js"></script>
</head>

<body>

    <?php include("includes/navbar.php"); ?>

    <div class="container">

        <!-- ========== Page Header ========== -->
        <div class="reviews-header">
            <h1><i class="fas fa-star"></i> Reviews & Ratings <h1>
        </div>

        <!-- ========== Write a Review ========== -->
        <div class="review-form-card">

            <h2><i class="fas fa-pen"></i> Write a Review</h2>

            <?php if ($success): ?>
                <p class="success-msg"><i class="fas fa-check-circle"></i> Thank you! Your review has been submitted.</p>
            <?php endif; ?>

            <form method="POST">

                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name"><i class="fas fa-user"></i> Your Name</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Enter your name"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="food_name"><i class="fas fa-utensils"></i> Food Item</label>
                        <select id="food_name" name="food_name" required>
                            <option value="">Select a dish...</option>
                            <?php
                            if ($food_result) {
                                while ($f = mysqli_fetch_assoc($food_result)) {
                                    echo '<option value="' . htmlspecialchars($f['food_name']) . '">' . htmlspecialchars($f['food_name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rating"><i class="fas fa-star"></i> Rating</label>
                        <select id="rating" name="rating" required>
                            <option value="">Select rating...</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review"><i class="fas fa-comment"></i> Your Review</label>
                    <textarea id="review" name="review" placeholder="Tell us about your experience..." rows="4"
                        required></textarea>
                </div>

                <button type="submit" name="submit_rating" class="btn-submit-review">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>

            </form>

        </div>

        <!-- ========== Reviews List ========== -->
        <div class="reviews-list">

            <h2 class="reviews-list-title">
                <i class="fas fa-comments"></i> All Reviews
            </h2>

            <?php if (mysqli_num_rows($reviews_result) == 0): ?>
                <div class="no-reviews">
                    <i class="far fa-comment-dots"></i>
                    <p>No reviews yet. Be the first to share your experience!</p>
                </div>
            <?php else: ?>

                <?php while ($rev = mysqli_fetch_assoc($reviews_result)): ?>
                    <div class="review-card">
                        <div class="review-card-header">
                            <div class="reviewer-avatar">
                                <?php echo strtoupper(substr($rev['customer_name'], 0, 1)); ?>
                            </div>
                            <div class="reviewer-info">
                                <strong><?php echo htmlspecialchars($rev['customer_name']); ?></strong>
                                <span class="review-food">
                                    <i class="fas fa-utensils"></i> <?php echo htmlspecialchars($rev['food_name']); ?>
                                </span>
                            </div>
                            <div class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i
                                        class="<?php echo $i <= $rev['rating'] ? 'fas fa-star star-filled' : 'far fa-star star-empty'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-body">
                            <p><?php echo nl2br(htmlspecialchars($rev['review'])); ?></p>
                        </div>
                        <?php if (isset($rev['created_at']) && $rev['created_at']): ?>
                            <div class="review-date">
                                <i class="far fa-clock"></i>
                                <?php echo date('M j, Y', strtotime($rev['created_at'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

            <?php endif; ?>

        </div>

    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>