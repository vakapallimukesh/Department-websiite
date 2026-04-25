<?php
session_start();
include './connect.php';

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_classes'])) {
    $faculty_id = (int)$_POST['faculty_id'];
    $selected_classes = $_POST['class_ids'] ?? [];
    
    if (empty($faculty_id)) {
        $error = "Please select a faculty member.";
    } else {
        // Convert array to comma-separated string
        $class_ids_string = !empty($selected_classes) ? implode(',', array_map('intval', $selected_classes)) : NULL;
        
        $update_query = "UPDATE faculties SET class_id = ? WHERE faculty_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        
        if ($class_ids_string === NULL) {
            mysqli_stmt_bind_param($stmt, "si", $class_ids_string, $faculty_id);
        } else {
            mysqli_stmt_bind_param($stmt, "si", $class_ids_string, $faculty_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Classes assigned successfully to faculty!";
        } else {
            $error = "Error assigning classes: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all faculties
$faculties_query = "SELECT faculty_id, faculty_name, email, class_id FROM faculties ORDER BY faculty_name";
$faculties_result = mysqli_query($conn, $faculties_query);
$faculties = [];
while ($faculty = mysqli_fetch_assoc($faculties_result)) {
    $faculties[] = $faculty;
}

// Fetch all classes
$classes_query = "SELECT class_id, year, branch, section FROM classes ORDER BY year, branch, section";
$classes_result = mysqli_query($conn, $classes_query);
$classes = [];
while ($class = mysqli_fetch_assoc($classes_result)) {
    $classes[] = $class;
}

// Get selected faculty's current classes
$selected_faculty_id = isset($_POST['faculty_id']) ? (int)$_POST['faculty_id'] : 0;
$current_classes = [];
if ($selected_faculty_id > 0) {
    $faculty_query = "SELECT class_id FROM faculties WHERE faculty_id = ?";
    $stmt = mysqli_prepare($conn, $faculty_query);
    mysqli_stmt_bind_param($stmt, "i", $selected_faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $faculty_data = mysqli_fetch_assoc($result);
    if ($faculty_data && !empty($faculty_data['class_id'])) {
        $current_classes = explode(',', $faculty_data['class_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Classes to Faculty - SRKR Engineering College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mb-4"><i class="fas fa-chalkboard-teacher"></i> Assign Multiple Classes to Faculty</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-body">
                        <form method="POST" action="admin_assign_faculty_classes.php">
                            <div class="mb-4">
                                <label for="faculty_id" class="form-label"><strong>Select Faculty:</strong></label>
                                <select class="form-control" id="faculty_id" name="faculty_id" required onchange="this.form.submit()">
                                    <option value="">Choose a faculty member...</option>
                                    <?php foreach ($faculties as $faculty): ?>
                                        <option value="<?php echo $faculty['faculty_id']; ?>" 
                                                <?php echo ($selected_faculty_id == $faculty['faculty_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($faculty['faculty_name']) . ' (' . htmlspecialchars($faculty['email']) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($selected_faculty_id > 0): ?>
                                <div class="mb-4">
                                    <label class="form-label"><strong>Assign Classes (Select Multiple):</strong></label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <?php foreach ($classes as $class): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="class_ids[]" 
                                                       value="<?php echo $class['class_id']; ?>"
                                                       id="class_<?php echo $class['class_id']; ?>"
                                                       <?php echo in_array($class['class_id'], $current_classes) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="class_<?php echo $class['class_id']; ?>">
                                                    <?php echo htmlspecialchars($class['year'] . '/' . $class['branch'] . '-' . $class['section']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="text-muted">Check all classes this faculty should have access to.</small>
                                </div>

                                <button type="submit" name="assign_classes" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Class Assignments
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Current Faculty Assignments -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Current Faculty Class Assignments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Faculty Name</th>
                                        <th>Email</th>
                                        <th>Assigned Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faculties as $faculty): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($faculty['faculty_name']); ?></td>
                                            <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                                            <td>
                                                <?php 
                                                if (!empty($faculty['class_id'])) {
                                                    $faculty_class_ids = explode(',', $faculty['class_id']);
                                                    $class_names = [];
                                                    foreach ($faculty_class_ids as $class_id) {
                                                        $class_id = trim($class_id);
                                                        foreach ($classes as $class) {
                                                            if ($class['class_id'] == $class_id) {
                                                                $class_names[] = $class['year'] . '/' . $class['branch'] . '-' . $class['section'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    echo htmlspecialchars(implode(', ', $class_names));
                                                } else {
                                                    echo '<span class="text-muted">No classes assigned</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="faculty_appreciations.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Appreciations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
