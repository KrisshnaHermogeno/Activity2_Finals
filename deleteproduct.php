<?php
require_once "config.php";
require_admin();

$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: products.php");
exit;
?>