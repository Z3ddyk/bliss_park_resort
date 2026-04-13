<?php
$is_admin = true;
include("../includes/admin_auth.php");
include("../includes/db.php");

$message = "";
$msg_type = "";
$edit_room = null;

// ====== DELETE ROOM ======
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    // Check if room has active bookings
    $checkBookings = $conn->prepare("SELECT COUNT(*) AS cnt FROM bookings WHERE room_id = ? AND booking_status IN ('confirmed','checked_in')");
    $checkBookings->bind_param("i", $delete_id);
    $checkBookings->execute();
    $hasBookings = $checkBookings->get_result()->fetch_assoc()['cnt'];

    if ($hasBookings > 0) {
        $message = "Cannot delete room with active bookings. Cancel bookings first.";
        $msg_type = "error";
    } else {
        $delStmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $delStmt->bind_param("i", $delete_id);
        if ($delStmt->execute()) {
            $message = "Room deleted successfully.";
            $msg_type = "success";
        } else {
            $message = "Failed to delete room.";
            $msg_type = "error";
        }
    }
}

// ====== LOAD ROOM FOR EDITING ======
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $editStmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $editStmt->bind_param("i", $edit_id);
    $editStmt->execute();
    $edit_room = $editStmt->get_result()->fetch_assoc();
}

// ====== ADD NEW ROOM ======
if (isset($_POST['add_room'])) {
    $room_name = trim($_POST['room_name']);
    $type_id = intval($_POST['type_id']);
    $price = floatval($_POST['price_per_night']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, type_id, price_per_night, description, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sidss", $room_name, $type_id, $price, $description, $status);

    if ($stmt->execute()) {
        $message = "Room '$room_name' added successfully!";
        $msg_type = "success";
    } else {
        $message = "Failed to add room. Error: " . $conn->error;
        $msg_type = "error";
    }
}

// ====== UPDATE ROOM ======
if (isset($_POST['update_room'])) {
    $room_id = intval($_POST['room_id']);
    $room_name = trim($_POST['room_name']);
    $type_id = intval($_POST['type_id']);
    $price = floatval($_POST['price_per_night']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms SET room_name = ?, type_id = ?, price_per_night = ?, description = ?, status = ? WHERE room_id = ?");
    $stmt->bind_param("sidssi", $room_name, $type_id, $price, $description, $status, $room_id);

    if ($stmt->execute()) {
        $message = "Room updated successfully!";
        $msg_type = "success";
        $edit_room = null; // Clear edit mode
    } else {
        $message = "Failed to update room.";
        $msg_type = "error";
    }
}

// Fetch all rooms
$rooms = mysqli_query($conn, "SELECT r.*, rt.type_name FROM rooms r JOIN room_types rt ON r.type_id = rt.type_id ORDER BY r.room_id ASC");

// Fetch room types for dropdown
$roomTypes = mysqli_query($conn, "SELECT * FROM room_types ORDER BY type_name");

$page_title = "Manage Rooms";
include("../includes/header.php");
?>

<div class="page-header">
    <h1>Manage Rooms</h1>
    <p>Add, edit, and remove rooms from the system</p>
</div>

<div class="container section">

    <?php if ($message != "") { ?>
        <div class="alert alert-<?php echo $msg_type; ?>">
            <?php echo $message; ?>
            <span class="alert-close" onclick="this.parentElement.style.display='none'">×</span>
        </div>
    <?php } ?>

    <div class="grid-2">
        <!-- Add / Edit Room Form -->
        <div class="form-container" style="max-width:100%;margin:0;">
            <h2><?php echo $edit_room ? 'Edit Room' : 'Add New Room'; ?></h2>

            <form method="POST">
                <?php if ($edit_room) { ?>
                    <input type="hidden" name="room_id" value="<?php echo $edit_room['room_id']; ?>">
                <?php } ?>

                <div class="form-group">
                    <label for="room_name">Room Name</label>
                    <input type="text" id="room_name" name="room_name" placeholder="e.g. Room 103"
                           value="<?php echo $edit_room ? htmlspecialchars($edit_room['room_name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="type_id">Room Type</label>
                    <select id="type_id" name="type_id" required>
                        <option value="">-- Select Type --</option>
                        <?php 
                        mysqli_data_seek($roomTypes, 0);
                        while ($type = mysqli_fetch_assoc($roomTypes)) { ?>
                            <option value="<?php echo $type['type_id']; ?>"
                                <?php echo ($edit_room && $edit_room['type_id'] == $type['type_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['type_name']); ?> (KES <?php echo number_format($type['base_price']); ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="price_per_night">Price Per Night (KES)</label>
                        <input type="number" id="price_per_night" name="price_per_night" step="0.01" min="0"
                               value="<?php echo $edit_room ? $edit_room['price_per_night'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="available" <?php echo ($edit_room && $edit_room['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="booked" <?php echo ($edit_room && $edit_room['status'] == 'booked') ? 'selected' : ''; ?>>Booked</option>
                            <option value="maintenance" <?php echo ($edit_room && $edit_room['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Room description..."><?php echo $edit_room ? htmlspecialchars($edit_room['description']) : ''; ?></textarea>
                </div>
                <div class="form-actions">
                    <?php if ($edit_room) { ?>
                        <button type="submit" name="update_room" class="btn btn-primary" style="width:100%;">Update Room</button>
                        <a href="manage_rooms.php" class="btn btn-outline" style="width:100%;margin-top:0.5rem;display:block;text-align:center;">Cancel Edit</a>
                    <?php } else { ?>
                        <button type="submit" name="add_room" class="btn btn-success" style="width:100%;">Add Room</button>
                    <?php } ?>
                </div>
            </form>
        </div>

        <!-- Rooms Table -->
        <div>
            <h3 style="margin-bottom:1rem;color:var(--primary-dark);">All Rooms (<?php echo mysqli_num_rows($rooms); ?>)</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price/Night</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($room = mysqli_fetch_assoc($rooms)) { ?>
                            <tr>
                                <td><?php echo $room['room_id']; ?></td>
                                <td><?php echo htmlspecialchars($room['room_name']); ?></td>
                                <td><?php echo htmlspecialchars($room['type_name']); ?></td>
                                <td>KES <?php echo number_format($room['price_per_night']); ?></td>
                                <td><span class="badge badge-<?php echo $room['status']; ?>"><?php echo ucfirst($room['status']); ?></span></td>
                                <td>
                                    <a href="manage_rooms.php?edit=<?php echo $room['room_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="manage_rooms.php?delete=<?php echo $room['room_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('room')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top:1.5rem;">
        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
