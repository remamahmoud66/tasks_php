<?php
/* ============================================================
   FILE 2: index.php, using the helpers

   include        loads the file, only a WARNING if it is missing
   require        loads the file, a FATAL ERROR (stops) if missing
   include_once   loads it only if it was not loaded before
   require_once   same, and the safest choice for function files

   Use require_once for anything the page cannot work without.
   Loading the same functions twice causes a "cannot redeclare" error,
   which is exactly what the _once versions prevent.

   __DIR__ makes the path correct from any folder.
   ============================================================ */

require_once __DIR__ . "/includes.php";

echo "<h1>" . e(ACADEMY_NAME) . "</h1>";
echo "<p>Course fee: " . formatJOD(250) . "</p>";

/* This pattern grows: includes/header.php and includes/footer.php
   hold the shared top and bottom of every page, so a design change
   happens in ONE file instead of twenty. */