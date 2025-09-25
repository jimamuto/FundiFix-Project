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

    // ... register, login, dashboard, profile, editProfile, updateProfile, changePassword methods here (same as Commit 5) ...

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ?action=login");
        exit();
    }

    // --- FORGOT/RESET PASSWORD METHODS ---

    public function showForgotPasswordPage() {
        require_once dirname(_DIR_) . '/views/forgot-password.php';
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $found_user = $this->user->findByEmail($email);

            if ($found_user) {
                $token = $this->user->setResetToken($email);
                if ($token) {
                    $this->sendPasswordResetEmail($email, $token);
                }
            }
            $_SESSION['message'] = "If an account with that email exists, a password reset link has been sent.";
            header("Location: ?action=forgotPassword");
            exit();
        }
    }

    private function send2FACode($email, $code) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USER'];
            $mail->Password   = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

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

    private function sendPasswordResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "?action=resetPassword&token=" . $token;

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USER'];
            $mail->Password   = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Fundi-Fix Password Reset Request';
            $mail->Body    = 'Hello,<br><br>We received a request to reset your password. Please click the link below to proceed:<br><br><a href="' . $reset_link . '">' . $reset_link . '</a><br><br>If you did not request this, you can safely ignore this email.';
            $mail->AltBody = 'To reset your password, please visit the following link: ' . $reset_link;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}