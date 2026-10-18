<?php
session_start();
require_once 'db.php';

// Redirect logged-in users
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success_message = '';

$username = '';
$email = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and validate form inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $plain_password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $plain_password === '') {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($plain_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {

        // Check if the email already exists
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "This email is already registered. Please log in.";
        }

        $stmt->close();

        // Assign a role only if the email is not already registered
        if ($error === '') {

            // Normalize email for role checking
            $normalized_email = strtolower($email);

            // Assign administrator role to designated accounts
            if (
                $normalized_email === 'sadiaislamdia2003@gmail.com' ||
                $normalized_email === 'admin2@bracu.ac.bd' ||
                $normalized_email === 'admin3@bracu.ac.bd'
            ) {
                $role = 'admin';

            } elseif (preg_match('/@bracu\.ac\.bd$/i', $email)) {
                $role = 'faculty';

            } elseif (preg_match('/@g\.bracu\.ac\.bd$/i', $email)) {
                $role = 'student';

            } else {
                $role = '';
                $error = "Please use a valid BRACU email address.";
            }

            // Register the user if all validations pass
            if ($error === '') {

                // Hash the password securely
                $hashed_password = password_hash(
                    $plain_password,
                    PASSWORD_DEFAULT
                );

                if ($hashed_password === false) {
                    $error = "Unable to process your password. Please try again.";
                } else {

                    // Save username in both name and username columns
                    $sql = "INSERT INTO users
                            (name, username, email, password, role)
                            VALUES (?, ?, ?, ?, ?)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "sssss",
                        $username,
                        $username,
                        $email,
                        $hashed_password,
                        $role
                    );

                    if ($stmt->execute()) {
                        $stmt->close();

                        // Redirect after successful registration
                        header("Location: login.php?registered=1");
                        exit();

                    } else {
                        // Handle a duplicate email if a database constraint
                        // prevents simultaneous duplicate registrations
                        if ($conn->errno === 1062) {
                            $error = "This email is already registered. Please log in.";
                        } else {
                            $error = "Registration failed. Please try again.";
                        }

                        $stmt->close();
                    }
                }
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

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    >
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <h2 class="text-center mb-4">
                Register for BRACU Lost and Found Portal
            </h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">

                <div class="mb-3">
                    <label for="username" class="form-label">
                        Username
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">
                        BRACU Email Address
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="254"
                        required
                    >

                    <div class="form-text">
                        Use your BRACU student or faculty email.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >

                    <div class="form-text">
                        Password must contain at least 8 characters.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Register
                </button>

            </form>

            <p class="mt-3 text-center">
                Already have an account?
                <a href="login.php">Login here</a>.
            </p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>



