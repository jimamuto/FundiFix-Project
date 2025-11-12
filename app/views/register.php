<?php

require_once 'layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Section 1: Clear Header with Icon -->
                    <!-- This gives the user immediate context for the page's purpose. -->
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                        <h3 class="card-title mt-3">Create Your Account</h3>
                    </div>

                    <?php
                    // Display any error or info message with a corresponding icon.
                    if (!empty($message)) {
                        echo '<div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . $message . '</div>';
                    }
                    ?>

                    <!-- The form submits its data to the router with the 'register' action. -->
                    <form action="?action=register" method="POST" onsubmit="return validateRegisterForm();">
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                            <label for="name">Full Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="8">
                            <label for="password">Password</label>
                            <div class="form-text">Must be at least 8 characters long.</div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                            <label for="confirm_password">Confirm Password</label>
                        </div>

                        <!-- Role Selection Dropdown -->
                        <div class="mb-3">
                            <label for="role" class="form-label">I am a...</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled selected>-- Select your role --</option>
                                <option value="resident">Resident (Looking for a service)</option>
                                <option value="fundi">Fundi (Offering a service)</option>
                            </select>
                        </div>

                        <!-- 2FA Setup Option -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" onchange="toggle2FAMessage()">
                            <label class="form-check-label" for="enable_2fa">Enable Two-Factor Authentication (Recommended)</label>
                        </div>
                        <div id="2fa_message" class="alert alert-info d-none">
                            After registration, you'll be prompted to set up 2FA using an authenticator app.
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">Create Account</button>
                    </form>
                    
                    <script>
                    function validateRegisterForm() {
                        var email = document.getElementById('email').value;
                        var pw = document.getElementById('password').value;
                        var confirmPw = document.getElementById('confirm_password').value;
                        var re = /^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
                        
                        if (!re.test(email)) {
                            alert('Please enter a valid email address.');
                            return false;
                        }
                        if (pw.length < 8) {
                            alert('Password must be at least 8 characters long.');
                            return false;
                        }
                        if (pw !== confirmPw) {
                            alert('Passwords do not match.');
                            return false;
                        }
                        return true;
                    }
                    
                    function toggle2FAMessage() {
                        var cb = document.getElementById('enable_2fa');
                        var msg = document.getElementById('2fa_message');
                        if (cb.checked) {
                            msg.classList.remove('d-none');
                        } else {
                            msg.classList.add('d-none');
                        }
                    }
                    </script>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="text-muted">Already have an account? <a href="?action=login">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// This view requires the footer file to be included at the bottom.
require_once 'layouts/footer.php';
?>