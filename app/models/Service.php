<?php

namespace App\Models;

use PDO;
use PDOException;

class Service
{
    private PDO $conn;
    private string $table_name = "services"; 

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    
    // CREATE A NEW SERVICE 
    //operations
    
    public function create(string $name, string $category, float $price, string $description): bool
    {
        $sql = "INSERT INTO {$this->table_name} (name, category, price, description, created_at)
                VALUES (:name, :category, :price, :description, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);

        return $stmt->execute();
    }

    
    // GET ALL SERVICES
    
    public function getAllServices(): array
    {
        $sql = "SELECT id, name, category, price, description, created_at 
                FROM {$this->table_name} 
                ORDER BY id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // GET SERVICE BY ID
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, name, category, price, description, created_at
                FROM {$this->table_name}
                WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        return $service ?: null;
    }

    
    // UPDATE A SERVICE
    
    public function update(int $id, string $name, string $category, float $price, string $description): bool
    {
        $sql = "UPDATE {$this->table_name} 
                SET name = :name, category = :category, price = :price, description = :description
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
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
}