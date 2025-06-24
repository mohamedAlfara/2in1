<?php
require 'db/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>

<header>
    <h1>Our Hotel</h1>
</header>

<main>
    <h2 class="page-title">Available Rooms</h2>

    <div class="room-grid">
        <?php
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE available = 1");
        $stmt->execute();
        $rooms = $stmt->fetchAll();

        foreach ($rooms as $room):
        ?>
            <div class="room-card">
                <?php if ($room['image']): ?>
                    <img src="images/<?= htmlspecialchars($room['image']) ?>" alt="Room Image">
                <?php endif; ?>
                <h3><?= htmlspecialchars($room['room_type']) ?></h3>
                <p><?= htmlspecialchars($room['description']) ?></p>
                <p><strong>$<?= number_format($room['price'], 2) ?></strong> / night</p>
                <a href="bookings/book.php?room_id=<?= $room['id'] ?>">Book Now</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer>
    <p style="text-align:center;">&copy; <?= date("Y") ?> Our Hotel</p>
</footer>

</body>
</html>
