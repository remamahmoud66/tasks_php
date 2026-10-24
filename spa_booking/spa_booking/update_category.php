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
    exit("Category ID is required.");
}

$category = $db_obj->fetchCategory($id);

$error = "";

// the first click (from admin_categories.php) sends only the id,
// so we update only when the form itself is submitted (it has "name")
if (isset($_POST['name'])) {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // keep the old image by default
    $image = $category['image'];

    if ($name === "") {

        $error = "Please enter the category name.";

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

        $db_obj->updateCategory($id, $name, $description, $image);

        // the image changed -> delete the old file (only if the admin uploaded it)
        if ($image !== $category['image'] && !empty($category['image'])
            && str_starts_with($category['image'], 'uploads/') && file_exists($category['image'])) {
            unlink($category['image']);
        }

        header("Location: admin_categories.php");
        exit();
    }
}

$page_title = "Update Category";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <div class="form-card">

        <h1>Update Category</h1>

        <?php if ($error !== "") : ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="update_category.php" method="POST" enctype="multipart/form-data">

            <!-- Send the ID through POST -->
            <input type="hidden" name="id" value="<?= (int) $id ?>">

            <label for="name">Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($_POST['name'] ?? $category['name']) ?>" required>

            <label for="description">Description</label>
            <input type="text" id="description" name="description"
                   value="<?= htmlspecialchars($_POST['description'] ?? $category['description'] ?? '') ?>">

            <label>Current Image</label>
            <?php if (!empty($category['image'])) : ?>
                <img src="<?= htmlspecialchars($category['image']) ?>" class="current-img" alt="category image">
            <?php else : ?>
                <p>No image</p>
            <?php endif; ?>

            <label for="image">New Image (leave empty to keep the current one)</label>
            <input type="file" id="image" name="image" accept="image/*">

            <div class="form-buttons">
                <button type="submit" class="btn">Save Changes</button>
                <a href="admin_categories.php" class="btn btn-outline">Cancel</a>
            </div>

        </form>

    </div>

</section>

<?php require "footer.php"; ?>
