<?php
// Set page title
$page_title = "MusikplayerWebshop - Login";

// Include header
include_once 'views/header.php';
?>

<div class="auth-container">
    <h2 class="auth-title">Login</h2>
    
    <div id="loginAlert" class="alert alert-danger" style="display: none;"></div>
    
    <form id="loginForm">
        <div class="form-group">
            <label for="username" class="form-label">Username or Email</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-check">
            <input type="checkbox" id="rememberMe" name="remember_me" class="form-check-input">
            <label for="rememberMe" class="form-check-label">Remember me</label>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    
    <div class="auth-footer">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

<?php
// Include footer
include_once 'views/footer.php';
?> 