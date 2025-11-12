<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Fundi;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

class UserController
{
    private User $user;
    private Booking $bookingModel;
    private string $baseUrl;

    public function __construct(PDO $db)

    
    {

          // Add session start in constructor
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->user = new User($db);
        $this->bookingModel = new Booking($db); 
        $this->baseUrl = "http://localhost/FundiFix-Project/public/index.php";
    }

    // ---------------- HOME ----------------
    public function home()
    {

         if (isset($_SESSION['reset_email'])) {
        // Don't redirect if user is in password reset process
        $pageTitle = "Welcome to FundiFix";
        include __DIR__ . '/../Views/home.php';
        return;
    }
        if (isset($_SESSION['user']) && !isset($_SESSION['2fa_user']) && !isset($_SESSION['verify_email'])) {
            $role = $_SESSION['user']['role'];
            $redirect = $role === 'admin' ? 'admin_dashboard' : 'dashboard';
            header("Location: {$this->baseUrl}?action=$redirect");
            exit;
        }

        if (isset($_SESSION['2fa_user'])) {
            header("Location: {$this->baseUrl}?action=verify2fa");
            exit;
        }

        if (isset($_SESSION['verify_email'])) {
            header("Location: {$this->baseUrl}?action=verifyAccount");
            exit;
        }

        $pageTitle = "Welcome to FundiFix";
        include __DIR__ . '/../Views/home.php';
    }

  // ---------------- DASHBOARD ----------------
public function dashboard()
{
    if (!isset($_SESSION['user'])) {
        header("Location: {$this->baseUrl}?action=login");
        exit;
    }

    $user = $_SESSION['user'];
    $userId = $user['id'];
    $userRole = $user['role'];
    
    // Load user stats for the dashboard
    if ($userRole === 'fundi') {
        $userStats = $this->user->getFundiStats($userId);
        
        // Format the stats properly for display
        $userStats = [
            'pending_bookings' => $userStats['pending_bookings'] ?? 0,
            'completed_bookings' => $userStats['completed_bookings'] ?? 0,
            'total_bookings' => $userStats['total_bookings'] ?? 0,
            'average_rating' => number_format($userStats['average_rating'] ?? 0, 1),
            'total_earnings' => 'KES ' . number_format($userStats['estimated_earnings'] ?? 0, 2)
        ];
        
    } else {
        $userStats = [
            'pending_bookings' => $this->user->getPendingBookings($userId),
            'completed_bookings' => $this->user->getCompletedBookings($userId),
            'total_bookings' => $this->user->getTotalBookings($userId),
            'average_rating' => $this->user->getAverageRatingGiven($userId)
        ];
        
        if ($userRole === 'resident') {
            $userStats['total_earnings'] = $this->user->getTotalSpent($userId);
        }
    }
    
    $_SESSION['user_stats'] = $userStats;
    require_once(__DIR__ . '/../Views/dashboard.php');
}
    // ---------------- LOGIN ----------------
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $message = "Please enter both email and password.";
                require_once(__DIR__ . '/../Views/login.php');
                return;
            }

            $user = $this->user->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $message = "Invalid email or password.";
                require_once(__DIR__ . '/../Views/login.php');
                return;
            }

            // Skip 2FA for admins
            if ($user['role'] === 'admin') {
                $_SESSION['user'] = $user;
                header("Location: {$this->baseUrl}?action=admin_dashboard");
                exit;
            }

            // Check if account is verified
            if (!$user['is_verified']) {
                $message = "Please verify your account first. Check your email for the verification code.";
                require_once(__DIR__ . '/../Views/login.php');
                return;
            }

            // Generate 2FA code and store in DATABASE
            $code = rand(100000, 999999);
            
            // Store in database
            if ($this->user->store2FACode($email, $code)) {
                // Send email
                $this->sendEmail($email, "Your 2FA Code", 
                    "Hello {$user['name']},<br>Your 2FA code is: <b>$code</b>.<br>Use this to complete your login.");

                // Redirect with email as parameter
                header("Location: {$this->baseUrl}?action=verify2fa&email=" . urlencode($email));
                exit;
            } else {
                $message = "Failed to generate verification code. Please try again.";
                require_once(__DIR__ . '/../Views/login.php');
            }
        }

        require_once(__DIR__ . '/../Views/login.php');
    }

    // ---------------- VERIFY 2FA ----------------
    public function verify2fa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $email = $_POST['email'] ?? $_GET['email'] ?? '';

            // Verify against 2FA database (twofa_code)
            $user = $this->user->verify2FACode($email, $code);
            
            if ($user) {
                // Clear the 2FA code from database
                $this->user->clear2FACode($email);
                
                // Set user session (remove password for security)
                unset($user['password'], $user['twofa_code'], $user['twofa_created_at']);
                $_SESSION['user'] = $user;

                header("Location: {$this->baseUrl}?action=dashboard");
                exit;
            } else {
                $message = "Invalid verification code or code expired. Please try again.";
                require_once(__DIR__ . '/../Views/verify2fa.php');
            }
        } else {
            require_once(__DIR__ . '/../Views/verify2fa.php');
        }
    }

    // ---------------- REGISTER ----------------
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'resident';

            if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
                $message = "All fields are required.";
                require_once(__DIR__ . '/../Views/register.php');
                return;
            }

            if ($password !== $confirm) {
                $message = "Passwords do not match.";
                require_once(__DIR__ . '/../Views/register.php');
                return;
            }

            if (!in_array($role, ['resident', 'fundi'])) {
                $message = "Please select a valid role.";
                require_once(__DIR__ . '/../Views/register.php');
                return;
            }

            if ($this->user->findByEmail($email)) {
                $message = "Email already registered.";
                require_once(__DIR__ . '/../Views/register.php');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verificationCode = rand(100000, 999999);

            $created = $this->user->register($name, $email, $hashedPassword, $role, $verificationCode);

            if ($created) {
                $_SESSION['verify_email'] = $email;
                $subject = "FundiFix Verification Code";
                $body = "Hello $name,<br>Your verification code is: <b>$verificationCode</b>.";

                $this->sendEmail($email, $subject, $body);
                header("Location: {$this->baseUrl}?action=verifyAccount");
                exit;
            } else {
                $message = "Registration failed. Please try again.";
            }
        }

        require_once(__DIR__ . '/../Views/register.php');
    }

    // ---------------- VERIFY ACCOUNT ----------------
    public function verifyAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $code = $_POST['code'] ?? '';

            // Verify account using verification_code 
            $verified = $this->user->verifyAccount($email, (int)$code);
            
            if ($verified) {
                $message = "Account verified successfully! You can now login.";
                require_once(__DIR__ . '/../Views/login.php');
                return;
            } else {
                $message = "Invalid verification code. Please check the code and try again.";
                require_once(__DIR__ . '/../Views/verify.php');
            }
        } else {
            // For GET requests, show the verification form
            require_once(__DIR__ . '/../Views/verify.php');
        }
    }

    // ---------------- LOGOUT ----------------
    public function logout()
    {
        session_destroy();
        session_start();
        header("Location: {$this->baseUrl}?action=home");
        exit;
    }

    // ---------------- PROFILE ----------------
    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: {$this->baseUrl}?action=login");
            exit;
        }

        $user = $_SESSION['user'];
        require_once(__DIR__ . '/../Views/profile.php');
    }

    // ---------------- CHANGE PASSWORD ----------------
    public function changePassword()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: {$this->baseUrl}?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $user_id = $_SESSION['user']['id'];

            $user = $this->user->findById($user_id);

            if (!$user || !password_verify($current_password, $user['password'])) {
                $message = "Current password is incorrect.";
                require_once(__DIR__ . '/../Views/changepassword.php');
                return;
            }

            if ($new_password !== $confirm_password) {
                $message = "New passwords do not match.";
                require_once(__DIR__ . '/../Views/changepassword.php');
                return;
            }

            if (strlen($new_password) < 8) {
                $message = "New password must be at least 8 characters long.";
                require_once(__DIR__ . '/../Views/changepassword.php');
                return;
            }

            $updated = $this->user->updatePassword($user_id, $new_password);
            $message = $updated ? "Password updated successfully!" : "Failed to update password.";
        }

        require_once(__DIR__ . '/../Views/changepassword.php');
    }

    // ---------------- UPDATE PROFILE ----------------
    public function updateProfile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: {$this->baseUrl}?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $user_id = $_SESSION['user']['id'];

            if (empty($name)) {
                $message = "Name cannot be empty.";
                require_once(__DIR__ . '/../Views/profile.php');
                return;
            }

            $updated = $this->user->updateProfile($user_id, $name);

            if ($updated) {
                $_SESSION['user']['name'] = $name;
                $_SESSION['message'] = "Profile updated successfully!";
                header("Location: {$this->baseUrl}?action=profile");
                exit;
            } else {
                $message = "Failed to update profile.";
            }
        }

        require_once(__DIR__ . '/../Views/profile.php');
    }
// ---------------- FORGOT PASSWORD WITH 2FA ----------------
public function forgotPassword()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $message = "Please enter your email address.";
            require_once(__DIR__ . '/../Views/forgotpassword.php');
            return;
        }

        $user = $this->user->findByEmail($email);
        if (!$user) {
            $message = "Email not found in our system.";
            require_once(__DIR__ . '/../Views/forgotpassword.php');
            return;
        }

        // Generate 2FA code for password reset
        $code = rand(100000, 999999);
        
        if ($this->user->store2FACode($email, $code)) {
            // Send 2FA code via email
            $this->sendEmail($email, "Password Reset Verification - FundiFix", 
                "Hello {$user['name']},<br>Your password reset verification code is: <b>$code</b>.<br>This code will expire in 10 minutes.");

            // Store email in session for verification step
            $_SESSION['reset_email'] = $email;
            header("Location: {$this->baseUrl}?action=verifyreset2fa");
            exit;
        } else {
            $message = "Failed to generate verification code. Please try again.";
        }
    }

    require_once(__DIR__ . '/../Views/forgotpassword.php');
}

// ---------------- VERIFY PASSWORD RESET 2FA ----------------
public function verifyreset2fa()
{
    if (!isset($_SESSION['reset_email'])) {
        header("Location: {$this->baseUrl}?action=forgotpassword");
        exit;
    }

    $email = $_SESSION['reset_email'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['code'] ?? '';

        // Verify 2FA code
        $user = $this->user->verify2FACode($email, $code);
        
        if ($user) {
            // Clear the 2FA code
            $this->user->clear2FACode($email);
            
            // Use the original setResetToken method (it should work now with the fix)
            $token = $this->user->setResetToken($email);
            
            if ($token) {
                unset($_SESSION['reset_email']);
                header("Location: {$this->baseUrl}?action=resetpassword&token=$token");
                exit;
            } else {
                $message = "Failed to generate reset token. Please try again.";
            }
        } else {
            $message = "Invalid verification code or code expired. Please try again.";
        }
    }

    require_once(__DIR__ . '/../Views/verifyreset2fa.php');
}

    // ---------------- RESET PASSWORD ----------------
    public function resetPassword()
    {
        $token = $_GET['token'] ?? ($_POST['token'] ?? '');

        if (empty($token)) {
            header("Location: {$this->baseUrl}?action=forgotpassword");
            exit;
        }

        $user = $this->user->findByResetToken($token);
        if (!$user) {
            $message = "Invalid or expired reset token.";
            require_once(__DIR__ . '/../Views/resetpassword.php');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($new_password !== $confirm_password) {
                $message = "Passwords do not match.";
            } elseif (strlen($new_password) < 8) {
                $message = "Password must be at least 8 characters long.";
            } else {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $updated = $this->user->updatePasswordByToken($token, $hash);

                if ($updated) {
                    $_SESSION['message'] = "Password reset successfully!";
                    header("Location: {$this->baseUrl}?action=login");
                    exit;
                } else {
                    $message = "Failed to reset password.";
                }
            }
        }

        require_once(__DIR__ . '/../Views/resetpassword.php');
    }

   // ---------------- RESIDENT ANALYTICS ----------------
public function residentAnalytics()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resident') {
        $_SESSION['error'] = 'Access denied. Residents only.';
        header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    
    // Get real analytics data from models
    $totalBookings = $this->user->getTotalBookings($user_id);
    $hasData = $totalBookings > 0;
    
    if (!$hasData && $user_id == 6) {
        // Demo resident (Jane Resident) - show sample data
        $analyticsData = $this->getDemoResidentAnalytics();
    } elseif (!$hasData) {
        // New resident - show empty state
        $analyticsData = [
            'total_bookings' => 0,
            'completed_bookings' => 0,
            'pending_bookings' => 0,
            'cancelled_bookings' => 0,
            'total_spent' => 'KES 0',
            'average_rating' => '0.0',
            'has_data' => false
        ];
    } else {
        // Resident with real data
        $analyticsData = [
            'total_bookings' => $totalBookings,
            'completed_bookings' => $this->user->getCompletedBookings($user_id),
            'pending_bookings' => $this->user->getPendingBookings($user_id),
            'cancelled_bookings' => $this->user->getCancelledBookings($user_id),
            'monthly_trends' => $this->user->getMonthlyBookingTrends($user_id),
            'service_types' => $this->user->getServiceTypeBreakdown($user_id),
            'total_spent' => $this->user->getTotalSpent($user_id),
            'average_rating' => $this->user->getAverageRatingGiven($user_id),
            'has_data' => true
        ];
    }

    $_SESSION['resident_analytics'] = $analyticsData;
    require_once "../App/Views/Users/resident_analytics.php";
}

//demo data
private function getDemoResidentAnalytics()
{
    return [
        'total_bookings' => 15,
        'completed_bookings' => 12,
        'pending_bookings' => 2,
        'cancelled_bookings' => 1,
        'total_spent' => 'KES 22,500',
        'average_rating' => '4.6',
        'has_data' => true
    ];
}
    // ---------------- EMAIL ----------------
    public function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;

            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME'] ?? 'FundiFix');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();
        } catch (\Exception $e) {
            error_log("Mail error: " . $e->getMessage());
            return false;
        }
    }
}
?>