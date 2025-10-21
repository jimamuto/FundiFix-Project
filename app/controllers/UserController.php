<?php

namespace App\Controllers;

use App\Models\User;
use App\Config\Database;



// For TOTP 2FA
use OTPHP\TOTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    private $conn;
    private $user;

 public function __construct() {
    $db = new Database();
    $this->conn = $db->connect();
    $this->user = new User($this->conn);
}

    // --- CORE ROUTER METHODS ---

    public function home() {
        require_once __DIR__ . '/../views/home.php';
    }

    public function register() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $role = htmlspecialchars(strip_tags($_POST['role']));
            $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
            $twofa_secret = null;
            if ($enable_2fa) {
                $totp = TOTP::create();
                $twofa_secret = $totp->getSecret();
            }

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $message = "Please fill in all fields.";
            } else {
                if ($this->user->register($name, $email, $password, $role, $twofa_secret, $enable_2fa)) {
                    if ($enable_2fa) {
                        $_SESSION['2fa_setup_secret'] = $twofa_secret;
                        $_SESSION['2fa_setup_email'] = $email;
                        header("Location: ?action=setup2fa");
                        exit();
                    } else {
                        $_SESSION['message'] = "Registration successful! Please log in.";
                        header("Location: ?action=login");
                        exit();
                    }
                } else {
                    $message = "Registration failed. Email may already be in use.";
                }
            }
        }
        require_once __DIR__ . '/../views/register.php';
    }

    // 2FA setup page (QR code)
    public function setup2fa() {
        if (!isset($_SESSION['2fa_setup_secret']) || !isset($_SESSION['2fa_setup_email'])) {
            header("Location: ?action=register");
            exit();
        }
        $secret = $_SESSION['2fa_setup_secret'];
        $email = $_SESSION['2fa_setup_email'];
        $totp = TOTP::create($secret);
        $totp->setLabel($email);
        $qrCodeUrl = $totp->getQrCodeUri();
        require_once __DIR__ . '/../views/setup2fa.php';
    }

    public function login() {
        $message = '';
        $show_2fa_form = false;
        $user = null;

        if (isset($_POST['login'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $user = $this->user->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['twofa_enabled'] && !empty($user['twofa_secret'])) {
                    $_SESSION['2fa_user_id'] = $user['id'];
                    $show_2fa_form = true;
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: ?action=dashboard");
                    exit();
                }
            } else {
                $message = "Invalid email or password.";
            }
        }

        if (isset($_POST['verify_2fa'])) {
            $user_id = $_SESSION['2fa_user_id'] ?? null;
            $code = $_POST['2fa_code'] ?? '';
            if ($user_id && $code) {
                $secret = $this->user->get2FASecret($user_id);
                $totp = TOTP::create($secret);
                if ($totp->verify($code)) {
                    $_SESSION['user_id'] = $user_id;
                    unset($_SESSION['2fa_user_id']);
                    header("Location: ?action=dashboard");
                    exit();
                } else {
                    $message = "Invalid 2FA code.";
                    $show_2fa_form = true;
                }
            } else {
                $message = "Invalid 2FA attempt.";
            }
        }

        require_once __DIR__ . '/../views/login.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit();
        }
        $user = $this->user->findById($_SESSION['user_id']);
        require_once __DIR__ . '/../views/dashboard.php';
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

        require_once __DIR__ . '/../views/profile.php';
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
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
                require_once __DIR__ . '/../views/edit-profile.php';
            }
        } else {
            header("Location: ?action=dashboard");
            exit();
        }
    }

    public function showChangePasswordPage() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit();
        }
        require_once __DIR__ . '/../views/change-password.php';
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            $user_id = $_SESSION['user_id'];
            $current_user = $this->user->findById($user_id);

            if (!$current_user || !password_verify($current_password, $current_user['password'])) {
                $message = "Your current password is incorrect.";
                require_once __DIR__ . '/../views/change-password.php';
                return;
            }
            if (strlen($new_password) < 8 || $new_password !== $confirm_password) {
                $message = "New passwords do not match or are too short.";
                require_once __DIR__ . '/../views/change-password.php';
                return;
            }

            if ($this->user->updatePassword($user_id, $new_password)) {
                $_SESSION['message'] = "Your password has been changed successfully.";
                header("Location: ?action=dashboard");
                exit();
            } else {
                $message = "An error occurred. Please try again.";
                require_once __DIR__ . '/../views/change-password.php';
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

    // --- PRIVATE HELPER METHODS ---
    // (No longer needed: send2FACode for email-based 2FA)
}
