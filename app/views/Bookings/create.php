
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h2>Book a Service</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="http://localhost/FundiFix-Project/public/index.php?action=bookings_create">
                <div class="form-group">
                    <label for="service_id">Service *</label>
                    <select class="form-control" id="service_id" name="service_id" required>
                        <option value="">Select a service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>" 
                                <?= isset($_GET['service_id']) && $_GET['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['name']) ?> - KES <?= number_format($service['price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fundi_id">Select Fundi *</label>
                    <select class="form-control" id="fundi_id" name="fundi_id" required>
                        <option value="">Select a fundi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Additional Notes (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                        placeholder="Any specific requirements or details..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Book Now</button>
                <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('service_id').addEventListener('change', function() {
    const serviceId = this.value;
    const fundiSelect = document.getElementById('fundi_id');
    
    if (serviceId) {
        fetch(`http://localhost/FundiFix-Project/public/index.php?action=api_fundis_by_service&service_id=${serviceId}`)
            .then(response => response.json())
            .then(fundis => {
                fundiSelect.innerHTML = '<option value="">Select a fundi</option>';
                fundis.forEach(fundi => {
                    fundiSelect.innerHTML += `<option value="${fundi.user_id}">
                        ${fundi.name} - ${fundi.location} (${fundi.skills})
                    </option>`;
                });
            })
            .catch(() => {
                fundiSelect.innerHTML = '<option value="">Error loading fundis</option>';
            });
    } else {
        fundiSelect.innerHTML = '<option value="">Select a fundi</option>';
    }
});

// Preselect fundi list if a service is already chosen
<?php if (isset($_GET['service_id'])): ?>
document.getElementById('service_id').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>