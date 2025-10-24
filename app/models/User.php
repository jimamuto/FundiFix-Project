<?php
namespace App\Models;

use PDO;

class User {
    private PDO $conn;
    public string $table_name = "users"; 

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    

// ---------------- 2FA MANAGEMENT ----------------
public function store2FACode($email, $code)
{
    $sql = "UPDATE {$this->table_name} SET twofa_code = :code, twofa_created_at = NOW() WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
}

// ---------------- 2FA VERIFICATION ----------------
public function verify2FACode($email, $code)
{
    $sql = "SELECT id, name, email, role, is_verified, twofa_code, twofa_created_at 
            FROM {$this->table_name} 
            WHERE email = :email AND twofa_code = :code 
            AND twofa_created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function clear2FACode($email)
{
    $sql = "UPDATE {$this->table_name} SET twofa_code = NULL, twofa_created_at = NULL WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
}

    // ---------------- USER RETRIEVAL ----------------
    public function findById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, password, is_verified, verification_code FROM {$this->table_name} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ---------------- GET ALL USERS (FOR ADMIN) ----------------
    public function getAllUsers(): array {
        $stmt = $this->conn->query("SELECT id, name, email, role, is_verified, created_at FROM {$this->table_name} ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------- DELETE USER ----------------
    public function deleteUser(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ---------------- REGISTER ----------------
    public function register(string $name, string $email, string $password, string $role, int $verification_code): bool {
        if ($this->findByEmail($email)) return false;

        $stmt = $this->conn->prepare("INSERT INTO {$this->table_name} 
            (name, email, password, role, verification_code, is_verified, created_at) 
            VALUES (?, ?, ?, ?, ?, 0, NOW())");

        return $stmt->execute([$name, $email, $password, $role, $verification_code]);
    }

// ---------------- VERIFY ACCOUNT ----------------
public function verifyAccount(string $email, int $code): bool {
    $stmt = $this->conn->prepare("UPDATE {$this->table_name} 
        SET is_verified = 1, verification_code = NULL 
        WHERE email = ? AND verification_code = ?");
    $stmt->execute([$email, $code]);
    return $stmt->rowCount() > 0;
}

    // ---------------- LOGIN ----------------
    public function loginUser(string $email, string $password): array|false {
        $user = $this->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) return false;

        if (!$user['is_verified']) {
            return ['error' => 'Account not verified. Please check your email.'];
        }

        unset($user['password'], $user['verification_code']);
        return $user;
    }


    public function updateProfile($user_id, $name)
{
    $sql = "UPDATE users SET name = :name WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $user_id);
    
    return $stmt->execute();
}

    // ---------------- PASSWORD MANAGEMENT ----------------
    public function updatePassword(int $id, string $new_password): bool {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET password = ? WHERE id = ?");
        return $stmt->execute([$hashed_password, $id]);
    }

    // ---------------- PASSWORD RESET ----------------
    public function setResetToken(string $email): ?string {
        $user = $this->findByEmail($email);
        if (!$user) return null;

        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare("UPDATE {$this->table_name}
                  SET password_reset_token = ?, password_reset_expires_at = ?
                  WHERE email = ?");

        return $stmt->execute([$token, $expires, $email]) ? $token : null;
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name}
                  WHERE password_reset_token = ? AND password_reset_expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePasswordByToken(string $token, string $hashed_password): bool {
        $stmt = $this->conn->prepare("UPDATE {$this->table_name}
                  SET password = ?, password_reset_token = NULL, password_reset_expires_at = NULL
                  WHERE password_reset_token = ? AND password_reset_expires_at > NOW()");
        return $stmt->execute([$hashed_password, $token]);
    }
}