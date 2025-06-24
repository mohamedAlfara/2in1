<?php
require '../db/config.php';
session_start();

// ✅ التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ التحقق من معرف الغرفة
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    die("Invalid room ID.");
}

$room_id = (int) $_GET['room_id'];

// ✅ جلب بيانات الغرفة
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    die("Room not found.");
}

// ✅ تنفيذ الحجز عند الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    if (strtotime($check_in) >= strtotime($check_out)) {
        $error = "Check-out date must be after check-in date.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out])) {
            header("Location: manage_bookings.php");
            exit();
        } else {
            $error = "Booking failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Room</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
    
</head>
<body>

<header>
    <h1>Book This Room</h1>
</header>

<main>
    <div class="container">
        <h2 style="text-align:center; color:#003366;">Room Details</h2>

        <div class="room-details">
            <img src="../images/<?= htmlspecialchars($room['image']) ?>" alt="Room Image">
            <div class="room-info">
                <p><strong>Type:</strong> <?= htmlspecialchars($room['room_type']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($room['description']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($room['price'], 2) ?></p>
                <p><strong>Status:</strong> <?= $room['available'] ? '✅ Available' : '❌ Not Available' ?></p>
            </div>
        </div>

        <?php if (isset($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" class="booking-form">
            <label for="check_in">Check-in Date:</label>
            <input type="date" name="check_in" id="check_in" required>

            <label for="check_out">Check-out Date:</label>
            <input type="date" name="check_out" id="check_out" required>

            <button type="submit">📅 Book Now</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
