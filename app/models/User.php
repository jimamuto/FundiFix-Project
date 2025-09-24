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

}

    ?>