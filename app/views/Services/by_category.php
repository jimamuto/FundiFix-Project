<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h2>Services in <?= htmlspecialchars(ucfirst($category ?? 'Unknown Category')) ?></h2>
    
    <div class="mb-3">
        <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-secondary">← Back to All Services</a>
    </div>

    <?php if (empty($services)): ?>
        <div class="alert alert-info">No services found in this category.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($services as $service): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($service['name'] ?? 'Unknown Service') ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($service['category'] ?? 'Uncategorized') ?></h6>
                        <p class="card-text"><?= htmlspecialchars($service['description'] ?? 'No description available') ?></p>
                        <p class="card-text">
                            <strong>Price:</strong> KES <?= number_format($service['price'] ?? 0, 2) ?>
                        </p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings_create&service_id=<?= $service['id'] ?? '' ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
