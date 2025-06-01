<?php
require_once '../../includes/auth.php';
require_once '../../config/db.php';

if ($_SESSION['user_role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // 🔽 🔽 INSERT THIS BLOCK HERE (Handle upload)
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = '../../uploads/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $filename;
        }
    }

    // ✅ Then insert the product including the image
    $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $name, $desc, $price, $stock, $image]);

    header("Location: ../seller_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Add New Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="../seller_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
