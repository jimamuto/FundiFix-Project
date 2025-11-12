<?php
namespace App\Models;

use PDO;

class Inventory
{
    private PDO $conn;
    private string $table_name = "inventory";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // ==================== CREATE OPERATIONS ====================

    /**
     * Add new inventory item
     */
    public function addItem(int $fundi_id, array $data): bool
    {
        $sql = "INSERT INTO {$this->table_name} 
                (fundi_id, item_name, category, quantity, unit_price, description, status) 
                VALUES (:fundi_id, :item_name, :category, :quantity, :unit_price, :description, :status)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->bindParam(':item_name', $data['item_name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':unit_price', $data['unit_price']);
        $stmt->bindParam(':description', $data['description']);
        
        // Auto-set status based on quantity
        $status = $this->calculateStatus($data['quantity']);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    // ==================== READ OPERATIONS ====================

    /**
     * Get all inventory items for a fundi
     */
    public function getByFundi(int $fundi_id): array
    {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE fundi_id = :fundi_id 
                ORDER BY category, item_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get inventory item by ID
     */
    public function getById(int $item_id): ?array
    {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE id = :id 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get inventory summary for dashboard
     */
    public function getInventorySummary(int $fundi_id): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    SUM(quantity) as total_quantity,
                    COUNT(CASE WHEN status = 'low_stock' THEN 1 END) as low_stock_items,
                    COUNT(CASE WHEN status = 'out_of_stock' THEN 1 END) as out_of_stock_items
                FROM {$this->table_name} 
                WHERE fundi_id = :fundi_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: [
            'total_items' => 0,
            'total_quantity' => 0,
            'low_stock_items' => 0,
            'out_of_stock_items' => 0
        ];
    }

    // ==================== UPDATE OPERATIONS ====================

    /**
     * Update item quantity
     */
    public function updateQuantity(int $item_id, int $new_quantity): bool
    {
        $status = $this->calculateStatus($new_quantity);
        
        $sql = "UPDATE {$this->table_name} 
                SET quantity = :quantity, status = :status 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Use item (reduce quantity)
     */
    public function useItem(int $item_id, int $amount_used): bool
    {
        $sql = "UPDATE {$this->table_name} 
                SET quantity = GREATEST(0, quantity - :amount_used) 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':amount_used', $amount_used, PDO::PARAM_INT);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->updateItemStatus($item_id);
            return true;
        }
        return false;
    }

    /**
     * Restock item (increase quantity)
     */
    public function restockItem(int $item_id, int $amount_added): bool
    {
        $sql = "UPDATE {$this->table_name} 
                SET quantity = quantity + :amount_added 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':amount_added', $amount_added, PDO::PARAM_INT);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->updateItemStatus($item_id);
            return true;
        }
        return false;
    }

    /**
     * Update item details
     */
    public function updateItem(int $item_id, array $data): bool
    {
        $sql = "UPDATE {$this->table_name} 
                SET item_name = :item_name, 
                    category = :category, 
                    unit_price = :unit_price, 
                    description = :description 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':item_name', $data['item_name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':unit_price', $data['unit_price']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // ==================== DELETE OPERATIONS ====================

    /**
     * Delete item
     */
    public function deleteItem(int $item_id): bool
    {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Calculate status based on quantity
     */
    private function calculateStatus(int $quantity): string
    {
        if ($quantity > 10) {
            return 'available';
        } elseif ($quantity > 0) {
            return 'low_stock';
        } else {
            return 'out_of_stock';
        }
    }

    /**
     * Update item status based on current quantity
     */
    private function updateItemStatus(int $item_id): void
    {
        $sql = "UPDATE {$this->table_name} 
                SET status = CASE 
                    WHEN quantity > 10 THEN 'available'
                    WHEN quantity > 0 THEN 'low_stock' 
                    ELSE 'out_of_stock' 
                END 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Get categories used by fundi
     */
    public function getFundiCategories(int $fundi_id): array
    {
        $sql = "SELECT DISTINCT category 
                FROM {$this->table_name} 
                WHERE fundi_id = :fundi_id 
                ORDER BY category";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get low stock items for alerts
     */
    public function getLowStockItems(int $fundi_id): array
    {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE fundi_id = :fundi_id 
                AND status IN ('low_stock', 'out_of_stock')
                ORDER BY status DESC, item_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fundi_id', $fundi_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}