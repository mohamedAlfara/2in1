<?php
require '../db/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // تسجيل الدخول
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['username'];
            $_SESSION['role']    = $user['role'];

            // التوجيه حسب الدور
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $error = "Incorrect email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>

<header>
    <h1>User Login</h1>
</header>

<main>
    <h2 style="text-align:center;">Welcome 2in1 hotel</h2>

    <?php if (isset($error)): ?>
        <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>

    <p style="text-align:center; margin-top:20px;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Hotel</p>
</footer>

</body>
</html>
