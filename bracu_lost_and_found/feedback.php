<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_text = $_POST['message'];
    $user_id = $_SESSION['user_id'];  // The ID of the user submitting the feedback

    // Insert the feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $feedback_text);
    $stmt->execute();

    // Redirect to a thank you page or back to the homepage
    header("Location: thank_you.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - BRACU Lost and Found Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">BRACU Lost and Found Portal</a>
</nav>

<!-- Feedback Form -->
<div class="container mt-5">
    <h2 class="text-center">Submit Your Feedback</h2>

    <form method="POST">
        <div class="form-group">
            <textarea class="form-control" name="message" rows="4" placeholder="Write your feedback here..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit Feedback</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
