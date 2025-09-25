<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    private $conn;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->user = new User($this->conn);
    }

    // --- CORE ROUTER METHODS ---

    public function home() {
        require_once dirname(_DIR_) . '/views/home.php';
    }
}