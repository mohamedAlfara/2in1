<?php
require('../db/config.php');
session_start();

// ✅ تحقق من صلاحية المسؤول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $available   = isset($_POST['available']) ? 1 : 0;
    $image_name  = null;

    // ✅ رفع الصورة إن وجدت
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
            $image_name = uniqid('room_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $image_name);
        }
    }

    // ✅ التحقق من الحقول المطلوبة
    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_type, description, price, available, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $available, $image_name]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>

<header>
    <h1>Hotel Management - Admin</h1>
</header>

<main>
    <h2>Add New Room</h2>

    <?php if (isset($error)) : ?>
        <p style="color: red; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Room Type:</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" style="resize: vertical;"></textarea>

        <label for="price">Price ($):</label>
        <input type="number" name="price" id="price" step="0.01" required>

        <label for="available">
            <input type="checkbox" name="available" id="available" checked>
            Available
        </label>

        <label for="image">Room Image:</label>
        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png">

        <button type="submit">Add Room</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel. All rights reserved.</p>
</footer>

</body>
</html>
