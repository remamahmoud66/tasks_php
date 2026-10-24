<?php

require "db.php";

$db_obj = new DB_con();

$categories = $db_obj->fetchCategories();
$featured_services = $db_obj->fetchFeaturedServices();

$page_title = "Home";
require "header.php";

?>

<!-- ============ Hero section ============ -->
<section class="hero">

    <div class="hero-content">

        <p class="eyebrow">Welcome to Serenity Spa</p>

        <h1>Relax your body,<br>refresh your soul.</h1>

        <p class="hero-text">
            Massages, facials, body scrubs, sauna and grooming - all in one calm place.
            Choose your treatment, pick a time, and we will take care of the rest.
        </p>

        <div class="hero-buttons">
            <a href="services.php" class="btn">Explore Treatments</a>

            <?php if (!isset($_SESSION['user_id'])) : ?>
                <a href="register.php" class="btn btn-light">Create Account</a>
            <?php endif; ?>
        </div>

    </div>

</section>


<!-- ============ Categories ============ -->
<section class="section">

    <div class="section-head">
        <p class="eyebrow">Our Treatments</p>
        <h2>Choose your experience</h2>
    </div>

    <div class="category-grid">

        <?php foreach ($categories as $category) : ?>

            <a href="services.php?category=<?= (int) $category['id'] ?>" class="category-card">

                <img src="<?= htmlspecialchars($category['image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">

                <div class="category-overlay">
                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                    <p><?= htmlspecialchars($category['description'] ?? '') ?></p>
                </div>

            </a>

        <?php endforeach; ?>

    </div>

</section>


<!-- ============ Featured services ============ -->
<section class="section section-soft">

    <div class="section-head">
        <p class="eyebrow">Signature Treatments</p>
        <h2>Most loved by our guests</h2>
    </div>

    <div class="service-grid">

        <?php foreach ($featured_services as $service) : ?>

            <div class="service-card">

                <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>">

                <div class="service-body">

                    <span class="tag"><?= htmlspecialchars($service['category_name']) ?></span>

                    <h3><?= htmlspecialchars($service['name']) ?></h3>

                    <div class="service-meta">
                        <span>&#9719; <?= (int) $service['duration'] ?> min</span>
                        <span class="price"><?= htmlspecialchars($service['price']) ?> JD</span>
                    </div>

                    <a href="service.php?id=<?= (int) $service['id'] ?>" class="btn btn-block">View Details</a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- ============ How it works ============ -->
<section class="section">

    <div class="section-head">
        <p class="eyebrow">Easy Booking</p>
        <h2>How it works</h2>
    </div>

    <div class="steps">

        <div class="step">
            <span class="step-number">1</span>
            <h3>Choose a treatment</h3>
            <p>Browse our categories and pick the treatment you love.</p>
        </div>

        <div class="step">
            <span class="step-number">2</span>
            <h3>Pick a date &amp; time</h3>
            <p>Select the day and the time that suits you best.</p>
        </div>

        <div class="step">
            <span class="step-number">3</span>
            <h3>Relax &amp; enjoy</h3>
            <p>We confirm your booking - just come and enjoy your time.</p>
        </div>

    </div>

</section>

<?php require "footer.php"; ?>
