<?php
session_start();
include "../utils/connect.php";

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Get the action parameter
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_dashboard_stats':
            getDashboardStats();
            break;
        
        case 'get_classes':
            getClasses();
            break;
        
        case 'add_class':
            addClass();
            break;
        
        case 'delete_class':
            deleteClass();
            break;
        
        case 'get_students':
            getStudents();
            break;
        
        case 'add_student':
            addStudent();
            break;
        
        case 'get_student':
            getStudent();
            break;
        
        case 'update_student':
            updateStudent();
            break;
        
        case 'delete_student':
            deleteStudent();
            break;
        
        case 'get_houses':
            getHouses();
            break;
        
        case 'search_students':
            searchStudents();
            break;
        
        case 'add_points_to_student':
            addPointsToStudent();
            break;
        
        case 'bulk_upload_points':
            bulkUploadPoints();
            break;
        
        case 'bulk_upload_students':
            bulkUploadStudents();
            break;
        
        case 'get_all_students':
            getAllStudents();
            break;
        
        case 'export_students':
            exportStudents();
            break;
        
        case 'get_house_performance':
            getHousePerformance();
            break;
        
        case 'get_branch_distribution':
            getBranchDistribution();
            break;
        
        case 'get_monthly_activity':
            getMonthlyActivity();
            break;
        
        case 'get_events':
            getEvents();
            break;
        
        case 'create_event':
            createEvent();
            break;
        
        case 'get_event_details':
            getEventDetails();
            break;
        
        case 'get_event_participants':
            getEventParticipants();
            break;
        
        case 'add_participant':
            addParticipant();
            break;
        
        case 'update_participant_points':
            updateParticipantPoints();
            break;
        
        case 'remove_participant':
            removeParticipant();
            break;
        
        case 'bulk_upload_participants':
            bulkUploadParticipants();
            break;
        
        case 'bulk_award_participant_points':
            bulkAwardParticipantPoints();
            break;
        
        case 'clear_all_participants':
            clearAllParticipants();
            break;
        
        case 'export_event_participants':
            exportEventParticipants();
            break;
        
        case 'award_appreciation':
            awardAppreciation();
            break;
        
        case 'get_appreciations':
            getAppreciations();
            break;
        
        case 'update_appreciation':
            updateAppreciation();
            break;
        
        case 'delete_appreciation':
            deleteAppreciation();
            break;
        
        case 'bulk_award_appreciations':
            bulkAwardAppreciations();
            break;
        
        case 'export_appreciations':
            exportAppreciations();
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

// Dashboard Statistics
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total classes
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM classes");
    $stats['total_classes'] = mysqli_fetch_assoc($result)['count'];
    
    // Total students
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
    $stats['total_students'] = mysqli_fetch_assoc($result)['count'];
    
    // Total houses
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM houses");
    $stats['total_houses'] = mysqli_fetch_assoc($result)['count'];
    
    // Total events
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM events");
    $stats['total_events'] = mysqli_fetch_assoc($result)['count'];
    
    echo json_encode($stats);
}

// Classes Management
function getClasses() {
    global $conn;
    
    $query = "SELECT c.*, COUNT(s.student_id) as student_count 
              FROM classes c 
              LEFT JOIN students s ON c.class_id = s.class_id 
              GROUP BY c.class_id 
              ORDER BY c.academic_year DESC, c.branch, c.section";
    
    $result = mysqli_query($conn, $query);
    $classes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $classes[] = $row;
    }
    
    echo json_encode($classes);
}

function addClass() {
    global $conn;
    
    $academic_year = mysqli_real_escape_string($conn, $_POST['academicYear']);
    $year = (int)$_POST['year'];
    $semester = (int)$_POST['semester'];
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    
    // Check if class already exists
    $check_query = "SELECT class_id FROM classes WHERE academic_year = '$academic_year' AND year = $year AND semester = $semester AND branch = '$branch' AND section = '$section'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Class already exists']);
        return;
    }
    
    $query = "INSERT INTO classes (academic_year, year, semester, branch, section) VALUES ('$academic_year', $year, $semester, '$branch', '$section')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Class added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding class: ' . mysqli_error($conn)]);
    }
}

function deleteClass() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = (int)$input['class_id'];
    
    // Check if class has students
    $check_query = "SELECT COUNT(*) as count FROM students WHERE class_id = $class_id";
    $check_result = mysqli_query($conn, $check_query);
    $count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete class with existing students']);
        return;
    }
    
    $query = "DELETE FROM classes WHERE class_id = $class_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Class deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting class: ' . mysqli_error($conn)]);
    }
}

// Students Management
function getStudents() {
    global $conn;
    
    $query = "SELECT s.*, h.name as house_name,
              COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
              FROM students s
              LEFT JOIN houses h ON s.hid = h.hid
              LEFT JOIN appreciations a ON s.student_id = a.student_id
              LEFT JOIN organizers o ON s.student_id = o.student_id
              LEFT JOIN participants p ON s.student_id = p.student_id
              GROUP BY s.student_id
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode($students);
}

function addStudent() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_POST['studentId']);
    $name = mysqli_real_escape_string($conn, $_POST['studentName']);
    $email = mysqli_real_escape_string($conn, $_POST['studentEmail']);
    $password = password_hash($_POST['studentPassword'], PASSWORD_DEFAULT);
    $branch = mysqli_real_escape_string($conn, $_POST['studentBranch']);
    $section = mysqli_real_escape_string($conn, $_POST['studentSection']);
    $class_id = (int)$_POST['studentClass'];
    $house_id = (int)$_POST['studentHouse'];
    
    // Check if student already exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
        return;
    }
    
    $query = "INSERT INTO students (student_id, name, email, password, branch, section, class_id, hid) 
              VALUES ('$student_id', '$name', '$email', '$password', '$branch', '$section', $class_id, $house_id)";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding student: ' . mysqli_error($conn)]);
    }
}

function getStudent() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'student' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
}

function updateStudent() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_POST['editStudentId']);
    $name = mysqli_real_escape_string($conn, $_POST['editStudentName']);
    $email = mysqli_real_escape_string($conn, $_POST['editStudentEmail']);
    $branch = mysqli_real_escape_string($conn, $_POST['editStudentBranch']);
    $section = mysqli_real_escape_string($conn, $_POST['editStudentSection']);
    $class_id = (int)$_POST['editStudentClass'];
    $house_id = (int)$_POST['editStudentHouse'];
    $reset_password = $_POST['reset_password'] === 'true';
    
    $query = "UPDATE students SET name = '$name', email = '$email', branch = '$branch', section = '$section', class_id = $class_id, hid = $house_id";
    
    if ($reset_password) {
        $password = password_hash($student_id, PASSWORD_DEFAULT);
        $query .= ", password = '$password'";
    }
    
    $query .= " WHERE student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating student: ' . mysqli_error($conn)]);
    }
}

function deleteStudent() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = mysqli_real_escape_string($conn, $input['student_id']);
    
    // Delete related records first
    mysqli_query($conn, "DELETE FROM appreciations WHERE student_id = '$student_id'");
    mysqli_query($conn, "DELETE FROM organizers WHERE student_id = '$student_id'");
    mysqli_query($conn, "DELETE FROM participants WHERE student_id = '$student_id'");
    
    // Delete student
    $query = "DELETE FROM students WHERE student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting student: ' . mysqli_error($conn)]);
    }
}

function getHouses() {
    global $conn;
    
    $query = "SELECT * FROM houses ORDER BY name";
    $result = mysqli_query($conn, $query);
    $houses = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $houses[] = $row;
    }
    
    echo json_encode($houses);
}

function searchStudents() {
    global $conn;
    
    $query = mysqli_real_escape_string($conn, $_GET['query']);
    
    $search_query = "SELECT s.student_id, s.name, s.branch, s.section, h.name as house_name,
                     COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
                     FROM students s
                     LEFT JOIN houses h ON s.hid = h.hid
                     LEFT JOIN appreciations a ON s.student_id = a.student_id
                     LEFT JOIN organizers o ON s.student_id = o.student_id
                     LEFT JOIN participants p ON s.student_id = p.student_id
                     WHERE s.student_id LIKE '%$query%' OR s.name LIKE '%$query%'
                     GROUP BY s.student_id
                     ORDER BY s.name
                     LIMIT 10";
    
    $result = mysqli_query($conn, $search_query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode($students);
}

function addPointsToStudent() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_POST['pointsStudentId']);
    $points = (float)$_POST['pointsValue'];
    $reason = mysqli_real_escape_string($conn, $_POST['pointsReason']);
    $date = mysqli_real_escape_string($conn, $_POST['pointsDate']);
    
    // Check if student exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }
    
    // Get or create a default "Manual Points" event for admin-awarded points
    $default_event_query = "SELECT event_id FROM events WHERE title = 'Manual Points Award' LIMIT 1";
    $default_event_result = mysqli_query($conn, $default_event_query);
    
    if (mysqli_num_rows($default_event_result) == 0) {
        // Create default event for manual points
        $create_event_query = "INSERT INTO events (title, description, date) VALUES 
                              ('Manual Points Award', 'Default event for manually awarded points by admin', CURDATE())";
        if (mysqli_query($conn, $create_event_query)) {
            $event_id = mysqli_insert_id($conn);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating default event: ' . mysqli_error($conn)]);
            return;
        }
    } else {
        $event_id = mysqli_fetch_assoc($default_event_result)['event_id'];
    }
    
    // Add to appreciations table with event_id
    $query = "INSERT INTO appreciations (student_id, event_id, points, reason, created_by, created_at) 
              VALUES ('$student_id', $event_id, $points, '$reason', " . ($_SESSION['superadmin_id'] ?? 'NULL') . ", '$date')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Points added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding points: ' . mysqli_error($conn)]);
    }
}

function bulkUploadPoints() {
    global $conn;
    
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $reason = mysqli_real_escape_string($conn, $_POST['pointsReason']);
    $file = $_FILES['excelFile']['tmp_name'];
    $extension = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'csv') {
            // Handle CSV files
            $rows = [];
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else if (in_array($extension, ['xlsx', 'xls'])) {
            // Check if PhpSpreadsheet is available
            if (file_exists('../vendor/autoload.php')) {
                require_once '../vendor/autoload.php';
                if ($extension === 'csv') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                } else {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                }
                $spreadsheet = $reader->load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            } else {
                echo json_encode(['success' => false, 'message' => 'Excel files not supported. Please use CSV format instead.']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Unsupported file format. Please use CSV format.']);
            return;
        }
        
        $processed = 0;
        $successful = 0;
        $errors = [];
        
        // Skip header row
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            if (empty($row[0]) || empty($row[1])) continue;
            
            $student_id = mysqli_real_escape_string($conn, trim($row[0]));
            $points = (float)$row[1];
            $processed++;
            
            // Check if student exists
            $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) == 0) {
                $errors[] = "Student ID $student_id not found";
                continue;
            }
            
            // Add points
            $date = date('Y-m-d');
            $insert_query = "INSERT INTO appreciations (student_id, points, reason, created_at) VALUES ('$student_id', $points, '$reason', '$date')";
            
            if (mysqli_query($conn, $insert_query)) {
                $successful++;
            } else {
                $errors[] = "Error adding points to $student_id: " . mysqli_error($conn);
            }
        }
        
        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'successful' => $successful,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
    }
}

function bulkUploadStudents() {
    global $conn;
    
    if (!isset($_FILES['studentExcelFile']) || $_FILES['studentExcelFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $default_class = !empty($_POST['defaultClass']) ? (int)$_POST['defaultClass'] : null;
    $default_house = !empty($_POST['defaultHouse']) ? (int)$_POST['defaultHouse'] : null;
    $skip_duplicates = $_POST['skip_duplicates'] === 'true';
    
    $file = $_FILES['studentExcelFile']['tmp_name'];
    $extension = strtolower(pathinfo($_FILES['studentExcelFile']['name'], PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'csv') {
            // Handle CSV files
            $rows = [];
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else if (in_array($extension, ['xlsx', 'xls'])) {
            // Check if PhpSpreadsheet is available
            if (file_exists('../vendor/autoload.php')) {
                require_once '../vendor/autoload.php';
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            } else {
                echo json_encode(['success' => false, 'message' => 'Excel files not supported. Please use CSV format instead.']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Unsupported file format. Please use CSV format.']);
            return;
        }
        
        $processed = 0;
        $successful = 0;
        $skipped = 0;
        $errors = [];
        
        // Skip header row
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            if (empty($row[0]) || empty($row[1])) continue;
            
            $student_id = mysqli_real_escape_string($conn, trim($row[0]));
            $name = mysqli_real_escape_string($conn, trim($row[1]));
            $email = mysqli_real_escape_string($conn, trim($row[2] ?? ''));
            $branch = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
            $section = mysqli_real_escape_string($conn, trim($row[4] ?? ''));
            $class_id = !empty($row[5]) ? (int)$row[5] : $default_class;
            $house_id = !empty($row[6]) ? (int)$row[6] : $default_house;
            $password = password_hash($row[7] ?? $student_id, PASSWORD_DEFAULT);
            
            $processed++;
            
            // Check for required fields
            if (empty($name) || empty($email) || empty($branch) || empty($section)) {
                $errors[] = "Missing required fields for student ID $student_id";
                continue;
            }
            
            // Check if student exists
            $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                if ($skip_duplicates) {
                    $skipped++;
                    continue;
                } else {
                    $errors[] = "Student ID $student_id already exists";
                    continue;
                }
            }
            
            // Insert student
            $insert_query = "INSERT INTO students (student_id, name, email, password, branch, section, class_id, hid) 
                            VALUES ('$student_id', '$name', '$email', '$password', '$branch', '$section', " . 
                            ($class_id ? $class_id : 'NULL') . ", " . ($house_id ? $house_id : 'NULL') . ")";
            
            if (mysqli_query($conn, $insert_query)) {
                $successful++;
            } else {
                $errors[] = "Error adding student $student_id: " . mysqli_error($conn);
            }
        }
        
        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'successful' => $successful,
            'skipped' => $skipped,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
    }
}

function getAllStudents() {
    global $conn;
    
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $class_filter = $_GET['class_filter'] ?? '';
    $house_filter = $_GET['house_filter'] ?? '';
    $branch_filter = mysqli_real_escape_string($conn, $_GET['branch_filter'] ?? '');
    $sort_by = mysqli_real_escape_string($conn, $_GET['sort_by'] ?? 'name');
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $where_conditions = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.email LIKE '%$search%')";
    }
    
    if (!empty($class_filter)) {
        $class_filter = (int)$class_filter;
        $where_conditions[] = "s.class_id = $class_filter";
    }
    
    if (!empty($house_filter)) {
        $house_filter = (int)$house_filter;
        $where_conditions[] = "s.hid = $house_filter";
    }
    
    if (!empty($branch_filter)) {
        $where_conditions[] = "s.branch = '$branch_filter'";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total FROM students s $where_clause";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_records / $limit);
    
    // Get students data
    $order_clause = "ORDER BY ";
    switch ($sort_by) {
        case 'points':
            $order_clause .= "(COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0)) DESC";
            break;
        case 'student_id':
            $order_clause .= "s.student_id";
            break;
        default:
            $order_clause .= "s.name";
    }
    
    $query = "SELECT s.*, h.name as house_name, c.academic_year, c.branch as class_branch, c.section as class_section,
              CONCAT(c.academic_year, ' - ', c.branch, ' ', c.section) as class_info,
              COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
              FROM students s
              LEFT JOIN houses h ON s.hid = h.hid
              LEFT JOIN classes c ON s.class_id = c.class_id
              LEFT JOIN appreciations a ON s.student_id = a.student_id
              LEFT JOIN organizers o ON s.student_id = o.student_id
              LEFT JOIN participants p ON s.student_id = p.student_id
              $where_clause
              GROUP BY s.student_id
              $order_clause
              LIMIT $limit OFFSET $offset";
    
    $result = mysqli_query($conn, $query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode([
        'students' => $students,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_records' => $total_records
    ]);
}

function exportStudents() {
    global $conn;
    
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $class_filter = $_GET['class_filter'] ?? '';
    $house_filter = $_GET['house_filter'] ?? '';
    $branch_filter = mysqli_real_escape_string($conn, $_GET['branch_filter'] ?? '');
    
    // Build WHERE clause (same as getAllStudents)
    $where_conditions = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.email LIKE '%$search%')";
    }
    
    if (!empty($class_filter)) {
        $class_filter = (int)$class_filter;
        $where_conditions[] = "s.class_id = $class_filter";
    }
    
    if (!empty($house_filter)) {
        $house_filter = (int)$house_filter;
        $where_conditions[] = "s.hid = $house_filter";
    }
    
    if (!empty($branch_filter)) {
        $where_conditions[] = "s.branch = '$branch_filter'";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $query = "SELECT s.student_id, s.name, s.email, s.branch, s.section, h.name as house_name,
              CONCAT(c.academic_year, ' - ', c.branch, ' ', c.section) as class_info,
              COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
              FROM students s
              LEFT JOIN houses h ON s.hid = h.hid
              LEFT JOIN classes c ON s.class_id = c.class_id
              LEFT JOIN appreciations a ON s.student_id = a.student_id
              LEFT JOIN organizers o ON s.student_id = o.student_id
              LEFT JOIN participants p ON s.student_id = p.student_id
              $where_clause
              GROUP BY s.student_id
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d') . '.xls"');
    
    echo "Student ID\tName\tEmail\tBranch\tSection\tClass\tHouse\tTotal Points\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['student_id']}\t{$row['name']}\t{$row['email']}\t{$row['branch']}\t{$row['section']}\t{$row['class_info']}\t{$row['house_name']}\t{$row['total_points']}\n";
    }
    exit;
}

// Reports and Analytics
function getHousePerformance() {
    global $conn;
    
    $query = "SELECT h.name as house_name,
              COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
              FROM houses h
              LEFT JOIN students s ON h.hid = s.hid
              LEFT JOIN appreciations a ON s.student_id = a.student_id
              LEFT JOIN organizers o ON s.student_id = o.student_id
              LEFT JOIN participants p ON s.student_id = p.student_id
              GROUP BY h.hid
              ORDER BY total_points DESC";
    
    $result = mysqli_query($conn, $query);
    
    $labels = [];
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['house_name'];
        $data[] = (float)$row['total_points'];
    }
    
    echo json_encode(['labels' => $labels, 'data' => $data]);
}

function getBranchDistribution() {
    global $conn;
    
    $query = "SELECT branch, COUNT(*) as count FROM students GROUP BY branch";
    $result = mysqli_query($conn, $query);
    
    $labels = [];
    $data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['branch'];
        $data[] = (int)$row['count'];
    }
    
    echo json_encode(['labels' => $labels, 'data' => $data]);
}

function getMonthlyActivity() {
    global $conn;
    
    // Get last 12 months of data
    $months = [];
    $events_data = [];
    $appreciations_data = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));
        $months[] = $month_name;
        
        // Count events
        $events_query = "SELECT COUNT(*) as count FROM events WHERE DATE_FORMAT(date, '%Y-%m') = '$month'";
        $events_result = mysqli_query($conn, $events_query);
        $events_count = mysqli_fetch_assoc($events_result)['count'];
        $events_data[] = (int)$events_count;
        
        // Count appreciations
        $appreciations_query = "SELECT COUNT(*) as count FROM appreciations WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'";
        $appreciations_result = mysqli_query($conn, $appreciations_query);
        $appreciations_count = mysqli_fetch_assoc($appreciations_result)['count'];
        $appreciations_data[] = (int)$appreciations_count;
    }
    
    echo json_encode([
        'labels' => $months,
        'events' => $events_data,
        'appreciations' => $appreciations_data
    ]);
}

// Event Management
function getEvents() {
    global $conn;
    
    $query = "SELECT * FROM events ORDER BY date DESC";
    $result = mysqli_query($conn, $query);
    $events = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
    
    echo json_encode($events);
}

function createEvent() {
    global $conn;
    
    $title = mysqli_real_escape_string($conn, $_POST['eventTitle']);
    $description = mysqli_real_escape_string($conn, $_POST['eventDescription']);
    $date = mysqli_real_escape_string($conn, $_POST['eventDate']);
    
    $query = "INSERT INTO events (title, description, date) VALUES ('$title', '$description', '$date')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Event created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating event: ' . mysqli_error($conn)]);
    }
}

function getEventDetails() {
    global $conn;
    
    $event_id = (int)$_GET['id'];
    
    $query = "SELECT * FROM events WHERE eid = $event_id";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'event' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
    }
}

function getEventParticipants() {
    global $conn;
    
    $event_id = (int)$_GET['event_id'];
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    
    $where_clause = "WHERE p.eid = $event_id";
    if (!empty($search)) {
        $where_clause .= " AND (s.student_id LIKE '%$search%' OR s.name LIKE '%$search%')";
    }
    
    $query = "SELECT p.*, s.name, s.branch, s.section, h.name as house_name
              FROM participants p
              JOIN students s ON p.student_id = s.student_id
              LEFT JOIN houses h ON s.hid = h.hid
              $where_clause
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    $participants = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $participants[] = $row;
    }
    
    echo json_encode(['participants' => $participants]);
}

function addParticipant() {
    global $conn;
    
    $event_id = (int)$_POST['participantEventId'];
    $student_id = mysqli_real_escape_string($conn, $_POST['participantStudentId']);
    $points = (float)($_POST['participantPoints'] ?? 0);
    
    // Check if student exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }
    
    // Check if already participant
    $participant_check = "SELECT * FROM participants WHERE eid = $event_id AND student_id = '$student_id'";
    $participant_result = mysqli_query($conn, $participant_check);
    
    if (mysqli_num_rows($participant_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student is already a participant in this event']);
        return;
    }
    
    $query = "INSERT INTO participants (eid, student_id, points) VALUES ($event_id, '$student_id', $points)";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Participant added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding participant: ' . mysqli_error($conn)]);
    }
}

function updateParticipantPoints() {
    global $conn;
    
    $event_id = (int)$_POST['event_id'];
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $points = (float)$_POST['points'];
    
    $query = "UPDATE participants SET points = $points WHERE eid = $event_id AND student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Points updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating points: ' . mysqli_error($conn)]);
    }
}

function removeParticipant() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = (int)$input['event_id'];
    $student_id = mysqli_real_escape_string($conn, $input['student_id']);
    
    $query = "DELETE FROM participants WHERE eid = $event_id AND student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Participant removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing participant: ' . mysqli_error($conn)]);
    }
}

function bulkUploadParticipants() {
    global $conn;
    
    if (!isset($_FILES['participantsFile']) || $_FILES['participantsFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $event_id = (int)$_POST['bulkParticipantEventId'];
    $default_points = (float)($_POST['defaultParticipantPoints'] ?? 0);
    
    $file = $_FILES['participantsFile']['tmp_name'];
    $extension = strtolower(pathinfo($_FILES['participantsFile']['name'], PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'csv') {
            $rows = array_map('str_getcsv', file($file));
        } else {
            require_once '../vendor/autoload.php';
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        }
        
        $processed = 0;
        $successful = 0;
        $skipped = 0;
        $errors = [];
        
        // Skip header row
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            if (empty($row[0])) continue;
            
            $student_id = mysqli_real_escape_string($conn, trim($row[0]));
            $points = !empty($row[1]) ? (float)$row[1] : $default_points;
            
            $processed++;
            
            // Check if student exists
            $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) == 0) {
                $errors[] = "Student ID $student_id not found";
                continue;
            }
            
            // Check if already participant
            $participant_check = "SELECT * FROM participants WHERE eid = $event_id AND student_id = '$student_id'";
            $participant_result = mysqli_query($conn, $participant_check);
            
            if (mysqli_num_rows($participant_result) > 0) {
                $skipped++;
                continue;
            }
            
            // Add participant
            $insert_query = "INSERT INTO participants (eid, student_id, points) VALUES ($event_id, '$student_id', $points)";
            
            if (mysqli_query($conn, $insert_query)) {
                $successful++;
            } else {
                $errors[] = "Error adding participant $student_id: " . mysqli_error($conn);
            }
        }
        
        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'successful' => $successful,
            'skipped' => $skipped,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
    }
}

function bulkAwardParticipantPoints() {
    global $conn;
    
    $event_id = (int)$_POST['bulkPointsEventId'];
    $points = (float)$_POST['bulkPointsValue'];
    $overwrite = $_POST['overwrite'] === 'true';
    
    if ($overwrite) {
        $query = "UPDATE participants SET points = $points WHERE eid = $event_id";
    } else {
        $query = "UPDATE participants SET points = points + $points WHERE eid = $event_id";
    }
    
    if (mysqli_query($conn, $query)) {
        $affected_rows = mysqli_affected_rows($conn);
        echo json_encode(['success' => true, 'updated' => $affected_rows, 'message' => 'Points awarded successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error awarding points: ' . mysqli_error($conn)]);
    }
}

function clearAllParticipants() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = (int)$input['event_id'];
    
    $query = "DELETE FROM participants WHERE eid = $event_id";
    
    if (mysqli_query($conn, $query)) {
        $affected_rows = mysqli_affected_rows($conn);
        echo json_encode(['success' => true, 'removed' => $affected_rows, 'message' => 'All participants removed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing participants: ' . mysqli_error($conn)]);
    }
}

function exportEventParticipants() {
    global $conn;
    
    $event_id = (int)$_GET['event_id'];
    
    // Get event details
    $event_query = "SELECT title FROM events WHERE eid = $event_id";
    $event_result = mysqli_query($conn, $event_query);
    $event_title = mysqli_fetch_assoc($event_result)['title'] ?? 'Unknown Event';
    
    $query = "SELECT p.student_id, s.name, s.branch, s.section, h.name as house_name, p.points
              FROM participants p
              JOIN students s ON p.student_id = s.student_id
              LEFT JOIN houses h ON s.hid = h.hid
              WHERE p.eid = $event_id
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $event_title) . '_participants_' . date('Y-m-d') . '.xls"');
    
    echo "Student ID\tName\tBranch\tSection\tHouse\tPoints\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['student_id']}\t{$row['name']}\t{$row['branch']}\t{$row['section']}\t{$row['house_name']}\t{$row['points']}\n";
    }
    exit;
}

// Appreciations Management
function awardAppreciation() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_POST['appreciationStudentId']);
    $event_id = !empty($_POST['appreciationEventId']) ? (int)$_POST['appreciationEventId'] : null;
    $points = (float)$_POST['appreciationPoints'];
    $reason = mysqli_real_escape_string($conn, $_POST['appreciationReason']);
    $date = date('Y-m-d');
    
    // Check if student exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }
    
    // If no event specified, get or create default event
    if (!$event_id) {
        $default_event_query = "SELECT event_id FROM events WHERE title = 'Manual Points Award' LIMIT 1";
        $default_event_result = mysqli_query($conn, $default_event_query);
        
        if (mysqli_num_rows($default_event_result) == 0) {
            // Create default event for manual points
            $create_event_query = "INSERT INTO events (title, description, date) VALUES 
                                  ('Manual Points Award', 'Default event for manually awarded points by admin', CURDATE())";
            if (mysqli_query($conn, $create_event_query)) {
                $event_id = mysqli_insert_id($conn);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error creating default event: ' . mysqli_error($conn)]);
                return;
            }
        } else {
            $event_id = mysqli_fetch_assoc($default_event_result)['event_id'];
        }
    }
    
    // Add to appreciations table
    $query = "INSERT INTO appreciations (student_id, event_id, points, reason, created_by, created_at) 
              VALUES ('$student_id', $event_id, $points, '$reason', " . ($_SESSION['superadmin_id'] ?? 'NULL') . ", '$date')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Appreciation awarded successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error awarding appreciation: ' . mysqli_error($conn)]);
    }
}

function getAppreciations() {
    global $conn;
    
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $event_filter = $_GET['event_filter'] ?? '';
    $date_filter = $_GET['date_filter'] ?? '';
    $points_filter = $_GET['points_filter'] ?? '';
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $where_conditions = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%')";
    }
    
    if (!empty($event_filter)) {
        $event_filter = (int)$event_filter;
        $where_conditions[] = "a.eid = $event_filter";
    }
    
    if (!empty($date_filter)) {
        $today = date('Y-m-d');
        switch ($date_filter) {
            case 'today':
                $where_conditions[] = "DATE(a.created_at) = '$today'";
                break;
            case 'week':
                $week_ago = date('Y-m-d', strtotime('-7 days'));
                $where_conditions[] = "DATE(a.created_at) >= '$week_ago'";
                break;
            case 'month':
                $month_ago = date('Y-m-d', strtotime('-30 days'));
                $where_conditions[] = "DATE(a.created_at) >= '$month_ago'";
                break;
        }
    }
    
    if (!empty($points_filter)) {
        switch ($points_filter) {
            case 'low':
                $where_conditions[] = "a.points <= 5";
                break;
            case 'medium':
                $where_conditions[] = "a.points > 5 AND a.points <= 15";
                break;
            case 'high':
                $where_conditions[] = "a.points > 15";
                break;
        }
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total 
                    FROM appreciations a 
                    JOIN students s ON a.student_id = s.student_id 
                    LEFT JOIN events e ON a.eid = e.eid 
                    $where_clause";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_records / $limit);
    
    // Get appreciations data
    $query = "SELECT a.*, s.name as student_name, e.title as event_title, 
              CASE WHEN a.created_by IS NOT NULL THEN 'Super Admin' ELSE 'System' END as awarded_by
              FROM appreciations a
              JOIN students s ON a.student_id = s.student_id
              LEFT JOIN events e ON a.eid = e.eid
              $where_clause
              ORDER BY a.created_at DESC
              LIMIT $limit OFFSET $offset";
    
    $result = mysqli_query($conn, $query);
    $appreciations = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $appreciations[] = $row;
    }
    
    echo json_encode([
        'appreciations' => $appreciations,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_records' => $total_records
    ]);
}

function updateAppreciation() {
    global $conn;
    
    $appreciation_id = (int)$_POST['appreciation_id'];
    $points = (float)$_POST['points'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    $query = "UPDATE appreciations SET points = $points, reason = '$reason' WHERE id = $appreciation_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Appreciation updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating appreciation: ' . mysqli_error($conn)]);
    }
}

function deleteAppreciation() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $appreciation_id = (int)$input['appreciation_id'];
    
    $query = "DELETE FROM appreciations WHERE id = $appreciation_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Appreciation deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting appreciation: ' . mysqli_error($conn)]);
    }
}

function bulkAwardAppreciations() {
    global $conn;
    
    if (!isset($_FILES['bulkAppreciationFile']) || $_FILES['bulkAppreciationFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $event_id = !empty($_POST['bulkAppreciationEventId']) ? (int)$_POST['bulkAppreciationEventId'] : null;
    $default_points = (float)($_POST['bulkAppreciationPoints'] ?? 5);
    $reason = mysqli_real_escape_string($conn, $_POST['bulkAppreciationReason'] ?? 'Bulk appreciation award');
    
    // If no event specified, get or create default event
    if (!$event_id) {
        $default_event_query = "SELECT event_id FROM events WHERE title = 'Manual Points Award' LIMIT 1";
        $default_event_result = mysqli_query($conn, $default_event_query);
        
        if (mysqli_num_rows($default_event_result) == 0) {
            // Create default event for manual points
            $create_event_query = "INSERT INTO events (title, description, date) VALUES 
                                  ('Manual Points Award', 'Default event for manually awarded points by admin', CURDATE())";
            if (mysqli_query($conn, $create_event_query)) {
                $event_id = mysqli_insert_id($conn);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error creating default event: ' . mysqli_error($conn)]);
                return;
            }
        } else {
            $event_id = mysqli_fetch_assoc($default_event_result)['event_id'];
        }
    }
    
    $file = $_FILES['bulkAppreciationFile']['tmp_name'];
    $extension = strtolower(pathinfo($_FILES['bulkAppreciationFile']['name'], PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'csv') {
            $rows = array_map('str_getcsv', file($file));
        } else {
            if (file_exists('../vendor/autoload.php')) {
                require_once '../vendor/autoload.php';
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            } else {
                echo json_encode(['success' => false, 'message' => 'Excel files not supported. Please use CSV format.']);
                return;
            }
        }
        
        $processed = 0;
        $successful = 0;
        $errors = [];
        
        // Skip header row
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            if (empty($row[0])) continue;
            
            $student_id = mysqli_real_escape_string($conn, trim($row[0]));
            $points = !empty($row[1]) ? (float)$row[1] : $default_points;
            $custom_reason = !empty($row[2]) ? mysqli_real_escape_string($conn, trim($row[2])) : $reason;
            
            $processed++;
            
            // Check if student exists
            $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) == 0) {
                $errors[] = "Student ID $student_id not found";
                continue;
            }
            
            // Add appreciation
            $date = date('Y-m-d');
            $insert_query = "INSERT INTO appreciations (student_id, event_id, points, reason, created_by, created_at) 
                             VALUES ('$student_id', $event_id, $points, '$custom_reason', " . ($_SESSION['superadmin_id'] ?? 'NULL') . ", '$date')";
            
            if (mysqli_query($conn, $insert_query)) {
                $successful++;
            } else {
                $errors[] = "Error awarding appreciation to $student_id: " . mysqli_error($conn);
            }
        }
        
        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'successful' => $successful,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
    }
}

function exportAppreciations() {
    global $conn;
    
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $event_filter = $_GET['event_filter'] ?? '';
    $date_filter = $_GET['date_filter'] ?? '';
    $points_filter = $_GET['points_filter'] ?? '';
    
    // Build WHERE clause (same as getAppreciations)
    $where_conditions = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%')";
    }
    
    if (!empty($event_filter)) {
        $event_filter = (int)$event_filter;
        $where_conditions[] = "a.eid = $event_filter";
    }
    
    if (!empty($date_filter)) {
        $today = date('Y-m-d');
        switch ($date_filter) {
            case 'today':
                $where_conditions[] = "DATE(a.created_at) = '$today'";
                break;
            case 'week':
                $week_ago = date('Y-m-d', strtotime('-7 days'));
                $where_conditions[] = "DATE(a.created_at) >= '$week_ago'";
                break;
            case 'month':
                $month_ago = date('Y-m-d', strtotime('-30 days'));
                $where_conditions[] = "DATE(a.created_at) >= '$month_ago'";
                break;
        }
    }
    
    if (!empty($points_filter)) {
        switch ($points_filter) {
            case 'low':
                $where_conditions[] = "a.points <= 5";
                break;
            case 'medium':
                $where_conditions[] = "a.points > 5 AND a.points <= 15";
                break;
            case 'high':
                $where_conditions[] = "a.points > 15";
                break;
        }
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $query = "SELECT a.id, a.student_id, s.name as student_name, e.title as event_title, a.points, a.reason, 
              CASE WHEN a.created_by IS NOT NULL THEN 'Super Admin' ELSE 'System' END as awarded_by, a.created_at
              FROM appreciations a
              JOIN students s ON a.student_id = s.student_id
              LEFT JOIN events e ON a.eid = e.eid
              $where_clause
              ORDER BY a.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="appreciations_export_' . date('Y-m-d') . '.xls"');
    
    echo "ID\tStudent ID\tStudent Name\tEvent\tPoints\tReason\tAwarded By\tDate\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['id']}\t{$row['student_id']}\t{$row['student_name']}\t{$row['event_title']}\t{$row['points']}\t{$row['reason']}\t{$row['awarded_by']}\t{$row['created_at']}\n";
    }
    exit;
}

?>