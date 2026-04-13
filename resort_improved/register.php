<?php
session_start();
include("includes/db.php");

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$msg_type = "";

// Process registration form (POST method - Lesson 10)
if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation (Lesson 9 - PHP control structures)
    if (strlen($full_name) < 3) {
        $message = "Full name must be at least 3 characters.";
        $msg_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $msg_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $msg_type = "error";
    } else {
        // Check if email already exists (Prepared statement)
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already registered!";
            $msg_type = "error";
        } else {
            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user (Prepared statement - Lesson 10)
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'guest')");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php?success=registered");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
                $msg_type = "error";
            }
            $stmt->close();
        }
        $check->close();
    }
}

$page_title = "Register";
include("includes/header.php");
?>

<div class="page-header">
    <h1>Create an Account</h1>
    <p>Join Bliss Park Resort and start booking</p>
</div>

<div class="form-container">
    <h2>Register</h2>

    <?php if ($message != "") { ?>
        <div class="alert alert-<?php echo $msg_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST" action="register.php" onsubmit="return validateRegistration()">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" 
                   value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email"
                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="e.g. 0712345678"
                   value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="register" class="btn btn-primary btn-lg" style="width:100%;">Register</button>
        </div>
    </form>
    <p class="form-link">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include("includes/footer.php"); ?>
