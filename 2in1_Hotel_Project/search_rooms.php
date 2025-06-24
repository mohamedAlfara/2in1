<?php
require 'config.php';

$rooms = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkin  = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';

    if ($checkin && $checkout) {
        $stmt = $conn->prepare("
            SELECT * FROM rooms 
            WHERE id NOT IN (
                SELECT room FROM bookings 
                WHERE checkin < ? AND checkout > ?
            ) AND available = 1
        ");
        $stmt->execute([$checkout, $checkin]);
        $rooms = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Available Rooms</title>
    <link rel="stylesheet" href="css/style.css">
 
</head>
<body>

<header>
    <h1>Search Rooms</h1>
</header>

<main>
    <div class="container">
        <h2>Find an Available Room</h2>

        <form method="POST">
            <label for="checkin">Check-in Date:</label>
            <input type="date" name="checkin" id="checkin" required>

            <label for="checkout">Check-out Date:</label>
           <div class="search-bar">
                    <input type="date" name="checkout" id="checkout" required>
           </div> 
         


            <button type="submit">Search</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h3>Available Rooms</h3>
            <?php if ($rooms): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Room Type</th>
                        <th>Description</th>
                        <th>Price</th>
                    </tr>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['id']) ?></td>
                            <td><?= htmlspecialchars($room['room_type']) ?></td>
                            <td><?= htmlspecialchars($room['description']) ?></td>
                            <td>$<?= number_format($room['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p class="no-results">No rooms available for the selected dates.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p style="text-align:center;">&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
