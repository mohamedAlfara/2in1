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
    <h1>Available Rooms</h1>
</header>

<main>
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by room name..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="room-list">
        <?php
        $sql = "SELECT * FROM rooms WHERE available = 1";
        $params = [];

        if (!empty($_GET['search'])) {
            $sql .= " AND room_type LIKE ?";
            $params[] = '%' . $_GET['search'] . '%';
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll();

        if ($rooms):
            foreach ($rooms as $room):
        ?>
            <div class="room-item">
                <img src="images/<?= htmlspecialchars($room['image']) ?>" alt="Room Image">
                <h3><?= htmlspecialchars($room['room_type']) ?></h3>
                <p><?= htmlspecialchars($room['description']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($room['price'], 2) ?> / night</p>
                <a href="bookings/book.php?room_id=<?= $room['id'] ?>">Book Now</a>
            </div>
        <?php
            endforeach;
        else:
            echo "<p style='text-align:center;'>No rooms available.</p>";
        endif;
        ?>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
