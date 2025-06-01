<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Restrict access to sellers only
if ($_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

// Fetch products by this seller
$seller_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
$stmt->execute([$seller_id]);
$products = $stmt->fetchAll();

// $notif_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
// $notif_stmt->execute([$_SESSION['user_id']]);
// $notif_count = $notif_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .product-card img {
            max-height: 150px;
            object-fit: contain;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <img src="../uploads/<?= $_SESSION['profile_pic'] ?? 'default-avatar.png' ?>" width="40" height="40" class="rounded-circle mr-2">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (Seller)</h2>
    <a href="profile.php" class="btn btn-outline-primary mb-3">👤 My Profile</a>
    <a href="notifications.php" class="btn btn-outline-primary position-relative mb-3">
        🔔 Notifications
        <span id="notifBadge" class="badge badge-danger position-absolute" style="top: 0; right: -10px; display: none;">!</span>
    </a>
    <p><a href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>
    <a href="seller/add_product.php" class="btn btn-primary mb-3">➕ Add New Product</a>
    <a href="seller/orders.php" class="btn btn-secondary mb-3">📦 View Orders</a>

    <div class="row">
        <?php foreach ($products as $p): ?>
            <div class="col-md-4 product-card">
                <div class="card mb-4">
                    <div class="card-body text-center">
                    <?php if (!empty($p['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" alt="Product Image" class="img-fluid mb-2" style="max-height: 150px;">
                    <?php else: ?>
                        <img src="../assets/default-product.png" alt="No Image" class="img-fluid mb-2" style="max-height: 150px;">
                    <?php endif; ?>


                        <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($p['description']) ?></p>
                        <strong>$<?= number_format($p['price'], 2) ?></strong><br>
                        <small>Stock: <?= $p['stock'] ?></small><br>

                        <a href="seller/edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning mt-2">✏️ Edit</a>
                        <a href="seller/delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script>
function checkNotifications() {
    fetch('push_poll.php')
        .then(response => response.json())
        .then(data => {
            console.log('Unread notifications:', data.count); // Debug output
            const badge = document.getElementById('notifBadge');
            if (data.count > 0) {
                badge.style.display = 'inline-block';
                badge.textContent = data.count > 9 ? '9+' : data.count;
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(err => console.error('Error fetching notifications:', err));
}

checkNotifications(); // initial load
setInterval(checkNotifications, 30000); // check every 30 seconds
</script>
</body>
</html>
