<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h2>My Fundi Profile</h2>

    <!-- Back to Dashboard -->
    <div class="mb-3">
        <a href="http://localhost/FundiFix-Project/public/index.php?action=dashboard" 
           class="btn btn-outline-secondary mb-3">← Dashboard</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Update Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="http://localhost/FundiFix-Project/public/index.php?action=update_fundi_profile">
                        <div class="form-group">
                            <label for="skills">Skills *</label>
                            <input type="text" class="form-control" id="skills" name="skills"
                                   value="<?= htmlspecialchars($fundiProfile['skills'] ?? '') ?>"
                                   placeholder="e.g., Plumbing, Electrical, Carpentry" required>
                            <small class="form-text text-muted">Separate multiple skills with commas</small>
                        </div>

                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   value="<?= htmlspecialchars($fundiProfile['location'] ?? '') ?>"
                                   placeholder="e.g., Nairobi, Mombasa" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                   value="<?= htmlspecialchars($fundiProfile['phone_number'] ?? '') ?>"
                                   placeholder="e.g., 0712345678" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Fundi Services Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">My Services</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($fundiServices)): ?>
                        <ul class="list-group">
                            <?php foreach ($fundiServices as $service): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($service['name'] ?? 'Unknown Service') ?>
                                    <span class="badge badge-primary badge-pill">
                                        KES <?= number_format($service['price'] ?? 0, 2) ?>
                                    </span>
                                    <a href="http://localhost/FundiFix-Project/public/index.php?action=remove_fundi_service&service_id=<?= $service['id'] ?? '' ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Remove this service from your profile?')">
                                       Remove
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">You haven't added any services yet.</p>
                    <?php endif; ?>

                    <hr>

                    <!-- Add New Service -->
                    <h6>Add New Service</h6>
                    <form method="POST" 
                          action="http://localhost/FundiFix-Project/public/index.php?action=add_fundi_service">
                        <div class="form-group">
                            <select class="form-control" name="service_id" required>
                                <option value="">Select a service to add</option>
                                <?php foreach ($allServices as $service): ?>
                                    <?php 
                                    $alreadyAdded = false;
                                    foreach ($fundiServices as $myService) {
                                        if ($myService['id'] == $service['id']) {
                                            $alreadyAdded = true;
                                            break;
                                        }
                                    }
                                    if (!$alreadyAdded && ($service['status'] === 'active')): 
                                    ?>
                                        <option value="<?= $service['id'] ?>">
                                            <?= htmlspecialchars($service['name'] ?? 'Unknown Service') ?> - 
                                            KES <?= number_format($service['price'] ?? 0, 2) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Add Service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Fundi Statistics -->
    <?php if (isset($fundiStats)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">My Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body text-center">
                                    <h3 class="card-title"><?= $fundiStats['total_bookings'] ?? 0 ?></h3>
                                    <p class="card-text">Total Bookings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success">
                                <div class="card-body text-center">
                                    <h3 class="card-title"><?= $fundiStats['completed_bookings'] ?? 0 ?></h3>
                                    <p class="card-text">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body text-center">
                                    <h3 class="card-title"><?= $fundiStats['pending_bookings'] ?? 0 ?></h3>
                                    <p class="card-text">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info">
                                <div class="card-body text-center">
                                    <h3 class="card-title"><?= number_format($fundiStats['average_rating'] ?? 0, 1) ?></h3>
                                    <p class="card-text">Avg Rating</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>