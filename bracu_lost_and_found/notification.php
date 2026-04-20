<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - BRACU Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Notifications</h2>

    <?php if ($role === 'admin'): ?>
        <h4>Admin Notifications</h4>

        <!-- New reported items -->
        <div class="card mb-3">
            <div class="card-header">Pending Report Approvals</div>
            <div class="card-body">
                <?php
               $stmt = $conn->prepare("SELECT id, item_description FROM reported_items WHERE approved = 'Pending'");

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <p>New item reported: <?= htmlspecialchars($row['item_description']) ?> 
                        <a href="admin_dashboard.php" class="btn btn-sm btn-primary">Review</a></p>
                <?php endwhile;
                else: ?>
                    <p>No new reported items pending approval.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- New claims -->
        <div class="card mb-3">
            <div class="card-header">Pending Claim Approvals</div>
            <div class="card-body">
                <?php
                $stmt = $conn->prepare("SELECT id, item_id, username, claim_message FROM item_claims WHERE status = 'Pending'");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <p>New claim submitted for Item ID <?= $row['item_id'] ?>: 
                        "<?= htmlspecialchars($row['claim_message']) ?>" 
                        <a href="admin_dashboard.php" class="btn btn-sm btn-warning">Review</a></p>
                <?php endwhile;
                else: ?>
                    <p>No new item claims pending approval.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <h4>User Notifications</h4>

        <!-- Approved reported items -->
        <div class="card mb-3">
            <div class="card-header">Your Reported Items</div>
            <div class="card-body">
                <?php
                $stmt = $conn->prepare("SELECT item_description FROM reported_items WHERE user_id = ? AND approved = 'Approved'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <p>Your reported item "<strong><?= htmlspecialchars($row['item_description']) ?></strong>" has been approved!</p>
                <?php endwhile;
                else: ?>
                    <p>No new approved reports yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approved claims -->
        <div class="card mb-3">
            <div class="card-header">Your Claim Status</div>
            <div class="card-body">
                <?php
                $stmt = $conn->prepare("SELECT item_id FROM item_claims WHERE user_id = ? AND status = 'Approved'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <p>Your claim for Item ID <strong><?= $row['item_id'] ?></strong> has been approved by the admin!</p>
                <?php endwhile;
                else: ?>
                    <p>No approved claims yet.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">← Back to Home</a>
    </div>
</div>

</body>
</html>
