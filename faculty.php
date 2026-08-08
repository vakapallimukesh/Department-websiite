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

        .linkedin-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0a66c2;
            color: #ffffff !important;
            border: 1.5px solid #0a66c2;
            padding: 9px 22px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(10, 102, 194, 0.28);
            cursor: pointer !important;
            position: relative;
            z-index: 5;
        }

        .linkedin-btn:hover {
            background: #004182;
            color: #ffffff !important;
            border-color: #004182;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(10, 102, 194, 0.42);
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
                <button class="filter-btn active" onclick="filterDepartment('all')">All Faculty (19)</button>
                <button class="filter-btn" onclick="filterDepartment('hod')">Heads of Department (2)</button>
                <button class="filter-btn" onclick="filterDepartment('csd')">CSD Department (9)</button>
                <button class="filter-btn" onclick="filterDepartment('csit')">CSIT Department (10)</button>
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
                                <span class="hod-badge"><i class="fas fa-crown"></i> Head of Department</span>
                                <span class="dept-pill tag-csd">CSD</span>
                            </div>
                            <h3 class="faculty-name">Dr. Suresh Babu Mudunuri</h3>
                            <div class="faculty-designation">Professor</div>
                            <p class="faculty-about-text">Dr. Suresh Babu Mudunuri is Professor and Head of Department of Computer Science & Design (CSD) at SRKR Engineering College. With over two decades of research and academic leadership, he leads strategic initiatives in Artificial Intelligence, Machine Learning, and Cloud Computing architectures.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-microchip"></i> AI, ML & Cloud</span>
                                <a href="mailto:suresh.mudunuri@srkrec.ac.in" class="email-btn"><i class="fas fa-envelope"></i> suresh.mudunuri@srkrec.ac.in</a>
                                <a href="https://www.linkedin.com/in/sureshmudunuri" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_780.jpeg" 
                                 alt="Dr. Suresh Babu Mudunuri" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/780.jpeg';">
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
                                <a href="https://www.linkedin.com/in/aswini-priyanka" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1339.jpg" 
                                 alt="A. Aswini Priyanka" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1339.jpg';">
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
                                <a href="https://www.linkedin.com/in/mohan-krishna-seerla" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1376.jpeg" 
                                 alt="S. Mohan Krishna" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1376.jpeg';">
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
                                <a href="https://www.linkedin.com/in/psv-surya-kumar" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1382.jpg" 
                                 alt="P S V SURYA KUMAR" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1382.jpg';">
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
                                <a href="https://www.linkedin.com/in/angara-satyam" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1472.jpg" 
                                 alt="ANGARA SATYAM" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1472.jpg';">
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
                                <a href="https://www.linkedin.com/in/dr-k-srinivasa-rao" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1474.jpg" 
                                 alt="Dr. K. Srinivasa Rao" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1474.jpg';">
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
                                <a href="https://www.linkedin.com/in/bhanu-rajesh-naidu" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1479.jpg" 
                                 alt="K. Bhanu Rajesh Naidu" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1479.jpg';">
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
                                <a href="https://www.linkedin.com/in/n-aneela" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1483.jpg" 
                                 alt="N. Aneela" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1483.jpg';">
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
                                <a href="https://www.linkedin.com/in/sai-madhuri" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csd_1504.jpeg" 
                                 alt="M Sai Madhuri" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csd/1504.jpeg';">
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
                                <span class="hod-badge"><i class="fas fa-crown"></i> Head of Department</span>
                                <span class="dept-pill tag-csit">CSIT</span>
                            </div>
                            <h3 class="faculty-name">DR NGK MURTHY</h3>
                            <div class="faculty-designation">Professor</div>
                            <p class="faculty-about-text">DR NGK MURTHY is Professor and Head of Department of CSIT at SRKR Engineering College. With over 18 years of academic leadership, he drives advanced research in enterprise IT architectures, data security, and communication networks.</p>
                            <div class="actions-group">
                                <span class="area-badge"><i class="fas fa-server"></i> Information Technology</span>
                                <a href="mailto:gopinukala@gmail.com" class="email-btn"><i class="fas fa-envelope"></i> gopinukala@gmail.com</a>
                                <a href="https://www.linkedin.com/in/dr-ngk-murthy" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_781.jpeg" 
                                 alt="DR NGK MURTHY" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/781.jpeg';">
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
                                <a href="https://www.linkedin.com/in/n-navya" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1259.jpg" 
                                 alt="N. NAVYA" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1259.jpg';">
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
                                <a href="https://www.linkedin.com/in/neti-praveen" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1348.jpg" 
                                 alt="NETI PRAVEEN" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1348.jpg';">
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
                                <a href="https://www.linkedin.com/in/kv-sunil-varma" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1372.jpg" 
                                 alt="K V SUNIL VARMA" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1372.jpg';">
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
                                <a href="https://www.linkedin.com/in/p-mouna" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1398.jpeg" 
                                 alt="P MOUNA" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1398.jpeg';">
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
                                <a href="https://www.linkedin.com/in/p-manoj-ai" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1399.jpeg" 
                                 alt="P MANOJ" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1399.jpeg';">
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
                                <a href="https://www.linkedin.com/in/anusuri-krishna-veni" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1478.jpg" 
                                 alt="ANUSURI KRISHNA VENI" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1478.jpg';">
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
                                <a href="https://www.linkedin.com/in/kvv-satya-trinadh-naidu" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1480.jpg" 
                                 alt="K V V Satya Trinadh Naidu" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1480.jpg';">
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
                                <a href="https://www.linkedin.com/in/d-parvathi" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1503.jpeg" 
                                 alt="D Parvathi" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1503.jpeg';">
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
                                <a href="https://www.linkedin.com/in/k-sri-vigyna" target="_blank" rel="noopener noreferrer" class="linkedin-btn"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                            </div>
                        </div>
                        <div class="faculty-photo-right">
                            <img src="assets/faculty_official/csit_1509.jpeg" 
                                 alt="K Sri Vigyna" 
                                 onerror="this.onerror=null; this.src='https://www.srkrec.ac.in/assets/images/faculty/csit/1509.jpeg';">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include "footer.php"; ?>

    <!-- Client-side Search & Filter JavaScript -->
    <script>
        function filterDepartment(filter) {
            // Update button active state
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const csdBlock = document.getElementById('csdSection');
            const csitBlock = document.getElementById('csitSection');
            const cards = document.querySelectorAll('.faculty-line-card');

            if (filter === 'all') {
                csdBlock.style.display = 'block';
                csitBlock.style.display = 'block';
                cards.forEach(card => card.style.display = 'block');
            } else if (filter === 'csd') {
                csdBlock.style.display = 'block';
                csitBlock.style.display = 'none';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-dept') === 'csd' ? 'block' : 'none';
                });
            } else if (filter === 'csit') {
                csdBlock.style.display = 'none';
                csitBlock.style.display = 'block';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-dept') === 'csit' ? 'block' : 'none';
                });
            } else if (filter === 'hod') {
                csdBlock.style.display = 'block';
                csitBlock.style.display = 'block';
                cards.forEach(card => {
                    card.style.display = card.getAttribute('data-role') === 'hod' ? 'block' : 'none';
                });
            }
        }

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
    </script>
</body>
</html>