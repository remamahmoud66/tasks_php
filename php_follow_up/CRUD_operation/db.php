<?php
    try {
        $dbhost = 'localhost';
        $dbname='dash';
        $dbuser = 'root';
        $dbpass = '';
        $connect = new PDO(
        "mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
       // echo "conn 200";
    }
   catch (PDOException $e) {
        echo "Error : " . $e->getMessage() . "<br/>";
        die();
    }


?>