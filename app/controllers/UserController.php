<?php

namespace App\Controllers;

use App\Models\User;
use App\Config\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController
{
    private $conn;
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
        $this->user = new User($this->conn);
    }

    // ---------------------------------------------------------------------
    // HOME PAGE
    // ---------------------------------------------------------------------
    public function home(): void
    {
        require_once dirname(__DIR__) . '/Views/home.php';
    }

    // ---------------------------------------------------------------------
    // REGISTER
    // ---------------------------------------------------------------------
    public function register(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $role = htmlspecialchars(strip_tags($_POST['role']));

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $message = "Please fill in all fields.";
            } else {
                if ($this->user->register($name, $email, $password, $role)) {
                    $user = $this->user->findByEmail($email);
                    $verification_code = $user['verification_code'];

                    if ($this->sendVerificationEmail($email, $name, $verification_code)) {
                        $_SESSION['pending_verification_email'] = $email;
                        header("Location: ?action=verifyAccount");
                        exit;
                    } else {
                        $message = "Registered successfully, but verification email could not be sent.";
                    }
                } else {
                    $message = "Registration failed. Email may already exist.";
                }
            }
        }

        require_once dirname(__DIR__) . '/Views/register.php';
    }

    // ---------------------------------------------------------------------
    // ACCOUNT VERIFICATION
    // ---------------------------------------------------------------------
    public function verifyAccount(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $code = htmlspecialchars(strip_tags($_POST['code']));

            if ($this->user->verifyAccount($email, $code)) {
                $_SESSION['message'] = "Account verified successfully! You can now log in.";
                unset($_SESSION['pending_verification_email']);
                header("Location: ?action=login");
                exit;
            } else {
                $message = "Invalid verification code or email.";
            }
        }

        require_once dirname(__DIR__) . '/Views/verify.php';
    }

    // ---------------------------------------------------------------------
    // LOGIN (Admin skips 2FA)
    // ---------------------------------------------------------------------
    public function login(): void
    {
        $message = '';
        $show_2fa_form = false;

        // Step 1: User submits login credentials
        if (isset($_POST['login'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $found_user = $this->user->findByEmail($email);

            if ($found_user && password_verify($password, $found_user['password'])) {

                // Ensure user is verified (skip this for admin)
                if ($found_user['role'] !== 'admin' && !$found_user['is_verified']) {
                    $_SESSION['message'] = "Please verify your account before logging in.";
                    $_SESSION['pending_verification_email'] = $found_user['email'];
                    header("Location: ?action=verifyAccount");
                    exit;
                }

                // If ADMIN — skip 2FA
                if ($found_user['role'] === 'admin') {
                    $_SESSION['user_id'] = $found_user['id'];
                    $_SESSION['role'] = 'admin';
                    header("Location: ?action=adminDashboard");
                    exit;
                }

                // Generate and send 2FA code for other users
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

        // Step 2: User enters 2FA code
        if (isset($_POST['verify_2fa'])) {
            $submitted_code = $_POST['2fa_code'] ?? '';
            if (isset($_SESSION['2fa_code']) && $submitted_code == $_SESSION['2fa_code']) {
                $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
                unset($_SESSION['2fa_user_id'], $_SESSION['2fa_code']);

                $user = $this->user->findById($_SESSION['user_id']);
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: ?action=adminDashboard');
                } elseif ($user['role'] === 'fundi') {
                    header('Location: ?action=fundiDashboard');
                } else {
                    header('Location: ?action=dashboard');
                }
                exit;
            }

            $message = "Invalid verification code. Please try again.";
            $show_2fa_form = true;
        }

        require_once dirname(__DIR__) . '/Views/login.php';
    }

    // ---------------------------------------------------------------------
    // DASHBOARD
    // ---------------------------------------------------------------------
    public function dashboard(): void
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $user = $this->user->findById($_SESSION['user_id']);

        if (!$user['is_verified']) {
            session_unset();
            $_SESSION['message'] = "Please verify your account first.";
            header("Location: ?action=login");
            exit;
        }

        require_once dirname(__DIR__) . '/Views/dashboard.php';
    }

    // ---------------------------------------------------------------------
    // PROFILE
    // ---------------------------------------------------------------------
    public function profile(): void
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $user_data = $this->user->findById($_SESSION['user_id']);
        if (!$user_data) {
            $_SESSION['message'] = "User not found.";
            header("Location: ?action=dashboard");
            exit;
        }

        require_once dirname(__DIR__) . '/Views/profile.php';
    }

    public function editProfile(): void
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $user = $this->user->findById($_SESSION['user_id']);
        require_once dirname(__DIR__) . '/Views/profile.php';
    }

    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));

            if ($this->user->update($id, $name, $email)) {
                $_SESSION['message'] = "Profile updated successfully!";
                header("Location: ?action=dashboard");
                exit;
            }

            $message = "Failed to update profile. Please try again.";
            $user = $this->user->findById($id);
            require_once dirname(__DIR__) . '/Views/edit-profile.php';
            return;
        }

        header("Location: ?action=dashboard");
        exit;
    }

    // ---------------------------------------------------------------------
    // PASSWORD CHANGE
    // ---------------------------------------------------------------------
    public function showChangePasswordPage(): void
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        require_once dirname(__DIR__) . '/Views/changepassword.php';
    }

    public function updatePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $current_user = $this->user->findById($user_id);

        if (!$current_user || !password_verify($current_password, $current_user['password'])) {
            $message = "Your current password is incorrect.";
            require dirname(__DIR__) . '/Views/changepassword.php';
            return;
        }

        if (strlen($new_password) < 8 || $new_password !== $confirm_password) {
            $message = "New passwords do not match or are too short.";
            require dirname(__DIR__) . '/Views/changepassword.php';
            return;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if ($this->user->updatePassword($user_id, $hashed_password)) {
            $_SESSION['message'] = "Your password has been changed successfully.";
            header("Location: ?action=dashboard");
            exit;
        }

        $message = "An error occurred. Please try again.";
        require dirname(__DIR__) . '/Views/changepassword.php';
    }

    // ---------------------------------------------------------------------
    // LOGOUT
    // ---------------------------------------------------------------------
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header("Location: ?action=login");
        exit;
    }

    // ---------------------------------------------------------------------
    // PASSWORD RESET
    // ---------------------------------------------------------------------
    public function showForgotPasswordPage(): void
    {
        require_once dirname(__DIR__) . '/Views/forgotpassword.php';
    }

    public function sendResetLink(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            exit;
        }
    }

    public function showResetPasswordPage(): void
    {
        $token = $_GET['token'] ?? null;
        $user = $this->user->findByResetToken($token);

        if (!$token || !$user) {
            $_SESSION['message'] = "This password reset link is invalid or has expired.";
            header("Location: ?action=login");
            exit;
        }

        require_once dirname(__DIR__) . '/Views/resetpassword.php';
    }

    public function updatePasswordFromReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?action=login");
            exit;
        }

        $token = $_POST['token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $user = $this->user->findByResetToken($token);
        if (!$user) {
            header("Location: ?action=login");
            exit;
        }

        if (strlen($new_password) < 8 || $new_password !== $confirm_password) {
            $message = "Passwords do not match or are too short. Please try again.";
            require dirname(__DIR__) . '/Views/resetpassword.php';
            return;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if ($this->user->updatePasswordByToken($token, $hashed_password)) {
            $_SESSION['message'] = "Your password has been reset successfully. Please log in.";
            header("Location: ?action=login");
            exit;
        }
    }

    // ---------------------------------------------------------------------
    // EMAIL HELPERS
    // ---------------------------------------------------------------------
    private function sendVerificationEmail(string $email, string $name, string $code): bool
    {
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
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'FundiFix - Verify Your Account';
            $mail->Body    = "Hello {$name},<br><br>Your verification code is <b>{$code}</b>.";
            $mail->AltBody = "Your verification code is: {$code}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function send2FACode(string $email, int $code): bool
    {
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
            $mail->Subject = 'Your FundiFix 2FA Code';
            $mail->Body    = 'Your login verification code is: <b>' . $code . '</b>';
            $mail->AltBody = 'Your login verification code is: ' . $code;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function sendPasswordResetEmail(string $email, string $token): bool
    {
        $mail = new PHPMailer(true);
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/FundiFix-Project/Public/?action=resetPassword&token=" . urlencode($token);

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
            $mail->Subject = 'FundiFix Password Reset';
            $mail->Body    = 'Click this link to reset your password: <a href="' . $reset_link . '">' . $reset_link . '</a>';
            $mail->AltBody = 'Reset your password using this link: ' . $reset_link;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}