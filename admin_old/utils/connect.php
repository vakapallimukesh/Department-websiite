<?php

// Database configuration - matching main application settings
if ($_SERVER['HTTP_HOST'] === 'csd-csit.page.gd' || strpos($_SERVER['HTTP_HOST'], 'page.gd') !== false) {
    // Live server configuration
    $db_host = 'sql302.infinityfree.com';
    $db_user = 'if0_39923791';
    $db_pass = 'WredXibeqKifLM';
    $db_name = 'if0_39923791_test';
} else {
    // Local server configuration
    $db_host = '127.0.0.1:4306';
    $db_user = 'root';
    $db_pass = 'password';
    $db_name = 'new_sem'; // Changed from 'merge' to '1234' to match main app
}

// Create connection using mysqli
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Set character set to UTF-8
$conn->set_charset("utf8");

// For backward compatibility
$sconn = $conn;
  
?>