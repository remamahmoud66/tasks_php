<?php

require "db.php";

$db_obj = new DB_con();

// the id comes from the link: service.php?id=5
$id = $_GET['id'] ?? null;

if (!$id) {
    exit("Service ID is required.");
}

$service = $db_obj->fetchService($id);

if (!$service) {
    exit("Service not found.");
}

// other services from the same category (we skip the current one inside the loop)
$related_services = $db_obj->fetchServicesByCategory($service['category_id']);

$page_title = $service['name'];
require "header.php";

?>

<section class="section">

    <a href="services.php" class="back-link">&larr; Back to all treatments</a>

    <div class="details">

        <div class="details-image">
            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>">
        </div>

        <div class="details-info">

            <a href="services.php?category=<?= (int) $service['category_id'] ?>" class="tag">
                <?= htmlspecialchars($service['category_name']) ?>
            </a>

            <h1><?= htmlspecialchars($service['name']) ?></h1>

            <p class="details-desc"><?= htmlspecialchars($service['description'] ?? '') ?></p>

            <div class="details-boxes">
                <div class="info-box">
                    <span class="info-label">Duration</span>
                    <span class="info-value"><?= (int) $service['duration'] ?> min</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Price</span>
                    <span class="info-value"><?= htmlspecialchars($service['price']) ?> JD</span>
                </div>
            </div>

            <a href="book.php?id=<?= (int) $service['id'] ?>" class="btn btn-large">Book this treatment</a>

        </div>

    </div>

</section>


<?php if (count($related_services) > 1) : ?>

<section class="section section-soft">

    <div class="section-head">
        <p class="eyebrow">You may also like</p>
        <h2>More from <?= htmlspecialchars($service['category_name']) ?></h2>
    </div>

    <div class="service-grid">

        <?php foreach ($related_services as $related) : ?>

            <?php if ($related['id'] != $service['id']) : ?>

                <div class="service-card">

                    <img src="<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['name']) ?>">

                    <div class="service-body">

                        <h3><?= htmlspecialchars($related['name']) ?></h3>

                        <div class="service-meta">
                            <span>&#9719; <?= (int) $related['duration'] ?> min</span>
                            <span class="price"><?= htmlspecialchars($related['price']) ?> JD</span>
                        </div>

                        <a href="service.php?id=<?= (int) $related['id'] ?>" class="btn btn-block">View Details</a>

                    </div>

                </div>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>

</section>

<?php endif; ?>

<?php require "footer.php"; ?>
