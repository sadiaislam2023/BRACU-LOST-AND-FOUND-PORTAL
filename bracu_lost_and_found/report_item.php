<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get username from session (assuming you store it during login)
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_category = $_POST['item_category'];
    $item_description = $_POST['item_description'];
    $status = $_POST['status'];
    $location = $_POST['location'];
    $location_details = $_POST['location_details'];
    $date = $_POST['date'];
    $reporter_details = $_POST['reporter_details'];
    $user_id = $_SESSION['user_id'];

    $image_path = '';
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image_url']['name']);
        $image_tmp = $_FILES['image_url']['tmp_name'];
        $image_path = 'uploads/reporting document/' . $image_name;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        move_uploaded_file($image_tmp, $image_path);
    }
    $approved = 'Pending';
    $stmt = $conn->prepare("INSERT INTO reported_items (user_id, username, item_category, item_description, status, location, location_details, date, reporter_details,image_url,approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $user_id, $username, $item_category, $item_description, $status, $location, $location_details, $date, $reporter_details, $image_path,  $approved);
    $stmt->execute();

    echo "<div class='alert alert-success text-center'>Item reported successfully! It will be reviewed by an admin.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Item - BRACU Lost and Found Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center">Report Lost/Found Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
        </div>
        <div class="form-group mb-3">
            <label for="item_category">Item Category</label>
            <select class="form-control" id="item_category" name="item_category" required>
                <option value="Cards and Documents">Cards and Documents</option>
                <option value="Bags & Wallets">Bags & Wallets</option>
                <option value="Electronics & Gadgets">Electronics & Gadgets</option>
                <option value="Accessories">Accessories</option>
                <option value="Clothing">Clothing</option>
                <option value="Keys">Keys</option>
                <option value="Vehicles">Vehicles</option>
                <option value="Others">Others</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="item_description">Item Description</label>
            <textarea class="form-control" id="item_description" name="item_description" rows="3" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Lost">Lost</option>
                <option value="Found">Found</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="location">Location</label>
            <select class="form-control" id="location" name="location" required>
                <option value="Library">Library</option>
                <option value="Cafeteria">Cafeteria</option>
                <option value="Rooftop">Rooftop</option>
                <option value="Classroom">Classroom</option>
                <option value="Lab">Lab</option>
                <option value="Multipurpose Hall">Multipurpose Hall</option>
                <option value="Auditorium">Auditorium</option>
                <option value="Others">Others</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="location_details">Location Details</label>
            <input type="text" class="form-control" id="location_details" name="location_details" required>
        </div>
        <div class="form-group mb-3">
            <label for="date">Date of Loss/Found</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group mb-3">
            <label for="reporter_details">Reporter Details</label>
            <textarea class="form-control" id="reporter_details" name="reporter_details" rows="3" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="image_url">Item Image</label>
            <input type="file" class="form-control" id="image_url" name="image_url">
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit Report</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

