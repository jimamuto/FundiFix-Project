<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class AdminController
{
    private PDO $conn;

    public function __construct()
    {
        // Initialize database connection
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Check admin authentication
        $this->checkAdmin();
    }

    private function checkAdmin(): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
    }

    public function dashboard(): void
    {
        // Get statistics for dashboard
        $usersCount = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $servicesCount = $this->conn->query("SELECT COUNT(*) FROM services")->fetchColumn();
        $activeServices = $this->conn->query("SELECT COUNT(*) FROM services WHERE status = 'active'")->fetchColumn();

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    // Show all users with CRUD operations
    public function showUsers(): void
    {
        $stmt = $this->conn->query("SELECT id, name, email, role, is_verified, created_at FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/admin/users_list.php';
    }

    // Show all services with CRUD operations
    public function showServices(): void
    {
        $stmt = $this->conn->query("SELECT id, name, description, price, status, created_at FROM services ORDER BY id ASC");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/admin/services_list.php';
    }

    // Edit User Form
    public function editUserForm(): void
    {
        if (!isset($_GET['id'])) {
            header("Location: ?action=admin_users");
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $this->conn->prepare("SELECT id, name, email, role, is_verified FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: ?action=admin_users");
            exit;
        }

        require_once __DIR__ . '/../Views/admin/edit_user.php';
    }

    // Update User
    public function updateUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?action=admin_users");
            exit;
        }

        $id = intval($_POST['id']);
        $name = htmlspecialchars($_POST['name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $role = $_POST['role'];
        $is_verified = isset($_POST['is_verified']) ? 1 : 0;

        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_verified = ? WHERE id = ?");
        
        if ($stmt->execute([$name, $email, $role, $is_verified, $id])) {
            $_SESSION['success'] = "User updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update user!";
        }

        header("Location: ?action=admin_users");
        exit;
    }

    // Add Service Form
    public function addServiceForm(): void
    {
        require_once __DIR__ . '/../Views/admin/add_service.php';
    }

    // Create Service
    public function createService(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?action=admin_services");
            exit;
        }

        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $price = floatval($_POST['price']);
        $status = $_POST['status'];

        $stmt = $this->conn->prepare("INSERT INTO services (name, description, price, status) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $description, $price, $status])) {
            $_SESSION['success'] = "Service created successfully!";
        } else {
            $_SESSION['error'] = "Failed to create service!";
        }

        header("Location: ?action=admin_services");
        exit;
    }

    // Edit Service Form
    public function editServiceForm(): void
    {
        if (!isset($_GET['id'])) {
            header("Location: ?action=admin_services");
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $this->conn->prepare("SELECT id, name, description, price, status FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            header("Location: ?action=admin_services");
            exit;
        }

        require_once __DIR__ . '/../Views/admin/edit_service.php';
    }

    // Update Service
    public function updateService(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?action=admin_services");
            exit;
        }

        $id = intval($_POST['id']);
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $price = floatval($_POST['price']);
        $status = $_POST['status'];

        $stmt = $this->conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, status = ? WHERE id = ?");
        
        if ($stmt->execute([$name, $description, $price, $status, $id])) {
            $_SESSION['success'] = "Service updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update service!";
        }

        header("Location: ?action=admin_services");
        exit;
    }

    // Delete a user
    public function deleteUser(): void
    {
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "No user ID provided!";
            header("Location: ?action=admin_users");
            exit;
        }

        $id = intval($_GET['id']);
        
        // Prevent admin from deleting themselves
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            $_SESSION['error'] = "You cannot delete your own account!";
            header("Location: ?action=admin_users");
            exit;
        }

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete user!";
        }

        header("Location: ?action=admin_users");
        exit;
    }

    // Delete a service
    public function deleteService(): void
    {
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "No service ID provided!";
            header("Location: ?action=admin_services");
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $this->conn->prepare("DELETE FROM services WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Service deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete service!";
        }

        header("Location: ?action=admin_services");
        exit;
    }
}