<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$otp = $input['otp'] ?? ($_POST['otp'] ?? '');

if (empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'OTP is required.']);
    exit();
}

if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please request a new OTP.']);
    exit();
}

if ($otp === $_SESSION['reset_otp']) {
    $_SESSION['reset_otp_verified'] = true;
    echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
}
?>
