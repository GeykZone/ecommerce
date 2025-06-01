<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['count' => 0]);
    exit;
}

// Count unread notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

echo json_encode(['count' => $count]);
