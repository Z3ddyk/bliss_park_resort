<?php
// Database configuration - Update these for your XAMPP setup
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "resort_management";

// Create connection using MySQLi (as taught in Lesson 10)
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to utf8 for proper character handling
$conn->set_charset("utf8");
?>
