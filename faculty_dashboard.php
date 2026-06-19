<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['faculty_logged_in']) || !$_SESSION['faculty_logged_in']) {
    header('Location: login.php');
    exit();
}

include dirname(__FILE__) . '/connect.php';

// Check database connection
if ($conn->connect_error) {
    error_log("Database connection failed in faculty_dashboard.php: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Get real faculty data from database
$faculty_id = $_SESSION['faculty_id'] ?? null;
if (!$faculty_id) {
    // Session data is missing, redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

$faculty_query = "SELECT faculty_name, class_id, phone_number, email FROM faculties WHERE faculty_id = ?";
$stmt_faculty = $conn->prepare($faculty_query);
if ($stmt_faculty) {
    $stmt_faculty->bind_param("i", $faculty_id);
    $stmt_faculty->execute();
    $faculty_result = $stmt_faculty->get_result();
    $faculty_data = $faculty_result ? mysqli_fetch_assoc($faculty_result) : null;
    $stmt_faculty->close();
} else {
    $faculty_data = null;
    error_log("Faculty query failed to prepare: " . $conn->error);
}

if ($faculty_data) {
    $faculty_name = $faculty_data['faculty_name'];
    $faculty_sections = (string)($faculty_data['class_id'] ?? '');
    $faculty_phone = $faculty_data['phone_number'];
    $faculty_email = $faculty_data['email'];
} else {
    // Fallback to session data if database query fails
    $faculty_name = $_SESSION['faculty_name'] ?? 'Unknown Faculty';
    $faculty_sections = $_SESSION['faculty_sections'] ?? '';
    $faculty_phone = $_SESSION['faculty_phone'] ?? '';
    $faculty_email = $_SESSION['faculty_email'] ?? '';
}

// Get assigned sections - handle empty sections properly
$assigned_sections = [];
if (!empty($faculty_sections)) {
    $assigned_sections = explode(',', $faculty_sections);
    // Clean up any empty entries
    $assigned_sections = array_filter($assigned_sections, function($section) {
        return !empty(trim($section));
    });
}

$first_assigned_section = !empty($assigned_sections) ? $assigned_sections[0] : '';

$classes = [];

// Filter sections that have data - only if we have assigned sections
$sections_with_data = [];
if (!empty($assigned_sections)) {
    foreach ($assigned_sections as $class_id) {
        if (!empty($class_id)) {
            // Get class details using prepared statement
            $class_query = "SELECT year, branch, section FROM classes WHERE class_id = ?";
            $stmt_class = $conn->prepare($class_query);
            if ($stmt_class) {
                $stmt_class->bind_param("i", $class_id);
                $stmt_class->execute();
                $class_result = $stmt_class->get_result();
                
                if ($class_row = mysqli_fetch_assoc($class_result)) {
                    $display_name = $class_row['year'] . '/4 ' . strtoupper($class_row['branch']);
                    if (!empty($class_row['section'])) {
                        $display_name .= '-' . strtoupper($class_row['section']);
                    }

                    // Check if there's attendance data for this class using prepared statement
                    $check_query = "SELECT COUNT(*) as count FROM student_attendance sa
                                   JOIN students s ON sa.student_id = s.student_id
                                   WHERE s.class_id = ?";
                    $stmt_check = $conn->prepare($check_query);
                    if ($stmt_check) {
                        $stmt_check->bind_param("i", $class_id);
                        $stmt_check->execute();
                        $result = $stmt_check->get_result();
                        if ($result) {
                            $row = mysqli_fetch_assoc($result);
                            if ($row && $row['count'] > 0) {
                                $sections_with_data[] = $class_id;
                                $classes[$class_id] = $display_name;
                            }
                        } else {
                            error_log("Check query failed for class_id $class_id: " . $stmt_check->error);
                        }
                        $stmt_check->close();
                    } else {
                        error_log("Check query failed to prepare for class_id $class_id: " . $conn->error);
                    }
                }
                $stmt_class->close();
            } else {
                error_log("Class query failed to prepare for class_id $class_id: " . $conn->error);
            }
        }
    }
}

// Function to generate WhatsApp message
function generateWhatsAppMessage($student_id, $student_name, $parent_phone, $start_date, $end_date, $faculty_name, $faculty_phone, $faculty_email, $conn) {
    // Get attendance data for the student
    $attendance_query = "SELECT attendance_date, session, status FROM student_attendance WHERE student_id = ? AND attendance_date BETWEEN ? AND ? ORDER BY attendance_date, session";
    $stmt = mysqli_prepare($conn, $attendance_query);
    mysqli_stmt_bind_param($stmt, "sss", $student_id, $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $attendance_result = mysqli_stmt_get_result($stmt);
    
    $attendance_data = [];
    while ($row = mysqli_fetch_assoc($attendance_result)) {
        $attendance_data[$row['attendance_date']][$row['session']] = $row['status'];
    }
    
    // Calculate attendance percentage
    $total_sessions = 0;
    $present_sessions = 0;
    
    $current_date = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    
    while ($current_date <= $end_date_obj) {
        $date_str = $current_date->format('Y-m-d');
        foreach (['forenoon', 'afternoon'] as $session) {
            $total_sessions++;
            if (isset($attendance_data[$date_str][$session]) && $attendance_data[$date_str][$session] === 'present') {
                $present_sessions++;
            }
        }
        $current_date->add(new DateInterval('P1D'));
    }
    
    $attendance_percentage = $total_sessions > 0 ? round(($present_sessions / $total_sessions) * 100, 2) : 0;
    
    // Generate message
    // All data is htmlspecialchars'd for safety, even if primarily for WhatsApp,
    // in case it's ever displayed on a web page.
    $message = "*SRKR Engineering College*\n";
    $message .= "*Weekly Attendance Report*\n\n";
    $message .= "*Student Details:*\n";
    $message .= "Name: " . htmlspecialchars($student_name) . "\n";
    $message .= "Registration No: " . htmlspecialchars($student_id) . "\n";
    $message .= "Parent Contact: " . htmlspecialchars($parent_phone) . "\n\n";
    $message .= "*Attendance Summary (" . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date) . "):*\n";
    $message .= "*Total Sessions:* " . htmlspecialchars($total_sessions) . "\n";
    $message .= "*Present Sessions:* " . htmlspecialchars($present_sessions) . "\n";
    $message .= "*Absent Sessions:* " . htmlspecialchars($total_sessions - $present_sessions) . "\n";
    $message .= "*Attendance Percentage:* " . htmlspecialchars($attendance_percentage) . "%\n\n";
    
    $message .= "*Class Teacher Details:*\n";
    $message .= "Name: " . htmlspecialchars($faculty_name) . "\n";
    $message .= "Contact: " . htmlspecialchars(!empty($faculty_phone) ? $faculty_phone : 'Pending') . "\n";
    $message .= "Email: " . htmlspecialchars(!empty($faculty_email) ? $faculty_email : 'Pending') . "\n\n";
    
    $message .= "*OFFICIAL NOTICE:*\n";
    $message .= "This is an official attendance report generated by SRKR Engineering College. For any discrepancies or queries, please contact the respective class teacher during college hours.\n\n";
    $message .= "*SRKR ENGINEERING COLLEGE, Bhimavaram*\n";
    return $message;
}

// Handle WhatsApp message generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_whatsapp'])) {
    $selected_section = (int)$_POST['section'];
    $end_date = $_POST['end_date'];
    
    // Validate dates
    $end_date_obj = new DateTime($end_date);
    $start_date_obj = clone $end_date_obj;
    $start_date_obj->sub(new DateInterval('P6D')); // 7 days including end date
    
    $start_date = $start_date_obj->format('Y-m-d');
    
    $whatsapp_messages = [];
    
    if (!empty($selected_section) && in_array($selected_section, $assigned_sections)) {
        // Get students for this section
        $students_query = "SELECT s.student_id, s.name, sp.parent_number 
                          FROM students s 
                          LEFT JOIN student_personal sp ON s.student_id = sp.student_id 
                          WHERE s.class_id = ? 
                          ORDER BY s.student_id";
        $stmt = mysqli_prepare($conn, $students_query);
        mysqli_stmt_bind_param($stmt, "i", $selected_section);
        mysqli_stmt_execute($stmt);
        $students_result = mysqli_stmt_get_result($stmt);
        
        while ($student = mysqli_fetch_assoc($students_result)) {
            $message = generateWhatsAppMessage(
                $student['student_id'],
                $student['name'] ?: 'Pending',
                $student['parent_number'] ?: 'Pending',
                $start_date,
                $end_date,
                $faculty_name,
                $faculty_phone,
                $faculty_email,
                $conn
            );
            
            $whatsapp_messages[] = [
                'register_no' => $student['student_id'],
                'student_name' => $student['name'] ?: 'Pending',
                'parent_phone' => $student['parent_number'] ?: 'Pending',
                'message' => $message
            ];
        }
        
        // Store in session for display
        $_SESSION['whatsapp_messages'] = $whatsapp_messages;
        $_SESSION['selected_section'] = $selected_section;
        $_SESSION['date_range'] = $start_date . ' to ' . $end_date;
    }
}

// --- Faculty Leave Approval Section ---
include_once dirname(__FILE__) . '/db_migration_helper.php';
$db_helper = new DatabaseMigrationHelper($conn);

$leave_error = '';
$leave_success = '';
$point_request_error = '';
$point_request_success = '';
$attendance_error = '';
$attendance_success = '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $attendance_section = $_POST['attendance_section'] ?? '';
    $attendance_date = $_POST['attendance_date'] ?? '';
    $attendance_session = $_POST['attendance_session'] ?? '';
    $attendance_statuses = $_POST['attendance_status'] ?? [];

    if (!empty($attendance_section) && !empty($attendance_date) && !empty($attendance_session) && !empty($attendance_statuses)) {
        $success_count = 0;
        $error_count = 0;

        foreach ($attendance_statuses as $student_id => $sessions) {
            // Handle the new session-based structure
            $session_data = $sessions[$attendance_session] ?? $sessions;
            $status = is_array($session_data) ? $session_data : $sessions;
            
            // Skip if status is empty (not marked)
            if (empty($status)) continue;

            // Check if attendance already exists for this student, date, and session
            $check_query = "SELECT id FROM student_attendance WHERE student_id = ? AND attendance_date = ? AND session = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "sss", $student_id, $attendance_date, $attendance_session);
            mysqli_stmt_execute($stmt);
            $existing = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($existing) > 0) {
                // Update existing attendance
                $update_query = "UPDATE student_attendance SET status = ?, faculty_id = ?, updated_at = NOW() WHERE student_id = ? AND attendance_date = ? AND session = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "sisss", $status, $faculty_id, $student_id, $attendance_date, $attendance_session);
            } else {
                // Insert new attendance
                $insert_query = "INSERT INTO student_attendance (student_id, attendance_date, session, status, faculty_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "sssss", $student_id, $attendance_date, $attendance_session, $status, $faculty_id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        if ($success_count > 0) {
            $attendance_success = "Attendance marked successfully for $success_count students in " . ucfirst($attendance_session) . " session on " . date('d M Y', strtotime($attendance_date)) . ".";
            if ($error_count > 0) {
                $attendance_success .= " $error_count records had errors.";
            }
        } else {
            $attendance_error = "Failed to mark attendance. Please try again.";
        }
    } else {
        $attendance_error = "Please select section, date, session, and mark attendance for at least one student.";
    }
}

// Handle faculty leave approval actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['faculty_leave_action'])) {
    $application_id = (int)$_POST['application_id'];
    $action = $_POST['faculty_leave_action'];
    $remarks = mysqli_real_escape_string($conn, $_POST['faculty_remarks'] ?? '');
    $faculty_name = $_SESSION['faculty_name'] ?? '';
    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    } else {
        $status = 'pending';
    }
    $update_query = "UPDATE leave_applications SET status='$status', hod_remarks=CONCAT(IFNULL(hod_remarks,''), '\nFaculty: $remarks'), processed_at=NOW(), processed_by='$faculty_id' WHERE id=$application_id";
    if (mysqli_query($conn, $update_query)) {
        // Send notification to student and HOD
        include_once dirname(__FILE__) . '/mail_config.php';
        include_once dirname(__FILE__) . '/mail_helper.php';
        $student_query = "SELECT * FROM leave_applications WHERE id=$application_id";
        $student_result = mysqli_query($conn, $student_query);
        if ($student_result && $row = mysqli_fetch_assoc($student_result)) {
            $mailHelper = new MailHelper($mail_config);
            $mailHelper->sendLeaveStatusNotification($row, $status, $remarks);
        }
        $leave_success = "Leave application has been $status.";
    } else {
        $leave_error = 'Error updating leave application.';
    }
}

// Handle faculty point request actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['faculty_point_request_action'])) {
    $request_id = (int)($_POST['request_id'] ?? 0);
    $action = $_POST['faculty_point_request_action'] ?? '';
    $remarks = trim($_POST['faculty_remarks'] ?? '');

    if ($request_id <= 0) {
        $point_request_error = 'Invalid point request.';
    } elseif (!in_array($action, ['approve', 'reject'], true)) {
        $point_request_error = 'Invalid action.';
    } elseif (empty($assigned_sections)) {
        $point_request_error = 'No assigned sections found for your account.';
    } else {
        $class_ids_in = implode(',', array_map('intval', $assigned_sections));
        $verify_query = "SELECT pr.id FROM point_requests pr
                         JOIN students s ON pr.student_id = s.student_id
                         WHERE pr.id = ? AND s.class_id IN ($class_ids_in)
                         LIMIT 1";
        $verify_stmt = mysqli_prepare($conn, $verify_query);

        if (!$verify_stmt) {
            $point_request_error = 'Database error while validating the request.';
        } else {
            mysqli_stmt_bind_param($verify_stmt, 'i', $request_id);
            mysqli_stmt_execute($verify_stmt);
            $verify_result = mysqli_stmt_get_result($verify_stmt);

            if (!$verify_result || mysqli_num_rows($verify_result) === 0) {
                $point_request_error = 'Request not found or not assigned to your sections.';
            } else {
                $status = $action === 'approve' ? 'Approved' : 'Rejected';
                $update_query = "UPDATE point_requests SET status = ?, faculty_remark = ?, updated_at = NOW() WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);

                if (!$update_stmt) {
                    $point_request_error = 'Database error while updating the request.';
                } else {
                    mysqli_stmt_bind_param($update_stmt, 'ssi', $status, $remarks, $request_id);
                    if (mysqli_stmt_execute($update_stmt)) {
                        $point_request_success = "Point request has been $status successfully.";
                    } else {
                        $point_request_error = 'Error updating point request.';
                    }
                    mysqli_stmt_close($update_stmt);
                }
            }

            mysqli_stmt_close($verify_stmt);
        }
    }
}
// Fetch all leave applications for assigned section (not just pending)
$all_leaves = [];
        if (!empty($assigned_sections)) {
            // $assigned_sections contains class_ids for the faculty
            $class_ids_in = implode(',', array_map('intval', $assigned_sections));
            if (!empty($class_ids_in)) {
                $students_query = "SELECT student_id FROM students WHERE class_id IN ($class_ids_in)";
                $students_result = mysqli_query($conn, $students_query);
                if (!$students_result) {
                    error_log("Students query failed for all_leaves: " . mysqli_error($conn));
                    $all_leaves = [];
                } else {
                    $student_ids = [];
                    while ($student_row = mysqli_fetch_assoc($students_result)) {
                        $student_ids[] = "'" . mysqli_real_escape_string($conn, $student_row['student_id']) . "'";
                    }
                    mysqli_free_result($students_result);

                    if (!empty($student_ids)) {
                        $student_ids_in = implode(',', $student_ids);
                        $query = "SELECT la.*, s.name as student_name, s.student_id as register_no, c.year, c.branch, c.section, sp.parent_number as parent_contact
                                  FROM leave_applications la 
                                  JOIN students s ON la.student_id = s.student_id 
                                  JOIN classes c ON s.class_id = c.class_id
                                  LEFT JOIN student_personal sp ON s.student_id = sp.student_id
                                  WHERE la.student_id IN ($student_ids_in) 
                                  ORDER BY la.applied_at DESC";
                        $result = mysqli_query($conn, $query);
                        if (!$result) {
                            error_log("All leaves query failed: " . mysqli_error($conn));
                            $all_leaves = [];
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Calculate total_days
                                $start = new DateTime($row['start_date']);
                                $end = new DateTime($row['end_date']);
                                $row['total_days'] = $start->diff($end)->days + 1;
                                
                                $all_leaves[] = $row;
                            }
                            mysqli_free_result($result);
                        }
                    }
                }
            }
        }
// Ensure $pending_leaves is always initialized
$pending_leaves = [];
        if (!empty($assigned_sections)) {
            // $assigned_sections contains class_ids for the faculty
            $class_ids_in = implode(',', array_map('intval', $assigned_sections));
            if (!empty($class_ids_in)) {
                $students_query = "SELECT student_id FROM students WHERE class_id IN ($class_ids_in)";
                $students_result = mysqli_query($conn, $students_query);
                if (!$students_result) {
                    error_log("Students query failed for pending_leaves: " . mysqli_error($conn));
                    $pending_leaves = [];
                } else {
                    $student_ids = [];
                    while ($student_row = mysqli_fetch_assoc($students_result)) {
                        $student_ids[] = "'" . mysqli_real_escape_string($conn, $student_row['student_id']) . "'";
                    }
                    mysqli_free_result($students_result);

                    if (!empty($student_ids)) {
                        $student_ids_in = implode(',', $student_ids);
                        $query = "SELECT la.*, s.name as student_name, s.student_id as register_no, c.year, c.branch, c.section 
                                  FROM leave_applications la 
                                  JOIN students s ON la.student_id = s.student_id 
                                  JOIN classes c ON s.class_id = c.class_id 
                                  WHERE la.status = 'pending' AND la.student_id IN ($student_ids_in) 
                                  ORDER BY la.applied_at DESC";
                        $result = mysqli_query($conn, $query);
                        if (!$result) {
                            error_log("Pending leaves query failed: " . mysqli_error($conn));
                            $pending_leaves = [];
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $pending_leaves[] = $row;
                            }
                            mysqli_free_result($result);
                        }
                    }
                }
            }
        }

// Fetch all point requests for assigned sections
$point_requests = [];
if (!empty($assigned_sections)) {
    $class_ids_in = implode(',', array_map('intval', $assigned_sections));
    if (!empty($class_ids_in)) {
        $students_query = "SELECT student_id FROM students WHERE class_id IN ($class_ids_in)";
        $students_result = mysqli_query($conn, $students_query);
        if ($students_result) {
            $student_ids = [];
            while ($student_row = mysqli_fetch_assoc($students_result)) {
                $student_ids[] = "'" . mysqli_real_escape_string($conn, $student_row['student_id']) . "'";
            }
            mysqli_free_result($students_result);

            if (!empty($student_ids)) {
                $student_ids_in = implode(',', $student_ids);
                $query = "SELECT pr.id, pr.student_id, pr.student_name, pr.event_name, pr.points_requested,
                                 pr.proof_file, pr.description, pr.status, pr.faculty_remark, pr.created_at,
                                 s.student_id as register_no, c.year, c.branch, c.section
                          FROM point_requests pr
                          JOIN students s ON pr.student_id = s.student_id
                          JOIN classes c ON s.class_id = c.class_id
                          WHERE pr.student_id IN ($student_ids_in)
                          ORDER BY pr.created_at DESC";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $point_requests[] = $row;
                    }
                    mysqli_free_result($result);
                } else {
                    error_log('Point requests query failed: ' . mysqli_error($conn));
                }
            }
        }
    }
}

$attendance_calendar_data = [];
if (!empty($assigned_sections)) {
    $class_ids_in = implode(',', array_map('intval', $assigned_sections));
    if (!empty($class_ids_in)) {
        // Get attendance data for current month and previous/next months
        $current_date = new DateTime();
        $start_of_month = $current_date->format('Y-m-01');
        $end_of_month = $current_date->format('Y-m-t');

        // Extend range to include previous and next month for better calendar view
        $start_range = date('Y-m-01', strtotime('-1 month', strtotime($start_of_month)));
        $end_range = date('Y-m-t', strtotime('+1 month', strtotime($end_of_month)));

        $calendar_query = "SELECT sa.attendance_date, COUNT(DISTINCT sa.session) as session_count,
                          GROUP_CONCAT(DISTINCT sa.session ORDER BY sa.session) as sessions
                          FROM student_attendance sa
                          JOIN students s ON sa.student_id = s.student_id
                          WHERE s.class_id IN ($class_ids_in)
                          AND sa.attendance_date BETWEEN '$start_range' AND '$end_range'
                          GROUP BY sa.attendance_date
                          ORDER BY sa.attendance_date";

        echo "<!-- DEBUG: Faculty ID: $faculty_id -->\n";
        echo "<!-- DEBUG: Assigned sections: " . implode(',', $assigned_sections) . " -->\n";
        echo "<!-- DEBUG: Date range: $start_range to $end_range -->\n";
        echo "<!-- DEBUG: Query: $calendar_query -->\n";

        $calendar_result = mysqli_query($conn, $calendar_query);
        if (!$calendar_result) {
            error_log("Calendar query failed: " . mysqli_error($conn));
            echo "<!-- DEBUG: Query failed: " . mysqli_error($conn) . " -->\n";
            $attendance_calendar_data = [];
        } else {
            $row_count = mysqli_num_rows($calendar_result);
            echo "<!-- DEBUG: Query returned $row_count rows -->\n";

            while ($row = mysqli_fetch_assoc($calendar_result)) {
                $date = $row['attendance_date'];
                $session_count = (int)$row['session_count'];
                $sessions = explode(',', $row['sessions']);
                $sessions = array_map('strtolower', $sessions);

                echo "<!-- DEBUG: Date $date has $session_count sessions: " . implode(',', $sessions) . " -->\n";

                $attendance_calendar_data[$date] = [
                    'session_count' => $session_count,
                    'sessions' => $sessions
                ];
            }
            mysqli_free_result($calendar_result);
        }
    } else {
        echo "<!-- DEBUG: No class IDs in assigned sections -->\n";
    }
} else {
    echo "<!-- DEBUG: No assigned sections -->\n";
}

echo "<!-- DEBUG: Final attendance data count: " . count($attendance_calendar_data) . " -->\n";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include dirname(__FILE__) . "/head.php"; ?>
    <title>Faculty Dashboard - SRKR Engineering College</title>
    <link rel="stylesheet" href="student_dashboard.css">
</head>
<body>
    <?php include dirname(__FILE__) . "/nav.php"; ?>
    
    <div class="main-content">
        <div class="container">
            <!-- Success/Error Messages -->
            <?php if (!empty($attendance_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($attendance_success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($attendance_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($attendance_error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($leave_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($leave_success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($leave_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($leave_error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($point_request_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($point_request_success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($point_request_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($point_request_error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="back-nav">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </div>
            
            <div class="row">
                <!-- Faculty Profile Card -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-user-tie"></i> Faculty Profile
                        </div>
                        <div class="card-body">
                            <div class="profile-section">
                                <div class="profile-picture-container">
                                    <div class="profile-placeholder">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                </div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($faculty_name); ?></h6>
                                <small class="text-muted">Faculty ID: <?php echo htmlspecialchars($faculty_id); ?></small>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($faculty_email ?: 'Not Set'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?php echo htmlspecialchars($faculty_phone ?: 'Not Set'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Sections</span>
                                <span class="info-value"><?php echo count($assigned_sections); ?> Assigned</span>
                            </div>
                            
                            <div class="mt-3">
                                <a href="faculty_edit_profile.php" class="action-btn">
                                    <i class="fas fa-user-edit"></i> Edit Profile
                                </a>
                                <a href="faculty_logout.php" class="action-btn">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions Card -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        <div class="card-body">
                            <button type="button" class="action-btn" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-excel"></i> Export CSV
                            </button>
                            <button type="button" class="action-btn" onclick="openAttendanceMarking()">
                                <i class="fas fa-address-book"></i> Take Attendance
                            </button>
                            <a href="faculty_leave_management.php" class="action-btn">
                                <i class="fas fa-address-book"></i> Leave Applications
                            </a>
                            <a href="#point-requests" class="action-btn">
                                <i class="fas fa-star"></i> Point Requests
                            </a>
                            <a href="faculty_appreciations.php" class="action-btn">
                                <i class="fas fa-award"></i> Appreciations
                            </a>
                            <a href="faculty_penalties.php" class="action-btn">
                                <i class="fas fa-minus-circle"></i> Penalties
                            </a>
                            
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Card -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar"></i> Statistics Overview
                        </div>
                        <div class="card-body">
                            <?php
                            // Get real statistics from database
                            $total_students = 0;
                            $total_attendance_records = 0;

                            foreach ($sections_with_data as $section) {
                                // Count students in this section
                                $student_count_query = "SELECT COUNT(DISTINCT s.student_id) as student_count FROM students s WHERE s.class_id = " . (int)$section;
                                $student_result = mysqli_query($conn, $student_count_query);
                                if ($student_result) {
                                    $student_count_row = mysqli_fetch_assoc($student_result);
                                    mysqli_free_result($student_result);
                                    $total_students += $student_count_row ? (int)$student_count_row['student_count'] : 0;
                                } else {
                                    error_log("Student count query failed for section $section: " . mysqli_error($conn));
                                }

                                // Count attendance records in this section
                                $attendance_count_query = "SELECT COUNT(*) as attendance_count FROM student_attendance sa JOIN students s ON sa.student_id = s.student_id WHERE s.class_id = " . (int)$section;
                                $attendance_result = mysqli_query($conn, $attendance_count_query);
                                if ($attendance_result) {
                                    $attendance_count_row = mysqli_fetch_assoc($attendance_result);
                                    mysqli_free_result($attendance_result);
                                    $total_attendance_records += $attendance_count_row ? (int)$attendance_count_row['attendance_count'] : 0;
                                } else {
                                    error_log("Attendance count query failed for section $section: " . mysqli_error($conn));
                                }
                            }
                            ?>
                            
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo count($sections_with_data); ?></div>
                                    <div class="stat-label">Active Sections</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo $total_students; ?></div>
                                    <div class="stat-label">Total Students</div>
                                </div>
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo $total_attendance_records; ?></div>
                                    <div class="stat-label">Attendance Records</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo count($pending_leaves); ?></div>
                                    <div class="stat-label">Pending Leaves</div>
                                </div>
                            </div>
                            
                            <!-- Attendance Calendar -->
                            <div class="mt-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-calendar-check"></i> Attendance Calendar
                                </h6>
                                <div id="attendanceCalendar" class="attendance-calendar">
                                    <?php
                                    $current_date = new DateTime();
                                    $current_month = $current_date->format('n');
                                    $current_year = $current_date->format('Y');
                                    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
                                    $first_day_of_month = new DateTime("$current_year-$current_month-01");
                                    $start_day_of_week = $first_day_of_month->format('w'); // 0 = Sunday, 6 = Saturday
                                    ?>
                                    
                                    <div class="calendar-header">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="changeMonth(-1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span class="calendar-title" id="calendarTitle">
                                            <?php echo $current_date->format('F Y'); ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="changeMonth(1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="calendar-weekdays">
                                        <div class="weekday">S</div>
                                        <div class="weekday">M</div>
                                        <div class="weekday">T</div>
                                        <div class="weekday">W</div>
                                        <div class="weekday">T</div>
                                        <div class="weekday">F</div>
                                        <div class="weekday">S</div>
                                    </div>
                                    
                                    <div class="calendar-days" id="calendarDays">
                                        <!-- Calendar days will be populated by JavaScript -->
                                    </div>
                                    
                                    <div class="calendar-legend">
                                        <div class="legend-item">
                                            <span class="legend-color submitted"></span>
                                            <span class="legend-text">Attendance Submitted</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-color not-submitted"></span>
                                            <span class="legend-text">Not Submitted</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-color today-indicator"></span>
                                            <span class="legend-text">Today</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-center">
                                        <small class="text-muted" id="attendanceCount">
                                            Loaded <?php echo count($attendance_calendar_data); ?> attendance records
                                        </small>
                                        <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="showMissingAttendanceDates()">
                                            <i class="fas fa-exclamation-triangle"></i> Show Missing Attendance Dates
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
            <!-- Data Summary -->
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 16px rgba(7,101,147,0.1); border-radius: 15px;">
                <div class="card-header" style="background: var(--light-blue); border-bottom: 1px solid #e3e6f0; border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0" style="color: var(--primary-blue); font-weight: 600;">
                        <i class="fas fa-chart-bar"></i> Real Data Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php
                    // Get real statistics from database
                    $total_students = 0;
                    $total_attendance_records = 0;

                    foreach ($sections_with_data as $section) {
                        // Count students in this section
                        $student_count_query = "SELECT COUNT(DISTINCT s.student_id) as student_count FROM students s WHERE s.class_id = " . (int)$section;
                        $student_result = mysqli_query($conn, $student_count_query);
                        if ($student_result) {
                            $student_count_row = mysqli_fetch_assoc($student_result);
                            mysqli_free_result($student_result);
                            $total_students += $student_count_row ? (int)$student_count_row['student_count'] : 0;
                        } else {
                            error_log("Student count query failed for section $section (summary): " . mysqli_error($conn));
                        }

                        // Count attendance records in this section
                        $attendance_count_query = "SELECT COUNT(*) as attendance_count FROM student_attendance sa JOIN students s ON sa.student_id = s.student_id WHERE s.class_id = " . (int)$section;
                        $attendance_result = mysqli_query($conn, $attendance_count_query);
                        if ($attendance_result) {
                            $attendance_count_row = mysqli_fetch_assoc($attendance_result);
                            mysqli_free_result($attendance_result);
                            $total_attendance_records += $attendance_count_row ? (int)$attendance_count_row['attendance_count'] : 0;
                        } else {
                            error_log("Attendance count query failed for section $section (summary): " . mysqli_error($conn));
                        }
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary"><?php echo count($sections_with_data); ?></h4>
                            <p class="text-muted">Active Sections</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success"><?php echo $total_students; ?></h4>
                            <p class="text-muted">Total Students</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info"><?php echo $total_attendance_records; ?></h4>
                            <p class="text-muted">Attendance Records</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning"><?php echo count($assigned_sections) - count($sections_with_data); ?></h4>
                            <p class="text-muted">Inactive Sections</p>
                        </div>
                    </div>
                </div>
            </div>
            
          
              
        </div>
    </div>

    <!-- Point Requests Review -->
    <div class="container mb-4" id="point-requests">
        <div class="card" style="border: none; box-shadow: 0 4px 16px rgba(7,101,147,0.1); border-radius: 15px;">
            <div class="card-header" style="background: var(--light-blue); border-bottom: 1px solid #e3e6f0; border-radius: 15px 15px 0 0;">
                <h5 class="mb-0" style="color: var(--primary-blue); font-weight: 600;">
                    <i class="fas fa-star"></i> Point Requests Review
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>Event Name</th>
                                <th>Requested Points</th>
                                <th>Proof</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Faculty Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($point_requests)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No point requests found for your assigned sections.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($point_requests as $request): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['register_no']); ?></td>
                                        <td><?php echo htmlspecialchars($request['event_name']); ?></td>
                                        <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($request['points_requested']); ?> pts</span></td>
                                        <td>
                                            <?php if (!empty($request['proof_file']) && strpos($request['proof_file'], 'uploads/proofs/') === 0): ?>
                                                <a href="<?php echo htmlspecialchars($request['proof_file']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i> View Proof
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Not available</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="min-width: 220px;"><?php echo nl2br(htmlspecialchars($request['description'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo strtolower($request['status']) === 'approved' ? 'success' : (strtolower($request['status']) === 'rejected' ? 'danger' : 'warning'); ?>">
                                                <?php echo htmlspecialchars($request['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($request['created_at'])); ?></td>
                                        <td style="min-width: 260px;">
                                            <?php if (strtolower($request['status']) === 'pending'): ?>
                                                <form method="POST" class="mb-0">
                                                    <input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
                                                    <div class="mb-2">
                                                        <textarea name="faculty_remarks" class="form-control form-control-sm" rows="2" placeholder="Optional faculty remark"></textarea>
                                                    </div>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <button type="submit" name="faculty_point_request_action" value="approve" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button type="submit" name="faculty_point_request_action" value="reject" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                </form>
                                            <?php else: ?>
                                                <small class="text-muted">Processed</small>
                                                <?php if (!empty($request['faculty_remark'])): ?>
                                                    <div class="small mt-1"><?php echo nl2br(htmlspecialchars($request['faculty_remark'])); ?></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export CSV Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 8px 32px rgba(7,101,147,0.15);">
                <div class="modal-header" style="background: var(--light-blue); border-bottom: 1px solid #e3e6f0; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="exportModalLabel" style="color: var(--primary-blue); font-weight: 600;">
                        <i class="fas fa-file-excel"></i> Export Attendance Data to CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" action="faculty_export_excel.php" id="exportForm">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 style="color: var(--primary-blue); font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-graduation-cap"></i> Select Section
                                </h6>
                                <div class="col-md-6 mb-3">
                                    <label for="export_section" class="form-label" style="color: var(--primary-blue); font-weight: 500;">
                                        <i class="fas fa-graduation-cap"></i> Select Section
                                    </label>
                                    <select name="section" id="export_section" class="form-control" required style="border-radius: 10px; padding: 10px 15px;">
                                        <option value="">Select Section</option>
                                        <?php foreach ($assigned_sections as $section_id): ?>
                                            <?php
                                            $section_query = "SELECT year, branch, section FROM classes WHERE class_id = " . (int)$section_id;
                                            $section_result = mysqli_query($conn, $section_query);
                                            if ($section_result) {
                                                $section_data = mysqli_fetch_assoc($section_result);
                                                mysqli_free_result($section_result);
                                                if ($section_data):
                                            ?>
                                                <option value="<?php echo $section_id; ?>" <?php echo ($section_id == $first_assigned_section) ? 'selected' : ''; ?> >
                                                    <?php echo $section_data['year'] . '/4 ' . strtoupper($section_data['branch']) . (!empty($section_data['section']) ? '-' . strtoupper($section_data['section']) : ''); ?>
                                                </option>
                                            <?php endif; ?>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 style="color: var(--primary-blue); font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-calendar"></i> Select Date Range (Optional)
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="export_start_date" class="form-label" style="color: var(--primary-blue); font-weight: 500;">
                                            <i class="fas fa-calendar-day"></i> Start Date
                                        </label>
                                        <input type="date" name="start_date" id="export_start_date" class="form-control" style="border-radius: 10px; padding: 10px 15px;">
                                        <small class="form-text text-muted">Leave empty to export all records</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="export_end_date" class="form-label" style="color: var(--primary-blue); font-weight: 500;">
                                            <i class="fas fa-calendar-day"></i> End Date
                                        </label>
                                        <input type="date" name="end_date" id="export_end_date" class="form-control" style="border-radius: 10px; padding: 10px 15px;">
                                        <small class="form-text text-muted">Leave empty to export all records</small>
                                        </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info" style="border-radius: 10px;">
                            <i class="fas fa-info-circle"></i> <strong>Export Information:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Format:</strong> S.No | Regn No | Faculty Name | Date (with FN/AN sub-headers)</li>
                                <li><strong>Header Structure:</strong> Two rows - dates in row 1, FN/AN in row 2</li>
                                <li><strong>Status Codes:</strong> 1 = Present, 0 = Absent, N/A = No data</li>
                                <li><strong>Session Codes:</strong> FN = Forenoon, AN = Afternoon</li>
                                <li><strong>File Format:</strong> CSV that opens perfectly in Excel</li>
                                <li><strong>Security:</strong> You can only export data for your assigned sections</li>
                            </ul>
                        </div>
                        
                        <input type="hidden" name="table" id="selected_table_export" value="">
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" id="exportExcelBtn" disabled>
                                <i class="fas fa-file-excel"></i> Export to CSV
                            </button>
                            <a href="#" class="btn btn-info ms-2" id="debugExportBtn" onclick="debugExport()">
                                <i class="fas fa-bug"></i> Debug Export
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include dirname(__FILE__) . "/footer.php"; ?>
    
    <script>
        // Function to open attendance marking page
        function openAttendanceMarking() {
            // Check if faculty has assigned sections
            const assignedSections = <?php echo json_encode($assigned_sections); ?>;
            if (!assignedSections || assignedSections.length === 0) {
                alert('No sections assigned to you. Please contact the administrator.');
                return;
            }
            
            // Get today\'s date as default
            const today = new Date().toISOString().split('T')[0];
            
            // For simplicity, use the first assigned section and current date
            const selectedSection = assignedSections[0];
            const selectedDate = today;
            
            // Determine current session based on time
            const currentHour = new Date().getHours();
            const defaultSession = currentHour < 14 ? 'forenoon' : 'afternoon';
            
            // Ask user to confirm or change session
            const sessionChoice = confirm(`Mark attendance for ${defaultSession} session?\n\nClick OK for ${defaultSession} or Cancel to choose afternoon.`);
            const selectedSession = sessionChoice ? defaultSession : (defaultSession === 'forenoon' ? 'afternoon' : 'forenoon');
            
            // Redirect to attendance marking page
            window.location.href = `attendance_marking.php?section=${selectedSection}&date=${selectedDate}&session=${selectedSession}`;
        }

        function sendWhatsApp(phone, message) {
            // If phone is "Pending", show alert
            if (phone === 'Pending' || phone === '') {
                alert('Phone number is pending. Please update contact information first.');
                return;
            }
            
            // Clean phone number - remove all non-numeric characters
            let cleanPhone = phone.replace(/[^\d]/g, '');
            
            // If number starts with 0, remove it
            if (cleanPhone.startsWith('0')) {
                cleanPhone = cleanPhone.substring(1);
            }
            
            // Add country code if not present
            if (!cleanPhone.startsWith('91')) {
                cleanPhone = '91' + cleanPhone;
            }
            
            // Ensure number is exactly 12 digits (91 + 10 digits)
            if (cleanPhone.length !== 12) {
                alert('Invalid phone number format. Please check the contact information.');
                return;
            }
            
            // Create WhatsApp URL
            const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
            
            // Try to open WhatsApp
            try {
                window.open(whatsappUrl, '_blank');
            } catch (error) {
                // Fallback: copy message to clipboard
                navigator.clipboard.writeText(message).then(function() {
                    alert('WhatsApp could not be opened. Message copied to clipboard. Please send manually.');
                });
            }
        }
        
        function copyMessage(message) {
            navigator.clipboard.writeText(message).then(function() {
                alert('Message copied to clipboard!');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
        
        function sendAllWhatsApp() {
            const messages = <?php echo json_encode($_SESSION['whatsapp_messages'] ?? []); ?>;
            let pendingCount = 0;
            
            messages.forEach((message, index) => {
                if (message.parent_phone === 'Pending') {
                    pendingCount++;
                } else {
                    setTimeout(() => {
                        sendWhatsApp(message.parent_phone, message.message);
                    }, index * 1000); // Send each message with 1 second delay
                }
            });
            
            if (pendingCount > 0) {
                alert(`${pendingCount} messages skipped due to pending phone numbers. Please update contact information first.`);
            }
        }
        
        // Debug function to test WhatsApp link
        function testWhatsAppLink(phone) {
            console.log('Testing phone number:', phone);
            let cleanPhone = phone.replace(/[^\d]/g, '');
            if (cleanPhone.startsWith('0')) {
                cleanPhone = cleanPhone.substring(1);
            }
            if (!cleanPhone.startsWith('91')) {
                cleanPhone = '91' + cleanPhone;
            }
            console.log('Cleaned phone number:', cleanPhone);
            console.log('WhatsApp URL:', `https://wa.me/${cleanPhone}`);
        }
        
        // Export CSV functionality
        function selectSectionExport(tableName, element) {
            // Remove active class from all sections
            document.querySelectorAll('.section-option-export').forEach(option => {
                option.style.borderColor = '#e3e6f0';
                option.style.backgroundColor = '#f8f9fa';
            });
            
            // Add active class to selected section
            element.style.borderColor = 'var(--primary-blue)';
            element.style.backgroundColor = '#e8f4fd';
            
            // Set the selected table
            document.getElementById('selected_table_export').value = tableName;
            
            // Enable the export button
            document.getElementById('exportExcelBtn').disabled = false;
        }
        
        // Set default dates for export modal
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
            
            // Set dates for export modal
            document.getElementById('export_end_date').value = today.toISOString().split('T')[0];
            document.getElementById('export_start_date').value = thirtyDaysAgo.toISOString().split('T')[0];
        });
        
        // Debug export function
        function debugExport() {
            const table = document.getElementById('selected_table_export').value;
            const startDate = document.getElementById('export_start_date').value;
            const endDate = document.getElementById('export_end_date').value;
            
            if (!table) {
                alert('Please select a section first');
                return;
            }
            
            // Create debug URL
            let debugUrl = `faculty_export_excel.php?table=${table}&debug=1`;
            if (startDate) debugUrl += `&start_date=${startDate}`;
            if (endDate) debugUrl += `&end_date=${endDate}`;
            
            // Open debug URL in new tab
            window.open(debugUrl, '_blank');
        }

        // OLD ATTENDANCE MODAL CODE REMOVED - NOW USING SEPARATE PAGE attendance_marking.php

        // Auto-load students if a section is pre-selected in the attendance form
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the attendance calendar
            initializeAttendanceCalendar();
        });
        
        // Attendance Calendar Functionality
        function autoLoadStudents() {
            const sectionId = document.getElementById('attendance_section').value;
            const attendanceDate = document.getElementById('attendance_date').value;
            const attendanceSession = document.getElementById('attendance_session').value;
            const studentListContainer = document.getElementById('studentListContainer');

            if (!sectionId || !attendanceDate || !attendanceSession) {
                studentListContainer.innerHTML = '<div class="alert alert-info text-center" style="border-radius: 10px;"><i class="fas fa-info-circle"></i> <strong>Instructions:</strong> Please select date and session to automatically load your students.</div>';
                return;
            }

            // Show loading indicator
            studentListContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Loading students...</p>
                </div>
            `;

            fetch(`get_students_for_attendance.php?attendance_section=${sectionId}&attendance_date=${attendanceDate}&attendance_session=${attendanceSession}`) 
                .then(response => response.text()) 
                .then(html => {
                    studentListContainer.innerHTML = html;
                    // Attach event listener to the new submit button after it's loaded
                    const submitAttendanceBtn = document.getElementById('submitAttendanceBtn');
                    if (submitAttendanceBtn) {
                        submitAttendanceBtn.addEventListener('click', submitAttendance);
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentListContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error loading students. Please try again.</div>';
                });
        }

        // Add event listeners for auto-loading
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceDate = document.getElementById('attendance_date');
            const attendanceSession = document.getElementById('attendance_session');
            
            if (attendanceDate) {
                attendanceDate.addEventListener('change', autoLoadStudents);
            }
            
            if (attendanceSession) {
                attendanceSession.addEventListener('change', autoLoadStudents);
            }
        });

        // Function to handle attendance submission via AJAX
        function submitAttendance() {
            const studentListContainer = document.getElementById('studentListContainer');
            const attendanceSection = studentListContainer.querySelector('input[name="attendance_section"]').value;
            const attendanceDate = studentListContainer.querySelector('input[name="attendance_date"]').value;
            const attendanceSession = studentListContainer.querySelector('input[name="attendance_session"]').value;
            
            const attendanceStatuses = {};
            
            // Get all checked radio buttons
            studentListContainer.querySelectorAll('.attendance-radio:checked').forEach(radio => {
                const studentId = radio.getAttribute('data-student-id');
                const status = radio.getAttribute('data-status');
                const session = radio.name.match(/\[(.*?)\]$/)[1]; // Extract session from name
                
                if (!attendanceStatuses[studentId]) {
                    attendanceStatuses[studentId] = {};
                }
                attendanceStatuses[studentId][session] = status;
            });

            if (!attendanceSection || !attendanceDate || !attendanceSession || Object.keys(attendanceStatuses).length === 0) {
                alert('Please mark attendance for at least one student before submitting.');
                return;
            }

            // Show loading indicator for submission
            const originalContent = studentListContainer.innerHTML;
            studentListContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Submitting...</span>
                    </div>
                    <h5 class="text-muted mt-3">Submitting Attendance...</h5>
                    <p class="text-muted">Please wait while we save your attendance data.</p>
                </div>
            `;

            // Prepare form data
            const formData = new FormData();
            formData.append('submit_attendance', '1');
            formData.append('attendance_section', attendanceSection);
            formData.append('attendance_date', attendanceDate);
            formData.append('attendance_session', attendanceSession);
            
            // Add attendance statuses
            for (const studentId in attendanceStatuses) {
                for (const session in attendanceStatuses[studentId]) {
                    formData.append(`attendance_status[${studentId}][${session}]`, attendanceStatuses[studentId][session]);
                }
            }

            fetch('faculty_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Create a temporary div to parse the HTML response
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Extract the alert messages from the response
                const successAlert = tempDiv.querySelector('.alert-success');
                const errorAlert = tempDiv.querySelector('.alert-danger');

                let message = '';
                if (successAlert) {
                    message = `
                        <div class="text-center py-5">
                            <div class="success-animation">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-success mt-3">Attendance Submitted Successfully!</h4>
                            <p class="text-muted">${successAlert.textContent}</p>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Refresh Dashboard
                            </button>
                        </div>
                    `;
                    
                    // Close the modal after successful submission
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('attendanceModal'));
                        if (modal) {
                            modal.hide();
                        }
                        // Reload the page to show updated statistics
                        window.location.reload();
                    }, 3000);
                } else if (errorAlert) {
                    message = `
                        <div class="text-center py-5">
                            <div class="error-animation">
                                <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-danger mt-3">Submission Failed</h4>
                            <p class="text-muted">${errorAlert.textContent}</p>
                            <button class="btn btn-secondary" onclick="restoreOriginalContent()">
                                <i class="fas fa-arrow-left"></i> Try Again
                            </button>
                        </div>
                    `;
                } else {
                    message = `
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle text-info" style="font-size: 4rem;"></i>
                            <h4 class="text-info mt-3">Attendance Processed</h4>
                            <p class="text-muted">Your attendance submission has been processed.</p>
                        </div>
                    `;
                }
                
                // Display the message
                studentListContainer.innerHTML = message;
                
                // Store original content for restoration
                window.originalAttendanceContent = originalContent;
            })
            .catch(error => {
                console.error('Error submitting attendance:', error);
                studentListContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="text-warning mt-3">Connection Error</h4>
                        <p class="text-muted">Unable to submit attendance. Please check your internet connection and try again.</p>
                        <button class="btn btn-secondary" onclick="restoreOriginalContent()">
                            <i class="fas fa-arrow-left"></i> Try Again
                        </button>
                    </div>
                `;
                
                // Store original content for restoration
                window.originalAttendanceContent = originalContent;
            });
        }
        
        // Function to restore original content
        function restoreOriginalContent() {
            if (window.originalAttendanceContent) {
                document.getElementById('studentListContainer').innerHTML = window.originalAttendanceContent;
                const submitBtn = document.getElementById('submitAttendanceBtn');
                if (submitBtn) {
                    submitBtn.addEventListener('click', submitAttendance);
                }
            }
        }

        // Auto-load students if a section is pre-selected in the attendance form
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceSectionSelect = document.getElementById('attendance_section');
            if (attendanceSectionSelect && attendanceSectionSelect.value !== '') {
                // Trigger the click event on the Load Students button
                document.getElementById('loadStudentsBtn').click();
            }
            
            // Initialize the attendance calendar
            initializeAttendanceCalendar();
        });
        
        // Attendance Calendar Functionality
        let currentCalendarDate = new Date();
        let attendanceData = <?php echo json_encode($attendance_calendar_data); ?>;
        
        function initializeAttendanceCalendar() {
            renderCalendar();
        }
        
        function changeMonth(direction) {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + direction);
            loadAttendanceData();
        }
        
        function loadAttendanceData() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth() + 1;
            
            // Show loading indicator
            document.getElementById('attendanceCount').innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Loading attendance data...';
            
            fetch(`get_calendar_attendance.php?year=${year}&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        attendanceData = data.attendance_data;
                        renderCalendar();
                    } else {
                        console.error('Error loading attendance data:', data.error);
                        document.getElementById('attendanceCount').textContent = 'Error loading data';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('attendanceCount').textContent = 'Error loading data';
                });
        }
        
        function renderCalendar() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            const today = new Date();

            console.log('Rendering calendar for:', year, month + 1);
            console.log('Attendance data:', attendanceData);

            // Update calendar title
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            document.getElementById('calendarTitle').textContent = `${monthNames[month]} ${year}`;

            // Calculate calendar layout
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();

            // Generate calendar days
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            // Add empty cells for days before the first day of the month
            for (let i = 0; i < startingDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                calendarDays.appendChild(emptyDay);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = day;

                console.log('Processing date:', dateStr, 'Has attendance:', attendanceData.hasOwnProperty(dateStr));

                // Check if this is today
                if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                    dayElement.classList.add('today');
                }

                // Check attendance data for this date
                if (attendanceData.hasOwnProperty(dateStr)) {
                    const data = attendanceData[dateStr];
                    const sessions = data.sessions;
                    const sessionCount = data.session_count;

                    dayElement.classList.add('has-attendance');
                    console.log('Added has-attendance class for:', dateStr);

                    // Add checkmark icon to indicate attendance is submitted
                    const checkmarkIcon = document.createElement('div');
                    checkmarkIcon.className = 'attendance-checkmark';
                    checkmarkIcon.innerHTML = '✓';
                    checkmarkIcon.title = 'Attendance submitted for this date';
                    dayElement.appendChild(checkmarkIcon);

                    // Set background color based on sessions attended
                    if (sessionCount >= 2) {
                        // Both sessions attended - dark green
                        dayElement.classList.add('both-sessions');
                    } else if (sessionCount === 1) {
                        // One session attended - light green
                        dayElement.classList.add('one-session');
                    }

                    // Add session indicators
                    const sessionIndicators = document.createElement('div');
                    sessionIndicators.className = 'session-indicators';

                    if (sessions.includes('forenoon')) {
                        const fnIndicator = document.createElement('div');
                        fnIndicator.className = 'session-indicator forenoon';
                        fnIndicator.title = 'Forenoon session marked';
                        sessionIndicators.appendChild(fnIndicator);
                    }

                    if (sessions.includes('afternoon')) {
                        const anIndicator = document.createElement('div');
                        anIndicator.className = 'session-indicator afternoon';
                        anIndicator.title = 'Afternoon session marked';
                        sessionIndicators.appendChild(anIndicator);
                    }

                    dayElement.appendChild(sessionIndicators);

                    // Add click event to show attendance details
                    dayElement.addEventListener('click', () => showAttendanceDetails(dateStr, sessions));
                    dayElement.style.cursor = 'pointer';
                } else {
                    dayElement.classList.add('no-attendance');
                    console.log('Added no-attendance class for:', dateStr);
                    // Add cross icon for days without attendance
                    const crossIcon = document.createElement('div');
                    crossIcon.className = 'attendance-cross';
                    crossIcon.innerHTML = '✗';
                    crossIcon.title = 'No attendance submitted for this date';
                    dayElement.appendChild(crossIcon);
                }

                dayElement.appendChild(dayNumber);
                calendarDays.appendChild(dayElement);
            }

            // Update attendance count
            const attendanceCount = Object.keys(attendanceData).filter(date => {
                const dateObj = new Date(date);
                return dateObj.getFullYear() === year && dateObj.getMonth() === month;
            }).length;

            document.getElementById('attendanceCount').textContent =
                `${attendanceCount} days with attendance records this month`;
        }
        
        function showAttendanceDetails(date, sessions) {
            const formattedDate = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            let sessionText = sessions.map(s => 
                s === 'forenoon' ? '🌅 Forenoon' : '🌞 Afternoon'
            ).join(', ');
            
            // Create a nicer modal instead of alert
            const modal = document.createElement('div');
            modal.className = 'attendance-detail-modal';
            modal.innerHTML = `
                <div class="modal-backdrop" onclick="closeAttendanceDetail()"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5><i class="fas fa-calendar-check"></i> Attendance Details</h5>
                        <button type="button" onclick="closeAttendanceDetail()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Date:</strong> ${formattedDate}</p>
                        <p><strong>Sessions Marked:</strong> ${sessionText}</p>
                        <div class="session-details">
                            ${sessions.map(s => `
                                <div class="session-badge ${s}">
                                    ${s === 'forenoon' ? '🌅 Forenoon' : '🌞 Afternoon'}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        function closeAttendanceDetail() {
            const modal = document.querySelector('.attendance-detail-modal');
            if (modal) {
                modal.remove();
            }
        }

        function showMissingAttendanceDates() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();

            const missingDates = [];
            const today = new Date();

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dateObj = new Date(dateStr);

                // Skip future dates
                if (dateObj > today) continue;

                // Skip Sundays (0 = Sunday)
                if (dateObj.getDay() === 0) continue;

                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                const formattedDate = dateObj.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });

                // Check which sessions are missing
                const missingSessions = [];
                let hasAnyAttendance = false;
                
                if (attendanceData.hasOwnProperty(dateStr)) {
                    const sessions = attendanceData[dateStr].sessions;
                    hasAnyAttendance = true;
                    
                    // Check which sessions are missing
                    if (!sessions.includes('forenoon')) {
                        missingSessions.push('Forenoon');
                    }
                    if (!sessions.includes('afternoon')) {
                        missingSessions.push('Afternoon');
                    }
                } else {
                    // No attendance at all for this date
                    missingSessions.push('Forenoon', 'Afternoon');
                }

                // Add to missing dates if there are any missing sessions
                if (missingSessions.length > 0) {
                    missingDates.push({
                        date: dateStr,
                        dayName: dayName,
                        formattedDate: formattedDate,
                        missingSessions: missingSessions,
                        hasPartialAttendance: hasAnyAttendance && missingSessions.length < 2
                    });
                }
            }

            // Create modal to show missing dates with session details
            const modal = document.createElement('div');
            modal.className = 'attendance-detail-modal';
            modal.innerHTML = `
                <div class="modal-backdrop" onclick="closeAttendanceDetail()"></div>
                <div class="modal-content" style="max-width: 700px;">
                    <div class="modal-header">
                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Missing Attendance Report</h5>
                        <button type="button" onclick="closeAttendanceDetail()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p><strong>Month:</strong> ${new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-box text-center">
                                        <div class="stat-number text-danger">${missingDates.length}</div>
                                        <div class="stat-label">Dates with Missing Sessions</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-box text-center">
                                        <div class="stat-number text-warning">${missingDates.filter(d => d.hasPartialAttendance).length}</div>
                                        <div class="stat-label">Partially Marked Dates</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-box text-center">
                                        <div class="stat-number text-info">${missingDates.reduce((total, d) => total + d.missingSessions.length, 0)}</div>
                                        <div class="stat-label">Total Missing Sessions</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${missingDates.length > 0 ? `
                            <div class="missing-dates-list">
                                <h6><i class="fas fa-calendar-times"></i> Dates with Missing Attendance Sessions:</h6>
                                <div class="missing-dates-grid">
                                    ${missingDates.map(dateInfo => `
                                        <div class="missing-date-item ${dateInfo.hasPartialAttendance ? 'partial-attendance' : 'no-attendance'}">
                                            <div class="date-info">
                                                <div class="date-header">
                                                    <strong>${dateInfo.formattedDate}</strong>
                                                    <span class="badge ${dateInfo.hasPartialAttendance ? 'badge-warning' : 'badge-danger'}">
                                                        ${dateInfo.hasPartialAttendance ? 'Partial' : 'No Attendance'}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block">${dateInfo.dayName}</small>
                                                <div class="missing-sessions-info">
                                                    <strong>Missing Sessions:</strong>
                                                    <div class="session-badges mt-1">
                                                        ${dateInfo.missingSessions.map(session => `
                                                            <span class="session-badge missing">
                                                                <i class="fas fa-${session === 'Forenoon' ? 'sun' : 'moon'}"></i>
                                                                ${session}
                                                            </span>
                                                        `).join('')}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="action-buttons">
                                                ${dateInfo.missingSessions.includes('Forenoon') ? `
                                                    <button type="button" class="btn btn-sm btn-warning mb-1" onclick="markAttendanceForDateSession('${dateInfo.date}', 'forenoon')" title="Mark Forenoon Session">
                                                        <i class="fas fa-sun"></i> FN
                                                    </button>
                                                ` : ''}
                                                ${dateInfo.missingSessions.includes('Afternoon') ? `
                                                    <button type="button" class="btn btn-sm btn-info mb-1" onclick="markAttendanceForDateSession('${dateInfo.date}', 'afternoon')" title="Mark Afternoon Session">
                                                        <i class="fas fa-moon"></i> AN
                                                    </button>
                                                ` : ''}
                                                <button type="button" class="btn btn-sm btn-primary" onclick="markAttendanceForDate('${dateInfo.date}')" title="Mark Both Sessions">
                                                    <i class="fas fa-plus"></i> Both
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6><i class="fas fa-info-circle text-primary"></i> Legend:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="legend-item">
                                                <span class="badge badge-danger">No Attendance</span>
                                                <span class="ms-2">Both sessions missing</span>
                                            </div>
                                            <div class="legend-item">
                                                <span class="badge badge-warning">Partial</span>
                                                <span class="ms-2">One session missing</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="legend-item">
                                                <span class="session-badge missing"><i class="fas fa-sun"></i> FN</span>
                                                <span class="ms-2">Forenoon Session</span>
                                            </div>
                                            <div class="legend-item">
                                                <span class="session-badge missing"><i class="fas fa-moon"></i> AN</span>
                                                <span class="ms-2">Afternoon Session</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ` : `
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                <strong>Excellent!</strong> All working days in this month have complete attendance records (both Forenoon and Afternoon sessions).
                            </div>
                        `}
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function markAttendanceForDate(date) {
            // Close the missing dates modal
            closeAttendanceDetail();

            // Redirect to attendance marking page with the selected date (no specific session, will allow both)
            const assignedSections = <?php echo json_encode($assigned_sections); ?>;
            if (assignedSections && assignedSections.length > 0) {
                const selectedSection = assignedSections[0];
                window.location.href = `attendance_marking.php?section=${selectedSection}&date=${date}`;
            } else {
                alert('No sections assigned. Please contact administrator.');
            }
        }

        function markAttendanceForDateSession(date, session) {
            // Close the missing dates modal
            closeAttendanceDetail();

            // Redirect to attendance marking page with the selected date and specific session
            const assignedSections = <?php echo json_encode($assigned_sections); ?>;
            if (assignedSections && assignedSections.length > 0) {
                const selectedSection = assignedSections[0];
                window.location.href = `attendance_marking.php?section=${selectedSection}&date=${date}&session=${session}`;
            } else {
                alert('No sections assigned. Please contact administrator.');
            }
        }
    </script>
    
    <style>
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(7,101,147,0.25);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Attendance Modal Specific Styles */
        .attendance-select {
            transition: all 0.3s ease;
        }
        
        .attendance-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(7,101,147,0.25);
        }
        
        .attendance-select option[value="present"] {
            background-color: #d4edda;
            color: #155724;
        }
        
        .attendance-select option[value="absent"] {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .attendance-select option[value="holiday"] {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(7,101,147,0.05);
        }
        
        /* Session selection styling */
        #attendance_session option {
            padding: 8px;
        }
        
        /* Quick action buttons */
        .btn-group .btn {
            border-radius: 8px;
            margin-right: 5px;
        }
        
        /* Modal improvements */
        .modal-xl {
            max-width: 95%;
        }
        
        @media (max-width: 768px) {
            .modal-xl {
                max-width: 98%;
                margin: 10px;
            }
            
            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .btn-group .btn {
                margin-right: 0;
                margin-bottom: 5px;
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            .attendance-select {
                min-width: 120px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 20px 15px;
            }
            
            .form-control {
                font-size: 16px;
                padding: 12px 15px;
            }
            
            .btn {
                padding: 12px 20px;
                font-size: 14px;
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            /* Export modal responsive improvements */
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }
            
            .modal-body {
                padding: 15px;
            }
            
            .section-option-export {
                padding: 10px !important;
            }
            
            .section-option-export i {
                font-size: 1.5rem !important;
            }
            
            .section-option-export h6 {
                font-size: 0.9rem !important;
            }
        }
        
        /* Export modal specific styles */
        .section-option-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(7,101,147,0.15);
            border-color: var(--primary-blue);
        }
        
        .section-option-export.selected {
            border-color: var(--primary-blue) !important;
            background-color: #e8f4fd !important;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                display: block;
                width: 100%;
            }
            .table {
                min-width: 600px;
            }
        }
        
        /* Attendance Calendar Styles */
        .attendance-calendar {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            overflow: hidden;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--light-blue);
            border-bottom: 1px solid #e3e6f0;
        }
        
        .calendar-title {
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 1.1rem;
        }
        
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .weekday {
            padding: 10px 5px;
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e3e6f0;
            padding: 1px;
        }
        
        .calendar-day {
            background: #fff;
            min-height: 45px;
            padding: 5px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            transition: all 0.2s ease;
        }
        
        .calendar-day.empty {
            background: #f8f9fa;
        }
        
        .calendar-day.today {
            background: var(--light-blue);
            border: 2px solid var(--primary-blue);
        }
        
        .calendar-day.today .day-number {
            color: var(--primary-blue);
            font-weight: bold;
        }
        
        .calendar-day.has-attendance {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        
        .calendar-day.has-attendance:hover {
            background: #c3e6cb;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .calendar-day.no-attendance {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        
        .day-number {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .attendance-checkmark {
            position: absolute;
            top: 2px;
            right: 2px;
            color: #28a745;
            font-size: 0.8rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #28a745;
        }

        .attendance-cross {
            position: absolute;
            top: 2px;
            right: 2px;
            color: #dc3545;
            font-size: 0.8rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dc3545;
        }

        .session-indicators {
            display: flex;
            gap: 2px;
            margin-top: auto;
        }
        
        .session-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 1px solid #fff;
        }
        
        .session-indicator.forenoon {
            background: #ffc107;
        }
        
        .session-indicator.afternoon {
            background: #fd7e14;
        }
        
        .calendar-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-top: 1px solid #e3e6f0;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }
        
        .legend-color.present {
            background: #d4edda;
        }
        
        .legend-color.absent {
            background: #f8d7da;
        }
        
        .legend-color.submitted {
            background: #28a745; /* green */
            border: 1px solid #1e7e34;
        }
        
        .legend-color.not-submitted {
            background: #f8d7da; /* light red */
            border: 1px solid #f5c6cb;
        }
        
        .legend-color.today-indicator {
            background: #007bff; /* blue */
            border: 1px solid #0056b3;
        }
        
        .legend-text {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Responsive calendar styles */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 40px;
                padding: 3px;
            }

            .day-number {
                font-size: 0.8rem;
            }

            .attendance-checkmark, .attendance-cross {
                width: 14px;
                height: 14px;
                font-size: 0.7rem;
                top: 1px;
                right: 1px;
            }

            .session-indicator {
                width: 6px;
                height: 6px;
            }

            .calendar-header {
                padding: 10px;
            }

            .calendar-title {
                font-size: 1rem;
            }

            .weekday {
                padding: 8px 3px;
                font-size: 0.75rem;
            }

            .calendar-legend {
                gap: 15px;
                padding: 8px;
            }

            .legend-text {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 480px) {
            .calendar-day {
                min-height: 35px;
                padding: 2px;
            }

            .day-number {
                font-size: 0.75rem;
            }

            .attendance-checkmark, .attendance-cross {
                width: 12px;
                height: 12px;
                font-size: 0.6rem;
                top: 1px;
                right: 1px;
            }

            .session-indicator {
                width: 5px;
                height: 5px;
            }

            .calendar-legend {
                flex-direction: column;
                gap: 5px;
                align-items: center;
            }
        }
        
        /* Attendance Detail Modal Styles */
        .attendance-detail-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .attendance-detail-modal .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .attendance-detail-modal .modal-content {
            background: white;
            border-radius: 15px;
            padding: 0;
            max-width: 400px;
            width: 90%;
            max-height: 90vh;
            position: relative;
            z-index: 10001;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
            margin: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .attendance-detail-modal .modal-header {
            background: var(--light-blue);
            padding: 15px 20px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e3e6f0;
            position: sticky;
            top: 0;
            z-index: 10002;
        }
        
        .attendance-detail-modal .modal-header h5 {
            margin: 0;
            color: var(--primary-blue);
            font-weight: 600;
        }
        
        .attendance-detail-modal .modal-header button {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.2s ease;
        }
        
        .attendance-detail-modal .modal-header button:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #495057;
        }
        
        .attendance-detail-modal .modal-body {
            padding: 20px;
            max-height: calc(90vh - 80px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .attendance-detail-modal .modal-body p {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .session-details {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .session-badge {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            flex: 1;
        }
        
        .session-badge.forenoon {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .session-badge.afternoon {
            background: #fde2e4;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
            height: 100%;
        }

        /* Mobile-specific modal adjustments */
        @media (max-width: 768px) {
            .attendance-detail-modal {
                padding: 10px;
                align-items: flex-start;
                padding-top: 20px;
            }

            .attendance-detail-modal .modal-content {
                max-width: 95%;
                width: 95%;
                max-height: 85vh;
                margin: 0;
            }

            .attendance-detail-modal .modal-header {
                padding: 12px 15px;
                position: sticky;
                top: 0;
            }

            .attendance-detail-modal .modal-body {
                padding: 15px;
                max-height: calc(85vh - 60px);
            }

            /* Enhanced missing dates modal for mobile */
            .missing-dates-grid {
                max-height: 50vh;
                padding: 10px;
            }

            .missing-date-item {
                padding: 12px;
                margin-bottom: 10px;
            }

            .stat-box {
                padding: 10px;
            }

            .stat-number {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .attendance-detail-modal {
                padding: 5px;
                padding-top: 10px;
            }

            .attendance-detail-modal .modal-content {
                max-width: 98%;
                width: 98%;
                max-height: 90vh;
                border-radius: 10px;
            }

            .attendance-detail-modal .modal-header {
                padding: 10px 12px;
                border-radius: 10px 10px 0 0;
            }

            .attendance-detail-modal .modal-body {
                padding: 12px;
                max-height: calc(90vh - 50px);
            }

            .missing-dates-grid {
                max-height: 45vh;
                padding: 8px;
            }
        }

        /* Missing Attendance Dates Styles */
        .missing-dates-list h6 {
            color: var(--primary-blue);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .missing-dates-grid {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 10px;
        }

        .missing-date-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            transition: all 0.2s ease;
        }

        .missing-date-item:hover {
            background: #e8f4fd;
            border-color: var(--primary-blue);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(7,101,147,0.1);
        }

        .missing-date-item .date-info strong {
            display: block;
            color: #495057;
            font-size: 0.9rem;
        }

        .missing-date-item .date-info small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .missing-date-item .btn {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        /* Responsive styles for missing dates */
        @media (max-width: 768px) {
            .missing-date-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .missing-date-item .btn {
                align-self: stretch;
                text-align: center;
            }
        }

        /* Enhanced Missing Attendance Report Styles */
        .missing-dates-grid {
            display: grid;
            gap: 15px;
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .missing-date-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .missing-date-item:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 12px rgba(7,101,147,0.15);
            transform: translateY(-2px);
        }

        .missing-date-item.partial-attendance {
            border-left: 4px solid #ffc107;
        }

        .missing-date-item.no-attendance {
            border-left: 4px solid #dc3545;
        }

        .date-info {
            flex: 1;
        }

        .date-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .date-header strong {
            color: var(--primary-blue);
            font-size: 1rem;
        }

        .missing-sessions-info {
            margin-top: 10px;
        }

        .missing-sessions-info strong {
            display: block;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .session-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .session-badge.missing {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            background: #ffeaa7;
            color: #856404;
            border: 1px solid #f39c12;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .session-badge.missing i {
            font-size: 0.7rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 80px;
            margin-left: 15px;
        }

        .action-buttons .btn {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
            white-space: nowrap;
        }

        .stat-box {
            padding: 15px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 12px rgba(7,101,147,0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        /* Responsive styles for enhanced missing attendance report */
        @media (max-width: 768px) {
            .missing-date-item {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                flex-direction: row;
                justify-content: space-around;
                margin-left: 0;
                margin-top: 15px;
                min-width: auto;
            }

            .action-buttons .btn {
                flex: 1;
                margin: 0 2px;
            }

            .date-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .session-badges {
                justify-content: flex-start;
            }

            .stat-number {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .missing-dates-grid {
                padding: 10px;
            }

            .missing-date-item {
                padding: 12px;
            }

            .action-buttons .btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            .session-badge.missing {
                font-size: 0.7rem;
                padding: 3px 8px;
            }

            .stat-box {
                padding: 10px;
            }
        }

        /* Dashboard Card Styles */
        .dashboard-card {
            background: #fff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 16px rgba(7,101,147,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(7,101,147,0.15);
        }

        .dashboard-card .card-header {
            background: var(--light-blue);
            border-bottom: 1px solid #e3e6f0;
            border-radius: 15px 15px 0 0;
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-blue);
        }

        .dashboard-card .card-body {
            padding: 20px;
        }

        /* Profile Section Styles */
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-picture-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .profile-placeholder {
            width: 80px;
            height: 80px;
            background: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 2rem;
            border: 3px solid #e3e6f0;
        }

        /* Info Items */
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        /* Action Buttons */
        .action-btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px 15px;
            margin: 8px 0;
            background: #f8f9fa;
            color: var(--primary-blue);
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .action-btn:hover {
            background: var(--light-blue);
            color: var(--primary-blue);
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(7,101,147,0.1);
        }

        .action-btn i {
            margin-right: 8px;
        }

        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: var(--light-blue);
            border-color: var(--primary-blue);
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-blue);
            margin-bottom: 5px;
            display: block;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* CSS Variables */
        :root {
            --primary-blue: #07659b;
            --light-blue: #e8f4fd;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .profile-placeholder {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .dashboard-card .card-body {
                padding: 15px;
            }

            .action-btn {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</body>
</html>
