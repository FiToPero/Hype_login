<?php
require_once __DIR__ . '/../src/Auth.php';

$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$currentUser = $auth->getCurrentUser();
$allUsers = $auth->getAllUsers();

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header('Location: login.php?message=logged_out');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Distributor - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    
    <div class="dashboard">
        <nav class="navbar">
            <div class="navbar-brand">
                <h1>HYPE</h1>
                <span>Distributor Portal</span>
            </div>
            
            <div class="navbar-user">
                <div class="user-info">
                    <span class="welcome">Welcome, <?php echo htmlspecialchars($currentUser['username']); ?>!</span>
                    <span class="user-email"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
                <a href="?logout=1" class="btn btn-logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="dashboard-header">
                <h2>🎉 Login Successful!</h2>
                <p class="success-message">
                    You have successfully logged into the Hype Distributor portal. 
                    Welcome to your secure dashboard where you can view registered distributors.
                </p>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo count($allUsers); ?></h3>
                        <p>Total Distributors</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🔐</div>
                    <div class="stat-info">
                        <h3>Secure</h3>
                        <p>Login System</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">⏰</div>
                    <div class="stat-info">
                        <h3><?php echo date('H:i'); ?></h3>
                        <p>Current Time</p>
                    </div>
                </div>
            </div>
            
            <div class="users-section">
                <div class="section-header">
                    <h3>📋 Registered Distributors</h3>
                    <p>Complete list of all registered distributor accounts</p>
                </div>
                
                <?php if (empty($allUsers)): ?>
                    <div class="no-users">
                        <div class="no-users-icon">👤</div>
                        <h4>No distributors registered yet</h4>
                        <p>You are the first distributor to join the portal!</p>
                        <a href="signup.php" class="btn btn-primary">Invite Others</a>
                    </div>
                <?php else: ?>
                    <div class="users-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registration Date</th>
                                    <th>Last Login</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allUsers as $user): ?>
                                <tr class="<?php echo $user['id'] == $currentUser['id'] ? 'current-user' : ''; ?>">
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td>
                                        <div class="user-cell">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                            <?php if ($user['id'] == $currentUser['id']): ?>
                                                <span class="badge badge-primary">You</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if ($user['last_login']): ?>
                                            <?php 
                                            $lastLogin = strtotime($user['last_login']);
                                            $now = time();
                                            $diff = $now - $lastLogin;
                                            
                                            if ($diff < 3600) {
                                                echo floor($diff / 60) . ' min ago';
                                            } elseif ($diff < 86400) {
                                                echo floor($diff / 3600) . ' hours ago';
                                            } else {
                                                echo date('M j, Y', $lastLogin);
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="security-info-dashboard">
                <h4>🔒 Security Features Active</h4>
                <div class="security-features">
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>Secure Session Management</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>CSRF Protection</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>Password Hashing (Argon2ID)</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>Rate Limiting</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>Login Attempt Monitoring</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">✅</span>
                        <span>Account Lockout Protection</span>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> Hype Distributor Portal. All rights reserved.</p>
            <p>
                Session started: <?php echo date('M j, Y H:i:s', $_SESSION['login_time'] ?? time()); ?> | 
                Your IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown'); ?>
            </p>
        </footer>
    </div>
    
    <style>
        .dashboard {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand h1 {
            font-size: 2rem;
            margin: 0;
            font-weight: bold;
        }
        
        .navbar-brand span {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .welcome {
            display: block;
            font-weight: 500;
        }
        
        .user-email {
            display: block;
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .btn-logout {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .btn-logout:hover {
            background-color: rgba(255,255,255,0.3);
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .dashboard-header h2 {
            color: #28a745;
            margin-bottom: 0.5rem;
        }
        
        .success-message {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            font-size: 2.5rem;
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 1.8rem;
            color: #495057;
        }
        
        .stat-info p {
            margin: 0;
            color: #6c757d;
        }
        
        .users-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .section-header {
            margin-bottom: 1.5rem;
        }
        
        .section-header h3 {
            margin: 0 0 0.5rem 0;
            color: #495057;
        }
        
        .users-table-container {
            overflow-x: auto;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .users-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .users-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .current-user {
            background-color: #e3f2fd !important;
        }
        
        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .no-users {
            text-align: center;
            padding: 3rem;
        }
        
        .no-users-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .security-info-dashboard {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .security-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
        }
        
        .feature-icon {
            color: #28a745;
        }
        
        .dashboard-footer {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>