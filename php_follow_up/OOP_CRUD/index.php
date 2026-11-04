<?php

    require "db.php";
    $db_obj = new DB_con();

    $users=$db_obj->fetchdata();

    $id = $_POST['id'];
    $delete = $db_obj->delete($id);
    $ubdate = $db_obj->abdate($name,$email,$password);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table id="mytable" class="table table-bordred table-striped" border=1>
        <thead>
        
        <th>First Name</th>
        <!-- <th>Last Name</th> -->
        <th>Email</th>
        <!-- <th>Contact</th> -->
        <!-- <th>Address</th> -->
        <!-- <th>Posting Date</th> -->
        <th>Edit</th>
        <th>Delete</th>
        </thead>
        <tbody>
            <?php foreach($users as $user):?>
        <tr>
                
                <td>
                    <?= htmlspecialchars($user['name'])?>
                </td>

                <td>
                    <?= htmlspecialchars($user['email'])?>
                </td>

                <td>
                  <form action="index.php" method="POST"
                      onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $user['id']) ?>">>
                    <button type="submit">Delete</button>
                </form>
                </td>

                <td>
                    <form action="index.php" method="POST"
                      onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="id" value="">>
                    <button type="submit">Update</button>
                </form>
                </td>
                
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</body>
</html>

