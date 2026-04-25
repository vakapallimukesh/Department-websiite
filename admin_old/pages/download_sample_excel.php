<?php
session_start();
include "../utils/connect.php";

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized access');
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="points_template.csv"');

echo "Student ID,Points\n";
echo "23B91A0701,10\n";
echo "23B91A0702,-5\n";
echo "23B91A0703,15\n";
echo "23B91A0704,8\n";
echo "23B91A0705,12\n";
?>