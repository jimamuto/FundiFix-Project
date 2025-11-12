<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- Navigation -->
<div class="mb-3">
    <a href="http://localhost/FundiFix-Project/public/index.php?action=dashboard" class="btn btn-outline-secondary mb-3">← Dashboard</a>
</div>

<div class="container">
    <h2>My Bookings</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">You haven't made any bookings yet.</div>
        <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-primary">Book a Service</a>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Fundi</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['service_name'] ?? 'General Service') ?></td>
                    <td><?= htmlspecialchars($booking['fundi_name'] ?? 'Unknown Fundi') ?></td>
                    <td>
                        <span class="badge badge-<?= 
                            $booking['status'] === 'completed' ? 'success' : 
                            ($booking['status'] === 'accepted' ? 'primary' : 
                            ($booking['status'] === 'cancelled' ? 'danger' : 'warning')) 
                        ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y g:i A', strtotime($booking['created_at'])) ?></td>
                    <td>
                        <?php if ($booking['status'] === 'pending'): ?>
                            <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings_cancel&id=<?= $booking['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                               Cancel
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
