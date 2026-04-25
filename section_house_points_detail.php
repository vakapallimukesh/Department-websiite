<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include './connect.php';
include './db_migration_helper.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$db_helper = new DatabaseMigrationHelper($conn);

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    header('Location: section_house_points.php');
    exit();
}

// Get class information using bind_result
$class_query = "SELECT class_id, year, branch, section, semester, academic_year FROM classes WHERE class_id = ?";
$stmt = mysqli_prepare($conn, $class_query);
mysqli_stmt_bind_param($stmt, "i", $class_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $class_id_result, $year, $branch, $section, $semester, $academic_year);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: section_house_points.php');
    exit();
}
mysqli_stmt_close($stmt);

$class_data = [
    'class_id' => $class_id_result,
    'year' => $year,
    'branch' => $branch,
    'section' => $section,
    'semester' => $semester,
    'academic_year' => $academic_year
];

$section_name = $class_data['year'] . '/4 ' . strtoupper($class_data['branch']) . '-' . strtoupper($class_data['section']);

// First, get all students using a regular query to avoid statement conflicts
$students_query = "
    SELECT s.student_id, s.name, h.name as house_name
    FROM students s
    LEFT JOIN houses h ON s.hid = h.hid
    WHERE s.class_id = " . (int)$class_id . "
    ORDER BY s.student_id ASC
";
$students_result = mysqli_query($conn, $students_query);

$students = [];
if ($students_result) {
    while ($row = mysqli_fetch_assoc($students_result)) {
        $student = [
            'student_id' => $row['student_id'],
            'name' => $row['name'],
            'house_name' => $row['house_name']
        ];
        
        // Calculate total house points from all valid sources using regular queries
        $house_points = 0;
        
        // Points from participants
        $participants_query = "SELECT COALESCE(SUM(points), 0) as points FROM participants WHERE student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'";
        $participants_result = mysqli_query($conn, $participants_query);
        if ($participants_result) {
            $participants_data = mysqli_fetch_assoc($participants_result);
            $house_points += (int)($participants_data['points'] ?? 0);
        }

        // Points from organizers
        $organizers_query = "SELECT COALESCE(SUM(points), 0) as points FROM organizers WHERE student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'";
        $organizers_result = mysqli_query($conn, $organizers_query);
        if ($organizers_result) {
            $organizers_data = mysqli_fetch_assoc($organizers_result);
            $house_points += (int)($organizers_data['points'] ?? 0);
        }

        // Points from appreciations
        $appreciations_query = "SELECT COALESCE(SUM(points), 0) as points FROM appreciations WHERE student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'";
        $appreciations_result = mysqli_query($conn, $appreciations_query);
        if ($appreciations_result) {
            $appreciations_data = mysqli_fetch_assoc($appreciations_result);
            $house_points += (int)($appreciations_data['points'] ?? 0);
        }

        // Add penalties (penalties are already negative values in database)
        $penalties_query = "SELECT COALESCE(SUM(points), 0) as points FROM penalties WHERE student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'";
        $penalties_result = mysqli_query($conn, $penalties_query);
        if ($penalties_result) {
            $penalties_data = mysqli_fetch_assoc($penalties_result);
            $house_points += (int)($penalties_data['points'] ?? 0);
        }

        // Get attendance stats and convert percentage to points (round to whole number)
        $attendance_stats = $db_helper->getStudentAttendanceStats($student['student_id']);
        $attendance_percentage = $attendance_stats['attendance_percentage'] ?? 0;
        $attendance_points = round($attendance_percentage); // Round to whole number

        // Get detailed appreciations data - using regular query since it's complex
        $appreciations_detail_query = "
            SELECT 'participant' as source, e.title as event_title, p.points, 'Participation' as reason, p.registered_at as created_at
            FROM participants p
            JOIN events e ON p.event_id = e.event_id
            WHERE p.student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "' AND p.points > 0
            
            UNION ALL
            
            SELECT 'organizer' as source, e.title as event_title, o.points, o.role as reason, o.assigned_at as created_at
            FROM organizers o
            JOIN events e ON o.event_id = e.event_id
            WHERE o.student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'
            
            UNION ALL
            
            SELECT 'appreciation' as source, e.title as event_title, a.points, a.reason, a.created_at
            FROM appreciations a
            JOIN events e ON a.event_id = e.event_id
            WHERE a.student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'
            
            ORDER BY created_at DESC
        ";
        $appreciations_detail_result = mysqli_query($conn, $appreciations_detail_query);
        $appreciations_details = [];
        if ($appreciations_detail_result) {
            while ($row = mysqli_fetch_assoc($appreciations_detail_result)) {
                $appreciations_details[] = $row;
            }
        }

        // Get detailed penalties data - using regular query since it's simpler
        $penalties_detail_query = "
            SELECT e.title as event_title, p.points, p.reason, p.created_at
            FROM penalties p
            JOIN events e ON p.event_id = e.event_id
            WHERE p.student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'
            ORDER BY p.created_at DESC
        ";
        $penalties_detail_result = mysqli_query($conn, $penalties_detail_query);
        $penalties_details = [];
        if ($penalties_detail_result) {
            while ($row = mysqli_fetch_assoc($penalties_detail_result)) {
                $penalties_details[] = $row;
            }
        }

        $student['house_points'] = $house_points;
        $student['attendance_points'] = $attendance_points;
        $student['appreciations_details'] = $appreciations_details;
        $student['penalties_details'] = $penalties_details;
        $students[] = $student;
    }
}

$student_count = count($students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title><?php echo htmlspecialchars($section_name); ?> - House Points</title>
</head>
<body>
    <?php include "nav.php"; ?>

    <div class="page-title">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($section_name); ?> - House Points</h2>
                    <p><?php echo $student_count; ?> students in this section</p>
                </div>
                <div>
                    <a href="section_house_points.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sections
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <!-- Section Info Card -->
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 16px rgba(7,101,147,0.1); border-radius: 15px;">
                <div class="card-header" style="background: var(--light-blue); border-bottom: 1px solid #e3e6f0; border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0" style="color: var(--primary-blue); font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Section Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="info-item">
                                <i class="fas fa-graduation-cap" style="font-size: 2rem; color: var(--primary-blue); margin-bottom: 10px;"></i>
                                <h4 class="text-primary"><?php echo $class_data['year']; ?>/4</h4>
                                <p class="text-muted mb-0">Year</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="info-item">
                                <i class="fas fa-code-branch" style="font-size: 2rem; color: var(--success); margin-bottom: 10px;"></i>
                                <h4 class="text-success"><?php echo strtoupper($class_data['branch']); ?></h4>
                                <p class="text-muted mb-0">Branch</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="info-item">
                                <i class="fas fa-layer-group" style="font-size: 2rem; color: var(--info); margin-bottom: 10px;"></i>
                                <h4 class="text-info"><?php echo strtoupper($class_data['section']); ?></h4>
                                <p class="text-muted mb-0">Section</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt" style="font-size: 2rem; color: var(--warning); margin-bottom: 10px;"></i>
                                <h4 class="text-warning"><?php echo $class_data['semester']; ?></h4>
                                <p class="text-muted mb-0">Semester</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <?php if (!empty($students)): ?>
                <div class="card" style="border: none; box-shadow: 0 4px 16px rgba(7,101,147,0.1); border-radius: 15px;">
                    <div class="card-header" style="background: var(--primary-blue); color: white; border-radius: 15px 15px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Student House Points</h5>
                            <button onclick="downloadPDF()" class="btn btn-light btn-sm">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Toggle Attendance Points -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="toggleAttendance" checked onchange="toggleAttendancePoints()">
                                <label class="form-check-label" for="toggleAttendance">
                                    Show Attendance Points
                                </label>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="housePointsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>House</th>
                                        <th>House Points</th>
                                        <th class="attendance-column">Attendance Points</th>
                                        <th>Appreciations</th>
                                        <th>Penalties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td data-label="Registration Number"><?php echo htmlspecialchars($student['student_id']); ?></td>
                                            <td data-label="Student Name"><?php echo htmlspecialchars($student['name']); ?></td>
                                            <td data-label="House">
                                                <?php if ($student['house_name']): ?>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($student['house_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="House Points">
                                                <span class="badge bg-warning text-dark">
                                                    <?php echo htmlspecialchars($student['house_points']); ?> pts
                                                </span>
                                            </td>
                                            <td class="attendance-column" data-label="Attendance Points">
                                                <span class="badge bg-success">
                                                    <?php echo htmlspecialchars($student['attendance_points']); ?> pts
                                                </span>
                                            </td>
                                            <td data-label="Appreciations">
                                                <?php if (!empty($student['appreciations_details'])): ?>
                                                    <div class="appreciation-details">
                                                        <?php foreach ($student['appreciations_details'] as $appreciation): ?>
                                                            <div class="mb-1">
                                                                <small class="text-success">
                                                                    <?php
                                                                    // Different icons for different sources
                                                                    $icon = 'fas fa-star';
                                                                    $badge_class = 'badge bg-success';
                                                                    if ($appreciation['source'] == 'participant') {
                                                                        $icon = 'fas fa-user-check';
                                                                        $badge_class = 'badge bg-primary';
                                                                    } elseif ($appreciation['source'] == 'organizer') {
                                                                        $icon = 'fas fa-users-cog';
                                                                        $badge_class = 'badge bg-info';
                                                                    }
                                                                    ?>
                                                                    <span class="<?php echo $badge_class; ?>" style="font-size: 0.7rem; margin-right: 3px;">
                                                                        <?php echo ucfirst($appreciation['source']); ?>
                                                                    </span>
                                                                    <i class="<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($appreciation['event_title']); ?>
                                                                    (<?php echo htmlspecialchars($appreciation['points']); ?> pts)
                                                                    <?php if ($appreciation['reason']): ?>
                                                                        - <?php echo htmlspecialchars($appreciation['reason']); ?>
                                                                    <?php endif; ?>
                                                                </small>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No points earned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Penalties">
                                                <?php if (!empty($student['penalties_details'])): ?>
                                                    <div class="penalty-details">
                                                        <?php foreach ($student['penalties_details'] as $penalty): ?>
                                                            <div class="mb-1">
                                                                <small class="text-danger">
                                                                    <i class="fas fa-minus-circle"></i> <?php echo htmlspecialchars($penalty['event_title']); ?>
                                                                    (<?php echo htmlspecialchars($penalty['points']); ?> pts)
                                                                    <?php if ($penalty['reason']): ?>
                                                                        - <?php echo htmlspecialchars($penalty['reason']); ?>
                                                                    <?php endif; ?>
                                                                </small>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No penalties</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate" style="font-size: 4rem; color: var(--gray-medium); margin-bottom: 20px;"></i>
                    <h4 class="text-muted">No students found</h4>
                    <p class="text-muted">This section doesn't have any students yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <style>
        .info-item {
            padding: 15px;
        }

        .info-item h4 {
            margin: 10px 0;
            font-weight: 600;
        }

        .table th {
            font-weight: 600;
        }

        .badge {
            font-size: 0.8rem;
        }

        .appreciation-details, .penalty-details {
            max-height: 100px;
            overflow-y: auto;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.9rem;
            }

            #housePointsTable thead {
                display: none;
            }

            #housePointsTable tbody tr {
                display: block;
                border: 1px solid #ddd;
                margin-bottom: 15px;
                padding: 10px;
                border-radius: 5px;
                background: #f8f9fa;
            }

            #housePointsTable tbody td {
                display: block;
                text-align: left;
                border: none;
                border-bottom: 1px solid #eee;
                padding: 5px 0;
                background: transparent;
            }

            #housePointsTable tbody td:last-child {
                border-bottom: none;
            }

            #housePointsTable tbody td:before {
                content: attr(data-label) ": ";
                font-weight: bold;
                color: #495057;
                display: inline-block;
                min-width: 140px;
            }

            .appreciation-details, .penalty-details {
                max-height: none;
                overflow: visible;
                font-size: 0.8rem;
            }
        }

        @media print {
            /* Hide everything except the table */
            body > *:not(.main-content) {
                display: none !important;
            }

            .main-content > .container > .card:first-child {
                display: none !important;
            }

            .main-content > .container > .card:nth-child(2) {
                display: block !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-body > .form-check {
                display: none !important;
            }

            .card-header {
                display: none !important;
            }

            .table-responsive {
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .table {
                font-size: 12px !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .badge {
                border: 1px solid #000 !important;
                background: white !important;
                color: black !important;
            }

            .appreciation-details, .penalty-details {
                max-height: none !important;
                overflow: visible !important;
                font-size: 10px !important;
            }

            /* Hide attendance column if checkbox is unchecked */
            .attendance-column[style*="display: none"] {
                display: none !important;
            }
        }
    </style>

    <script>
        function downloadPDF() {
            // Hide attendance points column if hidden before printing
            const attendanceVisible = document.getElementById('toggleAttendance').checked;
            const attendanceCols = document.querySelectorAll('.attendance-column');
            attendanceCols.forEach(col => {
                col.style.display = attendanceVisible ? '' : 'none';
            });
            window.print();
        }

        function toggleAttendancePoints() {
            const checkbox = document.getElementById('toggleAttendance');
            const attendanceCols = document.querySelectorAll('.attendance-column');
            attendanceCols.forEach(col => {
                col.style.display = checkbox.checked ? '' : 'none';
            });
        }
    </script>
</body>
</html>
