<?php
/* ============================================================
   FILE 1: includes/helpers.php
   Functions we will reuse on EVERY page from now on.
   Project structure:
   php-basics/
     includes/helpers.php
     index.php
   ============================================================ */

declare(strict_types=1);

/* e() = "escape". Use it on EVERYTHING a user typed before
   printing it into HTML. It blocks script injection (XSS). */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function formatJOD(float $amount): string
{
    return number_format($amount, 3) . " JOD";
}

const ACADEMY_NAME = "Orange Coding Academy";