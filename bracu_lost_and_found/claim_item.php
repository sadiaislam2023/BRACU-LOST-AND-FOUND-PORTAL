<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure username is set in session
if (!isset($_SESSION['username'])) {
    die("Error: Username not set in session. Please log in again.");
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch all available approved found items
$stmt = $conn->prepare("SELECT id, item_category, item_description, location, date, username AS founder_username, image_url FROM reported_items WHERE status = 'found' AND approved = 'Approved'");
$stmt->execute();
$result = $stmt->get_result();

// Build item image map for JavaScript
$item_images = [];
while ($row = $result->fetch_assoc()) {
    $item_images[$row['id']] = !empty($row['image_url']) ? $row['image_url'] : 'placeholder.jpg';
}
// Re-execute the statement to use it again
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission
$claim_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $claim_message = trim($_POST['claim_message']);
    $contact_info = trim($_POST['contact_info']);
    $proof_file_path = null;

    if (isset($_FILES['proof_document']) && $_FILES['proof_document']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        $file_tmp = $_FILES['proof_document']['tmp_name'];
        $file_name = basename($_FILES['proof_document']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $upload_dir = 'uploads/proofs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_filename = uniqid('proof_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $proof_file_path = $target_path;
            } else {
                $error_message = "Failed to upload the proof of ownership document.";
            }
        } else {
            $error_message = "Invalid file type. Allowed types: PDF, JPG, JPEG, PNG.";
        }
    }

    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO item_claims (item_id, user_id, username, claim_message, contact_info, proof_document, claim_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissss", $item_id, $user_id, $username, $claim_message, $contact_info, $proof_file_path);
        $stmt->execute();
        $claim_success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Item - BRACU Lost and Found Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .item-option {
            display: flex;
            align-items: center;
        }
        .item-option img {
            max-height: 50px;
            margin-right: 10px;
            border-radius: 5px;
        }
        select option {
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Claim Found Item</h2>

    <?php if ($claim_success): ?>
        <div class="alert alert-success text-center">
            Your claim has been submitted! The admin will review it shortly.
        </div>
    <?php elseif (!empty($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="item_id">Select Item to Claim</label>
            <select class="form-control" id="item_id" name="item_id" required>
                <option value="">-- Select an Item --</option>
                <?php while ($item = $result->fetch_assoc()): ?>
                    <?php
                        $display_text = "{ $item['item_id']} - $item['item_category']} - {$item['item_description']} | {$item['location']} | {$item['date']} | Reported by: {$item['founder_username']}";
                    ?>
                    <option value="<?= $item['id'] ?>">
                        <?= htmlspecialchars($display_text) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Preview Image Section -->
        <div class="form-group mb-3 text-center">
            <label>Preview Image:</label><br>
            <img id="itemPreview" src="placeholder.jpg" alt="Item Preview" style="max-height: 150px; border-radius: 8px;">
        </div>

        <div class="form-group mb-3">
            <label for="claim_message">Why do you believe this is yours?</label>
            <textarea class="form-control" id="claim_message" name="claim_message" rows="3" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="contact_info">Your Contact Information</label>
            <input type="text" class="form-control" id="contact_info" name="contact_info" required>
        </div>

        <div class="form-group mb-3">
            <label for="proof_file">Proof of Ownership (optional)</label>
            <input type="file" class="form-control" id="proof_document" name="proof_document">
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Claim</button>
    </form>

    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-link">← Back to Home</a>
    </div>
</div>

<!-- JS Preview Logic -->
<script>
    const items = <?= json_encode($item_images) ?>;
    const select = document.getElementById('item_id');
    const preview = document.getElementById('itemPreview');

    select.addEventListener('change', function() {
        const selectedId = this.value;
        const imgPath = items[selectedId] ? items[selectedId] : 'placeholder.jpg';
        preview.src = imgPath;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






