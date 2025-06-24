<?php
require '../db/config.php';
session_start();

// ✅ تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ تحقق من ID الحجز
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid booking ID.");
}

$booking_id = (int) $_GET['id'];

// ✅ تأكد أن الحجز يعود للمستخدم الحالي
$stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    die("You are not authorized to delete this booking.");
}

// ✅ حذف الحجز
$stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
if ($stmt->execute([$booking_id])) {
    header("Location: manage_bookings.php");
    exit();
} else {
    echo "❌ Failed to delete booking. Please try again.";
}
?>
