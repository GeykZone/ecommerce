<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact_number'];
    $address = $_POST['address'];

    // Handle profile picture upload
    $profile_pic = $_SESSION['profile_pic'] ?? null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/' . $filename);
        $profile_pic = $filename;
    }

    $sql = "UPDATE users SET contact_number = ?, address = ?, profile_pic = ? WHERE id = ?";
    $update = $pdo->prepare($sql);
    $update->execute([$contact, $address, $profile_pic, $user_id]);

    // Update session
    $_SESSION['profile_pic'] = $profile_pic;

    header("Location: profile.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>My Profile</h2>
    <?php if (!empty($user['profile_pic'])): ?>
        <img src="../uploads/<?= $user['profile_pic'] ?>" class="avatar mb-3" alt="Profile Picture">
    <?php else: ?>
        <img src="../assets/default-avatar.png" class="avatar mb-3" alt="Default Picture">
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="text" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Contact Number:</label>
            <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Address:</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Profile Picture:</label>
            <input type="file" name="profile_pic" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?= $_SESSION['user_role'] ?>_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
