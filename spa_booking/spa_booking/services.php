<?php

require "db.php";

$db_obj = new DB_con();

$categories = $db_obj->fetchCategories();

// if the URL has ?category=3 we show only that category, otherwise we show all services
if (isset($_GET['category'])) {

    $category_id = $_GET['category'];

    $services = $db_obj->fetchServicesByCategory($category_id);
    $current_category = $db_obj->fetchCategory($category_id);

} else {

    $category_id = null;

    $services = $db_obj->fetchServices();
    $current_category = false;
}

$page_title = "Treatments";
require "header.php";

?>

<section class="page-banner">
    <p class="eyebrow">Treatments Menu</p>

    <?php if ($current_category) : ?>
        <h1><?= htmlspecialchars($current_category['name']) ?></h1>
        <p><?= htmlspecialchars($current_category['description'] ?? '') ?></p>
    <?php else : ?>
        <h1>All Treatments</h1>
        <p>Find the perfect treatment for your body, skin and mind.</p>
    <?php endif; ?>
</section>


<section class="section">

    <!-- filter buttons: one link for every category -->
    <div class="filter-bar">

        <a href="services.php" class="filter <?= $category_id === null ? 'active' : '' ?>">All</a>

        <?php foreach ($categories as $category) : ?>
            <a href="services.php?category=<?= (int) $category['id'] ?>"
               class="filter <?= $category_id == $category['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($category['name']) ?>
            </a>
        <?php endforeach; ?>

    </div>


    <?php if (count($services) === 0) : ?>

        <p class="empty">No treatments in this category yet.</p>

    <?php else : ?>

        <div class="service-grid">

            <?php foreach ($services as $service) : ?>

                <div class="service-card">

                    <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>">

                    <div class="service-body">

                        <span class="tag"><?= htmlspecialchars($service['category_name']) ?></span>

                        <h3><?= htmlspecialchars($service['name']) ?></h3>

                        <p class="service-desc"><?= htmlspecialchars($service['description'] ?? '') ?></p>

                        <div class="service-meta">
                            <span>&#9719; <?= (int) $service['duration'] ?> min</span>
                            <span class="price"><?= htmlspecialchars($service['price']) ?> JD</span>
                        </div>

                        <div class="card-buttons">
                            <a href="service.php?id=<?= (int) $service['id'] ?>" class="btn btn-outline">Details</a>
                            <a href="book.php?id=<?= (int) $service['id'] ?>" class="btn">Book Now</a>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require "footer.php"; ?>
