<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Distributor - Sign Up</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <div class="logo">
                <h1>HYPE</h1>
                <p>Distributor Portal</p>
            </div>
            
            <h2>Create Account</h2>
            
            <?php
            require_once __DIR__ . '/../src/Auth.php';
            
            $auth = new Auth();
            $errors = [];
            $success = '';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // CSRF protection
                if (!$auth->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                    $errors[] = 'Invalid security token. Please try again.';
                } else {
                    $result = $auth->register(
                        $_POST['username'] ?? '',
                        $_POST['email'] ?? '',
                        $_POST['password'] ?? '',
                        $_POST['confirm_password'] ?? ''
                    );
                    
                    if ($result['success']) {
                        $success = $result['message'];
                    } else {
                        $errors = $result['errors'];
                    }
                }
            }
            
            if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <p><?php echo htmlspecialchars($success); ?></p>
                    <p><a href="login.php">Click here to login</a></p>
                </div>
            <?php else: ?>
            
            <form id="signupForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required minlength="3" maxlength="50"
                           pattern="[a-zA-Z0-9_-]+"
                           title="Username can only contain letters, numbers, underscores, and hyphens">
                    <div class="field-error" id="usernameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required maxlength="100">
                    <div class="field-error" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="field-error" id="passwordError"></div>
                    <small class="help-text">
                        Password must be at least 8 characters and contain uppercase, lowercase, number, and special character
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <div class="field-error" id="confirmPasswordError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>
            
            <?php endif; ?>
            
            <p class="auth-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
    
    <script src="../assets/js/signup-validation.js"></script>
</body>
</html>