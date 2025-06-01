<?php
require_once '../../includes/auth.php';
require_once '../../config/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$type = $_GET['type'];

if ($type === 'users') {
    $stmt = $pdo->query("SELECT id, name, email, role FROM users");
    $filename = "users.csv";
} elseif ($type === 'orders') {
    $stmt = $pdo->query("
        SELECT o.id, u.name AS buyer, p.name AS product, o.total_price, o.order_date
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        JOIN products p ON o.product_id = p.id
    ");
    $filename = "orders.csv";
} else {
    die("Invalid export type.");
}

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$filename\"");

$output = fopen('php://output', 'w');
$columns = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
fputcsv($output, $columns);
$stmt->execute(); // rewind result set
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}
fclose($output);
exit;
