<?php

require "db.php";

$db_obj = new DB_con();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // simple checks before saving
    if ($full_name === "" || $email === "" || $password === "") {
        $error = "Please fill in all required fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "The two passwords do not match.";
    } elseif ($db_obj->emailExists($email)) {
        $error = "This email is already registered. Please log in.";
    } else {

        $db_obj->register($full_name, $email, $phone, $password);

        // go to the login page and show a success message there
        header("Location: login.php?registered=1");
        exit();
    }
}

$page_title = "Register";
require "header.php";

?>

<section class="auth-page">

    <div class="auth-card">

        <h1>Create Account</h1>
        <p class="auth-sub">Join Serenity Spa and book your first treatment.</p>

        <?php if ($error !== "") : ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">

            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name"
                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>

            <label for="email">Email *</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="07XXXXXXXX">

            <label for="password">Password *</label>
            <input type="password" id="password" name="password" minlength="6" required>

            <label for="confirm_password">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>

            <button type="submit" class="btn btn-block">Register</button>

        </form>

        <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>

    </div>

</section>

<?php require "footer.php"; ?>
