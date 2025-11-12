<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">My Bookings</h1>
            <p class="text-muted">Manage your incoming booking requests and track your jobs.</p>

            <div class="d-flex gap-2 mb-4">
                <a href="http://localhost/FundiFix-Project/public/index.php?action=dashboard" class="btn btn-outline-secondary">
                    ← Back to Dashboard
                </a>
                <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_profile" class="btn btn-outline-primary">
                    Update My Profile
                </a>
            </div>
            <hr>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                <h3 class="text-muted">No Bookings Yet</h3>
                <p class="text-muted">You don't have any booking requests yet. Make sure your profile is complete and you've added your services.</p>
                <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_profile" class="btn btn-primary">
                    <i class="bi bi-person-badge me-2"></i>Complete My Profile
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'pending')) ?></h4>
                            <p class="mb-0">Pending</p>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'accepted')) ?></h4>
                            <p class="mb-0">Accepted</p>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'completed')) ?></h4>
                            <p class="mb-0">Completed</p>
                        </div>
                        <i class="bi bi-award fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled' || $b['status'] === 'declined')) ?></h4>
                            <p class="mb-0">Cancelled</p>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-check me-2"></i>All Bookings
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Customer</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-tools text-primary me-2"></i>
                                            <strong><?= htmlspecialchars($booking['service_name'] ?? 'General Service') ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-secondary me-2"></i>
                                            <div>
                                                <strong><?= htmlspecialchars($booking['resident_name'] ?? 'Unknown Customer') ?></strong>
                                                <?php if (!empty($booking['resident_email'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($booking['resident_email']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="bi bi-calendar me-1"></i><?= date('M j, Y', strtotime($booking['created_at'])) ?><br>
                                            <i class="bi bi-clock me-1"></i><?= date('g:i A', strtotime($booking['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?= 
                                            $booking['status'] === 'completed' ? 'success' : 
                                            ($booking['status'] === 'accepted' ? 'primary' : 
                                            ($booking['status'] === 'cancelled' ? 'danger' : 
                                            ($booking['status'] === 'declined' ? 'warning' : 'secondary'))) 
                                        ?>">
                                            <i class="bi bi-<?= 
                                                $booking['status'] === 'completed' ? 'check-circle' : 
                                                ($booking['status'] === 'accepted' ? 'play-circle' : 
                                                ($booking['status'] === 'cancelled' ? 'x-circle' : 'clock')) 
                                            ?> me-1"></i>
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= !empty($booking['description']) 
                                                ? htmlspecialchars(substr($booking['description'], 0, 50)) . 
                                                  (strlen($booking['description']) > 50 ? '...' : '') 
                                                : 'No description' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=bookings_update_status" class="d-inline">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Accept this booking request from <?= htmlspecialchars($booking['resident_name'] ?? 'the customer') ?>?')">
                                                        <i class="bi bi-check-lg me-1"></i>Accept
                                                    </button>
                                                </form>
                                                <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=bookings_update_status" class="d-inline">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Decline this booking request from <?= htmlspecialchars($booking['resident_name'] ?? 'the customer') ?>?')">
                                                        <i class="bi bi-x-lg me-1"></i>Decline
                                                    </button>
                                                </form>
                                            <?php elseif ($booking['status'] === 'accepted'): ?>
                                                <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=bookings_update_status" class="d-inline">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-primary btn-sm"
                                                        onclick="return confirm('Mark this booking as completed? This will notify the customer.')">
                                                        <i class="bi bi-check2-all me-1"></i>Complete
                                                    </button>
                                                </form>
                                            <?php elseif ($booking['status'] === 'completed'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-award me-1"></i>Completed
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">No actions available</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-question-circle me-2"></i>Need Help?
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Email Notifications</h6>
                        <p class="small text-muted">You'll receive email alerts for new booking requests. Respond within 24 hours for best results.</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Response Time</h6>
                        <p class="small text-muted">Quick responses improve your booking acceptance rate and customer satisfaction.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>