<?php

namespace App\Models;

use PDO;

class Service
{
    private PDO $conn;
    private string $table_name = "services"; 

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // CREATE A NEW SERVICE
    public function create(string $name, string $category, float $price, string $description, string $status = 'active'): bool
    {
        $sql = "INSERT INTO {$this->table_name} (name, category, price, description, status, created_at)
                VALUES (:name, :category, :price, :description, :status, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // GET ALL SERVICES
    public function getAllServices(): array
    {
        $sql = "SELECT id, name, price, description, status, created_at 
                FROM {$this->table_name} 
                ORDER BY id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET SERVICE BY ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, name, price, description, status, created_at
                FROM {$this->table_name}
                WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        return $service ?: null;
    }

    // UPDATE A SERVICE
    public function update(int $id, string $name, string $category, float $price, string $description, string $status): bool
    {
        $sql = "UPDATE {$this->table_name} 
                SET name = :name, category = :category, price = :price, description = :description, status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE A SERVICE
    public function deleteService(int $id): bool
    {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // GET ACTIVE SERVICES ONLY (for residents to book)
    public function getActiveServices(): array
    {
        $sql = "SELECT id, name, category, price, description, created_at 
                FROM {$this->table_name} 
                WHERE status = 'active'
                ORDER BY name ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET SERVICE BY ID
    public function getServiceById(int $service_id): ?array
    {
        $sql = "SELECT * FROM services WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $service_id, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        return $service ?: null;
    }

    // GET SERVICES BY CATEGORY
    public function getServicesByCategory(string $category): array
    {
        $sql = "SELECT id, name, price, description 
                FROM {$this->table_name} 
                WHERE category = :category AND status = 'active'
                ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET ALL UNIQUE CATEGORIES (for filtering)
    public function getCategories(): array
    {
        $sql = "SELECT DISTINCT category 
                FROM {$this->table_name} 
                WHERE status = 'active'
                ORDER BY category ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // GET SERVICES WITH FUNDI COUNT (advanced data processing)
    public function getServicesWithFundiCount(): array
    {
        $sql = "SELECT s.*, COUNT(fs.fundi_profile_id) as fundi_count
                FROM {$this->table_name} s
                LEFT JOIN fundi_services fs ON s.id = fs.service_id
                WHERE s.status = 'active'
                GROUP BY s.id
                ORDER BY fundi_count DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // SEARCH SERVICES (advanced filtering)
    public function searchServices(string $keyword): array
    {
        $sql = "SELECT id, name, category, price, description 
                FROM {$this->table_name} 
                WHERE status = 'active' 
                AND (name LIKE :keyword OR category LIKE :keyword OR description LIKE :keyword)
                ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $searchTerm = "%$keyword%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET SERVICE STATISTICS (for analytics)
    public function getServiceStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_services,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_services,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_services,
                    COUNT(DISTINCT category) as total_categories
                FROM {$this->table_name}";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // GET FUNDIS BY SERVICE ID
    public function getFundisByService(int $service_id): array
    {
        $sql = "SELECT fp.id as fundi_profile_id, u.id as user_id, u.name, fp.skills, fp.location, fp.phone_number
                FROM fundi_profiles fp
                JOIN users u ON fp.user_id = u.id
                JOIN fundi_services fs ON fp.id = fs.fundi_profile_id
                WHERE fs.service_id = :service_id
                AND u.role = 'fundi'";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}