<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // Include database connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user has any approved claims
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM item_claims WHERE user_id = ? AND status = 'Approved'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$has_approved_claim = $result['count'] > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 text-center">
    <h2 class="mb-3">Messages</h2>
    
    <?php if ($has_approved_claim): ?>
        <p class="mb-4">Welcome to your messaging center! Please choose an option below.</p>
        <div class="d-flex justify-content-center gap-4">
            <a href="send_message.php" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send Message
            </a>
            <a href="view_messages.php" class="btn btn-secondary">
                <i class="fas fa-inbox"></i> View Sent Messages
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            You can access the messaging center once your item claim is approved by an admin.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-link">← Back to Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

