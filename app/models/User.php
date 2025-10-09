<?php

namespace App\Models;

use PDO;

class User {
    private PDO $conn;
    private string $table_name = "users";

    public function __construct(PDO $db) {
        $this->conn = $db; // $db must be a PDO instance from Database::getConnection()
    }

    // Find user by ID
    public function findById(int $id): ?array {
        $query = "SELECT id, name, email, role, password 
                  FROM {$this->table_name} 
                  WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null; // return null if no result
    }

    // Find user by Email
    public function findByEmail(string $email): ?array {
        $query = "SELECT * FROM {$this->table_name} 
                  WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    // Register a new user
    public function register(string $name, string $email, string $password, string $role): bool {
        // Check if user already exists
        if ($this->findByEmail($email)) {
            return false; // already exists
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table_name} 
                  (name, email, password, role) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $hashed_password, $role]);
    }

    // Update user details
    public function update(int $id, string $name, string $email): bool {
        $query = "UPDATE {$this->table_name} 
                  SET name = ?, email = ? 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $id]);
    }

    // Update password
    public function updatePassword(int $id, string $new_password): bool {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $query = "UPDATE {$this->table_name} 
                  SET password = ? 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashed_password, $id]);
    }
}
