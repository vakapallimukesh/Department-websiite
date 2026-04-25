<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['faculty_logged_in']) || !$_SESSION['faculty_logged_in']) {
    header('Location: login.php');
    exit();
}

include './connect.php';

// Get parameters from URL
$section_id = $_GET['section'] ?? '';
$attendance_date = $_GET['date'] ?? date('Y-m-d'); // Default to today if not provided
$attendance_session = $_GET['session'] ?? 'forenoon'; // Default to forenoon if not provided

// Validate date - only allow today's date
$today = date('Y-m-d');
if ($attendance_date !== $today) {
    // Redirect to today's date
    header("Location: attendance_marking.php?section=$section_id&date=$today&session=$attendance_session");
    exit();
}

// Handle session change from form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_session'])) {
    $new_session = $_POST['new_session'] ?? 'forenoon';
    $new_date = $_POST['new_date'] ?? $attendance_date;
    
    // Ensure new_date is today
    if ($new_date !== $today) {
        $new_date = $today;
    }
    
    $attendance_session = $new_session;
    $attendance_date = $new_date;
    
    // Redirect to update URL parameters
    header("Location: attendance_marking.php?section=$section_id&date=$attendance_date&session=$attendance_session");
    exit();
}

// Validate that faculty has access to this section
$faculty_id = $_SESSION['faculty_id'] ?? null;
$faculty_sections = $_SESSION['faculty_sections'] ?? '';
$assigned_sections = array_filter(array_map('trim', explode(',', $faculty_sections)));

if (!in_array($section_id, $assigned_sections)) {
    header('Location: faculty_dashboard.php?error=access_denied');
    exit();
}

// Get section information
$section_query = "SELECT year, branch, section FROM classes WHERE class_id = ?";
$stmt = mysqli_prepare($conn, $section_query);
mysqli_stmt_bind_param($stmt, "i", $section_id);
mysqli_stmt_execute($stmt);
$section_result = mysqli_stmt_get_result($stmt);
$section_data = mysqli_fetch_assoc($section_result);

if (!$section_data) {
    header('Location: faculty_dashboard.php?error=section_not_found');
    exit();
}

$section_name = $section_data['year'] . '/4 ' . strtoupper($section_data['branch']) . (!empty($section_data['section']) ? '-' . strtoupper($section_data['section']) : '');

// Get students in this section
$students_query = "SELECT s.student_id, s.name, sp.parent_number 
                   FROM students s 
                   LEFT JOIN student_personal sp ON s.student_id = sp.student_id 
                   WHERE s.class_id = ? 
                   ORDER BY s.student_id";
$stmt = mysqli_prepare($conn, $students_query);
mysqli_stmt_bind_param($stmt, "i", $section_id);
mysqli_stmt_execute($stmt);
$students_result = mysqli_stmt_get_result($stmt);

$students = [];
while ($student = mysqli_fetch_assoc($students_result)) {
    $students[] = $student;
}

if (empty($students)) {
    header('Location: faculty_dashboard.php?error=no_students');
    exit();
}

// Check existing attendance for this date and session
$existing_attendance = [];
$attendance_query = "SELECT student_id, status FROM student_attendance WHERE attendance_date = ? AND session = ?";
$stmt = mysqli_prepare($conn, $attendance_query);
mysqli_stmt_bind_param($stmt, "ss", $attendance_date, $attendance_session);
mysqli_stmt_execute($stmt);
$attendance_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($attendance_result)) {
    $existing_attendance[$row['student_id']] = $row['status'];
}

// Get attendance history for this section (last 7 days)
$attendance_history = [];
$history_query = "
    SELECT 
        sa.attendance_date,
        sa.session,
        COUNT(*) as total_students,
        SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN sa.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN sa.status = 'holiday' THEN 1 ELSE 0 END) as holiday_count
    FROM student_attendance sa
    JOIN students s ON sa.student_id = s.student_id
    WHERE s.class_id = ? 
    AND sa.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY sa.attendance_date, sa.session
    ORDER BY sa.attendance_date DESC, sa.session ASC
";
$history_stmt = mysqli_prepare($conn, $history_query);
mysqli_stmt_bind_param($history_stmt, "i", $section_id);
mysqli_stmt_execute($history_stmt);
$history_result = mysqli_stmt_get_result($history_stmt);

while ($row = mysqli_fetch_assoc($history_result)) {
    $attendance_history[] = $row;
}

// Get individual student attendance history for selected date
$student_history = [];
$student_history_query = "
    SELECT 
        s.student_id,
        s.name,
        sa.status,
        sa.created_at,
        sa.updated_at
    FROM students s
    LEFT JOIN student_attendance sa ON s.student_id = sa.student_id 
        AND sa.attendance_date = ? 
        AND sa.session = ?
    WHERE s.class_id = ?
    ORDER BY s.student_id
";
$student_history_stmt = mysqli_prepare($conn, $student_history_query);
mysqli_stmt_bind_param($student_history_stmt, "ssi", $attendance_date, $attendance_session, $section_id);
mysqli_stmt_execute($student_history_stmt);
$student_history_result = mysqli_stmt_get_result($student_history_stmt);

while ($row = mysqli_fetch_assoc($student_history_result)) {
    $student_history[] = $row;
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $attendance_data = json_decode($_POST['attendance_data'] ?? '[]', true);
    
    if (!empty($attendance_data)) {
        $saved_count = 0;
        $errors = [];
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        foreach ($attendance_data as $attendance) {
            $student_id = $attendance['student_id'] ?? '';
            $status = $attendance['status'] ?? '';
            
            if (empty($student_id) || empty($status)) {
                continue;
            }
            
            // Check if attendance record exists
            $check_query = "SELECT id FROM student_attendance WHERE student_id = ? AND attendance_date = ? AND session = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "sss", $student_id, $attendance_date, $attendance_session);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                // Update existing record
                $update_query = "UPDATE student_attendance SET status = ?, updated_at = NOW() WHERE student_id = ? AND attendance_date = ? AND session = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ssss", $status, $student_id, $attendance_date, $attendance_session);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $saved_count++;
                } else {
                    $errors[] = "Failed to update attendance for student $student_id";
                }
            } else {
                // Insert new record
                $insert_query = "INSERT INTO student_attendance (student_id, attendance_date, session, status, faculty_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                $insert_stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "sssss", $student_id, $attendance_date, $attendance_session, $status, $faculty_id);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $saved_count++;
                } else {
                    $errors[] = "Failed to save attendance for student $student_id";
                }
            }
        }
        
        if (empty($errors)) {
            // Commit transaction
            mysqli_commit($conn);
            $message = "Attendance saved successfully for $saved_count students!";
            $message_type = 'success';
            
            // Refresh existing attendance data
            $existing_attendance = [];
            $attendance_query = "SELECT student_id, status FROM student_attendance WHERE attendance_date = ? AND session = ?";
            $stmt = mysqli_prepare($conn, $attendance_query);
            mysqli_stmt_bind_param($stmt, "ss", $attendance_date, $attendance_session);
            mysqli_stmt_execute($stmt);
            $attendance_result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($attendance_result)) {
                $existing_attendance[$row['student_id']] = $row['status'];
            }
        } else {
            // Rollback transaction
            mysqli_rollback($conn);
            $message = 'Some records failed to save. Please try again.';
            $message_type = 'error';
        }
    } else {
        $message = 'No attendance data provided.';
        $message_type = 'warning';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title>Mark Attendance - <?php echo htmlspecialchars($section_name); ?></title>
    <link rel="stylesheet" href="student_dashboard.css">
    <style>
        :root {
            --primary-color: #007bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
            --text-muted: #6c757d;
        }

        .main-content {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px 0;
            margin-top: 80px; /* Add space for fixed navbar */
        }

        .attendance-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Header */
        .attendance-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 25px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-button {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .session-info {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-primary { background: rgba(255,255,255,0.25); color: white; }
        .badge-info { background: rgba(255,255,255,0.2); color: white; }
        .badge-secondary { background: rgba(255,255,255,0.15); color: white; }

        /* Actions Bar */
        .actions-bar {
            background: var(--light-gray);
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .left-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .select-all {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 500;
            margin: 0;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .select-all:hover {
            background: rgba(0,123,255,0.1);
        }

        .select-all input {
            width: 18px;
            height: 18px;
        }

        .selected-info {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 500;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-success { background: var(--success-color); color: white; }
        .btn-danger { background: var(--danger-color); color: white; }
        .btn-warning { background: var(--warning-color); color: #333; }
        .btn-primary { background: var(--primary-color); color: white; }

        /* Table */
        .table-container {
            overflow-x: auto;
            max-height: 65vh;
            overflow-y: auto;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .students-table thead {
            background: var(--light-gray);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .students-table th,
        .students-table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .students-table th {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table tbody tr {
            transition: all 0.3s;
        }

        .students-table tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transform: scale(1.002);
        }

        /* Status-based row colors */
        .student-row.status-present {
            background-color: rgba(40,167,69,0.05);
            border-left: 4px solid var(--success-color);
        }

        .student-row.status-absent {
            background-color: rgba(220,53,69,0.05);
            border-left: 4px solid var(--danger-color);
        }

        .student-row.status-holiday {
            background-color: rgba(255,193,7,0.05);
            border-left: 4px solid var(--warning-color);
        }

        /* Table cells */
        .student-id {
            font-weight: 600;
            color: var(--primary-color);
            font-family: 'Courier New', monospace;
        }

        .student-name {
            font-weight: 500;
            color: #333;
        }

        .contact {
            font-size: 13px;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge.present {
            background: rgba(40,167,69,0.15);
            color: var(--success-color);
        }

        .status-badge.absent {
            background: rgba(220,53,69,0.15);
            color: var(--danger-color);
        }

        .status-badge.holiday {
            background: rgba(255,193,7,0.15);
            color: #856404;
        }

        .status-badge:not(.present):not(.absent):not(.holiday) {
            background: rgba(108,117,125,0.15);
            color: var(--text-muted);
        }

        /* Quick Action Buttons */
        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .btn-quick {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-quick:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-present {
            background: var(--success-color);
            color: white;
        }

        .btn-absent {
            background: var(--danger-color);
            color: white;
        }

        .btn-holiday {
            background: var(--warning-color);
            color: #333;
        }

        /* Messages */
        .alert {
            padding: 15px 20px;
            margin: 20px 25px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .loading-spinner {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .loading-spinner i {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 60px;
                padding: 10px 0;
            }

            .attendance-wrapper {
                margin: 10px;
                border-radius: 8px;
            }

            .attendance-header {
                padding: 20px 15px;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .section-title {
                font-size: 22px;
                text-align: center;
            }

            .session-info {
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }

            .badge {
                padding: 6px 12px;
                font-size: 12px;
            }

            .back-button {
                align-self: flex-start;
                margin-bottom: 10px;
            }
            
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
                padding: 15px;
            }

            .left-actions {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .bulk-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .btn {
                padding: 12px 16px;
                font-size: 13px;
                min-height: 44px; /* Touch-friendly */
                justify-content: center;
            }
            
            .students-table {
                font-size: 12px;
            }
            
            .students-table th,
            .students-table td {
                padding: 8px 6px;
                font-size: 12px;
            }

            .students-table th:nth-child(4), /* Contact column */
            .students-table td:nth-child(4) {
                display: none;
            }

            .student-id {
                font-size: 11px;
                word-break: break-all;
            }

            .student-name {
                font-size: 12px;
                line-height: 1.3;
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .action-buttons {
                flex-direction: row;
                gap: 4px;
                justify-content: center;
            }
            
            .btn-quick {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .status-badge {
                font-size: 10px;
                padding: 4px 8px;
            }

            .session-form .actions-bar {
                flex-direction: column;
                gap: 12px;
            }

            .session-form .left-actions,
            .session-form .bulk-actions {
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .session-form input[type="date"], 
            .session-form select {
                min-width: 140px;
                padding: 10px;
                font-size: 14px;
            }

            .table-container {
                max-height: 50vh;
                border-radius: 8px;
                overflow: auto;
            }

            .alert {
                margin: 15px 10px;
                padding: 12px 15px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .attendance-wrapper {
                margin: 5px;
            }

            .attendance-header {
                padding: 15px 10px;
            }

            .section-title {
                font-size: 18px;
            }

            .badge {
                padding: 4px 8px;
                font-size: 11px;
            }

            .actions-bar {
                padding: 10px;
            }

            .bulk-actions {
                grid-template-columns: 1fr;
            }

            .btn {
                padding: 10px 12px;
                font-size: 12px;
                min-height: 40px;
            }

            .students-table th,
            .students-table td {
                padding: 6px 4px;
                font-size: 11px;
            }

            .students-table th:first-child,
            .students-table td:first-child {
                width: 30px;
                padding: 6px 2px;
            }

            .students-table th:nth-child(2), /* Student ID */
            .students-table td:nth-child(2) {
                width: 80px;
                font-size: 10px;
            }

            .students-table th:nth-child(3), /* Name */
            .students-table td:nth-child(3) {
                max-width: 100px;
            }

            .student-name {
                max-width: 90px;
                font-size: 11px;
            }

            .btn-quick {
                width: 28px;
                height: 28px;
                font-size: 10px;
            }

            .status-badge {
                font-size: 9px;
                padding: 3px 6px;
                word-break: break-word;
            }

            .status-badge i {
                margin-right: 2px;
            }

            .session-form input[type="date"], 
            .session-form select {
                width: 100%;
                margin-bottom: 8px;
            }

            .session-form label {
                display: block;
                margin-bottom: 4px;
                text-align: center;
            }

            .loading-spinner {
                padding: 20px;
                margin: 10px;
            }

            .loading-spinner i {
                font-size: 24px;
            }
        }

        /* Landscape orientation for mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .table-container {
                max-height: 35vh;
            }
            
            .main-content {
                margin-top: 50px;
            }
            
            .attendance-header {
                padding: 10px 15px;
            }
            
            .session-title {
                font-size: 16px;
            }
        }

        /* Touch-friendly improvements */
        .btn, .btn-quick {
            touch-action: manipulation; /* Prevent zoom on touch */
            user-select: none;
        }

        input[type="checkbox"] {
            transform: scale(1.2);
            margin: 0 4px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .btn-quick {
                border: 2px solid currentColor;
            }
            
            .status-badge {
                border: 1px solid currentColor;
            }
        }

        /* Focus improvements for accessibility */
        .btn:focus,
        .btn-quick:focus,
        input:focus,
        select:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Pre-filled attendance indicator */
        .pre-filled-indicator {
            background: rgba(255, 193, 7, 0.1);
            border-left: 4px solid var(--warning-color);
            padding: 10px 15px;
            margin: 15px 25px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pre-filled-indicator i {
            color: var(--warning-color);
        }

        /* Modification tracking */
        .modified-row {
            background-color: rgba(255, 193, 7, 0.1) !important;
            border-left: 4px solid var(--info-color) !important;
            animation: highlightModification 0.5s ease;
        }

        @keyframes highlightModification {
            0% {
                background-color: rgba(23, 162, 184, 0.3);
            }
            100% {
                background-color: rgba(255, 193, 7, 0.1);
            }
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <div class="main-content">
        <div class="attendance-wrapper">
            <!-- Header Section -->
            <div class="attendance-header">
                <div class="header-content">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <a href="faculty_dashboard.php" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                        <h2 class="section-title">
                            <i class="fas fa-users"></i>
                            <?php echo htmlspecialchars($section_name); ?>
                        </h2>
                    </div>
                    <div class="session-info">
                        <span class="badge badge-primary">
                            <i class="fas fa-calendar-day"></i>
                            <?php echo date('d M Y', strtotime($attendance_date)); ?>
                        </span>
                        <span class="badge badge-info">
                            <i class="fas fa-clock"></i>
                            <?php echo ucfirst($attendance_session); ?>
                        </span>
                        <span class="badge badge-secondary">
                            <i class="fas fa-users"></i>
                            <?php echo count($students); ?> Students
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pre-filled Attendance Notice -->
            <?php if (!empty($existing_attendance)): ?>
                <div class="pre-filled-indicator">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Attendance Already Marked</strong><br>
                        <small>You can modify existing attendance records below. Changes will be highlighted and saved automatically.</small>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success/Error Messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'error' ? 'exclamation-circle' : 'exclamation-triangle'); ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Quick Actions Bar -->
            <div class="actions-bar">
                <div class="left-actions">
                    <label class="select-all">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        <span>Select All</span>
                    </label>
                    <span id="selectedCount" class="selected-info">0 selected</span>
                </div>
                
                <div class="bulk-actions">
                    <button type="button" class="btn btn-success" onclick="markSelectedAs('present')" id="bulkPresentBtn" disabled>
                        <i class="fas fa-check"></i> Mark Present
                    </button>
                    <button type="button" class="btn btn-danger" onclick="markSelectedAs('absent')" id="bulkAbsentBtn" disabled>
                        <i class="fas fa-times"></i> Mark Absent
                    </button>
                    <button type="button" class="btn btn-warning" onclick="markSelectedAs('holiday')" id="bulkHolidayBtn" disabled>
                        <i class="fas fa-umbrella-beach"></i> Mark Holiday
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveAttendance()" id="saveBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>

            <!-- Session Selection Form -->
            <form method="POST" class="session-form">
                <div class="actions-bar">
                    <div class="left-actions">
                        <label for="new_date">Date:</label>
                        <input type="date" id="new_date" name="new_date" value="<?php echo htmlspecialchars($attendance_date); ?>">
                    </div>
                    <div class="bulk-actions">
                        <label for="new_session">Session:</label>
                        <select id="new_session" name="new_session">
                            <option value="forenoon" <?php echo $attendance_session === 'forenoon' ? 'selected' : ''; ?>>Forenoon</option>
                            <option value="afternoon" <?php echo $attendance_session === 'afternoon' ? 'selected' : ''; ?>>Afternoon</option>
                        </select>
                        <button type="submit" class="btn btn-primary" name="change_session">
                            <i class="fas fa-sync"></i> Change Session
                        </button>
                    </div>
                </div>
            </form>

            <!-- Students Table -->
            <div class="table-container">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="headerSelect" onchange="toggleSelectAll()">
                            </th>
                            <th width="130">Student ID</th>
                            <th>Name</th>
                            <th width="140">Contact</th>
                            <th width="120">Status</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php 
                            $current_status = $existing_attendance[$student['student_id']] ?? '';
                            $status_class = $current_status ? 'status-' . $current_status : 'status-unmarked';
                            ?>
                            <tr class="student-row <?php echo $status_class; ?>" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>">
                                <td>
                                    <input type="checkbox" class="student-check" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" onchange="updateSelection()">
                                </td>
                                <td class="student-id">
                                    <?php echo htmlspecialchars($student['student_id']); ?>
                                </td>
                                <td class="student-name">
                                    <?php echo htmlspecialchars($student['name'] ?: 'Name Pending'); ?>
                                </td>
                                <td class="contact">
                                    <?php if (!empty($student['parent_number'])): ?>
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($student['parent_number']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="status-cell">
                                    <span class="status-badge <?php echo $current_status; ?>" data-status="<?php echo $current_status; ?>">
                                        <?php 
                                        switch($current_status) {
                                            case 'present':
                                                echo '<i class="fas fa-check"></i> Present';
                                                break;
                                            case 'absent':
                                                echo '<i class="fas fa-times"></i> Absent';
                                                break;
                                            case 'holiday':
                                                echo '<i class="fas fa-umbrella-beach"></i> Holiday';
                                                break;
                                            default:
                                                echo '<i class="fas fa-minus"></i> Not Marked';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <button type="button" class="btn-quick btn-present" onclick="markStudent('<?php echo htmlspecialchars($student['student_id']); ?>', 'present')" title="Present">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn-quick btn-absent" onclick="markStudent('<?php echo htmlspecialchars($student['student_id']); ?>', 'absent')" title="Absent">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" class="btn-quick btn-holiday" onclick="markStudent('<?php echo htmlspecialchars($student['student_id']); ?>', 'holiday')" title="Holiday">
                                            <i class="fas fa-umbrella-beach"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Saving attendance...</p>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        // Global variables
        var studentData = {};
        var hasChanges = false;

        // Load existing attendance data
        function loadExistingAttendance() {
            document.querySelectorAll('.student-row').forEach(function(row) {
                const studentId = row.getAttribute('data-student-id');
                const statusBadge = row.querySelector('.status-badge');
                const currentStatus = statusBadge ? statusBadge.getAttribute('data-status') : '';
                
                studentData[studentId] = {
                    status: currentStatus,
                    originalStatus: currentStatus
                };
            });
        }

        // Toggle select all
        function toggleSelectAll() {
            try {
                const selectAll = document.getElementById('selectAll');
                const headerSelect = document.getElementById('headerSelect');
                
                if (!selectAll || !headerSelect) return;
                
                const isChecked = selectAll.checked || headerSelect.checked;
                
                // Sync both checkboxes
                selectAll.checked = isChecked;
                headerSelect.checked = isChecked;
                
                // Update all student checkboxes
                document.querySelectorAll('.student-check').forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                
                updateSelection();
            } catch (error) {
                console.error('Error in toggleSelectAll:', error);
            }
        }

        // Update selection count and button states
        function updateSelection() {
            try {
                const selectedCheckboxes = document.querySelectorAll('.student-check:checked');
                const totalCheckboxes = document.querySelectorAll('.student-check');
                const count = selectedCheckboxes.length;
                const total = totalCheckboxes.length;
                
                // Update count display
                const selectedCount = document.getElementById('selectedCount');
                if (selectedCount) {
                    selectedCount.textContent = count + ' selected';
                }
                
                // Update bulk action buttons
                const bulkButtons = ['bulkPresentBtn', 'bulkAbsentBtn', 'bulkHolidayBtn'];
                bulkButtons.forEach(function(btnId) {
                    const btn = document.getElementById(btnId);
                    if (btn) {
                        btn.disabled = count === 0;
                    }
                });
                
                // Update select all checkboxes
                const selectAll = document.getElementById('selectAll');
                const headerSelect = document.getElementById('headerSelect');
                
                if (selectAll && headerSelect) {
                    if (count === 0) {
                        selectAll.checked = false;
                        headerSelect.checked = false;
                        selectAll.indeterminate = false;
                        headerSelect.indeterminate = false;
                    } else if (count === total) {
                        selectAll.checked = true;
                        headerSelect.checked = true;
                        selectAll.indeterminate = false;
                        headerSelect.indeterminate = false;
                    } else {
                        selectAll.checked = false;
                        headerSelect.checked = false;
                        selectAll.indeterminate = true;
                        headerSelect.indeterminate = true;
                    }
                }
            } catch (error) {
                console.error('Error in updateSelection:', error);
            }
        }

        // Mark single student
        function markStudent(studentId, status) {
            try {
                const row = document.querySelector('[data-student-id="' + studentId + '"]');
                if (!row) return;
                
                const statusBadge = row.querySelector('.status-badge');
                if (!statusBadge) return;
                
                // Update data
                if (!studentData[studentId]) {
                    studentData[studentId] = { originalStatus: '' };
                }
                
                studentData[studentId].status = status;
                hasChanges = true;
                
                // Update visual
                row.className = 'student-row status-' + status;
                statusBadge.setAttribute('data-status', status);
                statusBadge.className = 'status-badge ' + status;
                
                // Update status text
                let statusText = '';
                switch(status) {
                    case 'present':
                        statusText = '<i class="fas fa-check"></i> Present';
                        break;
                    case 'absent':
                        statusText = '<i class="fas fa-times"></i> Absent';
                        break;
                    case 'holiday':
                        statusText = '<i class="fas fa-umbrella-beach"></i> Holiday';
                        break;
                }
                statusBadge.innerHTML = statusText;
                
                updateUI();
            } catch (error) {
                console.error('Error in markStudent:', error);
            }
        }

        // Mark selected students
        function markSelectedAs(status) {
            try {
                const selectedCheckboxes = document.querySelectorAll('.student-check:checked');
                
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one student');
                    return;
                }
                
                let count = 0;
                selectedCheckboxes.forEach(function(checkbox) {
                    const studentId = checkbox.getAttribute('data-student-id');
                    if (studentId) {
                        markStudent(studentId, status);
                        checkbox.checked = false;
                        count++;
                    }
                });
                
                updateSelection();
                alert(count + ' students marked as ' + status);
            } catch (error) {
                console.error('Error in markSelectedAs:', error);
            }
        }

        // Save attendance
        function saveAttendance() {
            try {
                if (!hasChanges) {
                    alert('No changes to save');
                    return;
                }
                
                const attendanceData = [];
                
                Object.keys(studentData).forEach(function(studentId) {
                    const data = studentData[studentId];
                    if (data.status !== data.originalStatus) {
                        attendanceData.push({
                            student_id: studentId,
                            status: data.status || 'absent'
                        });
                    }
                });
                
                if (attendanceData.length === 0) {
                    alert('No changes to save');
                    return;
                }
                
                showLoading(true);
                
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const attendanceInput = document.createElement('input');
                attendanceInput.type = 'hidden';
                attendanceInput.name = 'attendance_data';
                attendanceInput.value = JSON.stringify(attendanceData);
                
                const saveInput = document.createElement('input');
                saveInput.type = 'hidden';
                saveInput.name = 'save_attendance';
                saveInput.value = '1';
                
                form.appendChild(attendanceInput);
                form.appendChild(saveInput);
                document.body.appendChild(form);
                form.submit();
                
            } catch (error) {
                console.error('Error in saveAttendance:', error);
                showLoading(false);
            }
        }

        // Update UI state
        function updateUI() {
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) {
                if (hasChanges) {
                    saveBtn.style.background = '#dc3545';
                    saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                } else {
                    saveBtn.style.background = 'var(--primary-color)';
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved';
                }
            }
        }

        // Show/hide loading
        function showLoading(show) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = show ? 'flex' : 'none';
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            loadExistingAttendance();
            updateUI();
        });
    </script>
</body>
</html>
