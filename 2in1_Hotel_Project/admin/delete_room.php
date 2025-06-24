<?php
require('../db/config.php');
session_start();

// ✅ تحقق من صلاحية المسؤول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied.");
}

// ✅ تحقق من وجود ID صالح
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid room ID.");
}

$id = intval($_GET['id']);

// ✅ جلب اسم الصورة لحذفها من السيرفر أيضًا
$stmt = $conn->prepare("SELECT image FROM rooms WHERE id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ حذف الصورة إذا كانت موجودة
if ($room && !empty($room['image'])) {
    $image_path = '../images/' . $room['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// ✅ حذف الغرفة من قاعدة البيانات
$stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
$stmt->execute([$id]);

header("Location: dashboard.php");
exit;
?>
