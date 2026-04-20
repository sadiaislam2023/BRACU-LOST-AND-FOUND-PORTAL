<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch user's reported items
$my_items_stmt = $conn->prepare("SELECT * FROM reported_items WHERE user_id = ? ORDER BY date DESC");
$my_items_stmt->bind_param("i", $user_id);
$my_items_stmt->execute();
$my_items = $my_items_stmt->get_result();

// Fetch user's claims with DISTINCT to remove duplicate entries
$my_claims_stmt = $conn->prepare("
    SELECT DISTINCT c.*, i.item_category AS category, i.item_description AS description, i.username AS founder_username
    FROM item_claims c
    JOIN reported_items i ON c.item_id = i.id
    WHERE c.user_id = ? 
    ORDER BY c.claim_date DESC
");
$my_claims_stmt->bind_param("i", $user_id);
$my_claims_stmt->execute();
$my_claims = $my_claims_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - BRACU Lost and Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">My Dashboard</h2>

    <h4 class="mt-4">My Reported Items</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Item Status</th>
                <th>Report Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $my_items->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($item['item_category']); ?></td>
                <td><?php echo htmlspecialchars($item['item_description']); ?></td>
                <td>
                    <?php 
                        if ($item['status'] === 'Lost') {
                            echo '<span class="badge bg-danger">Lost</span>';
                        } elseif ($item['status'] === 'Found') {
                            echo '<span class="badge bg-success">Found</span>';
                        } else {
                            echo htmlspecialchars($item['status']);
                        }
                    ?>
                </td>
                <td>
                    <?php 
                        if ($item['approved'] === 'Approved') {
                            echo '<span class="badge bg-success">Approved</span>';
                        } elseif ($item['approved'] === 'Pending') {
                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                        } else {
                            echo htmlspecialchars($item['approved']);
                        }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($item['date']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4 class="mt-5">My Claims</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Founder</th>
                <th>Proof Document</th>
                <th>Claim Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($claim = $my_claims->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($claim['category'] . ' - ' . $claim['description']); ?></td>
                <td><?php echo htmlspecialchars($claim['username']); ?></td>
                <td>
                    <?php if (!empty($claim['proof_document'])): ?>
                        <a href="uploads/proofs/<?php echo htmlspecialchars($claim['proof_document']); ?>" target="_blank">View</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                        if ($claim['status'] === 'Approved') {
                            echo '<span class="badge bg-success">Approved</span>';
                        } elseif ($claim['status'] === 'Pending') {
                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                        } else {
                            echo htmlspecialchars($claim['status']);
                        }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($claim['claim_date']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if ($user_role == 'admin') { ?>
        <div class="mt-4 text-center">
            <a href="admin_dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

