<?php
declare(strict_types=1);

// require_once __DIR__ . "/includes/helpers.php";
require_once __DIR__ . "/10includeANDrequire/includes.php";



/* ===== 1. $_GET ===== */
$city = $_GET["city"] ?? "not provided";
echo "City: " . e((string) $city) . "<br>";

/* ===== 3. $_SERVER: the request and the server ===== */
echo $_SERVER["REQUEST_METHOD"] . "<br>";     // GET or POST
echo $_SERVER["REQUEST_URI"] . "<br>";        // /superglobals.php?city=Amman&page=2
echo $_SERVER["QUERY_STRING"] . "<br>";       // city=Amman&page=2
echo $_SERVER["SCRIPT_NAME"] . "<br>";        // /superglobals.php
echo $_SERVER["HTTP_HOST"] . "<br>";          // localhost:8000
echo $_SERVER["REMOTE_ADDR"] . "<br>";        // the visitor's IP address


/* Some values are sent by the browser, so they can be faked.
   Never make a security decision based on them. */
echo e($_SERVER["HTTP_USER_AGENT"] ?? "unknown") . "<br>";   // browser and OS
echo e($_SERVER["HTTP_REFERER"] ?? "none") . "<br>";         // the previous page
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