<?php
namespace App\Controllers;

use App\Models\Inventory;

class InventoryController
{
    private $inventoryModel;

    public function __construct($db)
    {
        $this->inventoryModel = new Inventory($db);
    }

    // ==================== SHOW INVENTORY DASHBOARD ====================
    public function index()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        $fundi_id = $_SESSION['user']['id'];
        $inventory = $this->inventoryModel->getByFundi($fundi_id);
        $summary = $this->inventoryModel->getInventorySummary($fundi_id);

        // Load the proper view file
        require_once __DIR__ . '/../Views/Inventory/index.php';
    }

    // ==================== ADD NEW INVENTORY ITEM ====================
    public function addItem()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fundi_id = $_SESSION['user']['id'];
            $item_data = [
                'item_name' => trim($_POST['item_name']),
                'category' => trim($_POST['category']),
                'quantity' => (int)$_POST['quantity'],
                'unit_price' => isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.00,
                'description' => trim($_POST['description'] ?? '')
            ];

            // Validate required fields
            if (empty($item_data['item_name']) || empty($item_data['category'])) {
                $_SESSION['error'] = "Item name and category are required.";
            } elseif ($item_data['quantity'] < 0) {
                $_SESSION['error'] = "Quantity cannot be negative.";
            } else {
                if ($this->inventoryModel->addItem($fundi_id, $item_data)) {
                    $_SESSION['success'] = "Item added to inventory successfully!";
                } else {
                    $_SESSION['error'] = "Failed to add item to inventory.";
                }
            }
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=inventory');
        exit;
    }

    // ==================== USE INVENTORY ITEM ====================
    public function useItem()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_id = (int)$_POST['item_id'];
            $amount_used = (int)$_POST['amount_used'];

            // Validate amount
            if ($amount_used <= 0) {
                $_SESSION['error'] = "Amount used must be greater than zero.";
            } else {
                // Check if item exists and has sufficient quantity
                $item = $this->inventoryModel->getById($item_id);
                if (!$item) {
                    $_SESSION['error'] = "Item not found.";
                } elseif ($amount_used > $item['quantity']) {
                    $_SESSION['error'] = "Insufficient quantity. Available: " . $item['quantity'];
                } else {
                    if ($this->inventoryModel->useItem($item_id, $amount_used)) {
                        $_SESSION['success'] = "Used {$amount_used} items successfully!";
                    } else {
                        $_SESSION['error'] = "Failed to update item quantity.";
                    }
                }
            }
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=inventory');
        exit;
    }

    // ==================== RESTOCK INVENTORY ITEM ====================
    public function restockItem()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_id = (int)$_POST['item_id'];
            $amount_added = (int)$_POST['amount_added'];

            // Validate amount
            if ($amount_added <= 0) {
                $_SESSION['error'] = "Amount added must be greater than zero.";
            } else {
                // Check if item exists
                $item = $this->inventoryModel->getById($item_id);
                if (!$item) {
                    $_SESSION['error'] = "Item not found.";
                } else {
                    if ($this->inventoryModel->restockItem($item_id, $amount_added)) {
                        $_SESSION['success'] = "Restocked {$amount_added} items successfully!";
                    } else {
                        $_SESSION['error'] = "Failed to restock item.";
                    }
                }
            }
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=inventory');
        exit;
    }

    // ==================== DELETE INVENTORY ITEM ====================
    public function deleteItem()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_id = (int)$_POST['item_id'];

            // Check if item exists
            $item = $this->inventoryModel->getById($item_id);
            if (!$item) {
                $_SESSION['error'] = "Item not found.";
            } else {
                if ($this->inventoryModel->deleteItem($item_id)) {
                    $_SESSION['success'] = "Item deleted from inventory successfully!";
                } else {
                    $_SESSION['error'] = "Failed to delete item from inventory.";
                }
            }
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=inventory');
        exit;
    }

    // ==================== UPDATE INVENTORY ITEM ====================
    public function updateItem()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_id = (int)$_POST['item_id'];
            $item_data = [
                'item_name' => trim($_POST['item_name']),
                'category' => trim($_POST['category']),
                'unit_price' => isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.00,
                'description' => trim($_POST['description'] ?? '')
            ];

            // Validate required fields
            if (empty($item_data['item_name']) || empty($item_data['category'])) {
                $_SESSION['error'] = "Item name and category are required.";
            } else {
                if ($this->inventoryModel->updateItem($item_id, $item_data)) {
                    $_SESSION['success'] = "Item updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update item.";
                }
            }
        }

        header('Location: http://localhost/FundiFix-Project/public/index.php?action=inventory');
        exit;
    }

    // ==================== GET INVENTORY STATS (API) ====================
    public function getStats()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $fundi_id = $_SESSION['user']['id'];
        $summary = $this->inventoryModel->getInventorySummary($fundi_id);
        $lowStockItems = $this->inventoryModel->getLowStockItems($fundi_id);

        header('Content-Type: application/json');
        echo json_encode([
            'summary' => $summary,
            'low_stock_alerts' => $lowStockItems
        ]);
        exit;
    }
}