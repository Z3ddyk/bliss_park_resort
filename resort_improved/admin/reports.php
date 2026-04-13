<?php
$is_admin = true;
include("../includes/admin_auth.php");
include("../includes/db.php");

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT b.*, u.full_name, u.email, u.phone, r.room_name, rt.type_name, r.price_per_night
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN rooms r ON b.room_id = r.room_id
          JOIN room_types rt ON r.type_id = rt.type_id";

if ($status_filter != '') {
    $query .= " WHERE b.booking_status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}
$query .= " ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);

// Summary stats
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE booking_status IN ('confirmed','checked_in','checked_out')"))['total'];

$page_title = "Booking Reports";
include("../includes/header.php");
?>

<div class="page-header">
    <h1>Booking Reports</h1>
    <p>View and manage all guest bookings</p>
</div>

<div class="container section">
    <!-- Filter Bar -->
    <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:end;margin-bottom:1.5rem;">
        <a href="reports.php" class="btn <?php echo $status_filter == '' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">All</a>
        <a href="reports.php?status=confirmed" class="btn <?php echo $status_filter == 'confirmed' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Confirmed</a>
        <a href="reports.php?status=checked_in" class="btn <?php echo $status_filter == 'checked_in' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Checked In</a>
        <a href="reports.php?status=checked_out" class="btn <?php echo $status_filter == 'checked_out' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Checked Out</a>
        <a href="reports.php?status=cancelled" class="btn <?php echo $status_filter == 'cancelled' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Cancelled</a>
        <span style="margin-left:auto;font-weight:600;color:var(--success);">Total Revenue: KES <?php echo number_format($totalRevenue); ?></span>
    </div>

    <!-- Bookings Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guest Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>#<?php echo $row['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['check_out'])); ?></td>
                            <td>KES <?php echo number_format($row['total_price']); ?></td>
                            <td><span class="badge badge-<?php echo $row['booking_status']; ?>"><?php echo ucfirst(str_replace('_',' ',$row['booking_status'])); ?></span></td>
                            <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="11" style="text-align:center;padding:2rem;">No bookings found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">
        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
