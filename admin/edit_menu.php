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
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js"></script>
</head>

<body>

<div class="container">

    <form method="POST" enctype="multipart/form-data">

        <h1>Edit Menu Item</h1>

        <input type="text"
               name="food_name"
               value="<?php echo htmlspecialchars($row['food_name']); ?>"
               required>

        <textarea name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>

        <input type="number"
               name="price"
               value="<?php echo htmlspecialchars($row['price']); ?>"
               required>

        <p>Current Image: <strong><?php echo htmlspecialchars($row['image']); ?></strong></p>

        <input type="file" name="image" accept="image/*">

        <small>Leave empty to keep current image</small>

        <br><br>

        <button type="submit" name="submit">Save Changes</button>

    </form>

</div>

</body>

</html>
