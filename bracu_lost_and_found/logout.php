<?php
session_start();
session_destroy();  // Destroy the session to log out the user
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out - BRACU Lost and Found Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

<div class="text-center">
    <h1 class="display-4 mb-3">Logged out!</h1>
    <p class="lead mb-4">Thank you for using <strong>BRACU Lost and Found Portal</strong>.</p>
    <a href="login.php" class="btn btn-primary btn-lg">
        <i class="fas fa-sign-in-alt"></i> Log in again
    </a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

