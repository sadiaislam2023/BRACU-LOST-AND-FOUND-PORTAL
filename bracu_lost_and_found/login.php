<?php
include 'db.php';
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on the role
    if ($_SESSION['role'] == 'admin') {
        header("Location: index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Initialize login message flag
$login_success = false;

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Add username to the query to fetch it
    $sql = "SELECT id, username, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $username, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username; // ✅ store username in session
            $_SESSION['role'] = $role;

            // Set success flag
            $login_success = true;

            // Delay redirect by 2 seconds using header refresh
            if ($role == 'admin') {
                header("Refresh: 2; URL=index.php");
            } else {
                header("Refresh: 2; URL=index.php");
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BRACU Lost and Found Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2>Login to BRACU Lost and Found Portal</h2>

    <?php
    if (isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }

    if ($login_success) {
        echo "<div class='alert alert-success'>
                You are now logged in 
              </div>";
    }
    ?>

    <form action="login.php" method="POST" <?php if ($login_success) echo 'style="display:none;"'; ?>>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

