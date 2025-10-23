<?php
require_once 'layouts/header.php';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Two-Factor Authentication</h3>
                        <p class="text-muted">Enter the 6-digit code from your authenticator app.</p>
                    </div>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $message; ?></div>
                    <?php endif; ?>
                    <form action="?action=verify2fa" method="POST" onsubmit="return validate2FACode();">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="2fa_code" name="2fa_code" placeholder="123456" required autofocus maxlength="6" pattern="\d{6}">
                            <label for="2fa_code">Verification Code</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Verify</button>
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
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once 'layouts/footer.php';
?>