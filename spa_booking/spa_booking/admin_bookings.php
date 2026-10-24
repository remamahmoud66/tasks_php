<?php

require "db.php";

$db_obj = new DB_con();

// only the admin can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// the only status values we accept
$statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

$message = "";

// the admin changed the status of one booking
if (isset($_POST['booking_id'])) {

    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'] ?? "";

    if (in_array($status, $statuses)) {
        $db_obj->updateBookingStatus($booking_id, $status);
        $message = "Booking #" . (int) $booking_id . " is now " . $status . ".";
    }
}

$bookings = $db_obj->fetchAllBookings();

$page_title = "Manage Bookings";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <h1>Bookings</h1>
    <p class="auth-sub">Confirm new bookings, mark finished ones as completed, or cancel them.</p>

    <?php if ($message !== "") : ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($bookings as $booking) : ?>

                    <tr>
                        <td><?= (int) $booking['id'] ?></td>
                        <td><?= htmlspecialchars($booking['full_name']) ?></td>
                        <td><?= htmlspecialchars($booking['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($booking['service_name']) ?></td>
                        <td><?= htmlspecialchars($booking['price']) ?> JD</td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars(substr($booking['booking_time'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($booking['notes'] ?? '') ?></td>
                        <td>
                            <span class="status status-<?= htmlspecialchars($booking['status']) ?>">
                                <?= htmlspecialchars($booking['status']) ?>
                            </span>
                        </td>
                        <td>

                            <form action="admin_bookings.php" method="POST" class="inline-form">

                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">

                                <select name="status">
                                    <?php foreach ($statuses as $option) : ?>
                                        <option value="<?= $option ?>" <?= $booking['status'] === $option ? 'selected' : '' ?>>
                                            <?= ucfirst($option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" class="btn btn-small">Save</button>

                            </form>

                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>
    </div>

</section>

<?php require "footer.php"; ?>
