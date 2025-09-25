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

    public function register() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $role = htmlspecialchars(strip_tags($_POST['role']));

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $message = "Please fill in all fields.";
            } else {
                if ($this->user->register($name, $email, $password, $role)) {
                    $_SESSION['message'] = "Registration successful! Please log in.";
                    header("Location: ?action=login");
                    exit();
                } else {
                    $message = "Registration failed. Email may already be in use.";
                }
            }
        }
        require_once dirname(_DIR_) . '/views/register.php';
    }

    public function login() {
        $message = '';
        if (isset($_POST['login'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $found_user = $this->user->findByEmail($email);

            if ($found_user && password_verify($password, $found_user['password'])) {
                $_SESSION['user_id'] = $found_user['id'];
                header("Location: ?action=dashboard");
                exit();
            } else {
                $message = "Invalid email or password.";
            }
        }
        require_once dirname(_DIR_) . '/views/login.php';
    }
}