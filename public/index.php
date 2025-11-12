<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify session is working
if (session_status() !== PHP_SESSION_ACTIVE) {
    die('Session failed to start');
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\UserController;
use App\Controllers\AdminController;
use App\Controllers\ServiceController;
use App\Controllers\BookingController;
use App\Controllers\FundiController;
use App\Controllers\InventoryController;
use App\Controllers\ExportController;
use Dotenv\Dotenv;

// Load Environment
$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
} else {
    die("Missing .env file in project root.");
}

// Initialize Database
$db = new Database();
$conn = $db->connect();

// Initialize Controllers
$userController = new UserController($conn);
$adminController = new AdminController($conn);
$serviceController = new ServiceController();
$bookingController = new BookingController();
$fundiController = new FundiController();
$inventoryController = new InventoryController($conn);

// Session & Role Check
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && ($_SESSION['user']['role'] ?? '') === 'admin';
$isFundi = $isLoggedIn && ($_SESSION['user']['role'] ?? '') === 'fundi';

// Normalize action to lowercase and underscores
$action = strtolower(str_replace('-', '_', $_GET['action'] ?? ''));

// Default routes
if (empty($action)) {
    $action = $isLoggedIn ? ($isAdmin ? 'admin_dashboard' : 'dashboard') : 'home';
}

// Prevent Unauthorized Admin Access
if (strpos($action, 'admin_') === 0 && !$isAdmin) {
    if (!in_array($action, ['home', 'login'])) {
        header('Location: index.php?action=home');
        exit;
    }
}

// ROUTER
switch ($action) {
    // ---------- PUBLIC ROUTES ----------
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

    case 'forgotpassword':
        $userController->forgotPassword();
        break;

    case 'resetpassword':
        $userController->resetPassword();
        break;

    // ---------- AUTHENTICATED USER ROUTES ----------
    case 'dashboard':
        $userController->dashboard();
        break;

    case 'profile':
        $userController->profile();
        break;

    case 'updateprofile':
        $userController->updateProfile();
        break;

    // ---------- SERVICE ROUTES ----------
    case 'services_available':
        $serviceController->available();
        break;

   // In your index.php router, make sure this route exists:
case 'services_category':
    if (isset($_GET['cat'])) {
        $serviceController->byCategory($_GET['cat']);
    } else {
        header('Location: index.php?action=services_available');
        exit;
    }
    break;

    // ---------- BOOKING ROUTES ----------
    case 'bookings':
        $bookingController->index();
        break;

    case 'bookings_create':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $bookingController->createForm();
        } else {
            $bookingController->create();
        }
        break;

    case 'bookings_update_status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingController->updateStatus();
        } else {
            header('Location: index.php?action=bookings');
            exit;
        }
        break;

    case 'bookings_cancel':
        if (isset($_GET['id'])) {
            $bookingController->cancel($_GET['id']);
        } else {
            header('Location: index.php?action=bookings');
            exit;
        }
        break;

    // ---------- FUNDI ROUTES ----------
    case 'fundi_profile':
        if ($isFundi) {
            $fundiController->profile();
        } else {
            header('Location: index.php?action=dashboard');
            exit;
        }
        break;

    case 'update_fundi_profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
            $fundiController->updateProfile($_POST);
        } else {
            header('Location: index.php?action=dashboard');
            exit;
        }
        break;

    case 'add_fundi_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
            $fundiController->addService();
        } else {
            header('Location: index.php?action=dashboard');
            exit;
        }
        break;

    case 'remove_fundi_service':
        if (isset($_GET['service_id']) && $isFundi) {
            $fundiController->removeService($_GET['service_id']);
        } else {
            header('Location: index.php?action=fundi_profile');
            exit;
        }
        break;

    // ---------- ADMIN ROUTES ----------
    case 'admin_dashboard':
        $adminController->showDashboard();
        break;

    case 'admin_users':
        $adminController->showUsers();
        break;

    case 'admin_edit_user':
        if (isset($_GET['id'])) {
            $adminController->editUser($_GET['id']);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

    case 'admin_update_user':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->updateUser($_POST);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

    case 'admin_delete_user':
        if (isset($_GET['id'])) {
            $adminController->deleteUser($_GET['id']);
        } else {
            header('Location: index.php?action=admin_users');
            exit;
        }
        break;

    case 'admin_services':
        $serviceController->index();
        break;

    case 'admin_add_service':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $serviceController->createForm();
        } else {
            $serviceController->create();
        }
        break;

    case 'admin_edit_service':
        if (isset($_GET['id'])) {
            $serviceController->editForm($_GET['id']);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;

    case 'admin_update_service':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $serviceController->update($_POST['id']);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;

    case 'admin_delete_service':
        if (isset($_GET['id'])) {
            $serviceController->delete($_GET['id']);
        } else {
            header('Location: index.php?action=admin_services');
            exit;
        }
        break;

    case 'admin_fundis':
        if ($isAdmin) {
            $fundiController->getAllFundis();
        } else {
            header('Location: index.php?action=dashboard');
            exit;
        }
        break;

    // ---------- API ROUTES ----------
    case 'api_services_stats':
        $serviceController->stats();
        break;

    case 'api_services_search':
        $serviceController->search();
        break;

    case 'api_fundis_by_service':
        if (isset($_GET['service_id'])) {
            $serviceController->getFundisByService($_GET['service_id']);
        }
        break;
case 'fundi_stats':
    if ($_SESSION['user']['role'] === 'fundi') {
        $fundiController->statsPage();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

    

case 'resident_analytics':
    if ($isLoggedIn && $_SESSION['user']['role'] === 'resident') {
        // You'll need to create a resident_analytics method in your controller
        $userController->residentAnalytics();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;



    // ---------- UTILITY ROUTES ----------
    case 'reset_session':
        session_destroy();
        session_start();
        header('Location: index.php?action=home');
        exit;
        break;

// ---------- INVENTORY ROUTES ----------
case 'inventory':
    if ($isFundi) {
        $inventoryController->index();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

case 'inventory_add':
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
        $inventoryController->addItem();
    } else {
        header('Location: index.php?action=inventory');
        exit;
    }
    break;

case 'inventory_use':
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
        $inventoryController->useItem();
    } else {
        header('Location: index.php?action=inventory');
        exit;
    }
    break;

case 'inventory_delete':
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
        $inventoryController->deleteItem();
    } else {
        header('Location: index.php?action=inventory');
        exit;
    }
    break;

case 'inventory_restock':
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFundi) {
        $inventoryController->restockItem();
    } else {
        header('Location: index.php?action=inventory');
        exit;
    }
    break;    

    // ---------- EXPORT ROUTES ----------
case 'export_fundi_pdf':
    if ($isFundi) {
        $exportController = new ExportController();
        $exportController->exportFundiPDF();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

case 'export_fundi_excel':
    if ($isFundi) {
        $exportController = new ExportController();
        $exportController->exportFundiExcel();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

case 'export_resident_pdf':
    if ($isLoggedIn && $_SESSION['user']['role'] === 'resident') {
        $exportController = new ExportController();
        $exportController->exportResidentPDF();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

case 'export_resident_excel':
    if ($isLoggedIn && $_SESSION['user']['role'] === 'resident') {
        $exportController = new ExportController();
        $exportController->exportResidentExcel();
    } else {
        header('Location: index.php?action=dashboard');
        exit;
    }
    break;

case 'verifyreset2fa':
    $userController->verifyreset2fa();
    break;

    // ---------- DEFAULT ROUTE ----------
    default:
        header('Location: index.php?action=home');
        exit;
}