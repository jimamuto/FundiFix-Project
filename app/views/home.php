<?php
// We start by including the header, which should now have the Bootstrap Icons link.
require_once 'layouts/header.php';
?>


<div class="container-fluid px-0">
    <div class="bg-dark text-white text-center py-5">
        <div class="container py-5">
            <!-- Increased the heading size for more impact. -->
            <h1 class="display-4 fw-bold">Reliable Artisans, Right at Your Doorstep</h1>
            <!-- Added 'lead' class for a softer, more elegant paragraph style. -->
            <p class="lead col-lg-8 mx-auto mb-4">
                Find, book, and review the best local fundis in Nairobi. Quick, safe, and hassle-free.
            </p>

            <a class="btn btn-success btn-lg" href="?action=register" role="button">
                <i class="bi bi-person-plus-fill me-2"></i>Get Started Today
            </a>
        </div>
    </div>
</div>

<!-- Section 2: The "Features" Section -->

<div class="container py-5">
    <div class="row text-center">

        <!-- Feature 1: Find a Fundi -->
      
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 p-4">
               
                <div class="mb-3">
                    <i class="bi bi-search fs-1 text-primary"></i>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Find a Fundi</h3>
                    
                    <p class="card-text text-muted">Browse profiles of vetted artisans in your area.</p>
                </div>
            </div>
        </div>

        <!-- Feature 2: Book a Service -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 p-4">
                <div class="mb-3">
                    <i class="bi bi-calendar-check fs-1 text-primary"></i>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Book a Service</h3>
                    <p class="card-text text-muted">Request a service with just a few clicks.</p>
                </div>
            </div>
        </div>

        <!-- Feature 3: Get the Job Done -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 p-4">
                <div class="mb-3">
                    <i class="bi bi-shield-check fs-1 text-primary"></i>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Get the Job Done</h3>
                    <p class="card-text text-muted">Enjoy quality work from trusted professionals.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
// Include the footer to close the HTML page.
require_once 'layouts/footer.php';
?>