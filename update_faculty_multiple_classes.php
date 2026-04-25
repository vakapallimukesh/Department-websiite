<?php
include './connect.php';

if (!isset($conn)) {
    die('Database connection failed.');
}

echo "<h2>Updating Faculty Table to Support Multiple Classes</h2>";

// First, check the current table structure
$check_query = "SHOW COLUMNS FROM faculties LIKE 'class_id'";
$result = mysqli_query($conn, $check_query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<h3>Current class_id Column:</h3>";
    echo "<pre>" . print_r($row, true) . "</pre>";
}

// Modify the class_id column to VARCHAR to store comma-separated values
$alter_query = "ALTER TABLE `faculties` 
                MODIFY COLUMN `class_id` VARCHAR(255) DEFAULT NULL 
                COMMENT 'Comma-separated class IDs'";

if (mysqli_query($conn, $alter_query)) {
    echo "<p style='color: green;'><b>Success:</b> The class_id column has been updated to support multiple classes (comma-separated values).</p>";
} else {
    echo "<p style='color: red;'><b>Error:</b> " . htmlspecialchars(mysqli_error($conn)) . "</p>";
}

// Verify the change
echo "<h3>Updated class_id Column:</h3>";
$verify_query = "SHOW COLUMNS FROM faculties LIKE 'class_id'";
$verify_result = mysqli_query($conn, $verify_query);
if ($verify_result) {
    $verify_row = mysqli_fetch_assoc($verify_result);
    echo "<pre>" . print_r($verify_row, true) . "</pre>";
}

// Show current faculty data
echo "<h3>Current Faculty Data:</h3>";
$faculty_query = "SELECT faculty_id, faculty_name, class_id FROM faculties ORDER BY faculty_id";
$faculty_result = mysqli_query($conn, $faculty_query);
if ($faculty_result) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Faculty ID</th><th>Faculty Name</th><th>Class ID(s)</th></tr>";
    while ($faculty = mysqli_fetch_assoc($faculty_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($faculty['faculty_id']) . "</td>";
        echo "<td>" . htmlspecialchars($faculty['faculty_name']) . "</td>";
        echo "<td>" . htmlspecialchars($faculty['class_id'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>How to Assign Multiple Classes:</h3>";
echo "<p>You can now assign multiple classes to a faculty by using comma-separated class IDs.</p>";
echo "<p><b>Example SQL:</b></p>";
echo "<pre>UPDATE faculties SET class_id = '1,2,3' WHERE faculty_id = 1;</pre>";
echo "<p>This will assign classes with IDs 1, 2, and 3 to faculty with ID 1.</p>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>IMPORTANT: Please delete this file (update_faculty_multiple_classes.php) after running it for security reasons.</p>";
echo "<p><a href='faculty_appreciations.php'>Go to Faculty Appreciations</a></p>";

mysqli_close($conn);
?>
