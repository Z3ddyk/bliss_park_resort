<?php
/**
 
 */

$password = "admin123"; // Change this to your desired admin password
$hashed = password_hash($password, PASSWORD_DEFAULT);
?>
<!DOCTYPE html>
<html>
<head><title>Password Hash Generator</title></head>
<body style="font-family:monospace;padding:2rem;background:#f5f5f5;">
    <h2>Password Hash Generator</h2>
    <p><strong>Plain Password:</strong> <?php echo htmlspecialchars($password); ?></p>
    <p><strong>Hashed Password:</strong></p>
    <textarea style="width:100%;height:80px;font-size:14px;padding:10px;"><?php echo $hashed; ?></textarea>
    <br><br>
    <p><strong>SQL to update admin:</strong></p>
    <textarea style="width:100%;height:60px;font-size:14px;padding:10px;">UPDATE users SET password='<?php echo $hashed; ?>' WHERE email='admin@blisspark.com';</textarea>
    <br><br>
    <p style="color:red;"><strong>⚠️ IMPORTANT:</strong> Delete this file after generating your password hash!</p>
</body>
</html>
