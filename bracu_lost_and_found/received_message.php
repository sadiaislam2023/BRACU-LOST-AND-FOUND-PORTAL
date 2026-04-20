<?php
session_start();
include 'db.php';  // sets up $conn

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Check if the logged-in user is an admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// If the user is not an admin, allow access to received messages
if ($role !== 'admin') {
    // Fetch messages where the logged-in user is the receiver
    $stmt = $conn->prepare(
        "SELECT m.content, m.created_at, u_sender.username AS sender
         FROM messages m
         JOIN users u_sender ON m.sender_id = u_sender.id
         WHERE m.receiver_id = ?
         ORDER BY m.created_at DESC"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($msgContent, $sentAt, $sender);

    $messages = [];
    while ($stmt->fetch()) {
        $messages[] = [
            'sender' => $sender,
            'content' => $msgContent,
            'sent_at' => $sentAt
        ];
    }
    $stmt->close();
} else {
    // Redirect admins to a different page or provide a message
    echo "<script>alert('Admins cannot access the received messages page.'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Received Messages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h2>Received Messages</h2>

  <?php if (empty($messages)): ?>
    <p>You have no received messages.</p>
  <?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Sender</th>
        <th>Message</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($messages as $msg): ?>
      <tr>
        <td><?php echo htmlspecialchars($msg['sender']); ?></td>
        <td><?php echo htmlspecialchars($msg['content']); ?></td>
        <td><?php echo htmlspecialchars($msg['sent_at']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
