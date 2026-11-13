<?php
require_once "config.php";
require_admin();

$id = (int)($_GET["id"] ?? 0);
$action = $_GET["action"] ?? "";

if ($id > 0 && $id != $_SESSION["user_id"]) {

    if ($action === "disable") {

        $status = "disabled";

    } elseif ($action === "enable") {

        $status = "approved";

    } else {

        header("Location: users.php");
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE users
        SET status = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: users.php");
exit;
?>