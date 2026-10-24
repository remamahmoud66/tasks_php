<?php

require "db.php";

// remove all session data = the user is logged out
session_destroy();

header("Location: index.php");
exit();
