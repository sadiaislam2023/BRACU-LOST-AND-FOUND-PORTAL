<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get claim id from URL
$claim_id = $_GET['id'];

// First, get the item_id related to this claim
$stmt = $conn->prepare("SELECT item_id FROM item_claims WHERE id = ?");
$stmt->bind_param("i", $claim_id);
$stmt->execute();
$result = $stmt->get_result();
$claim = $result->fetch_assoc();

if ($claim) {
    $item_id = $claim['item_id'];

    // Approve the claim
    $stmt1 = $conn->prepare("UPDATE item_claims SET status = 'Approved' WHERE id = ?");
    $stmt1->bind_param("i", $claim_id);
    $stmt1->execute();

    // Update the reported item as claimed and approved
    $stmt2 = $conn->prepare("UPDATE reported_items SET approved = 'Approved', status = 'Item is claimed' WHERE id = ?");
    $stmt2->bind_param("i", $item_id);
    $stmt2->execute();
}

header("Location: admin_dashboard.php");
exit();
?>

