<?php

$query = $_POST["q"] ?? "";               
$query = trim(is_string($query) ? $query : "");
echo $_SERVER["REQUEST_METHOD"] . "<br>"; 
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Search Jordan</title></head>
<body>


  <form action="" method="post">
    <label for="q">Search a destination</label>
    <input type="text" id="q" name="q" value="<?= htmlspecialchars($query) ?>">
    <button type="submit">Search</button>
  </form>

  <?php if ($query !== ""): ?>
    <p>You searched for: <strong><?= htmlspecialchars($query) ?></strong></p>
  <?php endif; ?>

</body>
</html>