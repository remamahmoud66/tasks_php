<?php
$q = trim($_GET["q"] ?? "");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <form method="get" action="search.php">
    <label for="q">Search</label>
    <input type="text" id="q" name="q" value="<?= htmlspecialchars($q) ?>">
    <button type="submit">Search</button>
  </form>

  <?php if ($q != ""): ?>
    <p style="text-align:center;">Search result for: <?= htmlspecialchars($q) ?></p>
  <?php endif; ?>
</body>
</html>