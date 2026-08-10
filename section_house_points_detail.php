<?php
// Disable error display in browser to prevent session warnings from cluttering page UI
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
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

if ($class_data['year'] >= 5) {
    $section_name = 'Graduated Batch';
} else {
    $section_name = $class_data['year'] . '/4 ' . strtoupper($class_data['branch']) . '-' . strtoupper($class_data['section']);
}

// Get all students
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

        // Add penalties
        $penalties_query = "SELECT COALESCE(SUM(points), 0) as points FROM penalties WHERE student_id = '" . mysqli_real_escape_string($conn, $student['student_id']) . "'";
        $penalties_result = mysqli_query($conn, $penalties_query);
        if ($penalties_result) {
            $penalties_data = mysqli_fetch_assoc($penalties_result);
            $house_points += (int)($penalties_data['points'] ?? 0);
        }

        // Attendance stats
        $attendance_stats = $db_helper->getStudentAttendanceStats($student['student_id']);
        $attendance_percentage = $attendance_stats['attendance_percentage'] ?? 0;
        $attendance_points = round($attendance_percentage);

        // Appreciations detail
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

        // Penalties detail
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
    <style>
        /* Clean Normal Light Theme for Section Detail Page */
        body {
            background: #f8fafc !important;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #1e293b !important;
        }

        .page-title {
            background: #ffffff !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
            padding: 30px 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .page-title h2 {
            color: #0f172a !important;
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 6px;
        }

        .page-title h2 i {
            color: #8B4513 !important;
        }

        .page-title p {
            color: #64748b !important;
            font-weight: 500;
            margin: 0;
        }

        .btn-back-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #8B4513 !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 10px 24px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            text-decoration: none !important;
            box-shadow: 0 4px 14px rgba(139, 69, 19, 0.25) !important;
            transition: all 0.3s ease !important;
        }

        .btn-back-pill:hover {
            background: #70370f !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.35) !important;
            color: #ffffff !important;
        }

        .btn-back-pill i {
            transition: transform 0.3s ease;
        }

        .btn-back-pill:hover i {
            transform: translateX(-4px);
        }

        /* Clean Container Cards */
        .card-custom {
            background: #ffffff !important;
            border-radius: 18px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden;
        }

        .info-card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 18px 24px;
        }

        .info-card-header h5 {
            color: #0f172a !important;
            font-weight: 800;
        }

        .info-card-header h5 i {
            color: #8B4513 !important;
            margin-right: 8px;
        }

        .graphic-stat-box {
            padding: 20px;
            border-radius: 16px;
            background: #f8fafc !important;
            border: 1.5px solid #e2e8f0 !important;
            transition: all 0.3s ease;
        }

        .graphic-stat-box:hover {
            transform: translateY(-4px);
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .graphic-icon-wrap {
            width: 52px;
            height: 52px;
            margin: 0 auto 12px auto;
            border-radius: 14px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease;
        }

        .graphic-stat-box:hover .graphic-icon-wrap {
            transform: scale(1.1);
        }

        .graphic-icon-wrap i {
            font-size: 1.5rem;
            color: #8B4513;
        }

        .graphic-stat-box h4 {
            color: #0f172a;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .graphic-stat-box p {
            color: #8B4513;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .table-card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 18px 24px;
        }

        .table-card-header h5 {
            color: #0f172a !important;
            font-weight: 800;
        }

        .table-card-header h5 i {
            color: #8B4513 !important;
            margin-right: 8px;
        }

        .btn-download-pdf {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9 !important;
            color: #8B4513 !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 50px !important;
            padding: 8px 20px !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            transition: all 0.3s ease !important;
            cursor: pointer;
        }

        .btn-download-pdf:hover {
            background: #8B4513 !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: 0 4px 14px rgba(139, 69, 19, 0.3) !important;
        }

        .form-check-input:checked {
            background-color: #8B4513 !important;
            border-color: #8B4513 !important;
        }

        .form-check-label {
            color: #0f172a !important;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .table-responsive {
            background: transparent !important;
            border: none !important;
        }

        .table {
            background: transparent !important;
            color: #1e293b !important;
        }

        .table th {
            background: #f1f5f9 !important;
            color: #8B4513 !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 16px 14px !important;
        }

        .table td {
            background: transparent !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #1e293b !important;
            padding: 14px !important;
            vertical-align: middle !important;
            font-weight: 500;
        }

        .table tbody tr {
            transition: all 0.3s ease !important;
        }

        .table tbody tr:hover {
            background: rgba(139, 69, 19, 0.03) !important;
        }

        /* Graphic House Badges */
        .house-badge-graphic {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .house-badge-aakash {
            background: linear-gradient(135deg, #1e88e5 0%, #4fc3f7 100%);
            color: #ffffff;
        }

        .house-badge-agni {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: #ffffff;
        }

        .house-badge-prudhvi {
            background: linear-gradient(135deg, #8d6e63 0%, #6d4c41 100%);
            color: #ffffff;
        }

        .house-badge-jal {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            color: #ffffff;
        }

        .house-badge-vayu {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: #ffffff;
        }

        .house-badge-default {
            background: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Points Badges */
        .points-badge-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            font-size: 0.82rem !important;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25) !important;
            display: inline-block;
        }

        .points-badge-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            font-size: 0.82rem !important;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25) !important;
            display: inline-block;
        }

        /* Appreciations & Penalties */
        .appreciation-details, .penalty-details {
            max-height: 120px;
            overflow-y: auto;
            font-size: 0.78rem;
        }

        .app-item-pill {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 8px;
            margin-bottom: 4px;
            display: block;
        }

        .pen-item-pill {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            padding: 4px 10px;
            border-radius: 8px;
            margin-bottom: 4px;
            display: block;
        }

        @media print {
            body > *:not(.main-content) {
                display: none !important;
            }

            .main-content > .container > .card-custom:first-child {
                display: none !important;
            }

            .main-content > .container > .card-custom:nth-child(2) {
                display: block !important;
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }

            .table-card-header, .form-check {
                display: none !important;
            }

            .table {
                font-size: 12px !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>

    <div class="page-title">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2><i class="fas fa-trophy"></i> <?php echo htmlspecialchars($section_name); ?> - House Points</h2>
                    <p><?php echo $student_count; ?> students in this section</p>
                </div>
                <div>
                    <a href="section_house_points.php" class="btn-back-pill">
                        <i class="fas fa-arrow-left"></i> Back to Sections
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content mb-5">
        <div class="container">
            <!-- Section Info Card -->
            <div class="card-custom mb-4">
                <div class="info-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Section Information
                    </h5>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-3 col-6 text-center">
                            <div class="graphic-stat-box">
                                <div class="graphic-icon-wrap">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h4><?php echo ($class_data['year'] >= 5) ? 'Graduated' : $class_data['year'] . '/4'; ?></h4>
                                <p>Year</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="graphic-stat-box">
                                <div class="graphic-icon-wrap">
                                    <i class="fas fa-code-branch"></i>
                                </div>
                                <h4><?php echo strtoupper($class_data['branch']); ?></h4>
                                <p>Branch</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="graphic-stat-box">
                                <div class="graphic-icon-wrap">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <h4><?php echo strtoupper($class_data['section']); ?></h4>
                                <p>Section</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="graphic-stat-box">
                                <div class="graphic-icon-wrap">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h4><?php echo $class_data['semester']; ?></h4>
                                <p>Semester</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table Card -->
            <?php if (!empty($students)): ?>
                <div class="card-custom">
                    <div class="table-card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Student House Points</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <!-- Toggle Attendance Points -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleAttendance" checked onchange="toggleAttendancePoints()">
                                <label class="form-check-label" for="toggleAttendance">
                                    Show Attendance Points
                                </label>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="housePointsTable">
                                <thead>
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
                                        <?php 
                                        $h_name = strtoupper($student['house_name'] ?? '');
                                        $badge_class = 'house-badge-default';
                                        if (strpos($h_name, 'AAKASH') !== false) $badge_class = 'house-badge-aakash';
                                        elseif (strpos($h_name, 'AGNI') !== false) $badge_class = 'house-badge-agni';
                                        elseif (strpos($h_name, 'PRUDHVI') !== false || strpos($h_name, 'PRUTHVI') !== false) $badge_class = 'house-badge-prudhvi';
                                        elseif (strpos($h_name, 'JAL') !== false) $badge_class = 'house-badge-jal';
                                        elseif (strpos($h_name, 'VAYU') !== false) $badge_class = 'house-badge-vayu';
                                        ?>
                                        <tr>
                                            <td data-label="Registration Number">
                                                <strong><?php echo htmlspecialchars($student['student_id']); ?></strong>
                                            </td>
                                            <td data-label="Student Name"><?php echo htmlspecialchars($student['name']); ?></td>
                                            <td data-label="House">
                                                <?php if ($student['house_name']): ?>
                                                    <span class="house-badge-graphic <?php echo $badge_class; ?>">
                                                        <?php echo htmlspecialchars($student['house_name']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="House Points">
                                                <span class="points-badge-gold">
                                                    <?php echo htmlspecialchars($student['house_points']); ?> pts
                                                </span>
                                            </td>
                                            <td class="attendance-column" data-label="Attendance Points">
                                                <span class="points-badge-green">
                                                    <?php echo htmlspecialchars($student['attendance_points']); ?> pts
                                                </span>
                                            </td>
                                            <td data-label="Appreciations">
                                                <?php if (!empty($student['appreciations_details'])): ?>
                                                    <div class="appreciation-details">
                                                        <?php foreach ($student['appreciations_details'] as $appreciation): ?>
                                                            <div class="app-item-pill">
                                                                <small class="text-success fw-bold">
                                                                    <?php
                                                                    $icon = 'fas fa-star';
                                                                    $source_badge = 'bg-success';
                                                                    if ($appreciation['source'] == 'participant') {
                                                                        $icon = 'fas fa-user-check';
                                                                        $source_badge = 'bg-primary';
                                                                    } elseif ($appreciation['source'] == 'organizer') {
                                                                        $icon = 'fas fa-users-cog';
                                                                        $source_badge = 'bg-info';
                                                                    }
                                                                    ?>
                                                                    <span class="badge <?php echo $source_badge; ?>" style="font-size: 0.68rem; margin-right: 3px;">
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
                                                    <span class="text-muted" style="font-size: 0.85rem;">No points earned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Penalties">
                                                <?php if (!empty($student['penalties_details'])): ?>
                                                    <div class="penalty-details">
                                                        <?php foreach ($student['penalties_details'] as $penalty): ?>
                                                            <div class="pen-item-pill">
                                                                <small class="text-danger fw-bold">
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
                                                    <span class="text-muted" style="font-size: 0.85rem;">No penalties</span>
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
                    <i class="fas fa-user-graduate" style="font-size: 4rem; color: #8B4513; margin-bottom: 20px;"></i>
                    <h4 class="text-muted">No students found</h4>
                    <p class="text-muted">This section doesn't have any students yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        function downloadPDF() {
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
