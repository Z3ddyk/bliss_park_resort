<?php
// Database configuration 
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "resort_management";

// Create connection using MySQLi 
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// 
$conn->set_charset("utf8");
?>
