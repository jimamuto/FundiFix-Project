<?php

class User {

    // Database connection details
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }


    
    public function findByEmail(string $email) {
        $query = "SELECT * FROM {$this->table_name} WHERE email = ? LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return ($result->num_rows === 1) ? $result->fetch_assoc() : false;
    }

       public function createUser(string $name, string $email, string $password, string $role): bool {
    
        $query = "INSERT INTO {$this->table_name} (name, email, password, role)
                  VALUES (?, ?, ?, ?)";
        $stmt  = $this->conn->prepare($query);
        
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        return $stmt->execute();
    
    }


    public function createUserHashed(string $name, string $email, string $password, string $role): bool {
        // --- Commit 5 code starts here ---
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO {$this->table_name} (name, email, password, role)
                  VALUES (?, ?, ?, ?)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        return $stmt->execute();
        
    }


    
     
    
    public function register(string $name, string $email, string $password, string $role): bool {
        if ($this->emailExists($email)) {
            return false;
        }
        
        return $this->createUserHashed($name, $email, $password, $role);
    }

   
    public function emailExists(string $email): bool {
        return $this->findByEmail($email) ? true : false;
    }



}

    ?>