<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all claims with joined item info, including proof_document
$stmt = $conn->prepare("
    SELECT c.*, 
           c.proof_document,
           r.item_category, 
           r.item_description, 
           r.username AS founder_username,
           r.image_url
    FROM item_claims c
    JOIN reported_items r ON c.item_id = r.id
    ORDER BY c.claim_date DESC
");
$stmt->execute();
$claims = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Claimed Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">All Claim Requests</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Item ID</th>
                <th>Item</th>
                <th>Image</th>
                <th>Claimer</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Founder</th>
                <th>Claim Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($claim = $claims->fetch_assoc()) { ?>
                <tr>
                     <td><?= htmlspecialchars($claim['item_id']) ?>
                    <td><?= htmlspecialchars($claim['item_category'] . ' - ' . $claim['item_description']) ?></td>
                    <td>
                        <?php if (!empty($claim['image_url'])): ?>
                            <img src="<?= htmlspecialchars($claim['image_url']) ?>" width="60" height="60">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($claim['username']) ?>
                    <td>
                        <?php if (!empty($claim['proof_document'])): ?>
                            <img src="<?= htmlspecialchars($claim['proof_document']) ?>" width="60" height="60">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($claim['status'] === 'Approved') { ?>
                            <span class="badge bg-success">Approved</span>
                        <?php } elseif ($claim['status'] === 'Pending') { ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php } else { ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($claim['status']) ?></span>
                        <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($claim['founder_username']) ?></td>
                    <td><?= htmlspecialchars($claim['claim_date']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
