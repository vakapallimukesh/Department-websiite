<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "new_sem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8
$conn->set_charset("utf8");

// For backward compatibility, allow $sconn to use $conn directly.
$sconn = $conn;
?>

