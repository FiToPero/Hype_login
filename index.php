<?php
require_once __DIR__ . '/src/Auth.php';

$auth = new Auth();

// Redirect based on authentication status
if ($auth->isLoggedIn()) {
    header('Location: public/dashboard.php');
} else {
    header('Location: public/login.php');
}
exit(); // Important: stop execution after redirect
?>