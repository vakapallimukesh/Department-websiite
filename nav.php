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
        padding-top: 100px !important;
    }
    
    @media (max-width: 991px) {
        body {
            padding-top: 85px !important;
        }
    }
    
    /* Navigation specific styles with high specificity */
    .navbar.navbar-expand-lg {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.1) 100%) !important;
        backdrop-filter: blur(25px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(25px) saturate(180%) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08), 
                    inset 0 1px 0 0 rgba(255, 255, 255, 0.65), 
                    inset 0 -1px 0 0 rgba(255, 255, 255, 0.15) !important;
        padding: 0.7rem 2rem !important;
        font-family: 'Inter', sans-serif !important;
        border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
        margin: 0 !important;
        position: fixed !important;
        top: 25px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 90% !important;
        max-width: 1200px !important;
        border-radius: 50px !important;
        z-index: 1000 !important;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .navbar.navbar-expand-lg.nav-scrolled {
        top: 12px !important;
        padding: 0.5rem 2rem !important;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0.25) 100%) !important;
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12), 
                    inset 0 1px 0 0 rgba(255, 255, 255, 0.75), 
                    inset 0 -1px 0 0 rgba(255, 255, 255, 0.2) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.5) !important;
    }

    @media (max-width: 991px) {
        .navbar.navbar-expand-lg {
            width: 94% !important;
            top: 15px !important;
            border-radius: 20px !important;
            padding: 0.5rem 1.2rem !important;
        }
        
        .navbar.navbar-expand-lg.nav-scrolled {
            top: 8px !important;
            padding: 0.4rem 1.2rem !important;
        }

        .navbar-collapse {
            padding-top: 12px !important;
            padding-bottom: 8px !important;
        }
    }
    
    .navbar .navbar-brand {
        font-family: 'Inter', sans-serif !important;
        font-weight: 600 !important;
        color: #1a365d !important;
        text-decoration: none !important;
        font-size: 1.125rem !important;
        transition: transform 0.3s ease !important;
    }

    .navbar .navbar-brand:hover {
        transform: scale(1.03) !important;
    }
    
    .navbar .nav-link {
        font-family: 'Inter', sans-serif !important;
        font-weight: 600 !important;
        color: #334155 !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        text-decoration: none !important;
        padding: 0.5rem 1.1rem !important;
        font-size: 0.9rem !important;
        border-radius: 25px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    
    .navbar .nav-link i {
        font-size: 0.95rem !important;
        opacity: 0.8 !important;
        transition: transform 0.3s ease !important;
    }
    
    .navbar .nav-link:hover i {
        transform: translateY(-2px) !important;
    }
    
    .navbar .nav-link:hover {
        color: #2563eb !important;
        background: rgba(255, 255, 255, 0.45) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02), inset 0 1px 0 rgba(255, 255, 255, 0.5) !important;
    }

    .navbar .nav-link.active {
        color: #1d4ed8 !important;
        background: rgba(255, 255, 255, 0.65) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
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
</style>

<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
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
                        <li><a class="dropdown-item" href="btech-cse.php"><i class="fas fa-book"></i> B.Tech CSD</a></li>
                        <li><a class="dropdown-item" href="btech-it.php"><i class="fas fa-laptop-code"></i> B.Tech CSIT</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="academic-calendar.php"><i class="fas fa-calendar-check"></i> Academic Calendar</a></li>
                        <li><a class="dropdown-item" href="syllabus.php"><i class="fas fa-clipboard-list"></i> Syllabus</a></li>
                    </ul>
                </li>

                <!-- Faculty -->
                <li class="nav-item">
                    <a class="nav-link" href="faculty.php">
                        <i class="fas fa-chalkboard-teacher"></i> Faculty
                    </a>
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
                        <li><a class="dropdown-item" href="startup_club.php"><i class="fas fa-rocket"></i> Startup Club</a></li>
                        <li><a class="dropdown-item" href="sdc_club.php"><i class="fas fa-code"></i> SDC</a></li>
                        <li><a class="dropdown-item" href="swecha_club.php"><i class="fab fa-linux"></i> Swecha</a></li>
                    </ul>
                </li>

                <!-- Houses -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="housesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-trophy"></i> Houses
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="housesDropdown">
                        <li><a class="dropdown-item" href="houses_dashboard.php"><i class="fas fa-trophy"></i> House Activities</a></li>
                        <li><a class="dropdown-item" href="events_overview.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a class="dropdown-item" href="section_house_points_detail.php"><i class="fas fa-layer-group"></i> Section</a></li>
                    </ul>
                </li>
                
                    

                <!-- Student Portal (when not logged in) or Dashboard (when logged in) -->
                <?php if (!empty($_SESSION['faculty_logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="faculty_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
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
                        <a class="nav-link" href="logout.php">
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
                        <a class="nav-link" href="logout.php">
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
                        <a class="btn btn-outline-primary px-3" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
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
        
        function checkScroll() {
            if (window.scrollY > 20) {
                nav.classList.add('nav-scrolled');
            } else {
                nav.classList.remove('nav-scrolled');
            }
        }
        
        window.addEventListener('scroll', checkScroll);
        checkScroll();
    });
</script>
