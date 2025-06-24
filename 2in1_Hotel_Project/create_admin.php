<?php
require '../db/config.php';
session_start();

// بيانات الحساب الإداري الافتراضي
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123';
$adminName = 'Admin';

// تحقق إن كان الحساب الإداري موجودًا
$checkAdmin = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkAdmin->execute([$adminEmail]);

if (!$checkAdmin->fetch()) {
    // إذا لم يكن موجودًا، أنشئ الحساب
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$adminName, $adminEmail, $hashedPassword, 'admin']);
    echo "<p style='color:green;'>✅ Admin account created: <strong>$adminEmail / $adminPassword</strong></p>";
} else {
    echo "<p style='color:blue;'>ℹ️ Admin account already exists.</p>";
}
?>
