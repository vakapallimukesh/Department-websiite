<?php
// Check for mysqli extension
if (function_exists('mysqli_connect')) {
    echo "mysqli extension is enabled.<br>";
} else {
    echo "mysqli extension is NOT enabled.<br>";
    exit;
}

// Database configuration
$db_host = 'sql302.infinityfree.com';
$db_user = 'if0_39923791';
$db_pass = 'WredXibeqKifLM';
$db_name = 'if0_39923791_department';

// Attempt to connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check the connection
if (!$conn) {
    echo "Database connection failed: " . mysqli_connect_error() . "<br>";
} else {
    echo "Database connection successful.<br>";
    mysqli_close($conn);
}

phpinfo();
?>