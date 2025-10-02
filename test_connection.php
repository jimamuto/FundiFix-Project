<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getConnection();
    echo " Database connection works!";
} catch (Exception $e) {
    echo " Connection failed: " . $e->getMessage();
}
