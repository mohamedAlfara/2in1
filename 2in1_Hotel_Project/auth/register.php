<?php
require '../db/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = "✅ Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "❌ Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>

<header>
    <h1>User Registration</h1>
</header>

<main>
    <h2 style="text-align:center;">Create a New Account</h2>

    <?php if (isset($error)): ?>
        <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color: green; text-align: center;"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top:20px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
