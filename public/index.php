<?php
// Start a session for login handling
session_start();

// 1. Load Composer's Autoloader 
// This one file automatically loads all your classes (User, UserController, etc.)
// so you don't need to 'require' them manually.
require_once __DIR__ . '/../vendor/autoload.php';



// 2. Load Environment Variables from the .env file
// This loads your DB_HOST, DB_USER, etc., fixing all database warnings.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 3. Bring the necessary classes into the current scope
use App\Controllers\UserController;

// 4. CREATE THE CONTROLLER & ROUTE THE REQUEST
$userController = new UserController();

// Use a simple router to handle different pages ('actions')
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'register':
        $userController->register();
        break;
    case 'login':
        $userController->login();
        break;
    case 'dashboard':
        $userController->dashboard();
        break;
    case 'logout':
        $userController->logout();
        break;
    default:
        // By default, show the home page
        $userController->home();
        break;
}