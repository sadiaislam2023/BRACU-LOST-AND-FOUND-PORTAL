<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all reported items
$stmt = $conn->prepare("SELECT * FROM reported_items ORDER BY date DESC");
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Reported Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">All Reported Items</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Approved</th>
                <th>Reporter</th>
                <th>Location</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()) { ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td>
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" width="60" height="60">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['item_category']) ?></td>
                    <td><?= htmlspecialchars($item['item_description']) ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($item['status']) ?></span></td>
                    <td>
                        <?php if ($item['approved'] === 'Approved') { ?>
                            <span class="badge bg-success">Approved</span>
                        <?php } elseif ($item['approved'] === 'Pending') { ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php } else { ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($item['approved']) ?></span>
                        <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($item['username']) ?><br><small><?= htmlspecialchars($item['reporter_details']) ?></small></td>
                    <td><?= htmlspecialchars($item['location']) ?><br><small><?= htmlspecialchars($item['location_details']) ?></small></td>
                    <td><?= htmlspecialchars($item['date']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

