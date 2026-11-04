<?php
session_start();
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS' ,'');
define('DB_NAME', 'user_system');
class DB_con
{
    private $connect;
public function __construct()
{
try {
        $this->connect = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",DB_USER, DB_PASS);
        echo "conn 200";
    }
   catch (PDOException $e) {
        echo "Error : " . $e->getMessage() . "<br/>";
        die();
    }


}
public function fetchdata(){
    $read_data="SELECT id,name,email FROM users";
    $result = $this->connect->query($read_data)->fetchAll();
return $result;
}

public function create($name, $email, $password){
    $sql = "INSERT INTO `users`( `name`, `email`, `password`) VALUES (?, ?, ?)";
    
    $stmt= $this->connect->prepare($sql);
    
    return  $stmt->execute([$name, $email, $password]);

}

public function delete($id){
    $deleteid = "DELETE FROM `users` WHERE id =?";

    $stmt= $this->connect->prepare($deleteid);
    
    return  $stmt->execute([$id]);
    
}
public function ubdate($name,$email,$password){

    $sql = "UPDATE users
                SET name = ?, email = ?, password = ?
                WHERE id = ?";

        $stmt = $connect->prepare($sql);
       return $stmt->execute([$name, $email, $password, $id]);
         
    }
public function read_abdate(){
$Query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $connect->prepare($Query)->fetchAll();
return $stmt;


}
}
// ?>