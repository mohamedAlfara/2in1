<?php
require '../db/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

if ($is_admin) {
    $stmt = $conn->query("SELECT b.*, r.room_type AS room_name, u.username 
                          FROM bookings b
                          JOIN rooms r ON b.room_id = r.id
                          JOIN users u ON b.user_id = u.id
                          ORDER BY b.check_in DESC");
} else {
    $stmt = $conn->prepare("SELECT b.*, r.room_type AS room_name 
                            FROM bookings b
                            JOIN rooms r ON b.room_id = r.id 
                            WHERE b.user_id = ?
                            ORDER BY b.check_in DESC");
    $stmt->execute([$user_id]);
}

$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
    
</head>
<body>

<header>
    <h1>Manage Bookings</h1>
</header>

<main>
    <div class="booking-container">
        <h2><?= $is_admin ? 'All Bookings' : 'Your Bookings' ?></h2>

        <?php if (count($bookings) === 0): ?>
            <p style="text-align: center;">No bookings found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <?php if ($is_admin): ?>
                        <th>User</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['room_name']) ?></td>
                        <td><?= htmlspecialchars($booking['check_in']) ?></td>
                        <td><?= htmlspecialchars($booking['check_out']) ?></td>
                        <?php if ($is_admin): ?>
                            <td><?= htmlspecialchars($booking['username']) ?></td>
                        <?php endif; ?>
                        <td class="actions">
                            <a href="edit_booking.php?id=<?= $booking['id'] ?>">Edit</a>
                            <a href="delete_booking.php?id=<?= $booking['id'] ?>" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
