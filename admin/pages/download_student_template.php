<?php
session_start();
include "../utils/connect.php";

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized access');
}

$format = $_GET['format'] ?? 'csv';

if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="student_template.xls"');
    
    echo "Student ID\tName\tEmail\tBranch\tSection\tClass ID\tHouse ID\tPassword\n";
    echo "23B91A0701\tJohn Doe\tjohn.doe@srkrec.edu.in\tCSD\tA\t1\t1\t23B91A0701\n";
    echo "23B91A0702\tJane Smith\tjane.smith@srkrec.edu.in\tCSIT\tB\t2\t2\t23B91A0702\n";
    
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="student_template.csv"');
    
    echo "Student ID,Name,Email,Branch,Section,Class ID,House ID,Password\n";
    echo "23B91A0701,John Doe,john.doe@srkrec.edu.in,CSD,A,1,1,23B91A0701\n";
    echo "23B91A0702,Jane Smith,jane.smith@srkrec.edu.in,CSIT,B,2,2,23B91A0702\n";
}
?>