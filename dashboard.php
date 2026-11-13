<?php
require_once "config.php";
require_login();

$user_count = 0;
$pending_count = 0;
$product_count = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM users");

if ($result) {
    $row = $result->fetch_assoc();
    $user_count = $row["total"];
}

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE status = 'pending'
");

if ($result) {
    $row = $result->fetch_assoc();
    $pending_count = $row["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM products");

if ($result) {
    $row = $result->fetch_assoc();
    $product_count = $row["total"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

    <div class="logo">
        Inventory System
    </div>

    <div class="nav-links">

        <a href="dashboard.php">Dashboard</a>

        <a href="products.php">Products</a>

        <?php if ($_SESSION["role"] === "admin"): ?>
            <a href="users.php">User Management</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>

    </div>

</nav>

<div class="container">

    <div class="welcome">
        <h2>Welcome, <?= h($_SESSION["username"]) ?>!</h2>

        <p>
            Role:
            <strong><?= h(ucfirst($_SESSION["role"])) ?></strong>
        </p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Products</h3>
            <p><?= $product_count ?></p>
        </div>

        <?php if ($_SESSION["role"] === "admin"): ?>

            <div class="card">
                <h3>Total Users</h3>
                <p><?= $user_count ?></p>
            </div>

            <div class="card">
                <h3>Pending Users</h3>
                <p><?= $pending_count ?></p>
            </div>

        <?php endif; ?>

    </div>

    <div class="content-box">

        <h2>Inventory Dashboard</h2>

        <p>
            Manage your products and inventory using the navigation menu.
        </p>

        <?php if ($_SESSION["role"] === "admin"): ?>

            <p>
                As an administrator, you can also approve, disable,
                and enable user accounts.
            </p>

        <?php endif; ?>

    </div>

</div>

</body>
</html>