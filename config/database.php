<?php
// Start a session on every page.
session_start();


// This loads both PHPMailer and the Dotenv library.
require_once '../vendor/autoload.php';

//LOAD ENVIRONMENT VARIABLES FROM .ENV FILE 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


// --- THE DATABASE CONNECTION CLASS ---

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            
            $this->conn = new mysqli(
                $_ENV['DB_HOST'], 
                $_ENV['DB_USER'], 
                $_ENV['DB_PASS'], 
                $_ENV['DB_NAME'], 
                $_ENV['DB_PORT']
            );
        } catch (mysqli_sql_exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

