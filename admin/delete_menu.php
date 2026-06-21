<?php

include("../db/connection.php");

$id = intval($_GET['id']);

$query = "DELETE FROM menu_items WHERE id='$id'";

mysqli_query($conn, $query);

header("Location:manage_menu.php");

?>