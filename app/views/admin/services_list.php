<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - FundiFix Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: #333;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center mb-4">FundiFix Admin</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="?action=admin_dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?action=admin_users">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="?action=admin_services">
                                <i class="fas fa-tools me-2"></i>
                                Manage Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Services</h1>
                    <a href="?action=add_service" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Service
                    </a>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($services) && is_array($services)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-dark text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Service Name</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td class="text-center"><?= htmlspecialchars($service['id']) ?></td>
                                                <td><strong><?= htmlspecialchars($service['name']) ?></strong></td>
                                                <td><?= htmlspecialchars($service['description'] ?? 'No description') ?></td>
                                                <td class="text-center">KSh <?= number_format($service['price'], 2) ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $service['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($service['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars($service['created_at']) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="index.php?action=edit_service&id=<?= $service['id'] ?>" 
                                                           class="btn btn-outline-primary"
                                                           title="Edit Service">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="index.php?action=delete_service&id=<?= $service['id'] ?>" 
                                                           class="btn btn-outline-danger"
                                                           onclick="return confirm('Are you sure you want to delete this service?');"
                                                           title="Delete Service">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Services Found</h4>
                                <p class="text-muted">Get started by adding your first service.</p>
                                <a href="?action=add_service" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add Your First Service
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>