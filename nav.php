<?php
?>
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Reset default margins and padding to eliminate gaps */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        margin: 0 !important;
        padding-top: 72px !important;
    }
    
    @media (max-width: 991px) {
        body {
            padding-top: 68px !important;
        }
    }

    .navbar.navbar-expand-lg {
        background: #1a0d06 !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.4), 
                    inset 0 -1px 0 0 rgba(217, 119, 6, 0.25) !important;
        padding: 0.5rem 2rem !important;
        font-family: 'Inter', sans-serif !important;
        border: none !important;
        border-bottom: 1px solid rgba(217, 119, 6, 0.25) !important;
        margin: 0 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 0 !important;
        z-index: 10000 !important;
        -webkit-backface-visibility: hidden !important;
        backface-visibility: hidden !important;
        transform: translate3d(0, 0, 0) !important;
        will-change: background-color, box-shadow !important;
        transition: background-color 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease !important;
    }

    .navbar.navbar-expand-lg.nav-scrolled {
        top: 0 !important;
        padding: 0.5rem 2rem !important;
        background: #0f0703 !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6) !important;
    }

    @media (max-width: 991px) {
        .navbar.navbar-expand-lg {
            width: 100% !important;
            top: 0 !important;
            border-radius: 0 !important;
            padding: 0.5rem 1rem !important;
        }
        
        .navbar.navbar-expand-lg.nav-scrolled {
            top: 0 !important;
            padding: 0.5rem 1rem !important;
        }

        .navbar-collapse {
            background: #1a0d06 !important;
            padding: 15px !important;
            border-radius: 16px !important;
            margin-top: 10px !important;
            border: 1px solid rgba(217, 119, 6, 0.25) !important;
        }
    }
    
    .navbar .navbar-brand {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        text-decoration: none !important;
        font-size: 1.125rem !important;
        transition: transform 0.3s ease !important;
    }

    .navbar .navbar-brand:hover {
        transform: scale(1.03) !important;
    }
    
    /* PillNav Item Styling */
    .navbar .nav-link {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 700 !important;
        color: #f1f5f9 !important;
        transition: all 0.25s ease !important;
        text-decoration: none !important;
        padding: 0.45rem 1.1rem !important;
        font-size: 0.88rem !important;
        border-radius: 9999px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        margin: 0 2px !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .navbar .nav-link i {
        font-size: 0.9rem !important;
        opacity: 0.85 !important;
        transition: transform 0.25s ease !important;
    }
    
    .navbar .nav-link:hover i {
        transform: translateY(-2px) !important;
        opacity: 1 !important;
    }
    
    .navbar .nav-link:hover {
        color: #ffffff !important;
        background: #d97706 !important;
        box-shadow: 0 4px 15px rgba(217, 119, 6, 0.4) !important;
    }

    .navbar .nav-link.active,
    .navbar .nav-item.active .nav-link {
        color: #ffffff !important;
        background: #d97706 !important;
        box-shadow: 0 4px 15px rgba(217, 119, 6, 0.4) !important;
    }
    
    .navbar .btn-outline-primary {
        font-family: 'Inter', sans-serif !important;
        font-weight: 600 !important;
        border-radius: 25px !important;
        border: 1.5px solid #2563eb !important;
        color: #2563eb !important;
        padding: 0.5rem 1.3rem !important;
        text-decoration: none !important;
        font-size: 0.875rem !important;
        background: rgba(37, 99, 235, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.05) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    
    .navbar .btn-outline-primary:hover {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3) !important;
        transform: translateY(-1px) !important;
    }
    
    /* Ensure navbar toggler works properly */
    .navbar-toggler {
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        border-radius: 8px !important;
        background: rgba(255, 255, 255, 0.35) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    }
    
    .navbar-toggler:focus {
        box-shadow: none !important;
    }
    
    /* Remove any bullet points from navigation items */
    .navbar-nav li,
    .navbar-nav li::before,
    .nav-item,
    .nav-item::before {
        list-style: none !important;
        content: none !important;
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
    
    .navbar-nav {
        list-style: none !important;
        padding-left: 0 !important;
    }
    
    /* Dropdown menu styles */
    .navbar .dropdown-menu {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.75) 100%) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.45) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(31, 38, 135, 0.08), 
                    inset 0 1px 0 rgba(255, 255, 255, 0.5) !important;
        padding: 8px 0 !important;
        margin-top: 12px !important;
        min-width: 220px !important;
        overflow: hidden !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    
    .navbar .dropdown-item {
        font-family: 'Inter', sans-serif !important;
        font-weight: 550 !important;
        color: #334155 !important;
        padding: 10px 18px !important;
        transition: all 0.25s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        font-size: 0.875rem !important;
    }
    
    .navbar .dropdown-item:hover {
        background: rgba(37, 99, 235, 0.08) !important;
        color: #2563eb !important;
        padding-left: 22px !important;
    }
    
    .navbar .dropdown-item i {
        width: 16px !important;
        text-align: center !important;
        font-size: 14px !important;
        color: #64748b !important;
        transition: color 0.25s ease !important;
    }
    
    .navbar .dropdown-item:hover i {
        color: #2563eb !important;
    }
    
    .navbar .dropdown-divider {
        margin: 6px 0 !important;
        border-top: 1px solid rgba(255, 255, 255, 0.4) !important;
    }
    
    .navbar .dropdown-toggle::after {
        margin-left: 6px !important;
        font-size: 12px !important;
    }
    
    /* Mobile dropdown improvements */
    @media (max-width: 991px) {
        .navbar .dropdown-menu {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
            margin-top: 6px !important;
            padding-left: 10px !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border-radius: 12px !important;
        }
        
        .navbar .dropdown-item {
            padding: 8px 12px !important;
            border-radius: 8px !important;
            margin: 2px 0 !important;
            color: #475569 !important;
        }
        
        .navbar .dropdown-item:hover {
            background-color: rgba(37, 99, 235, 0.1) !important;
            color: #2563eb !important;
            padding-left: 16px !important;
        }
    }

    /* Faculty Mega Menu Styles */
    .mega-dropdown {
        position: relative !important;
    }
    
    .navbar .mega-menu {
        width: 360px !important;
        left: 50% !important;
        transform: translateX(-50%) translateY(15px) !important;
        padding: 15px !important;
        border-radius: 16px !important;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.92) 100%) !important;
        box-shadow: 0 15px 35px rgba(31, 38, 135, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        display: block !important;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .navbar .mega-dropdown:hover .mega-menu,
    .navbar .mega-dropdown.show .mega-menu,
    .navbar .mega-menu.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateX(-50%) translateY(0) !important;
    }

    @media (max-width: 991px) {
        .navbar .mega-menu {
            width: 100% !important;
            transform: none !important;
            left: 0 !important;
            position: relative !important;
            box-shadow: none !important;
            background: rgba(255, 255, 255, 0.05) !important;
            padding: 10px !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: none !important;
            border: none !important;
        }
        
        .navbar .mega-dropdown.show .mega-menu {
            display: block !important;
        }
    }
    
    .mega-menu-grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .mega-menu-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 8px;
        padding-left: 8px;
    }
    
    .mega-menu-list {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .mega-menu-link {
        display: flex !important;
        align-items: center;
        gap: 12px;
        padding: 10px 12px !important;
        border-radius: 10px !important;
        text-decoration: none !important;
        color: #334155 !important;
        transition: all 0.2s ease !important;
    }
    
    .mega-menu-link:hover {
        background: rgba(16, 185, 129, 0.08) !important;
        color: #059669 !important;
        transform: translateX(4px);
    }
    
    .mega-menu-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    
    .mega-menu-link:hover .mega-menu-icon {
        background: #10b981;
        color: #ffffff;
    }
    
    .mega-menu-text {
        display: flex;
        flex-direction: column;
    }
    
    .mega-menu-item-title {
        font-size: 0.88rem;
        font-weight: 600;
        line-height: 1.2;
    }
    
    .mega-menu-item-desc {
        font-size: 0.72rem;
        color: #64748b;
    }
    
    .mega-menu-link:hover .mega-menu-item-desc {
        color: #059669;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid px-lg-5">
        <!-- Logo + Title -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="logo.png" alt="SRKR Engineering College" 
                 onerror="this.style.display='none'" 
                 style="height:45px; margin-right:10px;">
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                
                <!-- Academics Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="academicsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-graduation-cap"></i> Academics
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="academicsDropdown">
                        <li><a class="dropdown-item" href="btech-cse.php"><i class="fas fa-book me-2"></i> B.Tech CSD</a></li>
                        <li><a class="dropdown-item" href="btech-it.php"><i class="fas fa-laptop-code me-2"></i> B.Tech CSIT</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="academic-calendar.php"><i class="fas fa-calendar-check me-2"></i> Academic Calendar</a></li>
                        <li><a class="dropdown-item" href="syllabus.php"><i class="fas fa-clipboard-list me-2"></i> Syllabus</a></li>
                    </ul>
                </li>

                <!-- Faculty Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="faculty.php" id="facultyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chalkboard-teacher"></i> Faculty
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="facultyDropdown">
                        <li><a class="dropdown-item" href="faculty.php#hod"><i class="fas fa-user-shield me-2"></i> Heads of Department</a></li>
                        <li><a class="dropdown-item" href="faculty.php#csd"><i class="fas fa-laptop-code me-2"></i> CSD Faculty</a></li>
                        <li><a class="dropdown-item" href="faculty.php#csit"><i class="fas fa-microchip me-2"></i> CSIT Faculty</a></li>
                    </ul>
                </li>

                <!-- Placements -->
                <li class="nav-item">
                    <a class="nav-link" href="placements.php">
                        <i class="fas fa-briefcase"></i> Placements
                    </a>
                </li>

                <!-- Clubs & Activities Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="clubsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users"></i> Clubs
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="clubsDropdown">
                        <li><a class="dropdown-item" href="startup_club.php"><i class="fas fa-rocket me-2"></i> Startup Club</a></li>
                        <li><a class="dropdown-item" href="sdc_club.php"><i class="fas fa-code me-2"></i> SDC</a></li>
                        <li><a class="dropdown-item" href="swecha_club.php"><i class="fab fa-linux me-2"></i> Swecha</a></li>
                    </ul>
                </li>

                <!-- Houses Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="housesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-trophy"></i> Houses
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="housesDropdown">
                        <li><a class="dropdown-item" href="houses_dashboard.php"><i class="fas fa-trophy me-2"></i> House Activities</a></li>
                        <li><a class="dropdown-item" href="events_overview.php"><i class="fas fa-calendar-alt me-2"></i> Events</a></li>
                        <li><a class="dropdown-item" href="section_house_points_detail.php"><i class="fas fa-layer-group me-2"></i> Section</a></li>
                    </ul>
                </li>

                <!-- Student Portal or Dashboard -->
                <?php if (!empty($_SESSION['faculty_logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="faculty_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger ms-lg-2 px-3" href="logout.php">
                            <i class="fas fa-power-off"></i> Logout
                        </a>
                    </li>
                <?php elseif (!empty($_SESSION['hod_logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="hod_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger ms-lg-2 px-3" href="logout.php">
                            <i class="fas fa-power-off"></i> Logout
                        </a>
                    </li>
                <?php elseif (!empty($_SESSION['student_logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="student_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger ms-lg-2 px-3" href="logout.php">
                            <i class="fas fa-power-off"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="students_overview.php">
                            <i class="fas fa-user-graduate"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-2 px-3" href="login.php" style="border-radius: 20px;">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nav = document.querySelector('.navbar');
        if (!nav) return;
        
        let isScrolled = false;
        let ticking = false;
        
        function checkScroll() {
            const shouldBeScrolled = window.scrollY > 20;
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                if (isScrolled) {
                    nav.classList.add('nav-scrolled');
                } else {
                    nav.classList.remove('nav-scrolled');
                }
            }
            ticking = false;
        }
        
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(checkScroll);
                ticking = true;
            }
        }, { passive: true });
        
        checkScroll();
    });
</script>
