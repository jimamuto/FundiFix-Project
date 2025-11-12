<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'fundi') {
    header('Location: http://localhost/FundiFix-Project/public/index.php?action=login');
    exit;
}

require_once __DIR__ . '/../layouts/header.php';

// Get analytics data
$stats = $_SESSION['fundi_stats'] ?? [];
$hasData = $stats['has_data'] ?? false;
$isDemoUser = in_array($_SESSION['user']['id'], [2, 3, 4, 5, 7]);
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-muted">Analytics & Performance</h5>
            <h1 class="fw-bold">My Performance Dashboard</h1>
            <p class="text-muted">Track your bookings, earnings, and ratings.</p>
            <hr>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-calendar-check fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $stats['total_bookings'] ?? 0; ?></h4>
                    <p class="mb-0">Total Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo $stats['completed_bookings'] ?? 0; ?></h4>
                    <p class="mb-0">Completed Jobs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-cash-coin fs-1 mb-2"></i>
                    <h4 class="fw-bold">KES <?php echo number_format($stats['total_earnings'] ?? 0, 2); ?></h4>
                    <p class="mb-0">Total Earnings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <i class="bi bi-star fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?php echo number_format($stats['average_rating'] ?? 0, 1); ?>/5</h4>
                    <p class="mb-0">Average Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <?php if ($hasData || $isDemoUser): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Export Reports</h5>
                    <p class="text-muted">Download your performance data in different formats.</p>
                    <div class="btn-group">
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=export_fundi_pdf" 
                           class="btn btn-danger">
                            <i class="bi bi-file-pdf"></i> Export to PDF
                        </a>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=export_fundi_excel" 
                           class="btn btn-success">
                            <i class="bi bi-file-spreadsheet"></i> Export to Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Charts Row -->
    <div class="row">
        <?php if ($hasData || $isDemoUser): ?>
            <!-- User has data or is demo user - show charts -->
            
            <!-- Bookings Overview Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Bookings Overview</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingsChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Monthly Performance</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Earnings by Service Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Earnings by Service</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="earningsChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Rating Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ratingsChart" height="250"></canvas>
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
                        <p class="text-muted">Your analytics will appear here once you start receiving bookings and completing jobs.</p>
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
    
    // Bookings Overview Chart (Doughnut)
    const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
    new Chart(bookingsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [
                    <?php echo $stats['completed_bookings'] ?? 10; ?>,
                    <?php echo $stats['pending_bookings'] ?? 5; ?>,
                    <?php echo $stats['cancelled_bookings'] ?? 2; ?>
                ],
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
                },
                title: {
                    display: true,
                    text: 'Booking Status Distribution'
                }
            }
        }
    });

    // Monthly Performance Chart (Line)
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Completed Jobs',
                data: [8, 12, 6, 14, 10, 16],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Earnings (KES)',
                data: [12000, 18000, 9000, 21000, 15000, 24000],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Number of Jobs'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Earnings (KES)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Earnings by Service Chart (Bar)
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'bar',
        data: {
            labels: ['Plumbing', 'Electrical', 'Carpentry', 'Interior Design'],
            datasets: [{
                label: 'Earnings (KES)',
                data: [45000, 38000, 22000, 31000],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)'
                ],
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
                        text: 'Earnings (KES)'
                    }
                }
            }
        }
    });

    // Rating Distribution Chart (Pie)
    const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
    new Chart(ratingsCtx, {
        type: 'pie',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                data: [15, 8, 3, 1, 0],
                backgroundColor: [
                    '#28a745',
                    '#20c997',
                    '#ffc107',
                    '#fd7e14',
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

    <?php endif; ?>
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>