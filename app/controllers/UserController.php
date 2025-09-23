<?php
// We need to use the PHPMailer classes, so we include them at the top.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    // Private properties to hold the database connection and the User model.
    private $conn;
    private $user;

    // The constructor runs when a new UserController object is created.
    public function __construct() {
        // Create a new database connection object.
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Create a new User object, passing the database connection to it.
        $this->user = new User($this->conn);
    }

    // --- ROUTER METHODS ---

    public function home() {
        require_once dirname(__DIR__) . '/views/home.php';
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
        require_once dirname(__DIR__) . '/views/register.php';
    }

    public function login() {
        $message = '';
        $show_2fa_form = false;

        if (isset($_POST['login'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $found_user = $this->user->findByEmail($email);

            if ($found_user && password_verify($password, $found_user['password'])) {
                $two_fa_code = rand(100000, 999999);
                $_SESSION['2fa_user_id'] = $found_user['id'];
                $_SESSION['2fa_code'] = $two_fa_code;

                if ($this->send2FACode($found_user['email'], $two_fa_code)) {
                    $message = "A verification code has been sent to your email.";
                    $show_2fa_form = true;
                } else {
                    $message = "Could not send verification code. Please try again.";
                }
            } else {
                $message = "Invalid email or password.";
            }
        }

        if (isset($_POST['verify_2fa'])) {
            $submitted_code = $_POST['2fa_code'];
            if (isset($_SESSION['2fa_code']) && $submitted_code == $_SESSION['2fa_code']) {
                $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
                unset($_SESSION['2fa_user_id']);
                unset($_SESSION['2fa_code']);
                header("Location: ?action=dashboard");
                exit();
            } else {
                $message = "Invalid verification code. Please try again.";
                $show_2fa_form = true;
            }
        }
        require_once dirname(__DIR__) . '/views/login.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /FundiApp/public/?action=login");
            exit();
        }
        require_once dirname(__DIR__) . '/views/dashboard.php';
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ?action=login");
        exit();
    }

    // --- PRIVATE HELPER METHOD ---
    private function send2FACode($email, $code) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
           
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USER'];
            $mail->Password   = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Fundi-Fix Verification Code';
            $mail->Body    = 'Your verification code is: <b>' . $code . '</b>';
            $mail->AltBody = 'Your verification code is: ' . $code;

            $mail->send();
            return true;
        } catch (Exception $e) {
           
            return false;
        }
    }
}

