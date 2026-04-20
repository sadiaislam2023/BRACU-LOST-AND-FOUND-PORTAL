<?php
session_start();
include 'navbar.php';  // Include the navbar.php file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRACU Lost and Found Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero-section {
            background: url('img/hero-bg.jpg') center center/cover no-repeat;
            background-blend-mode: overlay;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 100px 20px;
            border-radius: 20px;
        }
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="container mt-4">
    <div class="hero-section text-center">
        <h1 class="display-4 fw-bold">BRACU LOST AND FOUND PORTAL</h1>
        <p class="lead">Report, search, claim, and recover your lost items on campus with ease!</p>

        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
            <!-- For non-admin users -->
            <a href="report_item.php" class="btn btn-primary btn-lg m-2">
                <i class="fas fa-upload"></i> Report an Item
            </a>
            <a href="claim_item.php" class="btn btn-success btn-lg m-2">
                <i class="fas fa-hand-paper"></i> Request to Claim
            </a>
            <a href="received_message.php" class="btn btn-info btn-lg m-2">
                <i class="fas fa-envelope"></i> View Received Messages
            </a>
            <a href="search_items.php" class="btn btn-warning btn-lg m-2">
                <i class="fas fa-search"></i> View Reported Items
            </a>
        <?php else: ?>
            <!-- For admins only -->
            <a href="admin_items.php" class="btn btn-dark btn-lg m-2">
                <i class="fas fa-list"></i> All Reported Items
            </a>
            <a href="admin_claim_items.php" class="btn btn-secondary btn-lg m-2">
                <i class="fas fa-check-circle"></i> All Claim Requests
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Features Section -->
<div class="container my-5 text-center">
    <h2 class="mb-4 fw-bold text-primary">Key Features</h2>
    <div class="row">
        <div class="col-md-4">
            <i class="fas fa-upload fa-2x text-primary mb-2"></i>
            <h5>Easy Reporting</h5>
            <p>Quickly report lost or found items with full details.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-search fa-2x text-success mb-2"></i>
            <h5>Smart Search</h5>
            <p>Find items using filters like category or location.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-shield-alt fa-2x text-danger mb-2"></i>
            <h5>Secure Claims</h5>
            <p>Claims are verified by admins for your safety.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5 rounded-3 mx-3">
    <p class="mb-0">&copy; 2025 BRACU LOST AND FOUND PORTAL. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



