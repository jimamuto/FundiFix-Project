<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resident') {
    header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
    exit;
}

require_once __DIR__ . '/../layouts/header.php';

// Get analytics data from session
$analyticsData = $_SESSION['resident_analytics'] ?? [];
$hasData = $analyticsData['has_data'] ?? false;
$isDemoUser = $_SESSION['user']['id'] == 6; // Jane Resident
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-muted">Usage Analytics</h5>
            <h1 class="fw-bold">My Service Analytics</h1>
            <p class="text-muted">Track your service usage and spending.</p>
            <hr>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-calendar-check fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $analyticsData['total_bookings'] ?? 0; ?></h4>
                    <p class="mb-0">Total Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $analyticsData['completed_bookings'] ?? 0; ?></h4>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-cash-coin fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $analyticsData['total_spent'] ?? 'KES 0'; ?></h4>
                    <p class="mb-0">Total Spent</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <i class="bi bi-star fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $analyticsData['average_rating'] ?? '0.0'; ?>/5</h4>
                    <p class="mb-0">Avg Rating Given</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <?php if ($hasData || $isDemoUser): ?>
            <!-- User has data or is demo user - show charts -->
            
            <!-- Monthly Booking Trends -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Monthly Booking Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendsChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Service Type Breakdown -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Service Type Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="serviceBreakdownChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Booking Status Distribution -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Booking Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Spending by Month -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Monthly Spending</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="spendingChart" height="250"></canvas>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- New user - show empty state -->
            <div class="col-12">
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-bar-chart fs-1 text-muted mb-3"></i>
                        <h4>No Analytics Data Yet</h4>
                        <p class="text-muted">Your analytics will appear here once you start booking services and completing jobs.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-primary">
                            Browse Services to Get Started
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($hasData || $isDemoUser): ?>

    // Monthly Trends Chart (Line)
    const trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Bookings',
                data: [2, 3, 1, 4, 2, 5],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Bookings'
                    }
                }
            }
        }
    });

    // Service Breakdown Chart (Doughnut)
    const serviceCtx = document.getElementById('serviceBreakdownChart').getContext('2d');
    new Chart(serviceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Plumbing', 'Electrical', 'Carpentry', 'Interior Design'],
            datasets: [{
                data: [6, 4, 2, 3],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Status Distribution Chart (Pie)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [10, 3, 2],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Spending Chart (Bar)
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    new Chart(spendingCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Spending (KES)',
                data: [3000, 4500, 1500, 6000, 3000, 7500],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount (KES)'
                    }
                }
            }
        }
    });

    <?php endif; ?>
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
