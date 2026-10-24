<?php

require "db.php";

$db_obj = new DB_con();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $user = $db_obj->login($email, $password);

    if ($user) {

        // save the user info in the session so every page knows who is logged in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        // the admin goes to the dashboard, the customer goes to the treatments
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: services.php");
        }
        exit();

    } else {
        $error = "Wrong email or password.";
    }
}

$page_title = "Login";
require "header.php";

?>

<section class="auth-page">

    <div class="auth-card">

        <h1>Welcome Back</h1>
        <p class="auth-sub">Log in to book and manage your treatments.</p>

        <?php if (isset($_GET['registered'])) : ?>
            <div class="alert alert-success">Your account was created. You can log in now.</div>
        <?php endif; ?>

        <?php if (isset($_GET['need_login'])) : ?>
            <div class="alert alert-info">Please log in first to book a treatment.</div>
        <?php endif; ?>

        <?php if ($error !== "") : ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">

            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-block">Log In</button>

        </form>

        <p class="auth-switch">New here? <a href="register.php">Create an account</a></p>

    </div>

</section>

<?php require "footer.php"; ?>
