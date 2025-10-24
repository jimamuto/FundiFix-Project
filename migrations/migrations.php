<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

// Get PDO connection
$db = new Database();
$conn = $db->connect();

// Helper function to drop a table
function dropTable($conn, $tableName) {
    try {
        $conn->exec("DROP TABLE IF EXISTS `$tableName`");
        echo "Dropped '$tableName' table.<br>";
    } catch (PDOException $e) {
        echo "Error dropping '$tableName': " . $e->getMessage() . "<br>";
    }
}

// Helper function to create a table
function createTable($conn, $tableName, $columns, $extra = "") {
    try {
        $cols = [];
        foreach ($columns as $name => $definition) {
            $cols[] = "`$name` $definition";
        }
        $sql = "CREATE TABLE `$tableName` (" . implode(", ", $cols);
        if (!empty($extra)) {
            $sql .= ", " . $extra;
        }
        $sql .= ")";
        $conn->exec($sql);
        echo "Created '$tableName' table.<br>";
    } catch (PDOException $e) {
        echo "Error creating '$tableName': " . $e->getMessage() . "<br>";
    }
}

/**
 * Drop in dependency order:
 * fundi_services → reviews → payments → bookings → fundi_profiles → services → users
 */

// ------------------------- DROP FIRST -------------------------
dropTable($conn, 'fundi_services');
dropTable($conn, 'reviews');
dropTable($conn, 'payments');
dropTable($conn, 'bookings');
dropTable($conn, 'fundi_profiles');
dropTable($conn, 'services');
dropTable($conn, 'users');

// ------------------------- USERS -------------------------
createTable($conn, 'users', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'name' => 'VARCHAR(255) NOT NULL',
  'email' => 'VARCHAR(255) NOT NULL UNIQUE',
  'password' => 'VARCHAR(255) NOT NULL',
  "role" => "ENUM('resident','fundi','admin') NOT NULL DEFAULT 'resident'",
  'verification_code' => 'VARCHAR(10) DEFAULT NULL',
  'is_verified' => 'TINYINT(1) DEFAULT 0',
  'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
  'password_reset_token' => 'VARCHAR(255) DEFAULT NULL',
  'password_reset_expires_at' => 'DATETIME DEFAULT NULL'
]);



// ------------------------- FUNDI PROFILES -------------------------
createTable($conn, 'fundi_profiles', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'user_id' => 'INT(11) NOT NULL',
  'skills' => 'VARCHAR(255) DEFAULT NULL',
  'location' => 'VARCHAR(255) DEFAULT NULL',
  'phone_number' => 'VARCHAR(20) DEFAULT NULL',
  'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

createTable($conn, 'services', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'name' => 'VARCHAR(100) NOT NULL UNIQUE',
  'category' => 'VARCHAR(100) NOT NULL', 
  'description' => 'TEXT DEFAULT NULL', 
  'price' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
  "status" => "ENUM('active','inactive') NOT NULL DEFAULT 'active'",
  'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

// ------------------------- FUNDI SERVICES -------------------------
createTable($conn, 'fundi_services', [
  'fundi_profile_id' => 'INT(11) NOT NULL',
  'service_id' => 'INT(11) NOT NULL'
], "PRIMARY KEY (fundi_profile_id, service_id)");

// ------------------------- BOOKINGS -------------------------
createTable($conn, 'bookings', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'resident_id' => 'INT(11) NOT NULL',
  'fundi_id' => 'INT(11) NOT NULL',
  "status" => "ENUM('pending','accepted','completed','cancelled') NOT NULL DEFAULT 'pending'",
  'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

// ------------------------- REVIEWS -------------------------
createTable($conn, 'reviews', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'booking_id' => 'INT(11) NOT NULL UNIQUE',
  'resident_id' => 'INT(11) NOT NULL',
  'fundi_id' => 'INT(11) NOT NULL',
  'rating' => 'TINYINT(3) UNSIGNED NOT NULL',
  'comment' => 'TEXT DEFAULT NULL',
  'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

// ------------------------- PAYMENTS -------------------------
createTable($conn, 'payments', [
  'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
  'booking_id' => 'INT(11) NOT NULL UNIQUE',
  'amount' => 'DECIMAL(10,2) NOT NULL',
  "status" => "ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending'",
  'transaction_id' => 'VARCHAR(255) DEFAULT NULL',
  'payment_method' => 'VARCHAR(50) DEFAULT NULL',
  'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
  'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

echo "<br><strong> All migrations executed successfully.</strong>";
