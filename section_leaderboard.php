<?php
session_start();
include './connect.php';
include './db_migration_helper.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$db_helper = new DatabaseMigrationHelper($conn);

// Get selected section from URL parameter
$selected_class_id = $_GET['class_id'] ?? null;

// Get all classes/sections for dropdown
$all_classes = $db_helper->getAllClasses();

// Initialize variables
$selected_section_data = null;
$sections_leaderboard = [];

if ($selected_class_id) {
    // Get specific section data
    $class_query = "SELECT * FROM classes WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $class_query);
    mysqli_stmt_bind_param($stmt, "i", $selected_class_id);
    mysqli_stmt_execute($stmt);
    $class_result = mysqli_stmt_get_result($stmt);
    $class_data = mysqli_fetch_assoc($class_result);
    
    if ($class_data) {
        // Get students in this specific class
        $students_query = "
            SELECT s.student_id, s.name, h.name as house_name,
                   s.branch, s.section
            FROM students s
            LEFT JOIN houses h ON s.hid = h.hid
            WHERE s.class_id = ?
            ORDER BY s.student_id ASC
        ";
        $stmt = mysqli_prepare($conn, $students_query);
        mysqli_stmt_bind_param($stmt, "i", $selected_class_id);
        mysqli_stmt_execute($stmt);
        $students_result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($student = mysqli_fetch_assoc($students_result)) {
            // Calculate total house points from all sources
            $house_points = 0;

            // Points from participants
            $participants_query = "SELECT SUM(points) as points FROM participants WHERE student_id = ?";
            $participants_stmt = mysqli_prepare($conn, $participants_query);
            mysqli_stmt_bind_param($participants_stmt, "s", $student['student_id']);
            mysqli_stmt_execute($participants_stmt);
            $participants_result = mysqli_stmt_get_result($participants_stmt);
            $participants_data = mysqli_fetch_assoc($participants_result);
            $house_points += (int)($participants_data['points'] ?? 0);

            // Points from winners
            $winners_query = "SELECT SUM(points) as points FROM winners WHERE student_id = ?";
            $winners_stmt = mysqli_prepare($conn, $winners_query);
            mysqli_stmt_bind_param($winners_stmt, "s", $student['student_id']);
            mysqli_stmt_execute($winners_stmt);
            $winners_result = mysqli_stmt_get_result($winners_stmt);
            $winners_data = mysqli_fetch_assoc($winners_result);
            $house_points += (int)($winners_data['points'] ?? 0);

            // Points from organizers
            $organizers_query = "SELECT SUM(points) as points FROM organizers WHERE student_id = ?";
            $organizers_stmt = mysqli_prepare($conn, $organizers_query);
            mysqli_stmt_bind_param($organizers_stmt, "s", $student['student_id']);
            mysqli_stmt_execute($organizers_stmt);
            $organizers_result = mysqli_stmt_get_result($organizers_stmt);
            $organizers_data = mysqli_fetch_assoc($organizers_result);
            $house_points += (int)($organizers_data['points'] ?? 0);

            // Points from appreciations
            $appreciations_query = "SELECT SUM(points) as points FROM appreciations WHERE student_id = ?";
            $appreciations_stmt = mysqli_prepare($conn, $appreciations_query);
            mysqli_stmt_bind_param($appreciations_stmt, "s", $student['student_id']);
            mysqli_stmt_execute($appreciations_stmt);
            $appreciations_result = mysqli_stmt_get_result($appreciations_stmt);
            $appreciations_data = mysqli_fetch_assoc($appreciations_result);
            $house_points += (int)($appreciations_data['points'] ?? 0);

            // Get attendance stats and convert percentage to points (round to whole number)
            $attendance_stats = $db_helper->getStudentAttendanceStats($student['student_id']);
            $attendance_percentage = $attendance_stats['attendance_percentage'] ?? 0;
            $attendance_points = round($attendance_percentage); // Round to whole number
            $house_points += $attendance_points; // Add attendance points to total house points

            // Subtract penalties
            $penalties_query = "SELECT SUM(points) as points FROM penalties WHERE student_id = ?";
            $penalties_stmt = mysqli_prepare($conn, $penalties_query);
            mysqli_stmt_bind_param($penalties_stmt, "s", $student['student_id']);
            mysqli_stmt_execute($penalties_stmt);
            $penalties_result = mysqli_stmt_get_result($penalties_stmt);
            $penalties_data = mysqli_fetch_assoc($penalties_result);
            $house_points -= (int)($penalties_data['points'] ?? 0);

            $student['house_points'] = max(0, $house_points); // Ensure non-negative
            $student['attendance_points'] = $attendance_points; // Store separately for display
            $students[] = $student;
        }
        
        // Sort students by house points (descending) and assign ranks
        usort($students, function($a, $b) {
            return $b['house_points'] - $a['house_points'];
        });
        
        // Assign perfect ascending ranks (1, 2, 3, 4...) based on points from highest to lowest
        $rank = 1;
        $last_points = null;
        $students_with_same_points = 0;
        
        foreach ($students as $index => &$student) {
            if ($last_points !== null && $student['house_points'] != $last_points) {
                // Move to next rank, accounting for tied students
                $rank += $students_with_same_points;
                $students_with_same_points = 1;
            } else {
                $students_with_same_points++;
            }
            
            $student['rank'] = $rank;
            $last_points = $student['house_points'];
        }
        
        $selected_section_data = [
            'class_id' => $selected_class_id,
            'section_name' => $all_classes[$selected_class_id],
            'class_data' => $class_data,
            'students' => $students,
            'student_count' => count($students)
        ];
    }
} else {
    // Get all sections data for overview
    foreach ($all_classes as $class_id => $display_name) {
        $class_query = "SELECT * FROM classes WHERE class_id = ?";
        $stmt = mysqli_prepare($conn, $class_query);
        mysqli_stmt_bind_param($stmt, "i", $class_id);
        mysqli_stmt_execute($stmt);
        $class_result = mysqli_stmt_get_result($stmt);
        $class_data = mysqli_fetch_assoc($class_result);
        
        if (!$class_data) continue;
        
        $student_count_query = "SELECT COUNT(*) as count FROM students WHERE class_id = ?";
        $stmt = mysqli_prepare($conn, $student_count_query);
        mysqli_stmt_bind_param($stmt, "i", $class_id);
        mysqli_stmt_execute($stmt);
        $count_result = mysqli_stmt_get_result($stmt);
        $count_data = mysqli_fetch_assoc($count_result);
        
        $sections_leaderboard[] = [
            'class_id' => $class_id,
            'section_name' => $display_name,
            'class_data' => $class_data,
            'student_count' => $count_data['count'] ?? 0
        ];
    }
    
    // Sort sections by year, then branch, then section
    usort($sections_leaderboard, function($a, $b) {
        if ($a['class_data']['year'] != $b['class_data']['year']) {
            return $a['class_data']['year'] - $b['class_data']['year'];
        }
        if ($a['class_data']['branch'] != $b['class_data']['branch']) {
            return strcmp($a['class_data']['branch'], $b['class_data']['branch']);
        }
        return strcmp($a['class_data']['section'], $b['class_data']['section']);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title>Section Leaderboards - House Points</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root {
            --primary-blue: #076593;
            --light-blue: #e3f2fd;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --gray-light: #f8f9fa;
            --gray-medium: #6c757d;
            --white: #ffffff;
        }

        body {
            background: linear-gradient(135deg, var(--light-blue) 0%, #ffffff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .section-selector {
            background: var(--white);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(7,101,147,0.1);
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .section-card {
            background: var(--white);
            border: 2px solid #e3e6f0;
            border-radius: 15px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .section-card:hover {
            border-color: var(--primary-blue);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(7,101,147,0.15);
            text-decoration: none;
            color: inherit;
        }

        .section-card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin: 0;
        }

        .section-card-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--gray-medium);
        }

        .leaderboard-card {
            background: var(--white);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(7,101,147,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #0891c7 100%);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-stats {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .rank-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: white;
        }

        .rank-1 { background: linear-gradient(135deg, #ffd700, #ffed4e); color: #b8860b; }
        .rank-2 { background: linear-gradient(135deg, #c0c0c0, #e5e5e5); color: #666; }
        .rank-3 { background: linear-gradient(135deg, #cd7f32, #daa520); color: white; }
        .rank-other { background: linear-gradient(135deg, var(--info), #20c997); }
        .rank-low { background: linear-gradient(135deg, var(--gray-medium), #95a5a6); }

        .student-row {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-row:hover {
            background-color: var(--gray-light);
        }

        .student-row:last-child {
            border-bottom: none;
        }

        .student-info {
            flex: 1;
        }

        .student-name {
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.25rem;
        }

        .student-id {
            color: var(--gray-medium);
            font-size: 0.9rem;
        }

        .house-badge {
            padding: 0.25rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .points-display {
            text-align: right;
            min-width: 80px;
        }

        .points-number {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--success);
        }

        .points-label {
            font-size: 0.8rem;
            color: var(--gray-medium);
        }

        .download-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, var(--primary-blue), #0891c7);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(7,101,147,0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(7,101,147,0.4);
        }

        @media print {
            body { background: white; }
            .download-btn, .section-selector { display: none; }
            .leaderboard-card { 
                page-break-inside: avoid; 
                margin-bottom: 1rem;
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>

    <div class="page-title">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-trophy"></i> Section Leaderboards</h2>
                    <p><?php echo $selected_section_data ? 'Student rankings for ' . htmlspecialchars($selected_section_data['section_name']) : 'Select a section to view detailed leaderboard'; ?></p>
                </div>
                <div>
                    <a href="sections_overview.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sections
                    </a>
                    <?php if ($selected_section_data): ?>
                        <a href="section_leaderboard.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-list"></i> All Sections
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <?php if (!$selected_section_data): ?>
                <!-- Section Selection -->
                <div class="section-selector">
                    <h4 class="mb-0"><i class="fas fa-mouse-pointer"></i> Select a Section</h4>
                    <p class="text-muted mb-0">Choose a section to view detailed student leaderboard with house points</p>
                    
                    <div class="section-grid">
                        <?php foreach ($sections_leaderboard as $section): ?>
                            <a href="?class_id=<?php echo $section['class_id']; ?>" class="section-card">
                                <div class="section-card-header">
                                    <h5 class="section-card-title"><?php echo htmlspecialchars($section['section_name']); ?></h5>
                                </div>
                                <div class="section-card-stats">
                                    <span><i class="fas fa-users"></i> <?php echo $section['student_count']; ?> Students</span>
                                    <span><i class="fas fa-calendar"></i> Year <?php echo $section['class_data']['year']; ?></span>
                                </div>
                                <div class="section-card-stats mt-2">
                                    <span><i class="fas fa-code-branch"></i> <?php echo $section['class_data']['branch']; ?></span>
                                    <span><i class="fas fa-layer-group"></i> Section <?php echo strtoupper($section['class_data']['section']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Selected Section Leaderboard -->
                <div class="leaderboard-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-users"></i>
                            <?php echo htmlspecialchars($selected_section_data['section_name']); ?>
                        </h3>
                        <div class="section-stats">
                            <span><i class="fas fa-user-graduate"></i> <?php echo $selected_section_data['student_count']; ?> Students</span>
                            <span><i class="fas fa-calendar"></i> Year <?php echo $selected_section_data['class_data']['year']; ?></span>
                            <span><i class="fas fa-code-branch"></i> <?php echo $selected_section_data['class_data']['branch']; ?></span>
                            <span><i class="fas fa-layer-group"></i> Section <?php echo strtoupper($selected_section_data['class_data']['section']); ?></span>
                        </div>
                    </div>
                    
                    <div class="leaderboard-body">
                        <?php if (!empty($selected_section_data['students'])): ?>
                            <?php foreach ($selected_section_data['students'] as $student): ?>
                                <div class="student-row">
                                    <div class="rank-badge <?php 
                                        if ($student['house_points'] >= 50) {
                                            if ($student['rank'] == 1) echo 'rank-1';
                                            elseif ($student['rank'] == 2) echo 'rank-2';
                                            elseif ($student['rank'] == 3) echo 'rank-3';
                                            else echo 'rank-other';
                                        } else {
                                            echo 'rank-low';
                                        }
                                    ?>">
                                        <?php echo $student['rank']; ?>
                                    </div>
                                    
                                    <div class="student-info">
                                        <div class="student-name"><?php echo htmlspecialchars($student['name']); ?></div>
                                        <div class="student-id"><?php echo htmlspecialchars($student['student_id']); ?></div>
                                    </div>
                                    
                                    <?php if (!empty($student['house_name'])): ?>
                                        <div class="house-badge bg-info text-white">
                                            <?php echo htmlspecialchars($student['house_name']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="points-display">
                                        <div class="points-number"><?php echo $student['house_points']; ?></div>
                                        <div class="points-label">points</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-graduate" style="font-size: 3rem; color: var(--gray-medium); margin-bottom: 1rem;"></i>
                                <h5 class="text-muted">No students found</h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($selected_section_data): ?>
        <!-- Download Options Dropdown -->
        <div class="dropdown" style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
            <button class="download-btn dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="downloadDropdown" style="border-radius: 10px; border: none; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                <li>
                    <a class="dropdown-item" href="#" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf text-danger"></i> Download PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="exportToExcel()">
                        <i class="fas fa-file-excel text-success"></i> Export Excel
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>

    <?php include "footer.php"; ?>

    <script>
        <?php if ($selected_section_data): ?>
        // Excel export function
        function exportToExcel() {
            // Prepare student data for Excel export
            const studentsData = [];
            <?php if (!empty($selected_section_data['students'])): ?>
                <?php foreach ($selected_section_data['students'] as $index => $student): ?>
                    studentsData.push({
                        'Rank': <?php echo $student['rank']; ?>,
                        'S.No': <?php echo $index + 1; ?>,
                        'Registration Number': '<?php echo addslashes($student['student_id']); ?>',
                        'Student Name': '<?php echo addslashes($student['name']); ?>',
                        'House': '<?php echo addslashes($student['house_name'] ?? 'Not Assigned'); ?>',
                        'House Points': <?php echo intval($student['house_points'] - $student['attendance_points']); ?>,
                        'Attendance Points': <?php echo $student['attendance_points']; ?>,
                        'Total Points': <?php echo $student['house_points']; ?>,
                        'Performance Level': <?php echo $student['house_points'] >= 80 ? "'Excellent'" : ($student['house_points'] >= 60 ? "'Good'" : ($student['house_points'] >= 40 ? "'Average'" : "'Needs Improvement'")); ?>
                    });
                <?php endforeach; ?>
            <?php endif; ?>

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(studentsData);

            // Set column widths for better formatting
            const columnWidths = [
                { wch: 6 },   // Rank
                { wch: 6 },   // S.No
                { wch: 18 },  // Registration Number
                { wch: 30 },  // Student Name
                { wch: 15 },  // House
                { wch: 12 },  // House Points
                { wch: 15 },  // Attendance Points
                { wch: 12 },  // Total Points
                { wch: 18 }   // Performance Level
            ];
            ws['!cols'] = columnWidths;

            // Add styling to headers
            const headerCells = ['A1', 'B1', 'C1', 'D1', 'E1', 'F1', 'G1', 'H1', 'I1'];
            headerCells.forEach(cell => {
                if (ws[cell]) {
                    ws[cell].s = {
                        font: { bold: true, color: { rgb: "FFFFFF" } },
                        fill: { fgColor: { rgb: "076593" } },
                        alignment: { horizontal: "center", vertical: "center" }
                    };
                }
            });

            // Color code rows based on performance and rank
            for (let i = 2; i <= studentsData.length + 1; i++) {
                const rankCell = `A${i}`;
                const totalPointsCell = `H${i}`;
                const performanceCell = `I${i}`;
                
                if (ws[rankCell] && ws[totalPointsCell]) {
                    const totalPoints = ws[totalPointsCell].v;
                    const rank = ws[rankCell].v;
                    
                    let fillColor = "FFFFFF"; // Default white
                    let fontColor = "000000"; // Default black
                    
                    // Color coding based on performance
                    if (totalPoints >= 80) {
                        fillColor = "D4F6D4"; // Light green for excellent
                        fontColor = "2D5016"; // Dark green text
                    } else if (totalPoints >= 60) {
                        fillColor = "FFF2CC"; // Light yellow for good
                        fontColor = "7A6000"; // Dark yellow text
                    } else if (totalPoints >= 40) {
                        fillColor = "FFE6CC"; // Light orange for average
                        fontColor = "B45F06"; // Dark orange text
                    } else {
                        fillColor = "FFCCCC"; // Light red for needs improvement
                        fontColor = "CC0000"; // Dark red text
                    }
                    
                    // Special styling for top 3 ranks
                    if (rank <= 3) {
                        // Apply rank cell styling with gold, silver, bronze
                        let rankFillColor = fillColor;
                        if (rank === 1) rankFillColor = "FFD700"; // Gold
                        else if (rank === 2) rankFillColor = "C0C0C0"; // Silver
                        else if (rank === 3) rankFillColor = "CD7F32"; // Bronze
                        
                        ws[rankCell].s = {
                            fill: { fgColor: { rgb: rankFillColor } },
                            font: { bold: true, color: { rgb: "000000" } },
                            alignment: { horizontal: "center", vertical: "center" }
                        };
                    } else {
                        ws[rankCell].s = {
                            fill: { fgColor: { rgb: fillColor } },
                            font: { color: { rgb: fontColor } },
                            alignment: { horizontal: "center", vertical: "center" }
                        };
                    }

                    // Apply performance styling to total points and performance level
                    ws[totalPointsCell].s = {
                        fill: { fgColor: { rgb: fillColor } },
                        font: { bold: true, color: { rgb: fontColor } },
                        alignment: { horizontal: "center", vertical: "center" }
                    };
                    
                    if (ws[performanceCell]) {
                        ws[performanceCell].s = {
                            fill: { fgColor: { rgb: fillColor } },
                            font: { bold: true, color: { rgb: fontColor } },
                            alignment: { horizontal: "center", vertical: "center" }
                        };
                    }
                }
            }

            // Create summary sheet
            const summaryData = [
                { 'Summary Item': 'Section', 'Value': '<?php echo addslashes($selected_section_data['section_name']); ?>' },
                { 'Summary Item': 'Total Students', 'Value': <?php echo $selected_section_data['student_count']; ?> },
                { 'Summary Item': 'Academic Year', 'Value': '<?php echo $selected_section_data['class_data']['academic_year'] ?? 'N/A'; ?>' },
                { 'Summary Item': 'Year/Branch', 'Value': '<?php echo $selected_section_data['class_data']['year'] . '/4 ' . $selected_section_data['class_data']['branch']; ?>' },
                { 'Summary Item': 'Section Letter', 'Value': '<?php echo strtoupper($selected_section_data['class_data']['section']); ?>' },
                { 'Summary Item': 'Report Generated', 'Value': new Date().toLocaleString() }
            ];

            // Add performance statistics
            const excellentCount = studentsData.filter(s => s['Total Points'] >= 80).length;
            const goodCount = studentsData.filter(s => s['Total Points'] >= 60 && s['Total Points'] < 80).length;
            const averageCount = studentsData.filter(s => s['Total Points'] >= 40 && s['Total Points'] < 60).length;
            const needsImprovementCount = studentsData.filter(s => s['Total Points'] < 40).length;
            const highestScore = studentsData.length > 0 ? Math.max(...studentsData.map(s => s['Total Points'])) : 0;
            const averageScore = studentsData.length > 0 ? Math.round(studentsData.reduce((sum, s) => sum + s['Total Points'], 0) / studentsData.length) : 0;

            summaryData.push(
                { 'Summary Item': '', 'Value': '' }, // Empty row
                { 'Summary Item': 'Performance Statistics', 'Value': '' },
                { 'Summary Item': 'Excellent (80+ points)', 'Value': excellentCount + ' students' },
                { 'Summary Item': 'Good (60-79 points)', 'Value': goodCount + ' students' },
                { 'Summary Item': 'Average (40-59 points)', 'Value': averageCount + ' students' },
                { 'Summary Item': 'Needs Improvement (<40 points)', 'Value': needsImprovementCount + ' students' },
                { 'Summary Item': '', 'Value': '' }, // Empty row
                { 'Summary Item': 'Score Statistics', 'Value': '' },
                { 'Summary Item': 'Highest Score', 'Value': highestScore + ' points' },
                { 'Summary Item': 'Average Score', 'Value': averageScore + ' points' }
            );

            const summaryWs = XLSX.utils.json_to_sheet(summaryData);
            summaryWs['!cols'] = [{ wch: 25 }, { wch: 20 }];

            // Add styling to summary sheet headers
            if (summaryWs['A8']) { // Performance Statistics header
                summaryWs['A8'].s = {
                    font: { bold: true, color: { rgb: "FFFFFF" } },
                    fill: { fgColor: { rgb: "076593" } },
                    alignment: { horizontal: "center", vertical: "center" }
                };
            }
            if (summaryWs['A14']) { // Score Statistics header
                summaryWs['A14'].s = {
                    font: { bold: true, color: { rgb: "FFFFFF" } },
                    fill: { fgColor: { rgb: "076593" } },
                    alignment: { horizontal: "center", vertical: "center" }
                };
            }

            // Add sheets to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Section Leaderboard");
            XLSX.utils.book_append_sheet(wb, summaryWs, "Summary & Statistics");

            // Generate filename with timestamp
            const timestamp = new Date().toISOString().slice(0,16).replace('T', '_').replace(/:/g, '-');
            const filename = `<?php echo addslashes($selected_section_data['section_name']); ?>_Leaderboard_${timestamp}.xlsx`;

            // Save the file
            XLSX.writeFile(wb, filename);
        }

        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // PDF Title
            pdf.setFontSize(20);
            pdf.setTextColor(7, 101, 147);
            pdf.text('Section Leaderboard - House Points', 20, 25);
            
            pdf.setFontSize(16);
            pdf.setTextColor(0, 0, 0);
            pdf.text('<?php echo addslashes($selected_section_data['section_name']); ?>', 20, 40);
            
            pdf.setFontSize(12);
            pdf.setTextColor(100, 100, 100);
            pdf.text('Generated on: ' + new Date().toLocaleDateString(), 20, 50);
            pdf.text('<?php echo $selected_section_data['student_count']; ?> Students | Year <?php echo $selected_section_data['class_data']['year']; ?> | <?php echo $selected_section_data['class_data']['branch']; ?> | Section <?php echo strtoupper($selected_section_data['class_data']['section']); ?>', 20, 60);
            
            let yPosition = 75;
            const pageHeight = pdf.internal.pageSize.height;
            const margin = 20;
            
            // Table Header
            pdf.setFillColor(240, 240, 240);
            pdf.rect(margin, yPosition, 170, 8, 'F');
            
            pdf.setFontSize(10);
            pdf.setTextColor(0, 0, 0);
            pdf.text('Rank', margin + 5, yPosition + 5);
            pdf.text('Student Name', margin + 25, yPosition + 5);
            pdf.text('Student ID', margin + 80, yPosition + 5);
            pdf.text('House', margin + 120, yPosition + 5);
            pdf.text('Points', margin + 155, yPosition + 5);
            
            yPosition += 12;
            
            // Students
            <?php if (!empty($selected_section_data['students'])): ?>
                <?php foreach ($selected_section_data['students'] as $studentIndex => $student): ?>
                    if (yPosition > pageHeight - 20) {
                        pdf.addPage();
                        yPosition = 20;
                    }
                    
                    // Alternate row colors
                    if (<?php echo $studentIndex; ?> % 2 === 0) {
                        pdf.setFillColor(250, 250, 250);
                        pdf.rect(margin, yPosition - 2, 170, 8, 'F');
                    }
                    
                    pdf.setFontSize(9);
                    pdf.setTextColor(0, 0, 0);
                    
                    // Rank with color coding
                    <?php if ($student['house_points'] >= 50): ?>
                        <?php if ($student['rank'] <= 3): ?>
                            pdf.setTextColor(184, 134, 11); // Gold for top 3
                        <?php else: ?>
                            pdf.setTextColor(23, 162, 184); // Blue for others above 50
                        <?php endif; ?>
                    <?php else: ?>
                        pdf.setTextColor(108, 117, 125); // Gray for below 50
                    <?php endif; ?>
                    
                    pdf.text('<?php echo $student['rank']; ?>', margin + 8, yPosition + 3);
                    
                    pdf.setTextColor(0, 0, 0);
                    pdf.text('<?php echo addslashes($student['name']); ?>', margin + 25, yPosition + 3);
                    pdf.text('<?php echo addslashes($student['student_id']); ?>', margin + 80, yPosition + 3);
                    pdf.text('<?php echo addslashes($student['house_name'] ?? '-'); ?>', margin + 120, yPosition + 3);
                    
                    // Points with color
                    <?php if ($student['house_points'] >= 50): ?>
                        pdf.setTextColor(40, 167, 69); // Green for good points
                    <?php else: ?>
                        pdf.setTextColor(220, 53, 69); // Red for low points
                    <?php endif; ?>
                    pdf.text('<?php echo $student['house_points']; ?>', margin + 155, yPosition + 3);
                    
                    yPosition += 8;
                <?php endforeach; ?>
            <?php endif; ?>
            
            // Save the PDF
            pdf.save('<?php echo addslashes($selected_section_data['section_name']); ?>_Leaderboard_' + new Date().toISOString().slice(0,10) + '.pdf');
        }
        <?php endif; ?>

        // Add smooth scrolling for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.section-card, .leaderboard-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });
        });
    </script>
</body>
</html>