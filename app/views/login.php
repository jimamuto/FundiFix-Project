<?php
// This view requires the header file to be included.
require_once 'layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">

                    <?php if (isset($show_2fa_form) && $show_2fa_form === true): ?>
                        
                        <!-- --- 2FA VERIFICATION FORM --- -->
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                            <h3 class="card-title mt-3">Two-Factor Authentication</h3>
                            <p class="text-muted">Open your authenticator app and enter the 6-digit code.</p>
                        </div>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i> <?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="?action=login" method="POST" onsubmit="return validate2FACode();">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="2fa_code" name="2fa_code" placeholder="123456" required autofocus maxlength="6" pattern="\d{6}">
                                <label for="2fa_code">Verification Code</label>
                            </div>
                            <button type="submit" name="verify_2fa" class="btn btn-primary w-100 btn-lg">Verify & Log In</button>
                        </form>
                        <script>
                        function validate2FACode() {
                            var code = document.getElementById('2fa_code').value;
                            if (!/^\d{6}$/.test(code)) {
                                alert('Please enter a valid 6-digit code.');
                                return false;
                            }
                            return true;
                        }
                        </script>

                    <?php else: ?>

                        <!-- --- STANDARD LOGIN FORM --- -->
                        <div class="text-center mb-4">
                            <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
                            <h3 class="card-title mt-3">Welcome Back!</h3>
                        </div>

                        <?php
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['message'] . '</div>';
                            unset($_SESSION['message']);
                        }
                        if (!empty($message)) {
                            echo '<div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . $message . '</div>';
                        }
                        ?>

                        <form action="?action=login" method="POST">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                <label for="email">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100 btn-lg">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="#">Forgot Password?</a>
                        </div>
                    
                    <?php endif; ?>

                </div>
            </div>
            <div class="text-center mt-3">
                <p class="text-muted">Don't have an account? <a href="?action=register">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// This view requires the footer file to be included.
require_once 'layouts/footer.php';
?>