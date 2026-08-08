<?php 
if (session_status() == PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary: #d97706;
    --primary-light: #f59e0b;
    --amber-gold: #d97706;
    --rich-espresso: #1a0d06;
    --cream-white: #fdfbf7;
    --text-primary: #1a0d06;
    --text-secondary: #6f5f54;
    --border-light: #f3eae1;
}

body {
    font-family: 'Inter', sans-serif;
    background: #fdfbf7;
    color: #1a0d06;
    line-height: 1.6;
}

.hero-banner {
    background: linear-gradient(135deg, #1a0d06 0%, #2a150a 50%, #3d1e0e 100%);
    color: white;
    padding: 85px 20px 65px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(230, 194, 128, 0.15) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.6;
    pointer-events: none;
}

.hero-tag {
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.12);
    padding: 6px 18px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 15px;
    border: 1px solid rgba(245, 158, 11, 0.25);
}

.hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 3.2rem;
    font-weight: 900;
    line-height: 1.15;
    margin-bottom: 18px;
}

.hero-title span {
    color: #f59e0b;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #f3eae1;
    border-radius: 20px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(180, 83, 9, 0.06);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: #d97706;
}

.stat-number {
    font-family: 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 900;
    color: #d97706;
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1a0d06;
    margin-top: 4px;
}

.internship-card {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-radius: 24px;
    padding: 30px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.internship-card:hover {
    transform: translateY(-8px);
    border-color: #d97706;
    box-shadow: 0 20px 40px rgba(217, 119, 6, 0.15);
}

.company-logo-badge {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: #fdfbf7;
    border: 1px solid #f3eae1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #d97706;
    margin-bottom: 20px;
}
</style>

<?php include "nav.php"; ?>

<!-- Hero Section -->
<section class="hero-banner">
    <div class="container position-relative z-1">
        <span class="hero-tag"><i class="fas fa-laptop-code me-2"></i> Industry Experience</span>
        <h1 class="hero-title">Student <span>Internships</span> & Training</h1>
        <p class="lead mx-auto" style="max-width: 680px; color: #e5d5c5; font-size: 1.15rem; line-height: 1.6;">
            Building real-world engineering skills through corporate internships, paid industrial stipends, and pre-placement training programs.
        </p>
    </div>
</section>

<!-- Stats Highlight -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">120+</div>
                    <div class="stat-label">Students Interning</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">₹50K</div>
                    <div class="stat-label">Highest Stipend / Mo</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">85%</div>
                    <div class="stat-label">PPO Conversion</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">45+</div>
                    <div class="stat-label">Corporate Partners</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Internship Opportunities -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f3eae1; border-bottom: 1px solid #f3eae1;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Active Internships</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">Featured <span style="color: #d97706;">Internship Streams</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 600px; margin: 0 auto;">Top domain streams and industrial roles offered to CSD & CSIT students.</p>
        </div>

        <div class="row g-4">
            <!-- Internship 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fab fa-amazon"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹45,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Software Development Intern</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Amazon Development Center</p>
                    <p class="text-secondary small mb-4">Hands-on experience with cloud architecture, AWS Lambda microservices, distributed system pipelines, and automated testing.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 6 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> PPO Included</span>
                    </div>
                </div>
            </div>

            <!-- Internship 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹35,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Full Stack Web Developer</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">TCS Innovation Labs</p>
                    <p class="text-secondary small mb-4">Building responsive enterprise web applications using modern React, Node.js, REST APIs, and database engineering.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 3 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Pre-Placement</span>
                    </div>
                </div>
            </div>

            <!-- Internship 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fas fa-brain"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹50,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Data Science & AI Intern</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Wipro Digital AI</p>
                    <p class="text-secondary small mb-4">Building predictive machine learning models, natural language processing pipelines, and data analytics dashboards.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 6 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Stipend + PPO</span>
                    </div>
                </div>
            </div>

            <!-- Internship 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹40,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Cybersecurity Analyst Intern</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Infosys Security Operations</p>
                    <p class="text-secondary small mb-4">Vulnerability assessment, penetration testing, network threat monitoring, and enterprise security compliance.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 4 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Certificate</span>
                    </div>
                </div>
            </div>

            <!-- Internship 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fas fa-cloud"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹38,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Cloud & DevOps Engineer Intern</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Cognizant Technology Solutions</p>
                    <p class="text-secondary small mb-4">Containerization with Docker, Kubernetes orchestration, CI/CD pipeline automation, and cloud infrastructure.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 6 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Full Time Offer</span>
                    </div>
                </div>
            </div>

            <!-- Internship 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="internship-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="company-logo-badge mb-0">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹30,000 / mo</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Mobile App Developer Intern</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Accenture Innovation Hub</p>
                    <p class="text-secondary small mb-4">Flutter & Android app development, UI/UX optimization, state management, and API integration.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-clock me-1"></i> 3 Months</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Stipend</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
</body>
</html>
