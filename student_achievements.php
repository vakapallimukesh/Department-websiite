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

.achievement-card {
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

.achievement-card:hover {
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
        <span class="hero-tag"><i class="fas fa-trophy me-2"></i> Student Hall of Fame</span>
        <h1 class="hero-title">Student <span>Achievements</span></h1>
        <p class="lead mx-auto" style="max-width: 680px; color: #e5d5c5; font-size: 1.15rem; line-height: 1.6;">
            Celebrating national hackathon winners, research authors, competitive coders, and student leaders in CSD & CSIT departments.
        </p>
    </div>
</section>

<!-- Stats Highlight -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">45+</div>
                    <div class="stat-label">Hackathon Awards</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">30+</div>
                    <div class="stat-label">Research Papers</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Patent Applications</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Global Certifications</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Student Achievements -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f3eae1; border-bottom: 1px solid #f3eae1;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Excellence Showcase</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">Key <span style="color: #d97706;">Student Highlights</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 600px; margin: 0 auto;">Proud accomplishments by CSD & CSIT students across national & international platforms.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-award"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">National 1st Rank</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Smart India Hackathon Winner</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">Team Agni Innovators (CSD)</p>
                    <p class="text-secondary small mb-4">Secured 1st Prize of ₹1,00,000 for AI-driven smart agriculture disease prediction system developed for Ministry of Agriculture.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-calendar-alt me-1"></i> 2025</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Verified Honor</span>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">IEEE Publication</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">IEEE Xplore Research Author</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">K. Suresh & Team (CSIT)</p>
                    <p class="text-secondary small mb-4">Published research paper titled "Optimized Federated Learning for Edge IoT Networks" in IEEE International Conference.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-globe me-1"></i> Scopus Indexed</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Published</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-code"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Global Rank 152</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">LeetCode Knight Title</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">V. Mukesh (CSD 3rd Year)</p>
                    <p class="text-secondary small mb-4">Achieved 2050+ Rating on LeetCode with 900+ algorithmic problems solved and top rank in Weekly Coding Contests.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-fire me-1"></i> 900+ Solved</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Knight Level</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fab fa-aws"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">AWS Certified</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">AWS Solutions Architect</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">P. Anitha (CSIT 4th Year)</p>
                    <p class="text-secondary small mb-4">Cleared AWS Certified Solutions Architect Professional examination with high distinction score.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-certificate me-1"></i> Global Credential</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Pro Certified</span>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Patent Published</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">IoT Patent Grant</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">S. Rajesh & Team (CSD)</p>
                    <p class="text-secondary small mb-4">Filed and published Indian Patent for "Automated Aquaponics Sensor Network with Machine Learning Diagnostics".</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-file-signature me-1"></i> Govt. Published</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Patent Grant</span>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="achievement-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="badge-icon mb-0">
                            <i class="fas fa-running"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Inter-University</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Sports Gold Medalist</h3>
                    <p class="fw-bold mb-3" style="color: #d97706;">House Prudhvi Captain</p>
                    <p class="text-secondary small mb-4">Won Gold Medal in Inter-University Badminton Tournament and represented JNTUK at National Level sports meet.</p>
                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="fas fa-medal me-1"></i> Gold Winner</span>
                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> State Player</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
</body>
</html>
