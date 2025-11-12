<?php
namespace App\Controllers;

use App\Models\Service;
use App\Config\Database;

class ServiceController
{
    private $serviceModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
        $this->serviceModel = new Service($this->db);
    }

    public function index()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $services = $this->serviceModel->getAllServices();
        require_once "../App/Views/Admin/services_list.php";
    }

    public function available()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $services = $this->serviceModel->getServicesWithFundiCount();
        $serviceModel = $this->serviceModel;
        require_once "../App/Views/Services/available.php";
    }

    public function byCategory($category)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $services = $this->serviceModel->getServicesByCategory($category);
        $categories = $this->serviceModel->getCategories();
        $serviceModel = $this->serviceModel;

        require_once "../App/Views/Services/by_category.php";
    }

    public function createForm()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        require_once "../App/Views/Admin/add_service.php";
    }

    public function create()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $category = trim($_POST['category']);
            $price = floatval($_POST['price']);
            $description = trim($_POST['description']);
            $status = $_POST['status'] ?? 'active';

            if (empty($name) || empty($category) || $price <= 0) {
                $_SESSION['error'] = 'Please fill all required fields correctly.';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_add_service');
                exit;
            }

            if ($this->serviceModel->create($name, $category, $price, $description, $status)) {
                $_SESSION['success'] = 'Service created successfully!';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_services');
            } else {
                $_SESSION['error'] = 'Failed to create service. Please try again.';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_add_service');
            }
            exit;
        }
    }

    public function editForm($id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $service = $this->serviceModel->findById($id);

        if (!$service) {
            $_SESSION['error'] = 'Service not found.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_services');
            exit;
        }

        require_once "../App/Views/Admin/edit_service.php";
    }

    public function update($id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $category = trim($_POST['category']);
            $price = floatval($_POST['price']);
            $description = trim($_POST['description']);
            $status = $_POST['status'];

            if (empty($name) || empty($category) || $price <= 0) {
                $_SESSION['error'] = 'Please fill all required fields correctly.';
                header("Location: http://localhost/FundiFix-Project/public/index.php?action=admin_edit_service&id=$id");
                exit;
            }

            if ($this->serviceModel->update($id, $name, $category, $price, $description, $status)) {
                $_SESSION['success'] = 'Service updated successfully!';
                header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_services');
            } else {
                $_SESSION['error'] = 'Failed to update service. Please try again.';
                header("Location: http://localhost/FundiFix-Project/public/index.php?action=admin_edit_service&id=$id");
            }
            exit;
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($this->serviceModel->deleteService($id)) {
            $_SESSION['success'] = 'Service deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete service. It may be in use.';
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=admin_services');
        exit;
    }

    public function getFundisByService($service_id)
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Please login first']);
            exit;
        }

        $fundis = $this->serviceModel->getFundisByService($service_id);
        header('Content-Type: application/json');
        echo json_encode($fundis);
    }

    public function stats()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $stats = $this->serviceModel->getServiceStats();
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    public function search()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Please login first']);
            exit;
        }

        $keyword = $_GET['q'] ?? '';

        if (empty($keyword)) {
            echo json_encode([]);
            exit;
        }

        $services = $this->serviceModel->searchServices($keyword);
        header('Content-Type: application/json');
        echo json_encode($services);
    }
}