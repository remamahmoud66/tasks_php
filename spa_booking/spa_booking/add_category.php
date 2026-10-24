<?php

require "db.php";

$db_obj = new DB_con();

// only the admin can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($name === "") {

        $error = "Please enter the category name.";

    } else {

        $image = $db_obj->uploadImage($_FILES['image'] ?? null);

        if ($image === false) {
            $error = "Please upload a valid image (jpg, jpeg, png, gif, webp - max 2MB).";
        } else {

            $db_obj->createCategory($name, $description, $image);

            header("Location: admin_categories.php");
            exit();
        }
    }
}

$page_title = "Add Category";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <div class="form-card">

        <h1>Add Category</h1>

        <?php if ($error !== "") : ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- enctype is required to send files -->
        <form action="add_category.php" method="POST" enctype="multipart/form-data">

            <label for="name">Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

            <label for="description">Description</label>
            <input type="text" id="description" name="description"
                   value="<?= htmlspecialchars($_POST['description'] ?? '') ?>">

            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <div class="form-buttons">
                <button type="submit" class="btn">Add Category</button>
                <a href="admin_categories.php" class="btn btn-outline">Cancel</a>
            </div>

        </form>

    </div>

</section>

<?php require "footer.php"; ?>
