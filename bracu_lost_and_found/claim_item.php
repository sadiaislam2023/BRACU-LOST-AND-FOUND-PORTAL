<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the username is available.
if (!isset($_SESSION['username']) || trim($_SESSION['username']) === '') {
    die("Error: Username not found. Please log in again.");
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];

$claim_success = false;
$error_message = '';
$claim_message = '';
$contact_info = '';
$selected_item_id = 0;
$items = [];
$item_images = [];

// Escape output before displaying it in HTML.
function escape_html($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Fetch approved found items.
$sql = "
    SELECT
        id,
        item_category,
        item_description,
        location,
        date,
        username AS founder_username,
        image_url
    FROM reported_items
    WHERE LOWER(status) = 'found'
      AND approved = 'Approved'
    ORDER BY date DESC, id DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to prepare the item query. Please check your database configuration.");
}

if (!$stmt->execute()) {
    die("Unable to load available items. Please check your database.");
}

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $items[] = $row;

    $item_images[(string) $row['id']] =
        !empty($row['image_url'])
            ? $row['image_url']
            : 'placeholder.jpg';
}

$stmt->close();

// Handle claim form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_item_id = filter_input(
        INPUT_POST,
        'item_id',
        FILTER_VALIDATE_INT
    );

    $claim_message = trim($_POST['claim_message'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');
    $proof_file_path = null;
    $uploaded_file_path = null;

    // Validate the submitted form.
    if (!$selected_item_id || $selected_item_id <= 0) {
        $error_message = "Please select an item to claim.";
    } elseif ($claim_message === '') {
        $error_message = "Please explain why you believe this item belongs to you.";
    } elseif ($contact_info === '') {
        $error_message = "Please provide your contact information.";
    }

    // Verify that the selected item is still available for claiming.
    if (empty($error_message)) {
        $check_stmt = $conn->prepare("
            SELECT id
            FROM reported_items
            WHERE id = ?
              AND LOWER(status) = 'found'
              AND approved = 'Approved'
        ");

        if (!$check_stmt) {
            $error_message = "Unable to verify the selected item.";
        } else {
            $check_stmt->bind_param("i", $selected_item_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows === 0) {
                $error_message = "This item is no longer available for claiming. Please refresh the page.";
            }

            $check_stmt->close();
        }
    }

    // Process the optional proof document.
    if (
        empty($error_message) &&
        isset($_FILES['proof_document']) &&
        $_FILES['proof_document']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['proof_document'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_message = "The proof document could not be uploaded.";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error_message = "The proof document must not exceed 5 MB.";
        } else {
            $original_name = basename($file['name']);
            $extension = strtolower(
                pathinfo($original_name, PATHINFO_EXTENSION)
            );

            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($extension, $allowed_extensions, true)) {
                $error_message = "Invalid file type. Allowed types: PDF, JPG, JPEG and PNG.";
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($file['tmp_name']);

                $allowed_mime_types = [
                    'pdf'  => ['application/pdf'],
                    'jpg'  => ['image/jpeg'],
                    'jpeg' => ['image/jpeg'],
                    'png'  => ['image/png']
                ];

                if (
                    !isset($allowed_mime_types[$extension]) ||
                    !in_array(
                        $mime_type,
                        $allowed_mime_types[$extension],
                        true
                    )
                ) {
                    $error_message = "The uploaded file does not match an allowed file type.";
                } else {
                    $upload_dir = __DIR__ . '/uploads/proofs/';

                    if (
                        !is_dir($upload_dir) &&
                        !mkdir($upload_dir, 0755, true) &&
                        !is_dir($upload_dir)
                    ) {
                        $error_message = "Unable to create the proof upload directory.";
                    } else {
                        $new_filename = bin2hex(random_bytes(16))
                            . '.' . $extension;

                        $absolute_path = $upload_dir . $new_filename;

                        if (
                            move_uploaded_file(
                                $file['tmp_name'],
                                $absolute_path
                            )
                        ) {
                            $proof_file_path =
                                'uploads/proofs/' . $new_filename;

                            $uploaded_file_path = $absolute_path;
                        } else {
                            $error_message = "Failed to save the proof document.";
                        }
                    }
                }
            }
        }
    }

    // Save the claim in the database.
    if (empty($error_message)) {
        $insert_stmt = $conn->prepare("
            INSERT INTO item_claims
                (
                    item_id,
                    user_id,
                    username,
                    claim_message,
                    contact_info,
                    proof_document
                )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$insert_stmt) {
            $error_message = "Unable to prepare your claim. Please try again.";
        } else {
            $insert_stmt->bind_param(
                "iissss",
                $selected_item_id,
                $user_id,
                $username,
                $claim_message,
                $contact_info,
                $proof_file_path
            );

            if ($insert_stmt->execute()) {
                $claim_success = true;
                $claim_message = '';
                $contact_info = '';
                $selected_item_id = 0;
            } else {
                $error_message = "Your claim could not be saved. Please try again.";

                // Remove the uploaded file if the database insert fails.
                if (
                    $uploaded_file_path !== null &&
                    is_file($uploaded_file_path)
                ) {
                    unlink($uploaded_file_path);
                }
            }

            $insert_stmt->close();
        }

        // Clean up the file if preparing the insert failed.
        if (
            $error_message !== '' &&
            $uploaded_file_path !== null &&
            is_file($uploaded_file_path)
        ) {
            unlink($uploaded_file_path);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Claim Item - BRACU Lost and Found Portal</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
        }

        .claim-container {
            max-width: 760px;
            margin: 45px auto;
            padding: 30px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .form-label {
            font-weight: 600;
        }

        #itemPreview {
            max-width: 100%;
            max-height: 220px;
            object-fit: contain;
            border-radius: 8px;
            margin-top: 10px;
        }

        .preview-container {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .submit-btn {
            padding: 11px;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="claim-container">

        <h2 class="text-center mb-4">Claim Found Item</h2>

        <?php if ($claim_success): ?>
            <div class="alert alert-success" role="alert">
                Your claim has been submitted successfully.
                The administrator will review it shortly.
            </div>
        <?php endif; ?>

        <?php if ($error_message !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= escape_html($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="alert alert-info" role="alert">
                No approved found items are currently available for claiming.
                Please check again later.
            </div>
        <?php else: ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="item_id" class="form-label">
                        Select Item to Claim
                    </label>

                    <select
                        class="form-select"
                        id="item_id"
                        name="item_id"
                        required
                    >
                        <option value="">-- Select an Item --</option>

                        <?php foreach ($items as $item): ?>
                            <?php
                            $display_text =
                                $item['id'] . ' - ' .
                                $item['item_category'] . ' - ' .
                                $item['item_description'] . ' | ' .
                                $item['location'] . ' | ' .
                                $item['date'] . ' | Reported by: ' .
                                $item['founder_username'];
                            ?>

                            <option
                                value="<?= (int) $item['id'] ?>"
                                <?= (
                                    (int) $selected_item_id ===
                                    (int) $item['id']
                                ) ? 'selected' : '' ?>
                            >
                                <?= escape_html($display_text) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 text-center">
                    <label class="form-label d-block">
                        Item Image Preview
                    </label>

                    <div class="preview-container">
                        <img
                            id="itemPreview"
                            src="placeholder.jpg"
                            alt="Selected item preview"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="claim_message" class="form-label">
                        Why do you believe this item is yours?
                    </label>

                    <textarea
                        class="form-control"
                        id="claim_message"
                        name="claim_message"
                        rows="4"
                        maxlength="5000"
                        required
                    ><?= escape_html($claim_message) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="contact_info" class="form-label">
                        Your Contact Information
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="contact_info"
                        name="contact_info"
                        maxlength="255"
                        value="<?= escape_html($contact_info) ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="proof_document" class="form-label">
                        Proof of Ownership (Optional)
                    </label>

                    <input
                        type="file"
                        class="form-control"
                        id="proof_document"
                        name="proof_document"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >

                    <div class="form-text">
                        Accepted formats: PDF, JPG, JPEG and PNG.
                        Maximum size: 5 MB.
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 submit-btn"
                >
                    Submit Claim
                </button>

            </form>

        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-link">
                &larr; Back to Home
            </a>
        </div>

    </div>
</div>

<script>
    const itemImages = <?= json_encode(
        $item_images,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const itemSelect = document.getElementById('item_id');
    const itemPreview = document.getElementById('itemPreview');

    function updateItemPreview() {
        if (!itemSelect || !itemPreview) {
            return;
        }

        const selectedId = itemSelect.value;
        const imagePath = selectedId && itemImages[selectedId]
            ? itemImages[selectedId]
            : 'placeholder.jpg';

        itemPreview.src = imagePath;
    }

    if (itemSelect) {
        itemSelect.addEventListener('change', updateItemPreview);
        updateItemPreview();
    }
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>

