<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Details - SRKR Engineering College (CSD & CSIT)</title>
    <meta name="description" content="Explore complete academic details, degree programs for CSD and CSIT, interactive semester curriculum, academic calendar, and syllabus model papers at SRKR Engineering College.">
    <link rel="icon" href="logo-bg-rem.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Premium Google Fonts (Matching Faculty Page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Fonts matching faculty.php */
            --font-display: 'Outfit', sans-serif;
            --font-heading: 'Plus Jakarta Sans', sans-serif;
            --font-body: 'Inter', sans-serif;
            
            /* Yellowish Amber & Golden Palette */
            --amber-gold: #d97706;
            --bright-yellow: #f59e0b;
            --golden-champagne: #e6c280;
            --amber-badge: #b45309;
            --warm-brown: #78350f;
            --rich-espresso: #1a0d06;
            --cream-white: #fdfbf7;
            
            --card-bg: #ffffff;
            --text-dark: #1a0d06;
            --text-muted: #6f5f54;
            --border-light: #f3eae1;
            
            --shadow-subtle: 0 12px 35px rgba(180, 83, 9, 0.07);
            --shadow-hover: 0 24px 55px rgba(180, 83, 9, 0.18);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--cream-white);
            color: var(--text-dark);
            line-height: 1.65;
        }

        /* Hero Banner - Warm Espresso & Amber Gold */
        .academics-hero {
            background: linear-gradient(135deg, #1a0d06 0%, #361a0c 50%, #522710 100%);
            color: #ffffff;
            padding: 95px 20px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .academics-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(230, 194, 128, 0.15) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.45;
        }

        .hero-container {
            max-width: 920px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212, 155, 89, 0.18);
            border: 1px solid rgba(230, 194, 128, 0.4);
            color: var(--golden-champagne);
            padding: 8px 22px;
            border-radius: 50px;
            font-family: var(--font-display);
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.6rem, 5.8vw, 4rem);
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f5ebe6 35%, #e6c280 70%, #d49b59 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.18rem;
            color: #e5d5c5;
            max-width: 760px;
            margin: 0 auto 35px;
            font-weight: 400;
        }

        /* Stats Bar */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(230, 194, 128, 0.2);
            padding: 14px 30px;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            text-align: center;
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--bright-yellow);
        }

        .stat-label {
            font-size: 0.78rem;
            color: #e5d5c5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filter Controls Card */
        .controls-wrapper {
            max-width: 1240px;
            margin: -35px auto 45px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .controls-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 20px 25px;
            box-shadow: 0 15px 35px rgba(180, 83, 9, 0.09);
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: #fffbeb;
            color: #78350f;
            border: 1px solid #fde68a;
            padding: 10px 24px;
            border-radius: 50px;
            font-family: var(--font-display);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover {
            background: #fef3c7;
            color: #451a03;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            border-color: #b45309;
            box-shadow: 0 8px 22px rgba(180, 83, 9, 0.35);
        }

        .search-box {
            position: relative;
            min-width: 280px;
            flex: 1;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 18px 12px 44px;
            border-radius: 50px;
            border: 1px solid var(--border-light);
            background: #fffbeb;
            font-family: var(--font-body);
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: var(--amber-gold);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.18);
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--amber-gold);
            font-size: 0.95rem;
        }

        /* Section Container */
        .academics-container {
            max-width: 1280px;
            margin: 0 auto 80px;
            padding: 0 20px;
        }

        .dept-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 50px 0 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-light);
        }

        .dept-title-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .dept-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #d97706 0%, #78350f 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 8px 20px rgba(180, 83, 9, 0.28);
        }

        .dept-title {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--rich-espresso);
        }

        /* Full Width Section Cards */
        .academic-full-section {
            background: #ffffff;
            border-radius: 28px;
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-light);
            margin-bottom: 45px;
            position: relative;
            overflow: hidden;
            transition: all 0.35s ease;
        }

        .academic-full-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 7px;
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
        }

        .academic-full-section:hover {
            box-shadow: var(--shadow-hover);
            border-color: rgba(217, 119, 6, 0.3);
        }

        .section-badge-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .academic-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            font-family: var(--font-display);
            font-size: 0.76rem;
            font-weight: 700;
            padding: 5px 16px;
            border-radius: 50px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(180, 83, 9, 0.28);
        }

        .tag-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fffbeb;
            color: #92400e;
            font-family: var(--font-body);
            font-size: 0.82rem;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 50px;
            border: 1px solid #fde68a;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--rich-espresso);
            margin-bottom: 8px;
        }

        .section-subtitle {
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 700;
            color: var(--amber-gold);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 25px;
        }

        /* Semester Accordion Toggles */
        .semester-tab {
            background: #fdfbf7;
            border: 1px solid #f3eae1;
            border-radius: 16px;
            padding: 18px 22px;
            margin-bottom: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #1a0d06;
        }

        .semester-tab:hover {
            background: #fffbeb;
            border-color: #d97706;
            transform: translateX(4px);
        }

        .semester-tab.active {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            border-color: #b45309;
            box-shadow: 0 8px 20px rgba(180, 83, 9, 0.25);
        }

        .subject-list {
            display: none;
            background: #ffffff;
            border-radius: 20px;
            padding: 22px 25px;
            margin-top: 10px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
            border: 1px solid #f3eae1;
        }

        .subject-list.active {
            display: block;
        }

        .subject-list ul li {
            padding: 10px 14px;
            border-bottom: 1px solid #f3eae1;
            font-size: 0.94rem;
            font-weight: 500;
            color: #4a3b32;
        }

        .subject-list ul li:last-child {
            border-bottom: none;
        }

        /* Tables for Syllabus & Calendar */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
        }

        .table-custom th {
            background: #fef3c7;
            color: #78350f;
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            padding: 16px 20px;
            border: none;
        }

        .table-custom td {
            background: #ffffff;
            padding: 16px 20px;
            border-top: 1px solid #f3eae1;
            border-bottom: 1px solid #f3eae1;
            vertical-align: middle;
        }

        .table-custom td:first-child {
            border-left: 1px solid #f3eae1;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .table-custom td:last-child {
            border-right: 1px solid #f3eae1;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .pdf-link {
            color: #d97706;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 10px;
            background: rgba(217, 119, 6, 0.1);
            border: 1px solid rgba(217, 119, 6, 0.2);
            transition: all 0.25s ease;
        }

        .pdf-link:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border-color: #b45309;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.25);
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <?php include "nav.php"; ?>

    <!-- Hero Banner (Matching faculty.php theme) -->
    <div class="academics-hero">
        <div class="hero-container">
            <div class="hero-badge">
                <i class="fas fa-graduation-cap"></i> SRKR ENGINEERING COLLEGE
            </div>
            <h1 class="hero-title">Academic Details</h1>
            <p class="hero-subtitle">
                Line-by-line comprehensive overview of degree programs across Computer Science & Design (CSD) and Information Technology (CSIT) with academic calendars, regulations, and syllabus downloads.
            </p>

            <div class="hero-stats">
                <div class="stat-card">
                    <div class="stat-value">2</div>
                    <div class="stat-label">DEGREE PROGRAMS (CSD & CSIT)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">4 YEARS</div>
                    <div class="stat-label">ACADEMIC DURATION</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">AICTE & NBA ACCREDITED</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="controls-wrapper">
        <div class="controls-card">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-th-large me-1"></i> All Academics (4)
                </button>
                <button class="filter-btn" data-filter="csd">
                    <i class="fas fa-book me-1"></i> B.Tech CSD
                </button>
                <button class="filter-btn" data-filter="csit">
                    <i class="fas fa-laptop-code me-1"></i> B.Tech CSIT
                </button>
                <button class="filter-btn" data-filter="calendar">
                    <i class="fas fa-calendar-check me-1"></i> Academic Calendar
                </button>
                <button class="filter-btn" data-filter="syllabus">
                    <i class="fas fa-clipboard-list me-1"></i> Syllabus & Model Papers
                </button>
            </div>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="academicsSearch" placeholder="Search programs, subjects, calendars...">
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="academics-container">
        
        <div class="dept-section-header">
            <div class="dept-title-group">
                <div class="dept-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h2 class="dept-title">Academic Programs & Resources</h2>
                </div>
            </div>
            <span class="dept-count" id="sectionCountLabel">4 Sections Available</span>
        </div>

        <!-- ================= SECTION 1: B.TECH CSD ================= -->
        <div class="academic-full-section" data-category="csd" data-keywords="csd b.tech computer science design 4-year undergraduate degree aicte nba 160 credits curriculum">
            <div class="section-badge-group">
                <span class="academic-badge"><i class="fas fa-graduation-cap"></i> Undergraduate Program</span>
                <span class="tag-badge"><i class="fas fa-award"></i> NBA Accredited</span>
                <span class="tag-badge"><i class="fas fa-check-circle"></i> AICTE Approved</span>
            </div>
            <h2 class="section-title">B.Tech in Computer Science & Design (CSD)</h2>
            <div class="section-subtitle">4-Year B.Tech Degree | AICTE Approved | NBA Accredited | Industry-Aligned Curriculum</div>
            
            <div class="row mb-4">
                <div class="col-lg-8">
                    <p style="color: #4a3b32; font-size: 1.02rem; line-height: 1.8;">
                        The B.Tech in Computer Science & Design is a comprehensive 4-year undergraduate program designed to provide students with a strong foundation in computer science principles, programming, software development, UI/UX design, and emerging technologies. Our curriculum is industry-aligned and regularly updated to include AI, Machine Learning, Cloud Computing, Cybersecurity, and Data Science.
                    </p>
                    <div style="background: #fdfbf7; padding: 22px; border-radius: 18px; border: 1px solid #f3eae1; margin-top: 15px;">
                        <h5 style="color: #1a0d06; font-family: var(--font-display); font-weight: 700; margin-bottom: 12px;"><i class="fas fa-star text-warning me-2"></i>CSD Program Highlights</h5>
                        <ul style="color: #6f5f54; margin: 0; padding-left: 20px; font-size: 0.94rem;">
                            <li>Industry-relevant curriculum integrating Computer Science with UI/UX & Product Design</li>
                            <li>Hands-on practical learning through state-of-the-art laboratory sessions</li>
                            <li>Industry internships and real-world capstone projects</li>
                            <li>Faculty-guided research and innovation opportunities</li>
                            <li>Comprehensive placement support with premier tech employers</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <div style="background: linear-gradient(135deg, #1a0d06 0%, #3d1e0e 100%); color: white; padding: 26px; border-radius: 24px; text-align: center; border: 1px solid #f3eae1; box-shadow: 0 10px 30px rgba(180, 83, 9, 0.15);">
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #f59e0b; margin-bottom: 20px;">CSD Program Specs</h4>
                        <div style="margin: 15px 0; padding-bottom: 12px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Duration</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">4 Years (8 Semesters)</p>
                        </div>
                        <div style="margin: 15px 0; padding-bottom: 12px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Total Credits</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">160 Credits</p>
                        </div>
                        <div style="margin: 15px 0;">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Annual Intake</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">120 Students</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CSD Curriculum Structure -->
            <h3 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin: 35px 0 20px;"><i class="fas fa-list-ul me-2" style="color: var(--amber-gold);"></i>CSD Curriculum Structure (8 Semesters)</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem1', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 1 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem1">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Mathematics I</li>
                            <li>Physics</li>
                            <li>Chemistry</li>
                            <li>Programming in C</li>
                            <li>English Communication</li>
                            <li>Engineering Drawing</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem2', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 2 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem2">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Mathematics II</li>
                            <li>Environmental Science</li>
                            <li>Programming in C++</li>
                            <li>Digital Logic Design</li>
                            <li>Basic Electrical Engineering</li>
                            <li>Professional Ethics</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem3', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 3 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem3">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Data Structures</li>
                            <li>Computer Organization</li>
                            <li>Discrete Mathematics</li>
                            <li>Object Oriented Programming</li>
                            <li>Database Management Systems</li>
                            <li>Software Engineering</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem4', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 4 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem4">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Algorithms Analysis</li>
                            <li>Operating Systems</li>
                            <li>Computer Networks</li>
                            <li>Web Technologies</li>
                            <li>Theory of Computation</li>
                            <li>Microprocessors</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem5', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 5 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem5">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Machine Learning</li>
                            <li>Compiler Design</li>
                            <li>Computer Graphics</li>
                            <li>Artificial Intelligence</li>
                            <li>Elective I</li>
                            <li>Project Work I</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem6', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 6 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem6">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Data Science</li>
                            <li>Cloud Computing</li>
                            <li>Cybersecurity</li>
                            <li>Mobile Application Development</li>
                            <li>Elective II</li>
                            <li>Internship</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem7', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 7 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem7">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Deep Learning</li>
                            <li>Blockchain Technology</li>
                            <li>IoT and Embedded Systems</li>
                            <li>Elective III</li>
                            <li>Elective IV</li>
                            <li>Major Project I</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSD('csd_sem8', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 8 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csd_sem8">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Industry Project</li>
                            <li>Advanced Elective</li>
                            <li>Seminar</li>
                            <li>Major Project II</li>
                            <li>Professional Development</li>
                            <li>Comprehensive Viva</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 2: B.TECH CSIT ================= -->
        <div class="academic-full-section" data-category="csit" data-keywords="csit b.tech computer science information technology 4-year undergraduate degree aicte 160 credits curriculum">
            <div class="section-badge-group">
                <span class="academic-badge"><i class="fas fa-laptop-code"></i> Undergraduate Program</span>
                <span class="tag-badge"><i class="fas fa-layer-group"></i> Advanced Computing</span>
                <span class="tag-badge"><i class="fas fa-check-circle"></i> AICTE Approved</span>
            </div>
            <h2 class="section-title">B.Tech in Computer Science & Information Technology (CSIT)</h2>
            <div class="section-subtitle">4-Year B.Tech Degree | Future-Ready IT Curriculum | System Administration & Software Engineering</div>

            <div class="row mb-4">
                <div class="col-lg-8">
                    <p style="color: #4a3b32; font-size: 1.02rem; line-height: 1.8;">
                        The B.Tech in Computer Science & Information Technology program prepares students for the rapidly evolving IT industry. The curriculum focuses on software engineering, system administration, network management, database technologies, and emerging domains such as cloud computing and cybersecurity with extensive laboratory sessions.
                    </p>
                    <div style="background: #fdfbf7; padding: 22px; border-radius: 18px; border: 1px solid #f3eae1; margin-top: 15px;">
                        <h5 style="color: #1a0d06; font-family: var(--font-display); font-weight: 700; margin-bottom: 12px;"><i class="fas fa-star text-warning me-2"></i>CSIT Program Highlights</h5>
                        <ul style="color: #6f5f54; margin: 0; padding-left: 20px; font-size: 0.94rem;">
                            <li>Industry-oriented curriculum covering software development & cloud architecture</li>
                            <li>Emphasis on practical and project-based software engineering learning</li>
                            <li>Strong foundation in hardware, networking, and security protocols</li>
                            <li>Industry partnerships, expert guest lectures, and hackathons</li>
                            <li>High placement success rate with leading global IT enterprises</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <div style="background: linear-gradient(135deg, #1a0d06 0%, #3d1e0e 100%); color: white; padding: 26px; border-radius: 24px; text-align: center; border: 1px solid #f3eae1; box-shadow: 0 10px 30px rgba(180, 83, 9, 0.15);">
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #f59e0b; margin-bottom: 20px;">CSIT Program Specs</h4>
                        <div style="margin: 15px 0; padding-bottom: 12px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Duration</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">4 Years (8 Semesters)</p>
                        </div>
                        <div style="margin: 15px 0; padding-bottom: 12px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Total Credits</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">160 Credits</p>
                        </div>
                        <div style="margin: 15px 0;">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 4px;">Annual Intake</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">120 Students</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CSIT Curriculum Structure -->
            <h3 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin: 35px 0 20px;"><i class="fas fa-list-ul me-2" style="color: var(--amber-gold);"></i>CSIT Curriculum Structure (8 Semesters)</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem1', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 1 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem1">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Mathematics I</li>
                            <li>Physics</li>
                            <li>Chemistry</li>
                            <li>Programming in C</li>
                            <li>English Communication</li>
                            <li>Computer Fundamentals</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem2', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 2 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem2">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Mathematics II</li>
                            <li>Environmental Science</li>
                            <li>Programming in Java</li>
                            <li>Digital Electronics</li>
                            <li>Basic Electrical Engineering</li>
                            <li>IT Workshop</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem3', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 3 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem3">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Data Structures & Algorithms</li>
                            <li>Computer Architecture</li>
                            <li>Discrete Mathematics</li>
                            <li>Object Oriented Analysis</li>
                            <li>Database Systems</li>
                            <li>Web Programming</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem4', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 4 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem4">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Operating Systems</li>
                            <li>Computer Networks</li>
                            <li>Software Engineering</li>
                            <li>Python Programming</li>
                            <li>Formal Languages & Automata</li>
                            <li>Network Security</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem5', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 5 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem5">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Cloud Computing Architecture</li>
                            <li>Data Mining & Warehousing</li>
                            <li>Information Security</li>
                            <li>Mobile Computing</li>
                            <li>Elective I</li>
                            <li>Mini Project</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem6', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 6 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem6">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Big Data Analytics</li>
                            <li>DevOps Engineering</li>
                            <li>Cyber Security & Laws</li>
                            <li>Artificial Intelligence</li>
                            <li>Elective II</li>
                            <li>Summer Internship</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem7', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 7 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem7">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Machine Learning Applications</li>
                            <li>IoT Networks</li>
                            <li>Software Testing</li>
                            <li>Elective III</li>
                            <li>Elective IV</li>
                            <li>Major Project Phase I</li>
                        </ul>
                    </div>

                    <div class="semester-tab" onclick="toggleSemCSIT('csit_sem8', this)">
                        <h5 style="margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            Semester 8 <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="csit_sem8">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>Industry Project / Internship</li>
                            <li>Advanced IT Elective</li>
                            <li>Technical Seminar</li>
                            <li>Major Project Phase II</li>
                            <li>Comprehensive Viva Voce</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 3: ACADEMIC CALENDAR ================= -->
        <div class="academic-full-section" data-category="calendar" data-keywords="academic calendar schedule 2025 2026 semester exam mid-terms instruction holidays pdf">
            <div class="section-badge-group">
                <span class="academic-badge"><i class="fas fa-calendar-alt"></i> Official Academic Schedule</span>
                <span class="tag-badge"><i class="fas fa-clock"></i> Current Session 2025-26</span>
                <span class="tag-badge"><i class="fas fa-file-pdf"></i> PDF Schedules Available</span>
            </div>
            <h2 class="section-title">Academic Calendar (2025 – 2026)</h2>
            <div class="section-subtitle">Instruction Schedules | Mid-Examination Dates | Practical Exams | Semester End Examinations</div>

            <p style="color: #4a3b32; font-size: 1.02rem; line-height: 1.8; margin-bottom: 25px;">
                Official academic calendar for all undergraduate CSD and CSIT batches detailing class commencement dates, mid-term examinations, preparation holidays, practical lab exams, and semester-end university examinations.
            </p>

            <div class="row g-4">
                <div class="col-md-6">
                    <div style="background: #ffffff; border-radius: 20px; padding: 25px; border: 1px solid #f3eae1; box-shadow: 0 8px 24px rgba(180, 83, 9, 0.06); display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div>
                                <h5 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin: 0 0 4px 0;">II & III B.Tech Academic Calendar 2025-26</h5>
                                <span style="font-size: 0.88rem; color: #6f5f54;">Official schedule for 2nd and 3rd year CSD & CSIT</span>
                            </div>
                        </div>
                        <a href="files/II_III_B.Tech_Academic_Calendar_2025-26.pdf" class="pdf-link" target="_blank">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div style="background: #ffffff; border-radius: 20px; padding: 25px; border: 1px solid #f3eae1; box-shadow: 0 8px 24px rgba(180, 83, 9, 0.06); display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h5 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin: 0 0 4px 0;">I & IV B.Tech Academic Calendar 2025-26</h5>
                                <span style="font-size: 0.88rem; color: #6f5f54;">Official schedule for 1st and 4th year CSD & CSIT</span>
                            </div>
                        </div>
                        <a href="files/II_III_B.Tech_Academic_Calendar_2025-26.pdf" class="pdf-link" target="_blank">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 4: SYLLABUS & MODEL PAPERS ================= -->
        <div class="academic-full-section" data-category="syllabus" data-keywords="syllabus regulations r20 r23 course structure model papers pdf downloads year 1 2 3 4 csit csd">
            <div class="section-badge-group">
                <span class="academic-badge"><i class="fas fa-clipboard-list"></i> Curriculum & Regulations</span>
                <span class="tag-badge"><i class="fas fa-book-open"></i> R20 & R23 Regulations</span>
                <span class="tag-badge"><i class="fas fa-file-pdf"></i> Complete Model Papers</span>
            </div>
            <h2 class="section-title">Syllabus & Model Question Papers</h2>
            <div class="section-subtitle">Semester-wise Syllabus Downloads | Regulation Documents | Model Papers</div>

            <p style="color: #4a3b32; font-size: 1.02rem; line-height: 1.8; margin-bottom: 25px;">
                Complete regulation-wise syllabus downloads and course structures for B.Tech CSD and CSIT programs. Download official PDF documents for R23 and R20 regulations covering Year 1 through Year 4 with model question papers.
            </p>

            <!-- Table 1: R23 Regulation Syllabus & Model Papers -->
            <div style="background: #fdfbf7; border-radius: 20px; padding: 25px; border: 1px solid #f3eae1; margin-bottom: 30px;">
                <h4 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin-bottom: 20px;">
                    <i class="fas fa-book-open text-warning me-2"></i>CSIT & CSD R23 Regulation Syllabus & Model Papers (Year 1 to Year 3)
                </h4>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Year & Regulation</th>
                                <th>CSIT Syllabus</th>
                                <th>CSD Syllabus</th>
                                <th>CSIT Model Papers</th>
                                <th>CSD Model Papers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1st Year R23 -->
                            <tr>
                                <td class="fw-bold text-dark">Year 1 (R23)</td>
                                <td><a href="./files/B.Tech_R23_I_Year_CSIT_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Syllabus</a></td>
                                <td><a href="./files/B.Tech_R23_I_Year_CSG_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Syllabus</a></td>
                                <td><a href="./files/B.Tech_R23_I_Year_CSIT_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Model Papers</a></td>
                                <td><a href="./files/B.Tech_R23_I_Year_CSG_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Model Papers</a></td>
                            </tr>

                            <!-- 2nd Year R23 -->
                            <tr>
                                <td class="fw-bold text-dark">Year 2 (R23)</td>
                                <td><a href="./files/B.Tech_R23_II_Year_CSIT_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Syllabus</a></td>
                                <td><a href="./files/B.Tech_R23_II_Year_CSG_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Syllabus</a></td>
                                <td><a href="./files/B.Tech_R23_II_Year_CSIT_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Model Papers</a></td>
                                <td><a href="./files/B.Tech_R23_II_Year_CSG_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Model Papers</a></td>
                            </tr>

                            <!-- 3rd Year R23 -->
                            <tr>
                                <td class="fw-bold text-dark">Year 3 (R23)</td>
                                <td><a href="./files/R23_3rd_YEAR_CSIT_SYLLABUS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Syllabus</a></td>
                                <td><a href="./files/R23_3RD_YEAR_CSD_SYLLABUS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Syllabus</a></td>
                                <td><a href="./files/R23_3RD_YEAR_CSIT_MQPS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Model Papers</a></td>
                                <td><a href="./files/R23_3RD_YEAR_CSD_MQPS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Model Papers</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: R20 Regulation Syllabus & Model Papers -->
            <div style="background: #fdfbf7; border-radius: 20px; padding: 25px; border: 1px solid #f3eae1;">
                <h4 style="font-family: var(--font-display); font-weight: 800; color: #1a0d06; margin-bottom: 20px;">
                    <i class="fas fa-award text-warning me-2"></i>CSD Year 4 (R20 Regulation) Syllabus & Model Papers
                </h4>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Year & Regulation</th>
                                <th>Syllabus Document</th>
                                <th>Model Question Papers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-dark">Year 4 (R20)</td>
                                <td><a href="./files/B.Tech R20 IV Year CSG Syllabus FINAL ws.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> IV Year R20 Syllabus</a></td>
                                <td><a href="./files/B.Tech R20 IV Year CSG MQP FINAL ws.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> IV Year R20 Model Papers</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Include Footer -->
    <?php include "footer.php"; ?>

    <!-- Interactive Filtering, Search & Semester Accordion JS Scripts -->
    <script>
    function toggleSemCSD(semId, elem) {
        const selectedSem = document.getElementById(semId);
        if (!selectedSem) return;
        
        if (selectedSem.classList.contains('active')) {
            selectedSem.classList.remove('active');
            elem.classList.remove('active');
        } else {
            selectedSem.classList.add('active');
            elem.classList.add('active');
        }
    }

    function toggleSemCSIT(semId, elem) {
        const selectedSem = document.getElementById(semId);
        if (!selectedSem) return;
        
        if (selectedSem.classList.contains('active')) {
            selectedSem.classList.remove('active');
            elem.classList.remove('active');
        } else {
            selectedSem.classList.add('active');
            elem.classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const sections = document.querySelectorAll('.academic-full-section');
        const searchInput = document.getElementById('academicsSearch');
        const sectionCountLabel = document.getElementById('sectionCountLabel');

        let currentFilter = 'all';
        let currentQuery = '';

        function filterSections() {
            let visibleCount = 0;

            sections.forEach(sec => {
                const category = sec.getAttribute('data-category');
                const keywords = sec.getAttribute('data-keywords').toLowerCase();
                const secText = sec.innerText.toLowerCase();

                const matchesFilter = (currentFilter === 'all') || (category === currentFilter);
                const matchesSearch = !currentQuery || keywords.includes(currentQuery) || secText.includes(currentQuery);

                if (matchesFilter && matchesSearch) {
                    sec.style.display = 'block';
                    visibleCount++;
                } else {
                    sec.style.display = 'none';
                }
            });

            if (sectionCountLabel) {
                sectionCountLabel.innerText = visibleCount + (visibleCount === 1 ? ' Section Available' : ' Sections Available');
            }
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.getAttribute('data-filter');
                filterSections();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                currentQuery = e.target.value.toLowerCase().trim();
                filterSections();
            });
        }
    });
    </script>
</body>
</html>
