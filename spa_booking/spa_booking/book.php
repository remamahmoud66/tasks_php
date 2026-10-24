<?php

require "db.php";

$db_obj = new DB_con();

// only logged-in users can book
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?need_login=1");
    exit();
}

// the service id comes from the link: book.php?id=5
$id = $_GET['id'] ?? null;

if (!$id) {
    exit("Service ID is required.");
}

$service = $db_obj->fetchService($id);

if (!$service) {
    exit("Service not found.");
}

// the times the spa accepts bookings (one every hour)
$time_slots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];

$today = date('Y-m-d');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $booking_date = $_POST["booking_date"] ?? "";
    $booking_time = $_POST["booking_time"] ?? "";
    $notes = trim($_POST["notes"] ?? "");

    if ($booking_date === "" || $booking_time === "") {
        $error = "Please choose a date and a time.";
    } elseif ($booking_date < $today) {
        $error = "You can't book a date in the past.";
    } elseif (!in_array($booking_time, $time_slots)) {
        $error = "Please choose a time from the list.";
    } elseif ($db_obj->isSlotTaken($id, $booking_date, $booking_time)) {
        $error = "Sorry, this time is already booked. Please choose another time.";
    } else {

        $db_obj->createBooking($_SESSION['user_id'], $id, $booking_date, $booking_time, $notes);

        // redirect so refreshing the page does not book twice
        header("Location: my_bookings.php?booked=1");
        exit();
    }
}

$page_title = "Book " . $service['name'];
require "header.php";

?>

<section class="section">

    <a href="service.php?id=<?= (int) $service['id'] ?>" class="back-link">&larr; Back to treatment</a>

    <div class="booking-layout">

        <!-- left: summary of the chosen service -->
        <div class="booking-summary">

            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>">

            <div class="booking-summary-body">
                <span class="tag"><?= htmlspecialchars($service['category_name']) ?></span>
                <h2><?= htmlspecialchars($service['name']) ?></h2>

                <ul class="summary-list">
                    <li><span>Duration</span> <strong><?= (int) $service['duration'] ?> min</strong></li>
                    <li><span>Price</span> <strong><?= htmlspecialchars($service['price']) ?> JD</strong></li>
                    <li><span>Payment</span> <strong>At the spa</strong></li>
                </ul>
            </div>

        </div>

        <!-- right: the booking form -->
        <div class="booking-form-card">

            <h1>Book your session</h1>
            <p class="auth-sub">Choose the day and time that suit you.</p>

            <?php if ($error !== "") : ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="book.php?id=<?= (int) $service['id'] ?>" method="POST">

                <label for="booking_date">Date</label>
                <input type="date" id="booking_date" name="booking_date" min="<?= $today ?>"
                       value="<?= htmlspecialchars($_POST['booking_date'] ?? '') ?>" required>

                <label>Time</label>
                <div class="time-grid">

                    <?php foreach ($time_slots as $slot) : ?>
                        <label class="time-option">
                            <input type="radio" name="booking_time" value="<?= $slot ?>"
                                <?= ($_POST['booking_time'] ?? '') === $slot ? 'checked' : '' ?> required>
                            <span><?= $slot ?></span>
                        </label>
                    <?php endforeach; ?>

                </div>

                <label for="notes">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Anything we should know? e.g. allergies, pressure preference..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>

                <button type="submit" class="btn btn-block btn-large">Confirm Booking</button>

            </form>

        </div>

    </div>

</section>

<?php require "footer.php"; ?>
