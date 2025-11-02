<?php

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    private $maxFailedAttempts = 5;
    private $lockoutTime = 900; // 15 minutes in seconds
    private $sessionLifetime = 3600; // 1 hour in seconds
    
    public function __construct() {
        $this->db = new Database();
        $this->startSecureSession();
    }
    
    private function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Only set session configuration if headers haven't been sent yet
            if (!headers_sent()) {
                // Secure session configuration
                ini_set('session.cookie_httponly', 1);
                ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
                ini_set('session.use_strict_mode', 1);
                ini_set('session.cookie_samesite', 'Strict');
            }
            
            session_start();
            
            // Regenerate session ID periodically for security
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
    
    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public function register($username, $email, $password, $confirmPassword) {
        $errors = [];
        
        // Input validation
        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Username must be between 3 and 50 characters";
        }
        
        // Validate username format (only letters, numbers, underscores, and hyphens)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors[] = "Username can only contain letters, numbers, underscores, and hyphens";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            $errors[] = "Please enter a valid email address";
        }
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if username or email already exists
        $existing = $this->db->fetch(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existing) {
            return ['success' => false, 'errors' => ['Username or email already exists']];
        }
        
        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        try {
            $this->db->query(
                "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
                [$username, $email, $hashedPassword]
            );
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
        }
    }
    
    public function login($username, $password) {
        $ipAddress = $this->getClientIP();
        
        // Check rate limiting
        if ($this->isRateLimited($ipAddress)) {
            $this->logLoginAttempt($ipAddress, $username, false);
            return ['success' => false, 'error' => 'Too many failed attempts. Please try again later.'];
        }
        
        // Get user
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE username = ? AND is_active = 1",
            [$username]
        );
        
        if (!$user) {
            $this->logLoginAttempt($ipAddress, $username, false);
            return ['success' => false, 'error' => 'Invalid username or password'];
        }
        
        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $this->logLoginAttempt($ipAddress, $username, false);
            return ['success' => false, 'error' => 'Account temporarily locked due to failed login attempts'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->handleFailedLogin($user['id'], $ipAddress, $username);
            return ['success' => false, 'error' => 'Invalid username or password'];
        }
        
        // Password is correct - reset failed attempts and create session
        $this->db->query(
            "UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?",
            [$user['id']]
        );
        
        $this->logLoginAttempt($ipAddress, $username, true);
        $this->createUserSession($user);
        
        return ['success' => true, 'user' => $user];
    }
    
    private function handleFailedLogin($userId, $ipAddress, $username) {
        // Increment failed attempts
        $this->db->query(
            "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = ?",
            [$userId]
        );
        
        // Check if we should lock the account
        $user = $this->db->fetch("SELECT failed_login_attempts FROM users WHERE id = ?", [$userId]);
        
        if ($user['failed_login_attempts'] >= $this->maxFailedAttempts) {
            $lockUntil = date('Y-m-d H:i:s', time() + $this->lockoutTime);
            $this->db->query(
                "UPDATE users SET locked_until = ? WHERE id = ?",
                [$lockUntil, $userId]
            );
        }
        
        $this->logLoginAttempt($ipAddress, $username, false);
    }
    
    private function isRateLimited($ipAddress) {
        // Check failed attempts in last 15 minutes
        $attempts = $this->db->fetch(
            "SELECT COUNT(*) as count FROM login_attempts 
             WHERE ip_address = ? AND success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [$ipAddress]
        );
        
        return $attempts['count'] >= 10; // Max 10 failed attempts per IP in 15 minutes
    }
    
    private function logLoginAttempt($ipAddress, $username, $success) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $this->db->query(
            "INSERT INTO login_attempts (ip_address, username, success, user_agent) VALUES (?, ?, ?, ?)",
            [$ipAddress, $username, $success ? 1 : 0, $userAgent]
        );
    }
    
    private function createUserSession($user) {
        $sessionId = bin2hex(random_bytes(32));
        $ipAddress = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $expiresAt = date('Y-m-d H:i:s', time() + $this->sessionLifetime);
        
        // Store session in database
        $this->db->query(
            "INSERT INTO sessions (id, user_id, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)",
            [$sessionId, $user['id'], $ipAddress, $userAgent, $expiresAt]
        );
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['session_id'] = $sessionId;
        $_SESSION['login_time'] = time();
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_id'])) {
            return false;
        }
        
        // Validate session in database
        $session = $this->db->fetch(
            "SELECT * FROM sessions WHERE id = ? AND user_id = ? AND is_active = 1 AND expires_at > NOW()",
            [$_SESSION['session_id'], $_SESSION['user_id']]
        );
        
        if (!$session) {
            $this->logout();
            return false;
        }
        
        // Update session activity
        $this->db->query(
            "UPDATE sessions SET last_activity = NOW() WHERE id = ?",
            [$_SESSION['session_id']]
        );
        
        return true;
    }
    
    public function logout() {
        if (isset($_SESSION['session_id'])) {
            // Deactivate session in database
            $this->db->query(
                "UPDATE sessions SET is_active = 0 WHERE id = ?",
                [$_SESSION['session_id']]
            );
        }
        
        // Clear session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT id, username, email, created_at, last_login FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }
    
    public function getAllUsers() {
        return $this->db->fetchAll(
            "SELECT id, username, email, created_at, last_login FROM users WHERE is_active = 1 ORDER BY created_at DESC"
        );
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                   'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}