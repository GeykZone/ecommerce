<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

// Mark all notifications as read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);

// Get all notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Notifications</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h2>🔔 My Notifications</h2>
  <a href="<?= $_SESSION['user_role'] ?>_dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>
  <ul class="list-group">
    <?php foreach ($notifications as $n): ?>
      <li class="list-group-item">
        <?= htmlspecialchars($n['message']) ?><br>
        <small class="text-muted"><?= $n['created_at'] ?></small>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
</body>
</html>
