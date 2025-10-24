<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'layouts/header.php';

// Get email from URL parameter or form
$email = $_GET['email'] ?? ($_POST['email'] ?? '');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">
                            Two-Factor Authentication
                        </h3>
                        <p class="text-muted">
                            Enter the 6-digit code sent to your email to complete your login.
                        </p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- 2FA Login Form -->
                    <form action="http://localhost/FundiFix-Project/public/index.php?action=verify2fa" method="POST" onsubmit="return validateCode();">
                        
                        <!-- Hidden email field to persist it -->
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        
                        <div class="form-floating mb-3">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="code" 
                                name="code" 
                                placeholder="123456" 
                                required 
                                autofocus 
                                maxlength="6" 
                                pattern="\d{6}"
                            >
                            <label for="code">Verification Code</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            Verify & Login
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-muted">
                            Didn't receive the code? 
                            <a href="http://localhost/FundiFix-Project/public/index.php?action=login">
                                Try again
                            </a>
                        </p>
                    </div>

                    <script>
                        function validateCode() {
                            const code = document.getElementById('code').value.trim();
                            if (!/^\d{6}$/.test(code)) {
                                alert('Please enter a valid 6-digit code.');
                                return false;
                            }
                            return true;
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'layouts/footer.php';
?>