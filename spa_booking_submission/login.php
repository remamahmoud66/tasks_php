<?php
session_start();
require_once "db.php";

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter your email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer["password"])) {
            session_regenerate_id(true);
            $_SESSION["customer_id"] = $customer["id"];
            $_SESSION["customer_name"] = $customer["name"];
            header("Location: welcome.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Verdant Spa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-layout">
    <section class="brand-panel">
        <div class="brand">VERDANT<span>.</span></div>
        <div class="brand-copy">
            <div class="eyebrow">Welcome back</div>
            <h1>Your next<br>refresh starts here.</h1>
            <p>Sign in to book and keep track of your haircut and beard-trimming appointments.</p>
        </div>
        <div class="brand-foot">SPA & GROOMING APPOINTMENTS</div>
    </section>
    <main class="form-panel">
        <h2>Welcome back</h2>
        <p class="subtitle">Enter your account details to continue.</p>

        <?php if ($error !== ""): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button class="submit-btn" type="submit">Log in</button>
        </form>
        <p class="switch-link">Don't have an account? <a href="register.php">Create account</a></p>
    </main>
</div>
</body>
</html>
