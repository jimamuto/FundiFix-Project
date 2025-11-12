<?php
namespace App\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Fundi;
use App\Models\User;
use App\Config\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BookingController
{
    private $bookingModel;
    private $serviceModel;
    private $fundiModel;
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
        $this->bookingModel = new Booking($this->db);
        $this->serviceModel = new Service($this->db);
        $this->fundiModel = new Fundi($this->db);
        $this->userModel = new User($this->db);
    }

    // Display bookings for the logged-in user
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $role = $_SESSION['user']['role'];
        $bookings = $this->bookingModel->getByUserId($user_id, $role);

        if ($role === 'fundi') {
            require_once "../App/Views/Bookings/fundi_bookings.php";
        } else {
            require_once "../App/Views/Bookings/resident_bookings.php";
        }
    }

    // Display booking creation form for residents
    public function createForm()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resident') {
            $_SESSION['error'] = 'Only residents can make bookings';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=services_available');
            exit;
        }

        $services = $this->serviceModel->getActiveServices();
        require_once "../App/Views/Bookings/create.php";
    }

    // Handle new booking creation
    public function create()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resident') {
            $_SESSION['error'] = 'Only residents can make bookings';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=services_available');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resident_id = $_SESSION['user']['id'];
            $fundi_id = $_POST['fundi_id'];
            $service_id = $_POST['service_id'];
            $description = $_POST['description'] ?? '';
            $booking_date = $_POST['booking_date'] ?? date('Y-m-d H:i:s');

            if (empty($fundi_id) || empty($service_id)) {
                $_SESSION['error'] = 'Please select both service and fundi';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings_create');
                exit;
            }

            $result = $this->bookingModel->create(
                (int)$resident_id,
                (int)$fundi_id,
                (int)$service_id,
                'pending'
            );

            if ($result) {
                $bookingId = $this->db->lastInsertId();
                $emailSent = $this->sendBookingNotificationToFundi($bookingId);

                $successMessage = 'Booking created successfully!';
                $successMessage .= $emailSent
                    ? ' The fundi has been notified and will respond soon.'
                    : ' (Email notification failed, but booking was created)';

                $_SESSION['success'] = $successMessage;
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings');
            } else {
                $_SESSION['error'] = 'Failed to create booking. Please try again.';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings_create');
            }
            exit;
        }
    }

    // Fundi updates booking status
    public function updateStatus()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            $_SESSION['error'] = 'Only fundis can update booking status';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $booking_id = $_POST['booking_id'];
            $status = $_POST['status'];

            if ($this->bookingModel->updateStatus((int)$booking_id, $status)) {
                $emailSent = $this->sendStatusUpdateNotification($booking_id, $status);
                $successMessage = 'Booking status updated successfully!';
                if ($emailSent) {
                    $successMessage .= ' The resident has been notified.';
                }
                $_SESSION['success'] = $successMessage;
            } else {
                $_SESSION['error'] = 'Failed to update booking status';
            }

            header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings');
            exit;
        }
    }

    // Cancel a booking
    public function cancel($booking_id)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($this->bookingModel->updateStatus((int)$booking_id, 'cancelled')) {
            $_SESSION['success'] = 'Booking cancelled successfully!';
        } else {
            $_SESSION['error'] = 'Failed to cancel booking';
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=bookings');
        exit;
    }

    // Notify fundi about a new booking via email
    private function sendBookingNotificationToFundi($bookingId)
    {
        $booking = $this->bookingModel->findById((int)$bookingId);
        if (!$booking) return false;

        $fundi = $this->userModel->findById($booking['fundi_id']);
        $resident = $this->userModel->findById($booking['resident_id']);
        $service = $this->getServiceDetails($booking['service_id']);
        if (!$fundi || !$resident) return false;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME'] ?? 'FundiFix');
            $mail->addAddress($fundi['email']);
            $mail->isHTML(true);
            $mail->Subject = "New Booking Request - FundiFix";

            // Updated: Link now goes to login page
            $viewBookingsLink = "http://localhost/FundiFix-Project/public/index.php?action=login";
            $serviceName = $service ? $service['name'] : 'Service';

            $mail->Body = '
            <html>
            <head>
              <style>
                body { font-family: Arial, sans-serif; background-color: #f6f9fc; color: #333; margin: 0; padding: 0; }
                .container { background-color: #ffffff; max-width: 600px; margin: 30px auto; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; background-color: #007bff; color: #ffffff; padding: 15px; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; line-height: 1.6; }
                .btn { display: inline-block; padding: 12px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 6px; margin-top: 15px; }
                .footer { text-align: center; color: #888; font-size: 13px; margin-top: 20px; }
              </style>
            </head>
            <body>
              <div class="container">
                <div class="header">
                  <h2>New Booking Request</h2>
                </div>
                <div class="content">
                  <p>Hello <strong>' . htmlspecialchars($fundi["name"]) . '</strong>,</p>
                  <p>You have received a new booking request from <strong>' . htmlspecialchars($resident["name"]) . '</strong> for the service <strong>' . htmlspecialchars($serviceName) . '</strong>.</p>
                  <p>Please log in to your FundiFix dashboard to view the details and respond to this request.</p>
                  <p style="text-align: center;">
                    <a href="' . $viewBookingsLink . '" class="btn">Log In to View</a>
                  </p>
                  <p>Thank you for using FundiFix.</p>
                </div>
                <div class="footer">
                  <p>&copy; ' . date("Y") . ' FundiFix. All rights reserved.</p>
                </div>
              </div>
            </body>
            </html>';

            $mail->AltBody = "New Booking Request\n\nHello {$fundi['name']},\n\nYou have received a new booking request from {$resident['name']} for {$serviceName}.\n\nPlease log in to your dashboard to respond:\nhttp://localhost/FundiFix-Project/public/index.php?action=login\n\nThank you,\nFundiFix Team";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    // Notify resident when fundi updates booking status
    private function sendStatusUpdateNotification($bookingId, $status)
    {
        $booking = $this->bookingModel->findById((int)$bookingId);
        if (!$booking) return false;

        $fundi = $this->userModel->findById($booking['fundi_id']);
        $resident = $this->userModel->findById($booking['resident_id']);
        $service = $this->getServiceDetails($booking['service_id']);
        if (!$resident || !$fundi) return false;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME'] ?? 'FundiFix');
            $mail->addAddress($resident['email']);
            $mail->isHTML(true);

            $statusText = ucfirst($status);
            $statusColor = $status === 'accepted' ? '#28a745' : ($status === 'completed' ? '#17a2b8' : '#dc3545');
            $mail->Subject = "Booking {$statusText} - FundiFix";
            $serviceName = $service ? $service['name'] : 'Service';

            $mail->Body = '
            <html>
            <head>
              <style>
                body { font-family: Arial, sans-serif; background-color: #f6f9fc; color: #333; margin: 0; padding: 0; }
                .container { background-color: #ffffff; max-width: 600px; margin: 30px auto; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; background-color: ' . $statusColor . '; color: #ffffff; padding: 15px; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; line-height: 1.6; }
                .btn { display: inline-block; padding: 12px 20px; background-color: ' . $statusColor . '; color: #ffffff; text-decoration: none; border-radius: 6px; margin-top: 15px; }
                .footer { text-align: center; color: #888; font-size: 13px; margin-top: 20px; }
              </style>
            </head>
            <body>
              <div class="container">
                <div class="header">
                  <h2>Booking ' . ucfirst($status) . '</h2>
                </div>
                <div class="content">
                  <p>Hello <strong>' . htmlspecialchars($resident["name"]) . '</strong>,</p>
                  <p>Your booking for <strong>' . htmlspecialchars($serviceName) . '</strong> with <strong>' . htmlspecialchars($fundi["name"]) . '</strong> has been <strong style="color:' . $statusColor . ';">' . ucfirst($status) . '</strong>.</p>
                  <p>You can log in to your FundiFix dashboard to view full booking details.</p>
                  <p style="text-align: center;">
                    <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings" class="btn">View My Bookings</a>
                  </p>
                  <p>Thank you for using FundiFix.</p>
                </div>
                <div class="footer">
                  <p>&copy; ' . date("Y") . ' FundiFix. All rights reserved.</p>
                </div>
              </div>
            </body>
            </html>';

            $mail->AltBody = "Booking {$statusText}\n\nHello {$resident['name']},\n\nYour booking for {$serviceName} with {$fundi['name']} has been {$status}.\n\nView your bookings: http://localhost/FundiFix-Project/public/index.php?action=bookings\n\nThank you,\nFundiFix Team";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    private function getServiceDetails($service_id)
    {
        try {
            if (method_exists($this->serviceModel, 'getServiceById')) {
                return $this->serviceModel->getServiceById((int)$service_id);
            }

            $sql = "SELECT * FROM services WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $service_id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }
}