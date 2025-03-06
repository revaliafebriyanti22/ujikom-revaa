<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE tasks SET status='Selesai' WHERE id='$id'");
}

header("Location: index.php");
exit();
?>
