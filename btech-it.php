<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<style>
:root {
    --primary: #d97706;
    --primary-light: #f59e0b;
    --amber-gold: #d97706;
    --bright-yellow: #f59e0b;
    --golden-champagne: #e6c280;
    --amber-badge: #b45309;
    --rich-espresso: #1a0d06;
    --cream-white: #fdfbf7;
    --text-primary: #1a0d06;
    --text-secondary: #6f5f54;
    --border-light: #f3eae1;
}

body {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    background: #fdfbf7;
    color: #1a0d06;
    overflow-x: hidden;
}

/* Placement Theme Hero Section */
.hero-section {
    background: linear-gradient(135deg, #1a0d06 0%, #2a150a 50%, #3d1e0e 100%);
    color: white;
    padding: 85px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(230, 194, 128, 0.15) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.6;
    pointer-events: none;
}

@keyframes floatLaptop {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(-3deg); }
}

.hero-icon-container {
    width: 130px;
    height: 130px;
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(14px);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(230, 194, 128, 0.3);
    animation: floatLaptop 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.it-card {
    background: white;
    border-radius: 28px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
    margin-bottom: 25px;
    transition: all 0.35s ease;
    border: 1px solid #f3eae1;
}

.it-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 40px rgba(217, 119, 6, 0.15);
    border-color: rgba(217, 119, 6, 0.3);
}

.semester-tab {
    background: #ffffff;
    border: 1px solid #f3eae1;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #1a0d06;
}

.semester-tab:hover {
    background: #fdfbf7;
    border-color: #d97706;
    transform: translateX(5px);
}

.semester-tab.active {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: white;
    border-color: #b45309;
    box-shadow: 0 8px 20px rgba(180, 83, 9, 0.25);
}

.subject-list {
    display: none;
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-top: 15px;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
    border: 1px solid #f3eae1;
}

.subject-list.active {
    display: block;
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(251, 191, 36, 0.3);">
                        <i class="fas fa-laptop-code" style="margin-right: 6px;"></i>Undergraduate Degree Program
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">CS & Information Technology</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #e5d5c5; max-width: 650px;">4-Year B.Tech Program | AICTE Approved | Industry Focused | Software Engineering & IT Systems</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-laptop-code" style="font-size: 60px; color: #fbbf24; filter: drop-shadow(0 0 15px rgba(251, 191, 36, 0.6));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Overview -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="it-card">
                        <h2 style="color: #1a0d06; margin-bottom: 20px; font-family: 'Outfit', sans-serif; font-weight: 800;">Program Overview</h2>
                        <p style="color: #6f5f54; line-height: 1.8; margin-bottom: 20px;">
                            The B.Tech in Information Technology program is designed to prepare students for the rapidly evolving 
                            IT industry. The curriculum focuses on software development, system administration, network management, 
                            database technologies, and emerging fields like cloud computing and cybersecurity.
                        </p>
                        <p style="color: #6f5f54; line-height: 1.8; margin-bottom: 20px;">
                            Our program emphasizes practical learning through industry projects, internships, and hands-on laboratory 
                            sessions. Students gain expertise in modern technologies and frameworks used in the IT industry.
                        </p>
                        <div style="background: #fdfbf7; padding: 20px; border-radius: 16px; margin-top: 20px; border: 1px solid #f3eae1;">
                            <h5 style="color: #1a0d06; margin-bottom: 15px; font-weight: 700;">Program Highlights</h5>
                            <ul style="color: #6f5f54; margin: 0;">
                                <li>Industry-oriented curriculum with latest IT trends</li>
                                <li>Emphasis on practical and project-based learning</li>
                                <li>Strong foundation in software and hardware technologies</li>
                                <li>Industry partnerships and guest lectures</li>
                                <li>Excellent placement record in IT companies</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: linear-gradient(135deg, #1a0d06 0%, #3d1e0e 100%); color: white; padding: 30px; border-radius: 24px; text-align: center; margin-bottom: 30px; border: 1px solid #f3eae1; box-shadow: 0 10px 30px rgba(180, 83, 9, 0.15);">
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #f59e0b; margin-bottom: 20px;">Program Details</h4>
                        <div style="margin: 20px 0; padding-bottom: 15px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 5px;">Duration</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">4 Years (8 Semesters)</p>
                        </div>
                        <div style="margin: 20px 0; padding-bottom: 15px; border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 5px;">Total Credits</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">160 Credits</p>
                        </div>
                        <div style="margin: 20px 0;">
                            <h6 style="color: #e6c280; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 5px;">Intake</h6>
                            <p style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #ffffff;">120 Students</p>
                        </div>
                    </div>
                    
                    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Admission Requirements</h5>
                        <ul style="color: #64748b; margin: 0;">
                            <li>12th grade with Physics, Chemistry & Mathematics</li>
                            <li>Minimum 75% in 12th grade</li>
                            <li>Valid JEE Main score</li>
                            <li>State entrance exam score (if applicable)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Curriculum -->
    <section style="padding: 60px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">Curriculum Structure</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemesterIT('sem1')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 1 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem1">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Mathematics I</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Physics</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Chemistry</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Programming in C</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">English Communication</li>
                            <li style="padding: 10px;">Computer Fundamentals</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem2')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 2 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem2">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Mathematics II</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Environmental Science</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Programming in Java</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Digital Electronics</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Basic Electrical Engineering</li>
                            <li style="padding: 10px;">IT Workshop</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem3')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 3 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem3">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Data Structures</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Computer Organization</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Discrete Mathematics</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Object Oriented Programming</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Database Management Systems</li>
                            <li style="padding: 10px;">Web Technologies</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem4')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 4 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem4">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Software Engineering</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Operating Systems</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Computer Networks</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">System Programming</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Theory of Computation</li>
                            <li style="padding: 10px;">Network Programming</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemesterIT('sem5')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 5 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem5">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Information Security</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Distributed Systems</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Data Mining</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Mobile Computing</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective I</li>
                            <li style="padding: 10px;">Mini Project</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem6')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 6 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem6">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Cloud Computing</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Software Project Management</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Enterprise Resource Planning</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Network Security</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective II</li>
                            <li style="padding: 10px;">Industry Training</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem7')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 7 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem7">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Artificial Intelligence</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">DevOps and Automation</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Internet of Things</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective III</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective IV</li>
                            <li style="padding: 10px;">Major Project I</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemesterIT('sem8')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 8 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem8">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Industry Project</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Advanced Elective</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Technical Seminar</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Major Project II</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Professional Skills</li>
                            <li style="padding: 10px;">Comprehensive Viva</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- IT Specializations -->
    <section style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">IT Specialization Areas</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="it-card" style="border-left: 5px solid #10b981;">
                        <i class="fas fa-cloud" style="font-size: 40px; color: #10b981; margin-bottom: 20px;"></i>
                        <h5>Cloud Computing & DevOps</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">AWS, Azure, Docker, Kubernetes, CI/CD</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Cloud Architecture</li>
                            <li>Container Technologies</li>
                            <li>Infrastructure as Code</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="it-card" style="border-left: 5px solid #3b82f6;">
                        <i class="fas fa-globe" style="font-size: 40px; color: #3b82f6; margin-bottom: 20px;"></i>
                        <h5>Web & Mobile Development</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">Full Stack Development, React, Angular, Flutter</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Frontend Frameworks</li>
                            <li>Backend Development</li>
                            <li>Mobile App Development</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="it-card" style="border-left: 5px solid #f59e0b;">
                        <i class="fas fa-network-wired" style="font-size: 40px; color: #f59e0b; margin-bottom: 20px;"></i>
                        <h5>Network & System Administration</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">Network Management, System Security, Infrastructure</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Network Design</li>
                            <li>System Administration</li>
                            <li>IT Infrastructure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Laboratory Facilities -->
    <section style="padding: 60px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">Laboratory Facilities</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="it-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Software Development Labs</h5>
                        <ul style="color: #64748b; margin: 0;">
                            <li>Programming Languages Lab</li>
                            <li>Web Development Lab</li>
                            <li>Mobile App Development Lab</li>
                            <li>Database Management Lab</li>
                            <li>Software Testing Lab</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="it-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Infrastructure Labs</h5>
                        <ul style="color: #64748b; margin: 0;">
                            <li>Network Configuration Lab</li>
                            <li>Cloud Computing Lab</li>
                            <li>Cybersecurity Lab</li>
                            <li>System Administration Lab</li>
                            <li>IoT and Embedded Systems Lab</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Career Prospects -->
    <section style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">Career Opportunities</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="it-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Job Roles</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Software Developer</li>
                                    <li>System Administrator</li>
                                    <li>Network Engineer</li>
                                    <li>Cloud Architect</li>
                                    <li>DevOps Engineer</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Database Administrator</li>
                                    <li>IT Consultant</li>
                                    <li>Project Manager</li>
                                    <li>Security Analyst</li>
                                    <li>Technical Support</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="it-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Industry Sectors</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>IT Services</li>
                                    <li>Software Companies</li>
                                    <li>Telecommunications</li>
                                    <li>Banking & Finance</li>
                                    <li>E-commerce</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Healthcare IT</li>
                                    <li>Government Agencies</li>
                                    <li>Manufacturing</li>
                                    <li>Consulting Firms</li>
                                    <li>Startups</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include "footer.php"; ?>

    <script>
        function toggleSemesterIT(semId) {
            // Hide all semester content
            const allSemesters = document.querySelectorAll('.subject-list');
            const allTabs = document.querySelectorAll('.semester-tab');
            
            allSemesters.forEach(sem => sem.classList.remove('active'));
            allTabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected semester
            const selectedSem = document.getElementById(semId);
            const selectedTab = event.currentTarget;
            
            if (selectedSem.classList.contains('active')) {
                selectedSem.classList.remove('active');
                selectedTab.classList.remove('active');
            } else {
                selectedSem.classList.add('active');
                selectedTab.classList.add('active');
            }
        }
    </script>
</body>
</html>
