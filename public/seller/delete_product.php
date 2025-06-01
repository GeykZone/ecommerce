<?php
require_once '../../includes/auth.php';
require_once '../../config/db.php';

if ($_SESSION['user_role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

$product_id = $_GET['id'];
$seller_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
$stmt->execute([$product_id, $seller_id]);

header("Location: ../seller_dashboard.php");
exit;
