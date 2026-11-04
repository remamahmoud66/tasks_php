<?php
    require "db.php";
    $db_obj = new DB_con();
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"];


    $users=$db_obj->create($name, $email, $password);
    echo $users;


}
   

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <form action="insert.php" method="POST">
        <h2>Create Account</h2>

        <label for="name">Name</label>
        <input
            type="text"
            id="name"
            name="name"
            required
        >

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            minlength="8"
        >

        <button type="submit">Register</button>
    </form>
</body>
</html>


