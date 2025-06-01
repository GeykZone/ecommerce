<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Analytics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $pdo->query("SELECT SUM(total_price) FROM orders")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <img src="../uploads/<?= $_SESSION['profile_pic'] ?? 'default-avatar.png' ?>" width="40" height="40" class="rounded-circle mr-2">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (Admin)</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text"><?= $totalProducts ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text"><?= $totalOrders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">$<?= $totalSales ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4>Exports</h4>
    <a href="admin/export_csv.php?type=users" class="btn btn-primary mb-2">📥 Export Users CSV</a>
    <a href="admin/export_csv.php?type=orders" class="btn btn-warning mb-2">📥 Export Orders CSV</a>

    <hr>

    <h4>Navigation</h4>
    <!-- Add any additional links here -->
    <!-- Example: <a href="manage_users.php" class="btn btn-outline-dark">Manage Users</a> -->
    <a href="profile.php" class="btn btn-outline-primary">👤 My Profile</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
