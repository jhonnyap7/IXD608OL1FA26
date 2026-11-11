<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOPHEA | PHP Practice</title>
</head>

<body>

<h1>SOPHEA - PHP Practice</h1>

<?php

// ====================================
// 1. ECHO - HELLO WORLD
// ====================================

echo "Hello World!";

echo "<div>Hello World</div>";
echo "<div>Goodbye World</div>";


// ====================================
// 2. VARIABLES
// ====================================

$a = 5;

echo "<p>Variable A: $a</p>";


// ====================================
// 3. STRING INTERPOLATION
// ====================================

echo "<div>I have $a things</div>";


// ====================================
// 4. VALUE TYPES
// ====================================

// Integer
$b = 15;

// Float
$c = 0.576;

// String
$name = "Jonathan";

// Boolean
$isStudent = true;

echo "<p>Integer: $b</p>";
echo "<p>Float: $c</p>";
echo "<p>Name: $name</p>";


// ====================================
// 5. OPERATIONS
// ====================================

$sum = $a + $b;

echo "<p>Sum: $sum</p>";


// ====================================
// 6. STRING CONCATENATION
// ====================================

$firstname = "Jonathan";
$lastname = "Amaya";

$fullname = "$firstname $lastname";

echo "<p>Full Name: $fullname</p>";


// ====================================
// 7. SUPERGLOBALS
// ====================================

echo "<h2>Superglobals</h2>";

$nameFromURL = $_GET['name'] ?? 'Guest';

echo "<p>Hello, " . htmlspecialchars($nameFromURL) . "</p>";


// ====================================
// 8. ARRAYS
// ====================================

echo "<h2>Arrays</h2>";

$colors = array("red", "green", "blue");

echo $colors[2];

echo "<br>" . $colors[0];
echo "<br>" . $colors[1];
echo "<br>" . $colors[2];

echo "<p>Total colors: " . count($colors) . "</p>";


// ====================================
// 9. ASSOCIATIVE ARRAYS
// ====================================

echo "<h2>Associative Arrays</h2>";

$colorsAssociative = [
    "red" => "#f00",
    "green" => "#0f0",
    "blue" => "#00f"
];

echo $colorsAssociative['green'];


// ====================================
// 10. OBJECTS AND CASTING
// ====================================

echo "<h2>Objects</h2>";

$colorsObject = (object) $colorsAssociative;

echo "<p>Red: " . $colorsObject->red . "</p>";


// ====================================
// 11. INDEX NOTATION
// ====================================

echo "<h2>Index Notation</h2>";

echo $colors[0] . "<br>";

echo $colorsAssociative['red'] . "<br>";

echo $colorsAssociative[$colors[0]] . "<br>";

echo $colorsObject->red . "<br>";

echo $colorsObject->{$colors[0]} . "<br>";


// ====================================
// 12. PRINT_R
// ====================================

echo "<h2>Print R</h2>";

echo "<pre>";

print_r($colors);

print_r($colorsAssociative);

print_r($colorsObject);

echo "</pre>";


// ====================================
// 13. FUNCTIONS
// ====================================

function print_p($value) {

    echo "<pre>";

    print_r($value);

    echo "</pre>";

}

echo "<h2>Functions</h2>";

print_p($_GET);

?>

</body>
</html>