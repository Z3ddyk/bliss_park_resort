<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Bliss Park Resort</title>
    <link rel="stylesheet" href="<?php echo isset($is_admin) ? '../' : ''; ?>assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="<?php echo isset($is_admin) ? '../' : ''; ?>index.php" class="logo">
                <span class="logo-icon">🏝️</span> Bliss Park Resort
            </a>
            <button class="nav-toggle" onclick="toggleNav()" aria-label="Toggle navigation">☰</button>
            <ul class="nav-links" id="navLinks">
                <?php if (isset($is_admin)) { ?>
                    <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="manage_rooms.php" class="<?php echo $current_page == 'manage_rooms.php' ? 'active' : ''; ?>">Rooms</a></li>
                    <li><a href="reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">Bookings</a></li>
                    <li><a href="messages.php" class="<?php echo $current_page == 'messages.php' ? 'active' : ''; ?>">Messages</a></li>
                    <li><a href="../logout.php" class="btn-nav btn-logout">Logout</a></li>
                <?php } else { ?>
                    <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="rooms.php" class="<?php echo $current_page == 'rooms.php' ? 'active' : ''; ?>">Rooms</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li><a href="my_bookings.php" class="<?php echo $current_page == 'my_bookings.php' ? 'active' : ''; ?>">My Bookings</a></li>
                    <?php } ?>
                    <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="nav-user">
                            <span>👋 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        </li>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <li><a href="admin/dashboard.php" class="btn-nav btn-admin">Admin</a></li>
                        <?php } ?>
                        <li><a href="logout.php" class="btn-nav btn-logout">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="login.php" class="btn-nav btn-login">Login</a></li>
                        <li><a href="register.php" class="btn-nav btn-register">Register</a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="container">
            <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'info'; ?>">
                <?php echo $_SESSION['message']; ?>
                <span class="alert-close" onclick="this.parentElement.style.display='none'">×</span>
            </div>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
    <?php } ?>
