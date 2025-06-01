<?php
require_once '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $message = 'Email already registered.';
    } else {
        // Insert into DB
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $role])) {
            $message = 'Registration successful! You can now <a href="login.php">login</a>.';
        } else {
            $message = 'Registration failed.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - E-commerce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header text-center">
            <h4>User Registration</h4>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email address:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>User Role:</label>
                    <select name="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="seller">Seller</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
