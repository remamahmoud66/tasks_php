<?php
// include("dashboard.php");
include("db.php");

echo $_POST['id'];

$deleteid = "DELETE FROM `users` WHERE id = $_POST[id]";

    $stmt= $connect->prepare($deleteid);
    $stmt->execute();
    

?>