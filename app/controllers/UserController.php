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
        require_once dirname(_DIR_) . '/views/login.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /FundiApp/public/?action=login");
            exit();
        }
        $user = $this->user->findById($_SESSION['user_id']);
        require_once dirname(_DIR_) . '/views/dashboard.php';
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit();
        }

        $user_data = $this->user->findById($_SESSION['user_id']);
        if (!$user_data) {
            $_SESSION['message'] = "User not found.";
            header("Location: ?action=dashboard");
            exit();
        }

        require_once dirname(_DIR_) . '/views/profile.php';
    }

    public function editProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /FundiApp/public/?action=login");
            exit();
        }
        $user = $this->user->findById($_SESSION['user_id']);
        require_once dirname(_DIR_) . '/views/edit-profile.php';
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $id = $_SESSION['user_id'];

            if ($this->user->update($id, $name, $email)) {
                $_SESSION['message'] = "Profile updated successfully!";
                header("Location: ?action=dashboard");
                exit();
            } else {
                $message = "Failed to update profile. Please try again.";
                $user = $this->user->findById($id);
                require_once dirname(_DIR_) . '/views/edit-profile.php';
            }
        } else {
            header("Location: ?action=dashboard");
            exit();
        }
    }

    public function showChangePasswordPage() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /FundiApp/public/?action=login");
            exit();
        }
        require_once dirname(_DIR_) . '/views/change-password.php';
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            $user_id = $_SESSION['user_id'];
            $current_user = $this->user->findById($user_id);

            if (!$current_user || !password_verify($current_password, $current_user['password'])) {
                $message = "Your current password is incorrect.";
                require_once dirname(_DIR_) . '/views/change-password.php';
                return;
            }
            if (strlen($new_password) < 8 || $new_password !== $confirm_password) {
                $message = "New passwords do not match or are too short.";
                require_once dirname(_DIR_) . '/views/change-password.php';
                return;
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($this->user->updatePassword($user_id, $hashed_password)) {
                $_SESSION['message'] = "Your password has been changed successfully.";
                header("Location: ?action=dashboard");
                exit();
            } else {
                $message = "An error occurred. Please try again.";
                require_once dirname(_DIR_) . '/views/change-password.php';
            }
        } else {
            header("Location: ?action=login");
            exit();
        }
    }
    
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

    public function showResetPasswordPage() {
        $token = $_GET['token'] ?? null;
        $user = $this->user->findByResetToken($token);
        
        if (!$token || !$user) {
            $_SESSION['message'] = "This password reset link is invalid or has expired.";
            header("Location: ?action=login");
            exit();
        }
        
        require_once dirname(_DIR_) . '/views/reset-password.php';
    }
    
    public function updatePasswordFromReset() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            $user = $this->user->findByResetToken($token);
            if (!$user) {
                header("Location: ?action=login");
                exit();
            }

            if (strlen($new_password) < 8 || $new_password !== $confirm_password) {
                $message = "Passwords do not match or are too short. Please try again.";
                require_once dirname(_DIR_) . '/views/reset-password.php';
                return;
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($this->user->updatePasswordByToken($token, $hashed_password)) {
                $_SESSION['message'] = "Your password has been reset successfully. Please log in.";
                header("Location: ?action=login");
                exit();
            }
        }
    }

    // --- PRIVATE HELPER METHODS ---

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

    private function sendPasswordResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "?action=resetPassword&token=" . $token;

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