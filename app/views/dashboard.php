<?php
// Redirect users who are not logged in to the login page
if (!isset($_SESSION['user'])) {
    header("Location: http://localhost/FundiFix-Project/public/index.php?action=login");
    exit();
}

require_once 'layouts/header.php';

// Retrieve user details and role
$userRole = $_SESSION['user']['role'] ?? 'resident';
$userName = htmlspecialchars($_SESSION['user']['name'] ?? 'User');
$userStats = $_SESSION['user_stats'] ?? [];
?>

<div class="container-fluid py-4">
    <?php if ($userRole === 'fundi'): ?>
        <!-- ==================== FUNDI DASHBOARD ==================== -->

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="text-muted">Dashboard</h5>
                <h1 class="fw-bold">Welcome Back, <?php echo $userName; ?>!</h1>
                <p class="text-muted">Manage your services and bookings.</p>
                <hr>
            </div>
        </div>

        <!-- Quick Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bi bi-clock-history fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['pending_bookings'] ?? 0; ?></h4>
                        <p class="mb-0">Pending Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['completed_bookings'] ?? 0; ?></h4>
                        <p class="mb-0">Completed Jobs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bi bi-cash-coin fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['total_earnings'] ?? 'KES 0'; ?></h4>
                        <p class="mb-0">Total Earnings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <i class="bi bi-star fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['average_rating'] ?? '0.0'; ?></h4>
                        <p class="mb-0">Average Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Requests & Analytics -->
            <div class="col-md-8">
                <!-- Building Requests -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Building Requests</h5>
                    </div>
                    <div class="card-body">
                        <!-- Combined Jobs Summary -->
                        <div class="mb-4">
                            <h6 class="text-muted">Combined Jobs</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-primary text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Pending Requests</h6>
                                            <h3 class="fw-bold"><?php echo $userStats['pending_bookings'] ?? 0; ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Completed Jobs</h6>
                                            <h3 class="fw-bold"><?php echo $userStats['completed_bookings'] ?? 0; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access Forms -->
                        <div class="row">
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border text-center">
                                        <div class="card-body">
                                            <h6 class="card-title">My Fundi Profile</h6>
                                            <p class="text-muted small">Logout</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- Performance Analytics -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Performance Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-muted">Monthly Job Performance</h6>
                                <canvas id="monthlyPerformanceChart" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Monthly by Service</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Training
                                        <span class="badge bg-primary rounded-pill">12</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Electrical Other
                                        <span class="badge bg-success rounded-pill">8</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Capacity
                                        <span class="badge bg-info rounded-pill">4</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Profile & Activity -->
            <div class="col-md-4">
                <!-- Fundi Profile -->
                <div class="card mb-4 text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">My Fundi Profile</h5>
                        <p class="text-muted">Manage your skills, services, and profile details.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_profile" class="btn btn-outline-primary w-100">Update Profile</a>
                    </div>
                </div>

                <!-- Performance -->
                <div class="card mb-4 text-center">
                    <div class="card-body">
                        <i class="bi bi-graph-up fs-1 text-success mb-3"></i>
                        <h5 class="card-title">My Performance</h5>
                        <p class="text-muted">View earnings, ratings, and booking stats.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=fundi_stats" class="btn btn-outline-success w-100">View Stats</a>
                    </div>
                </div>

                <!-- INVENTORY CARD - ADD THIS -->
<div class="card mb-4 text-center">
    <div class="card-body">
        <i class="bi bi-box-seam fs-1 text-warning mb-3"></i>
        <h5 class="card-title">My Inventory</h5>
        <p class="text-muted">Manage your tools, equipment, and materials.</p>
        <a href="http://localhost/FundiFix-Project/public/index.php?action=inventory" class="btn btn-outline-warning w-100">Manage Inventory</a>
    </div>
</div>

                <!-- Recent Activity -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">8+</span>
                                    <span>New booking request for plumbing service</span>
                                </div>
                                <small class="text-muted">1 hour ago</small>
                            </div>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">$</span>
                                    <span>Payment received for electrical repair</span>
                                </div>
                                <small class="text-muted">5 hours ago</small>
                            </div>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">★</span>
                                    <span>Received 5-star rating from Jim Amuto</span>
                                </div>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Requests -->
                <div class="card mb-4 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Job Requests</h5>
                        <p class="text-muted">View and manage incoming booking requests.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings" class="btn btn-outline-info w-100">Manage Bookings</a>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ==================== RESIDENT DASHBOARD ==================== -->

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="text-muted">Dashboard</h5>
                <h1 class="fw-bold">Welcome Back, <?php echo $userName; ?>!</h1>
                <p class="text-muted">Book services and manage your requests.</p>
                <hr>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bi bi-clock-history fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['pending_bookings'] ?? 0; ?></h4>
                        <p class="mb-0">Pending Bookings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['completed_bookings'] ?? 0; ?></h4>
                        <p class="mb-0">Completed Jobs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bi bi-calendar-check fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['total_bookings'] ?? 0; ?></h4>
                        <p class="mb-0">Total Bookings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <i class="bi bi-star fs-1 mb-2"></i>
                        <h4 class="fw-bold"><?php echo $userStats['average_rating'] ?? '0.0'; ?></h4>
                        <p class="mb-0">Average Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resident Actions -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-tools fs-2 text-primary me-3"></i>
                            <h4 class="card-title mb-0">Book a Service</h4>
                        </div>
                        <p class="text-muted">Find and book professional fundis for your repair needs.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=services_available" class="btn btn-outline-primary mt-auto">Browse Services</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-week fs-2 text-success me-3"></i>
                            <h4 class="card-title mb-0">My Bookings</h4>
                        </div>
                        <p class="text-muted">View your past and upcoming bookings and their status.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=bookings" class="btn btn-outline-success mt-auto">View My Bookings</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle fs-2 text-info me-3"></i>
                            <h4 class="card-title mb-0">My Profile</h4>
                        </div>
                        <p class="text-muted">Manage your personal and public profile details.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=profile" class="btn btn-outline-info mt-auto">Manage Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-graph-up fs-2 text-warning me-3"></i>
                            <h4 class="card-title mb-0">My Analytics</h4>
                        </div>
                        <p class="text-muted">View analytics and reports on your service usage.</p>
                        <a href="http://localhost/FundiFix-Project/public/index.php?action=resident_analytics" class="btn btn-outline-warning mt-auto">View Analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-activity me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <span>Plumbing service completed by Jim Amuto</span>
                                </div>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-clock text-warning me-2"></i>
                                    <span>Electrical repair scheduled for tomorrow</span>
                                </div>
                                <small class="text-muted">1 day ago</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-star text-info me-2"></i>
                                    <span>You rated a carpentry service 5 stars</span>
                                </div>
                                <small class="text-muted">3 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($userRole === 'fundi'): ?>
        // Fundi monthly performance chart
        const ctx = document.getElementById('monthlyPerformanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Completed Jobs',
                        data: [8, 12, 6, 14, 10, 16],
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pending Requests',
                        data: [3, 5, 2, 6, 4, 8],
                        backgroundColor: 'rgba(255, 205, 86, 0.8)',
                        borderColor: 'rgba(255, 205, 86, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    <?php endif; ?>
});
</script>

<?php
require_once 'layouts/footer.php';
?>
