<?php
session_start();
require_once '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } elseif ($user['role'] === 'seller') {
            header('Location: seller_dashboard.php');
        } else {
            header('Location: customer_dashboard.php');
        }
        exit;
    } else {
        $message = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - E-commerce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header text-center">
            <h4>User Login</h4>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Email address:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
