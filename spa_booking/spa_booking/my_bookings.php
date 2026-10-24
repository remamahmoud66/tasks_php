<?php

require "db.php";

$db_obj = new DB_con();

// only logged-in users can see their bookings
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// the customer pressed "Cancel" on one of his bookings
if (isset($_POST['cancel_id'])) {

    $cancel_id = $_POST['cancel_id'];

    $db_obj->cancelBooking($cancel_id, $_SESSION['user_id']);

    $message = "Your booking was cancelled.";
}

if (isset($_GET['booked'])) {
    $message = "Your booking was sent! We will confirm it soon.";
}

$bookings = $db_obj->fetchUserBookings($_SESSION['user_id']);

$page_title = "My Bookings";
require "header.php";

?>

<section class="page-banner">
    <p class="eyebrow">Your Appointments</p>
    <h1>My Bookings</h1>
    <p>Here you can follow the status of every treatment you booked.</p>
</section>

<section class="section">

    <?php if ($message !== "") : ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (count($bookings) === 0) : ?>

        <div class="empty-box">
            <p>You have no bookings yet.</p>
            <a href="services.php" class="btn">Browse Treatments</a>
        </div>

    <?php else : ?>

        <div class="booking-list">

            <?php foreach ($bookings as $booking) : ?>

                <div class="booking-item">

                    <img src="<?= htmlspecialchars($booking['image']) ?>" alt="<?= htmlspecialchars($booking['service_name']) ?>">

                    <div class="booking-info">
                        <h3><?= htmlspecialchars($booking['service_name']) ?></h3>
                        <p>
                            &#128197; <?= htmlspecialchars($booking['booking_date']) ?>
                            &nbsp; &#9719; <?= htmlspecialchars(substr($booking['booking_time'], 0, 5)) ?>
                            &nbsp; &middot; <?= (int) $booking['duration'] ?> min
                            &nbsp; &middot; <?= htmlspecialchars($booking['price']) ?> JD
                        </p>

                        <?php if (!empty($booking['notes'])) : ?>
                            <p class="booking-notes">Note: <?= htmlspecialchars($booking['notes']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="booking-actions">

                        <!-- the status name is also the CSS class: status-pending, status-confirmed ... -->
                        <span class="status status-<?= htmlspecialchars($booking['status']) ?>">
                            <?= htmlspecialchars($booking['status']) ?>
                        </span>

                        <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed') : ?>

                            <form action="my_bookings.php" method="POST"
                                  onsubmit="return confirm('Cancel this booking?');">

                                <input type="hidden" name="cancel_id" value="<?= (int) $booking['id'] ?>">

                                <button type="submit" class="btn btn-small btn-danger">Cancel</button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require "footer.php"; ?>
