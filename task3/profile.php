<?php
require "User.php";
session_start();

$user = $_SESSION["user"] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
</head>
<body style="font-family: Arial; width:500px; margin:30px auto;">
<?php if ($user): ?>
    <h2>Welcome, <?= htmlspecialchars($user->name) ?></h2>
    <p>Email: <?= htmlspecialchars($user->email) ?></p>
    <p>Mobile: <?= htmlspecialchars($user->mobile) ?></p>
    <p>Governorate: <?= htmlspecialchars($user->governorate) ?></p>
    <p>Track: <?= htmlspecialchars($user->track) ?></p>
    <p>Skills: <?= htmlspecialchars(implode(", ", $user->skills)) ?></p>
    <p>Message: <?= htmlspecialchars($user->message) ?></p>
<?php else: ?>
    <p>No registration found. <a href="index.php">Register here</a></p>
<?php endif; ?>
</body>
</html>