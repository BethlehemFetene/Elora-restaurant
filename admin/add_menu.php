<?php

include("../db/connection.php");

if(isset($_POST['submit'])){

    $food_name = $_POST['food_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../assets/images/" . $image);

    $query = "INSERT INTO menu_items(food_name, description, price, image)
              VALUES('$food_name','$description','$price','$image')";

    mysqli_query($conn, $query);

    header("Location:dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item — Elora Restaurant</title>
    <link rel="stylesheet" href="../style.css">
    <style>
.add-form-wrap {
  max-width: 580px;
  margin: 56px auto;
  padding: 0 40px;
}

.add-form-wrap .section-label {
  font-size: 11px;
  letter-spacing: 0.24em;
  text-transform: uppercase;
  color: #c0392b;
  font-weight: 600;
  display: block;
  margin-bottom: 8px;
}

.add-form-wrap h1 {
  font-family: Georgia, serif;
  font-size: 34px;
  color: white;
  font-weight: 400;
  margin-bottom: 36px;
}

.add-form-wrap .form-card {
  background: #0f2027;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 6px;
  padding: 40px 36px;
}

.add-form-wrap label {
  display: block;
  font-size: 12px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.5);
  margin-bottom: 8px;
  margin-top: 22px;
}

.add-form-wrap .elora-form label {
  display: block;
  font-size: 11px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.5);
  margin-bottom: 6px;
  margin-top: 18px;
  font-weight: 600;
}

.add-form-wrap .elora-form input,
.add-form-wrap .elora-form select,
.add-form-wrap .elora-form textarea {
  width: 100%;
  padding: 12px 16px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 3px;
  color: #fff;
  font-family: 'Segoe UI', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  margin: 0;
}

.add-form-wrap .elora-form input:focus,
.add-form-wrap .elora-form select:focus,
.add-form-wrap .elora-form textarea:focus {
  border-color: #c0392b;
}

.add-form-wrap .elora-form input::placeholder,
.add-form-wrap .elora-form textarea::placeholder {
  color: rgba(255,255,255,0.25);
}

.add-form-wrap .elora-form select option {
  background: #0f2027;
  color: #fff;
}

.add-form-wrap .elora-form textarea {
  min-height: 140px;
  resize: vertical;
}

.add-form-wrap .elora-form input[type="file"] {
  color: rgba(255,255,255,0.75);
  font-size: 14px;
  margin-top: 4px;
  background: transparent;
}

.add-form-wrap .elora-form button[type="submit"] {
  margin-top: 24px;
  width: 100%;
  padding: 14px;
  background: #c0392b;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.2s;
  font-family: 'Segoe UI', sans-serif;
}

.add-form-wrap .elora-form button[type="submit"]:hover {
  background: #e74c3c;
}
</style>
</head>
<body>

<nav class="admin-nav">
    <div class="admin-nav-logo">Elora Restaurant <span>Admin</span></div>
    <ul class="admin-nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_menu.php" class="active">Add Item</a></li>
        <li><a href="manage_orders.php">Orders</a></li>
        <li><a href="manage_reservations.php">Reservations</a></li>
        <li><a href="manage_ratings.php">Reviews</a></li>
    </ul>
    <a href="../logout.php" class="admin-nav-logout">Logout</a>
</nav>

<div class="add-form-wrap">
  <span class="section-label">Menu Management</span>
  <h1>Add Menu Item</h1>
  <div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <label>Food Name</label>
        <input type="text" name="food_name" placeholder="e.g. Grilled Tibs" required>
        <label>Description</label>
        <textarea name="description" placeholder="Describe the dish..." required></textarea>
        <label>Price (ETB)</label>
        <input type="number" name="price" placeholder="e.g. 250" required>
        <label>Image</label>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="submit">Add Menu Item</button>
    </form>
  </div>
</div>

</body>
</html>
