<?php
// Set page title
$page_title = "MusikplayerWebshop - Register";

// Include header
include_once 'views/header.php';
?>

<div class="auth-container" id="registerFormContainer">
    <h2 class="auth-title">Create an Account</h2>
    
    <div id="registerAlert" class="alert alert-danger" style="display: none;"></div>
    
    <form id="registerForm">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <select id="title" name="title" class="form-control" required>
                        <option value="">Select Title</option>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" id="firstName" name="first_name" class="form-control" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" id="lastName" name="last_name" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address" class="form-control" required>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="postalCode" class="form-label">Postal Code</label>
                    <input type="text" id="postalCode" name="postal_code" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
    
    <div class="auth-footer">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<?php
// Include footer
include_once 'views/footer.php';
?> 