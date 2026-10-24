<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- every page sets $page_title before it requires header.php -->
    <title><?= htmlspecialchars($page_title) ?> | Serenity Spa</title>

    <!-- Google Fonts: an elegant serif for titles + a clean font for text -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="navbar">

    <a href="index.php" class="logo">
        <span class="logo-mark">&#10047;</span>
        Serenity <span>Spa</span>
    </a>

    <nav class="nav-links">

        <a href="index.php">Home</a>
        <a href="services.php">Treatments</a>

        <?php if (isset($_SESSION['user_id'])) : ?>

            <?php if ($_SESSION['role'] === 'admin') : ?>
                <a href="admin_dashboard.php">Dashboard</a>
            <?php else : ?>
                <a href="my_bookings.php">My Bookings</a>
            <?php endif; ?>

            <span class="nav-user">Hi, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="logout.php" class="btn btn-small btn-outline">Logout</a>

        <?php else : ?>

            <a href="login.php">Login</a>
            <a href="register.php" class="btn btn-small">Register</a>

        <?php endif; ?>

    </nav>

</header>
