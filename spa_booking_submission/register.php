<?php
session_start();
require_once "db.php";

$error = "";
$success = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "" || $confirm_password === "") {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            $success = "Account created successfully. You can now log in.";
            $name = "";
            $email = "";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Verdant Spa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-layout">
    <section class="brand-panel">
        <div class="brand">VERDANT<span>.</span></div>
        <div class="brand-copy">
            <div class="eyebrow">Your time. Your care.</div>
            <h1>A little time<br>just for you.</h1>
            <p>Book your next haircut or beard-trimming appointment with ease.</p>
        </div>
        <div class="brand-foot">SPA & GROOMING APPOINTMENTS</div>
    </section>
    <main class="form-panel">
        <h2>Create account</h2>
        <p class="subtitle">Join us to manage your grooming appointments.</p>

        <?php if ($error !== ""): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success !== ""): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?> <a href="login.php">Log in</a></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="At least 6 characters" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter your password again" minlength="6" required>
            </div>
            <button class="submit-btn" type="submit">Create account</button>
        </form>
        <p class="switch-link">Already have an account? <a href="login.php">Log in</a></p>
    </main>
</div>
</body>
</html>
