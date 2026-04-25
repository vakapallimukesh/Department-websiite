<?php
session_start();
include "../utils/connect.php";

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get the action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
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
    case 'bulk_upload_points':
        bulkUploadPoints();
        break;
    case 'bulk_upload_students':
        bulkUploadStudents();
        break;
    case 'add_points_to_student':
        addPointsToStudent();
        break;
    case 'search_students':
        searchStudents();
        break;
    case 'get_all_students':
        getAllStudents();
        break;
    case 'export_students':
        exportStudents();
        break;
    case 'get_dashboard_stats':
        getDashboardStats();
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
    case 'bulk_award_participant_points':
        bulkAwardParticipantPoints();
        break;
    case 'clear_all_participants':
        clearAllParticipants();
        break;
    case 'bulk_upload_participants':
        bulkUploadParticipants();
        break;
    case 'export_event_participants':
        exportEventParticipants();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function getClasses() {
    global $conn;
    
    $query = "SELECT c.*, COUNT(s.student_id) as student_count 
              FROM classes c 
              LEFT JOIN students s ON c.class_id = s.class_id 
              GROUP BY c.class_id 
              ORDER BY c.academic_year DESC, c.year, c.semester, c.branch, c.section";
    
    $result = mysqli_query($conn, $query);
    $classes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $classes[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($classes);
}

function addClass() {
    global $conn;
    
    $academic_year = mysqli_real_escape_string($conn, $_POST['academicYear']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $section = strtoupper(mysqli_real_escape_string($conn, $_POST['section']));
    
    // Check if class already exists
    $check_query = "SELECT class_id FROM classes 
                    WHERE academic_year = '$academic_year' 
                    AND year = '$year' 
                    AND semester = '$semester' 
                    AND branch = '$branch' 
                    AND section = '$section'";
    
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Class already exists']);
        return;
    }
    
    $query = "INSERT INTO classes (academic_year, year, semester, branch, section) 
              VALUES ('$academic_year', '$year', '$semester', '$branch', '$section')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Class added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add class: ' . mysqli_error($conn)]);
    }
}

function deleteClass() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $class_id = mysqli_real_escape_string($conn, $input['class_id']);
    
    // Check if there are students in this class
    $check_query = "SELECT COUNT(*) as count FROM students WHERE class_id = '$class_id'";
    $check_result = mysqli_query($conn, $check_query);
    $count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete class with existing students']);
        return;
    }
    
    $query = "DELETE FROM classes WHERE class_id = '$class_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Class deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete class: ' . mysqli_error($conn)]);
    }
}

function getStudents() {
    global $conn;
    
    $search = $_GET['search'] ?? '';
    $class_filter = $_GET['class_filter'] ?? '';
    $house_filter = $_GET['house_filter'] ?? '';
    
    $where_conditions = [];
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.email LIKE '%$search%')";
    }
    
    if (!empty($class_filter)) {
        $class_filter = mysqli_real_escape_string($conn, $class_filter);
        $where_conditions[] = "s.class_id = '$class_filter'";
    }
    
    if (!empty($house_filter)) {
        $house_filter = mysqli_real_escape_string($conn, $house_filter);
        $where_conditions[] = "s.hid = '$house_filter'";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $query = "SELECT s.*, h.name as house_name,
              COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
              FROM students s
              LEFT JOIN houses h ON s.hid = h.hid
              LEFT JOIN appreciations a ON s.student_id = a.student_id
              LEFT JOIN organizers o ON s.student_id = o.student_id
              LEFT JOIN participants p ON s.student_id = p.student_id
              $where_clause
              GROUP BY s.student_id
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($students);
}

function addStudent() {
    global $conn;
    
    $student_id = mysqli_real_escape_string($conn, $_POST['studentId']);
    $name = mysqli_real_escape_string($conn, $_POST['studentName']);
    $email = mysqli_real_escape_string($conn, $_POST['studentEmail']);
    $password = mysqli_real_escape_string($conn, $_POST['studentPassword']);
    $branch = mysqli_real_escape_string($conn, $_POST['studentBranch']);
    $section = mysqli_real_escape_string($conn, $_POST['studentSection']);
    $class_id = mysqli_real_escape_string($conn, $_POST['studentClass']);
    $hid = mysqli_real_escape_string($conn, $_POST['studentHouse']);
    
    // Check if student ID already exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
        return;
    }
    
    // Check if email already exists
    $email_check = "SELECT student_id FROM students WHERE email = '$email'";
    $email_result = mysqli_query($conn, $email_check);
    
    if (mysqli_num_rows($email_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    
    $query = "INSERT INTO students (student_id, name, email, password, branch, section, class_id, hid) 
              VALUES ('$student_id', '$name', '$email', '$password', '$branch', '$section', '$class_id', '$hid')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add student: ' . mysqli_error($conn)]);
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
    $class_id = mysqli_real_escape_string($conn, $_POST['editStudentClass']);
    $hid = mysqli_real_escape_string($conn, $_POST['editStudentHouse']);
    $reset_password = $_POST['reset_password'] === 'true';
    
    $query = "UPDATE students SET 
              name = '$name', 
              email = '$email', 
              branch = '$branch', 
              section = '$section', 
              class_id = '$class_id', 
              hid = '$hid'";
    
    if ($reset_password) {
        $query .= ", password = '$student_id'";
    }
    
    $query .= " WHERE student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update student: ' . mysqli_error($conn)]);
    }
}

function deleteStudent() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = mysqli_real_escape_string($conn, $input['student_id']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete related records first
        $tables = ['appreciations', 'organizers', 'participants'];
        foreach ($tables as $table) {
            $delete_query = "DELETE FROM $table WHERE student_id = '$student_id'";
            mysqli_query($conn, $delete_query);
        }
        
        // Delete student
        $delete_student = "DELETE FROM students WHERE student_id = '$student_id'";
        
        if (mysqli_query($conn, $delete_student)) {
            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            throw new Exception('Failed to delete student');
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to delete student: ' . $e->getMessage()]);
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
    
    header('Content-Type: application/json');
    echo json_encode($houses);
}

function bulkUploadPoints() {
    global $conn;
    
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $reason = mysqli_real_escape_string($conn, $_POST['pointsReason']);
    $file_extension = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);
    
    $processed = 0;
    $successful = 0;
    $errors = [];
    
    try {
        // Handle CSV files directly
        if (strtolower($file_extension) === 'csv') {
            $handle = fopen($_FILES['excelFile']['tmp_name'], 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (empty($data[0]) || empty($data[1])) {
                    continue; // Skip empty rows
                }
                
                $student_id = mysqli_real_escape_string($conn, trim($data[0]));
                $points = floatval($data[1]);
                
                $processed++;
                
                // Check if student exists
                $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
                $check_result = mysqli_query($conn, $check_query);
                
                if (mysqli_num_rows($check_result) === 0) {
                    $errors[] = "Student ID $student_id not found";
                    continue;
                }
                
                // Insert points as appreciation
                $insert_query = "INSERT INTO appreciations (student_id, points, reason, date) 
                                VALUES ('$student_id', '$points', '$reason', CURDATE())";
                
                if (mysqli_query($conn, $insert_query)) {
                    $successful++;
                } else {
                    $errors[] = "Failed to add points for $student_id";
                }
            }
            
            fclose($handle);
            
        } else {
            // For Excel files, try to use PhpSpreadsheet if available, otherwise suggest CSV
            if (file_exists('../vendor/autoload.php')) {
                require_once '../vendor/autoload.php';
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['excelFile']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Skip header row (assuming first row is header)
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    
                    if (empty($row[0]) || empty($row[1])) {
                        continue; // Skip empty rows
                    }
                    
                    $student_id = mysqli_real_escape_string($conn, trim($row[0]));
                    $points = floatval($row[1]);
                    
                    $processed++;
                    
                    // Check if student exists
                    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
                    $check_result = mysqli_query($conn, $check_query);
                    
                    if (mysqli_num_rows($check_result) === 0) {
                        $errors[] = "Student ID $student_id not found";
                        continue;
                    }
                    
                    // Insert points as appreciation
                    $insert_query = "INSERT INTO appreciations (student_id, points, reason, date) 
                                    VALUES ('$student_id', '$points', '$reason', CURDATE())";
                    
                    if (mysqli_query($conn, $insert_query)) {
                        $successful++;
                    } else {
                        $errors[] = "Failed to add points for $student_id";
                    }
                }
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Excel files not supported. Please convert to CSV format or install PhpSpreadsheet library.'
                ]);
                return;
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
    
    $default_class = !empty($_POST['defaultClass']) ? mysqli_real_escape_string($conn, $_POST['defaultClass']) : '';
    $default_house = !empty($_POST['defaultHouse']) ? mysqli_real_escape_string($conn, $_POST['defaultHouse']) : '';
    $skip_duplicates = $_POST['skip_duplicates'] === 'true';
    
    $file_extension = pathinfo($_FILES['studentExcelFile']['name'], PATHINFO_EXTENSION);
    
    $processed = 0;
    $successful = 0;
    $skipped = 0;
    $errors = [];
    
    try {
        $rows = [];
        
        // Handle CSV files directly
        if (strtolower($file_extension) === 'csv') {
            $handle = fopen($_FILES['studentExcelFile']['tmp_name'], 'r');
            
            // Get header row to map columns
            $header = fgetcsv($handle);
            $header = array_map('trim', $header);
            $header = array_map('strtolower', $header);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 5) { // At least 5 columns required
                    $rows[] = array_combine($header, $data);
                }
            }
            
            fclose($handle);
            
        } else {
            // For Excel files, try to use PhpSpreadsheet if available
            if (file_exists('../vendor/autoload.php')) {
                require_once '../vendor/autoload.php';
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['studentExcelFile']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                
                if (count($data) > 1) {
                    $header = array_map('trim', $data[0]);
                    $header = array_map('strtolower', $header);
                    
                    for ($i = 1; $i < count($data); $i++) {
                        if (count($data[$i]) >= 5) {
                            $rows[] = array_combine($header, $data[$i]);
                        }
                    }
                }
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Excel files not supported. Please convert to CSV format or install PhpSpreadsheet library.'
                ]);
                return;
            }
        }
        
        // Process each row
        foreach ($rows as $row) {
            $processed++;
            
            // Skip empty rows
            if (empty(trim($row['student id'] ?? '')) || empty(trim($row['name'] ?? ''))) {
                continue;
            }
            
            // Extract data from row
            $student_id = mysqli_real_escape_string($conn, trim($row['student id'] ?? ''));
            $name = mysqli_real_escape_string($conn, trim($row['name'] ?? ''));
            $email = mysqli_real_escape_string($conn, trim($row['email'] ?? ''));
            $branch = mysqli_real_escape_string($conn, strtoupper(trim($row['branch'] ?? '')));
            $section = mysqli_real_escape_string($conn, strtoupper(trim($row['section'] ?? '')));
            
            // Optional fields with defaults
            $class_id = !empty($row['class id']) ? mysqli_real_escape_string($conn, trim($row['class id'])) : $default_class;
            $house_id = !empty($row['house id']) ? mysqli_real_escape_string($conn, trim($row['house id'])) : $default_house;
            $password = !empty($row['password']) ? mysqli_real_escape_string($conn, trim($row['password'])) : $student_id;
            
            // Validate required fields
            if (empty($student_id) || empty($name) || empty($email) || empty($branch) || empty($section)) {
                $errors[] = "Row $processed: Missing required fields (Student ID, Name, Email, Branch, Section)";
                continue;
            }
            
            // Validate branch
            if (!in_array($branch, ['CSD', 'CSIT'])) {
                $errors[] = "Row $processed: Invalid branch '$branch'. Must be CSD or CSIT";
                continue;
            }
            
            // Validate section
            if (!in_array($section, ['A', 'B'])) {
                $errors[] = "Row $processed: Invalid section '$section'. Must be A or B";
                continue;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $processed: Invalid email format '$email'";
                continue;
            }
            
            // Check if student ID already exists
            $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                if ($skip_duplicates) {
                    $skipped++;
                    continue;
                } else {
                    $errors[] = "Row $processed: Student ID '$student_id' already exists";
                    continue;
                }
            }
            
            // Check if email already exists
            $email_check = "SELECT student_id FROM students WHERE email = '$email'";
            $email_result = mysqli_query($conn, $email_check);
            
            if (mysqli_num_rows($email_result) > 0) {
                $errors[] = "Row $processed: Email '$email' already exists";
                continue;
            }
            
            // Validate class_id if provided
            if (!empty($class_id)) {
                $class_check = "SELECT class_id FROM classes WHERE class_id = '$class_id'";
                $class_result = mysqli_query($conn, $class_check);
                
                if (mysqli_num_rows($class_result) === 0) {
                    $errors[] = "Row $processed: Class ID '$class_id' does not exist";
                    continue;
                }
            }
            
            // Validate house_id if provided
            if (!empty($house_id)) {
                $house_check = "SELECT hid FROM houses WHERE hid = '$house_id'";
                $house_result = mysqli_query($conn, $house_check);
                
                if (mysqli_num_rows($house_result) === 0) {
                    $errors[] = "Row $processed: House ID '$house_id' does not exist";
                    continue;
                }
            }
            
            // If class_id or house_id is still empty, we need them
            if (empty($class_id)) {
                $errors[] = "Row $processed: Class ID is required (provide in file or set default)";
                continue;
            }
            
            if (empty($house_id)) {
                $errors[] = "Row $processed: House ID is required (provide in file or set default)";
                continue;
            }
            
            // Insert student
            $insert_query = "INSERT INTO students (student_id, name, email, password, branch, section, class_id, hid) 
                            VALUES ('$student_id', '$name', '$email', '$password', '$branch', '$section', '$class_id', '$house_id')";
            
            if (mysqli_query($conn, $insert_query)) {
                $successful++;
            } else {
                $errors[] = "Row $processed: Failed to insert student '$student_id' - " . mysqli_error($conn);
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

function addPointsToStudent() {
    global $conn;

    $student_id = mysqli_real_escape_string($conn, $_POST['pointsStudentId']);
    $points = floatval($_POST['pointsValue']);
    $reason = mysqli_real_escape_string($conn, $_POST['pointsReason']);
    $date = mysqli_real_escape_string($conn, $_POST['pointsDate']);

    // Validate student exists
    $check_query = "SELECT student_id, name FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (!$check_result) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
        return;
    }

    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }

    $student = mysqli_fetch_assoc($check_result);

    // Validate required fields
    if (empty($points) && $points !== 0) {
        echo json_encode(['success' => false, 'message' => 'Points value is required']);
        return;
    }

    if (empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Reason is required']);
        return;
    }

    if (empty($date)) {
        echo json_encode(['success' => false, 'message' => 'Date is required']);
        return;
    }

    // Insert points as appreciation
    $insert_query = "INSERT INTO appreciations (student_id, points, reason, date)
                    VALUES ('$student_id', '$points', '$reason', '$date')";
    $insert_result = mysqli_query($conn, $insert_query);

    if (!$insert_result) {
        echo json_encode(['success' => false, 'message' => 'Failed to add points: ' . mysqli_error($conn)]);
        return;
    }

    $action_type = $points >= 0 ? 'awarded' : 'deducted';
    $points_abs = abs($points);
    echo json_encode([
        'success' => true,
        'message' => "Successfully $action_type $points_abs points to {$student['name']} (ID: $student_id)"
    ]);
}

function searchStudents() {
    global $conn;
    
    $query = mysqli_real_escape_string($conn, $_GET['query'] ?? '');
    
    if (strlen($query) < 2) {
        echo json_encode([]);
        return;
    }
    
    $search_query = "SELECT s.student_id, s.name, s.branch, s.section, h.name as house_name,
                     COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
                     FROM students s
                     LEFT JOIN houses h ON s.hid = h.hid
                     LEFT JOIN appreciations a ON s.student_id = a.student_id
                     LEFT JOIN organizers o ON s.student_id = o.student_id
                     LEFT JOIN participants p ON s.student_id = p.student_id
                     WHERE (s.student_id LIKE '%$query%' OR s.name LIKE '%$query%')
                     GROUP BY s.student_id
                     ORDER BY s.name
                     LIMIT 10";
    
    $result = mysqli_query($conn, $search_query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($students);
}

function getAllStudents() {
    global $conn;
    
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;
    
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $class_filter = mysqli_real_escape_string($conn, $_GET['class_filter'] ?? '');
    $house_filter = mysqli_real_escape_string($conn, $_GET['house_filter'] ?? '');
    $branch_filter = mysqli_real_escape_string($conn, $_GET['branch_filter'] ?? '');
    $sort_by = mysqli_real_escape_string($conn, $_GET['sort_by'] ?? 'name');
    
    $where_conditions = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.email LIKE '%$search%')";
    }
    
    if (!empty($class_filter)) {
        $where_conditions[] = "s.class_id = '$class_filter'";
    }
    
    if (!empty($house_filter)) {
        $where_conditions[] = "s.hid = '$house_filter'";
    }
    
    if (!empty($branch_filter)) {
        $where_conditions[] = "s.branch = '$branch_filter'";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $order_by = match($sort_by) {
        'points' => 'total_points DESC',
        'student_id' => 's.student_id ASC',
        default => 's.name ASC'
    };
    
    $query = "SELECT s.*, h.name as house_name, 
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
              ORDER BY $order_by
              LIMIT $limit OFFSET $offset";
    
    $result = mysqli_query($conn, $query);
    $students = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(DISTINCT s.student_id) as total
                    FROM students s
                    LEFT JOIN houses h ON s.hid = h.hid
                    LEFT JOIN classes c ON s.class_id = c.class_id
                    $where_clause";
    
    $count_result = mysqli_query($conn, $count_query);
    $total_students = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_students / $limit);
    
    echo json_encode([
        'students' => $students,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_students' => $total_students
    ]);
}

function exportStudents() {
    global $conn;
    
    $search = $_GET['search'] ?? '';
    $class_filter = $_GET['class_filter'] ?? '';
    $house_filter = $_GET['house_filter'] ?? '';
    $branch_filter = $_GET['branch_filter'] ?? '';
    
    $where_conditions = [];
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $where_conditions[] = "(s.student_id LIKE '%$search%' OR s.name LIKE '%$search%' OR s.email LIKE '%$search%')";
    }
    
    if (!empty($class_filter)) {
        $class_filter = mysqli_real_escape_string($conn, $class_filter);
        $where_conditions[] = "s.class_id = '$class_filter'";
    }
    
    if (!empty($house_filter)) {
        $house_filter = mysqli_real_escape_string($conn, $house_filter);
        $where_conditions[] = "s.hid = '$house_filter'";
    }
    
    if (!empty($branch_filter)) {
        $branch_filter = mysqli_real_escape_string($conn, $branch_filter);
        $where_conditions[] = "s.branch = '$branch_filter'";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $query = "SELECT s.student_id, s.name, s.email, s.branch, s.section, 
              h.name as house_name, 
              CONCAT(c.academic_year, ' - ', c.branch, ' ', c.section, ' (Year ', c.year, ')') as class_info,
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
    header('Content-Disposition: attachment;filename="students_export_' . date('Y-m-d') . '.xls"');
    
    echo "Student ID\tName\tEmail\tBranch\tSection\tClass\tHouse\tTotal Points\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['student_id']}\t{$row['name']}\t{$row['email']}\t{$row['branch']}\t{$row['section']}\t{$row['class_info']}\t{$row['house_name']}\t{$row['total_points']}\n";
    }
}

function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    $queries = [
        'total_classes' => "SELECT COUNT(*) as count FROM classes",
        'total_students' => "SELECT COUNT(*) as count FROM students",
        'total_houses' => "SELECT COUNT(*) as count FROM houses",
        'total_events' => "SELECT COUNT(*) as count FROM events"
    ];
    
    foreach ($queries as $key => $query) {
        $result = mysqli_query($conn, $query);
        $stats[$key] = mysqli_fetch_assoc($result)['count'];
    }
    
    echo json_encode($stats);
}

function getHousePerformance() {
    global $conn;
    
    $query = "SELECT h.name,
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
        $labels[] = $row['name'];
        $data[] = $row['total_points'];
    }
    
    header('Content-Type: application/json');
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
        $data[] = $row['count'];
    }
    
    header('Content-Type: application/json');
    echo json_encode(['labels' => $labels, 'data' => $data]);
}

function getMonthlyActivity() {
    global $conn;
    
    // Get last 12 months
    $months = [];
    $events = [];
    $appreciations = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $months[] = date('M Y', strtotime("-$i months"));
        
        // Count events
        $event_query = "SELECT COUNT(*) as count FROM events WHERE DATE_FORMAT(date, '%Y-%m') = '$date'";
        $event_result = mysqli_query($conn, $event_query);
        $events[] = mysqli_fetch_assoc($event_result)['count'];
        
        // Count appreciations
        $app_query = "SELECT COUNT(*) as count FROM appreciations WHERE DATE_FORMAT(date, '%Y-%m') = '$date'";
        $app_result = mysqli_query($conn, $app_query);
        $appreciations[] = mysqli_fetch_assoc($app_result)['count'];
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'labels' => $months,
        'events' => $events,
        'appreciations' => $appreciations
    ]);
}

function getEvents() {
    global $conn;

    $query = "SELECT event_id as eid, title, description, event_date as date, 'General' as type FROM events ORDER BY event_date DESC, title ASC";
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
    $description = mysqli_real_escape_string($conn, $_POST['eventDescription'] ?? '');
    $date = mysqli_real_escape_string($conn, $_POST['eventDate']);
    $type = mysqli_real_escape_string($conn, $_POST['eventType']);
    
    if (empty($title) || empty($date) || empty($type)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        return;
    }
    
    $query = "INSERT INTO events (title, description, date, type) 
              VALUES ('$title', '$description', '$date', '$type')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Event created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create event: ' . mysqli_error($conn)]);
    }
}

function getEventDetails() {
    global $conn;

    $event_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT event_id as eid, title, description, event_date as date, 'General' as type FROM events WHERE event_id = '$event_id'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'event' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
    }
}

function getEventParticipants() {
    global $conn;
    
    $event_id = mysqli_real_escape_string($conn, $_GET['event_id']);
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    
    $where_clause = "p.eid = '$event_id'";
    if (!empty($search)) {
        $where_clause .= " AND (s.student_id LIKE '%$search%' OR s.name LIKE '%$search%')";
    }
    
    $query = "SELECT p.*, s.name, s.branch, s.section, h.name as house_name
              FROM participants p
              JOIN students s ON p.student_id = s.student_id
              LEFT JOIN houses h ON s.hid = h.hid
              WHERE $where_clause
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
    
    $event_id = mysqli_real_escape_string($conn, $_POST['participantEventId']);
    $student_id = mysqli_real_escape_string($conn, $_POST['participantStudentId']);
    $points = floatval($_POST['participantPoints'] ?? 0);
    
    // Check if student exists
    $student_check = "SELECT student_id FROM students WHERE student_id = '$student_id'";
    $student_result = mysqli_query($conn, $student_check);
    
    if (mysqli_num_rows($student_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }
    
    // Check if already participant
    $participant_check = "SELECT * FROM participants WHERE eid = '$event_id' AND student_id = '$student_id'";
    $participant_result = mysqli_query($conn, $participant_check);
    
    if (mysqli_num_rows($participant_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Student is already a participant in this event']);
        return;
    }
    
    $query = "INSERT INTO participants (eid, student_id, points) VALUES ('$event_id', '$student_id', '$points')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Participant added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add participant: ' . mysqli_error($conn)]);
    }
}

function updateParticipantPoints() {
    global $conn;
    
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $points = floatval($_POST['points']);
    
    $query = "UPDATE participants SET points = '$points' 
              WHERE eid = '$event_id' AND student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Points updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update points: ' . mysqli_error($conn)]);
    }
}

function removeParticipant() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = mysqli_real_escape_string($conn, $input['event_id']);
    $student_id = mysqli_real_escape_string($conn, $input['student_id']);
    
    $query = "DELETE FROM participants WHERE eid = '$event_id' AND student_id = '$student_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Participant removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove participant: ' . mysqli_error($conn)]);
    }
}

function bulkAwardParticipantPoints() {
    global $conn;
    
    $event_id = mysqli_real_escape_string($conn, $_POST['bulkPointsEventId']);
    $points = floatval($_POST['bulkPointsValue']);
    $reason = mysqli_real_escape_string($conn, $_POST['bulkPointsReason']);
    $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === 'true';
    
    if ($overwrite) {
        $query = "UPDATE participants SET points = '$points' WHERE eid = '$event_id'";
    } else {
        $query = "UPDATE participants SET points = points + $points WHERE eid = '$event_id'";
    }
    
    if (mysqli_query($conn, $query)) {
        $updated = mysqli_affected_rows($conn);
        echo json_encode(['success' => true, 'message' => 'Points awarded successfully', 'updated' => $updated]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to award points: ' . mysqli_error($conn)]);
    }
}

function clearAllParticipants() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = mysqli_real_escape_string($conn, $input['event_id']);
    
    $query = "DELETE FROM participants WHERE eid = '$event_id'";
    
    if (mysqli_query($conn, $query)) {
        $removed = mysqli_affected_rows($conn);
        echo json_encode(['success' => true, 'message' => 'All participants cleared', 'removed' => $removed]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear participants: ' . mysqli_error($conn)]);
    }
}

function bulkUploadParticipants() {
    global $conn;
    
    if (!isset($_FILES['participantsFile']) || $_FILES['participantsFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $event_id = mysqli_real_escape_string($conn, $_POST['bulkParticipantEventId']);
    $default_points = floatval($_POST['defaultParticipantPoints'] ?? 0);
    
    $file_path = $_FILES['participantsFile']['tmp_name'];
    $file_extension = strtolower(pathinfo($_FILES['participantsFile']['name'], PATHINFO_EXTENSION));
    
    $processed = 0;
    $successful = 0;
    $skipped = 0;
    $errors = [];
    
    try {
        if ($file_extension === 'csv') {
            $handle = fopen($file_path, 'r');
            $headers = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $processed++;
                
                if (empty($data[0])) {
                    $errors[] = "Row $processed: Student ID is required";
                    continue;
                }
                
                $student_id = mysqli_real_escape_string($conn, trim($data[0]));
                $points = isset($data[1]) && is_numeric($data[1]) ? floatval($data[1]) : $default_points;
                
                // Check if student exists
                $student_check = "SELECT student_id FROM students WHERE student_id = '$student_id'";
                if (mysqli_num_rows(mysqli_query($conn, $student_check)) === 0) {
                    $errors[] = "Row $processed: Student ID $student_id not found";
                    continue;
                }
                
                // Check if already participant
                $participant_check = "SELECT * FROM participants WHERE eid = '$event_id' AND student_id = '$student_id'";
                if (mysqli_num_rows(mysqli_query($conn, $participant_check)) > 0) {
                    $skipped++;
                    continue;
                }
                
                // Insert participant
                $insert_query = "INSERT INTO participants (eid, student_id, points) VALUES ('$event_id', '$student_id', '$points')";
                if (mysqli_query($conn, $insert_query)) {
                    $successful++;
                } else {
                    $errors[] = "Row $processed: Failed to add participant $student_id";
                }
            }
            fclose($handle);
        } else {
            // Handle Excel files using a simple approach
            $errors[] = "Excel files not fully supported yet. Please use CSV format.";
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

function exportEventParticipants() {
    global $conn;
    
    $event_id = mysqli_real_escape_string($conn, $_GET['event_id']);
    
    // Get event details
    $event_query = "SELECT title FROM events WHERE eid = '$event_id'";
    $event_result = mysqli_query($conn, $event_query);
    $event = mysqli_fetch_assoc($event_result);
    
    // Get participants
    $query = "SELECT p.student_id, s.name, s.branch, s.section, h.name as house_name, p.points
              FROM participants p
              JOIN students s ON p.student_id = s.student_id
              LEFT JOIN houses h ON s.hid = h.hid
              WHERE p.eid = '$event_id'
              ORDER BY s.name";
    
    $result = mysqli_query($conn, $query);
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $event['title'] . '_participants.xls"');
    
    echo "Student ID\tName\tBranch\tSection\tHouse\tPoints\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['student_id']}\t{$row['name']}\t{$row['branch']}\t{$row['section']}\t{$row['house_name']}\t{$row['points']}\n";
    }
}
?>