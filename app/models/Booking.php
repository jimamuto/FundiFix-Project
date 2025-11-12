<?php
namespace App\Models;

use PDO;

class Booking
{
    private PDO $conn;
    private string $table_name = "bookings";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // CREATE NEW BOOKING
    public function create(int $resident_id, int $fundi_id, int $service_id, string $status = 'pending'): bool
    {
        $sql = "INSERT INTO {$this->table_name} (resident_id, fundi_id, service_id, status, created_at) 
                VALUES (:resident_id, :fundi_id, :service_id, :status, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // GET BOOKINGS BY USER ID AND ROLE
    public function getByUserId(int $user_id, string $role): array
    {
        if ($role === 'fundi') {
            $sql = "SELECT b.*, u.name AS resident_name, s.name AS service_name
                    FROM {$this->table_name} b
                    JOIN users u ON b.resident_id = u.id
                    LEFT JOIN services s ON b.service_id = s.id
                    WHERE b.fundi_id = :user_id
                    ORDER BY b.created_at DESC";
        } else {
            $sql = "SELECT b.*, u.name AS fundi_name, s.name AS service_name
                    FROM {$this->table_name} b
                    JOIN users u ON b.fundi_id = u.id
                    LEFT JOIN services s ON b.service_id = s.id
                    WHERE b.resident_id = :user_id
                    ORDER BY b.created_at DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE BOOKING STATUS
    public function updateStatus(int $booking_id, string $status): bool
    {
        $sql = "UPDATE {$this->table_name} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // GET BOOKING BY ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT b.*, 
                       u.name AS resident_name, 
                       u2.name AS fundi_name, 
                       s.name AS service_name
                FROM {$this->table_name} b
                JOIN users u ON b.resident_id = u.id
                JOIN users u2 ON b.fundi_id = u2.id
                LEFT JOIN services s ON b.service_id = s.id
                WHERE b.id = :id 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    // -------------------
    // ANALYTICS - RESIDENT
    // -------------------

    public function getTotalBookingsByResident($resident_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM bookings WHERE resident_id = :resident_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getCompletedBookingsByResident($resident_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE resident_id = :resident_id AND status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getPendingBookingsByResident($resident_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE resident_id = :resident_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getCancelledBookingsByResident($resident_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE resident_id = :resident_id AND status = 'cancelled'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getMonthlyBookingTrendsByResident($resident_id)
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%b') AS month,
                    COUNT(*) AS count
                FROM bookings 
                WHERE resident_id = :resident_id 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b')
                ORDER BY MIN(created_at) ASC
                LIMIT 6";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $trends = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $trends[$row['month']] = (int)$row['count'];
        }
        
        // Ensure consistent 6-month trend data
        $last6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('M', strtotime("-$i months"));
            $last6Months[$month] = $trends[$month] ?? 0;
        }
        
        return $last6Months;
    }

    public function getServiceTypeBreakdownByResident($resident_id)
    {
        $sql = "SELECT 
                    s.name AS service_name,
                    COUNT(b.id) AS booking_count
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.resident_id = :resident_id
                GROUP BY s.name
                ORDER BY booking_count DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $breakdown = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $breakdown[$row['service_name']] = (int)$row['booking_count'];
        }
        
        return $breakdown;
    }

    public function getTotalSpentByResident($resident_id)
    {
        // Estimate based on completed bookings
        $sql = "SELECT COUNT(*) AS completed_count 
                FROM bookings 
                WHERE resident_id = :resident_id AND status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $estimatedTotal = ($row['completed_count'] ?? 0) * 1500; // Estimated cost per job
        
        return 'KES ' . number_format($estimatedTotal);
    }

    public function getAverageRatingGivenByResident($resident_id)
    {
        $sql = "SELECT AVG(rating) AS avg_rating 
                FROM reviews 
                WHERE resident_id = :resident_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row['avg_rating'] ?? 4.5, 1);
    }

    // ----------------
    // ANALYTICS - FUNDI
    // ----------------

    public function getTotalBookingsByFundi($fundi_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM bookings WHERE fundi_id = :fundi_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getCompletedBookingsByFundi($fundi_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE fundi_id = :fundi_id AND status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getPendingBookingsByFundi($fundi_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE fundi_id = :fundi_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getCancelledBookingsByFundi($fundi_id)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE fundi_id = :fundi_id AND status = 'cancelled'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getMonthlyPerformanceByFundi($fundi_id)
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%b') AS month,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending
                FROM bookings 
                WHERE fundi_id = :fundi_id 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b')
                ORDER BY MIN(created_at) ASC
                LIMIT 6";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $performance = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $performance[$row['month']] = [
                'completed' => (int)$row['completed'],
                'pending' => (int)$row['pending']
            ];
        }
        
        return $performance;
    }

    public function getEarningsByServiceByFundi($fundi_id)
    {
        $sql = "SELECT 
                    s.name AS service_name,
                    COUNT(b.id) AS job_count
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.fundi_id = :fundi_id AND b.status = 'completed'
                GROUP BY s.name
                ORDER BY job_count DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $earnings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $earnings[$row['service_name']] = $row['job_count'] * 1500;
        }
        
        return $earnings;
    }

    public function getTotalEarningsByFundi($fundi_id)
    {
        $sql = "SELECT COUNT(*) AS completed_count 
                FROM bookings 
                WHERE fundi_id = :fundi_id AND status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $estimatedTotal = ($row['completed_count'] ?? 0) * 1500;
        
        return $estimatedTotal;
    }

    public function getAverageRatingReceivedByFundi($fundi_id)
    {
        $sql = "SELECT AVG(rating) AS avg_rating 
                FROM reviews 
                WHERE fundi_id = :fundi_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($row['avg_rating'] ?? 4.7, 1);
    }

    // GET ALL BOOKINGS (Admin)
    public function getAllBookings(): array
    {
        $sql = "SELECT b.*, 
                       u1.name AS resident_name, 
                       u2.name AS fundi_name, 
                       s.name AS service_name
                FROM {$this->table_name} b
                JOIN users u1 ON b.resident_id = u1.id
                JOIN users u2 ON b.fundi_id = u2.id
                LEFT JOIN services s ON b.service_id = s.id
                ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET RECENT BOOKINGS
    public function getRecentBookings($limit = 5): array
    {
        $sql = "SELECT b.*, 
                       u1.name AS resident_name, 
                       u2.name AS fundi_name, 
                       s.name AS service_name
                FROM {$this->table_name} b
                JOIN users u1 ON b.resident_id = u1.id
                JOIN users u2 ON b.fundi_id = u2.id
                LEFT JOIN services s ON b.service_id = s.id
                ORDER BY b.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CHECK IF BOOKING EXISTS
    public function bookingExists($resident_id, $fundi_id, $service_id): bool
    {
        $sql = "SELECT COUNT(*) AS count 
                FROM {$this->table_name} 
                WHERE resident_id = :resident_id 
                AND fundi_id = :fundi_id 
                AND service_id = :service_id 
                AND status IN ('pending', 'accepted')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_INT);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['count'] ?? 0) > 0;
    }
}