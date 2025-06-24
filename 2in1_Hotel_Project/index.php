<?php
session_start();
require_once 'db/config.php';

// جلب 3 غرف متاحة
$stmt = $conn->query("SELECT * FROM rooms WHERE available = 1 LIMIT 3");
$rooms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book your stay in our luxury hotel. Discover comfortable rooms, friendly service, and affordable prices.">
    <meta name="keywords" content="Hotel, Booking, Rooms, Reservation, Stay">
    <meta name="author" content="Our Hotel">
    <title>Hotel Booking System</title>
    <link rel="stylesheet" href="css/style.css">
   
</head>
<body>

<header>
    <h1>Welcome to Our Hotel</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="available_rooms.php">Available Rooms</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="bookings/manage_bookings.php">My Bookings</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php">Admin Panel</a>
            <?php endif; ?>
            <a href="auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="auth/login.php">Login</a>
            <a href="auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <section class="welcome">
        <h2>Enjoy a Luxurious Stay</h2>
        <p>Book your room now at the best price!</p>
        <a href="available_rooms.php" class="btn">View All Rooms</a>
    </section>

    <section class="available-rooms">
        <h2>Featured Rooms</h2>
        <div class="room-grid">
            <?php if (count($rooms) > 0): ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <img src="images/<?= htmlspecialchars($room['image'] ?? 'default.jpg'); ?>" alt="Room Image">
                        <h3><?= htmlspecialchars($room['room_type']) ?></h3>
                        <p>Price: $<?= number_format($room['price'], 2) ?> / night</p>
                        <a href="bookings/book.php?room_id=<?= $room['id']; ?>" class="btn">Book Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">No rooms available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> Our Hotel. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/main.js"></script>
</body>
</html>
