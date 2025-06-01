<?php
require_once '../../includes/auth.php';
require_once '../../config/db.php';

// Ensure seller access
if ($_SESSION['user_role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

$product_id = $_GET['id'] ?? null;
$seller_id = $_SESSION['user_id'];

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->execute([$product_id, $seller_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found or unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    // Handle image upload
    $currentImage = $product['image'];
    $newImage = $currentImage;

    if (!empty($_FILES['image']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = '../../uploads/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $newImage = $filename;

            // Optional: delete old image
            if ($currentImage && file_exists('../../uploads/' . $currentImage)) {
                unlink('../../uploads/' . $currentImage);
            }
        }
    }

    // Update the product
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ? AND seller_id = ?");
    $stmt->execute([$name, $desc, $price, $stock, $newImage, $product_id, $seller_id]);

    header("Location: ../seller_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Product</h2>
    <a href="../seller_dashboard.php" class="btn btn-secondary mb-3">Back</a>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Current Image:</label><br>
            <?php if ($product['image']): ?>
                <img src="../../uploads/<?= $product['image'] ?>" alt="Product Image" style="max-height: 150px;"><br>
            <?php else: ?>
                <em>No image uploaded</em><br>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Change Image (optional):</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
</body>
</html>
