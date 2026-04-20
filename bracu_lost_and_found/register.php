<?php
include 'db.php';
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $role = '';
    $error = '';
    $success_message = '';

    // Check if the email already exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already registered
        $error = "This email is already registered. Please log in.";
    } else {
        // Assign the role based on email domain
        if ($email === 'sadiaislamdia2003@gmail.com' || $email === 'admin2@bracu.ac.bd' || $email === 'admin3@bracu.ac.bd') {
            $role = 'admin';
        } elseif (strpos($email, '@bracu.ac.bd') !== false) {
            $role = 'faculty';
        } elseif (strpos($email, '@g.bracu.ac.bd') !== false) {
            $role = 'student';
        } else {
            $error = "Please use a valid BRACU email address.";
        }

        // If no errors, insert the user into the database
        if (empty($error)) {
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                // Show success message
                $success_message = "Registration successful! You will be redirected to the login page shortly.";
                // Wait for 1 second and then redirect to login page
                header("refresh:1;url=login.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BRACU Lost and Found Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2>Register for BRACU Lost and Found Portal</h2>

    <?php 
    // Show error message if there is one
    if (isset($error) && !empty($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }

    // Show success message if registration is successful
    if (isset($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    }
    ?>

    <form action="register.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">BRACU Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="form-text">Use your BRACU student or faculty email.</div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



