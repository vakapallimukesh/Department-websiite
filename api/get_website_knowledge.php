<?php
/**
 * Dynamic Website Knowledge API Endpoint
 * SRKREC CSD & CSIT Department Assistant
 *
 * Connects Department AI directly to live MySQL Database tables (`faculties`, `students`, `houses`, `classes`)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/../connect.php";

$faculties = [];
$students = [];
$houses = [];
$classes = [];

// 1. Fetch All Faculties from MySQL `faculties` Table
$facRes = mysqli_query($conn, "SELECT * FROM faculties ORDER BY faculty_id ASC");
if ($facRes) {
    while ($row = mysqli_fetch_assoc($facRes)) {
        $name = isset($row['faculty_name']) ? $row['faculty_name'] : (isset($row['name']) ? $row['name'] : '');
        if (empty($name)) continue;

        $hasPhD = (stripos($name, 'Dr.') !== false || stripos($name, 'Dr ') !== false);
        $qual = $hasPhD ? 'Ph.D' : 'M.Tech in CSE';

        // Custom rich qualification metadata mapping for verified faculty
        if (stripos($name, 'Suresh Babu') !== false) {
            $qual = 'Ph.D in Computer Science (JNTU, 2010)';
            $hasPhD = true;
        } elseif (stripos($name, 'Gopala Krishna') !== false || stripos($name, 'NGK') !== false) {
            $qual = 'Ph.D in Information Technology (JNTU, 2011)';
            $hasPhD = true;
        } elseif (stripos($name, 'Srinivasa Rao') !== false) {
            $qual = 'Ph.D in Computer Science (Andhra University, 2018)';
            $hasPhD = true;
        }

        $faculties[] = [
            'id' => 'faculty_' . $row['faculty_id'],
            'faculty_id' => (int)$row['faculty_id'],
            'fullName' => $name,
            'email' => isset($row['email']) ? $row['email'] : '',
            'phone' => isset($row['phone_number']) ? $row['phone_number'] : '',
            'qualification' => $qual,
            'hasPhD' => $hasPhD,
            'department' => (stripos($name, 'Murthy') !== false || stripos($name, 'Trinadh') !== false || stripos($name, 'Manoj') !== false || stripos($name, 'Praveen') !== false) ? 'CSIT' : 'CSD',
            'role' => (stripos($name, 'Suresh') !== false || stripos($name, 'Murthy') !== false) ? 'Professor & Head of Department' : 'Assistant Professor',
            'category' => (stripos($name, 'Suresh') !== false || stripos($name, 'Murthy') !== false) ? 'Faculty & Head of Department' : 'Faculty Member'
        ];
    }
}

// 2. Fetch Houses from MySQL `houses` Table
$houseRes = mysqli_query($conn, "SELECT * FROM houses ORDER BY hid ASC");
if ($houseRes) {
    while ($row = mysqli_fetch_assoc($houseRes)) {
        $houses[] = [
            'hid' => (int)$row['hid'],
            'name' => $row['name']
        ];
    }
}

// 3. Fetch Classes from MySQL `classes` Table
$classRes = mysqli_query($conn, "SELECT * FROM classes ORDER BY class_id ASC");
if ($classRes) {
    while ($row = mysqli_fetch_assoc($classRes)) {
        $classes[] = [
            'class_id' => (int)$row['class_id'],
            'branch' => $row['branch'],
            'year' => $row['year'],
            'section' => $row['section']
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'total_faculties' => count($faculties),
    'total_houses' => count($houses),
    'total_classes' => count($classes),
    'faculties' => $faculties,
    'houses' => $houses,
    'classes' => $classes
]);
exit();
