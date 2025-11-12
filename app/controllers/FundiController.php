<?php
namespace App\Controllers;

use App\Models\Fundi;
use App\Models\Service;
use App\Models\Booking;
use App\Config\Database;
use PDO;

class FundiController
{
    private $fundiModel;
    private $serviceModel;
    private $bookingModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
        $this->fundiModel = new Fundi($this->db);
        $this->serviceModel = new Service($this->db);
        $this->bookingModel = new Booking($this->db);
    }

    // DISPLAY FUNDI PROFILE
    public function profile()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            $_SESSION['error'] = 'Access denied. Fundi only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $fundiProfile = $this->fundiModel->getByUserId($user_id);

        // Create default profile if none exists
        if (!$fundiProfile) {
            $profileData = [
                'skills' => '',
                'location' => '',
                'phone_number' => ''
            ];

            if ($this->fundiModel->create($user_id, $profileData)) {
                $fundiProfile = $this->fundiModel->getByUserId($user_id);
                $_SESSION['info'] = 'Please complete your fundi profile to get started.';
            } else {
                $_SESSION['error'] = 'Failed to create fundi profile. Please try again.';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=dashboard');
                exit;
            }
        }

        $fundiServices = $fundiProfile ? $this->fundiModel->getServices($fundiProfile['id']) : [];
        $fundiStats = $fundiProfile ? $this->fundiModel->getStats($fundiProfile['id']) : [];
        $allServices = $this->serviceModel->getAllServices();

        require_once "../App/Views/Users/fundi_profile.php";
    }

    // UPDATE FUNDI PROFILE
    public function updateProfile($data)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            $_SESSION['error'] = 'Access denied. Fundi only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];

        if (empty($data['skills']) || empty($data['location']) || empty($data['phone_number'])) {
            $_SESSION['error'] = 'Please fill all required fields.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
            exit;
        }

        $profileData = [
            'skills' => trim($data['skills']),
            'location' => trim($data['location']),
            'phone_number' => trim($data['phone_number'])
        ];

        if ($this->fundiModel->save($user_id, $profileData)) {
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile. Please try again.';
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
        exit;
    }

    // ADD A SERVICE TO FUNDI PROFILE
    public function addService()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            $_SESSION['error'] = 'Access denied. Fundi only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user']['id'];
            $service_id = $_POST['service_id'];
            $fundiProfile = $this->fundiModel->getByUserId($user_id);

            if (!$fundiProfile) {
                $_SESSION['error'] = 'Please complete your profile first.';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
                exit;
            }

            if ($this->fundiModel->addService($fundiProfile['id'], $service_id)) {
                $_SESSION['success'] = 'Service added successfully!';
            } else {
                $_SESSION['error'] = 'Failed to add service. You may already have this service.';
            }

            header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
            exit;
        }
    }

    // REMOVE A SERVICE FROM FUNDI PROFILE
    public function removeService($service_id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            $_SESSION['error'] = 'Access denied. Fundi only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $fundiProfile = $this->fundiModel->getByUserId($user_id);

        if ($fundiProfile && $this->fundiModel->removeService($fundiProfile['id'], $service_id)) {
            $_SESSION['success'] = 'Service removed successfully!';
        } else {
            $_SESSION['error'] = 'Failed to remove service.';
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
        exit;
    }

   // DISPLAY FUNDI STATISTICS PAGE
public function statsPage()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
        $_SESSION['error'] = 'Access denied. Fundi only.';
        header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $fundiProfile = $this->fundiModel->getByUserId($user_id);

    if (!$fundiProfile) {
        $_SESSION['error'] = 'Please complete your fundi profile first.';
        header('Location: http://localhost/FundiFix-Project/public/index.php?action=fundi_profile');
        exit;
    }

    $fundiStats = $this->fundiModel->getStats($fundiProfile['id']);
    
    // Check if user has data or should see demo data
    $hasData = $fundiStats['total_bookings'] > 0;
    
    if (!$hasData && in_array($user_id, [2, 3, 4, 5, 7])) {
        // Demo users - show sample data
        $fundiStats = $this->getDemoFundiStats($user_id);
    } elseif (!$hasData) {
        // New user - show empty state
        $fundiStats = [
            'total_bookings' => 0,
            'completed_bookings' => 0,
            'pending_bookings' => 0,
            'cancelled_bookings' => 0,
            'total_earnings' => 0,
            'average_rating' => 0,
            'has_data' => false
        ];
    } else {
        // User with real data
        $fundiStats['has_data'] = true;
    }

    $_SESSION['fundi_stats'] = $fundiStats;
    require_once "../App/Views/Users/fundi_stats.php";
}

// ADD THIS METHOD FOR DEMO DATA
private function getDemoFundiStats($user_id)
{
    // Different demo data for different fundis
    $demoData = [
        2 => [ // John Fundi - Plumbing & Electrical
            'total_bookings' => 28,
            'completed_bookings' => 22,
            'pending_bookings' => 4,
            'cancelled_bookings' => 2,
            'total_earnings' => 45600,
            'average_rating' => 4.7,
            'has_data' => true
        ],
        3 => [ // Mike Electrician
            'total_bookings' => 15,
            'completed_bookings' => 12,
            'pending_bookings' => 2,
            'cancelled_bookings' => 1,
            'total_earnings' => 28500,
            'average_rating' => 4.5,
            'has_data' => true
        ],
        4 => [ // Sarah Plumber
            'total_bookings' => 32,
            'completed_bookings' => 28,
            'pending_bookings' => 3,
            'cancelled_bookings' => 1,
            'total_earnings' => 51200,
            'average_rating' => 4.8,
            'has_data' => true
        ],
        5 => [ // David Carpenter
            'total_bookings' => 18,
            'completed_bookings' => 15,
            'pending_bookings' => 2,
            'cancelled_bookings' => 1,
            'total_earnings' => 32400,
            'average_rating' => 4.6,
            'has_data' => true
        ],
        7 => [ // Larissa Joseph - Interior Design
            'total_bookings' => 24,
            'completed_bookings' => 20,
            'pending_bookings' => 3,
            'cancelled_bookings' => 1,
            'total_earnings' => 60000,
            'average_rating' => 4.9,
            'has_data' => true
        ]
    ];

    return $demoData[$user_id] ?? [
        'total_bookings' => 0,
        'completed_bookings' => 0,
        'pending_bookings' => 0,
        'cancelled_bookings' => 0,
        'total_earnings' => 0,
        'average_rating' => 0,
        'has_data' => false
    ];
}

    // HELPER: GET MONTHLY PERFORMANCE DATA
    private function getMonthlyPerformance($fundi_id)
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed
                FROM bookings 
                WHERE fundi_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC
                LIMIT 6";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fundi_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HELPER: GET EARNINGS BY SERVICE
    private function getEarningsByService($fundi_id)
    {
        $sql = "SELECT 
                    s.name AS service_name,
                    COUNT(b.id) AS booking_count,
                    COALESCE(SUM(CASE WHEN b.status = 'completed' THEN s.price ELSE 0 END), 0) AS total_earnings
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.fundi_id = ?
                GROUP BY s.name
                ORDER BY total_earnings DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fundi_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // RETURN FUNDI STATS (API Endpoint)
    public function stats()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $fundiProfile = $this->fundiModel->getByUserId($user_id);

        if ($fundiProfile) {
            $stats = $this->fundiModel->getStats($fundiProfile['id']);
            header('Content-Type: application/json');
            echo json_encode($stats);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Fundi profile not found']);
        }
    }

    // DISPLAY ALL FUNDIS (Admin View)
    public function getAllFundis()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $fundis = $this->fundiModel->getAll();
        require_once "../App/Views/Admin/fundis_list.php";
    }
}