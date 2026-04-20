<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all feedback from users
$stmt = $conn->prepare("SELECT f.*, u.name AS user_name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">BRACU Lost and Found Portal</a>
</nav>

<!-- Feedback List -->
<div class="container mt-5">
    <h2 class="text-center">User Feedback</h2>

    <?php if ($result->num_rows > 0) { ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="list-group-item">
                    <strong><?php echo $row['user_name']; ?>:</strong>
                    <p><?php echo $row['message']; ?></p>
                    <small class="text-muted"><?php echo $row['created_at']; ?></small>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="text-center">No feedback submitted yet.</p>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
