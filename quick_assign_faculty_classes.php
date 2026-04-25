<?php
include './connect.php';

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

echo "<h2>Quick Assign Multiple Classes to Faculty</h2>";
echo "<p>This script helps you quickly assign multiple classes to faculty members.</p>";

// Example assignments - Modify these based on your needs
$assignments = [
    // Format: faculty_id => 'comma-separated class_ids'
    // Example: 14 => '3,5,6' means faculty ID 14 gets classes 3, 5, and 6
    
    // Uncomment and modify the lines below as needed:
    // 1 => '1',           // Dr.M.Suresh Babu -> 4/4 CSD-A
    // 2 => '2',           // Dr. K. Srinivasa Rao -> 3/4 CSD-A
    // 3 => '4',           // Mr. K. Bhanu Rajesh Naidu -> 2/4 CSD-A
    // 8 => '3',           // Dr N. Gopala Krishna Murthy -> 3/4 CSIT-A
    // 9 => '5',           // Jonnapalli Tulasi Rajesh -> 2/4 CSIT-A
    // 10 => '6',          // Navya Nallaparaju -> 2/4 CSIT-B
    // 14 => '3',          // Penmetsa Mouna -> 3/4 CSIT-A
    
    // Example of multiple classes:
    // 14 => '3,5,6',      // Penmetsa Mouna -> 3/4 CSIT-A, 2/4 CSIT-A, 2/4 CSIT-B
];

echo "<h3>Assignments to Process:</h3>";
if (empty($assignments)) {
    echo "<p style='color: orange;'><b>No assignments configured.</b> Please edit this file and uncomment/modify the assignments array.</p>";
    echo "<p>Example format:</p>";
    echo "<pre>\$assignments = [
    14 => '3,5,6',  // Faculty ID 14 gets classes 3, 5, and 6
    8 => '3,4',     // Faculty ID 8 gets classes 3 and 4
];</pre>";
} else {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Faculty ID</th><th>Faculty Name</th><th>Class IDs</th><th>Class Names</th><th>Status</th></tr>";
    
    foreach ($assignments as $faculty_id => $class_ids) {
        // Get faculty name
        $faculty_query = "SELECT faculty_name FROM faculties WHERE faculty_id = ?";
        $stmt = mysqli_prepare($conn, $faculty_query);
        mysqli_stmt_bind_param($stmt, "i", $faculty_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $faculty = mysqli_fetch_assoc($result);
        $faculty_name = $faculty ? $faculty['faculty_name'] : 'Unknown';
        
        // Get class names
        $class_id_array = explode(',', $class_ids);
        $class_ids_in = implode(',', array_map('intval', $class_id_array));
        $classes_query = "SELECT year, branch, section FROM classes WHERE class_id IN ($class_ids_in) ORDER BY year, branch, section";
        $classes_result = mysqli_query($conn, $classes_query);
        $class_names = [];
        while ($class = mysqli_fetch_assoc($classes_result)) {
            $class_names[] = $class['year'] . '/' . $class['branch'] . '-' . $class['section'];
        }
        
        // Update faculty
        $update_query = "UPDATE faculties SET class_id = ? WHERE faculty_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $class_ids, $faculty_id);
        
        $status = mysqli_stmt_execute($update_stmt) ? 
            "<span style='color: green;'>✓ Success</span>" : 
            "<span style='color: red;'>✗ Failed: " . mysqli_error($conn) . "</span>";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($faculty_id) . "</td>";
        echo "<td>" . htmlspecialchars($faculty_name) . "</td>";
        echo "<td>" . htmlspecialchars($class_ids) . "</td>";
        echo "<td>" . htmlspecialchars(implode(', ', $class_names)) . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
        
        mysqli_stmt_close($update_stmt);
    }
    
    echo "</table>";
}

// Show all current assignments
echo "<hr>";
echo "<h3>Current Faculty Assignments (After Update):</h3>";
$all_faculty_query = "SELECT f.faculty_id, f.faculty_name, f.email, f.class_id 
                      FROM faculties f 
                      ORDER BY f.faculty_name";
$all_faculty_result = mysqli_query($conn, $all_faculty_query);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Faculty ID</th><th>Faculty Name</th><th>Email</th><th>Class IDs</th><th>Class Names</th></tr>";

while ($faculty = mysqli_fetch_assoc($all_faculty_result)) {
    $class_names = [];
    if (!empty($faculty['class_id'])) {
        $class_id_array = explode(',', $faculty['class_id']);
        $class_ids_in = implode(',', array_map('intval', $class_id_array));
        $classes_query = "SELECT year, branch, section FROM classes WHERE class_id IN ($class_ids_in) ORDER BY year, branch, section";
        $classes_result = mysqli_query($conn, $classes_query);
        while ($class = mysqli_fetch_assoc($classes_result)) {
            $class_names[] = $class['year'] . '/' . $class['branch'] . '-' . $class['section'];
        }
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($faculty['faculty_id']) . "</td>";
    echo "<td>" . htmlspecialchars($faculty['faculty_name']) . "</td>";
    echo "<td>" . htmlspecialchars($faculty['email']) . "</td>";
    echo "<td>" . htmlspecialchars($faculty['class_id'] ?? 'NULL') . "</td>";
    echo "<td>" . (empty($class_names) ? '<i>No classes</i>' : htmlspecialchars(implode(', ', $class_names))) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>IMPORTANT: Please delete this file (quick_assign_faculty_classes.php) after use for security reasons.</p>";
echo "<p><a href='admin_assign_faculty_classes.php'>Go to Admin Class Assignment Interface</a></p>";
echo "<p><a href='faculty_appreciations.php'>Go to Faculty Appreciations</a></p>";

mysqli_close($conn);
?>
