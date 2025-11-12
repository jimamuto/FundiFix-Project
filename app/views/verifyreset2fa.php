<?php
// Include the main header layout.
require_once 'layouts/header.php';

// Define baseUrl for this view
$baseUrl = "http://localhost/FundiFix-Project/public/index.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <i class="bi bi-shield-check fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Enter Verification Code</h3>
                        <p class="text-muted">Enter the 6-digit code sent to your email.</p>
                    </div>

                    <?php
                    // Display error messages from the controller
                    if (!empty($message)) {
                        echo '<div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . $message . '</div>';
                    }
                    ?>

                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="code" name="code" 
                                   placeholder="000000" maxlength="6" required>
                            <label for="code">Verification Code</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Verify Code</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?= $baseUrl ?>?action=forgotpassword">Back to Forgot Password</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the main footer layout.
require_once 'layouts/footer.php';
?>