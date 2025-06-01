<?php
require_once '../../includes/auth.php';
require_once '../../config/db.php';

if ($_SESSION['user_role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    // Notify customer
    $info = $pdo->prepare("
        SELECT o.customer_id, p.name AS product_name 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.id = ?
    ");
    $info->execute([$order_id]);
    $data = $info->fetch();

    if ($data) {
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif->execute([
            $data['customer_id'], 
            "📦 Your order for '" . $data['product_name'] . "' is now marked as $new_status."
        ]);
    }

    header("Location: orders.php");
    exit;
}

// Fetch orders
$stmt = $pdo->prepare("
    SELECT o.*, p.name AS product_name, u.name AS buyer_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN users u ON o.customer_id = u.id
    WHERE p.seller_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll();

// Total revenue
$totalRevenue = array_sum(array_column($orders, 'total_price'));

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="my_product_orders.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Product', 'Buyer', 'Total Price', 'Date', 'Status']);
    foreach ($orders as $o) {
        fputcsv($output, [
            $o['product_name'], 
            $o['buyer_name'], 
            $o['total_price'], 
            $o['order_date'], 
            $o['status']
        ]);
    }
    fputcsv($output, ['', 'Total Revenue', $totalRevenue, '', '']);
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Product Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Orders for My Products</h2>
    <a href="../seller_dashboard.php" class="btn btn-secondary mb-3">Back</a>
    <a href="?export=csv" class="btn btn-success mb-3">⬇️ Export CSV</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Buyer</th>
                <th>Total Price</th>
                <th>Date</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['product_name']) ?></td>
                <td><?= htmlspecialchars($o['buyer_name']) ?></td>
                <td>$<?= number_format($o['total_price'], 2) ?></td>
                <td><?= $o['order_date'] ?></td>
                <td><?= $o['status'] ?></td>
                <td>
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <select name="status" class="form-control form-control-sm mr-1">
                            <option <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option <?= $o['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option <?= $o['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Revenue</th>
                <th colspan="4">$<?= number_format($totalRevenue, 2) ?></th>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
