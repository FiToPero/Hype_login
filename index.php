<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hype Distributor Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php
    require_once __DIR__ . '/src/Auth.php';
    
    $auth = new Auth();
    
    // Redirect based on authentication status
    if ($auth->isLoggedIn()) {
        header('Location: public/dashboard.php');
    } else {
        header('Location: public/login.php');
    }
    exit();
    ?>
</body>
</html>