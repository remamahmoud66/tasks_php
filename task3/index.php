<?php
require "User.php";   
session_start();      


$governorates = ["Amman", "Irbid", "Aqaba", "Zarqa"];
$tracks       = ["Full Stack", "Frontend", "Backend"];
$allSkills    = ["HTML", "CSS", "JavaScript"];


$name = $email = $mobile = $governorate = $track = $message = "";
$skills = [];
$agree = false;
$errors = [];
$success = false;
$user = null;


if (isset($_COOKIE["user_name"]))  { $name  = $_COOKIE["user_name"]; }
if (isset($_COOKIE["user_track"])) { $track = $_COOKIE["user_track"]; }


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = trim($_POST["name"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $mobile      = trim($_POST["mobile"] ?? "");
    $governorate = trim($_POST["governorate"] ?? "");
    $track       = trim($_POST["track"] ?? "");
    $message     = trim($_POST["message"] ?? "");
    $skills      = $_POST["skills"] ?? [];
    $agree       = isset($_POST["agree"]);

  
    if ($name == "") {
        $errors["name"] = "Full name is required";
    }

    if ($email == "") {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email format is not valid";
    }

    if ($mobile == "") {
        $errors["mobile"] = "Mobile is required";
    } elseif (!preg_match("/^07[789][0-9]{7}$/", $mobile)) {
        $errors["mobile"] = "Mobile must look like 0791234567";
    }

    if ($governorate == "" || !in_array($governorate, $governorates)) {
        $errors["governorate"] = "Please choose a governorate";
    }

    if ($track == "" || !in_array($track, $tracks)) {
        $errors["track"] = "Please choose a track";
    }

    if (count($skills) == 0) {
        $errors["skills"] = "Choose at least one skill";
    }

    if (!$agree) {
        $errors["agree"] = "You must accept the terms";
    }

    if (empty($errors)) {
        $user = new User($name, $email, $mobile, $governorate, $track, $skills, $message);

        $_SESSION["user"] = $user;                                 
        setcookie("user_name", $name, time() + 60*60*24*7, "/");     
        setcookie("user_track", $track, time() + 60*60*24*7, "/");

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($success): ?>
    <div style="width:500px; margin:30px auto;">
        <h2>Registration successful ✅</h2>
        <p>Name: <?= htmlspecialchars($user->name) ?></p>
        <p>Email: <?= htmlspecialchars($user->email) ?></p>
        <p>Mobile: <?= htmlspecialchars($user->mobile) ?></p>
        <p>Governorate: <?= htmlspecialchars($user->governorate) ?></p>
        <p>Track: <?= htmlspecialchars($user->track) ?></p>
        <p>Skills: <?= htmlspecialchars(implode(", ", $user->skills)) ?></p>
        <p>Message: <?= htmlspecialchars($user->message) ?></p>
        <a href="profile.php">Go to my profile</a>
    </div>
<?php else: ?>

<form action="" method="post">

  <label for="name">Full Name</label>
  <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>">
  <span class="error"><?= $errors["name"] ?? "" ?></span>

  <label for="email">Email</label>
  <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
  <span class="error"><?= $errors["email"] ?? "" ?></span>

  <label for="mobile">Mobile</label>
  <input type="tel" id="mobile" name="mobile" placeholder="0791234567" value="<?= htmlspecialchars($mobile) ?>">
  <span class="error"><?= $errors["mobile"] ?? "" ?></span>

  <label for="governorate">Governorate</label>
  <select id="governorate" name="governorate">
    <option value="">Choose a governorate</option>
    <?php foreach ($governorates as $g): ?>
      <option value="<?= $g ?>" <?= $governorate == $g ? "selected" : "" ?>><?= $g ?></option>
    <?php endforeach; ?>
  </select>
  <span class="error"><?= $errors["governorate"] ?? "" ?></span>

  <label>Track</label>
  <?php foreach ($tracks as $t): ?>
    <label class="inline">
      <input type="radio" name="track" value="<?= $t ?>" <?= $track == $t ? "checked" : "" ?>>
      <?= $t ?>
    </label>
  <?php endforeach; ?>
  <span class="error"><?= $errors["track"] ?? "" ?></span>

  <label>Skills you already have</label>
  <?php foreach ($allSkills as $s): ?>
    <label class="inline">
      <input type="checkbox" name="skills[]" value="<?= $s ?>" <?= in_array($s, $skills) ? "checked" : "" ?>>
      <?= $s ?>
    </label>
  <?php endforeach; ?>
  <span class="error"><?= $errors["skills"] ?? "" ?></span>

  <label for="message">Why do you want to join? (optional)</label>
  <textarea id="message" name="message" rows="4"><?= htmlspecialchars($message) ?></textarea>

  <label class="inline terms">
    <input type="checkbox" name="agree" <?= $agree ? "checked" : "" ?>>
    I agree to the academy terms
  </label>
  <span class="error"><?= $errors["agree"] ?? "" ?></span>

  <button type="submit">Register</button>
</form>

<?php endif; ?>
</body>
</html>