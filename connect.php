<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "new_sem";

// Disable mysqli error reporting throwing uncaught exceptions in PHP 8.1+
mysqli_report(MYSQLI_REPORT_OFF);

$conn = false;

try {
    // Create connection
    $temp_conn = @new mysqli($servername, $username, $password, $dbname);
    
    // If connection to new_sem fails, attempt fallback to dept database
    if ($temp_conn && $temp_conn->connect_error) {
        $temp_conn = @new mysqli($servername, $username, $password, "dept");
    }
    
    if ($temp_conn && !$temp_conn->connect_error) {
        $conn = $temp_conn;
        $conn->set_charset("utf8");
    }
} catch (Throwable $e) {
    $conn = false;
}

// For backward compatibility, allow $sconn to use $conn directly.
$sconn = $conn;
?>

