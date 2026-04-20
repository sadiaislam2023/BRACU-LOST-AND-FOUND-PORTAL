<?php
session_start();
// Optional: Redirect if accessed directly without submitting feedback
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - BRACU Lost and Found Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">BRACU Lost and Found Portal</a>
    </div>
</nav>

<!-- Thank You Message -->
<div class="container mt-5">
    <div class="card shadow p-4 text-center">
        <h2 class="text-success">Thank You!</h2>
        <p class="mt-3">Your feedback has been submitted successfully. We appreciate your input and will use it to improve our services.</p>
        <a href="index.php" class="btn btn-primary mt-4">Back to Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
