<?php
session_start();
include("includes/db.php");

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$msg_type = "";

// Check for registration success
if (isset($_GET['success']) && $_GET['success'] == "registered") {
    $message = "Registration successful! Please login with your credentials.";
    $msg_type = "success";
}

// Process login form (POST method - Lesson 10)
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection (Lesson 10)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password (Lesson 9 - PHP functions)
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == "admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "Incorrect password. Please try again.";
            $msg_type = "error";
        }
    } else {
        $message = "No account found with that email address.";
        $msg_type = "error";
    }
    $stmt->close();
}

$page_title = "Login";
include("includes/header.php");
?>

<div class="page-header">
    <h1>Welcome Back</h1>
    <p>Login to manage your bookings</p>
</div>

<div class="form-container">
    <h2>Login</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-<?php echo $msg_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="form-actions">
            <button type="submit" name="login" class="btn btn-primary btn-lg" style="width:100%;">Login</button>
        </div>
    </form>
    <p class="form-link">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include("includes/footer.php"); ?>
