<?php

namespace App\Models;

use PDO;
use PDOException;

class User {
    private PDO $conn;
    public string $table_name = "users"; 

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // USER RETRIEVAL METHODS
  

    /**
     * Find a user by their ID.
     */
    public function findById(int $id): ?array {
        $query = "SELECT id, name, email, role, is_verified FROM {$this->table_name} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?array {
        $query = "SELECT * FROM {$this->table_name} WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    
    // USER AUTHENTICATION & REGISTRATION
   

    /**
     * Register a new user.
     */
    public function register(string $name, string $email, string $password, string $role): bool {
        // Prevent duplicate registration
        if ($this->findByEmail($email)) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_code = rand(100000, 999999); // 6-digit verification code

        $query = "INSERT INTO {$this->table_name} 
                  (name, email, password, role, verification_code, is_verified, created_at) 
                  VALUES (?, ?, ?, ?, ?, 0, NOW())";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $hashed_password, $role, $verification_code]);
    }

    /**
     * Verify a user's account using the verification code.
     */
    public function verifyAccount(string $email, string $code): bool {
        $query = "UPDATE {$this->table_name} 
                  SET is_verified = 1, verification_code = NULL 
                  WHERE email = ? AND verification_code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email, $code]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Authenticate a user (only if verified).
     */
    public function loginUser(string $email, string $password): array|false {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Check verification
        if (isset($user['is_verified']) && !$user['is_verified']) {
            return ['error' => 'Account not verified. Please check your email for the 6-digit code.'];
        }

        // Remove sensitive data
        unset($user['password'], $user['verification_code']);
        return $user;
    }

    public function getAllUsers(): array
{
    $sql = "SELECT id, name, email, role, is_verified, created_at FROM {$this->table_name} ORDER BY id DESC";
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function deleteUser(int $id): bool
{
    $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

    
    // PROFILE MANAGEMENT
   

    /**
     * Update user details.
     */
    public function update(int $id, string $name, string $email): bool {
        $query = "UPDATE {$this->table_name} SET name = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $id]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(int $id, string $new_password): bool {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE {$this->table_name} SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashed_password, $id]);
    }

    // PASSWORD RESET SYSTEM


    /**
     * Create a password reset token for the given email.
     */
    public function setResetToken(string $email): ?string {
        $user = $this->findByEmail($email);
        if (!$user) return null;

        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE {$this->table_name}
                  SET password_reset_token = :token, password_reset_expires_at = :expires
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':email', $email);

        return $stmt->execute() ? $token : null;
    }

    /**
     * Find user by password reset token.
     */
    public function findByResetToken(string $token): ?array {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE password_reset_token = :token
                  AND password_reset_expires_at > NOW()
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update password using reset token.
     */
    public function updatePasswordByToken(string $token, string $hashed_password): bool {
        $query = "UPDATE {$this->table_name}
                  SET password = :password,
                      password_reset_token = NULL,
                      password_reset_expires_at = NULL
                  WHERE password_reset_token = :token
                    AND password_reset_expires_at > NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }
}