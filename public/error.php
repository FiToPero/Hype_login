<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Distributor - Login Error</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="error-page">
            <div class="logo">
                <h1>HYPE</h1>
                <p>Distributor Portal</p>
            </div>
            
            <div class="error-content">
                <h2>Login Error</h2>
                
                <div class="alert alert-error">
                    <h3>Access Denied</h3>
                    <p>We encountered an issue with your login attempt. This could be due to:</p>
                    
                    <ul class="error-list">
                        <li><strong>Invalid credentials:</strong> Please check your username and password</li>
                        <li><strong>Account lockout:</strong> Your account may be temporarily locked due to multiple failed login attempts</li>
                        <li><strong>Rate limiting:</strong> Too many login attempts from your IP address</li>
                        <li><strong>Inactive account:</strong> Your account may have been deactivated</li>
                    </ul>
                </div>
                
                <div class="security-measures">
                    <h3>Security Measures in Place</h3>
                    <div class="security-grid">
                        <div class="security-item">
                            <strong>Account Lockout:</strong>
                            <p>After 5 failed login attempts, accounts are locked for 15 minutes</p>
                        </div>
                        <div class="security-item">
                            <strong>Rate Limiting:</strong>
                            <p>IP addresses are limited to 10 failed attempts per 15 minutes</p>
                        </div>
                        <div class="security-item">
                            <strong>Session Security:</strong>
                            <p>All sessions use secure cookies and CSRF protection</p>
                        </div>
                        <div class="security-item">
                            <strong>Login Monitoring:</strong>
                            <p>All login attempts are logged and monitored for security</p>
                        </div>
                    </div>
                </div>
                
                <div class="recovery-options">
                    <h3>What can you do?</h3>
                    <div class="options-list">
                        <div class="option">
                            <strong>Wait and retry:</strong>
                            <p>If your account is locked, wait 15 minutes and try again with the correct credentials.</p>
                        </div>
                        <div class="option">
                            <strong>Check your credentials:</strong>
                            <p>Make sure you're using the correct username and password. Remember that passwords are case-sensitive.</p>
                        </div>
                        <div class="option">
                            <strong>Create a new account:</strong>
                            <p>If you don't have an account yet, you can sign up for distributor access.</p>
                        </div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="login.php" class="btn btn-primary">Try Login Again</a>
                    <a href="signup.php" class="btn btn-secondary">Create Account</a>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .error-page {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .error-content {
            text-align: center;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .error-list {
            text-align: left;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .error-list li {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            border-left: 3px solid #e74c3c;
            background-color: #fdf2f2;
        }
        
        .security-measures {
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .security-item {
            padding: 1rem;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: left;
        }
        
        .recovery-options {
            margin: 2rem 0;
            text-align: left;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .option {
            margin-bottom: 1rem;
            padding: 1rem;
            border-left: 4px solid #3498db;
            background-color: #f8f9ff;
        }
        
        .action-buttons {
            margin: 2rem 0;
        }
        
        .action-buttons .btn {
            margin: 0 0.5rem;
        }
        
        .contact-info {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: #e8f5e8;
            border-radius: 8px;
        }
        
        .contact-info ul {
            list-style: none;
            padding: 0;
        }
        
        .contact-info li {
            margin: 0.5rem 0;
        }
    </style>
</body>
</html>