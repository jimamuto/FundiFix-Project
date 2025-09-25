<?php require_once 'layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Manage Your Profile</h3>
                    </div>
                    <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if (!empty($message)) {
                        echo '<div class="alert alert-danger">' . $message . '</div>';
                    }
                    ?>
                    <form action="?action=updateProfile" method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Full Name"
                                   value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                            <label for="name">Full Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Email"
                                   value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                            <label for="email">Email address (cannot be changed)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Save Changes</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="?action=dashboard">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>