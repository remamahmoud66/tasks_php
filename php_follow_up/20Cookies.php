<?php
declare(strict_types=1);

/* ============================================================
   cookies.php
   A COOKIE is a small piece of data the SERVER asks the BROWSER to
   keep. The browser sends it back with EVERY later request.

   HTTP has no memory: every request is a stranger. Cookies and
   sessions are how a site remembers who you are.

   Facts:
   about 4 KB each
   stored on the VISITOR's device, so the visitor can read and
   edit them. Never trust them, never store secrets in them.
   ============================================================ */

/* RULE: setcookie() must run BEFORE any output, not even a space
   or an HTML tag. Cookies travel in the response HEADERS, and
   headers are sent first. Otherwise you will meet the famous error
   "headers already sent". */

$allowedThemes = ["light", "dark"];

if (isset($_GET["theme"]) && in_array($_GET["theme"], $allowedThemes, true)) {

    setcookie("theme", $_GET["theme"], [
        "expires"  => time() + 60 * 60 * 24 * 30,   // 30 days from now
        "path"     => "/",                          // available on the whole site
        "secure"   => false,                        // set true when using HTTPS
        "httponly" => true,                         // JavaScript cannot read it
        "samesite" => "Lax",                        // helps block cross-site abuse
    ]);

    /* A cookie we set is NOT visible in $_COOKIE during the same
       request. $_COOKIE only shows what the browser SENT us. So we
       redirect, and the next request carries the new cookie. */
    header("Location: cookies.php");
    exit;
}

// DELETE a cookie: set it again with a time in the past (same path!)
if (isset($_GET["reset"])) {
    setcookie("theme", "", [
        "expires" => time() - 3600,
        "path"    => "/",
    ]);
    header("Location: cookies.php");
    exit;
}

// READ a cookie, with ?? as the fallback and a whitelist check
$theme = $_COOKIE["theme"] ?? "light";
if (!in_array($theme, $allowedThemes, true)) {
    $theme = "light";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cookie Demo</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 30px; }
    body.dark { background: #0B2545; color: #fff; }
    a { color: #D4AF37; }
  </style>
</head>
<body class="<?= $theme ?>">

  <h1>Your theme: <?= $theme ?></h1>
  <p>
    <a href="?theme=light">Light</a> |
    <a href="?theme=dark">Dark</a> |
    <a href="?reset=1">Reset</a>
  </p>
  <p>Close the browser and come back: the choice is still here.</p>

  <!-- See it live: DevTools, Application tab, Cookies -->
</body>
</html>