<?php
// Load Composer autoloader
require _DIR_ . '/vendor/autoload.php';

use App\Config\Database;

echo "<h3>Testing Database Connection...</h3>";

try {
    // Create Database instance
    $db = new Database();

    // Connect to the database
    $conn = $db->connect();

    // Check if connection was successful
    if ($conn) {
        echo "<p style='color: green;'> Database connection successful!</p>";
    } else {
        echo "<p style='color: red;'> Database connection failed.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'> Error: " . $e->getMessage() . "</p>";
}