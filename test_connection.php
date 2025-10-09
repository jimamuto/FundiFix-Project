<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

echo "<h3>Testing Database Connection...</h3>";

$db = new Database();
$conn = $db->connect();

if ($conn) {
    echo "<p style='color: green;'>Connection successful!</p>";
} else {
    echo "<p style='color: red;'>Connection failed.</p>";
}
