<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

include '../connect.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$new_password = $input['new_password'] ?? ($_POST['new_password'] ?? '');

if (empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'New password is required.']);
    exit();
}

if (!isset($_SESSION['reset_otp_verified']) || $_SESSION['reset_otp_verified'] !== true || !isset($_SESSION['reset_student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized request.']);
    exit();
}

$student_id = $_SESSION['reset_student_id'];

// Hash the password - assuming plaintext match logic is migrating to password_hash
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$update_stmt = mysqli_prepare($conn, "UPDATE students SET password = ? WHERE student_id = ?");
if ($update_stmt) {
    mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $student_id);
    if (mysqli_stmt_execute($update_stmt)) {
        
        // Fetch student details to auto-login
        $stmt = mysqli_prepare($conn, "
            SELECT s.student_id, s.name, s.email, s.class_id, s.branch, s.section, s.hid,
                   c.year, c.semester, c.academic_year
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.class_id
            WHERE s.student_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = $row['student_id'];
            $_SESSION['student_name'] = $row['name'];
            $_SESSION['student_email'] = $row['email'];
            $_SESSION['student_class_id'] = $row['class_id'];
            $_SESSION['student_branch'] = $row['branch'];
            $_SESSION['student_section'] = $row['section'];
            $_SESSION['student_hid'] = $row['hid'];
            $_SESSION['student_year'] = $row['year'];
            $_SESSION['student_semester'] = $row['semester'];
            $_SESSION['student_academic_year'] = $row['academic_year'];
        }
        
        // Clear reset session variables
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_student_id']);
        unset($_SESSION['reset_otp_verified']);
        
        echo json_encode(['success' => true, 'message' => 'Password reset successfully. Logging you in...']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
    }
    mysqli_stmt_close($update_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}

mysqli_close($conn);
?>
