<?php


//  loading all the classes the Controller will need.
require_once '../config/database.php'; 
require_once '../app/models/User.php';     
// the Controller that uses them.
require_once '../app/controllers/UserController.php';


//  CREATE THE CONTROLLER 

$userController = new UserController();

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
        $userController->home();
        break;
}

