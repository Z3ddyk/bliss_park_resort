<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include("includes/db.php");

$message = "";
$msg_type = "";

// Process contact form 
if (isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $msg = trim($_POST['message']);

    // Server-side validation
    if (strlen($name) < 2 || strlen($email) < 5 || strlen($msg) < 10) {
        $message = "Please fill in all fields properly.";
        $msg_type = "error";
    } else {
        // Insert message into database 
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $msg);

        if ($stmt->execute()) {
            $message = "Thank you! Your message has been sent successfully. We'll get back to you soon.";
            $msg_type = "success";
            // Clear form values
            $name = $email = $subject = $msg = "";
        } else {
            $message = "Failed to send message. Please try again.";
            $msg_type = "error";
        }
        $stmt->close();
    }
}

$page_title = "Contact Us";
include("includes/header.php");
?>

<div class="page-header">
    <h1>Contact Us</h1>
    <p>We'd love to hear from you</p>
</div>

<div class="container section">
    <div class="grid-2">
        <!-- Contact Form -->
        <div class="form-container" style="max-width:100%;margin:0;">
            <h2>Send Us a Message</h2>

            <?php if ($message != "") { ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST" action="contact.php" onsubmit="return validateContact()">
                <div class="form-group">
                    <label for="contact_name">Your Name</label>
                    <input type="text" id="contact_name" name="name" placeholder="Enter your full name"
                           value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact_email">Email Address</label>
                    <input type="email" id="contact_email" name="email" placeholder="Enter your email"
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="What is this about?"
                           value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="contact_message">Message</label>
                    <textarea id="contact_message" name="message" placeholder="Type your message here..." required><?php echo isset($msg) ? htmlspecialchars($msg) : ''; ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" name="send_message" class="btn btn-primary btn-lg" style="width:100%;">Send Message</button>
                </div>
            </form>
        </div>

        <!-- Contact Info -->
        <div>
            <div class="card" style="padding:2rem;margin-bottom:1.5rem;">
                <h3 style="color:var(--primary-dark);margin-bottom:1rem;">Get In Touch</h3>
                <p style="margin-bottom:0.8rem;">📍 <strong>Address:</strong> Diani Beach Road, Kwale County, Kenya</p>
                <p style="margin-bottom:0.8rem;">📞 <strong>Phone:</strong> +254 700 123 456</p>
                <p style="margin-bottom:0.8rem;">📧 <strong>Email:</strong> info@blisspark.com</p>
                <p style="margin-bottom:0.8rem;">🕐 <strong>Reception:</strong> Open 24/7</p>
            </div>
            <div class="card" style="padding:2rem;">
                <h3 style="color:var(--primary-dark);margin-bottom:1rem;">Business Hours</h3>
                <p style="margin-bottom:0.5rem;">🍽️ <strong>Restaurant:</strong> 6:30 AM - 10:30 PM</p>
                <p style="margin-bottom:0.5rem;">🏊 <strong>Pool:</strong> 7:00 AM - 8:00 PM</p>
                <p style="margin-bottom:0.5rem;">💆 <strong>Spa:</strong> 9:00 AM - 7:00 PM</p>
                <p style="margin-bottom:0.5rem;">🏋️ <strong>Gym:</strong> 6:00 AM - 9:00 PM</p>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
