<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Business Analytics</h1>
            <p class="text-muted">Detailed business reports and performance analytics</p>
            
            <div class="d-flex gap-2">
                <a href="http://localhost/FundiFix-Project/public/index.php?action=dashboard" class="btn btn-secondary">← Back to Dashboard</a>
                <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_stats" class="btn btn-outline-primary">View Performance Stats</a>
            </div>
            <hr>
        </div>
    </div>

    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info"><?= $_SESSION['info'] ?></div>
        <?php unset($_SESSION['info']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-graph-up-arrow fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?= $analyticsData['stats']['total_bookings'] ?? 0 ?></h4>
                    <p class="mb-0">Total Bookings</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-currency-dollar fs-1 mb-2"></i>
                    <h4 class="fw-bold">KES <?= number_format($analyticsData['stats']['estimated_earnings'] ?? 0, 2) ?></h4>
                    <p class="mb-0">Total Revenue</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-person-check fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?= $analyticsData['stats']['completed_bookings'] ?? 0 ?></h4>
                    <p class="mb-0">Completed Jobs</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <i class="bi bi-star fs-1 mb-2"></i>
                    <h4 class="fw-bold"><?= number_format($analyticsData['stats']['average_rating'] ?? 0, 1) ?></h4>
                    <p class="mb-0">Avg Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>Monthly Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyPerformanceChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2"></i>Earnings by Service</h5>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics and Insights -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-speedometer2 me-2"></i>Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <?php
                        $totalBookings = $analyticsData['stats']['total_bookings'] ?? 0;
                        $completedBookings = $analyticsData['stats']['completed_bookings'] ?? 0;
                        $pendingBookings = $analyticsData['stats']['pending_bookings'] ?? 0;
                        $completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0;
                        $responseRate = $totalBookings > 0 ? round((($completedBookings + $pendingBookings) / $totalBookings) * 100, 1) : 0;
                    ?>

                    <div class="mb-3">
                        <strong>Completion Rate:</strong>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-success" style="width: <?= $completionRate ?>%">
                                <?= $completionRate ?>%
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Response Rate:</strong>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-info" style="width: <?= $responseRate ?>%">
                                <?= $responseRate ?>%
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Customer Satisfaction:</strong>
                        <div class="mt-1">
                            <?php $rating = $analyticsData['stats']['average_rating'] ?? 0; ?>
                            <?php if ($rating >= 4): ?>
                                <span class="badge bg-success">Excellent (<?= number_format($rating, 1) ?>)</span>
                            <?php elseif ($rating >= 3): ?>
                                <span class="badge bg-warning">Good (<?= number_format($rating, 1) ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Needs Improvement (<?= number_format($rating, 1) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Recent Insights</h5>
                </div>
                <div class="card-body">
                    <?php if ($totalBookings === 0): ?>
                        <p class="text-muted">Start accepting bookings to see your analytics data.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_profile" class="btn btn-primary">Complete Your Profile</a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <strong>Performance Summary:</strong><br>
                            You've completed <strong><?= $completedBookings ?></strong> out of <strong><?= $totalBookings ?></strong> total bookings.
                        </div>

                        <?php if ($completionRate < 70): ?>
                            <div class="alert alert-warning">
                                <strong>Tip:</strong> Improve your completion rate by accepting more booking requests.
                            </div>
                        <?php endif; ?>

                        <?php if ($rating < 4): ?>
                            <div class="alert alert-warning">
                                <strong>Tip:</strong> Focus on delivering quality service to improve your ratings.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const performanceCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [
                {
                    label: 'Completed Jobs',
                    data: [8,12,6,14,10,16,12,15,18,14,16,20],
                    backgroundColor: 'rgba(75,192,192,0.8)',
                    borderColor: 'rgba(75,192,192,1)',
                    borderWidth: 1
                },
                {
                    label: 'Pending Requests',
                    data: [3,5,2,6,4,8,5,7,6,4,5,8],
                    backgroundColor: 'rgba(255,205,86,0.8)',
                    borderColor: 'rgba(255,205,86,1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Monthly Job Performance' },
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Number of Jobs' } },
                x: { title: { display: true, text: 'Months' } }
            }
        }
    });

    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Plumbing', 'Electrical', 'Carpentry', 'Other Services'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    'rgba(255,99,132,0.8)',
                    'rgba(54,162,235,0.8)',
                    'rgba(255,205,86,0.8)',
                    'rgba(75,192,192,0.8)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54,162,235,1)',
                    'rgba(255,205,86,1)',
                    'rgba(75,192,192,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Earnings Distribution' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${percentage}% (KES ${(value * 1000).toLocaleString()})`;
                        }
                    }
                }
            }
        }
    });

    // Animate progress bars
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-in-out';
            bar.style.width = width;
        }, 500);
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>