<?php require_once 'layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Create a New Password</h3>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form action="?action=updatePasswordFromReset" method="POST" onsubmit="return validatePasswords();">
                        <!-- Hidden input to carry the token -->
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required minlength="8">
                            <label for="new_password">New Password</label>
                            <div class="form-text">Must be at least 8 characters long.</div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                            <label for="confirm_password">Confirm New Password</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Reset Password</button>
                    </form>
                    <script>
                    function validatePasswords() {
                        var pw1 = document.getElementById('new_password').value;
                        var pw2 = document.getElementById('confirm_password').value;
                        if (pw1.length < 8) {
                            alert('Password must be at least 8 characters long.');
                            return false;
                        }
                        if (pw1 !== pw2) {
                            alert('Passwords do not match.');
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

<?php require_once 'layouts/footer.php'; ?>