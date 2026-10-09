<?php

$arr = array("Twinkle, ", " twinkle,", " twinkle", " little star.");
var_dump($arr);
echo "<br>";

$letter = 'a';
if ($letter == 'z'){
    echo 'a';
}
else {
    $letter++;
    echo $letter;
}
echo "<br>";

$str = "The quick brown fox"; 
$arr = explode(" ", $str); 
echo reset($arr);
echo "<br>";

$strrr = '0000657022.24';
echo str_replace('0', '', $strrr);
echo "<br>";

$str1 = 'The quick brown fox jumps over the lazy dog---';
echo rtrim($str1, '-');
echo "<br>";

$str2 = 'The quick brown fox jumps over the lazy dog';
$words = explode(" ", $str2);
echo implode(" ", array_slice($words, 0, 5));
echo "<br>";

$str3 = '2,543.12';
echo str_replace(',', '', $str3);
echo "<br>";

$a = 0;
$b = 1;
for ($i = 0; $i < 9; $i++) {
    echo $a;
    if ($i < 8) {
        echo ", ";
    }
    $next = $a + $b;
    $a = $b;
    $b = $next;
}
echo "<br>";

$n = 5;
$num = 1;
for ($i = 1; $i <= $n; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo $num . " ";
        $num++;
    }
    echo "<br>";
}
echo "<br>";

$year = 2013;
if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) {
    echo "This year is a leap year";
} else {
    echo "This year is not a leap year";
}
echo "<br>";

$temp = 27;
if ($temp < 20) {
    echo "It is wintertime";
} else {
    echo "It is summertime";
}
echo "<br>";

$firstInteger = 2;
$secondInteger = 2;
$sum = $firstInteger + $secondInteger;
if ($firstInteger == $secondInteger) {
    $result = $sum * 3;
    echo "( $firstInteger + $secondInteger ) * 3 = $result";
} else {
    echo "$firstInteger + $secondInteger = $sum";
}
echo "<br>";

$result = [];
for ($i = 200; $i <= 250; $i++) {
    if ($i % 4 == 0) {
        $result[] = $i;
    }
}
echo implode(",", $result);
echo "<br>";

$min = 11;
$max = 20;
$numbers = range($min, $max);   
shuffle($numbers);            
echo implode(" ", $numbers);
?>