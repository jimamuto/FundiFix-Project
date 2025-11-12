<?php
require_once 'layouts/header.php';
?>

<!-- Hero Section - Professional & Clean -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50 py-lg-5">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-4">
                    Professional Home Services
                    <span class="text-warning">On Demand</span>
                </h1>
                
                <p class="lead mb-4 opacity-75">
                    Connect with verified artisans for all your home repair and maintenance needs. 
                    Quality service, transparent pricing, and guaranteed satisfaction.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="?action=register" class="btn btn-success btn-lg px-4 py-3 fw-semibold">
                        <i class="bi bi-tools me-2"></i>Get Started
                    </a>
                    <a href="?action=services_available" class="btn btn-outline-light btn-lg px-4 py-3">
                        Browse Services
                    </a>
                </div>

                <div class="mt-4 pt-3">
                    <div class="d-flex align-items-center text-sm opacity-75">
                        <div class="d-flex me-3">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <?php endfor; ?>
                        </div>
                        <span>Rated 4.8/5 by 2,000+ clients</span>
                    </div>
                </div>
            </div>
            
            <!-- Professional Stats Visualization -->
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <div class="position-relative">
                    <div class="bg-white rounded-3 p-4 shadow-lg border">
                        <div class="row g-3 text-dark">
                            <div class="col-6">
                                <div class="p-3 border rounded-2 bg-success bg-opacity-10">
                                    <i class="bi bi-person-check fs-2 text-success mb-2 d-block"></i>
                                    <h4 class="fw-bold text-success mb-1">500+</h4>
                                    <small class="text-muted">Verified Fundis</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-2 bg-primary bg-opacity-10">
                                    <i class="bi bi-briefcase fs-2 text-primary mb-2 d-block"></i>
                                    <h4 class="fw-bold text-primary mb-1">2K+</h4>
                                    <small class="text-muted">Jobs Completed</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-2 bg-warning bg-opacity-10">
                                    <i class="bi bi-lightning fs-2 text-warning mb-2 d-block"></i>
                                    <h4 class="fw-bold text-warning mb-1">30min</h4>
                                    <small class="text-muted">Avg Response Time</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-2 bg-info bg-opacity-10">
                                    <i class="bi bi-shield-check fs-2 text-info mb-2 d-block"></i>
                                    <h4 class="fw-bold text-info mb-1">100%</h4>
                                    <small class="text-muted">Satisfaction Guarantee</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating badge -->
                    <div class="position-absolute top-0 start-0 bg-warning text-dark px-3 py-2 rounded-pill small fw-bold shadow-sm">
                        ⚡ Instant Matching
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 text-muted fw-normal">Our Services</h2>
            <h3 class="display-6 fw-bold mb-3">Professional Solutions for Every Need</h3>
            <p class="text-muted lead">Comprehensive home maintenance and repair services</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="service-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 70px; height: 70px;">
                            <i class="bi bi-droplet fs-3 text-primary"></i>
                        </div>
                        <h6 class="fw-bold">Plumbing</h6>
                        <small class="text-muted">Leaks, installations, repairs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="service-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 70px; height: 70px;">
                            <i class="bi bi-lightning-charge fs-3 text-success"></i>
                        </div>
                        <h6 class="fw-bold">Electrical</h6>
                        <small class="text-muted">Wiring, fixtures, repairs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="service-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 70px; height: 70px;">
                            <i class="bi bi-hammer fs-3 text-warning"></i>
                        </div>
                        <h6 class="fw-bold">Carpentry</h6>
                        <small class="text-muted">Furniture, fixtures, repairs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="service-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 70px; height: 70px;">
                            <i class="bi bi-paint-bucket fs-3 text-info"></i>
                        </div>
                        <h6 class="fw-bold">Painting</h6>
                        <small class="text-muted">Walls, interiors, exteriors</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="display-6 fw-bold mb-3">How FundiFix Works</h3>
            <p class="text-muted">Simple process, professional results</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-5">1</span>
                        </div>
                        <h5 class="fw-bold mb-3">Describe Your Need</h5>
                        <p class="text-muted mb-0">
                            Tell us what service you need and when you need it. We'll match you with qualified professionals.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-5">2</span>
                        </div>
                        <h5 class="fw-bold mb-3">Compare & Choose</h5>
                        <p class="text-muted mb-0">
                            Review profiles, ratings, and quotes from available fundis. Select the best fit for your project.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 h-100 text-center hover-shadow">
                    <div class="card-body p-4">
                        <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <h5 class="fw-bold mb-3">Get It Done</h5>
                        <p class="text-muted mb-0">
                            Your fundi arrives on time, completes the job professionally, and you pay only when satisfied.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section - -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="display-6 fw-bold mb-3">Client Testimonials</h3>
            <p class="text-muted">What our customers say about our service</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="client-avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person fs-5 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Sarah M.</h6>
                                <small class="text-muted">Kileleshwa • Plumbing</small>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-muted mb-0">
                            "Professional service with quick response. The plumber fixed our kitchen sink efficiently and explained everything clearly."
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="client-avatar bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person fs-5 text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">John K.</h6>
                                <small class="text-muted">Westlands • Electrical</small>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-muted mb-0">
                            "Excellent electrical work. The technician was knowledgeable, professional, and completed the job ahead of schedule."
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="client-avatar bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-person fs-5 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Grace W.</h6>
                                <small class="text-muted">Kilimani • Carpentry</small>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-muted mb-0">
                            "Reliable and skilled carpenter. Delivered exactly what was promised with great attention to detail."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Ready to Get Started?</h2>
                <p class="lead mb-4 opacity-75">
                    Join thousands of satisfied customers who trust FundiFix for their home service needs. 
                    Professional, reliable, and guaranteed.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="?action=register" class="btn btn-success btn-lg px-4 py-3 fw-semibold">
                        <i class="bi bi-rocket-takeoff me-2"></i>Start Your Project
                    </a>
                    <a href="?action=services_available" class="btn btn-outline-light btn-lg px-4 py-3">
                        View Service Catalog
                    </a>
                </div>
                <p class="small text-muted mt-3">No upfront costs • Verified professionals • Quality guarantee</p>
            </div>
        </div>
    </div>
</section>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
.min-vh-50 {
    min-height: 60vh;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
}
.step-number {
    font-size: 1.25rem;
}
.service-icon, .client-avatar {
    transition: all 0.3s ease;
}
.card:hover .service-icon,
.card:hover .client-avatar {
    transform: scale(1.1);
}
</style>

<?php
require_once 'layouts/footer.php';
?>