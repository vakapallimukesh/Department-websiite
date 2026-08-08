<?php 
if (session_status() == PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dome-gallery.css">

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
    --text-light: #94a3b8;
    --bg-light: #fdfbf7;
    --border-light: #f3eae1;
    --white: #ffffff;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 10px 30px rgba(180, 83, 9, 0.08);
    --shadow-lg: 0 20px 45px rgba(180, 83, 9, 0.16);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #fdfbf7;
    line-height: 1.6;
    color: #1a0d06;
    font-size: 14px;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #1a0d06 0%, #2a150a 50%, #3d1e0e 100%);
    color: white;
    padding: 85px 20px 65px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(230, 194, 128, 0.15) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.45;
}

.hero-tag {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.12);
    padding: 6px 20px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 16px;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: clamp(2.4rem, 5vw, 3.6rem);
    font-weight: 900;
    line-height: 1.15;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #ffffff 0%, #f5ebe6 35%, #e6c280 70%, #d49b59 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.15rem;
    font-weight: 400;
    color: #e5d5c5;
    max-width: 680px;
    margin: 0 auto;
}

/* Stats Section */
.stats-section {
    padding: 55px 0;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}

.stat-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 28px 20px;
    text-align: center;
    border: 1px solid #f3eae1;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
}

.stat-card:hover {
    box-shadow: 0 20px 45px rgba(180, 83, 9, 0.16);
    transform: translateY(-4px);
    border-color: rgba(217, 119, 6, 0.4);
}

.stat-number {
    font-family: 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 4px;
}

.stat-label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: #1a0d06;
    margin-bottom: 3px;
}

.stat-sublabel {
    font-size: 0.84rem;
    color: #6f5f54;
    font-weight: 500;
}

/* Recruiters Slide Section (Top Placement) */
.recruiters-section {
    padding: 60px 0;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}

/* Currently Working on Internship Section */
.active-internship-section {
    padding: 70px 0;
    background: #fdfbf7;
}

.section-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    text-align: center;
    margin-bottom: 12px;
    color: #1a0d06;
}

.company-announcement-card {
    background: linear-gradient(135deg, #1a0d06 0%, #2a150a 100%);
    border-radius: 24px;
    padding: 35px 30px;
    color: white;
    box-shadow: 0 15px 40px rgba(26, 13, 6, 0.25);
    border: 1px solid rgba(230, 194, 128, 0.2);
    margin-bottom: 45px;
    position: relative;
    overflow: hidden;
}

.company-announcement-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.student-card {
    background: #ffffff;
    border: 1px solid #f3eae1;
    border-radius: 20px;
    padding: 24px 20px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.student-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(217, 119, 6, 0.15);
    border-color: rgba(217, 119, 6, 0.4);
}

.student-photo-wrapper {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    padding: 5px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
    box-shadow: 0 8px 20px rgba(217, 119, 6, 0.25);
    margin-bottom: 18px;
    position: relative;
}

.student-photo {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: #ffffff;
}

.student-name {
    font-family: 'Outfit', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    color: #1a0d06;
    margin-bottom: 4px;
    line-height: 1.3;
}

.student-roll {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    color: #d97706;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.student-class {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6f5f54;
    background: #fdfbf7;
    padding: 3px 12px;
    border-radius: 12px;
    border: 1px solid #f3eae1;
    display: inline-block;
    margin-bottom: 14px;
}

.student-role-tag {
    font-size: 0.78rem;
    font-weight: 700;
    color: #166534;
    background: #dcfce7;
    padding: 4px 14px;
    border-radius: 20px;
    margin-top: auto;
    width: 100%;
}

.fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative z-1">
            <span class="hero-tag fade-in"><i class="fas fa-laptop-code me-2"></i> Industry Experience & Corporate Placements</span>
            <h1 class="hero-title fade-in">Student <span>Internships</span> & Training</h1>
            <p class="hero-subtitle fade-in">
                Building real-world engineering skills through corporate internships, paid industrial stipends, and pre-placement training programs.
            </p>
        </div>
    </section>

    <!-- Top Internship Recruiters & Partners Slide Section (MOVED TO TOP) -->
    <section class="recruiters-section">
        <div class="container">
            <div class="text-center mb-4">
                <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">OUR RECRUITERS</span>
                <h2 class="section-title">Companies Providing <span style="color: #d97706;">Internships</span></h2>
                <p style="color: #6f5f54; font-size: 1.05rem; max-width: 600px; margin: 0 auto;">Leading multinational & tech enterprises offering internship opportunities to CSD & CSIT students.</p>
            </div>

            <!-- ReactBits Interactive Circular Gallery for Top Recruiters -->
            <div id="topRecruitersCircularGallery" style="height: 520px; width: 100%; position: relative; overflow: hidden; background: #ffffff; border-radius: 24px; border: 1px solid #f3eae1; box-shadow: 0 15px 40px rgba(0,0,0,0.07); margin-top: 10px;"></div>
        </div>
    </section>

    <!-- Currently Working on Internship Section -->
    <section class="active-internship-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">ACTIVE INTERNSHIPS</span>
                <h2 class="section-title">Currently Working on <span style="color: #d97706;">Internships</span></h2>
                <p style="color: #6f5f54; font-size: 1.08rem; max-width: 650px; margin: 0 auto;">
                    Hearty congratulations to our CSD & CSIT students selected for prestigious industrial internships.
                </p>
            </div>

            <!-- Featured Announcement Banner (Headline & Data Only, No Poster View) -->
            <div class="company-announcement-card fade-in">
                <div class="row align-items-center">
                    <div class="col-12 text-center text-md-start">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3" style="font-size: 0.85rem;">SELECTION ANNOUNCEMENT</span>
                        <h3 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; color: #ffffff;">Zennith Digital Tech LLP</h3>
                        <p class="mb-0" style="color: #e5d5c5; font-size: 1.15rem; line-height: 1.6;">
                            Best Wishes to our students for getting selected by <strong>Zennith Digital Tech LLP</strong> as <strong>Software Engineering Interns</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Individual Student Cards Grid -->
            <div class="row g-4 justify-content-center">
                <!-- Student 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="student-card fade-in">
                        <div class="student-photo-wrapper">
                            <img src="assets/images/internships/student_leela_madhav.jpg" alt="N. Leela Madhav Rao" class="student-photo">
                        </div>
                        <h4 class="student-name">N. Leela Madhav Rao</h4>
                        <div class="student-roll">23B91A0738</div>
                        <div class="student-class">3/4 CSIT</div>
                        <div class="student-role-tag"><i class="fas fa-check-circle me-1"></i> Software Engineering Intern</div>
                    </div>
                </div>

                <!-- Student 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="student-card fade-in">
                        <div class="student-photo-wrapper">
                            <img src="assets/images/internships/student_sriram_charan.jpg" alt="K. S. Sriram Charan Teja" class="student-photo">
                        </div>
                        <h4 class="student-name">K. S. Sriram Charan Teja</h4>
                        <div class="student-roll">23B91A0727</div>
                        <div class="student-class">3/4 CSIT</div>
                        <div class="student-role-tag"><i class="fas fa-check-circle me-1"></i> Software Engineering Intern</div>
                    </div>
                </div>

                <!-- Student 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="student-card fade-in">
                        <div class="student-photo-wrapper">
                            <img src="assets/images/internships/student_nikhila_valli.jpg" alt="G. Nikhila Valli" class="student-photo">
                        </div>
                        <h4 class="student-name">G. Nikhila Valli</h4>
                        <div class="student-roll">23B91A0714</div>
                        <div class="student-class">3/4 CSIT</div>
                        <div class="student-role-tag"><i class="fas fa-check-circle me-1"></i> Software Engineering Intern</div>
                    </div>
                </div>

                <!-- Student 4 -->
                <div class="col-lg-4 col-md-6">
                    <div class="student-card fade-in">
                        <div class="student-photo-wrapper">
                            <img src="assets/images/internships/student_manoj_kumar.jpg" alt="G. Manoj Kumar" class="student-photo">
                        </div>
                        <h4 class="student-name">G. Manoj Kumar</h4>
                        <div class="student-roll">23B91A6219</div>
                        <div class="student-class">3/4 CSD</div>
                        <div class="student-role-tag"><i class="fas fa-check-circle me-1"></i> Software Engineering Intern</div>
                    </div>
                </div>

                <!-- Student 5 -->
                <div class="col-lg-4 col-md-6">
                    <div class="student-card fade-in">
                        <div class="student-photo-wrapper">
                            <img src="assets/images/internships/student_uma_sai_pavan.jpg" alt="T. Uma Sai Pavan" class="student-photo">
                        </div>
                        <h4 class="student-name">T. Uma Sai Pavan</h4>
                        <div class="student-roll">24B95A6207</div>
                        <div class="student-class">3/4 CSD</div>
                        <div class="student-role-tag"><i class="fas fa-check-circle me-1"></i> Software Engineering Intern</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Circular Gallery Engine -->
    <script src="assets/js/circular-gallery.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fade in animation observer
        const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    });
    </script>

    <?php include "footer.php"; ?>
</body>
</html>


