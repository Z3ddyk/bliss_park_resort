<?php
include("includes/auth.php");
include("includes/db.php");

if (!isset($_GET['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

// Get booking info - ensure it belongs to this user (Prepared statement)
$stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ? AND user_id = ? AND booking_status = 'confirmed'");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    $room_id = $booking['room_id'];

    // Update booking status to cancelled
    $updateBooking = $conn->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE booking_id = ?");
    $updateBooking->bind_param("i", $booking_id);
    $updateBooking->execute();

    // Update room status back to available
    $updateRoom = $conn->prepare("UPDATE rooms SET status = 'available' WHERE room_id = ?");
    $updateRoom->bind_param("i", $room_id);
    $updateRoom->execute();

    $_SESSION['message'] = "Booking #$booking_id has been cancelled successfully.";
    $_SESSION['msg_type'] = "success";
} else {
    $_SESSION['message'] = "Invalid booking or already cancelled.";
    $_SESSION['msg_type'] = "error";
}

header("Location: my_bookings.php");
exit();
?>
