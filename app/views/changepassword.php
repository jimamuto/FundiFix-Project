<?php
// If the user is not logged in, they are immediately redirected to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: /FundiApp/public/?action=login");
    exit();
}

// Load the header
require_once 'layouts/header.php';
?>

<div class="container py-5" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="card-title fw-bold mb-4">Change Password</h1>

            <!-- This will display any error or success messages from the controller -->
            <?php if (isset($message)) : ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="?action=updatePassword" method="POST">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                     <div class="form-text">Password must be at least 8 characters long.</div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="?action=editProfile" class="text-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Load the footer
require_once 'layouts/footer.php';
?>