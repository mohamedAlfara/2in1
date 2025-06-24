<?php
require('../db/config.php');
session_start();

// ✅ تحقق من صلاحية المدير
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied.");
}

// ✅ تحقق من ID الغرفة
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid room ID.");
}

$id = intval($_GET['id']);

// ✅ جلب بيانات الغرفة
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    exit("Room not found.");
}

// ✅ تنفيذ التحديث إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $available   = isset($_POST['available']) ? 1 : 0;
    $new_image   = $room['image']; // الصورة الحالية الافتراضية

    // ✅ إذا تم رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
            // حذف الصورة القديمة إن وجدت
            if (!empty($room['image']) && file_exists('../images/' . $room['image'])) {
                unlink('../images/' . $room['image']);
            }

            // رفع الصورة الجديدة
            $new_image = uniqid('room_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $new_image);
        }
    }

    // ✅ تنفيذ التحديث في قاعدة البيانات
    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE rooms SET room_type = ?, description = ?, price = ?, available = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $available, $new_image, $id]);
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
    <title>Edit Room</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>

<header>
    <h1>Edit Room</h1>
</header>

<main>
    <h2 style="text-align:center;">Room ID: <?= $room['id'] ?></h2>

    <?php if (isset($error)) : ?>
        <p style="color: red; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Room Type:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($room['room_type']) ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($room['description']) ?></textarea>

        <label for="price">Price ($):</label>
        <input type="number" name="price" id="price" step="0.01" value="<?= htmlspecialchars($room['price']) ?>" required>

        <label for="available">
            <input type="checkbox" name="available" id="available" <?= $room['available'] ? 'checked' : '' ?>>
            Available
        </label>

        <label>Current Image:</label><br>
        <?php if (!empty($room['image']) && file_exists('../images/' . $room['image'])): ?>
            <img src="../images/<?= htmlspecialchars($room['image']) ?>" alt="Room Image" style="max-width: 300px;"><br><br>
        <?php else: ?>
            <p>No image uploaded.</p>
        <?php endif; ?>

        <label for="image">Change Image:</label>
        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png">

        <br><br>
        <button type="submit">Update Room</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel Admin Panel</p>
</footer>

</body>
</html>
