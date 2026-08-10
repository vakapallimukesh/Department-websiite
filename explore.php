<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore SRKREC CSD-CSIT Department</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800;900&family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Premium Styles -->
    <link rel="stylesheet" href="explore-premium.css">
</head>
<body>
    
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="mobile-logo">
            <h2>SRKREC CSD-CSIT</h2>
        </div>
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Animated Background -->
    <div class="premium-background">
        <div class="gradient-blob blob-1"></div>
        <div class="gradient-blob blob-2"></div>
        <div class="gradient-blob blob-3"></div>
        <canvas id="particleCanvas"></canvas>
    </div>

    <!-- Dashboard Container -->
    <div class="explore-dashboard">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar-nav" id="sidebarNav">
            <div class="sidebar-header">
                <h3>Explore</h3>
                <button class="sidebar-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="sidebar-menu">
                <a href="#home" class="menu-item active" data-section="home">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="#academics" class="menu-item" data-section="academics">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Academics</span>
                </a>
                <a href="#faculty" class="menu-item" data-section="faculty">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Faculty</span>
                </a>
                <a href="#placements" class="menu-item" data-section="placements">
                    <i class="fas fa-briefcase"></i>
                    <span>Placements</span>
                </a>
                <a href="#clubs" class="menu-item" data-section="clubs">
                    <i class="fas fa-users"></i>
                    <span>Clubs</span>
                </a>
                <a href="#houses" class="menu-item" data-section="houses">
                    <i class="fas fa-trophy"></i>
                    <span>Houses</span>
                </a>
                <a href="#students" class="menu-item" data-section="students">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="index.php" class="back-home-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Home Section -->
            <section id="home" class="content-section active">
                <div class="section-header" data-aos="fade-down">
                    <h1 class="page-title">Explore SRKREC CSD-CSIT</h1>
                    <p class="page-subtitle">Everything you need to know about the department.</p>
                </div>

                <div class="overview-grid" data-aos="fade-up">
                    <div class="overview-card">
                        <div class="card-icon-circle academics-gradient">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Academics</h3>
                        <p>Comprehensive programs in Computer Science Design and Information Technology</p>
                        <button class="card-nav-btn" data-navigate="academics">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overview-card">
                        <div class="card-icon-circle faculty-gradient">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Faculty</h3>
                        <p>Experienced professors and researchers</p>
                        <button class="card-nav-btn" data-navigate="faculty">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overview-card">
                        <div class="card-icon-circle placements-gradient">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Placements</h3>
                        <p>Outstanding placement records with top recruiters</p>
                        <button class="card-nav-btn" data-navigate="placements">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overview-card">
                        <div class="card-icon-circle clubs-gradient">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Clubs</h3>
                        <p>Active student clubs fostering innovation</p>
                        <button class="card-nav-btn" data-navigate="clubs">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overview-card">
                        <div class="card-icon-circle houses-gradient">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3>Houses</h3>
                        <p>Competitive house system promoting teamwork</p>
                        <button class="card-nav-btn" data-navigate="houses">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overview-card">
                        <div class="card-icon-circle students-gradient">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Students</h3>
                        <p>Achievements and testimonials from our students</p>
                        <button class="card-nav-btn" data-navigate="students">
                            Explore <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Academics Section -->
            <section id="academics" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Academics</h2>
                    <p class="section-description">Comprehensive programs designed for future leaders</p>
                </div>

                <div class="program-grid">
                    <a href="academic-calendar.php" class="program-card" data-aos="fade-right">
                        <div class="program-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="program-info">
                            <h4>B.Tech CSD</h4>
                            <p>Computer Science & Design</p>
                        </div>
                        <i class="fas fa-arrow-right program-arrow"></i>
                    </a>

                    <a href="academic-calendar.php" class="program-card" data-aos="fade-left">
                        <div class="program-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <div class="program-info">
                            <h4>B.Tech CSIT</h4>
                            <p>Computer Science & IT</p>
                        </div>
                        <i class="fas fa-arrow-right program-arrow"></i>
                    </a>

                    <a href="academic-calendar.php" class="program-card" data-aos="fade-right">
                        <div class="program-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="program-info">
                            <h4>Academic Calendar</h4>
                            <p>Important dates & events</p>
                        </div>
                        <i class="fas fa-arrow-right program-arrow"></i>
                    </a>

                    <a href="academic-calendar.php" class="program-card" data-aos="fade-left">
                        <div class="program-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="program-info">
                            <h4>Syllabus</h4>
                            <p>Course structure & content</p>
                        </div>
                        <i class="fas fa-arrow-right program-arrow"></i>
                    </a>
                </div>
            </section>

            <!-- Faculty Section -->
            <section id="faculty" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Faculty</h2>
                    <p class="section-description">Meet our dedicated faculty members</p>
                </div>

                <div class="faculty-grid">
                    <div class="faculty-category-card" data-aos="zoom-in">
                        <div class="category-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Head of Department</h3>
                        <p>Leadership and vision</p>
                        <a href="faculty.php" class="category-link">
                            View Profile <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="faculty-category-card" data-aos="zoom-in">
                        <div class="category-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>CSD Faculty</h3>
                        <p>Computer Science & Design</p>
                        <a href="faculty.php" class="category-link">
                            View Faculty <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="faculty-category-card" data-aos="zoom-in">
                        <div class="category-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>CSIT Faculty</h3>
                        <p>Computer Science & IT</p>
                        <a href="faculty.php" class="category-link">
                            View Faculty <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Placements Section -->
            <section id="placements" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Placements</h2>
                    <p class="section-description">Outstanding career opportunities</p>
                </div>

                <div class="placements-grid">
                    <div class="placement-card" data-aos="flip-left">
                        <div class="placement-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>Top Recruiters</h3>
                        <p>Leading companies hiring our students</p>
                        <div class="placement-stat">50+ Companies</div>
                        <a href="placements.php" class="placement-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="placement-card" data-aos="flip-left">
                        <div class="placement-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Statistics</h3>
                        <p>Year-wise placement data</p>
                        <div class="placement-stat">95% Placed</div>
                        <a href="placements.php" class="placement-link">
                            View Stats <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="placement-card" data-aos="flip-left">
                        <div class="placement-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3>Highest Package</h3>
                        <p>Record-breaking offers</p>
                        <div class="placement-stat">₹45 LPA</div>
                        <a href="placements.php" class="placement-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="placement-card" data-aos="flip-left">
                        <div class="placement-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Training</h3>
                        <p>Industry-focused development</p>
                        <div class="placement-stat">Year Round</div>
                        <a href="placements.php" class="placement-link">
                            Explore <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Clubs Section -->
            <section id="clubs" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Clubs</h2>
                    <p class="section-description">Active student organizations</p>
                </div>

                <div class="clubs-grid">
                    <div class="club-card" data-aos="fade-up">
                        <div class="club-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>Startup Club</h3>
                        <p>Entrepreneurship initiatives</p>
                        <a href="coding-club.php" class="club-link">
                            Join Club <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="club-card" data-aos="fade-up">
                        <div class="club-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3>SDC</h3>
                        <p>Student Developer Club</p>
                        <a href="coding-club.php" class="club-link">
                            Join Club <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="club-card" data-aos="fade-up">
                        <div class="club-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>AI Club</h3>
                        <p>Artificial intelligence & ML</p>
                        <a href="coding-club.php" class="club-link">
                            Join Club <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="club-card" data-aos="fade-up">
                        <div class="club-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>Swecha</h3>
                        <p>Open source movement</p>
                        <a href="coding-club.php" class="club-link">
                            Join Club <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Houses Section -->
            <section id="houses" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Houses</h2>
                    <p class="section-description">Competitive house system fostering teamwork and excellence</p>
                    <div style="margin-top: 15px;">
                        <a href="houses_dashboard.php" class="category-link" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--primary-blue); color: white; border-radius: 10px; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-trophy"></i> Open Houses Dashboard <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="houses-grid">
                    <div class="house-card red-house" data-aos="zoom-in">
                        <div class="house-shield">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Red House (Agni)</h3>
                        <p>Courage & Determination</p>
                        <div class="house-stats">
                            <span class="stat-badge">320 Points</span>
                        </div>
                        <a href="houses_dashboard.php" class="category-link" style="margin-top: 15px; display: inline-block;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="house-card blue-house" data-aos="zoom-in">
                        <div class="house-shield">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Blue House (Jal)</h3>
                        <p>Wisdom & Excellence</p>
                        <div class="house-stats">
                            <span class="stat-badge">285 Points</span>
                        </div>
                        <a href="houses_dashboard.php" class="category-link" style="margin-top: 15px; display: inline-block;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="house-card green-house" data-aos="zoom-in">
                        <div class="house-shield">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Green House (Vayu)</h3>
                        <p>Growth & Harmony</p>
                        <div class="house-stats">
                            <span class="stat-badge">295 Points</span>
                        </div>
                        <a href="houses_dashboard.php" class="category-link" style="margin-top: 15px; display: inline-block;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="house-card yellow-house" data-aos="zoom-in">
                        <div class="house-shield">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Yellow House (Aakash)</h3>
                        <p>Energy & Innovation</p>
                        <div class="house-stats">
                            <span class="stat-badge">310 Points</span>
                        </div>
                        <a href="houses_dashboard.php" class="category-link" style="margin-top: 15px; display: inline-block;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Students Section -->
            <section id="students" class="content-section">
                <div class="section-header" data-aos="fade-down">
                    <h2 class="section-title">Students</h2>
                    <p class="section-description">Celebrating student excellence</p>
                </div>

                <div class="students-grid">
                    <div class="student-card" data-aos="fade-right">
                        <div class="student-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>Achievements</h3>
                        <p>Awards and accomplishments</p>
                        <a href="students_overview.php" class="student-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="student-card" data-aos="fade-left">
                        <div class="student-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Gallery</h3>
                        <p>Photos from events</p>
                        <a href="students_overview.php" class="student-link">
                            Browse <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="student-card" data-aos="fade-right">
                        <div class="student-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3>Projects</h3>
                        <p>Student innovations</p>
                        <a href="students_overview.php" class="student-link">
                            Explore <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>

        </main>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="explore-premium.js"></script>
    <!-- AI Department Assistant Chatbot Component -->
    <?php include_once __DIR__ . '/includes/chatbot.php'; ?>

</body>
</html>

