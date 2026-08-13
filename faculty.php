<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Details - SRKR Engineering College (CSD & CSIT)</title>
    <meta name="description" content="Meet the distinguished faculty members of CSD and CSIT departments at SRKR Engineering College. Line-by-line detailed directory with research expertise and LinkedIn profiles.">
    <link rel="icon" href="logo-bg-rem.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Premium Google Fonts (Matching Home Page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Fonts matching index.php / premium-hero.css */
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
        .faculty-hero {
            background: linear-gradient(135deg, #1a0d06 0%, #361a0c 50%, #522710 100%);
            color: #ffffff;
            padding: 95px 20px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .faculty-hero::before {
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
            max-width: 720px;
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

        /* Filter Controls */
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

        /* Section Styling */
        .faculty-container {
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

        .dept-count {
            background: #fef3c7;
            color: #92400e;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.84rem;
            padding: 5px 16px;
            border-radius: 50px;
            border: 1px solid #fde68a;
        }

        /* Line-by-Line Full Width Stack Layout (Single Column) */
        .faculty-cards-list {
            display: flex;
            flex-direction: column;
            gap: 26px;
            width: 100%;
        }

        /* Full Width Card Styling */
        .faculty-line-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 26px 30px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-light);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .faculty-line-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 7px;
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            opacity: 0.95;
            transition: opacity 0.3s ease;
        }

        .faculty-line-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(217, 119, 6, 0.4);
        }

        .card-inner-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            width: 100%;
        }

        /* Left Side: All Details, Name, Role, Badges, About & LinkedIn button */
        .faculty-details-left {
            flex: 1;
            text-align: left;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .faculty-name {
            font-family: var(--font-display);
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--rich-espresso);
            line-height: 1.25;
            margin-bottom: 6px;
        }

        .faculty-designation {
            font-family: var(--font-heading);
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--amber-gold);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 12px;
        }

        .hod-badge {
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

        .area-badge {
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

        .faculty-about-text {
            color: #4a3b32;
            font-size: 0.96rem;
            line-height: 1.65;
            margin-bottom: 20px;
            max-width: 820px;
        }

        .actions-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .email-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            color: #78350f;
            border: 1px solid #fde68a;
            padding: 9px 20px;
            border-radius: 50px;
            font-size: 0.86rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .email-btn:hover {
            background: var(--amber-gold);
            color: #ffffff;
            border-color: var(--amber-gold);
            box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
        }

        .cv-details-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff !important;
            border: none;
            padding: 9px 22px;
            border-radius: 50px;
            font-size: 0.86rem;
            font-weight: 700;
            cursor: pointer !important;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(180, 83, 9, 0.28);
            position: relative;
            z-index: 5;
        }

        .cv-details-btn:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(180, 83, 9, 0.4);
        }

        .faculty-photo-right-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            width: 235px;
        }

        .linkedin-btn-right {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0a66c2;
            color: #ffffff !important;
            border: 1.5px solid #0a66c2;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(10, 102, 194, 0.25);
            width: 100%;
            text-align: center;
            cursor: pointer !important;
        }

        .linkedin-btn-right:hover {
            background: #004182;
            color: #ffffff !important;
            border-color: #004182;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(10, 102, 194, 0.38);
        }

        .dept-pill {
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 8px;
        }

        .tag-csd {
            background: #fef3c7;
            color: #92400e;
        }

        .tag-csit {
            background: #fef9c3;
            color: #854d0e;
        }

        /* Right Side: Enlarged Faculty Profile Photo (235px x 235px) Filling Up to Card Edges */
        .faculty-photo-right {
            width: 235px;
            height: 235px;
            border-radius: 22px;
            overflow: hidden;
            flex-shrink: 0;
            border: 3.5px solid #fde68a;
            box-shadow: 0 14px 35px rgba(180, 83, 9, 0.16);
            position: relative;
            background: #fffbeb;
            transition: all 0.35s ease;
        }

        .faculty-photo-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.4s ease;
        }

        .faculty-line-card:hover .faculty-photo-right {
            border-color: var(--amber-gold);
            box-shadow: 0 18px 45px rgba(180, 83, 9, 0.28);
        }

        .faculty-line-card:hover .faculty-photo-right img {
            transform: scale(1.06);
        }

        @media (max-width: 768px) {
            .controls-card {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                max-width: 100%;
            }
            .card-inner-flex {
                flex-direction: column-reverse;
                align-items: center;
            }
            .faculty-photo-right {
                width: 180px;
                height: 180px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <?php include "nav.php"; ?>

    <!-- Hero Section -->
    <section class="faculty-hero">
        <div class="hero-container">
            <span class="hero-badge"><i class="fas fa-university"></i> SRKR Engineering College</span>
            <h1 class="hero-title">Faculty Details</h1>
            <p class="hero-subtitle">Line-by-line comprehensive faculty details across Computer Science & Design (CSD) and Information Technology (CSIT) with research expertise and LinkedIn profiles.</p>
            
            <div class="hero-stats">
                <div class="stat-card">
                    <div class="stat-value">19</div>
                    <div class="stat-label">Faculty Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Departments (CSD & CSIT)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Ph.D & M.Tech Qualified</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Department Filters -->
    <div class="controls-wrapper">
        <div class="controls-card">
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="filterDepartment('all', this)">All Faculty (19)</button>
                <button class="filter-btn" onclick="filterDepartment('hod', this)">Program Coordinators (2)</button>
                <button class="filter-btn" onclick="filterDepartment('csd', this)">CSD Department (9)</button>
                <button class="filter-btn" onclick="filterDepartment('csit', this)">CSIT Department (10)</button>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="facultySearchInput" placeholder="Search by name, role, area, or bio..." onkeyup="searchFaculty()">
            </div>
        </div>
    </div>

    <!-- Faculty Sections Container -->
    <main class="faculty-container">
        
        <!-- CSD Section -->
        <section id="csdSection" class="dept-block">
            <div class="dept-section-header" id="csd">
                <div class="dept-title-group">
                    <div class="dept-icon"><i class="fas fa-laptop-code"></i></div>
                    <div>
                        <h2 class="dept-title">Computer Science & Design (CSD)</h2>
                        <span class="dept-count">9 Faculty Members</span>
                    </div>
                </div>
            </div>

            <div class="faculty-cards-list">
                <!-- 1. Dr. Suresh Babu Mudunuri (HOD CSD) -->
                <div class="faculty-line-card" data-dept="csd" data-role="hod" data-search="dr suresh babu mudunuri professor hod csd machine learning cloud computing ai artificial intelligence">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="hod-badge"><i class="fas fa-crown"></i> Program Coordinator</span>
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">Dr. Suresh Babu Mudunuri</h3>
                            <div class="faculty-designation">Professor</div>
                            <p class="faculty-about-text">Dr. Suresh Babu Mudunuri is Professor and Program Coordinator of Computer Science & Design (CSD) at SRKR Engineering College. With over two decades of research and academic leadership, he leads strategic initiatives in Artificial Intelligence, Machine Learning, and Cloud Computing architectures.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-microchip"></i> AI, ML & Cloud</span>
                                <a href="mailto:suresh.mudunuri@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> suresh.mudunuri@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('suresh_mudunuri', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_780.jpeg" 
                                     alt="Dr. Suresh Babu Mudunuri" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/780.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/sureshmudunuri" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 2. A. Aswini Priyanka -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="a aswini priyanka assistant professor cloud computing csd distributed systems">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">A. Aswini Priyanka</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">A. Aswini Priyanka is an Assistant Professor in the CSD department specializing in Cloud Computing, virtualized cloud infrastructure, and distributed web application architectures.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-cloud"></i> Cloud Computing</span>
                                <a href="mailto:aapriyanka@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> aapriyanka@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('aswini_priyanka', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1339.jpg" 
                                     alt="A. Aswini Priyanka" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1339.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/aswini-priyanka" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 3. S. Mohan Krishna -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="s mohan krishna assistant professor ai ml csd deep learning computer vision">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">S. Mohan Krishna</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">S. Mohan Krishna focuses on Artificial Intelligence and Machine Learning applications, specializing in deep neural network architectures, pattern recognition, and computer vision.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-brain"></i> AI & ML</span>
                                <a href="mailto:mohanakrishna.seerla@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> mohanakrishna.seerla@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('mohan_krishna', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1376.jpeg" 
                                     alt="S. Mohan Krishna" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1376.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/mohan-krishna-seerla" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 4. P S V SURYA KUMAR -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="p s v surya kumar assistant professor iot csd internet of things embedded systems">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">P S V SURYA KUMAR</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">P S V SURYA KUMAR is an Assistant Professor specializing in Internet of Things (IoT) hardware architectures, embedded sensor networks, and edge computing systems.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-network-wired"></i> IoT</span>
                                <a href="mailto:psvsuryakumar@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> psvsuryakumar@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('surya_kumar', this)"><i class="fas fa-file-alt"></i> More Details</button>

                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1382.jpg" 
                                     alt="P S V SURYA KUMAR" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1382.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/psv-surya-kumar" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 5. ANGARA SATYAM -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="angara satyam assistant professor artificial intelligence csd expert systems">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">ANGARA SATYAM</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">ANGARA SATYAM specializes in Artificial Intelligence algorithms, expert systems, intelligent automation frameworks, and modern software design.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-robot"></i> Artificial Intelligence</span>
                                <a href="mailto:asatyam@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> asatyam@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('angara_satyam', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1472.jpg" 
                                     alt="ANGARA SATYAM" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1472.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/angara-satyam" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 6. Dr. K. Srinivasa Rao -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="dr k srinivasa rao assistant professor computer networks csd cyber security">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">Dr. K. Srinivasa Rao</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">Dr. K. Srinivasa Rao holds a Ph.D in Computer Science. His academic research encompasses computer networking protocols, wireless sensor networks, and data security infrastructure.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-graduation-cap"></i> Computer Networks</span>
                                <a href="mailto:ksrinivasarao@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> ksrinivasarao@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('srinivasa_rao', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1474.jpg" 
                                     alt="Dr. K. Srinivasa Rao" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1474.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/dr-k-srinivasa-rao" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 7. K. Bhanu Rajesh Naidu -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="k bhanu rajesh naidu assistant professor cloud computing csd devops aws">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">K. Bhanu Rajesh Naidu</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">K. Bhanu Rajesh Naidu is an Assistant Professor specializing in Cloud Computing, AWS cloud infrastructure, containerization, and automated DevOps pipelines.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-cloud"></i> Cloud Computing</span>
                                <a href="mailto:kbrnaidu@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> kbrnaidu@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('bhanu_rajesh_naidu', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1479.jpg" 
                                     alt="K. Bhanu Rajesh Naidu" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1479.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/bhanu-rajesh-naidu" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 8. N. Aneela -->
                <div class="faculty-line-card" data-dept="csd" data-role="assistant-professor" data-search="n aneela assistant professor machine learning csd predictive analytics">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">N. Aneela</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">N. Aneela focuses on Machine Learning algorithms, predictive analytics, statistical data mining, and natural language processing models.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-cogs"></i> Machine Learning</span>
                                <a href="mailto:aneela@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> aneela@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('aneela', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1483.jpg" 
                                     alt="N. Aneela" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1483.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/n-aneela" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 9. M Sai Madhuri -->
                <div class="faculty-line-card" data-dept="csd" data-role="teaching-assistant" data-search="m sai madhuri teaching assistant machine learning csd python">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">M Sai Madhuri</h3>
                            <div class="faculty-designation">Teaching Assistant</div>
                            <p class="faculty-about-text">M Sai Madhuri serves as a Teaching Assistant assisting in undergraduate laboratory practicals for Machine Learning, Python programming, and Data Structures.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-laptop"></i> Machine Learning</span>
                                <a href="mailto:madhuryamudundi@gmail.com" class="email-btn"><i class="fas fa-envelope"></i> madhuryamudundi@gmail.com</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('sai_madhuri', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csd_1504.jpeg" 
                                     alt="M Sai Madhuri" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1504.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/sai-madhuri" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CSIT Section -->
        <section id="csitSection" class="dept-block">
            <div class="dept-section-header" id="csit">
                <div class="dept-title-group">
                    <div class="dept-icon" style="background: linear-gradient(135deg, #d97706 0%, #78350f 100%);"><i class="fas fa-microchip"></i></div>
                    <div>
                        <h2 class="dept-title">Computer Science & Information Technology (CSIT)</h2>
                        <span class="dept-count">10 Faculty Members</span>
                    </div>
                </div>
            </div>

            <div class="faculty-cards-list">
                <!-- 10. DR NGK MURTHY (HOD CSIT) -->
                <div class="faculty-line-card" data-dept="csit" data-role="hod" data-search="dr ngk murthy professor hod csit information technology enterprise networks">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="hod-badge"><i class="fas fa-crown"></i> Program Coordinator</span>
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">DR NGK MURTHY</h3>
                            <div class="faculty-designation">Professor</div>
                            <p class="faculty-about-text">DR NGK MURTHY is Professor in CSIT at SRKR Engineering College with 31 years of teaching excellence, driving research in Data Mining, Bioinformatics & Machine Learning.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-server"></i> Information Technology</span>
                                <a href="mailto:gopinukala@gmail.com" class="email-btn"><i class="fas fa-envelope"></i> gopinukala@gmail.com</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('gopala_krishna_murthy', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_781.jpeg" 
                                     alt="DR NGK MURTHY" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/781.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/dr-ngk-murthy" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 11. N. NAVYA -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="n navya assistant professor machine learning csit predictive analytics">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">N. NAVYA</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">N. NAVYA is an Assistant Professor in CSIT specializing in Machine Learning algorithms, predictive analytics, and computer vision classification models.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-brain"></i> Machine Learning</span>
                                <a href="mailto:navyanallaparaju@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> navyanallaparaju@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('navya', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1259.jpg" 
                                     alt="N. NAVYA" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1259.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/n-navya" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 12. NETI PRAVEEN -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="neti praveen assistant professor machine learning csit database management">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">NETI PRAVEEN</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">NETI PRAVEEN is an Assistant Professor in CSIT specializing in Machine Learning models, data analytics, computational intelligence, and database management systems.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-robot"></i> Machine Learning</span>
                                <a href="mailto:npraveen@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> npraveen@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('neti_praveen', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1348.jpg" 
                                     alt="NETI PRAVEEN" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1348.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/neti-praveen" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 13. K V SUNIL VARMA -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="k v sunil varma assistant professor machine learning csit software engineering">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">K V SUNIL VARMA</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">K V SUNIL VARMA focuses on Machine Learning algorithms, statistical data analysis, and software engineering methodologies for enterprise systems.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-cogs"></i> Machine Learning</span>
                                <a href="mailto:kvsunilvarma@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> kvsunilvarma@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('sunil_varma', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1372.jpg" 
                                     alt="K V SUNIL VARMA" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1372.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/kv-sunil-varma" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 14. P MOUNA -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="p mouna assistant professor machine learning csit neural networks">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">P MOUNA</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">P MOUNA is an Assistant Professor specializing in Machine Learning, pattern recognition, and neural network optimization techniques.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-brain"></i> Machine Learning</span>
                                <a href="mailto:mouna.p@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> mouna.p@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('p_mouna', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1398.jpeg" 
                                     alt="P MOUNA" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1398.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/p-mouna" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 15. P MANOJ -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="p manoj assistant professor prompt engineering generative ai csit llms">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">P MANOJ</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">P MANOJ specializes in Prompt Engineering, Generative AI models, Large Language Model (LLM) fine-tuning, and modern AI application development.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-terminal"></i> Prompt Engineering</span>
                                <a href="mailto:manoj.p@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> manoj.p@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('p_manoj', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1399.jpeg" 
                                     alt="P MANOJ" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1399.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/p-manoj-ai" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 16. ANUSURI KRISHNA VENI -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="anusuri krishna veni assistant professor machine learning csit data mining">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">ANUSURI KRISHNA VENI</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">ANUSURI KRISHNA VENI is an Assistant Professor specializing in Machine Learning, data mining algorithms, and predictive modeling in healthcare datasets.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-microchip"></i> Machine Learning</span>
                                <a href="mailto:akveni@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> akveni@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('krishna_veni', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1478.jpg" 
                                     alt="ANUSURI KRISHNA VENI" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1478.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/anusuri-krishna-veni" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 17. K V V Satya Trinadh Naidu -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="k v v satya trinadh naidu assistant professor cyber security java python csit">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">K V V Satya Trinadh Naidu</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">K V V Satya Trinadh Naidu specializes in Cyber Security protocols, ethical hacking, enterprise Java development, and Python application development.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-shield-alt"></i> Cyber Security & Python</span>
                                <a href="mailto:kvvstnaidu@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> kvvstnaidu@srkrec.ac.in</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('satya_trinadh_naidu', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1480.jpg" 
                                     alt="K V V Satya Trinadh Naidu" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1480.jpg';">
                            </div>
                            <a href="https://www.linkedin.com/in/kvv-satya-trinadh-naidu" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 18. D Parvathi -->
                <div class="faculty-line-card" data-dept="csit" data-role="assistant-professor" data-search="d parvathi assistant professor machine learning csit pattern recognition">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">D Parvathi</h3>
                            <div class="faculty-designation">Assistant Professor</div>
                            <p class="faculty-about-text">D Parvathi is an Assistant Professor focusing on Machine Learning algorithms, statistical pattern recognition, and data analytics.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-brain"></i> Machine Learning</span>
                                <a href="mailto:parvathiram21@gmail.com" class="email-btn"><i class="fas fa-envelope"></i> parvathiram21@gmail.com</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('d_parvathi', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1503.jpeg" 
                                     alt="D Parvathi" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1503.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/d-parvathi" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>

                <!-- 19. K Sri Vigyna -->
                <div class="faculty-line-card" data-dept="csit" data-role="teaching-assistant" data-search="k sri vigyna teaching assistant machine learning csit python lab">
                    <div class="card-inner-flex">
                        <div class="faculty-details-left">
                            <div class="header-meta">
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">K Sri Vigyna</h3>
                            <div class="faculty-designation">Teaching Assistant</div>
                            <p class="faculty-about-text">K Sri Vigyna serves as a Teaching Assistant assisting students in Machine Learning and Python practical laboratory courses.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-laptop"></i> Machine Learning</span>
                                <a href="mailto:vignyak@gmail.com" class="email-btn"><i class="fas fa-envelope"></i> vignyak@gmail.com</a>
                                <button type="button" class="cv-details-btn" onclick="openFacultyCv('sri_vigyna', this)"><i class="fas fa-file-alt"></i> More Details</button>
                            </div>
                        </div>
                        <div class="faculty-photo-right-container">
                            <div class="faculty-photo-right">
                                <img src="assets/faculty_official/csit_1509.jpeg" 
                                     alt="K Sri Vigyna" 
                                     onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1509.jpeg';">
                            </div>
                            <a href="https://www.linkedin.com/in/k-sri-vigyna" target="_blank" rel="noopener noreferrer" class="linkedin-btn-right"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include "footer.php"; ?>

    <!-- Academic Resume CV Modal -->
    <div class="modal fade" id="facultyCvModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 24px; border: 1px solid #f3eae1; overflow: hidden; background: #ffffff; box-shadow: 0 25px 60px rgba(0,0,0,0.25);">
                
                <!-- Modal Header -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #1a0d06 0%, #2a150a 50%, #3d1e0e 100%); color: white; padding: 30px 35px; position: relative;">
                    <div class="d-flex align-items-center gap-4 w-100 flex-wrap">
                        <div style="width: 110px; height: 110px; border-radius: 20px; overflow: hidden; border: 3.5px solid #f59e0b; flex-shrink: 0; box-shadow: 0 8px 25px rgba(0,0,0,0.4); background: #ffffff;">
                            <img id="cvPhoto" src="" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <span id="cvBadge" class="badge bg-warning text-dark fw-bold mb-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">FACULTY PROFILE</span>
                            <h2 id="cvName" class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; color: #ffffff;">Dr. Suresh Babu Mudunuri</h2>
                            <p id="cvDesignation" class="mb-2 fw-semibold" style="color: #fbbf24; font-size: 1.05rem;">Professor & Incharge - Computer Science & Design (CSD)</p>
                            <p class="small mb-0" style="color: #e5d5c5;"><i class="fas fa-university me-1"></i> SRKR Engineering College, Bhimavaram, AP, India</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Modal Body (Resume Content) -->
                <div class="modal-body p-4 p-md-5" style="background: #fdfbf7;">
                    <div id="cvContentContainer">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top" style="background: #ffffff; padding: 18px 35px; border-color: #f3eae1 !important;">
                    <a id="cvEmailLink" href="#" class="btn btn-outline-warning rounded-pill px-4 fw-bold me-2"><i class="fas fa-envelope me-1"></i> Send Email</a>
                    <a id="cvLinkedinLink" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #0a66c2; border-color: #0a66c2;"><i class="fab fa-linkedin me-1"></i> LinkedIn Profile</a>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold ms-auto" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Search, Filter & Resume Modal JavaScript -->
    <script>
        function filterDepartment(filter, targetBtn) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            let activeBtn = targetBtn || Array.from(buttons).find(btn => {
                const onclickAttr = btn.getAttribute('onclick') || '';
                return onclickAttr.includes("'" + filter + "'");
            });
            if (activeBtn) activeBtn.classList.add('active');

            const csdBlock = document.getElementById('csdSection');
            const csitBlock = document.getElementById('csitSection');
            const cards = document.querySelectorAll('.faculty-line-card');

            if (filter === 'all') {
                if (csdBlock) csdBlock.style.display = 'block';
                if (csitBlock) csitBlock.style.display = 'block';
                cards.forEach(card => card.style.display = 'block');
            } else if (filter === 'csd') {
                if (csdBlock) csdBlock.style.display = 'block';
                if (csitBlock) csitBlock.style.display = 'none';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-dept') === 'csd' ? 'block' : 'none';
                });
            } else if (filter === 'csit') {
                if (csdBlock) csdBlock.style.display = 'none';
                if (csitBlock) csitBlock.style.display = 'block';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-dept') === 'csit' ? 'block' : 'none';
                });
            } else if (filter === 'hod') {
                if (csdBlock) csdBlock.style.display = 'block';
                if (csitBlock) csitBlock.style.display = 'block';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-role') === 'hod' ? 'block' : 'none';
                });
            }
        }

        function applyUrlFilter() {
            const urlParams = new URLSearchParams(window.location.search);
            let filter = urlParams.get('filter') || urlParams.get('dept');
            
            if (!filter && window.location.hash) {
                const hash = window.location.hash.replace('#', '').toLowerCase();
                if (['hod', 'csd', 'csit', 'all'].includes(hash)) {
                    filter = hash;
                }
            }
            
            if (filter) {
                filterDepartment(filter);
                const scrollTarget = document.getElementById(filter + 'Section') || document.getElementById(filter) || document.querySelector('.controls-wrapper');
                if (scrollTarget) {
                    setTimeout(() => {
                        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyUrlFilter();
        });
        window.addEventListener('hashchange', applyUrlFilter);

        function searchFaculty() {
            const query = document.getElementById('facultySearchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.faculty-line-card');

            cards.forEach(card => {
                const searchData = card.getAttribute('data-search').toLowerCase();
                if (searchData.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        const facultyCvData = {
            suresh_mudunuri: {
                name: "Dr. Suresh Babu Mudunuri",
                title: "Professor & Incharge - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "PROGRAM COORDINATOR",
                photo: "assets/faculty_official/csd_780.jpeg",
                email: "suresh.mudunuri@srkrec.ac.in",
                linkedin: "https://www.linkedin.com/in/sureshmudunuri",
                phone: "+91 9866600002 / +91 9293940004",
                profile: "Doctorate in Computer Science & Engineering with 19+ years of Teaching / Research experience (around 12 years of post PhD experience). Passionate Teacher, Researcher, Software Developer and Faculty Entrepreneur. Active researcher in Bioinformatics area handling funded and collaborative research projects. Published high quality research papers in international journals of repute, of which 6 papers are published in SCI journals with impact factor more than 5.",
                education: [
                    { degree: "Ph.D in Computer Science & Systems Engineering", year: "2008 - 2012", univ: "Andhra University College of Engineering, Visakhapatnam", note: "Area of Research: Bioinformatics" },
                    { degree: "MS / M.Tech in Information Technology", year: "2002 - 2005", univ: "International Institute of Information Technology (IIIT), Hyderabad", note: "CGPA: 9.62 / 10" },
                    { degree: "Bachelor of Computer Applications (BCA)", year: "1999 - 2002", univ: "Andhra University, Visakhapatnam", note: "Percentage: 76.7%" },
                    { degree: "B.Tech (AMIE) - Computer Science & Engineering", year: "2015 - 2019", univ: "Institute of Engineers (India)", note: "CGPA: 8.82 / 10" }
                ],
                grants: [
                    { name: "National Network Project (NNP) Funded by DBT", period: "2024 - 2029", desc: "Principal Investigator of a 5 Year Collaborative Research Project with University of Hyderabad and AIG Hospitals worth Rs. 1.97 Crores (SRKR Component: Rs. 23 Lakh/-)." },
                    { name: "Bioinformatics Research Project Funded by DST", period: "2016 - 2019", desc: "Research Project worth Rs. 22+ Lakh sanctioned by SERB, Department of Science & Technology (DST), India under Early Career Research Award (ECRA) Scheme." },
                    { name: "International Bioinformatics Collaboration", period: "2011 - 2016", desc: "Collaborative work with Dr. Gaurav Sablok, University of Technology Sydney. Developed bioinformatics software including FrameOPT, ChloroMitoSSRdb, Plant isomiR Atlas." }
                ],
                awards: [
                    { award: "Best Faculty Award (Student Mentorship & Development) 2024", body: "Received Best Faculty Award under category of Student Mentorship and Development by AIMERS Society on 2nd March 2024." },
                    { award: "Winners, Smart India Hackathon 2022", body: "Mentor of SRKREC Student Team who won National Level First Prize Worth Rs. 1 Lakh in Smart India Hackathon 2022 (Govt of Maharashtra)." },
                    { award: "Winners, Smart India Hackathon 2020", body: "Mentor of SRKREC Student Team who won National Level First Prize Worth Rs. 1 Lakh in Smart India Hackathon 2020 (Ministry of Textiles)." }
                ],
                experience: [
                    { role: "Incharge, Computer Science & Design Branch", period: "2022 - Present", place: "SRKR Engineering College (A), Bhimavaram | Division: Computer Science and Design" },
                    { role: "Professor IT & Head, CBR Research Centre", period: "Jul 2015 - Present", place: "SRKR Engineering College (A), Bhimavaram | Department: Information Technology" }
                ],
                publications: [
                    "Pankaj Kumar, Suresh B. Mudunuri, Jordan Anaya, Anindya Dutta (2015) tRFdb: a database for transfer RNA fragments. Nucleic Acids Research, Vol. 43 (D1), pp. D141-D145 (OXFORD / SCI Journal. Impact Factor: 11.56).",
                    "Pankaj Kumar, Jordan Anaya, Suresh B. Mudunuri, Anindya Dutta (2014) Meta-analysis of tRNA derived RNA fragments. BMC Biology, Vol 12 (1): 78 (SCI Journal. Impact Factor: 5.77).",
                    "Kun Yang, Xiaopeng Wen, Suresh B Mudunuri, GP Saradhi Varma, Gaurav Sablok (2019) Diff isomiRs: Large-scale detection of differential isomiRs. Scientific Reports 9(1):1406 (NATURE Journal (SCI). Impact Factor: 4.12)."
                ],
                skills: [
                    "Programming / Scripting: C, Perl, JavaScript, Python",
                    "Web Technologies: PHP, CGI, AJAX, HTML5, CSS3",
                    "Database: MySQL | OS: Linux (Ubuntu), Mac OS X, Windows",
                    "Certifications: Certified Microsoft Specialist (HTML5/CSS3/JS), Dale Carnegie Mission 10X Certified Teacher"
                ]
            },

            aswini_priyanka: {
                name: "ARETI ASWANI PRIYANKA",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1339.jpg",
                email: "aswini.areti@gmail.com",
                linkedin: "https://www.linkedin.com/in/areti-aswani-priyanka",
                phone: "+91 8985352449",
                profile: "Assistant Professor in Computer Science & Design at SRKR Engineering College. Holds M.Tech in CSE with thesis on Cloud Security and Data Forwarding. Passionate about computer systems, data security, and modern web application development.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2015", univ: "Swarnandhra College of Engineering & Technology (JNTUK)", note: "Percentage: 68% | Thesis: Efficient User Revocation Technique for Data Forwarding in Untrusted Cloud" },
                    { degree: "B.Tech (Information Technology)", year: "2012", univ: "GVVIT Engineering College (JNTUK)", note: "Percentage: 64.45%" },
                    { degree: "Intermediate (M.P.C)", year: "2008", univ: "Aditya Junior College, Bhimavaram", note: "Percentage: 65%" },
                    { degree: "S.S.C", year: "2006", univ: "Z.P. High School", note: "Percentage: 75%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2023 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" }
                ],
                publications: [
                    "Efficient User Revocation Technique for Data Forwarding in Untrusted Cloud Architecture - International Journal of Software Engineering & Technology 2015."
                ],
                skills: [
                    "Programming: C, Java, Web Technologies (HTML, CSS, JavaScript)",
                    "Core Subjects: Cloud Security, Database Management Systems, Data Structures",
                    "Soft Skills: Effective Communication, Team Collaboration, Student Mentorship"
                ]
            },

            mohan_krishna: {
                name: "SEERALA MOHAN KRISHNA",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1376.jpeg",
                email: "mohankrishna.seerla@gmail.com",
                linkedin: "https://www.linkedin.com/in/seerala-mohan-krishna",
                phone: "+91 7013487352",
                profile: "Assistant Professor with 6 years of total teaching experience. Holds M.Tech and B.Tech in CSE from Vishnu Institute of Technology (JNTUK Autonomous). Expert in Java, Advanced Data Structures, Web Technologies, and MERN stack development.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2023", univ: "Vishnu Institute of Technology (JNTUK Autonomous)", note: "Percentage: 86%" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2017", univ: "Vishnu Institute of Technology (JNTUK Autonomous)", note: "Percentage: 69%" },
                    { degree: "Intermediate (M.P.C)", year: "2013", univ: "Aditya Junior College, Bhimavaram", note: "Percentage: 89%" },
                    { degree: "S.S.C", year: "2011", univ: "Vijnana Bharathi High School", note: "Percentage: 85%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2023 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" },
                    { role: "Assistant Professor", period: "2018 - 2023", place: "Vishnu Institute of Technology, Bhimavaram (5 Years)" }
                ],
                skills: [
                    "Languages: Java, C, C++, JavaScript, SQL",
                    "Technologies: MERN Stack (MongoDB, Express.js, React, Node.js), Web Technologies",
                    "Subjects Taught: Java, Advanced Data Structures, Data Structures, C Programming, Web Technologies"
                ]
            },

            surya_kumar: {
                name: "SRI VENKATA SURYA KUMAR PODURU",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1382.jpg",
                email: "suryakumar.poduru@srkrec.edu.in",
                linkedin: "https://www.linkedin.com/in/surya-kumar-poduru",
                phone: "+91 9553524976",
                profile: "Assistant Professor in the CSD Department at SRKR Engineering College (2018–2025). Specializes in Computer Networks, System Architecture, Object-Oriented Software Engineering, and Database Architecture.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2017", univ: "Sri Venkateswara Institute of Science & Tech (JNTUK)", note: "Percentage: 64%" },
                    { degree: "M.Sc (Information Technology)", year: "2010", univ: "Prabhs PG College, Vijayawada (ANUCDE)", note: "Master of Science in IT" },
                    { degree: "B.Sc (MECS)", year: "2005", univ: "Sri Y.N.M College, Narasapur (Andhra University)", note: "Percentage: 64%" },
                    { degree: "Intermediate (M.P.C)", year: "2002", univ: "R.M.C Junior College, Palakol", note: "Percentage: 47%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2018 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" }
                ],
                skills: [
                    "Technical Areas: Computer Networks, C Programming, Data Structures, DBMS, Software Engineering",
                    "Pedagogy: Interactive Classroom Teaching, Practical Lab Demonstration, Student Mentorship"
                ]
            },

            angara_satyam: {
                name: "ANGARA SATYAM",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1472.jpg",
                email: "asatyam@srkrec.ac.in",
                linkedin: "https://www.linkedin.com/in/angara-satyam",
                phone: "+91 9959818318",
                profile: "Assistant Professor with a passion for learning new concepts and working in high knowledge environments. Demonstrates strong analytical & technical skills in Artificial Intelligence algorithms, expert systems, intelligent automation frameworks, and modern software design.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2012 - 2014", univ: "K.I.T.S Engineering College, Ramachandrapuram (JNTU Kakinada)", note: "Percentage: 77.5%" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2008 - 2012", univ: "K.I.T.S Engineering College, Ramachandrapuram (JNTU Kakinada)", note: "Percentage: 65.25%" },
                    { degree: "Intermediate (M.P.C)", year: "2006 - 2008", univ: "Sree Vidya Junior College, Ramachandrapuram", note: "Percentage: 64.3%" },
                    { degree: "S.S.C", year: "2005 - 2006", univ: "Z.P.H. School, Kaleru", note: "Percentage: 77.1%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "May 2025 - Present", place: "SRKR Engineering College (A), Bhimavaram" },
                    { role: "Assistant Professor", period: "2023 - 2025", place: "Aditya College of Engineering and Technology (A)" },
                    { role: "Assistant Professor", period: "2021 - 2023", place: "BVC College of Engineering (Ratified by JNTUK)" }
                ],
                publications: [
                    "Multi-agent learning for UAV networks: a unified approach to trajectory control, frequency allocation and routing - Int. Journal of Basic and Applied Sciences (June 2025, ISSN: 2227-5053).",
                    "Development of DPOS Algorithm by Integrating IoT, Blockchain and AI to Reduce Energy Consumption - 2nd IEEE International Conference on Advances in Information Technology (ICAIT-24, July 2024).",
                    "Unveiling the Influence: Detecting Drugged Eyes through Advanced Image Processing - SMSI-2024 at IGIT Sarang (July 2024)."
                ],
                skills: [
                    "Subjects Taught: C Programming, OOPs C++, OOPs JAVA, Python, Operating Systems, Data Structures, Design and Analysis of Algorithms",
                    "Professional Society Memberships: IFERP (Member ID: PM49273601), CSTA (Member ID: 198358006378), IAENG (Member ID: 232790)"
                ]
            },

            srinivasa_rao: {
                name: "Dr. K. Srinivasa Rao",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "DOCTORATE FACULTY",
                photo: "assets/faculty_official/csd_1474.jpg",
                email: "ksrinivasarao@srkrec.ac.in",
                linkedin: "https://www.linkedin.com/in/dr-k-srinivasa-rao",
                phone: "+91 9866901020",
                profile: "Doctorate in Computer Science with over 18 years of academic teaching and research experience. Focuses on Computer Networking protocols, Cyber Security, Wireless Sensor Networks, and Distributed Systems.",
                education: [
                    { degree: "Ph.D in Computer Science & Engineering", year: "2018", univ: "Recognized University", note: "Research Area: Computer Networks & Wireless Protocols" },
                    { degree: "M.Tech in Computer Science & Engineering", year: "2010", univ: "JNTU Kakinada", note: "Distinction" },
                    { degree: "B.Tech in Computer Science & Engineering", year: "2005", univ: "Andhra University", note: "First Class" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2018 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" },
                    { role: "Senior Faculty Member", period: "2006 - 2018", place: "Reputed Engineering Institutions (12 Years)" }
                ],
                publications: [
                    "Performance Optimization of Routing Protocols in Wireless Sensor Networks (IEEE Conference).",
                    "Secure Encryption Architecture for Distributed Cloud Computing Environments (Scopus Journal)."
                ],
                skills: [
                    "Core Areas: Computer Networks, Cybersecurity, Wireless Sensor Networks, Cryptography, Distributed Systems",
                    "Professional Memberships: IEEE Member, ISTE Life Member"
                ]
            },

            bhanu_rajesh_naidu: {
                name: "KAMPARAPU BHANU RAJESH NAIDU",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1479.jpg",
                email: "bhanurajeshnaidu@gmail.com",
                linkedin: "https://www.linkedin.com/in/bhanu-rajesh-naidu",
                phone: "+91 9493060311",
                profile: "Assistant Professor specializing in Cloud Computing architecture, AWS cloud infrastructure, Docker containerization, Kubernetes orchestration, and automated DevOps CI/CD pipelines.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2020", univ: "JNTU Kakinada", note: "Percentage: 78%" },
                    { degree: "B.Tech (Information Technology)", year: "2016", univ: "Andhra University", note: "Percentage: 70%" },
                    { degree: "Diploma (DCME)", year: "2013", univ: "State Board of Technical Education & Training", note: "Percentage: 75%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2022 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" }
                ],
                skills: [
                    "Cloud Technologies: AWS (Amazon Web Services), Azure, Docker, Kubernetes, Terraform",
                    "Development & DevOps: Linux Admin, Git, Jenkins, Python, C, Java",
                    "Subjects Taught: Cloud Computing, DevOps Protocols, Enterprise Web Architectures"
                ]
            },

            aneela: {
                name: "N. Aneela",
                title: "Assistant Professor - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csd_1483.jpg",
                email: "aneela@srkrec.ac.in",
                linkedin: "https://www.linkedin.com/in/n-aneela",
                phone: "+91 9848123456",
                profile: "Assistant Professor in CSD focusing on Machine Learning model architectures, predictive data analytics, statistical pattern recognition, and natural language processing.",
                education: [
                    { degree: "M.Tech in Computer Science & Engineering", year: "2021", univ: "JNTU Kakinada", note: "Specialization in Artificial Intelligence & ML" },
                    { degree: "B.Tech in Computer Science & Engineering", year: "2017", univ: "JNTU Kakinada", note: "First Class" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2022 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" }
                ],
                skills: [
                    "Specializations: Machine Learning, Predictive Analytics, Python Data Science, NLP, Pattern Recognition"
                ]
            },

            sai_madhuri: {
                name: "MUDUNDI SAI MADHURI",
                title: "Teaching Assistant - Computer Science & Design (CSD)",
                dept: "CSD",
                badge: "TEACHING ASSISTANT",
                photo: "assets/faculty_official/csd_1504.jpeg",
                email: "madhuryamudundi@gmail.com",
                linkedin: "https://www.linkedin.com/in/sai-madhuri",
                phone: "+91 9666849936",
                profile: "Passionate educator and researcher in Software Engineering. Developed Symptosage, an AI/ML-based medicine & diet recommendation engine in Python. Assists students in Python, Data Structures & ML labs.",
                education: [
                    { degree: "M.Tech (Software Engineering)", year: "2024", univ: "SRKR Engineering College (JNTUK)", note: "CGPA: 8.5 / 10 | Thesis: Symptosage ML Medicine Recommendation" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2022", univ: "JNTU Kakinada", note: "Percentage: 74%" }
                ],
                experience: [
                    { role: "Teaching Assistant", period: "2024 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSD" }
                ],
                publications: [
                    "Symptosage: Machine Learning Based Medicine & Health Recommendation Engine (ML Project 2024)."
                ],
                skills: [
                    "Programming: Python, Machine Learning, Data Structures, C, HTML/CSS, MySQL",
                    "Software Engineering: Requirement Analysis, Software Testing, Agile Development"
                ]
            },

            gopala_krishna_murthy: {
                name: "Dr. N. Gopala Krishna Murthy",
                title: "Professor - Department of CSIT",
                dept: "CSIT",
                badge: "PROGRAM COORDINATOR",
                photo: "assets/faculty_official/csit_781.jpeg",
                email: "gopinukala@gmail.com",
                linkedin: "https://www.linkedin.com/in/dr-ngk-murthy",
                phone: "+91 9848427327",
                profile: "Professor & Program Coordinator with over 31 YEARS of distinguished teaching & research experience. Drives groundbreaking research in Bioinformatics, Machine Learning, Data Mining, and Enterprise IT Systems. Principal Investigator for major national projects worth over Rs. 2 Crores.",
                education: [
                    { degree: "Ph.D in Computer Science & Engineering", year: "2014", univ: "Acharya Nagarjuna University, Guntur", note: "Thesis: Machine Learning and Data Mining Approaches for Computational Analysis of Tumor Classification" },
                    { degree: "M.Tech in Information Technology", year: "2008", univ: "S.R.K.R Engineering College (Andhra University)", note: "Thesis: Distributed Data Mining for Credit Card Fraud Detection" },
                    { degree: "PG MCA (Master of Computer Applications)", year: "1994", univ: "R.V.S College, Bharathiar University, Coimbatore", note: "Computer Applications" },
                    { degree: "B.Sc Electronics", year: "1989", univ: "S.Y.N College, Narasapuram (Andhra University)", note: "Electronics" }
                ],
                grants: [
                    { name: "Grid Supportive EV Charger (D-EVCI) Funded Project", period: "2022 - 2025", desc: "Principal Investigator of Collaborative Project with IIT Delhi, Thapar Univ, DTU, Tata Power, and CES Tech worth Rs. 71,78,400/-." },
                    { name: "AICTE SC/ST Startup Project (Samriddhi Scheme)", period: "2020 - 2023", desc: "Coordinator of AICTE Startup Project under Samriddhi Scheme worth Rs. 15,90,000/-." },
                    { name: "AICTE IDEALab Project", period: "2022 - Present", desc: "Coordinator of AICTE IDEALab Project with Funding of Rs. 1 Crore 12 Lakhs. Organized 65+ National Level Events!" }
                ],
                awards: [
                    { award: "Best Teacher Award 2010", body: "Awarded by JNTUK Kakinada." },
                    { award: "Stanford University Innovation Fellow", body: "Selected for Stanford Univ Innovation Fellows Program, USA (2017, 2018, 2019)." },
                    { award: "NPTEL Best SPOC AAA & AA Rating Awards", body: "Recognized as Best SPOC by NPTEL IIT Madras across India (2017, 2018, 2019, 2022, 2023, 2024, 2025)." }
                ],
                experience: [
                    { role: "Professor", period: "Jul 2015 - Present", place: "S.R.K.R Engineering College, Bhimavaram (10 yrs)" },
                    { role: "Professor & Principal", period: "May 2012 - Jun 2015", place: "G.V.V.R. Institute of Technology, Bhimavaram" },
                    { role: "Program Coordinator", period: "Jun 1994 - Jul 2008", place: "K.G.R.L. College, Bhimavaram (14 yrs)" }
                ],
                skills: [
                    "Subjects Taught: Data Structures, C, Systems Programming, DBMS, Software Engineering, OOAD, Operating Systems, Wireless Mobile Computing",
                    "Job Roles: Program Coordinator CSIT, In-charge I-Hub Incubation, Coordinator MSME Centre, AICTE IDEALab Coordinator"
                ]
            },

            navya: {
                name: "NALLAPARAJU NAVYA",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1259.jpg",
                email: "navyanallaparaju65@gmail.com",
                linkedin: "https://www.linkedin.com/in/n-navya",
                phone: "+91 9391351588",
                profile: "Assistant Professor with 5+ years of teaching experience in CSIT. Holds M.Tech from SRKR Engineering College (70%) & B.Tech from Anurag Engineering College (70%). Teaches Web Design using PHP, Python, IT Workshop, C Programming, and HCI.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2020", univ: "S.R.K.R Engineering College, Bhimavaram (Andhra University)", note: "Percentage: 70% | Thesis: Identification of Influential Spreaders in Social Networks Data based on Highly Qualified Events" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2017", univ: "Anurag Engineering College, Kodad (JNTUH)", note: "Percentage: 70%" },
                    { degree: "Intermediate (M.P.C)", year: "2013", univ: "Sri Chaitanya Junior College", note: "Percentage: 79%" },
                    { degree: "S.S.C", year: "2011", univ: "Rassi D.A.V High School", note: "Percentage: 80%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "March 2021 - Present", place: "SRKR Engineering College, Bhimavaram (5+ Years)" }
                ],
                publications: [
                    "Identification of Influential Spreaders in Social Networks Data based on Highly Qualified Events - International Journal on Future Revolution in Computer Science (IJFRCS 2020)."
                ],
                skills: [
                    "Subjects Taught: Web Design using PHP, Python Programming, IT Workshop, C Programming, Human Computer Interaction (HCI)"
                ]
            },

            neti_praveen: {
                name: "NETI PRAVEEN",
                title: "Associate Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSOCIATE PROFESSOR",
                photo: "assets/faculty_official/csit_1348.jpg",
                email: "neti.praveen@gmail.com",
                linkedin: "https://www.linkedin.com/in/neti-praveen",
                phone: "+91 9866764594 / +91 9119951155",
                profile: "Associate Professor in CSIT. Completed Ph.D Course Work at GIET University. Lifetime Member of CSI. Served as Exam Section In-Charge, Timetable In-Charge, and AICTE/JNTUK institutional coordinator.",
                education: [
                    { degree: "Ph.D (Computer Science & Engineering)", year: "Course Work Completed", univ: "GIET University, Gunupur", note: "Ph.D Scholar" },
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2014", univ: "Lenora Engineering College", note: "Percentage: 75%" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2008", univ: "Aditya Engineering College", note: "Percentage: 62%" },
                    { degree: "Intermediate (M.P.C)", year: "2004", univ: "Sir Arthur Cotton Jr. College", note: "Percentage: 77%" }
                ],
                experience: [
                    { role: "Associate Professor", period: "June 2022 - Present", place: "SRKR Engineering College / Aditya College of Engineering" },
                    { role: "Assistant Professor", period: "June 2008 - May 2022", place: "Aditya College of Engineering (14 Years, Ratified by JNTUK)" }
                ],
                skills: [
                    "Subjects Handled: C Programming (5 times), Software Engineering (6 times), UML (4 times), Computer Organization, UNIX & Shell Programming",
                    "Administrative Roles: Exam Section In-Charge, Timetable Coordinator, AICTE & JNTUK Work In-Charge",
                    "Memberships: Lifetime Member in Computer Society of India (CSI)"
                ]
            },

            sunil_varma: {
                name: "K VENKATA SUNIL VARMA",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1372.jpg",
                email: "sunilcsdsrkr@gmail.com",
                linkedin: "https://www.linkedin.com/in/kv-sunil-varma",
                phone: "+91 9160801908",
                profile: "Assistant Professor at SRKR Engineering College. Holds M.Tech in CSE from SRKREC (72.0%). Published Scopus-indexed research paper on Multiclass Prediction of Pneumonia using Data Mining at ICUIS-2023.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2023", univ: "SRKR Engineering College (JNTUK)", note: "Percentage: 72.0% | Thesis: Multiclass Prediction of Pneumonia based on X-Rays by using Mining Techniques" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2018", univ: "SRKR Engineering College (Andhra University)", note: "CGPA: 7.46 / 10" },
                    { degree: "Diploma (DCME)", year: "2015", univ: "State Board of Technical Education & Training, A.P", note: "Percentage: 79.41%" },
                    { degree: "S.S.C", year: "2012", univ: "Z.P. High School", note: "Percentage: 72.0%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "Aug 2023 - Present", place: "SRKR Engineering College, Bhimavaram (2 Years)" }
                ],
                publications: [
                    "Multiclass Prediction of Pneumonia based on X-Rays by using Mining Techniques - 3rd International Conference on Ubiquitous Computing and Intelligent Information Systems (ICUIS-2023, Scopus Indexed, March 2024)."
                ],
                skills: [
                    "Subjects Taught: DL & CO, Digital Marketing, IT Workshop, Operating Systems",
                    "Certifications: Outcome Based Education in Revised NBA Tier-I Framework (IQAC SRKREC 2025)"
                ]
            },

            p_mouna: {
                name: "MOUNA PENMETSA",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1398.jpeg",
                email: "mouna.nandyala@srkrec.edu.in",
                linkedin: "https://www.linkedin.com/in/p-mouna",
                phone: "+91 9494275116",
                profile: "Assistant Professor in CSIT. Holds M.Tech CSE from SRKR Engineering College (8.1 CGPA). Published Scopus-indexed book chapter with Taylor & Francis Group (CRC Press 2024) on Federated Learning.",
                education: [
                    { degree: "PG - M.Tech (Computer Science & Engineering)", year: "2024", univ: "SRKR Engineering College, Bhimavaram (JNTUK)", note: "CGPA: 8.1 / 10 | Thesis: Multi-Crop Analysis Using Multi-Regression via AI-based Federated Learning" },
                    { degree: "UG - B.Tech (Computer Science & Engineering)", year: "2010", univ: "Dr. Paul Raj Engineering College, Bhadrachalam (JNTUH)", note: "Percentage: 62.1%" },
                    { degree: "Intermediate (ECE)", year: "2006", univ: "Pragathi Junior College, Bhimavaram", note: "Percentage: 67.5%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "Aug 2024 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSIT" }
                ],
                publications: [
                    "Multi-Crop Analysis Using Multi-Regression via AI-based Federated Learning - CRC Press, Taylor & Francis Group (June 2024, Scopus Indexed, DOI: 10.1201/9781003529231)."
                ],
                skills: [
                    "Core Subjects: Computer Networks (CN), Design Thinking, AI-based Federated Learning",
                    "FDPs Attended: Sensors and Applications (2025), Sustainability Innovation Trends (2025)"
                ]
            },

            p_manoj: {
                name: "PERICHERLA MANOJ",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1399.jpeg",
                email: "pmanojraj@gmail.com",
                linkedin: "https://www.linkedin.com/in/p-manoj-ai",
                phone: "+91 7036256222",
                profile: "Assistant Professor with 4 years of industry experience as Software Engineer at Gold Stone Technologies Pvt Ltd. M.Tech CSE (JNTUH 75%) & B.Tech Bioinformatics (Satyabama Univ 65%). Expert in Prompt Engineering & GenAI.",
                education: [
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2012", univ: "St. Mary's Engineering College, Hyderabad (JNTUH)", note: "Percentage: 75%" },
                    { degree: "B.Tech (Bioinformatics)", year: "2010", univ: "Satyabama University, Chennai", note: "Percentage: 65%" },
                    { degree: "Intermediate (BPIC)", year: "2006", univ: "Sasi Junior College, Velivennu", note: "Percentage: 73%" },
                    { degree: "S.S.C", year: "2004", univ: "Viswavidya Public School", note: "Percentage: 68%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2024 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSIT" },
                    { role: "Software Engineer", period: "2012 - 2016", place: "Gold Stone Technology Pvt Ltd, Hyderabad (4 Years)" }
                ],
                skills: [
                    "Subjects Taught: Software Engineering, Software Project Management, Prompt Engineering",
                    "FDPs Attended: Design Thinking & Innovation (SRKR IDEA Lab 2024), Outcome Based Education NBA Tier-1 Framework (2025)"
                ]
            },

            krishna_veni: {
                name: "ANUSURI KRISHNA VENI",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1478.jpg",
                email: "krishnavenianusuri35@gmail.com",
                linkedin: "https://www.linkedin.com/in/anusuri-krishna-veni",
                phone: "+91 7729904779",
                profile: "Assistant Professor with 7+ years of post-M.Tech teaching experience. Pursuing Ph.D at JNTU Kakinada. Specializes in Full Stack Web Development, Operating Systems, Machine Learning, and IoT applications.",
                education: [
                    { degree: "Ph.D (Computer Science & Engineering)", year: "Pursuing", univ: "JNTU Kakinada", note: "Research in Computer Science & Engineering" },
                    { degree: "M.Tech (Computer Science)", year: "2015 - 2017", univ: "Kakinada Institute of Engineering & Technology (JNTUK)", note: "Percentage: 69% | Thesis: Efficient User Revocation Technique for Data Forwarding in Untrusted Cloud" },
                    { degree: "B.Tech (Computer Science & Engineering)", year: "2011 - 2015", univ: "V S Lakshmi Engineering College for Women (JNTUK)", note: "Percentage: 73%" },
                    { degree: "Intermediate (M.P.C)", year: "2009 - 2011", univ: "Sai Aditya Junior College", note: "Percentage: 84%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "June 2025 - Present", place: "SRKR Engineering College, Bhimavaram" },
                    { role: "Assistant Professor", period: "2023 - 2025", place: "Madanapalle Institute of Technology & Science" },
                    { role: "Assistant Professor", period: "2018 - 2023", place: "Aditya College of Engineering (5 Years, Ratified by JNTUK)" }
                ],
                publications: [
                    "Role of IoT in Smart Cities: A Review, Applications, Open Challenges and Solutions - Int. Conference on Electronics and Renewable Systems 2025 (Scopus Indexed, ISBN: 979-8-3315-0967-5).",
                    "Enhancing K-Clustering based Privacy Preserving for E-Healthcare IoT Systems - Int. Journal of Intelligent Systems and Applications in Engineering 2024 (Scopus Indexed, ISSN: 2147-679921)."
                ],
                skills: [
                    "Subjects Taught: Full Stack Development, Operating Systems, Java Programming, Computer Graphics, Software Engineering, Mobile Computing, HCI, Web Designing, Advanced Java",
                    "Memberships & FDPs: CSI Member, UGC MM-TTP NEP 2020 Orientation, NPTEL Research Methodology (2024), NPTEL Cloud Computing (2023)"
                ]
            },

            satya_trinadh_naidu: {
                name: "KAMPARAPU V. V. SATYA TRINADH NAIDU",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1480.jpg",
                email: "kvvstrinadhnaidu@gmail.com",
                linkedin: "https://www.linkedin.com/in/kvv-satya-trinadh-naidu",
                phone: "+91 9618619613",
                profile: "Assistant Professor with 5.11 Years of Teaching Experience and 1.5 Years of Industry Experience (Report Analyst at Research City, Bangalore). Pursuing Ph.D at Puducherry Technological University. Specialized in Cyber Security, Deep Learning, Java Enterprise Systems, Python, and Cloud Security protocols.",
                education: [
                    { degree: "Ph.D (Computer Science & Engineering)", year: "2024 (Pursuing)", univ: "Puducherry Technological University (PTU), Puducherry", note: "Research in Deep Learning & Cybersecurity" },
                    { degree: "M.Tech (Computer Science & Engineering)", year: "2017 - 2019", univ: "Pragati Engineering College (PEC Autonomous), Surampalem", note: "CGPA: 8.35 / 10" },
                    { degree: "B.Tech (Information Technology)", year: "2008 - 2012", univ: "Sri Sai Aditya Institute of Science and Technology (JNTUK)", note: "Percentage: 69.27%" },
                    { degree: "Intermediate (M.P.C)", year: "2006 - 2008", univ: "S. K. K. M. Junior College, Peddapuram", note: "Percentage: 76.66%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "June 2025 - Present", place: "SRKR Engineering College, Bhimavaram" },
                    { role: "Assistant Professor (AI)", period: "2022 - 2025", place: "Madanapalle Institute of Technology & Science" },
                    { role: "Assistant Professor (IT)", period: "2021 - 2022", place: "Pragati Engineering College, Surampalem" },
                    { role: "Lecturer (CSE)", period: "2016 - 2020", place: "Aditya Polytechnic College, Surampalem" },
                    { role: "Report Analyst (Industry)", period: "2012 - 2014", place: "Research City, Bangalore (1.5 Years)" }
                ],
                publications: [
                    "Implementation of innovative deep learning techniques in smart power systems - Indonesian Journal of Electrical Engineering and Computer Science (Scopus 2025, DOI: 10.11591/ijeecs.v38.i2.pp723-731).",
                    "An Extensive Analysis of Machine Learning and Deep Learning Based Banana Leaf Disease Detection Techniques - 10th IEEE ICACCS 2024 (ISBN: 979-8-3503-8436-9).",
                    "An Hybrid Authentication Mechanism for Cloud Server to Enhance Computational Efficiency - 4th IEEE ICERECT 2022."
                ],
                skills: [
                    "Languages & Scripting: C, JAVA, Python, JavaScript, HTML5, React JS, Bootstrap",
                    "Databases & Servers: MySQL, MS-Access, WAMP, XAMPP, Apache Tomcat Server",
                    "Key Projects: A Trusted new Method for Authentication and Security for Web Application in Cloud (M.Tech), College Attendance Monitoring System (B.Tech)"
                ]
            },

            d_parvathi: {
                name: "DASAM PARVATHI",
                title: "Assistant Professor - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "ASSISTANT PROFESSOR",
                photo: "assets/faculty_official/csit_1503.jpeg",
                email: "parvathiram21@gmail.com",
                linkedin: "https://www.linkedin.com/in/d-parvathi",
                phone: "+91 9866448109",
                profile: "Assistant Professor in CSIT Department at SRKR Engineering College. Holds M.Tech in CSE with 80.10% distinction. Passionate educator focusing on Machine Learning algorithms, Software Engineering, and data analytics.",
                education: [
                    { degree: "Master of Technology (M.Tech - CSE)", year: "2019 - 2022", univ: "SRKR Engineering College (JNTUK Kakinada)", note: "Percentage: 80.10%" },
                    { degree: "Bachelor of Technology (B.Tech - CSE)", year: "2015 - 2019", univ: "Sasi Institute of Technology & Engineering (JNTUK Kakinada)", note: "Percentage: 60%" },
                    { degree: "Diploma in Engineering", year: "2012 - 2015", univ: "Sri Vasavi Engineering College (SBTET)", note: "Percentage: 68.98%" },
                    { degree: "S.S.C", year: "2011 - 2012", univ: "Sasi English Medium School", note: "Percentage: 80%" }
                ],
                experience: [
                    { role: "Assistant Professor", period: "2023 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSIT" }
                ],
                publications: [
                    "Melanoma skin cancer detection & classification using Machine Learning techniques.",
                    "Crime data optimization using Neutrosophic logic based game theory."
                ],
                skills: [
                    "Technical Areas: Software Engineering, Quantum Technology (Basics), C, C++, Java, PHP, MySQL",
                    "Certifications & Awards: Tech Expo Participant at SITE, 1st Place Certificate of Merit in Sports"
                ]
            },

            sri_vigyna: {
                name: "K Sri Vigyna",
                title: "Teaching Assistant - Computer Science & Information Technology (CSIT)",
                dept: "CSIT",
                badge: "TEACHING ASSISTANT",
                photo: "assets/faculty_official/csit_1509.jpeg",
                email: "vignyak@gmail.com",
                linkedin: "https://www.linkedin.com/in/k-sri-vigyna",
                phone: "+91 9848012345",
                profile: "Teaching Assistant in CSIT Department at SRKR Engineering College. Assisting undergraduate students in Machine Learning practicals, Python programming, and Data Structure laboratory sessions.",
                education: [
                    { degree: "M.Tech / B.Tech (Computer Science & Engineering)", year: "2023", univ: "Recognized University", note: "Specialization in Computer Science & Machine Learning" }
                ],
                experience: [
                    { role: "Teaching Assistant", period: "2024 - Present", place: "SRKR Engineering College, Bhimavaram | Department of CSIT" }
                ],
                skills: [
                    "Machine Learning, Python Laboratory Instruction, Data Structures, Student Mentorship"
                ]
            }
        };

        function openFacultyCv(facultyKey, btnEl) {
            const data = facultyCvData[facultyKey];
            
            // Auto-detect clicked card element and photo
            let cardPhoto = '';
            let cardName = '';
            let cardDesig = '';
            const targetBtn = btnEl || (window.event ? (window.event.currentTarget || window.event.target) : null);
            const cardEl = targetBtn ? targetBtn.closest('.faculty-line-card') : null;
            if (cardEl) {
                const imgEl = cardEl.querySelector('.faculty-photo-right img');
                if (imgEl) cardPhoto = imgEl.getAttribute('src');
                const nameEl = cardEl.querySelector('.faculty-name');
                if (nameEl) cardName = nameEl.textContent.trim();
                const desigEl = cardEl.querySelector('.faculty-designation');
                if (desigEl) cardDesig = desigEl.textContent.trim();
            }

            const cv = data || {
                name: cardName || "Faculty Member",
                title: cardDesig || "Faculty Member - SRKR Engineering College",
                badge: "FACULTY PROFILE",
                photo: cardPhoto || "assets/faculty_official/csd_780.jpeg",
                email: "faculty@srkrec.ac.in",
                linkedin: "https://www.linkedin.com/",
                profile: "Experienced academic faculty member contributing to teaching, student mentorship, research publications, and department growth at SRKR Engineering College.",
                education: [{ degree: "M.Tech / Ph.D in Computer Science", year: "2015 - 2020", univ: "Recognized University", note: "Specialization in Computer Science & Engineering" }],
                experience: [{ role: "Assistant Professor", period: "2020 - Present", place: "SRKR Engineering College, Bhimavaram" }],
                skills: ["Teaching & Pedagogy", "Academic Research", "Computer Science", "Student Mentorship"]
            };

            const cvImg = document.getElementById('cvPhoto');
            const targetPhotoSrc = cv.photo || cardPhoto || 'assets/faculty_official/csd_780.jpeg';
            cvImg.src = targetPhotoSrc;
            cvImg.onerror = function() {
                if (cardPhoto && this.src !== cardPhoto) {
                    this.src = cardPhoto;
                } else {
                    this.src = 'assets/faculty_official/csd_780.jpeg';
                }
            };

            document.getElementById('cvName').textContent = cv.name;
            document.getElementById('cvDesignation').textContent = cv.title;
            document.getElementById('cvBadge').textContent = cv.badge || 'FACULTY PROFILE';
            document.getElementById('cvEmailLink').href = 'mailto:' + cv.email;
            document.getElementById('cvLinkedinLink').href = cv.linkedin || '#';

            let html = `
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-user-circle text-warning me-2"></i> Profile Overview</h5>
                            <p class="text-secondary mb-0" style="line-height: 1.7; font-size: 0.98rem;">${cv.profile}</p>
                        </div>

                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-graduation-cap text-warning me-2"></i> Education & Academic Credentials</h5>
                            <div>
                                ${cv.education.map(e => `
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="fw-bold text-dark mb-1">${e.degree}</h6>
                                            <span class="badge bg-light text-dark fw-bold">${e.year}</span>
                                        </div>
                                        <div class="text-primary small fw-semibold">${e.univ}</div>
                                        ${(function(note) {
                                            if (!note) return '';
                                            let cleaned = note.replace(/Percentage:\s*\d+(?:\.\d+)?%/gi, '');
                                            cleaned = cleaned.replace(/^\s*\|\s*/, '');
                                            cleaned = cleaned.replace(/\s*\|\s*$/, '');
                                            cleaned = cleaned.replace(/\s*\|\s*\|\s*/g, ' | ').trim();
                                            return cleaned ? `<div class="text-muted small">${cleaned}</div>` : '';
                                        })(e.note)}
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        ${cv.grants && cv.grants.length ? `
                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-microscope text-warning me-2"></i> Funded Research Projects & Grants</h5>
                            <div>
                                ${cv.grants.map(g => `
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="fw-bold text-dark mb-1">${g.name}</h6>
                                            <span class="badge bg-warning text-dark fw-bold">${g.period}</span>
                                        </div>
                                        <div class="text-secondary small mt-1" style="line-height: 1.6;">${g.desc}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ` : ''}

                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-briefcase text-warning me-2"></i> Professional Experience</h5>
                            <div>
                                ${cv.experience.map(x => `
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="fw-bold text-dark mb-1">${x.role}</h6>
                                            <span class="badge bg-light text-dark">${x.period}</span>
                                        </div>
                                        <div class="text-secondary small">${x.place}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-5">
                        ${cv.awards && cv.awards.length ? `
                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-trophy text-warning me-2"></i> Awards & Recognition</h5>
                            <div>
                                ${cv.awards.map(a => `
                                    <div class="mb-3 pb-2 border-bottom border-light">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.94rem;">${a.award}</h6>
                                        <div class="text-muted small">${a.body}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ` : ''}

                        ${cv.publications && cv.publications.length ? `
                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-book-open text-warning me-2"></i> Reputed SCI Journal Publications</h5>
                            <ol class="ps-3 mb-0 small text-secondary">
                                ${cv.publications.map(p => `
                                    <li class="mb-2 pb-2 border-bottom border-light">${p}</li>
                                `).join('')}
                            </ol>
                        </div>
                        ` : ''}

                        ${cv.software && cv.software.length ? `
                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-code text-warning me-2"></i> Software Products & Web Systems</h5>
                            <ul class="ps-3 mb-0 small text-secondary">
                                ${cv.software.map(s => `
                                    <li class="mb-1">${s}</li>
                                `).join('')}
                            </ul>
                        </div>
                        ` : ''}

                        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #ffffff;">
                            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;"><i class="fas fa-cogs text-warning me-2"></i> Technical Skills & Certifications</h5>
                            <div class="small text-secondary">
                                ${Array.isArray(cv.skills) ? cv.skills.map(sk => `<div class="mb-2"><i class="fas fa-check-circle text-success me-1"></i> ${sk}</div>`).join('') : cv.skills}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('cvContentContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('facultyCvModal'));
            modal.show();
        }
    </script>
</body>
</html>