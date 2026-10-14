
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$adminCheck = $conn->prepare(
    "SELECT role FROM users WHERE id = ?"
);
$adminCheck->execute([$_SESSION["user_id"]]);
$currentUser = $adminCheck->fetch(PDO::FETCH_ASSOC);

if (!$currentUser || $currentUser["role"] !== "admin") {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$message = "";
$editUser = null;
$editProduct = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    try {

        // Add user
        if ($action === "add_user") {
            $name = trim($_POST["name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";

            if ($name === "" || $email === "" || $password === "") {
                $message = "All user fields are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Enter a valid email.";
            } else {
                $check = $conn->prepare(
                    "SELECT id FROM users WHERE email = ?"
                );
                $check->execute([$email]);

                if ($check->fetch()) {
                    $message = "Email already exists.";
                } else {
                    $hash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $conn->prepare(
                        "INSERT INTO users (name, email, password, role)
                         VALUES (?, ?, ?, 'user')"
                    );
                    $stmt->execute([$name, $email, $hash]);

                    $message = "User added successfully.";
                }
            }
        }

        // Update user
        elseif ($action === "update_user") {
            $id = (int)($_POST["id"] ?? 0);
            $name = trim($_POST["name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";

            if ($name === "" || $email === "") {
                $message = "Name and email are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Enter a valid email.";
            } else {
                $check = $conn->prepare(
                    "SELECT id FROM users WHERE email = ? AND id != ?"
                );
                $check->execute([$email, $id]);

                if ($check->fetch()) {
                    $message = "Email already exists.";
                } else {
                    if ($password !== "") {
                        $hash = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                        $stmt = $conn->prepare(
                            "UPDATE users
                             SET name = ?, email = ?, password = ?
                             WHERE id = ?"
                        );
                        $stmt->execute([$name, $email, $hash, $id]);
                    } else {
                        $stmt = $conn->prepare(
                            "UPDATE users
                             SET name = ?, email = ?
                             WHERE id = ?"
                        );
                        $stmt->execute([$name, $email, $id]);
                    }

                    $message = "User updated successfully.";
                }
            }
        }

        // Delete user
        elseif ($action === "delete_user") {
            $id = (int)($_POST["id"] ?? 0);

            if ($id === (int)$_SESSION["user_id"]) {
                $message = "You cannot delete your own account.";
            } else {
                $stmt = $conn->prepare(
                    "DELETE FROM users WHERE id = ?"
                );
                $stmt->execute([$id]);

                $message = "User deleted successfully.";
            }
        }

        // Add product
        elseif ($action === "add_product") {
            $name = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $price = $_POST["price"] ?? "";
            $quantity = $_POST["quantity"] ?? "";

            if (
                $name === "" || $description === "" ||
                $price === "" || $quantity === ""
            ) {
                $message = "All product fields are required.";
            } elseif (
                !is_numeric($price) || (float)$price < 0 ||
                filter_var($quantity, FILTER_VALIDATE_INT) === false ||
                (int)$quantity < 0
            ) {
                $message = "Enter a valid price and quantity.";
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO products
                     (name, description, price, quantity)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    (int)$quantity
                ]);

                $message = "Product added successfully.";
            }
        }

        // Update product
        elseif ($action === "update_product") {
            $id = (int)($_POST["id"] ?? 0);
            $name = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $price = $_POST["price"] ?? "";
            $quantity = $_POST["quantity"] ?? "";

            if (
                $name === "" || $description === "" ||
                $price === "" || $quantity === ""
            ) {
                $message = "All product fields are required.";
            } elseif (
                !is_numeric($price) || (float)$price < 0 ||
                filter_var($quantity, FILTER_VALIDATE_INT) === false ||
                (int)$quantity < 0
            ) {
                $message = "Enter a valid price and quantity.";
            } else {
                $stmt = $conn->prepare(
                    "UPDATE products
                     SET name = ?, description = ?, price = ?, quantity = ?
                     WHERE id = ?"
                );
                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    (int)$quantity,
                    $id
                ]);

                $message = "Product updated successfully.";
            }
        }

        // Delete product
        elseif ($action === "delete_product") {
            $id = (int)($_POST["id"] ?? 0);

            $stmt = $conn->prepare(
                "DELETE FROM products WHERE id = ?"
            );
            $stmt->execute([$id]);

            $message = "Product deleted successfully.";
        }

    } catch (PDOException $e) {
        $message = "Operation failed. Check the entered data.";
    }
}

// Get user for editing
if (isset($_GET["edit_user"])) {
    $stmt = $conn->prepare(
        "SELECT id, name, email FROM users WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit_user"]]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get product for editing
if (isset($_GET["edit_product"])) {
    $stmt = $conn->prepare(
        "SELECT id, name, description, price, quantity
         FROM products WHERE id = ?"
    );
    $stmt->execute([(int)$_GET["edit_product"]]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Read data for tables
$users = $conn->query(
    "SELECT id, name, email, role FROM users ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$products = $conn->query(
    "SELECT id, name, description, price, quantity, created_at
     FROM products ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$userCount = $conn->query(
    "SELECT COUNT(*) FROM users"
)->fetchColumn();

$productCount = $conn->query(
    "SELECT COUNT(*) FROM products"
)->fetchColumn();

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= h($_SESSION["user_name"]) ?></p>
    </div>
    <a class="logout" href="logout.php">Logout</a>
</header>

<nav class="nav-links">
    <a href="#users">Users</a>
    <a href="#products">Products</a>
</nav>

<main class="container">

    <?php if ($message !== ""): ?>
        <p class="message"><?= h($message) ?></p>
    <?php endif; ?>

    <section class="stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?= (int)$userCount ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Products</h3>
            <p><?= (int)$productCount ?></p>
        </div>
    </section>

    <!-- USERS -->
    <section id="users" class="panel">
        <h2>User Management</h2>

        <h3><?= $editUser ? "Edit User" : "Add New User" ?></h3>

        <form method="post" class="data-form">
            <input type="hidden" name="action"
                   value="<?= $editUser ? "update_user" : "add_user" ?>">

            <?php if ($editUser): ?>
                <input type="hidden" name="id"
                       value="<?= (int)$editUser["id"] ?>">
            <?php endif; ?>

            <label>Full Name</label>
            <input type="text" name="name" required
                   value="<?= h($editUser["name"] ?? "") ?>">

            <label>Email</label>
            <input type="email" name="email" required
                   value="<?= h($editUser["email"] ?? "") ?>">

            <label>
                <?= $editUser ? "New Password (optional)" : "Password" ?>
            </label>
            <input type="password" name="password"
                   <?= $editUser ? "" : "required" ?>>

            <button type="submit">
                <?= $editUser ? "Save Changes" : "Add User" ?>
            </button>

            <?php if ($editUser): ?>
                <a href="users.php#users">Cancel</a>
            <?php endif; ?>
        </form>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int)$user["id"] ?></td>
                        <td><?= h($user["name"]) ?></td>
                        <td><?= h($user["email"]) ?></td>
                        <td><?= h($user["role"]) ?></td>
                        <td class="actions">
                            <a href="users.php?edit_user=<?= (int)$user["id"] ?>#users">
                                Edit
                            </a>

                            <?php if ((int)$user["id"] !== (int)$_SESSION["user_id"]): ?>
                                <form method="post"
                                      onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$user["id"] ?>">
                                    <button class="delete" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$users): ?>
                    <tr><td colspan="5">No users found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </section>

    <!-- PRODUCTS -->
    <section id="products" class="panel">
        <h2>Product Management</h2>

        <h3><?= $editProduct ? "Edit Product" : "Add New Product" ?></h3>

        <form method="post" class="data-form">
            <input type="hidden" name="action"
                   value="<?= $editProduct ? "update_product" : "add_product" ?>">

            <?php if ($editProduct): ?>
                <input type="hidden" name="id"
                       value="<?= (int)$editProduct["id"] ?>">
            <?php endif; ?>

            <label>Product Name</label>
            <input type="text" name="name" required
                   value="<?= h($editProduct["name"] ?? "") ?>">

            <label>Description</label>
            <textarea name="description" required><?= h($editProduct["description"] ?? "") ?></textarea>

            <label>Price</label>
            <input type="number" name="price" min="0" step="0.01" required
                   value="<?= h($editProduct["price"] ?? "") ?>">

            <label>Quantity</label>
            <input type="number" name="quantity" min="0" step="1" required
                   value="<?= h($editProduct["quantity"] ?? "") ?>">

            <button type="submit">
                <?= $editProduct ? "Save Changes" : "Add Product" ?>
            </button>

            <?php if ($editProduct): ?>
                <a href="users.php#products">Cancel</a>
            <?php endif; ?>
        </form>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= (int)$product["id"] ?></td>
                        <td><?= h($product["name"]) ?></td>
                        <td><?= h($product["description"]) ?></td>
                        <td><?= number_format((float)$product["price"], 2) ?></td>
                        <td><?= (int)$product["quantity"] ?></td>
                        <td><?= h($product["created_at"]) ?></td>
                        <td class="actions">
                            <a href="users.php?edit_product=<?= (int)$product["id"] ?>#products">
                                Edit
                            </a>

                            <form method="post"
                                  onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="id"
                                       value="<?= (int)$product["id"] ?>">
                                <button class="delete" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$products): ?>
                    <tr><td colspan="7">No products found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </section>

</main>
</body>
</html>
