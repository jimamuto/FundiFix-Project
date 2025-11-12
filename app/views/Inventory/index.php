<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
    header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
    exit;
}

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-muted">Inventory Management</h5>
            <h1 class="fw-bold">My Tool Inventory</h1>
            <p class="text-muted">Manage your tools, equipment, and materials.</p>
            <hr>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-box-seam fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $summary['total_items'] ?? 0; ?></h4>
                    <p class="mb-0">Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $summary['total_quantity'] ?? 0; ?></h4>
                    <p class="mb-0">Total Quantity</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $summary['low_stock_items'] ?? 0; ?></h4>
                    <p class="mb-0">Low Stock</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-danger text-white">
                <div class="card-body">
                    <i class="bi bi-x-circle fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $summary['out_of_stock_items'] ?? 0; ?></h4>
                    <p class="mb-0">Out of Stock</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Item Form -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Add New Item</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=inventory_add">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Tools, Materials" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="unit_price" class="form-label">Unit Price (KES)</label>
                            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="1" placeholder="Optional description"></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Item</button>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">My Inventory Items</h5>
        </div>
        <div class="card-body">
            <?php if (empty($inventory)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                    <h5>No inventory items yet</h5>
                    <p class="text-muted">Add your first item using the form above.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                        <?php if (!empty($item['description'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($item['description']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td>
                                        <span class="fw-bold"><?php echo $item['quantity']; ?></span>
                                    </td>
                                    <td>KES <?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'available' => 'badge bg-success',
                                            'low_stock' => 'badge bg-warning',
                                            'out_of_stock' => 'badge bg-danger'
                                        ];
                                        $statusText = [
                                            'available' => 'Available',
                                            'low_stock' => 'Low Stock',
                                            'out_of_stock' => 'Out of Stock'
                                        ];
                                        ?>
                                        <span class="<?php echo $statusClass[$item['status']]; ?>">
                                            <?php echo $statusText[$item['status']]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Use Item Form -->
                                            <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=inventory_use" class="d-inline">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="amount_used" class="form-control" style="width: 80px;" 
                                                           placeholder="Qty" min="1" max="<?php echo $item['quantity']; ?>" 
                                                           <?php echo $item['quantity'] == 0 ? 'disabled' : ''; ?>>
                                                    <button type="submit" class="btn btn-outline-primary" <?php echo $item['quantity'] == 0 ? 'disabled' : ''; ?>>
                                                        Use
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <!-- Delete Form -->
                                            <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=inventory_delete" class="d-inline">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>