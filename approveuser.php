<?php
require_once "config.php";
require_admin();

$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare("
        UPDATE users
        SET status = 'approved'
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: users.php");
exit;
?>