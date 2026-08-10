<?php
session_start();
include './connect.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    header('Location: sections_overview.php');
    exit();
}

// Get class information
$class_query = "SELECT * FROM classes WHERE class_id = ?";
$stmt = mysqli_prepare($conn, $class_query);
mysqli_stmt_bind_param($stmt, "i", $class_id);
mysqli_stmt_execute($stmt);
$class_result = mysqli_stmt_get_result($stmt);
$class_data = mysqli_fetch_assoc($class_result);

if (!$class_data) {
    header('Location: sections_overview.php');
    exit();
}

if ($class_data['year'] >= 5) {
    $section_name = 'Graduated Batch';
} else {
    $section_name = $class_data['year'] . '/4 ' . strtoupper($class_data['branch']) . '-' . strtoupper($class_data['section']);
}

// Get students in this class with house points
$students_query = "
    SELECT s.*, h.name as house_name,
           sp.parent_number, sp.address, sp.blood_group, sp.dob,
           spr.summary, spr.skills, spr.social_links, spr.projects, spr.cgpa,
           hp.total_points
    FROM students s
    LEFT JOIN houses h ON s.hid = h.hid
    LEFT JOIN student_personal sp ON s.student_id = sp.student_id
    LEFT JOIN student_profile spr ON s.student_id = spr.student_id
    LEFT JOIN house_points hp ON s.student_id = hp.regd_no
    WHERE s.class_id = ?
    ORDER BY s.student_id ASC
";
$stmt = mysqli_prepare($conn, $students_query);
mysqli_stmt_bind_param($stmt, "i", $class_id);
mysqli_stmt_execute($stmt);
$students_result = mysqli_stmt_get_result($stmt);

$students = [];
while ($student = mysqli_fetch_assoc($students_result)) {
    $students[] = $student;
}

$student_count = count($students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title><?php echo htmlspecialchars($section_name); ?> - Students</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Header */
        .section-students-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 45px 0 65px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(99, 102, 241, 0.2);
            margin-bottom: -35px;
        }

        .section-students-hero::before {
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
            margin-bottom: 8px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .hero-title i {
            color: #818cf8;
        }

        .hero-subtitle {
            color: #94a3b8;
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0;
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

        /* Section Info Banner */
        .info-card-box {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 24px;
            margin-bottom: 35px;
        }

        .info-metric-tile {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .info-metric-tile:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }

        .tile-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .tile-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .tile-label {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Student Card */
        .student-item-col {
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

        .student-card-fancy {
            background: #ffffff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .student-card-fancy:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
            border-color: #818cf8;
        }

        .avatar-wrap {
            position: relative;
            margin-bottom: 14px;
            display: inline-block;
        }

        .avatar-img {
            width: 88px;
            height: 88px;
            object-fit: cover;
            object-position: center 20%;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            border-radius: 50%;
            border: 3px solid #6366f1;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);
            transition: transform 0.3s ease;
        }

        .student-card-fancy:hover .avatar-img {
            transform: scale(1.08);
        }

        .avatar-placeholder {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto;
            border: 3px solid #6366f1;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.2);
        }

        .student-title-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .regd-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 50px;
            margin-bottom: 12px;
        }

        .hp-badge-pill {
            background: linear-gradient(135deg, #fffbe0 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            color: #b45309;
            font-weight: 800;
            font-size: 0.88rem;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 14px;
        }

        .house-tag-chip {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 50px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Banner -->
    <div class="section-students-hero mb-5">
        <div class="full-width-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="hero-title"><i class="fas fa-users"></i> <?php echo htmlspecialchars($section_name); ?></h1>
                    <p class="hero-subtitle"><?php echo $student_count; ?> registered students in this section roster</p>
                </div>
                <div>
                    <a href="sections_overview.php" class="back-btn-pill">
                        <i class="fas fa-arrow-left"></i> Back to All Sections
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">
            <!-- Section Info Card -->
            <div class="info-card-box">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="info-metric-tile">
                            <i class="fas fa-graduation-cap tile-icon text-primary"></i>
                            <div class="tile-value text-primary"><?php echo $class_data['year']; ?>/4</div>
                            <div class="tile-label">Academic Year</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-metric-tile">
                            <i class="fas fa-code-branch tile-icon text-success"></i>
                            <div class="tile-value text-success"><?php echo strtoupper($class_data['branch']); ?></div>
                            <div class="tile-label">Branch</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-metric-tile">
                            <i class="fas fa-layer-group tile-icon text-info"></i>
                            <div class="tile-value text-info"><?php echo strtoupper($class_data['section']); ?></div>
                            <div class="tile-label">Section</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-metric-tile">
                            <i class="fas fa-calendar-alt tile-icon text-warning"></i>
                            <div class="tile-value text-warning"><?php echo $class_data['semester']; ?></div>
                            <div class="tile-label">Semester</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Students Grid -->
            <?php if (!empty($students)): ?>
                <div class="row g-4">
                    <?php foreach ($students as $index => $student): ?>
                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 student-item-col" style="animation-delay: <?php echo ($index * 0.04); ?>s;">
                            <div class="student-card-fancy p-4 text-center" onclick="window.location.href='student_profile.php?student_id=<?php echo urlencode($student['student_id']); ?>'">
                                <!-- Profile Picture -->
                                <?php 
                                $srkr_photo_url = !empty($student['profile_picture']) && file_exists($student['profile_picture'])
                                    ? htmlspecialchars($student['profile_picture'])
                                    : (!empty($student['student_id']) ? 'https://srkrexams.in/SRKR/photo/' . strtoupper($student['student_id']) . '.jpg' : '');
                                ?>
                                <div class="avatar-wrap" onclick="event.stopPropagation(); openPhotoLightbox('<?php echo $srkr_photo_url; ?>', '<?php echo addslashes($student['name']); ?>', '<?php echo addslashes($student['student_id']); ?>');" title="Click to view full photo" style="cursor: pointer;">
                                    <?php if (!empty($srkr_photo_url)): ?>
                                        <img src="<?php echo $srkr_photo_url; ?>" alt="Profile" class="avatar-img" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-placeholder" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Student Name & ID -->
                                <h3 class="student-title-name"><?php echo htmlspecialchars($student['name']); ?></h3>
                                <div>
                                    <span class="regd-badge"><i class="fas fa-id-card me-1"></i> <?php echo htmlspecialchars($student['student_id']); ?></span>
                                </div>
                                
                                <!-- House Points -->
                                <div>
                                    <span class="hp-badge-pill">
                                        <i class="fas fa-trophy"></i>
                                        <span><?php echo isset($student['total_points']) ? htmlspecialchars($student['total_points']) : '0'; ?> House Pts</span>
                                    </span>
                                </div>
                                
                                <!-- House Name & Details -->
                                <div class="mt-auto">
                                    <?php if (!empty($student['house_name'])): ?>
                                        <div class="mb-2">
                                            <span class="house-tag-chip">
                                                <i class="fas fa-home me-1"></i> <?php echo htmlspecialchars($student['house_name']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($student['cgpa'])): ?>
                                        <div class="text-muted small fw-bold mt-1">
                                            <i class="fas fa-chart-line text-success me-1"></i> CGPA: <?php echo htmlspecialchars($student['cgpa']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold text-slate">No Students Enrolled</h4>
                    <p class="text-muted">This section currently has no registered students.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        #photoLightboxModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999999;
        }
        .photo-lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.25s ease;
        }
        .photo-lightbox-card {
            position: relative;
            background: #ffffff;
            border-radius: 24px;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: zoomIn 0.25s ease;
        }
        .photo-lightbox-close {
            position: absolute;
            top: 14px;
            right: 18px;
            background: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            z-index: 10;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .photo-lightbox-close:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
        .photo-lightbox-img-wrap {
            width: 100%;
            max-height: 480px;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .photo-lightbox-img-wrap img {
            max-width: 100%;
            max-height: 480px;
            object-fit: contain;
        }
        .photo-lightbox-info {
            padding: 24px 20px;
        }
        .photo-lightbox-info h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            font-size: 1.4rem;
        }
        .photo-lightbox-info p {
            font-size: 0.95rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 16px;
        }
        .photo-download-btn {
            border-radius: 50px !important;
            padding: 12px 28px !important;
            font-weight: 700 !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35) !important;
            transition: all 0.25s ease !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .photo-download-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.45) !important;
            color: #ffffff !important;
        }
    </style>
    <script>
        function openPhotoLightbox(photoUrl, name, studentId) {
            if (!photoUrl) return;

            let lightbox = document.getElementById('photoLightboxModal');
            if (!lightbox) {
                lightbox = document.createElement('div');
                lightbox.id = 'photoLightboxModal';
                lightbox.innerHTML = `
                    <div class="photo-lightbox-overlay" onclick="closePhotoLightbox(event)">
                        <div class="photo-lightbox-card" onclick="event.stopPropagation()">
                            <button class="photo-lightbox-close" onclick="closePhotoLightbox()">&times;</button>
                            <div class="photo-lightbox-img-wrap">
                                <img id="lightboxImg" src="" alt="Student Photo">
                            </div>
                            <div class="photo-lightbox-info">
                                <h4 id="lightboxName">Student Name</h4>
                                <p id="lightboxId" class="mb-0">Registration Number</p>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(lightbox);
            }

            const img = document.getElementById('lightboxImg');
            const nameEl = document.getElementById('lightboxName');
            const idEl = document.getElementById('lightboxId');

            img.src = photoUrl;
            nameEl.textContent = name || 'Student Photo';
            idEl.textContent = studentId ? `Regd No: ${studentId}` : '';

            lightbox.style.display = 'block';
        }

        function closePhotoLightbox(e) {
            if (!e || e.target.classList.contains('photo-lightbox-overlay') || e.target.classList.contains('photo-lightbox-close')) {
                const lightbox = document.getElementById('photoLightboxModal');
                if (lightbox) {
                    lightbox.style.display = 'none';
                }
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoLightbox();
            }
        });
    </script>

    <?php include "footer.php"; ?>
</body>
</html>
