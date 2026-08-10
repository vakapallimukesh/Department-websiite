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

// Get all classes/sections
$classes = $db_helper->getAllClasses();

// Validate getAllClasses() return
if (!is_array($classes)) {
    die("Error: getAllClasses() did not return a valid array.");
}

// Get section details with student counts
$sections_data = [];
foreach ($classes as $class_id => $display_name) {
    // Get student count for this class
    $student_count_query = "SELECT COUNT(*) as student_count FROM students WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $student_count_query);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $student_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Get class details
    $class_query = "SELECT class_id, year, branch, section, semester, academic_year FROM classes WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $class_query);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $class_id_result, $year, $branch, $section, $semester, $academic_year);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    $class_data = [
        'class_id' => $class_id_result,
        'year' => $year,
        'branch' => $branch,
        'section' => $section,
        'semester' => $semester,
        'academic_year' => $academic_year
    ];

    $sections_data[] = [
        'class_id' => $class_id,
        'display_name' => $display_name,
        'student_count' => $student_count,
        'year' => $class_data['year'],
        'branch' => $class_data['branch'],
        'section' => $class_data['section'],
        'semester' => $class_data['semester'],
        'academic_year' => $class_data['academic_year']
    ];
}

// Sort sections by year, then branch, then section
usort($sections_data, function($a, $b) {
    if ($a['year'] != $b['year']) {
        return $a['year'] - $b['year'];
    }
    if ($a['branch'] != $b['branch']) {
        return strcmp($a['branch'], $b['branch']);
    }
    return strcmp($a['section'], $b['section']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title>Sections House Points - SRKR Engineering College</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --accent-purple: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --accent-blue: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        body {
            background: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Banner */
        .section-hp-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 50px 0 70px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(245, 158, 11, 0.2);
            margin-bottom: -35px;
        }

        .section-hp-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(245, 158, 11, 0.2) 0%, transparent 60%),
                        radial-gradient(circle at 70% 60%, rgba(139, 92, 246, 0.25) 0%, transparent 55%);
            animation: pulseGlow 8s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            100% { transform: scale(1.1) rotate(3deg); opacity: 1; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #fef3c7 50%, #fde68a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .hero-title i {
            color: #fbbf24;
            animation: floatIcon 3s ease-in-out infinite;
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .hero-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            font-weight: 500;
            max-width: 600px;
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
                font-size: 2rem;
            }
        }

        /* Stat Cards */
        .stat-card-custom {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 22px 20px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card-custom::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-gradient, var(--primary-gradient));
            border-radius: 20px 20px 0 0;
        }

        .stat-card-custom:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            background: var(--icon-bg, rgba(245, 158, 11, 0.1));
            color: var(--icon-color, #d97706);
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .stat-card-custom:hover .stat-icon-wrap {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            background: var(--stat-gradient, var(--primary-gradient));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .stat-label-text {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Search Bar Box */
        .search-box-wrap {
            position: relative;
            margin-bottom: 30px;
        }

        .search-input-fancy {
            width: 100%;
            background: #ffffff !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 50px !important;
            padding: 16px 28px 16px 56px !important;
            font-size: 1.05rem !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.04) !important;
            transition: all 0.3s ease !important;
        }

        .search-input-fancy:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.2) !important;
            outline: none;
        }

        .search-box-wrap i {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: #f59e0b;
        }

        /* Section Cards */
        .section-item {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
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

        .section-card-box {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .section-card-box:hover {
            transform: translateY(-10px) scale(1.015);
            box-shadow: 0 25px 50px rgba(245, 158, 11, 0.2);
            border-color: #fbbf24;
        }

        .card-top-banner {
            background: linear-gradient(135deg, #fffbe0 0%, #fef3c7 100%);
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid #fde68a;
            position: relative;
        }

        .trophy-icon-wrap {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 12px auto;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.35);
            transition: transform 0.3s ease;
        }

        .section-card-box:hover .trophy-icon-wrap {
            transform: scale(1.12) rotate(-8deg);
        }

        .card-display-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .section-card-body {
            padding: 20px 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .detail-pills-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 18px;
        }

        .pill-stat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
        }

        .stat-val-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #d97706;
            line-height: 1.2;
        }

        .stat-val-text.blue { color: #2563eb; }

        .stat-lbl-text {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
        }

        .info-bar i {
            color: #d97706;
            margin-right: 8px;
        }

        .btn-view-hp {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 13px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            border: none;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .btn-view-hp i {
            transition: transform 0.3s ease;
        }

        .section-card-box:hover .btn-view-hp {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.45);
            color: #ffffff;
        }

        .section-card-box:hover .btn-view-hp i {
            transform: translateX(6px);
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>

    <!-- Hero Banner -->
    <div class="section-hp-hero mb-5">
        <div class="full-width-container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title"><i class="fas fa-trophy"></i> Sections House Points</h1>
                    <p class="hero-subtitle">Comprehensive house performance leaderboards, section standings, and student contribution tallies.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Full Width Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--primary-gradient); --icon-bg: rgba(245, 158, 11, 0.1); --icon-color: #d97706;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-number"><?php echo count($sections_data); ?></div>
                        <div class="stat-label-text">Total Sections</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-emerald); --icon-bg: rgba(16, 185, 129, 0.1); --icon-color: #10b981;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number"><?php echo array_sum(array_column($sections_data, 'student_count')); ?></div>
                        <div class="stat-label-text">Total Students</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-blue); --icon-bg: rgba(59, 130, 246, 0.1); --icon-color: #3b82f6;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($sections_data, 'branch'))); ?></div>
                        <div class="stat-label-text">Branches</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-purple); --icon-bg: rgba(139, 92, 246, 0.1); --icon-color: #8b5cf6;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($sections_data, 'academic_year'))); ?></div>
                        <div class="stat-label-text">Academic Batches</div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-box-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="search-input-fancy" placeholder="Search sections by branch, year, or section name...">
            </div>

            <!-- Sections Grid -->
            <div class="row g-4" id="sectionsGrid">
                <?php foreach ($sections_data as $index => $section): ?>
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 section-item" 
                         style="animation-delay: <?php echo ($index * 0.05); ?>s;"
                         data-branch="<?php echo htmlspecialchars(strtolower($section['branch'])); ?>" 
                         data-year="<?php echo htmlspecialchars($section['year']); ?>" 
                         data-section="<?php echo htmlspecialchars(strtolower($section['section'])); ?>">
                        <div class="section-card-box" onclick="window.location.href='section_house_points_detail.php?class_id=<?php echo $section['class_id']; ?>'">
                            <div class="card-top-banner">
                                <div class="trophy-icon-wrap">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h3 class="card-display-title"><?php echo htmlspecialchars($section['display_name']); ?></h3>
                            </div>

                            <div class="section-card-body">
                                <div class="detail-pills-row">
                                    <div class="pill-stat-box">
                                        <div class="stat-val-text"><?php echo $section['student_count']; ?></div>
                                        <div class="stat-lbl-text">Students</div>
                                    </div>
                                    <div class="pill-stat-box">
                                        <div class="stat-val-text blue"><?php echo $section['semester']; ?></div>
                                        <div class="stat-lbl-text">Semester</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="info-bar">
                                        <span><i class="fas fa-code-branch"></i> Branch</span>
                                        <span class="fw-bold text-uppercase text-warning"><?php echo htmlspecialchars($section['branch']); ?></span>
                                    </div>
                                    <div class="info-bar">
                                        <span><i class="fas fa-calendar-alt"></i> Academic Year</span>
                                        <span class="fw-bold"><?php echo htmlspecialchars($section['academic_year']); ?></span>
                                    </div>
                                </div>

                                <button class="btn-view-hp">
                                    <span>View House Points</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($sections_data)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-trophy text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold text-slate">No Section Data Found</h4>
                    <p class="text-muted">No house points sections configured.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        // Real-time search filter for sections
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.section-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
