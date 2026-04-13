<?php
$page_title = "Our Rooms";
if (session_status() === PHP_SESSION_NONE) session_start();
include("includes/db.php");

// Auto-update rooms: mark as available if booking checkout has passed
$today = date('Y-m-d');
$updateRooms = $conn->prepare("UPDATE rooms r JOIN bookings b ON r.room_id = b.room_id 
                                SET r.status = 'available' 
                                WHERE b.check_out < ? AND b.booking_status = 'confirmed'");
$updateRooms->bind_param("s", $today);
$updateRooms->execute();

// Filter by room type if selected
$filter = isset($_GET['type']) ? $_GET['type'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT r.*, rt.type_name, rt.capacity 
          FROM rooms r 
          JOIN room_types rt ON r.type_id = rt.type_id 
          WHERE 1=1";
$params = [];
$types = "";

if ($filter != '') {
    $query .= " AND r.type_id = ?";
    $params[] = $filter;
    $types .= "i";
}
if ($status_filter != '') {
    $query .= " AND r.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY r.price_per_night ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get room types for filter dropdown
$typesResult = mysqli_query($conn, "SELECT * FROM room_types ORDER BY base_price ASC");

include("includes/header.php");
?>

<div class="page-header">
    <h1>Our Rooms</h1>
    <p>Find the perfect room for your stay</p>
</div>

<div class="container section">
    <!-- Filters -->
    <form method="GET" action="rooms.php" style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;align-items:end;">
        <div class="form-group" style="margin:0;">
            <label>Room Type</label>
            <select name="type">
                <option value="">All Types</option>
                <?php while ($type = mysqli_fetch_assoc($typesResult)) { ?>
                    <option value="<?php echo $type['type_id']; ?>" <?php echo $filter == $type['type_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type['type_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="booked" <?php echo $status_filter == 'booked' ? 'selected' : ''; ?>>Booked</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="rooms.php" class="btn btn-outline">Clear</a>
    </form>

    <!-- Room Cards Grid -->
    <div class="grid-3">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($room = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <div class="card-img">🛏️</div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                        <p><strong><?php echo htmlspecialchars($room['type_name']); ?></strong> · Capacity: <?php echo $room['capacity']; ?> guest(s)</p>
                        <p><?php echo htmlspecialchars($room['description']); ?></p>
                        <div class="card-price">KES <?php echo number_format($room['price_per_night']); ?> <small>/night</small></div>
                    </div>
                    <div class="card-footer">
                        <span class="badge badge-<?php echo $room['status']; ?>"><?php echo ucfirst($room['status']); ?></span>
                        <?php if ($room['status'] == 'available') { ?>
                            <?php if (isset($_SESSION['user_id'])) { ?>
                                <a href="bookings.php?room_id=<?php echo $room['room_id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                            <?php } else { ?>
                                <a href="login.php" class="btn btn-outline btn-sm">Login to Book</a>
                            <?php } ?>
                        <?php } else { ?>
                            <span style="color:var(--gray);font-size:0.85rem;">Currently Occupied</span>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p style="text-align:center;grid-column:1/-1;padding:3rem;">No rooms found matching your criteria.</p>
        <?php } ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
