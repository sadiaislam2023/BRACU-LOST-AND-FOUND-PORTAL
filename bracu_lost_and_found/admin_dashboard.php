<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch reported items
$reported_items = $conn->query("SELECT * FROM reported_items ORDER BY date DESC");

// Fetch claimed items
$claimed_items = $conn->query("
    SELECT c.* 
    FROM item_claims c 
    JOIN reported_items i ON c.item_id = i.id 
    ORDER BY c.claim_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Reported Items</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Reporter Username</th>
                <th>Item Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Location</th>
                <th>Location Details</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $reported_items->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id']); ?></td>
                <td><?php echo htmlspecialchars($item['username']); ?></td>
                <td><?php echo htmlspecialchars($item['item_category']); ?></td>
                <td><?php echo htmlspecialchars($item['item_description']); ?></td>
                <td><?php echo htmlspecialchars($item['status']); ?></td>
                <td><?php echo htmlspecialchars($item['location']); ?></td>
                <td><?php echo htmlspecialchars($item['location_details']); ?></td>
                <td><?php echo $item['date']; ?></td>
                <td><?php echo htmlspecialchars($item['approved']); ?></td>
                
                <td>
                    <?php if ($item['approved'] === 'Pending') { ?>
                        <a href="approve_item.php?id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="mt-5">Manage Claimed Items</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Claimant Username</th>
                <th>Claim Message</th>
                <th>Proof of Ownership</th>
                <th>Claim Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($claim = $claimed_items->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($claim['item_id']); ?></td>
                <td><?php echo htmlspecialchars($claim['username']); ?></td>
                <td><?php echo htmlspecialchars($claim['claim_message']); ?></td>
                <td><?php echo htmlspecialchars($claim['proof_document']); ?></td>
                <td><?php echo htmlspecialchars($claim['status']); ?></td>
                <td>
                    <?php if ($claim['status'] === 'Pending') { ?>
                        <a href="approve_claim.php?id=<?php echo $claim['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>