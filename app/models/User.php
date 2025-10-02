<?php

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db; // $db is a PDO instance
    }

    // Find user by ID
    public function findById($id) {
        $query = "SELECT id, name, email, role, password 
                  FROM {$this->table_name} 
                  WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch(); 
    }

    // Find user by Email
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table_name} 
                  WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    // Register a new user
    public function register($name, $email, $password, $role) {
        // Check if user already exists
        if ($this->findByEmail($email)) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table_name} 
                  (name, email, password, role) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $hashed_password, $role]);
    }

    // Update user details
    public function update($id, $name, $email) {
        $query = "UPDATE {$this->table_name} 
                  SET name = ?, email = ? 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $id]);
    }

    // Update password
    public function updatePassword($id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $query = "UPDATE {$this->table_name} 
                  SET password = ? 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashed_password, $id]);
    }
}
