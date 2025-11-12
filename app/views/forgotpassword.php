<?php
// Include the main header layout.
require_once 'layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <i class="bi bi-key-fill fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Forgot Your Password?</h3>
                        <p class="text-muted">No problem. Enter your email address below and we'll send you a link to reset it.</p>
                    </div>

                    <?php
                    // Display any feedback messages from the controller.
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i>' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']); // Clear the message after displaying it.
                    }
                    ?>

                    <!-- The form posts the email to the 'sendResetLink' action. -->
                    <form action="?action=sendResetLink" method="POST" onsubmit="return validateEmail();">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Send Reset Link</button>
                    </form>
                    <script>
                    function validateEmail() {
                        var email = document.getElementById('email').value;
                        var re = /^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
                        if (!re.test(email)) {
                            alert('Please enter a valid email address.');
                            return false;
                        }
                        return true;
                    }
                    </script>

                    <div class="text-center mt-3">
                        <a href="?action=login">Back to Login</a>
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