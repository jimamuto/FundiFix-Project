<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - FundiFix Admin</title>
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
                            <a class="nav-link active" href="?action=admin_users">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?action=admin_services">
                                <i class="fas fa-tools me-2"></i>
                                Manage Services
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit User</h1>
                    <a href="?action=admin_users" class="btn btn-secondary">Back to Users</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="?action=update_user">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    <option value="fundi" <?= $user['role'] === 'fundi' ? 'selected' : '' ?>>Fundi</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" <?= $user['is_verified'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_verified">
                                        Email Verified
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="?action=admin_users" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>