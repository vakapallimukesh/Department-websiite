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

.faculty-card {
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

.faculty-card:hover {
    transform: translateY(-8px);
    border-color: #d97706;
    box-shadow: 0 20px 40px rgba(217, 119, 6, 0.15);
}

.badge-icon {
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
        <span class="hero-tag"><i class="fas fa-chalkboard-teacher me-2"></i> Faculty Hall of Excellence</span>
        <h1 class="hero-title">Faculty <span>Achievements</span></h1>
        <p class="lead mx-auto" style="max-width: 680px; color: #e5d5c5; font-size: 1.15rem; line-height: 1.6;">
            Honoring research contributions, funded projects, patents, published textbooks, and academic honors of CSD & CSIT professors.
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
                    <div class="stat-label">Journal Papers</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">₹85L+</div>
                    <div class="stat-label">Research Grants</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">12+</div>
                    <div class="stat-label">Patents Granted</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Textbooks & Books</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Faculty Achievements -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f3eae1; border-bottom: 1px solid #f3eae1;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Academic Leadership</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">Faculty <span style="color: #d97706;">Milestones</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 600px; margin: 0 auto;">Distinguished research accomplishments and professional recognition.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-award"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">State Honor</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Best Teacher Award</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Dr. Suresh Babu Mudunuri (HOD CSD)</p>
                    <p class="text-secondary small mb-4">Awarded Best Teacher Award by Government of Andhra Pradesh for outstanding contribution to technical education and AI research.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-university me-1"></i> Govt Award</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> State Awardee</span>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">₹35 Lakhs Grant</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">DST Research Grant</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Dr. G. Mahesh (HOD CSIT)</p>
                    <p class="text-secondary small mb-4">Sanctioned major research grant from Department of Science & Technology (DST) for Smart Agriculture IoT project.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-flask me-1"></i> DST Funded</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Active Project</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Patent Granted</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Granted International Patent</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Dr. K. Srinivas (Professor)</p>
                    <p class="text-secondary small mb-4">Granted International Patent for "AI-Assisted Medical Image Segmentation Framework for Early Cancer Detection".</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-globe me-1"></i> International</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Patent Granted</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Springer Publication</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Textbook Author</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Prof. M. V. Rama Rao</p>
                    <p class="text-secondary small mb-4">Authored textbook titled "Advanced Data Structures & Algorithms with Python" published by Springer International.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-book-open me-1"></i> Springer Text</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Published</span>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Keynote Speaker</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">International Keynote</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Dr. P. V. S. R. V. Prasada Rao</p>
                    <p class="text-secondary small mb-4">Delivered Keynote Address on "Generative AI in Cyber Defence" at IEEE International Conference in Singapore.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-plane-departure me-1"></i> Singapore</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Keynote Delivered</span>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="faculty-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Consultancy</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Industry Consultancy</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">CSD & CSIT Faculty Team</p>
                    <p class="text-secondary small mb-4">Completed ₹15 Lakhs corporate software consultancy project for Smart City Traffic Optimization system.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-briefcase me-1"></i> Corporate Project</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Delivered</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
</body>
</html>
