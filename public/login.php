<?php
require_once __DIR__ . '/../src/Auth.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $result = $auth->login(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Distributor - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <div class="logo">
                <h1>HYPE</h1>
                <p>Distributor Portal</p>
            </div>
            
            <h2>Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <?php if (strpos($error, 'Too many failed attempts') !== false): ?>
                        <p><a href="error.php">Learn more about account security</a></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required maxlength="50" autocomplete="username">
                    <div class="field-error" id="usernameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           required autocomplete="current-password">
                    <div class="field-error" id="passwordError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="auth-link">
                Don't have an account? <a href="signup.php">Sign up here</a>
            </p>
            
            <div class="security-info">
                <small>
                    <strong>Security Notice:</strong> Your account will be temporarily locked after 5 failed login attempts.
                    We monitor all login attempts for security purposes.
                </small>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/login-validation.js"></script>
</body>
</html>