<?php
require_once "config.php";
require_admin();

$result = $conn->query("
    SELECT id, username, role, status
    FROM users
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Inventory System</title>
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

        <a href="users.php">User Management</a>

        <a href="logout.php">Logout</a>

    </div>

</nav>

<div class="container">

    <div class="content-box">

        <h2>User Management</h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($user = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?= h($user["id"]) ?></td>

                            <td><?= h($user["username"]) ?></td>

                            <td><?= h(ucfirst($user["role"])) ?></td>

                            <td>

                                <?php if ($user["status"] === "approved"): ?>

                                    <span class="status approved">
                                        Approved
                                    </span>

                                <?php elseif ($user["status"] === "pending"): ?>

                                    <span class="status pending">
                                        Pending
                                    </span>

                                <?php else: ?>

                                    <span class="status disabled">
                                        Disabled
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="actions">

                                <?php if ($user["status"] === "pending"): ?>

                                    <a
                                        class="btn approve"
                                        href="approveuser.php?id=<?= $user["id"] ?>"
                                        onclick="return confirm('Approve this user?');"
                                    >
                                        Approve
                                    </a>

                                <?php elseif ($user["status"] === "approved"): ?>

                                    <?php if ($user["id"] != $_SESSION["user_id"]): ?>

                                        <a
                                            class="btn disable"
                                            href="disableuser.php?id=<?= $user["id"] ?>&action=disable"
                                            onclick="return confirm('Disable this user?');"
                                        >
                                            Disable
                                        </a>

                                    <?php else: ?>

                                        <span class="current-user">
                                            Current User
                                        </span>

                                    <?php endif; ?>

                                <?php elseif ($user["status"] === "disabled"): ?>

                                    <a
                                        class="btn approve"
                                        href="disableuser.php?id=<?= $user["id"] ?>&action=enable"
                                        onclick="return confirm('Enable this user?');"
                                    >
                                        Enable
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>