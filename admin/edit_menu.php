<?php

include("../db/connection.php");

$id = intval($_GET['id']);

$query = "SELECT * FROM menu_items WHERE id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if(isset($_POST['submit'])){

    $food_name   = $_POST['food_name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];

    if(!empty($_FILES['image']['name'])){

        $image = $_FILES['image']['name'];
        $tmp   = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../assets/images/" . $image);

    } else {

        $image = $row['image'];

    }

    $query = "UPDATE menu_items SET food_name='$food_name', description='$description', price='$price', image='$image' WHERE id='$id'";

    mysqli_query($conn, $query);

    header("Location:dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Menu Item — Elora Restaurant Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
<?php include('admin_navbar.php'); ?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <span class="admin-page-label">Menu Management</span>
            <h1>Edit Menu Item</h1>
        </div>
    </div>

    <div class="admin-form-card">
        <form method="POST" enctype="multipart/form-data">

            <label>Food Name</label>
            <input type="text" name="food_name" value="<?php echo htmlspecialchars($row['food_name']); ?>" required>

            <label>Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>

            <label>Price (ETB)</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>

            <div class="current-image">
                Current Image: <strong><?php echo htmlspecialchars($row['image']); ?></strong>
            </div>

            <label>Upload New Image</label>
            <input type="file" name="image" accept="image/*">

            <small>Leave empty to keep current image</small>

            <button type="submit" name="submit">Save Changes</button>

        </form>
    </div>
</div>

</body>

</html>
