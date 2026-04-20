<?php 
session_start();
include 'db.php';  // sets up $conn

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// ✅ Restrict access if user doesn't have an approved claim
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM item_claims WHERE user_id = ? AND status = 'approved'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['count'] == 0) {
    echo "<script>alert('Access denied. You can only view messages after an approved claim.'); window.location.href = 'message.php';</script>";
    exit();
}

// Fetch messages where the logged-in user is sender or receiver
$stmt = $conn->prepare(
    "SELECT m.content, m.created_at,
        CASE
            WHEN m.sender_id = ? THEN u_receiver.username
            ELSE u_sender.username
        END AS other_user
     FROM messages m
     JOIN users u_sender ON m.sender_id = u_sender.id
     JOIN users u_receiver ON m.receiver_id = u_receiver.id
     WHERE m.sender_id = ? OR m.receiver_id = ?
     ORDER BY m.created_at DESC"
);
$stmt->bind_param("iii", $userId, $userId, $userId);
$stmt->execute();
$stmt->bind_result($msgContent, $sentAt, $otherUser);

$messages = [];
while ($stmt->fetch()) {
    $messages[] = [
        'receiver' => $otherUser,
        'content' => $msgContent,
        'sent_at' => $sentAt
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inbox</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <h2>Message Sending History</h2>

  <?php if (empty($messages)): ?>
    <p>You have no messages.</p>
  <?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Other User</th>
        <th>Message</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($messages as $msg): ?>
      <tr>
        <td><?php echo htmlspecialchars($msg['receiver']); ?></td>
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
