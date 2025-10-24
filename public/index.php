<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;
use App\Controllers\AdminController;
use Dotenv\Dotenv;

// ----------------------
// Load Environment
// ----------------------
$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
} else {
    die("⚠️ Missing .env file in project root.");
}

// ----------------------
// Initialize Database
// ----------------------
$db = new Database();
$conn = $db->connect();

$userController = new UserController($conn);
$adminController = new AdminController($conn);

// ----------------------
// Session & Role Check
// ----------------------
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && ($_SESSION['user']['role'] ?? '') === 'admin';

// Normalize action to lowercase and underscores
$action = strtolower(str_replace('-', '_', $_GET['action'] ?? ''));

// Default routes
if (empty($action)) {
    $action = $isAdmin ? 'admin_dashboard' : 'home';
}

// ----------------------
// Prevent Unauthorized Admin Access
// ----------------------
if (strpos($action, 'admin_') === 0 && !$isAdmin) {
    // Prevent redirect loop if already on home or login
    if (!in_array($action, ['home', 'login'])) {
        header('Location: index.php?action=home');
        exit;
    }
}

// ----------------------
// ROUTER
// ----------------------
switch ($action) {

    // ---------- PUBLIC ----------
    case 'home':
        $userController->home();
        break;

    case 'register':
        $userController->register();
        break;

    case 'verifyaccount':
        $userController->verifyAccount();
        break;

    case 'login':
        $userController->login();
        break;

    case 'verify2fa':
        $userController->verify2fa();
        break;

    case 'logout':
        $userController->logout();
        break;

    case 'dashboard':
        $userController->dashboard();
        break;

    // ---------- ADMIN ----------
    case 'admin_dashboard':
        $adminController->showDashboard();
        break;

    case 'admin_users':
        $adminController->showUsers();
        break;

    case 'edit_user':
        if (isset($_GET['id'])) {
            $adminController->editUser($_GET['id']);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

    case 'update_user':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->updateUser($_POST);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

    case 'delete_user':
        if (isset($_GET['id'])) {
            $adminController->deleteUser($_GET['id']);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

            // ---------- SERVICES ----------
    case 'admin_services':
        $adminController->showServices();
        break;

    case 'add_service':
        $adminController->addService();
        break;

    case 'edit_service':
        if (isset($_GET['id'])) {
            $adminController->editService($_GET['id']);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;

    case 'update_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->updateService($_POST);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;

    case 'delete_service':
        if (isset($_GET['id'])) {
            $adminController->deleteService($_GET['id']);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;


    // ---------- FALLBACK ----------
    default:
        header('Location: index.php?action=home');
        exit;
}
