<?php
session_start();
include './connect.php';
include './db_migration_helper.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$db_helper = new DatabaseMigrationHelper($conn);

// Get all classes/sections
$classes = $db_helper->getAllClasses();

// Get section details with student counts
$sections_data = [];
foreach ($classes as $class_id => $display_name) {
    // Get student count for this class
    $student_count_query = "SELECT COUNT(*) as student_count FROM students WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $student_count_query);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student_count = mysqli_fetch_assoc($result)['student_count'];
    
    // Get class details
    $class_query = "SELECT * FROM classes WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $class_query);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $class_result = mysqli_stmt_get_result($stmt);
    $class_data = mysqli_fetch_assoc($class_result);
    
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
    <title>Sections Overview - SRKR Engineering College</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            --card-glass: rgba(255, 255, 255, 0.95);
            --card-border: rgba(99, 102, 241, 0.15);
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-cyan: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --accent-amber: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --accent-rose: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
        }

        body {
            background: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Header Section */
        .sections-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 50px 0 70px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(139, 92, 246, 0.2);
            margin-bottom: -35px;
        }

        .sections-hero::before {
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
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .hero-title i {
            color: #818cf8;
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        /* Full Width Container Padding */
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
            padding: 24px 20px;
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
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.12);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .stat-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            background: var(--icon-bg, rgba(79, 70, 229, 0.1));
            color: var(--icon-color, #4f46e5);
            font-size: 1.6rem;
            transition: transform 0.3s ease;
        }

        .stat-card-custom:hover .stat-icon-wrap {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-number {
            font-size: 2.3rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            background: var(--stat-gradient, var(--primary-gradient));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .stat-label-text {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Cards Grid */
        .section-card-wrapper {
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

        .section-box {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .section-box:hover {
            transform: translateY(-10px) scale(1.015);
            box-shadow: 0 25px 50px rgba(99, 102, 241, 0.18);
            border-color: #818cf8;
        }

        .section-header-banner {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        .section-avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 14px auto;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            transition: transform 0.3s ease;
        }

        .section-box:hover .section-avatar-circle {
            transform: scale(1.1) rotate(-6deg);
        }

        .section-title-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.2px;
        }

        .section-badge-year {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 4px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-body-content {
            padding: 20px 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .metrics-pill-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .metric-pill-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
            transition: background 0.3s ease;
        }

        .section-box:hover .metric-pill-box {
            background: #ffffff;
            border-color: #cbd5e1;
        }

        .pill-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .pill-val.emerald { color: #059669; }
        .pill-val.indigo { color: #4f46e5; }
        .pill-val.purple { color: #7c3aed; }

        .pill-lbl {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .info-row-item {
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

        .info-row-item i {
            color: #6366f1;
            margin-right: 8px;
        }

        .btn-view-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 13px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            border: none;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .btn-view-section i {
            transition: transform 0.3s ease;
        }

        .section-box:hover .btn-view-section {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
            color: #ffffff;
        }

        .section-box:hover .btn-view-section i {
            transform: translateX(6px);
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Header -->
    <div class="sections-hero mb-5">
        <div class="full-width-container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title"><i class="fas fa-users"></i> Sections Overview</h1>
                    <p class="hero-subtitle">Explore all department sections, student rosters, academic performance, and class house analytics.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Full-Width Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">
            
            <!-- Statistics Cards Row -->
            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--primary-gradient); --icon-bg: rgba(79, 70, 229, 0.1); --icon-color: #4f46e5;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-number"><?php echo count($sections_data); ?></div>
                        <div class="stat-label-text">Total Active Sections</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-emerald); --icon-bg: rgba(16, 185, 129, 0.1); --icon-color: #10b981;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number"><?php echo array_sum(array_column($sections_data, 'student_count')); ?></div>
                        <div class="stat-label-text">Enrolled Students</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-cyan); --icon-bg: rgba(6, 182, 212, 0.1); --icon-color: #06b6d4;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($sections_data, 'branch'))); ?></div>
                        <div class="stat-label-text">Branches Represented</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card-custom" style="--stat-gradient: var(--accent-amber); --icon-bg: rgba(245, 158, 11, 0.1); --icon-color: #f59e0b;">
                        <div class="stat-icon-wrap">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($sections_data, 'academic_year'))); ?></div>
                        <div class="stat-label-text">Academic Batches</div>
                    </div>
                </div>
            </div>
            
            <!-- Sections Grid Row -->
            <div class="row g-4">
                <?php foreach ($sections_data as $index => $section): ?>
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 section-card-wrapper" style="animation-delay: <?php echo ($index * 0.06); ?>s;">
                        <div class="section-box" onclick="window.location.href='section_students.php?class_id=<?php echo $section['class_id']; ?>'">
                            <div class="section-header-banner">
                                <div class="section-avatar-circle">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="section-title-text"><?php echo htmlspecialchars($section['display_name']); ?></h3>
                                <span class="section-badge-year"><?php echo htmlspecialchars($section['academic_year']); ?></span>
                            </div>
                            
                            <div class="section-body-content">
                                <div class="metrics-pill-grid">
                                    <div class="metric-pill-box">
                                        <div class="pill-val emerald"><?php echo $section['student_count']; ?></div>
                                        <div class="pill-lbl">Students</div>
                                    </div>
                                    <div class="metric-pill-box">
                                        <div class="pill-val indigo"><?php echo htmlspecialchars($section['semester']); ?></div>
                                        <div class="pill-lbl">Semester</div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="info-row-item">
                                        <span><i class="fas fa-laptop-code"></i> Branch</span>
                                        <span class="fw-bold text-uppercase text-primary"><?php echo htmlspecialchars($section['branch']); ?></span>
                                    </div>
                                    <div class="info-row-item">
                                        <span><i class="fas fa-graduation-cap"></i> Academic Year</span>
                                        <span class="fw-bold"><?php echo htmlspecialchars($section['year']); ?>/4 Year</span>
                                    </div>
                                </div>
                                
                                <button class="btn-view-section">
                                    <span>View Section Roster</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($sections_data)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold text-slate">No Sections Configured</h4>
                    <p class="text-muted">No department sections found in database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
</body>
</html>

