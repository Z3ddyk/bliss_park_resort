<?php
$is_admin = true;
include("../includes/admin_auth.php");
include("../includes/db.php");

// Mark message as read
if (isset($_GET['mark_read'])) {
    $msg_id = intval($_GET['mark_read']);
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE message_id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
}

// Delete message
if (isset($_GET['delete'])) {
    $msg_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
    $_SESSION['message'] = "Message deleted.";
    $_SESSION['msg_type'] = "success";
    header("Location: messages.php");
    exit();
}

// Fetch all messages
$messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
$unreadCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM contact_messages WHERE is_read=0"))['cnt'];

$page_title = "Messages";
include("../includes/header.php");
?>

<div class="page-header">
    <h1>Contact Messages</h1>
    <p><?php echo $unreadCount; ?> unread message(s)</p>
</div>

<div class="container section">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($messages) > 0) { ?>
                    <?php while ($msg = mysqli_fetch_assoc($messages)) { ?>
                        <tr style="<?php echo $msg['is_read'] ? '' : 'font-weight:600;background:#eef5f0;'; ?>">
                            <td><?php echo $msg['message_id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </td>
                            <td><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td>
                                <?php if ($msg['is_read']) { ?>
                                    <span class="badge badge-checked_out">Read</span>
                                <?php } else { ?>
                                    <span class="badge badge-confirmed">New</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if (!$msg['is_read']) { ?>
                                    <a href="messages.php?mark_read=<?php echo $msg['message_id']; ?>" class="btn btn-primary btn-sm">Mark Read</a>
                                <?php } ?>
                                <a href="messages.php?delete=<?php echo $msg['message_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('message')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="8" style="text-align:center;padding:2rem;">No messages yet.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">
        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
