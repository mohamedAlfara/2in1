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

// ✅ جلب بيانات الحجز
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Booking not found or access denied.");
}

// ✅ تنفيذ التعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // تحقق من التواريخ
    if (strtotime($check_in) >= strtotime($check_out)) {
        $error = "Check-out must be after check-in.";
    } else {
        $stmt = $conn->prepare("UPDATE bookings SET check_in = ?, check_out = ? WHERE id = ?");
        if ($stmt->execute([$check_in, $check_out, $booking_id])) {
            header("Location: manage_bookings.php");
            exit();
        } else {
            $error = "Failed to update booking.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
    
</head>
<body>

<header>
    <h1>Edit Booking</h1>
</header>

<main>
    <div class="edit-container">
        <h2>Update Booking Dates</h2>

        <?php if (isset($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="check_in">Check-in Date:</label>
            <input type="date" name="check_in" id="check_in" value="<?= htmlspecialchars($booking['check_in']) ?>" required>

            <label for="check_out">Check-out Date:</label>
            <input type="date" name="check_out" id="check_out" value="<?= htmlspecialchars($booking['check_out']) ?>" required>

            <button type="submit">Update Booking</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
