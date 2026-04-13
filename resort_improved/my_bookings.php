<?php
include("includes/auth.php");
include("includes/db.php");

$user_id = $_SESSION['user_id'];

// Fetch all bookings for this user with room details
$stmt = $conn->prepare("SELECT b.*, r.room_name, rt.type_name, r.price_per_night
                         FROM bookings b
                         JOIN rooms r ON b.room_id = r.room_id
                         JOIN room_types rt ON r.type_id = rt.type_id
                         WHERE b.user_id = ?
                         ORDER BY b.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "My Bookings";
include("includes/header.php");
?>

<div class="page-header">
    <h1>My Bookings</h1>
    <p>View and manage your reservations</p>
</div>

<div class="container section">
    <div style="margin-bottom:1.5rem;">
        <a href="rooms.php" class="btn btn-primary">Book Another Room</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Guests</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Booked On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['check_out'])); ?></td>
                            <td><?php echo $row['guests']; ?></td>
                            <td>KES <?php echo number_format($row['total_price']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $row['booking_status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['booking_status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php if ($row['booking_status'] == 'confirmed') { ?>
                                    <a href="cancel.php?booking_id=<?php echo $row['booking_id']; ?>" 
                                       class="btn btn-danger btn-sm" onclick="return confirmCancel()">Cancel</a>
                                <?php } else { ?>
                                    <span style="color:var(--gray);font-size:0.8rem;">—</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div style="text-align:center;padding:3rem;">
            <p style="font-size:1.2rem;color:var(--gray);">You have no bookings yet.</p>
            <a href="rooms.php" class="btn btn-primary btn-lg" style="margin-top:1rem;">Browse Rooms</a>
        </div>
    <?php } ?>
</div>

<?php include("includes/footer.php"); ?>
