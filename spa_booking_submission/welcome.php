<?php
session_start();
if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}
$name = htmlspecialchars($_SESSION["customer_name"] ?? "Customer");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | Verdant Spa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-layout" style="min-height:360px;max-width:760px;grid-template-columns:1fr;">
    <main class="form-panel" style="text-align:center;">
        <div class="brand" style="color:#254b3c;margin-bottom:25px;">VERDANT<span>.</span></div>
        <h2>Welcome, <?php echo $name; ?>!</h2>
        <p class="subtitle">You are logged in successfully. The appointment booking section will be added next.</p>
        <a class="submit-btn" style="display:block;text-decoration:none;" href="logout.php">Log out</a>
    </main>
</div>
</body>
</html>
