<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "ctwa_finalsdb");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}

function require_admin() {
    require_login();

    if ($_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php");
        exit;
    }
}
?>