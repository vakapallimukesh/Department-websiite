<?php
session_start();
if (!isset($_SESSION['hod_logged_in']) || !$_SESSION['hod_logged_in']) {
    header('Location: login.php');
    exit();
}
include './connect.php';
include './db_migration_helper.php';

$section_key = $_GET['section'] ?? '';
if (empty($section_key)) {
    header('Location: hod_dashboard.php');
    exit();
}

// Parse section key
list($year, $branch, $section) = array_pad(explode('_', $section_key), 3, '');

// Get section data with students, skills, and attendance points
$query = "
    SELECT 
        s.student_id,
        s.name,
        s.email,
        sp.skills,
        (
            SELECT COUNT(*)
            FROM student_attendance sa 
            WHERE sa.student_id = s.student_id 
            AND sa.status = 'Present'
        ) as attendance_points
    FROM classes c
    LEFT JOIN students s ON s.class_id = c.class_id
    LEFT JOIN student_profile sp ON s.student_id = sp.student_id
    WHERE c.year = ? AND c.branch = ? AND c.section = ?
    ORDER BY s.name
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iss", $year, $branch, $section);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$students = [];
$all_skills = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Extract skills and add to all_skills array
    $skills = !empty($row['skills']) ? array_map('trim', explode(',', $row['skills'])) : [];
    foreach ($skills as $skill) {
        if (!empty($skill) && !in_array($skill, $all_skills)) {
            $all_skills[] = $skill;
        }
    }

    // Format registration number
    $reg_no = str_replace('@srkrec.edu.in', '', $row['email']);

    $students[] = [
        'student_id' => $row['student_id'],
        'name' => $row['name'],
        'reg_no' => $reg_no,
        'skills' => implode(', ', $skills),
        'attendance_points' => (int)$row['attendance_points']
    ];
}

// Sort skills alphabetically
sort($all_skills);
?>
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title><?php echo "$year/4 $branch-$section"; ?> - SRKR Engineering College</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --accent-cyan: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        }
        
        body {
            background-color: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Header */
        .section-view-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 45px 0 65px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(99, 102, 241, 0.2);
            margin-bottom: -35px;
        }

        .section-view-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99, 102, 241, 0.25) 0%, transparent 60%),
                        radial-gradient(circle at 70% 60%, rgba(236, 72, 153, 0.2) 0%, transparent 55%);
            animation: pulseGlow 8s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            100% { transform: scale(1.1) rotate(3deg); opacity: 1; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .hero-title i {
            color: #818cf8;
        }

        .back-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 50px !important;
            padding: 10px 24px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            text-decoration: none !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease !important;
        }

        .back-btn-pill:hover {
            background: #ffffff !important;
            color: #0f172a !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.25) !important;
        }

        .full-width-container {
            width: 100%;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        @media (max-width: 768px) {
            .full-width-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .hero-title {
                font-size: 1.8rem;
            }
        }
        
        /* Controls Card */
        .controls-card-fancy {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            margin-bottom: 35px;
            overflow: hidden;
        }
        
        .controls-header-fancy {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 26px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .controls-header-fancy i {
            color: #4f46e5;
        }

        .search-box-wrap {
            position: relative;
        }
        
        .search-box-wrap input {
            padding: 14px 20px 14px 50px;
            border-radius: 50px;
            border: 1.5px solid #cbd5e1;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .search-box-wrap input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        
        .search-box-wrap i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            font-size: 1.2rem;
        }
        
        .skill-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .skill-filter-btn {
            padding: 8px 18px;
            border-radius: 50px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .skill-filter-btn:hover {
            background: #e0e7ff;
            border-color: #818cf8;
            color: #4f46e5;
        }
        
        .skill-filter-btn.active {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }
        
        /* Students Table Box */
        .students-card-fancy {
            background: white;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .students-table {
            margin: 0;
        }
        
        .students-table th {
            background: #f8fafc !important;
            color: #4f46e5 !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 18px 20px !important;
        }
        
        .students-table td {
            padding: 18px 20px !important;
            border-color: #f1f5f9 !important;
            vertical-align: middle !important;
            font-weight: 500;
        }
        
        .students-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .students-table tbody tr:hover {
            background: rgba(99, 102, 241, 0.04) !important;
        }
        
        .skill-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 50px;
            background: linear-gradient(135deg, #e0e7ff 0%, #eef2ff 100%);
            border: 1px solid #c7d2fe;
            color: #4f46e5;
            font-size: 0.8rem;
            font-weight: 700;
            margin: 3px;
        }
        
        .points-badge {
            padding: 6px 16px;
            border-radius: 50px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 800;
            font-size: 0.88rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            display: inline-block;
        }
        
        .sort-btn {
            background: #e0e7ff;
            border: none;
            color: #4f46e5;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 6px;
            transition: all 0.3s ease;
        }
        
        .sort-btn:hover {
            transform: scale(1.15);
            background: #4f46e5;
            color: white;
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Header -->
    <div class="section-view-hero mb-5">
        <div class="full-width-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="hero-title">
                        <i class="fas fa-users"></i> 
                        <?php echo "$year/4 $branch" . ($section ? "-$section" : ""); ?> Roster
                    </h1>
                    <p class="text-slate mb-0" style="color: #94a3b8; font-weight: 500;">Section Student List, Skill Badges & Attendance Summary</p>
                </div>
                <div>
                    <a href="hod_dashboard.php" class="back-btn-pill">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">
            <!-- Search and Filter Controls -->
            <div class="controls-card-fancy">
                <div class="controls-header-fancy">
                    <i class="fas fa-filter"></i> Search & Skill Filtering Options
                </div>
                <div class="p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-5">
                            <div class="search-box-wrap">
                                <i class="fas fa-search"></i>
                                <input type="text" id="studentSearch" class="form-control" 
                                       placeholder="Search student by name or reg. number...">
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="skill-filters">
                                <?php foreach ($all_skills as $skill): ?>
                                    <button class="skill-filter-btn" data-skill="<?php echo htmlspecialchars($skill); ?>">
                                        <?php echo htmlspecialchars($skill); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Students Table -->
            <div class="students-card-fancy">
                <div class="table-responsive">
                    <table class="table students-table">
                        <thead>
                            <tr>
                                <th>Registration No</th>
                                <th>Student Name</th>
                                <th>
                                    Attendance Points
                                    <button class="sort-btn" data-sort="points" title="Sort by Points">
                                        <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>Acquired Skills</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="student-row" 
                                    data-name="<?php echo strtolower(htmlspecialchars($student['name'])); ?>"
                                    data-regno="<?php echo strtolower(htmlspecialchars($student['reg_no'])); ?>"
                                    data-skills="<?php echo strtolower(htmlspecialchars($student['skills'])); ?>">
                                    <td class="fw-bold" style="color: #475569;"><?php echo htmlspecialchars($student['reg_no']); ?></td>
                                    <td class="fw-bold" style="color: #0f172a;"><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td data-points="<?php echo $student['attendance_points']; ?>">
                                        <span class="points-badge">
                                            <i class="fas fa-check-circle me-1"></i> <?php echo $student['attendance_points']; ?> pts
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $skills = array_filter(explode(',', $student['skills']));
                                        if (!empty($skills)) {
                                            foreach ($skills as $skill) {
                                                echo '<span class="skill-badge">' . htmlspecialchars(trim($skill)) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted small">No skills listed</span>';
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
    </div>
    
    <?php include "footer.php"; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Skill filter handling
        const skillButtons = document.querySelectorAll('.skill-filter-btn');
        skillButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.classList.toggle('active');
                filterStudents();
            });
        });
        
        // Search handling
        const searchInput = document.getElementById('studentSearch');
        searchInput.addEventListener('input', filterStudents);
        
        // Combined filter function
        function filterStudents() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeSkills = Array.from(document.querySelectorAll('.skill-filter-btn.active'))
                .map(btn => btn.getAttribute('data-skill').toLowerCase());
            
            const rows = document.querySelectorAll('.student-row');
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const regno = row.getAttribute('data-regno');
                const skills = row.getAttribute('data-skills').split(',').map(s => s.trim().toLowerCase());
                
                // Search filter
                const matchesSearch = searchTerm === '' || 
                    name.includes(searchTerm) || 
                    regno.includes(searchTerm);
                
                // Skills filter
                const matchesSkills = activeSkills.length === 0 || 
                    activeSkills.every(skill => skills.includes(skill));
                
                // Show/hide row based on combined filters
                row.style.display = (matchesSearch && matchesSkills) ? '' : 'none';
            });
        }
        
        // Sorting functionality
        document.querySelector('.sort-btn').addEventListener('click', function() {
            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aPoints = parseInt(a.querySelector('td[data-points]').getAttribute('data-points'));
                const bPoints = parseInt(b.querySelector('td[data-points]').getAttribute('data-points'));
                return this.classList.contains('asc') ? aPoints - bPoints : bPoints - aPoints;
            });
            
            this.classList.toggle('asc');
            const icon = this.querySelector('i');
            icon.className = this.classList.contains('asc') ? 'fas fa-sort-up' : 'fas fa-sort-down';
            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        });
    });
    </script>
</body>
</html>