<?php
session_start();
require('../db/config.php');

// ✅ تحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// ✅ جلب كل الغرف من قاعدة البيانات
$stmt = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
   
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
</header>

<main>
    <h2 style="text-align:center;">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p style="text-align:center; color: #555;">You are logged in as <strong><?= $_SESSION['role'] ?></strong></p>

    <div class="dashboard-menu">
        <a href="add_room.php">➕ Add New Room</a>
        <a href="../bookings/manage_bookings.php">📋 Manage Bookings</a>
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>

    <h2 style="text-align:center; margin-top: 50px;">🛏️ All Rooms</h2>

    <div class="room-list">
        <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <?php if (!empty($room['image']) && file_exists('../images/' . $room['image'])): ?>
                    <img src="../images/<?= htmlspecialchars($room['image']) ?>" alt="Room Image">
                <?php else: ?>
                    <img src="../images/default.jpg" alt="No Image">
                <?php endif; ?>
                <div class="content">
                    <h3><?= htmlspecialchars($room['room_type']) ?></h3>
                    <p><?= htmlspecialchars($room['description']) ?></p>
                    <p><strong>$<?= number_format($room['price'], 2) ?></strong></p>
                    <p>Status: <?= $room['available'] ? '✅ Available' : '❌ Unavailable' ?></p>
                    <div class="actions">
                        <a class="edit-btn" href="edit_room.php?id=<?= $room['id'] ?>">✏️ Edit</a>
                        <a class="delete-btn" href="delete_room.php?id=<?= $room['id'] ?>" onclick="return confirm('Are you sure you want to delete this room?');">🗑️ Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel Admin Panel</p>
</footer>

</body>
</html>
