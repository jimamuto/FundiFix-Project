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
        $query = "SELECT id, name, email, role, password, twofa_secret, twofa_enabled
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

    // Set 2FA secret and enable 2FA
    public function enable2FA(int $userId, string $secret): bool {
        $query = "UPDATE {$this->table_name} SET twofa_secret = ?, twofa_enabled = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$secret, $userId]);
    }

    // Disable 2FA
    public function disable2FA(int $userId): bool {
        $query = "UPDATE {$this->table_name} SET twofa_secret = NULL, twofa_enabled = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId]);
    }

    // Get 2FA secret for user
    public function get2FASecret(int $userId): ?string {
        $query = "SELECT twofa_secret FROM {$this->table_name} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? $row['twofa_secret'] : null;
    }

    // Check if 2FA is enabled for user
    public function is2FAEnabled(int $userId): bool {
        $query = "SELECT twofa_enabled FROM {$this->table_name} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row && $row['twofa_enabled'] == 1;
    }

    // Register a new user
    public function register(string $name, string $email, string $password, string $role, $twofa_secret = null, $twofa_enabled = 0): bool {
        // Check if user already exists
        if ($this->findByEmail($email)) {
            return false; // already exists
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table_name} 
                  (name, email, password, role, twofa_secret, twofa_enabled) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $hashed_password, $role, $twofa_secret, $twofa_enabled]);
    }

    public function loginUser($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Remove password before returning user data
            unset($user['password']);
            return $user;
        }
        return false;
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
