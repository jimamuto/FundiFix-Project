<?php
// Start a session on every page
session_start();

// Load PHPMailer and Dotenv libraries
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// --- DATABASE CONNECTION CLASS ---
class Database {
    private static ?\PDO $conn = null;

    public static function getConnection(): \PDO {
        if (self::$conn !== null) {
            return self::$conn;
        }

        $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port    = $_ENV['DB_PORT'] ?? '3310';
        $dbname  = $_ENV['DB_NAME'] ?? 'fundi_database';
        $user    = $_ENV['DB_USER'] ?? 'root';
        $pass    = $_ENV['DB_PASS'] ?? '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            self::$conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
                PDO::ATTR_EMULATE_PREPARES   => false,                  
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return self::$conn;
    }
}
