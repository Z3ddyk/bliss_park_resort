<?php
$page_title = "Welcome";
include("includes/header.php");
include("includes/db.php");

// Fetch featured rooms
$query = "SELECT r.*, rt.type_name, rt.capacity 
          FROM rooms r 
          JOIN room_types rt ON r.type_id = rt.type_id 
          WHERE r.status = 'available' 
          ORDER BY r.price_per_night ASC LIMIT 6";
$result = mysqli_query($conn, $query);
?>

<!-- Hero Section -->
<section class="hero">
    <h1>Welcome to Bliss Park Resort</h1>
    <p>Experience luxury and serenity at Kenya's premier resort destination. Relax in our world-class rooms with stunning views.</p>
    <a href="rooms.php" class="btn btn-white btn-lg">Explore Our Rooms</a>
</section>

<!-- Features Section -->
<div class="container section">
    <h2 class="section-title">Why Choose Us?</h2>
    <p class="section-subtitle">Unforgettable experiences await you</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="icon">🏊</div>
            <h3>Infinity Pool</h3>
            <p>Swim in our stunning infinity pool overlooking the ocean.</p>
        </div>
        <div class="feature-card">
            <div class="icon">🍽️</div>
            <h3>Fine Dining</h3>
            <p>Savor gourmet cuisine from our award-winning chefs.</p>
        </div>
        <div class="feature-card">
            <div class="icon">💆</div>
            <h3>Luxury Spa</h3>
            <p>Rejuvenate your body and soul at our full-service spa.</p>
        </div>
        <div class="feature-card">
            <div class="icon">🌅</div>
            <h3>Beach Access</h3>
            <p>Private beach access with complimentary sun loungers.</p>
        </div>
    </div>
</div>

<!-- Featured Rooms -->
<div class="container section">
    <h2 class="section-title">Our Rooms</h2>
    <p class="section-subtitle">Handpicked accommodations for every guest</p>
    <div class="grid-3">
        <?php while ($room = mysqli_fetch_assoc($result)) { ?>
            <div class="card">
                <div class="card-img">🛏️</div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                    <p><?php echo htmlspecialchars($room['type_name']); ?> · <?php echo $room['capacity']; ?> Guest(s)</p>
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
                        <span class="btn btn-sm" style="opacity:0.5;cursor:default;">Unavailable</span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div style="text-align:center;margin-top:2rem;">
        <a href="rooms.php" class="btn btn-primary btn-lg">View All Rooms</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>
