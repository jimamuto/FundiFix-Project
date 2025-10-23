<?php
require_once _DIR_ . '/vendor/autoload.php';

use App\Config\Database;
use App\Models\Service;

// Initialize DB connection
$db = new Database();
$conn = $db->connect();

// Initialize the Service model
$service = new Service($conn);

// 1 Test creating a new service
echo "Adding new service...\n";
if ($service->create("Laptop Repair", "Electronics", 2500.00, "Fixing hardware and software issues")) {
    echo " Service added successfully!\n";
} else {
    echo " Failed to add service.\n";
}

// 2 Test fetching all services
echo "\nFetching all services:\n";
$all = $service->getAllServices();
print_r($all);

// 3 Test finding a service by ID (adjust ID to one from your DB)
echo "\nFetching single service:\n";
$one = $service->findById(1);
print_r($one);

// 4 Test updating
echo "\nUpdating service with ID 1...\n";
if ($service->update(1, "Laptop Repair (Updated)", "Electronics", 2600.00, "Updated description")) {
    echo " Service updated!\n";
} else {
    echo " Failed to update service.\n";
}

// 5 Test deleting
// echo "\nDeleting service with ID 2...\n";
// if ($service->deleteService(2)) {
//     echo " Deleted service 2.\n";
// }