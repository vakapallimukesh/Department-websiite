<?php
/**
 * Database Migration Helper
 * Provides functions to map between old and new database structures
 */

class DatabaseMigrationHelper {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get class information from old table name
     */
    public function getClassFromOldTable($old_table) {
        $mapping = [
            '28csit_a_attendance' => ['year' => 2, 'branch' => 'CSIT', 'section' => 'A'],
            '28csit_b_attendance' => ['year' => 2, 'branch' => 'CSIT', 'section' => 'B'],
            '28csd_attendance' => ['year' => 2, 'branch' => 'CSD', 'section' => ''],
            '27csit_attendance' => ['year' => 3, 'branch' => 'CSIT', 'section' => ''],
            '27csd_attendance' => ['year' => 3, 'branch' => 'CSD', 'section' => ''],
            '26csd_attendance' => ['year' => 4, 'branch' => 'CSD', 'section' => '']
        ];
        
        return $mapping[$old_table] ?? null;
    }
    
    /**
     * Get class_id from year, branch, and section
     */
    public function getClassId($year, $branch, $section = '') {
        $query = "SELECT class_id FROM classes WHERE year = ? AND branch = ? AND section = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $year, $branch, $section);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row ? $row['class_id'] : null;
    }
    
    /**
     * Get student_id from registration number
     */
    public function getStudentId($reg_no) {
        $email = "$reg_no@srkrec.edu.in";
        $query = "SELECT student_id FROM students WHERE email = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row ? $row['student_id'] : null;
    }
    
    /**
     * Get faculty_id from faculty name
     */
    public function getFacultyId($faculty_name) {
        $query = "SELECT faculty_id FROM faculties WHERE faculty_name = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $faculty_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row ? $row['faculty_id'] : null;
    }
    
    /**
     * Get all students for a specific class
     */
    public function getStudentsByClass($class_id) {
        $query = "SELECT student_id, name, email FROM students WHERE class_id = ? ORDER BY name";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $class_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        return $students;
    }
    
    /**
     * Get attendance records for a student
     */
    public function getStudentAttendance($student_id, $start_date = null, $end_date = null) {
        $query = "SELECT sa.*, f.faculty_name 
                  FROM student_attendance sa 
                  LEFT JOIN faculties f ON sa.faculty_id = f.faculty_id 
                  WHERE sa.student_id = ?";
        
        $params = [$student_id];
        $types = "i";
        
        if ($start_date) {
            $query .= " AND sa.attendance_date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }
        
        if ($end_date) {
            $query .= " AND sa.attendance_date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }
        
        $query .= " ORDER BY sa.attendance_date DESC, sa.session";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $attendance = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $attendance[] = $row;
        }
        
        return $attendance;
    }
    
    /**
     * Get attendance statistics for a student
     */
    public function getStudentAttendanceStats($student_id, $start_date = null, $end_date = null) {
        $query = "SELECT 
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_sessions,
                    ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
                  FROM student_attendance 
                  WHERE student_id = ?";
        
        $params = [$student_id];
        $types = "i";
        
        if ($start_date) {
            $query .= " AND attendance_date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }
        
        if ($end_date) {
            $query .= " AND attendance_date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }
    
    /**
     * Get all classes with their display names
     */
    public function getAllClasses() {
        $query = "SELECT class_id, academic_year, year, semester, branch, section 
                  FROM classes 
                  ORDER BY year, branch, section";
        
        $stmt = mysqli_prepare($this->conn, $query);
        if (!$stmt) {
            error_log("Failed to prepare getAllClasses query: " . mysqli_error($this->conn));
            return [];
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $class_id, $academic_year, $year, $semester, $branch, $section);
        
        $classes = [];
        while (mysqli_stmt_fetch($stmt)) {
            if ($year >= 5) {
                $display_name = 'Graduated Batch';
            } else {
                $display_name = $year . '/4 ' . $branch;
                if (!empty($section)) {
                    $display_name .= '-' . $section;
                }
            }
            
            $classes[$class_id] = $display_name;
        }
        mysqli_stmt_close($stmt);
        return $classes;
    }
    
    /**
     * Get faculty members with their assigned classes
     */
    public function getFacultyMembers() {
        $query = "SELECT f.faculty_id, f.faculty_name, f.email, f.phone_number, 
                         c.year, c.branch, c.section
                  FROM faculties f
                  LEFT JOIN classes c ON f.class_id = c.class_id
                  WHERE f.is_active = 1
                  ORDER BY f.faculty_name";
        
        $result = mysqli_query($this->conn, $query);
        $faculty = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['year']) && $row['year'] >= 5) {
                $display_section = 'Graduated Batch';
            } else {
                $display_section = (!empty($row['year']) ? $row['year'] . '/4 ' : '') . ($row['branch'] ?? '');
                if (!empty($row['section'])) {
                    $display_section .= '-' . $row['section'];
                }
            }
            
            $faculty[] = [
                'faculty_id' => $row['faculty_id'],
                'faculty_name' => $row['faculty_name'],
                'email' => $row['email'],
                'phone_number' => $row['phone_number'],
                'assigned_section' => $display_section,
                'class_id' => $row['class_id'] ?? null
            ];
        }
        
        return $faculty;
    }

    /**
     * Get section names from class IDs
     */
    public function getSectionNamesByIds($class_ids) {
        if (empty($class_ids)) {
            return [];
        }
        // Create a string of placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
        $query = "SELECT section FROM classes WHERE class_id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare getSectionNamesByIds query: " . $this->conn->error);
            return [];
        }
        
        // Dynamically bind parameters
        $types = str_repeat('i', count($class_ids)); // All class_ids are integers
        $stmt->bind_param($types, ...$class_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sections = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sections[] = $row['section'];
        }
        $stmt->close();
        return $sections;
    }
    
    /**
     * Mark attendance for multiple students
     */
    public function markAttendance($class_id, $date, $session, $faculty_id, $student_attendance) {
        $success = true;
        
        foreach ($student_attendance as $student_id => $status) {
            $query = "INSERT INTO student_attendance (attendance_date, session, student_id, status, faculty_id) 
                      VALUES (?, ?, ?, ?, ?) 
                      ON DUPLICATE KEY UPDATE status = ?, faculty_id = ?";
            
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ssissis", $date, $session, $student_id, $status, $faculty_id, $status, $faculty_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
                break;
            }
        }
        
        return $success;
    }
    
    /**
     * Log attendance modification
     */
    public function logModification($table_name, $date, $session, $faculty_name, $reason, $changes = null) {
        $query = "INSERT INTO attendance_modifications 
                  (table_name, attendance_date, session, faculty_name, modification_reason, changes_made) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $table_name, $date, $session, $faculty_name, $reason, $changes);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Get attendance modifications
     */
    public function getModifications($filters = []) {
        $query = "SELECT * FROM attendance_modifications WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['table_name'])) {
            $query .= " AND table_name = ?";
            $params[] = $filters['table_name'];
            $types .= "s";
        }
        
        if (!empty($filters['faculty_name'])) {
            $query .= " AND faculty_name LIKE ?";
            $params[] = "%" . $filters['faculty_name'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND attendance_date >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND attendance_date <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        $query .= " ORDER BY modified_at DESC";
        
        if (!empty($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = $filters['limit'];
            $types .= "i";
        }
        
        $stmt = mysqli_prepare($this->conn, $query);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $modifications = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $modifications[] = $row;
        }
        
        return $modifications;
    }
    
    /**
     * Get house information
     */
    public function getHouses() {
        $query = "SELECT hid, name FROM houses ORDER BY name";
        $stmt = mysqli_prepare($this->conn, $query);
        if (!$stmt) {
            error_log("Failed to prepare getHouses query: " . mysqli_error($this->conn));
            return [];
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hid, $name);
        
        $houses = [];
        while (mysqli_stmt_fetch($stmt)) {
            $houses[$hid] = $name;
        }
        mysqli_stmt_close($stmt);
        return $houses;
    }
    
    /**
     * Get students with house assignments
     */
    public function getStudentsWithHouses($class_id = null) {
        $query = "SELECT s.student_id, s.name, s.email, s.branch, s.section, h.name as house_name
                  FROM students s
                  LEFT JOIN houses h ON s.hid = h.hid";
        
        $params = [];
        $types = "";
        
        if ($class_id) {
            $query .= " WHERE s.class_id = ?";
            $params[] = $class_id;
            $types = "i";
        }
        
        $query .= " ORDER BY s.name";
        
        $stmt = mysqli_prepare($this->conn, $query);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        return $students;
    }
}

/*
// Add student_profile table if it doesn't exist (run this once manually if needed)
// $create_student_profile = "...";
// if (isset($conn)) { mysqli_query($conn, $create_student_profile); }

// Add default skills for existing students if they don't have skills (run once)
// $default_skills = "...";
// if (isset($conn)) { mysqli_query($conn, $default_skills); }
*/

// Global helper instance
$db_helper = new DatabaseMigrationHelper($conn);
?>
