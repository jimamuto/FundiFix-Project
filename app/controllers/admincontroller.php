<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Service;
use PDO;

class AdminController
{
    private $conn;
    private $userModel;
    private $serviceModel;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->userModel = new User($db);
        $this->serviceModel = new Service($db);
    }

    // -------------------- DASHBOARD --------------------
    public function showDashboard()
    {
        $users = $this->userModel->getAllUsers();
        $services = $this->serviceModel->getAllServices();

        $usersCount = count($users);
        $servicesCount = count($services);

        include __DIR__ . '/../Views/Admin/dashboard.php';
    }

    // -------------------- USERS MANAGEMENT --------------------
    public function showUsers()
    {
        $users = $this->userModel->getAllUsers();
        include __DIR__ . '/../Views/Admin/users_list.php';
    }

    public function editUser($id)
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header('Location: index.php?action=admin_users');
            exit;
        }

        include __DIR__ . '/../Views/Admin/edit_user.php';
    }

    public function updateUser($data)
    {
        $id = $data['id'];
        $name = $data['name'];
        $email = $data['email'];
        $role = $data['role'];
        $is_verified = isset($data['is_verified']) ? 1 : 0;

        $query = "UPDATE users 
                  SET name = :name, email = :email, role = :role, is_verified = :is_verified 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':is_verified', $is_verified, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update user.";
        }

        header('Location: index.php?action=admin_users');
        exit;
    }

    public function deleteUser($id)
    {
        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success'] = "User deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete user.";
        }

        header('Location: index.php?action=admin_users');
        exit;
    }

    // -------------------- SERVICES MANAGEMENT --------------------
    public function showServices()
    {
        $services = $this->serviceModel->getAllServices();
        include __DIR__ . '/../Views/Admin/services_list.php';
    }

   public function addService()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $category = $_POST['category'] ?? 'General'; // Add category field
        $price = $_POST['price'];
        $description = $_POST['description'];
        $status = $_POST['status'] ?? 'active';

        if ($this->serviceModel->create($name, $category, $price, $description, $status)) {
            $_SESSION['success'] = "Service added successfully.";
            header('Location: index.php?action=admin_services');
            exit;
        } else {
            $_SESSION['error'] = "Failed to add service.";
        }
    }

    include __DIR__ . '/../Views/Admin/add_service.php';
}

    public function editService($id)
    {
        $service = $this->serviceModel->findById($id);
        if (!$service) {
            $_SESSION['error'] = "Service not found.";
            header('Location: index.php?action=admin_services');
            exit;
        }

        include __DIR__ . '/../Views/Admin/edit_service.php';
    }

    public function updateService($data)
{
    $id = $data['id'];
    $name = $data['name'];
    $category = $data['category'] ?? 'General';
    $price = $data['price'];
    $description = $data['description'];
    $status = $data['status'];

    if ($this->serviceModel->update($id, $name, $category, $price, $description, $status)) {
        $_SESSION['success'] = "Service updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update service.";
    }

    header('Location: index.php?action=admin_services');
    exit;
}

    public function deleteService($id)
    {
        if ($this->serviceModel->deleteService($id)) {
            $_SESSION['success'] = "Service deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete service.";
        }

        header('Location: index.php?action=admin_services');
        exit;
    }
}