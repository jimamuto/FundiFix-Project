<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - FundiFix Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
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
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Add New Service</h1>
                    <a href="?action=admin_services" class="btn btn-secondary">Back to Services</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="?action=create_service">
                            <div class="mb-3">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Plumbing, Electrical, Carpentry" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the service..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price (KSh)</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Service</button>
                            <a href="?action=admin_services" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>