<?php
// config.php
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "password123";

// functions.php
function calculateTotal($items) {
    return array_sum($items);
}

// Let's demonstrate the differences between include and require

// 1. Using include
echo "Using include:\n";
echo "-------------\n";

// If file exists, code continues
include 'config.php';
echo "Database Host: " . $dbHost . "\n";

// If file doesn't exist, warning is shown but code continues
include 'non_existent_file.php';
echo "This line will still execute after include error\n\n";

// 2. Using require
echo "Using require:\n";
echo "-------------\n";

// If file exists, code continues
require 'config.php';
echo "Database Host: " . $dbHost . "\n";

// If file doesn't exist, fatal error occurs and script stops
require 'another_non_existent_file.php';
echo "This line will never execute after require error\n";

// 3. Include_once and require_once
echo "Using include_once and require_once:\n";
echo "--------------------------------\n";

// First inclusion
include_once 'functions.php';
$total1 = calculateTotal([1, 2, 3]);
echo "First total: " . $total1 . "\n";

// Second inclusion - won't include again
include_once 'functions.php';
$total2 = calculateTotal([4, 5, 6]);
echo "Second total: " . $total2 . "\n";

// Same behavior with require_once
require_once 'config.php';
echo "Config loaded once\n";
require_once 'config.php';
echo "Config not loaded again\n";

/*
Key Differences:
1. Error Handling:
   - include: Produces a WARNING if file not found (script continues)
   - require: Produces a FATAL ERROR if file not found (script stops)

2. Use Cases:
   - include: For optional files (templates, functions)
   - require: For mandatory files (configuration, core functions)

3. Performance:
   - include_once/require_once: Check if file already included
   - include/require: Always include file, might cause duplicate declarations

4. Best Practices:
   - Use require for critical files
   - Use include for optional features
   - Use _once versions when multiple inclusions might occur
*/
?>