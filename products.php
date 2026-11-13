<?php
require_once "config.php";
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $quantity = (int)($_POST["quantity"] ?? 0);
    $price = (float)($_POST["price"] ?? 0);
    $created_by = $_SESSION["user_id"];

    if ($name === "") {

        $message = "Product name is required.";

    } elseif ($quantity < 0) {

        $message = "Quantity cannot be negative.";

    } elseif ($price < 0) {

        $message = "Price cannot be negative.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO products
            (name, description, quantity, price, created_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "ssidi",
            $name,
            $description,
            $quantity,
            $price,
            $created_by
        );

        if ($stmt->execute()) {
            $message = "Product added successfully.";
        } else {
            $message = "Unable to add product.";
        }

        $stmt->close();
    }
}

$result = $conn->query("
    SELECT
        p.*,
        u.username AS creator
    FROM products p
    LEFT JOIN users u ON p.created_by = u.id
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Inventory System</title>
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

    <div class="content-box">

        <h2>Add Product</h2>

        <?php if ($message !== ""): ?>
            <div class="message success">
                <?= h($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="product-form">

            <label>Product Name</label>
            <input type="text" name="name" required>

            <label>Description</label>
            <textarea name="description" rows="4"></textarea>

            <label>Quantity</label>
            <input type="number" name="quantity" min="0" required>

            <label>Price</label>
            <input type="number" name="price" min="0" step="0.01" required>

            <button type="submit">Add Product</button>

        </form>

    </div>

    <div class="content-box">

        <h2>Product List</h2>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($product = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?= h($product["id"]) ?></td>

                            <td><?= h($product["name"]) ?></td>

                            <td><?= h($product["description"]) ?></td>

                            <td><?= h($product["quantity"]) ?></td>

                            <td>
                                ₱<?= number_format((float)$product["price"], 2) ?>
                            </td>

                            <td>
                                <?= h($product["creator"] ?? "Unknown") ?>
                            </td>

                            <td><?= h($product["created_at"]) ?></td>

                            <td><?= h($product["updated_at"]) ?></td>

                            <td class="actions">

                                <a
                                    class="btn edit"
                                    href="editproduct.php?id=<?= $product["id"] ?>"
                                >
                                    Edit
                                </a>

                                <?php if ($_SESSION["role"] === "admin"): ?>

                                    <a
                                        class="btn delete"
                                        href="deleteproduct.php?id=<?= $product["id"] ?>"
                                        onclick="return confirm('Are you sure you want to delete this product?');"
                                    >
                                        Delete
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9">No products found.</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>