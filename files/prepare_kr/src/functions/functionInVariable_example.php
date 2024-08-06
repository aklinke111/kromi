<?php
// Define some functions
function sayHello() {
    echo "Hello, World!";
}

function greet($name) {
    echo "Hello, $name!";
}

function add($a, $b) {
    return $a + $b;
}

// Store function names in variables
$func1 = 'sayHello';
$func2 = 'greet';
$func3 = 'add';

// Call the functions using the variables
if (function_exists($func1)) {
    $func1();  // Output: Hello, World!
    echo "\n";
}

if (function_exists($func2)) {
    $func2('Alice');  // Output: Hello, Alice!
    echo "\n";
}

if (function_exists($func3)) {
    $result = $func3(2, 3);  // Output: 5
    echo $result;
}