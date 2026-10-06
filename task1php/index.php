<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

echo "<h3>Array 1</h3>";
$colors = array('white', 'green', 'red');
sort($colors);
echo "<ul>";
foreach ($colors as $c) {
  echo "<li>$c</li>";
}
echo "</ul>";

echo "<h3>Array 2</h3>";
$cities = array("Italy" => "Rome", "Luxembourg" => "Luxembourg", "Belgium" => "Brussels", "Denmark" => "Copenhagen", "Finland" => "Helsinki", "France" => "Paris", "Slovakia" => "Bratislava", "Slovenia" => "Ljubljana", "Germany" => "Berlin", "Greece" => "Athens", "Ireland" => "Dublin", "Netherlands" => "Amsterdam", "Portugal" => "Lisbon", "Spain" => "Madrid");
asort($cities);
foreach ($cities as $country => $capital) {
  echo "The capital of $country is $capital<br>";
}

echo "<h3>Array 3</h3>";
$color = array(4 => 'white', 6 => 'green', 11 => 'red');
echo reset($color);

echo "<h3>Array 4</h3>";
$arr = array(1, 2, 3, 4, 5);
$location = 4;
$newItem = '$';
array_splice($arr, $location - 1, 0, $newItem);
echo implode(" ", $arr);

echo "<h3>Array 5</h3>";
$fruits = array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple");
asort($fruits);
foreach ($fruits as $key => $value) {
  echo "$key = $value<br>";
}

echo "<h3>Array 6</h3>";
$temps = array(78, 60, 62, 68, 71, 68, 73, 85, 66, 64, 76, 63, 75, 76, 73, 68, 62, 73, 72, 65, 74, 62, 62, 65, 64, 68, 73, 75, 79, 73);
sort($temps);
echo "Average Temperature is: " . round(array_sum($temps) / count($temps), 1) . "<br>";
echo "Five lowest: " . implode(", ", array_slice($temps, 0, 5)) . "<br>";
echo "Five highest: " . implode(", ", array_slice($temps, -5)) . "<br>";

echo "<h3>Array 7</h3>";
$array1 = array("color" => "red", 2, 4);
$array2 = array("a", "b", "color" => "green", "shape" => "trapezoid", 4);
echo "<pre>";
print_r(array_merge($array1, $array2));
echo "</pre>";


echo "<h3>Function 1</h3>";
function isPrime($n) {
  if ($n < 2) {
    return false;
  }
  for ($i = 2; $i < $n; $i++) {
    if ($n % $i == 0) {
      return false;
    }
  }
  return true;
}
$num = 3;
if (isPrime($num)) {
  echo "$num is a prime number";
} else {
  echo "$num is not a prime number";
}

echo "<h3>Function 2</h3>";
echo strrev("remove");

echo "<h3>Function 3</h3>";
function swapValues(&$a, &$b) {
  $temp = $a;
  $a = $b;
  $b = $temp;
}
$x = 12;
$y = 10;
swapValues($x, $y);
echo "y=$y x=$x";

echo "<h3>Function 4</h3>";
function isArmstrong($n) {
  $sum = 0;
  foreach (str_split($n) as $digit) {
    $sum += $digit * $digit * $digit;
  }
  return $sum == $n;
}
$num = 407;
if (isArmstrong($num)) {
  echo "$num is Armstrong Number";
} else {
  echo "$num is not Armstrong Number";
}

echo "<h3>Function 5</h3>";
function isPalindrome($text) {
  $text = strtolower(preg_replace("/[^a-zA-Z]/", "", $text));
  return $text == strrev($text);
}
if (isPalindrome("Eva, can I see bees in a cave?")) {
  echo "Yes it is a palindrome";
} else {
  echo "No it is not a palindrome";
}

echo "<h3>Function 6</h3>";
function removeDuplicates($arr) {
  return array_values(array_unique($arr));
}
echo "<pre>";
print_r(removeDuplicates(array(2, 4, 7, 4, 8, 4)));
echo "</pre>";

echo "<h3>Logic 1</h3>";
$firstInteger = 10;
$secondInteger = 10;
if ($firstInteger + $secondInteger == 30) {
  echo $firstInteger + $secondInteger;
} else {
  echo "false";
}

echo "<h3>Logic 2</h3>";
$number = 20;
if ($number % 3 == 0) {
  echo "true";
} else {
  echo "false";
}

echo "<h3>Logic 3</h3>";
$number = 50;
if ($number >= 20 && $number <= 50) {
  echo "true";
} else {
  echo "false";
}

echo "<h3>Logic 4</h3>";
echo max(1, 5, 9);

echo "<h3>Logic 5</h3>";
$units = 300;
if ($units <= 50) {
  $bill = $units * 2.50;
} elseif ($units <= 150) {
  $bill = 50 * 2.50 + ($units - 50) * 5.00;
} elseif ($units <= 250) {
  $bill = 50 * 2.50 + 100 * 5.00 + ($units - 150) * 6.20;
} else {
  $bill = 50 * 2.50 + 100 * 5.00 + 100 * 6.20 + ($units - 250) * 7.50;
}
echo "Bill: $bill JOD";

echo "<h3>Logic 7</h3>";
$age = 15;
if ($age >= 18) {
  echo "Eligible to vote";
} else {
  echo "Is no eligible to vote";
}

echo "<h3>Logic 8</h3>";
$number = -60;
if ($number > 0) {
  echo "Positive";
} elseif ($number < 0) {
  echo "Negative";
} else {
  echo "Zero";
}

echo "<h3>Logic 9</h3>";
$scores = array(60, 86, 95, 63, 55, 74, 79, 62, 50);
$avg = array_sum($scores) / count($scores);
if ($avg < 60) {
  echo "F";
} elseif ($avg < 70) {
  echo "D";
} elseif ($avg < 80) {
  echo "C";
} elseif ($avg < 90) {
  echo "B";
} else {
  echo "A";
}

echo "<h3>Loop 1</h3>";
for ($i = 1; $i <= 10; $i++) {
  echo $i;
  if ($i < 10) {
    echo "-";
  }
}

echo "<h3>Loop 2</h3>";
$total = 0;
for ($i = 0; $i <= 30; $i++) {
  $total += $i;
}
echo $total;

echo "<h3>Loop 3</h3>";
echo "<pre>";
for ($i = 0; $i < 5; $i++) {
  for ($j = 0; $j < 5; $j++) {
    if ($j < 4 - $i) {
      echo "A ";
    } else {
      echo chr(65 + $i) . " ";
    }
  }
  echo "<br>";
}
echo "</pre>";

echo "<h3>Loop 4</h3>";
echo "<pre>";
for ($i = 0; $i < 5; $i++) {
  for ($j = 0; $j < 5; $j++) {
    if ($j < 4 - $i) {
      echo "1 ";
    } else {
      echo ($i + 1) . " ";
    }
  }
  echo "<br>";
}
echo "</pre>";

echo "<h3>Loop 5</h3>";
echo "<pre>";
for ($i = 0; $i < 5; $i++) {
  for ($j = 0; $j < 5; $j++) {
    if ($i == $j) {
      echo ($i + 1) . " ";
    } else {
      echo "0 ";
    }
  }
  echo "<br>";
}
echo "</pre>";

echo "<h3>Loop 6</h3>";
$number = 5;
$factorial = 1;
for ($i = 1; $i <= $number; $i++) {
  $factorial = $factorial * $i;
}
echo $factorial;

echo "<h3>Loop 7</h3>";
echo '<table border="1" cellpadding="3px" cellspacing="0px">';
for ($i = 1; $i <= 6; $i++) {
  echo "<tr>";
  for ($j = 1; $j <= 5; $j++) {
    echo "<td>$i * $j = " . ($i * $j) . "</td>";
  }
  echo "</tr>";
}
echo "</table>";

echo "<h3>String 1</h3>";
$text = "hello orange coding academy";
echo strtoupper($text) . "<br>";
echo strtolower($text) . "<br>";
echo ucfirst($text) . "<br>";
echo ucwords($text) . "<br>";

echo "<h3>String 2</h3>";
$str = "085119";
echo substr($str, 0, 2) . ":" . substr($str, 2, 2) . ":" . substr($str, 4, 2);

echo "<h3>String 3</h3>";
$sentence = "I am a full stack developer at orange coding academy";
if (stripos($sentence, "Orange") !== false) {
  echo "Word Found!";
} else {
  echo "Word Not Found!";
}

echo "<h3>String 4</h3>";
echo basename("www.orange.com/index.php");

echo "<h3>String 5</h3>";
echo strstr("info@orange.com", "@", true);

echo "<h3>String 6</h3>";
echo substr("info@orange.com", -3);

echo "<h3>String 7</h3>";
$chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
echo substr(str_shuffle($chars), 0, 8);

echo "<h3>String 8</h3>";
$sentence = "That new trainee is so genius.";
$position = strpos($sentence, " ");
echo "Our" . substr($sentence, $position);

?>
