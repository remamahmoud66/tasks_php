
<?php
include("db.php");

$Result = null;
$error = "";

// Get the user ID from POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST['id'] ?? null;

    if (!$id) {
        exit("User ID is required.");
    }

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Name, email, and password are required.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Hash the password before saving
      

        // Update user based on ID
        $sql = "UPDATE users
                SET name = ?, email = ?, password = ?
                WHERE id = ?";

        $stmt = $connect->prepare($sql);
        $stmt->execute([$name, $email, $password, $id]);

        header("Location: dashboard.php");
        exit;
    }

} else {
    // Get ID from URL when opening the form
    $id = $_GET['id'] ?? null;

    if (!$id) {
        exit("User ID is required.");
    }
}

// Fetch user information
$Query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $connect->prepare($Query);
$stmt->execute([$id]);

$Result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$Result) {
    exit("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>

    <h1>Update User</h1>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form action="update.php" method="POST">

        <!-- Send the ID through POST -->
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label for="name">Name:</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($_POST['name'] ?? $Result['name']) ?>"
            required
        >

        <br><br>

        <label for="email">Email:</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? $Result['email']) ?>"
            required
        >

        <br><br>

        <label for="password">New Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
        >

        <br><br>

        <button type="submit">Save Changes</button>

        <a href="dashboard.php">Cancel</a>

    </form>

</body>
</html>
