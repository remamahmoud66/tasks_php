<?php

require "db.php";

$db_obj = new DB_con();

// only the admin can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_POST['id'] ?? null;

if (!$id) {
    exit("Service ID is required.");
}

$service = $db_obj->fetchService($id);

// for the dropdown
$categories = $db_obj->fetchCategories();

$error = "";

// the first click (from admin_services.php) sends only the id,
// so we update only when the form itself is submitted (it has "name")
if (isset($_POST['name'])) {

    $category_id = $_POST["category_id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $duration = $_POST["duration"] ?? "";

    // keep the old image by default
    $image = $service['image'];

    if ($category_id === "" || $name === "" || $price === "" || $duration === "") {

        $error = "Please fill in all required fields.";

    } elseif (!empty($_FILES['image']['name'])) {

        // the admin chose a new image
        $new_image = $db_obj->uploadImage($_FILES['image']);

        if ($new_image === false) {
            $error = "Please upload a valid image (jpg, jpeg, png, gif, webp - max 2MB).";
        } else {
            $image = $new_image;
        }
    }

    if ($error === "") {

        $db_obj->updateService($id, $category_id, $name, $description, $price, $duration, $image);

        // the image changed -> delete the old file (only if the admin uploaded it)
        if ($image !== $service['image'] && !empty($service['image'])
            && str_starts_with($service['image'], 'uploads/') && file_exists($service['image'])) {
            unlink($service['image']);
        }

        header("Location: admin_services.php");
        exit();
    }
}

$page_title = "Update Service";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <div class="form-card">

        <h1>Update Service</h1>

        <?php if ($error !== "") : ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="update_service.php" method="POST" enctype="multipart/form-data">

            <!-- Send the ID through POST -->
            <input type="hidden" name="id" value="<?= (int) $id ?>">

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>

                <?php foreach ($categories as $category) : ?>
                    <option value="<?= (int) $category['id'] ?>"
                        <?= (string) ($_POST['category_id'] ?? $service['category_id']) === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="name">Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($_POST['name'] ?? $service['name']) ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? $service['description'] ?? '') ?></textarea>

            <div class="form-row">
                <div>
                    <label for="price">Price (JD)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           value="<?= htmlspecialchars($_POST['price'] ?? $service['price']) ?>" required>
                </div>

                <div>
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="5" step="5"
                           value="<?= htmlspecialchars($_POST['duration'] ?? $service['duration']) ?>" required>
                </div>
            </div>

            <label>Current Image</label>
            <?php if (!empty($service['image'])) : ?>
                <img src="<?= htmlspecialchars($service['image']) ?>" class="current-img" alt="service image">
            <?php else : ?>
                <p>No image</p>
            <?php endif; ?>

            <label for="image">New Image (leave empty to keep the current one)</label>
            <input type="file" id="image" name="image" accept="image/*">

            <div class="form-buttons">
                <button type="submit" class="btn">Save Changes</button>
                <a href="admin_services.php" class="btn btn-outline">Cancel</a>
            </div>

        </form>

    </div>

</section>

<?php require "footer.php"; ?>
