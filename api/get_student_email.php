<?php
header('Content-Type: application/json');

include '../connect.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$student_id = $_GET['student_id'] ?? '';

if (empty($student_id)) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT email FROM students WHERE student_id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'email' => $row['email']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No student found with this ID.']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}

mysqli_close($conn);
?>
