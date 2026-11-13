<?php
require_once "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please enter your username and password.";
    } else {

        $stmt = $conn->prepare("
            SELECT id, username, password, role, status
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ($password !== $user["password"]) {
                $message = "Incorrect username or password.";
            } elseif ($user["status"] === "pending") {
                $message = "Your account is still waiting for admin approval.";
            } elseif ($user["status"] === "disabled") {
                $message = "Your account has been disabled.";
            } elseif ($user["status"] === "approved") {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                header("Location: dashboard.php");
                exit;
            }

        } else {
            $message = "Incorrect username or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System - Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">

<div class="login-box">

    <h1>Inventory System</h1>
    <p class="subtitle">Login to your account</p>

    <?php if ($message !== ""): ?>
        <div class="message error">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Sign In</button>

    </form>

    <p class="account-link">
        Don't have an account?
        <a href="register.php">Create Account</a>
    </p>

</div>

</body>
</html>