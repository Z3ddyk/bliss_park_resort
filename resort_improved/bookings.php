<?php
include("includes/auth.php");
include("includes/db.php");

if (!isset($_GET['room_id'])) {
    header("Location: rooms.php");
    exit();
}

$room_id = intval($_GET['room_id']);

// Get room details using prepared statement
$stmt = $conn->prepare("SELECT r.*, rt.type_name, rt.capacity 
                         FROM rooms r 
                         JOIN room_types rt ON r.type_id = rt.type_id 
                         WHERE r.room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$roomResult = $stmt->get_result();
$room = $roomResult->fetch_assoc();

if (!$room || $room['status'] != 'available') {
    $_SESSION['message'] = "This room is not available for booking.";
    $_SESSION['msg_type'] = "error";
    header("Location: rooms.php");
    exit();
}

$message = "";
$msg_type = "";

// Process booking form (POST method - Lesson 10)
if (isset($_POST['book'])) {
    $user_id = $_SESSION['user_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guests = intval($_POST['guests']);
    $special_requests = trim($_POST['special_requests']);

    // Calculate nights and total price
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $interval = $date1->diff($date2);
    $nights = $interval->days;

    if ($nights <= 0) {
        $message = "Check-out date must be after check-in date!";
        $msg_type = "error";
    } elseif ($guests > $room['capacity']) {
        $message = "This room only accommodates " . $room['capacity'] . " guest(s).";
        $msg_type = "error";
    } else {
        $total_price = $nights * $room['price_per_night'];

        // Check for date conflicts
        $conflictStmt = $conn->prepare("SELECT booking_id FROM bookings 
                                         WHERE room_id = ? AND booking_status IN ('confirmed','checked_in')
                                         AND check_in < ? AND check_out > ?");
        $conflictStmt->bind_param("iss", $room_id, $check_out, $check_in);
        $conflictStmt->execute();
        $conflicts = $conflictStmt->get_result();

        if ($conflicts->num_rows > 0) {
            $message = "This room is already booked for the selected dates.";
            $msg_type = "error";
        } else {
            // Insert booking (Prepared statement)
            $bookStmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out, guests, special_requests, total_price, booking_status) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')");
            $bookStmt->bind_param("iissisd", $user_id, $room_id, $check_in, $check_out, $guests, $special_requests, $total_price);

            if ($bookStmt->execute()) {
                // Update room status
                $updateStmt = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE room_id = ?");
                $updateStmt->bind_param("i", $room_id);
                $updateStmt->execute();

                $_SESSION['message'] = "Booking confirmed! Total: KES " . number_format($total_price) . " for $nights night(s).";
                $_SESSION['msg_type'] = "success";
                header("Location: my_bookings.php");
                exit();
            } else {
                $message = "Booking failed! Please try again.";
                $msg_type = "error";
            }
        }
    }
}

$page_title = "Book " . $room['room_name'];
include("includes/header.php");
?>

<div class="page-header">
    <h1>Book Your Stay</h1>
    <p>Complete the form below to reserve your room</p>
</div>

<div class="container section">
    <div class="grid-2">
        <!-- Room Details Card -->
        <div class="card">
            <div class="card-img" style="height:250px;">🛏️</div>
            <div class="card-body">
                <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($room['type_name']); ?></p>
                <p><strong>Capacity:</strong> <?php echo $room['capacity']; ?> guest(s)</p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
                <div class="card-price">KES <?php echo number_format($room['price_per_night']); ?> <small>/night</small></div>
                <input type="hidden" id="price_per_night" value="<?php echo $room['price_per_night']; ?>">
            </div>
        </div>

        <!-- Booking Form -->
        <div class="form-container" style="max-width:100%;margin:0;">
            <h2>Reservation Details</h2>

            <?php if ($message != "") { ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST" onsubmit="return validateBooking()">
                <div class="form-row">
                    <div class="form-group">
                        <label for="check_in">Check-in Date</label>
                        <input type="date" id="check_in" name="check_in" required onchange="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="check_out">Check-out Date</label>
                        <input type="date" id="check_out" name="check_out" required onchange="calculateTotal()">
                    </div>
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <select id="guests" name="guests" required>
                        <?php for ($i = 1; $i <= $room['capacity']; $i++) { ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest(s)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="special_requests">Special Requests (Optional)</label>
                    <textarea id="special_requests" name="special_requests" placeholder="Any special requirements?"></textarea>
                </div>

                <!-- Dynamic Price Display (JavaScript - Lesson 8) -->
                <div id="price_display" style="background:var(--gray-light);padding:1rem;border-radius:var(--radius);margin:1rem 0;text-align:center;font-size:1.1rem;">
                    Select dates to see total price
                </div>

                <div class="form-actions">
                    <button type="submit" name="book" class="btn btn-primary btn-lg" style="width:100%;">Confirm Booking</button>
                </div>
            </form>
            <p class="form-link"><a href="rooms.php">&larr; Back to Rooms</a></p>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
