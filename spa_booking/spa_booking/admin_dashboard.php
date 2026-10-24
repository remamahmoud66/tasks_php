<?php

require "db.php";

$db_obj = new DB_con();

// only the admin can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$total_services = $db_obj->countServices();
$total_customers = $db_obj->countCustomers();
$total_bookings = $db_obj->countBookings();
$pending_bookings = $db_obj->countPendingBookings();

// the 5 newest bookings
$latest_bookings = array_slice($db_obj->fetchAllBookings(), 0, 5);

$page_title = "Admin Dashboard";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <h1>Dashboard</h1>
    <p class="auth-sub">Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?>. Here is today's overview.</p>

    <div class="stats-grid">

        <div class="stat-card">
            <span class="stat-number"><?= (int) $total_bookings ?></span>
            <span class="stat-label">Total Bookings</span>
        </div>

        <div class="stat-card stat-highlight">
            <span class="stat-number"><?= (int) $pending_bookings ?></span>
            <span class="stat-label">Waiting for confirmation</span>
        </div>

        <div class="stat-card">
            <span class="stat-number"><?= (int) $total_services ?></span>
            <span class="stat-label">Services</span>
        </div>

        <div class="stat-card">
            <span class="stat-number"><?= (int) $total_customers ?></span>
            <span class="stat-label">Customers</span>
        </div>

    </div>


    <div class="table-head">
        <h2>Latest Bookings</h2>
        <a href="admin_bookings.php" class="btn btn-small">Manage all bookings</a>
    </div>

    <div class="table-wrap">
        <table class="table">

            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($latest_bookings as $booking) : ?>

                    <tr>
                        <td><?= htmlspecialchars($booking['full_name']) ?></td>
                        <td><?= htmlspecialchars($booking['service_name']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars(substr($booking['booking_time'], 0, 5)) ?></td>
                        <td>
                            <span class="status status-<?= htmlspecialchars($booking['status']) ?>">
                                <?= htmlspecialchars($booking['status']) ?>
                            </span>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>
    </div>

</section>

<?php require "footer.php"; ?>
