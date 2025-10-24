<?php
// If the user is not logged in, they are immediately redirected to the login page.
if (!isset($_SESSION['user'])) {
    header("Location: http://localhost/FundiFix-Project/public/index.php?action=login");
    exit();
}

// If the check passes, we can load the page.
require_once 'layouts/header.php';
?>


<div class="container py-5">

    <!-- Section 1: The Personalized Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h5 class="text-muted fw-light">Dashboard</h5>
            <h1 class="fw-bold">Welcome Back, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?>!</h1>
        </div>
        <div>
            <a href="#" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle me-2"></i>New Booking
            </a>
        </div>
    </div>

    <!-- Section 2: Quick Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center p-3 shadow-sm border-0">
                <div class="card-body">
                    <p class="text-muted mb-0 fs-5">Pending Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 shadow-sm border-0">
                <div class="card-body">
                    <p class="text-muted mb-0 fs-5">Completed Jobs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-3 shadow-sm border-0">
                <div class="card-body">
                    <p class="text-muted mb-0 fs-5">Unread Messages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Main Action Cards -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-circle fs-2 text-primary me-3"></i>
                        <h4 class="card-title mb-0">Your Account</h4>
                    </div>
                    <p class="card-text text-muted">View your public profile and manage your personal information.</p>
                    <a href="http://localhost/FundiFix-Project/public/index.php?action=profile" class="btn btn-outline-primary btn-lg">Manage My Profile</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-calendar-week fs-2 text-primary me-3"></i>
                        <h4 class="card-title mb-0">Bookings</h4>
                    </div>
                    <p class="card-text text-muted">View your past and upcoming service bookings and their status.</p>
                    <a href="#" class="btn btn-outline-secondary mt-auto">View My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer to close the HTML structure.
require_once 'layouts/footer.php';
?>