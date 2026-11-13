<?php
require_once "config.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($username === "" || $password === "" || $confirm_password === "") {

        $message = "Please fill in all fields.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        $stmt = $conn->prepare("
            SELECT id
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "Username already exists.";

        } else {

            $role = "user";
            $status = "pending";

            $insert = $conn->prepare("
                INSERT INTO users
                (username, password, role, status)
                VALUES (?, ?, ?, ?)
            ");

            $insert->bind_param(
                "ssss",
                $username,
                $password,
                $role,
                $status
            );

            if ($insert->execute()) {
                $success = "Account created successfully. Please wait for admin approval.";
            } else {
                $message = "Unable to create account.";
            }

            $insert->close();
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
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">

<div class="login-box">

    <h1>Create Account</h1>
    <p class="subtitle">Register for Inventory System</p>

    <?php if ($message !== ""): ?>
        <div class="message error">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div class="message success">
            <?= h($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Create Account</button>

    </form>

    <p class="account-link">
        Already have an account?
        <a href="index.php">Back to Login</a>
    </p>

</div>

</body>
</html>