<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$item_id = $_GET['id'];
$stmt = $conn->prepare("UPDATE reported_items SET approved = 'Approved' WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit();
?>


