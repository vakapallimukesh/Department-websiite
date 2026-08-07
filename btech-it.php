<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<style>
body {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    background: #f8fafc;
    color: #334155;
    overflow-x: hidden;
}

/* Animated Hero Section */
.hero-section {
    background: linear-gradient(-45deg, #0f172a, #047857, #065f46, #0f172a);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    color: white;
    padding: 85px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.7;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
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
    border: 1px solid rgba(255, 255, 255, 0.18);
    animation: floatLaptop 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.it-card {
    background: white;
    border-radius: 28px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    margin-bottom: 25px;
    transition: all 0.35s ease;
    border: 1px solid #e2e8f0;
}

.it-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 40px rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.3);
}

.semester-tab {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.semester-tab:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.semester-tab.active {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: white;
    border-color: #10b981;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
}

.subject-list {
    display: none;
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-top: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
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
                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(52, 211, 153, 0.3);">
                        <i class="fas fa-laptop-code" style="margin-right: 6px;"></i>Undergraduate Degree Program
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">CS & Information Technology</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #a7f3d0; max-width: 650px;">4-Year B.Tech Program | AICTE Approved | Industry Focused | Software Engineering & IT Systems</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-laptop-code" style="font-size: 60px; color: #34d399; filter: drop-shadow(0 0 15px rgba(52, 211, 153, 0.6));"></i>
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
                        <h2 style="color: #1e293b; margin-bottom: 20px;">Program Overview</h2>
                        <p style="color: #64748b; line-height: 1.8; margin-bottom: 20px;">
                            The B.Tech in Information Technology program is designed to prepare students for the rapidly evolving 
                            IT industry. The curriculum focuses on software development, system administration, network management, 
                            database technologies, and emerging fields like cloud computing and cybersecurity.
                        </p>
                        <p style="color: #64748b; line-height: 1.8; margin-bottom: 20px;">
                            Our program emphasizes practical learning through industry projects, internships, and hands-on laboratory 
                            sessions. Students gain expertise in modern technologies and frameworks used in the IT industry.
                        </p>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-top: 20px;">
                            <h5 style="color: #1e293b; margin-bottom: 15px;">Program Highlights</h5>
                            <ul style="color: #64748b; margin: 0;">
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
                    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; border-radius: 15px; text-align: center; margin-bottom: 30px;">
                        <h4>Program Details</h4>
                        <div style="margin: 25px 0;">
                            <h5>Duration</h5>
                            <p>4 Years (8 Semesters)</p>
                        </div>
                        <div style="margin: 25px 0;">
                            <h5>Total Credits</h5>
                            <p>160 Credits</p>
                        </div>
                        <div style="margin: 25px 0;">
                            <h5>Intake</h5>
                            <p>120 Students</p>
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
