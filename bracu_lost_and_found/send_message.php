<?php
// send_message.php
session_start();
include 'db.php';  // sets up $conn

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$senderId = $_SESSION['user_id'];

// Handle form submission
$successMsg = "";
$errorMsg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiverId = intval($_POST['receiver'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if ($receiverId <= 0 || empty($message)) {
        $errorMsg = "Please select a recipient and enter a message.";
    } else {
        // Insert the message using a prepared statement
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $senderId, $receiverId, $message);
        if ($stmt->execute()) {
            $successMsg = "Message sent successfully!";
        } else {
            $errorMsg = "Database error: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Message</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <script>
    function validateForm() {
      const receiver = document.getElementById('receiver').value;
      const message = document.getElementById('message').value.trim();
      if (!receiver || !message) {
        alert("Please select a recipient and enter a message.");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h2>Send Message</h2>

  <?php if ($successMsg): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
  <?php elseif ($errorMsg): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
  <?php endif; ?>

  <form action="send_message.php" method="post" onsubmit="return validateForm()">
    <div class="mb-3">
      <label for="receiver" class="form-label">Recipient</label>
      <select name="receiver" id="receiver" class="form-select" required>
        <option value="">-- Select User --</option>
        <?php
        // Populate dropdown with other users
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE id != ?");
        $stmt->bind_param("i", $senderId);
        $stmt->execute();
        $stmt->bind_result($uid, $uname);
        while ($stmt->fetch()) {
            echo '<option value="'.htmlspecialchars($uid).'">'.htmlspecialchars($uname).'</option>';
        }
        $stmt->close();
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="message" class="form-label">Message</label>
      <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-paper-plane" aria-hidden="true"></i> Send
    </button>
  </form>
</div>

<!-- Bootstrap JS (optional, for dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
