<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if faculty is logged in
if (!isset($_SESSION['faculty_logged_in']) || !$_SESSION['faculty_logged_in']) {
    header('Location: login.php');
    exit();
}

include './connect.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
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
$stmt = mysqli_prepare($conn, $faculty_query);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$faculty_result = mysqli_stmt_get_result($stmt);
$faculty_data = mysqli_fetch_assoc($faculty_result);

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

$classes = [
    '28csit_a_attendance' => '2/4 CSIT-A',
    '28csit_b_attendance' => '2/4 CSIT-B',
    '28csd_attendance'    => '2/4 CSD',
    '27csit_attendance'   => '3/4 CSIT',
    '27csd_attendance'    => '3/4 CSD',
    '26csd_attendance'    => '4/4 CSD',
];

$success = '';
$error = '';

// Get selected class filter from GET/POST or default to all assigned sections
$selected_class_filter = isset($_REQUEST['class_filter']) ? (int)$_REQUEST['class_filter'] : 0;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 30;
$offset = ($page - 1) * $limit;
$search_query_param = isset($_REQUEST['search_query']) ? trim($_REQUEST['search_query']) : '';

// Handle appreciation points submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['award_appreciation'])) {
    try {
        $mode = isset($_POST['mode']) ? $_POST['mode'] : 'bulk';
        $event_id = isset($_POST['event_id']) && !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
        
        // Get student IDs based on mode
        if ($mode === 'single') {
            $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
            $student_ids = !empty($student_id) ? [$student_id] : [];
        } else {
            $student_ids = isset($_POST['student_ids']) && is_array($_POST['student_ids']) ? $_POST['student_ids'] : [];
        }
        
        // Validation
        if (empty($student_ids) || $points < 1 || empty($reason)) {
            $error = "Please fill all required fields (Students, Points, Reason).";
        } else {
            $success_count = 0;
            $error_count = 0;
            
            // Insert appreciation for each student
            foreach ($student_ids as $student_id) {
                $student_id = trim($student_id);
                if (empty($student_id)) continue;
                
                try {
                    // Build query based on whether event_id is provided
                    if ($event_id === null) {
                        // Insert without event_id
                        $insert_query = "INSERT INTO appreciations (student_id, points, reason, created_by, created_at) 
                                       VALUES (?, ?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conn, $insert_query);
                        
                        if ($stmt === false) {
                            throw new Exception("Error preparing statement: " . mysqli_error($conn));
                        }
                        
                        mysqli_stmt_bind_param($stmt, "sisi", $student_id, $points, $reason, $_SESSION['faculty_id']);
                    } else {
                        // Insert with event_id
                        $insert_query = "INSERT INTO appreciations (student_id, event_id, points, reason, created_by, created_at) 
                                       VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conn, $insert_query);
                        
                        if ($stmt === false) {
                            throw new Exception("Error preparing statement: " . mysqli_error($conn));
                        }
                        
                        mysqli_stmt_bind_param($stmt, "siisi", $student_id, $event_id, $points, $reason, $_SESSION['faculty_id']);
                    }
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                    
                    mysqli_stmt_close($stmt);
                } catch (Exception $e) {
                    $error_count++;
                }
            }
            
            if ($success_count > 0) {
                if ($success_count == 1) {
                    $success = "Appreciation points awarded successfully!";
                } else {
                    $success = "Appreciation points awarded successfully to $success_count student(s)!";
                }
                if ($error_count > 0) {
                    $success .= " ($error_count failed)";
                }
            } else {
                $error = "Failed to award appreciation points.";
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle delete appreciation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appreciation'])) {
    try {
        $appreciation_id = isset($_POST['appreciation_id']) ? (int)$_POST['appreciation_id'] : 0;
        
        if ($appreciation_id > 0) {
            // Verify that this appreciation was created by the current faculty OR faculty is assigned to student's class
            $verify_query = "SELECT a.created_by, s.class_id FROM appreciations a JOIN students s ON a.student_id = s.student_id WHERE a.appreciation_id = ?";
            $stmt = mysqli_prepare($conn, $verify_query);
            mysqli_stmt_bind_param($stmt, "i", $appreciation_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $appreciation = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if ($appreciation && ($appreciation['created_by'] == $_SESSION['faculty_id'] || in_array($appreciation['class_id'], $assigned_sections))) {
                // Delete the appreciation
                $delete_query = "DELETE FROM appreciations WHERE appreciation_id = ?";
                $stmt = mysqli_prepare($conn, $delete_query);
                mysqli_stmt_bind_param($stmt, "i", $appreciation_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Appreciation deleted successfully!";
                } else {
                    throw new Exception("Error deleting appreciation: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "You don't have permission to delete this appreciation.";
            }
        } else {
            $error = "Invalid appreciation ID.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle edit appreciation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_appreciation'])) {
    try {
        $appreciation_id = isset($_POST['appreciation_id']) ? (int)$_POST['appreciation_id'] : 0;
        $points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
        
        if ($appreciation_id > 0 && $points > 0 && !empty($reason)) {
            // Verify that this appreciation was created by the current faculty OR faculty is assigned to student's class
            $verify_query = "SELECT a.created_by, s.class_id FROM appreciations a JOIN students s ON a.student_id = s.student_id WHERE a.appreciation_id = ?";
            $stmt = mysqli_prepare($conn, $verify_query);
            mysqli_stmt_bind_param($stmt, "i", $appreciation_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $appreciation = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if ($appreciation && ($appreciation['created_by'] == $_SESSION['faculty_id'] || in_array($appreciation['class_id'], $assigned_sections))) {
                // Update the appreciation
                $update_query = "UPDATE appreciations SET points = ?, reason = ? WHERE appreciation_id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "isi", $points, $reason, $appreciation_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Appreciation updated successfully!";
                } else {
                    throw new Exception("Error updating appreciation: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "You don't have permission to edit this appreciation.";
            }
        } else {
            $error = "Invalid input for editing appreciation.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


// Fetch events for the dropdown
$events_query = "SELECT event_id, title FROM events ORDER BY event_date DESC";
$events_result = mysqli_query($conn, $events_query);
$events = [];
while ($event = mysqli_fetch_assoc($events_result)) {
    $events[] = $event;
}

// Fetch available classes for the faculty
$available_classes = [];
if (!empty($assigned_sections)) {
    $class_ids_in = implode(',', array_map('intval', $assigned_sections));
    $classes_query = "SELECT class_id, year, branch, section 
                      FROM classes 
                      WHERE class_id IN ($class_ids_in) 
                      ORDER BY year, branch, section";
    $classes_result = mysqli_query($conn, $classes_query);
    while ($class = mysqli_fetch_assoc($classes_result)) {
        $available_classes[] = $class;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title>Manage Appreciations - SRKR Engineering College</title>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <div class="page-title">
        <div class="container">
            <h2><i class="fas fa-award"></i> Manage Appreciations</h2>
            <p>Award and view appreciation points for students</p>
        </div>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($error): ?>
                <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="text-end mb-4">
                <a href="faculty_dashboard.php" class="btn btn-primary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="faculty_logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Appreciation Points Section -->
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 16px rgba(7,101,147,0.1); border-radius: 15px;">
                <div class="card-header" style="background: var(--light-blue); border-bottom: 1px solid #e3e6f0; border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0" style="color: var(--primary-blue); font-weight: 600;">
                        <i class="fas fa-award"></i> Award Appreciation Points
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-4" id="appreciationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">
                                <i class="fas fa-user"></i> Single Student
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab">
                                <i class="fas fa-users"></i> Multiple Students
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="appreciationTabContent">
                        <!-- Single Student Form -->
                        <div class="tab-pane fade show active" id="single" role="tabpanel">
                            <form method="POST" action="faculty_appreciations.php" id="singleAppreciationForm">
                                <input type="hidden" name="mode" value="single">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="class_filter_single" class="form-label">Select Section</label>
                                        <select class="form-control" id="class_filter_single" name="class_filter" onchange="this.form.submit()">
                                            <option value="0">All Sections</option>
                                            <?php foreach ($available_classes as $class): ?>
                                                <option value="<?php echo $class['class_id']; ?>" 
                                                        <?php echo ($selected_class_filter == $class['class_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($class['year'] . '/' . $class['branch'] . '-' . $class['section']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="student_select_single" class="form-label">Select Student</label>
                                        <select class="form-control" id="student_select_single" name="student_id" required>
                                            <option value="">Choose a student...</option>
                                            <?php
                                            if (!empty($assigned_sections)) {
                                                if ($selected_class_filter > 0) {
                                                    $class_ids_in = (int)$selected_class_filter;
                                                    $students_query = "SELECT s.student_id, s.name, c.year, c.branch, c.section 
                                                                     FROM students s 
                                                                     JOIN classes c ON s.class_id = c.class_id 
                                                                     WHERE s.class_id = $class_ids_in 
                                                                     ORDER BY s.name";
                                                } else {
                                                    $class_ids_in = implode(',', array_map('intval', $assigned_sections));
                                                    $students_query = "SELECT s.student_id, s.name, c.year, c.branch, c.section 
                                                                     FROM students s 
                                                                     JOIN classes c ON s.class_id = c.class_id 
                                                                     WHERE s.class_id IN ($class_ids_in) 
                                                                     ORDER BY c.year, c.branch, c.section, s.name";
                                                }
                                                $students_result = mysqli_query($conn, $students_query);
                                                while ($student = mysqli_fetch_assoc($students_result)) {
                                                    echo '<option value="' . htmlspecialchars($student['student_id']) . '">' 
                                                        . htmlspecialchars($student['name']) . ' - ' 
                                                        . htmlspecialchars($student['year'] . '/' . $student['branch'] . '-' . $student['section'])
                                                        . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="event_select_single" class="form-label">Select Event (Optional)</label>
                                        <select class="form-control" id="event_select_single" name="event_id">
                                            <option value="">Choose an event...</option>
                                            <?php foreach ($events as $event): ?>
                                                <option value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                                    <?php echo htmlspecialchars($event['title']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <label for="points_single" class="form-label">Points</label>
                                        <input type="number" class="form-control" id="points_single" name="points" min="1" max="100" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="reason_single" class="form-label">Reason</label>
                                        <input type="text" class="form-control" id="reason_single" name="reason" required>
                                    </div>
                                </div>
                                <button type="submit" name="award_appreciation" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Award Points
                                </button>
                            </form>
                        </div>

                        <!-- Multiple Students Form -->
                        <div class="tab-pane fade" id="bulk" role="tabpanel">
                            <form method="POST" action="faculty_appreciations.php" id="bulkAppreciationForm">
                                <input type="hidden" name="mode" value="bulk">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="class_filter_bulk" class="form-label">Select Section</label>
                                        <select class="form-control" id="class_filter_bulk" name="class_filter" onchange="this.form.submit()">
                                            <option value="0">All Sections</option>
                                            <?php foreach ($available_classes as $class): ?>
                                                <option value="<?php echo $class['class_id']; ?>" 
                                                        <?php echo ($selected_class_filter == $class['class_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($class['year'] . '/' . $class['branch'] . '-' . $class['section']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="student_select_bulk" class="form-label">Select Students (Hold Ctrl/Cmd to select multiple)</label>
                                        <select class="form-control" id="student_select_bulk" name="student_ids[]" multiple size="8" required style="height: auto; min-height: 200px;">
                                            <?php
                                            if (!empty($assigned_sections)) {
                                                if ($selected_class_filter > 0) {
                                                    $class_ids_in = (int)$selected_class_filter;
                                                    $students_query = "SELECT s.student_id, s.name, c.year, c.branch, c.section 
                                                                     FROM students s 
                                                                     JOIN classes c ON s.class_id = c.class_id 
                                                                     WHERE s.class_id = $class_ids_in 
                                                                     ORDER BY s.name";
                                                } else {
                                                    $class_ids_in = implode(',', array_map('intval', $assigned_sections));
                                                    $students_query = "SELECT s.student_id, s.name, c.year, c.branch, c.section 
                                                                     FROM students s 
                                                                     JOIN classes c ON s.class_id = c.class_id 
                                                                     WHERE s.class_id IN ($class_ids_in) 
                                                                     ORDER BY c.year, c.branch, c.section, s.name";
                                                }
                                                $students_result = mysqli_query($conn, $students_query);
                                                while ($student = mysqli_fetch_assoc($students_result)) {
                                                    echo '<option value="' . htmlspecialchars($student['student_id']) . '">' 
                                                        . htmlspecialchars($student['name']) . ' - ' 
                                                        . htmlspecialchars($student['year'] . '/' . $student['branch'] . '-' . $student['section'])
                                                        . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) and click to select multiple students</small>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="event_select_bulk" class="form-label">Select Event (Optional)</label>
                                        <select class="form-control" id="event_select_bulk" name="event_id">
                                            <option value="">Choose an event...</option>
                                            <?php foreach ($events as $event): ?>
                                                <option value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                                    <?php echo htmlspecialchars($event['title']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="points_bulk" class="form-label">Points</label>
                                        <input type="number" class="form-control" id="points_bulk" name="points" min="1" max="100" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="reason_bulk" class="form-label">Reason</label>
                                        <input type="text" class="form-control" id="reason_bulk" name="reason" required>
                                    </div>
                                </div>
                                <button type="submit" name="award_appreciation" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Award Points to Selected Students
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Appreciation Points History -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <h6 class="mb-0">Recent Appreciation Points Awarded 
                                <?php if ($selected_class_filter > 0): ?>
                                    <?php 
                                    $filtered_class = array_filter($available_classes, function($c) use ($selected_class_filter) {
                                        return $c['class_id'] == $selected_class_filter;
                                    });
                                    $filtered_class = reset($filtered_class);
                                    if ($filtered_class) {
                                        echo '(' . htmlspecialchars($filtered_class['year'] . '/' . $filtered_class['branch'] . '-' . $filtered_class['section']) . ')';
                                    }
                                    ?>
                                <?php endif; ?>
                            </h6>
                            <div class="search-box mt-2 mt-md-0">
                                <form method="GET" action="faculty_appreciations.php" class="d-flex m-0">
                                    <input type="hidden" name="class_filter" value="<?php echo $selected_class_filter; ?>">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" name="search_query" id="tableSearch" class="form-control" placeholder="Search records..." style="max-width: 200px;" value="<?php echo htmlspecialchars($search_query_param); ?>">
                                        <button class="btn btn-primary" type="submit">Search</button>
                                        <?php if(!empty($search_query_param)): ?>
                                            <a href="faculty_appreciations.php?class_filter=<?php echo $selected_class_filter; ?>" class="btn btn-secondary">Clear</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Points</th>
                                        <th>Reason</th>
                                        <th>Awarded On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $search_condition = "";
                                    if (!empty($search_query_param)) {
                                        $escaped_search = mysqli_real_escape_string($conn, $search_query_param);
                                        $search_condition = " AND (s.name LIKE '%$escaped_search%' OR a.reason LIKE '%$escaped_search%') ";
                                    }

                                    // Count total records for pagination
                                    if ($selected_class_filter > 0) {
                                        $class_ids_in = (int)$selected_class_filter;
                                        $count_query = "SELECT COUNT(*) as total FROM appreciations a 
                                                        JOIN students s ON a.student_id = s.student_id 
                                                        WHERE s.class_id = $class_ids_in $search_condition";
                                    } else {
                                        $where_clause = empty($search_condition) ? "" : "WHERE 1=1 " . $search_condition;
                                        $count_query = "SELECT COUNT(*) as total FROM appreciations a 
                                                        JOIN students s ON a.student_id = s.student_id " . $where_clause;
                                    }
                                    $count_result = mysqli_query($conn, $count_query);
                                    $total_row = mysqli_fetch_assoc($count_result);
                                    $total_records = $total_row['total'] ?? 0;
                                    $total_pages = ceil(max(1, $total_records) / $limit);

                                    // Filter by selected class or show all
                                    if ($selected_class_filter > 0) {
                                        $class_ids_in = (int)$selected_class_filter;
                                        $recent_points_query = "SELECT a.*, s.name, c.year, c.branch, c.section, s.class_id 
                                                              FROM appreciations a 
                                                              JOIN students s ON a.student_id = s.student_id 
                                                              JOIN classes c ON s.class_id = c.class_id 
                                                              WHERE s.class_id = $class_ids_in $search_condition
                                                              ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset";
                                    } else {
                                        // Show all appreciations regardless of assigned sections
                                        $where_clause = empty($search_condition) ? "" : "WHERE 1=1 " . $search_condition;
                                        $recent_points_query = "SELECT a.*, s.name, c.year, c.branch, c.section, s.class_id 
                                                              FROM appreciations a 
                                                              JOIN students s ON a.student_id = s.student_id 
                                                              JOIN classes c ON s.class_id = c.class_id 
                                                              $where_clause
                                                              ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset";
                                    }
                                    
                                    $recent_points_result = mysqli_query($conn, $recent_points_query);
                                    if (mysqli_num_rows($recent_points_result) > 0) {
                                        while ($point = mysqli_fetch_assoc($recent_points_result)) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($point['name']) . ' (' 
                                                . htmlspecialchars($point['year'] . '/' . $point['branch'] . '-' . $point['section']) . ')</td>';
                                            echo '<td>' . htmlspecialchars($point['points']) . '</td>';
                                            echo '<td>' . htmlspecialchars($point['reason']) . '</td>';
                                            echo '<td>' . date('d M Y H:i', strtotime($point['created_at'])) . '</td>';
                                            echo '<td>';
                                            
                                            // Only show edit/delete buttons if current faculty created this appreciation OR is assigned to the student's class
                                            $can_edit = false;
                                            if (isset($point['class_id'])) {
                                                $can_edit = ($point['created_by'] == $_SESSION['faculty_id'] || in_array($point['class_id'], $assigned_sections));
                                            }
                                            
                                            if ($can_edit) {
                                                echo '<div style="display: flex; gap: 5px;">';
                                                echo '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' . $point['appreciation_id'] . '" data-points="' . htmlspecialchars($point['points']) . '" data-reason="' . htmlspecialchars($point['reason']) . '"><i class="fas fa-edit"></i></button>';
                                                echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this appreciation?\');">';
                                                echo '<input type="hidden" name="appreciation_id" value="' . $point['appreciation_id'] . '">';
                                                echo '<button type="submit" name="delete_appreciation" class="btn btn-sm btn-danger">';
                                                echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                                echo '</form>';
                                                echo '</div>';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No appreciation points awarded yet.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <nav aria-label="Appreciations pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php
                                $class_filter_param = "";
                                if ($selected_class_filter > 0) {
                                    $class_filter_param .= "&class_filter=" . $selected_class_filter;
                                }
                                if (!empty($search_query_param)) {
                                    $class_filter_param .= "&search_query=" . urlencode($search_query_param);
                                }
                                ?>
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $class_filter_param; ?>" tabindex="-1">Previous</a>
                                </li>
                                <?php 
                                // Show at most 5 page links around the current page
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1' . $class_filter_param . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $class_filter_param; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; 
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . $class_filter_param . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $class_filter_param; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="faculty_appreciations.php">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Appreciation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="appreciation_id" id="edit_appreciation_id">
                        <div class="mb-3">
                            <label class="form-label">Points</label>
                            <input type="number" class="form-control" name="points" id="edit_points" min="1" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <input type="text" class="form-control" name="reason" id="edit_reason" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_appreciation" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
    
    <style>
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(7,101,147,0.25);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            border-bottom: 3px solid transparent;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-blue);
            background: transparent;
            border-bottom: 3px solid var(--primary-blue);
            font-weight: 600;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom: 3px solid #ccc;
        }
        
        #student_select_bulk {
            min-height: 200px;
        }
        
        #student_select_bulk option {
            padding: 8px;
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
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Edit Modal Functionality
        const editBtns = document.querySelectorAll('.edit-btn');
        if (editBtns.length > 0) {
            // Use standard bootstrap modal if available
            let editModalInstance;
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                editModalInstance = new bootstrap.Modal(document.getElementById('editModal'));
            }
            
            const editIdInput = document.getElementById('edit_appreciation_id');
            const editPointsInput = document.getElementById('edit_points');
            const editReasonInput = document.getElementById('edit_reason');
            
            editBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    editIdInput.value = this.dataset.id;
                    editPointsInput.value = this.dataset.points;
                    editReasonInput.value = this.dataset.reason;
                    
                    if (editModalInstance) {
                        editModalInstance.show();
                    } else {
                        // Fallback fallback if bootstrap JS is not loaded
                        // Try jQuery if available
                        if (typeof $ !== 'undefined') {
                            $('#editModal').modal('show');
                        }
                    }
                });
            });
        }
    });
    </script>
</body>
</html>