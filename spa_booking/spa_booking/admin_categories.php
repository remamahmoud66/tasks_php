<?php

require "db.php";

$db_obj = new DB_con();

// only the admin can open this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if (isset($_POST['delete_id'])) {

    $id = $_POST['delete_id'];

    // a category that still has services can't be deleted (foreign key)
    if ($db_obj->countServicesInCategory($id) > 0) {

        $error = "You can't delete this category because it still has services. Move or delete its services first.";

    } else {

        // get the category first so we can delete its image file too
        $category = $db_obj->fetchCategory($id);

        $db_obj->deleteCategory($id);

        // we only delete images that the admin uploaded (inside uploads/)
        if (!empty($category['image']) && str_starts_with($category['image'], 'uploads/') && file_exists($category['image'])) {
            unlink($category['image']);
        }

        $message = "Category deleted.";
    }
}

$categories = $db_obj->fetchCategories();

$page_title = "Manage Categories";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <div class="table-head">
        <h1>Categories</h1>
        <a href="add_category.php" class="btn">+ Add Category</a>
    </div>

    <?php if ($message !== "") : ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error !== "") : ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table class="table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($categories as $category) : ?>

                    <tr>
                        <td><?= (int) $category['id'] ?></td>

                        <td>
                            <?php if (!empty($category['image'])) : ?>
                                <img src="<?= htmlspecialchars($category['image']) ?>" class="table-img" alt="category image">
                            <?php else : ?>
                                No image
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td><?= htmlspecialchars($category['description'] ?? '') ?></td>

                        <td>
                            <form action="update_category.php" method="POST">
                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                <button type="submit" class="btn btn-small btn-outline">Update</button>
                            </form>
                        </td>

                        <td>
                            <form action="admin_categories.php" method="POST"
                                  onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_id" value="<?= (int) $category['id'] ?>">
                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>
    </div>

</section>

<?php require "footer.php"; ?>
