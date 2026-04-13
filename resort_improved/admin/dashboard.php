<?php
$is_admin = true;
include("../includes/admin_auth.php");
include("../includes/db.php");

// Fetch real statistics from database
$totalRooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM rooms"))['total'];
$availableRooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM rooms WHERE status='available'"))['total'];
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))['total'];
$activeBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE booking_status='confirmed'"))['total'];
$totalGuests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='guest'"))['total'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE booking_status IN ('confirmed','checked_in','checked_out')"))['total'];
$unreadMessages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact_messages WHERE is_read=0"))['total'];

// Recent bookings
$recentBookings = mysqli_query($conn, "SELECT b.*, u.full_name, r.room_name 
                                        FROM bookings b 
                                        JOIN users u ON b.user_id = u.user_id 
                                        JOIN rooms r ON b.room_id = r.room_id 
                                        ORDER BY b.created_at DESC LIMIT 5");

$page_title = "Admin Dashboard";
include("../includes/header.php");
?>

<div class="page-header">
    <h1>Admin Dashboard</h1>
    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
</div>

<div class="container section">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Rooms</h4>
            <div class="stat-number"><?php echo $totalRooms; ?></div>
            <small style="color:var(--gray);"><?php echo $availableRooms; ?> available</small>
        </div>
        <div class="stat-card bookings">
            <h4>Bookings</h4>
            <div class="stat-number"><?php echo $totalBookings; ?></div>
            <small style="color:var(--gray);"><?php echo $activeBookings; ?> active</small>
        </div>
        <div class="stat-card guests">
            <h4>Registered Guests</h4>
            <div class="stat-number"><?php echo $totalGuests; ?></div>
        </div>
        <div class="stat-card revenue">
            <h4>Total Revenue</h4>
            <div class="stat-number">KES <?php echo number_format($totalRevenue); ?></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-actions" style="margin-top:2rem;">
        <a href="manage_rooms.php" class="btn btn-primary">Manage Rooms</a>
        <a href="reports.php" class="btn btn-secondary">All Bookings</a>
        <a href="messages.php" class="btn btn-outline">
            Messages <?php if ($unreadMessages > 0) echo "($unreadMessages new)"; ?>
        </a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Recent Bookings Table -->
    <h3 style="margin-top:2rem;color:var(--primary-dark);">Recent Bookings</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($recentBookings)) { ?>
                    <tr>
                        <td>#<?php echo $row['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['check_out'])); ?></td>
                        <td>KES <?php echo number_format($row['total_price']); ?></td>
                        <td><span class="badge badge-<?php echo $row['booking_status']; ?>"><?php echo ucfirst(str_replace('_',' ',$row['booking_status'])); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php } ?>
                <?php if (mysqli_num_rows($recentBookings) == 0) { ?>
                    <tr><td colspan="8" style="text-align:center;">No bookings yet.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
