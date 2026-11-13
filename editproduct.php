<?php
require_once "config.php";
require_login();

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: products.php");
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $quantity = (int)($_POST["quantity"] ?? 0);
    $price = (float)($_POST["price"] ?? 0);

    if ($name === "") {

        $message = "Product name is required.";

    } elseif ($quantity < 0) {

        $message = "Quantity cannot be negative.";

    } elseif ($price < 0) {

        $message = "Price cannot be negative.";

    } else {

        $update = $conn->prepare("
            UPDATE products
            SET
                name = ?,
                description = ?,
                quantity = ?,
                price = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $update->bind_param(
            "ssidi",
            $name,
            $description,
            $quantity,
            $price,
            $id
        );

        if ($update->execute()) {
            $update->close();
            header("Location: products.php");
            exit;
        }

        $message = "Unable to update product.";

        $update->close();
    }

    $product["name"] = $name;
    $product["description"] = $description;
    $product["quantity"] = $quantity;
    $product["price"] = $price;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Inventory System</title>
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

    <div class="content-box small-box">

        <h2>Edit Product</h2>

        <?php if ($message !== ""): ?>
            <div class="message error">
                <?= h($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Product Name</label>

            <input
                type="text"
                name="name"
                value="<?= h($product["name"]) ?>"
                required
            >

            <label>Description</label>

            <textarea
                name="description"
                rows="4"
            ><?= h($product["description"]) ?></textarea>

            <label>Quantity</label>

            <input
                type="number"
                name="quantity"
                min="0"
                value="<?= h($product["quantity"]) ?>"
                required
            >

            <label>Price</label>

            <input
                type="number"
                name="price"
                min="0"
                step="0.01"
                value="<?= h($product["price"]) ?>"
                required
            >

            <button type="submit">Update Product</button>

            <a class="cancel-button" href="products.php">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>