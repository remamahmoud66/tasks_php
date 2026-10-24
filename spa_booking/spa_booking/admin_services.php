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

    // a service that has bookings can't be deleted (foreign key)
    if ($db_obj->countBookingsForService($id) > 0) {

        $error = "You can't delete this service because customers have booked it.";

    } else {

        // get the service first so we can delete its image file too
        $service = $db_obj->fetchService($id);

        $db_obj->deleteService($id);

        // we only delete images that the admin uploaded (inside uploads/)
        if (!empty($service['image']) && str_starts_with($service['image'], 'uploads/') && file_exists($service['image'])) {
            unlink($service['image']);
        }

        $message = "Service deleted.";
    }
}

$services = $db_obj->fetchServices();

$page_title = "Manage Services";
require "header.php";

?>

<section class="admin-page">

    <?php require "admin_menu.php"; ?>

    <div class="table-head">
        <h1>Services</h1>
        <a href="add_service.php" class="btn">+ Add Service</a>
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
                    <th>Category</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($services as $service) : ?>

                    <tr>
                        <td><?= (int) $service['id'] ?></td>

                        <td>
                            <?php if (!empty($service['image'])) : ?>
                                <img src="<?= htmlspecialchars($service['image']) ?>" class="table-img" alt="service image">
                            <?php else : ?>
                                No image
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($service['name']) ?></td>
                        <td><?= htmlspecialchars($service['category_name']) ?></td>
                        <td><?= htmlspecialchars($service['price']) ?> JD</td>
                        <td><?= (int) $service['duration'] ?> min</td>

                        <td>
                            <form action="update_service.php" method="POST">
                                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                <button type="submit" class="btn btn-small btn-outline">Update</button>
                            </form>
                        </td>

                        <td>
                            <form action="admin_services.php" method="POST"
                                  onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_id" value="<?= (int) $service['id'] ?>">
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
