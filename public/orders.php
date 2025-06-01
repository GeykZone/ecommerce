<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.customer_id = ? ORDER BY o.order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Order History</h2>
    <a href="<?= $_SESSION['user_role'] ?>_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['product_name']) ?></td>
                <td>$<?= $o['total_price'] ?></td>
                <td><?= $o['order_date'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
