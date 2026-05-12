<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

include '../connect.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$student_id = $input['student_id'] ?? ($_POST['student_id'] ?? '');

if (empty($student_id)) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required.']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT email, name FROM students WHERE student_id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $email = $row['email'];
        $name = $row['name'];
        
        // Generate a 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Save in session
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_student_id'] = $student_id;
        $_SESSION['reset_otp_verified'] = false;
        
        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'srkrcsdcsitleavemanagement@gmail.com';
            $mail->Password   = 'krkuryiibmblbmlm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('srkrcsdcsitleavemanagement@gmail.com', 'Department Portal');
            $mail->addAddress($email, $name);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body    = "Hello $name,<br><br>Your OTP for password reset is: <b>$otp</b><br><br>Please enter this OTP to reset your password. Do not share it with anyone.<br><br>Regards,<br>Department Portal";

            $mail->send();
            
            // Mask the email for response
            $parts = explode('@', $email);
            $masked_email = substr($parts[0], 0, 3) . '***@' . $parts[1];
            
            echo json_encode(['success' => true, 'message' => "OTP sent successfully to $masked_email"]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No student found with this ID.']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}

mysqli_close($conn);
?>
