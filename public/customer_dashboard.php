<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Make sure only customers can access this
if ($_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Handle search and price filter input
$search = $_GET['search'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Build WHERE conditions dynamically
$where = ["p.stock > 0"];
$params = [];

if (!empty($search)) {
    $where[] = "p.name LIKE ?";
    $params[] = "%$search%";
}

if ($min_price !== '') {
    $where[] = "p.price >= ?";
    $params[] = $min_price;
}

if ($max_price !== '') {
    $where[] = "p.price <= ?";
    $params[] = $max_price;
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Query with optional filters
$stmt = $pdo->prepare("
    SELECT p.*, u.name AS seller_name 
    FROM products p 
    JOIN users u ON p.seller_id = u.id 
    $where_sql
    ORDER BY p.id DESC
");
$stmt->execute($params);
$products = $stmt->fetchAll();

// $notif_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
// $notif_stmt->execute([$_SESSION['user_id']]);
// $notif_count = $notif_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
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
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> (Customer)</h2>
    <a href="profile.php" class="btn btn-outline-primary mb-3">👤 My Profile</a>
    <a href="notifications.php" class="btn btn-outline-primary position-relative mb-3">
        🔔 Notifications
        <span id="notifBadge" class="badge badge-danger position-absolute" style="top: 0; right: -10px; display: none;">!</span>
    </a>


    <p><a href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>

    <h4>Available Products</h4>

    <!-- Filter Form -->
    <form method="GET" class="form-inline mb-4">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>" class="form-control mr-2">
        <input type="number" step="0.01" name="min_price" placeholder="Min Price" value="<?= htmlspecialchars($min_price) ?>" class="form-control mr-2">
        <input type="number" step="0.01" name="max_price" placeholder="Max Price" value="<?= htmlspecialchars($max_price) ?>" class="form-control mr-2">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="customer_dashboard.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <!-- Product List -->
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 product-card">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <?php if (!empty($p['image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" alt="Product Image" class="img-fluid mb-2">
                            <?php else: ?>
                                <img src="../assets/default-product.png" alt="No Image" class="img-fluid mb-2">
                            <?php endif; ?>

                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($p['description']) ?></p>
                            <p class="text-muted">Seller: <?= htmlspecialchars($p['seller_name']) ?></p>
                            <strong>$<?= number_format($p['price'], 2) ?></strong><br>
                            <small>Stock: <?= $p['stock'] ?></small><br>
                            <a href="buy_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-success mt-2">Buy Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No products found based on your search/filter.</p>
            </div>
        <?php endif; ?>
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
