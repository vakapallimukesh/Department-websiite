<?php
session_start();

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized access');
}

$format = $_GET['format'] ?? 'csv';

if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="participants_template.xls"');
    
    echo "Student ID\tPoints\n";
    echo "23B91A0701\t10\n";
    echo "23B91A0702\t8\n";
    echo "23B91A0703\t5\n";
    
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="participants_template.csv"');
    
    echo "Student ID,Points\n";
    echo "23B91A0701,10\n";
    echo "23B91A0702,8\n";
    echo "23B91A0703,5\n";
}
?>