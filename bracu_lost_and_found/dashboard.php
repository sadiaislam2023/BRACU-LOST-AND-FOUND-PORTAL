<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? '';

// Fetch user's reported items
$my_items_stmt = $conn->prepare("
    SELECT *
    FROM reported_items
    WHERE user_id = ?
    ORDER BY date DESC
");
$my_items_stmt->bind_param("i", $user_id);
$my_items_stmt->execute();
$my_items = $my_items_stmt->get_result();

// Fetch user's claims
$my_claims_stmt = $conn->prepare("
    SELECT DISTINCT
        c.*,
        i.item_category AS category,
        i.item_description AS description,
        i.username AS founder_username
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BRACU Lost and Found</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    >
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4 mb-5">
    <h2 class="text-center mb-4">My Dashboard</h2>

    <!-- My Reported Items -->
    <h4 class="mt-4">My Reported Items</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
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
                <?php if ($my_items->num_rows > 0): ?>
                    <?php while ($item = $my_items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($item['item_category'] ?? '') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($item['item_description'] ?? '') ?>
                            </td>
                            <td>
                                <?php
                                $itemStatus = $item['status'] ?? '';

                                if (strcasecmp($itemStatus, 'Lost') === 0) {
                                    echo '<span class="badge bg-danger">Lost</span>';
                                } elseif (strcasecmp($itemStatus, 'Found') === 0) {
                                    echo '<span class="badge bg-success">Found</span>';
                                } else {
                                    echo htmlspecialchars($itemStatus);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $approval = $item['approved'] ?? '';

                                if (strcasecmp($approval, 'Approved') === 0) {
                                    echo '<span class="badge bg-success">Approved</span>';
                                } elseif (strcasecmp($approval, 'Pending') === 0) {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                } else {
                                    echo htmlspecialchars($approval);
                                }
                                ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($item['date'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            You have not reported any items yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- My Claims -->
    <h4 class="mt-5">My Claims</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
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
                <?php if ($my_claims->num_rows > 0): ?>
                    <?php while ($claim = $my_claims->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars(
                                    ($claim['category'] ?? '') . ' - ' .
                                    ($claim['description'] ?? '')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $claim['founder_username'] ?? ''
                                ) ?>
                            </td>

                            <!-- Corrected Proof Document link -->
                            <td>
                                <?php
                                $proofPath = trim(
                                    $claim['proof_document'] ?? ''
                                );

                                if ($proofPath !== '') {
                                    // Normalize path separators.
                                    $proofPath = str_replace(
                                        '\\',
                                        '/',
                                        $proofPath
                                    );

                                    // Remove query strings or URL fragments
                                    // if the database happens to contain them.
                                    $proofPath = preg_replace(
                                        '/[?#].*$/',
                                        '',
                                        $proofPath
                                    );

                                    // Extract only the filename, whether
                                    // the database stores a filename,
                                    // a relative path, or an absolute URL.
                                    $proofFilename = basename($proofPath);

                                    // Build the URL with the folder once.
                                    $proofUrl =
                                        '/BRACU-LOST-AND-FOUND-PORTAL-main/' .
                                        'bracu_lost_and_found/uploads/proofs/' .
                                        rawurlencode($proofFilename);

                                    // Check the expected physical file location.
                                    $proofFile = __DIR__ .
                                        DIRECTORY_SEPARATOR . 'uploads' .
                                        DIRECTORY_SEPARATOR . 'proofs' .
                                        DIRECTORY_SEPARATOR . $proofFilename;
                                ?>

                                    <?php if (is_file($proofFile)): ?>
                                        <a
                                            href="<?= htmlspecialchars(
                                                $proofUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-danger">
                                            Proof file not found
                                        </span>
                                    <?php endif; ?>

                                <?php } else { ?>
                                    <span class="text-muted">N/A</span>
                                <?php } ?>
                            </td>

                            <td>
                                <?php
                                $claimStatus = $claim['status'] ?? '';

                                if (strcasecmp($claimStatus, 'Approved') === 0) {
                                    echo '<span class="badge bg-success">Approved</span>';
                                } elseif (strcasecmp($claimStatus, 'Pending') === 0) {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                } elseif (strcasecmp($claimStatus, 'Rejected') === 0) {
                                    echo '<span class="badge bg-danger">Rejected</span>';
                                } else {
                                    echo htmlspecialchars($claimStatus);
                                }
                                ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($claim['claim_date'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            You have not submitted any claims yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Admin Dashboard -->
    <?php if ($user_role === 'admin'): ?>
        <div class="mt-4 text-center">
            <a href="admin_dashboard.php" class="btn btn-primary">
                Go to Admin Dashboard
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
