<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];
$customer_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product || $product['stock'] < 1) {
    die("Product not available.");
}

$total_price = $product['price'];

// 1. Create order
$order = $pdo->prepare("INSERT INTO orders (customer_id, product_id, total_price) VALUES (?, ?, ?)");
$order->execute([$customer_id, $product_id, $total_price]);

// Add this after successfully placing an order
$notif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$notif->execute([$product['seller_id'], "📦 New order placed for your product: " . $product['name']]);


// 2. Reduce stock
$updateStock = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
$updateStock->execute([$product_id]);

header("Location: customer_dashboard.php?msg=success");
exit;
