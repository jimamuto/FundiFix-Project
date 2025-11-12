<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">

<!-- Back to Dashboard Navigation -->
    <div class="mb-3">
        <a href="http://localhost/FundiFix-Project/public/index.php?action=dashboard" class="btn btn-outline-secondary mb-3">← Dashboard</a>
    </div>
    <h2>Available Services</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Category Filter -->
    <div class="mb-4">
        <h5>Filter by Category:</h5>
        <div class="btn-group">
            <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-outline-primary">All</a>
            <?php 
            // Use the serviceModel directly instead of serviceController
            $categories = $serviceModel->getCategories();
            foreach ($categories as $category): 
            ?>
                <a href="http://localhost/FundiFix-Project/public/index.php?action=services_category&cat=<?= urlencode($category) ?>" 
                   class="btn btn-outline-primary"><?= htmlspecialchars(ucfirst($category)) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <?php foreach ($services as $service): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($service['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($service['category']) ?></h6>
                    <p class="card-text"><?= htmlspecialchars($service['description']) ?></p>
                    <p class="card-text">
                        <strong>Price:</strong> KES <?= number_format($service['price'], 2) ?><br>
                        <strong>Available Fundis:</strong> <?= $service['fundi_count'] ?>
                    </p>
                    <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings_create&service_id=<?= $service['id'] ?>" class="btn btn-primary">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>