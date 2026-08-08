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

@keyframes floatCap {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(3deg); }
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
    animation: floatCap 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.curriculum-card {
    background: white;
    border-radius: 28px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
    margin-bottom: 25px;
    transition: all 0.35s ease;
    border: 1px solid #f3eae1;
}

.curriculum-card:hover {
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
                        <i class="fas fa-graduation-cap" style="margin-right: 6px;"></i>Undergraduate Degree Program
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Computer Science & Design</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #e2e8f0; max-width: 650px;">4-Year B.Tech Program | AICTE Approved | NBA Accredited | Industry-Aligned Curriculum</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-graduation-cap" style="font-size: 60px; color: #fbbf24; filter: drop-shadow(0 0 15px rgba(251, 191, 36, 0.6));"></i>
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
                    <div class="curriculum-card">
                        <h2 style="color: #1a0d06; margin-bottom: 20px; font-family: 'Outfit', sans-serif; font-weight: 800;">Program Overview</h2>
                        <p style="color: #6f5f54; line-height: 1.8; margin-bottom: 20px;">
                            The B.Tech in Computer Science & Engineering is a comprehensive 4-year undergraduate program designed to provide 
                            students with a strong foundation in computer science principles, programming, software development, and emerging technologies.
                        </p>
                        <p style="color: #6f5f54; line-height: 1.8; margin-bottom: 20px;">
                            Our curriculum is industry-aligned and regularly updated to include the latest technologies like AI, Machine Learning, 
                            Cloud Computing, Cybersecurity, and Data Science. Students gain hands-on experience through projects, internships, 
                            and industry collaborations.
                        </p>
                        <div style="background: #fdfbf7; padding: 20px; border-radius: 16px; margin-top: 20px; border: 1px solid #f3eae1;">
                            <h5 style="color: #1a0d06; margin-bottom: 15px; font-weight: 700;">Program Highlights</h5>
                            <ul style="color: #6f5f54; margin: 0;">
                                <li>Industry-relevant curriculum with latest technologies</li>
                                <li>Hands-on learning through labs and projects</li>
                                <li>Industry internships and live projects</li>
                                <li>Research opportunities with faculty</li>
                                <li>Placement assistance with top companies</li>
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
                    <div class="semester-tab" onclick="toggleSemester('sem1')">
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
                            <li style="padding: 10px;">Engineering Drawing</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem2')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 2 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem2">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Mathematics II</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Environmental Science</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Programming in C++</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Digital Logic Design</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Basic Electrical Engineering</li>
                            <li style="padding: 10px;">Professional Ethics</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem3')">
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
                            <li style="padding: 10px;">Software Engineering</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem4')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 4 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem4">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Algorithms Analysis</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Operating Systems</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Computer Networks</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Web Technologies</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Theory of Computation</li>
                            <li style="padding: 10px;">Microprocessors</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="semester-tab" onclick="toggleSemester('sem5')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 5 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem5">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Machine Learning</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Compiler Design</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Computer Graphics</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Artificial Intelligence</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective I</li>
                            <li style="padding: 10px;">Project Work I</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem6')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 6 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem6">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Data Science</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Cloud Computing</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Cybersecurity</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Mobile Application Development</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective II</li>
                            <li style="padding: 10px;">Internship</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem7')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 7 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem7">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Deep Learning</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Blockchain Technology</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">IoT and Embedded Systems</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective III</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Elective IV</li>
                            <li style="padding: 10px;">Major Project I</li>
                        </ul>
                    </div>
                    
                    <div class="semester-tab" onclick="toggleSemester('sem8')">
                        <h5 style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                            Semester 8 
                            <i class="fas fa-chevron-down"></i>
                        </h5>
                    </div>
                    <div class="subject-list" id="sem8">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Industry Project</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Advanced Elective</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Seminar</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Major Project II</li>
                            <li style="padding: 10px; border-bottom: 1px solid #f3f4f6;">Professional Development</li>
                            <li style="padding: 10px;">Comprehensive Viva</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Specializations -->
    <section style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">Specialization Tracks</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="curriculum-card" style="border-left: 5px solid #3b82f6;">
                        <i class="fas fa-brain" style="font-size: 40px; color: #3b82f6; margin-bottom: 20px;"></i>
                        <h5>Artificial Intelligence & Machine Learning</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">Deep Learning, Neural Networks, Computer Vision, NLP</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Advanced ML Algorithms</li>
                            <li>Deep Learning Frameworks</li>
                            <li>AI Ethics and Applications</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="curriculum-card" style="border-left: 5px solid #10b981;">
                        <i class="fas fa-database" style="font-size: 40px; color: #10b981; margin-bottom: 20px;"></i>
                        <h5>Data Science & Analytics</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">Big Data, Data Mining, Business Intelligence, Statistics</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Statistical Analysis</li>
                            <li>Data Visualization</li>
                            <li>Predictive Modeling</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="curriculum-card" style="border-left: 5px solid #f59e0b;">
                        <i class="fas fa-shield-alt" style="font-size: 40px; color: #f59e0b; margin-bottom: 20px;"></i>
                        <h5>Cybersecurity</h5>
                        <p style="color: #64748b; margin-bottom: 15px;">Network Security, Cryptography, Ethical Hacking, Digital Forensics</p>
                        <ul style="color: #64748b; font-size: 0.9rem;">
                            <li>Security Analysis</li>
                            <li>Penetration Testing</li>
                            <li>Risk Management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Career Prospects -->
    <section style="padding: 60px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 50px; color: #1e293b;">Career Prospects</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="curriculum-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Job Roles</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Software Engineer</li>
                                    <li>Data Scientist</li>
                                    <li>AI/ML Engineer</li>
                                    <li>Cybersecurity Analyst</li>
                                    <li>Cloud Architect</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Full Stack Developer</li>
                                    <li>DevOps Engineer</li>
                                    <li>Product Manager</li>
                                    <li>Research Scientist</li>
                                    <li>Entrepreneur</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="curriculum-card">
                        <h5 style="color: #1e293b; margin-bottom: 20px;">Industry Sectors</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Information Technology</li>
                                    <li>Banking & Finance</li>
                                    <li>Healthcare</li>
                                    <li>E-commerce</li>
                                    <li>Gaming</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul style="color: #64748b; margin: 0;">
                                    <li>Telecommunications</li>
                                    <li>Automotive</li>
                                    <li>Government</li>
                                    <li>Education</li>
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
        function toggleSemester(semId) {
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
