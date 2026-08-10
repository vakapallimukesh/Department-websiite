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
}

/* Hero Section matching Placements Page */
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
    margin-bottom: 0.5rem;
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

/* Stats Cards Section */
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

/* Active Internship Student Section */
.active-internship-section {
    padding: 65px 0;
    background: #ffffff;
    border-bottom: 1px solid #f3eae1;
}

.company-announcement-card {
    background: linear-gradient(135deg, #1a0d06 0%, #2a150a 100%);
    border-radius: 24px;
    padding: 35px 30px;
    margin-bottom: 45px;
    border: 1px solid rgba(217, 119, 6, 0.3);
    box-shadow: 0 15px 35px rgba(26, 13, 6, 0.15);
}

.student-card {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(26, 13, 6, 0.04);
    transition: all 0.35s ease;
    height: 100%;
}

.student-card:hover {
    transform: translateY(-6px);
    border-color: #d97706;
    box-shadow: 0 18px 40px rgba(217, 119, 6, 0.15);
}

.student-photo-wrapper {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    margin: 0 auto 18px;
    padding: 5px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 8px 20px rgba(217, 119, 6, 0.25);
}

.student-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    background: #ffffff;
}

.student-name {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
    color: #1a0d06;
    margin-bottom: 4px;
}

.student-roll {
    font-size: 0.88rem;
    font-weight: 700;
    color: #d97706;
    margin-bottom: 2px;
}

.student-class {
    font-size: 0.82rem;
    color: #6f5f54;
    font-weight: 600;
    margin-bottom: 12px;
}

.student-role-tag {
    display: inline-block;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 4px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
}

/* Gallery Section Styling */
.gallery-section {
    padding: 65px 0;
    background: #fdfbf7;
}

.section-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    color: #1a0d06;
}

.gallery-nav-btn {
    background: #ffffff;
    color: #6f5f54;
    border: 1.5px solid #f3eae1;
    padding: 10px 22px;
    border-radius: 999px;
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    font-size: 0.92rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.gallery-nav-btn:hover, .gallery-nav-btn.active {
    background: #1a0d06;
    color: #f59e0b;
    border-color: #1a0d06;
    box-shadow: 0 6px 20px rgba(26, 13, 6, 0.15);
}

.photo-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1.5px solid #f3eae1;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 10px 25px rgba(26, 13, 6, 0.04);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.photo-card:hover {
    transform: translateY(-8px);
    border-color: #d97706;
    box-shadow: 0 20px 45px rgba(217, 119, 6, 0.16);
}

.photo-thumb-wrap {
    position: relative;
    width: 100%;
    padding-top: 62%;
    background: #1a0d06;
    overflow: hidden;
    cursor: pointer;
}

.photo-thumb-wrap img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.photo-card:hover .photo-thumb-wrap img {
    transform: scale(1.06);
}

.photo-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(26, 13, 6, 0) 40%, rgba(26, 13, 6, 0.85) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    padding: 16px;
}

.photo-card:hover .photo-overlay {
    opacity: 1;
}

.zoom-badge {
    background: #f59e0b;
    color: #1a0d06;
    font-weight: 800;
    font-size: 0.8rem;
    padding: 6px 14px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.photo-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.photo-category-tag {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #d97706;
    margin-bottom: 6px;
}

.photo-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: #1a0d06;
    margin-bottom: 8px;
    line-height: 1.35;
}

/* Lightbox Modal */
.lightbox-modal-content {
    background: #1a0d06;
    border-radius: 24px;
    border: 1px solid rgba(245, 158, 11, 0.3);
    overflow: hidden;
}

.lightbox-img-wrap {
    text-align: center;
    background: #0d0603;
    padding: 20px;
    max-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-img-wrap img {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
</style>

<?php include "nav.php"; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative z-1">
        <span class="hero-tag"><i class="fas fa-trophy me-2"></i> Corporate Achievements</span>
        <h1 class="hero-title">Placements & <span>Internships Showcase</span></h1>
        <p class="hero-subtitle mb-4">
            Celebrating student achievements, high-value corporate offers, paid industrial stipends, and verified departmental placement records.
        </p>
    </div>
</section>

<!-- Stats Highlight Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">120+</div>
                    <div class="stat-label">Students Interning</div>
                    <div class="stat-sublabel">Top MNCs & Tech Labs</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">₹50K</div>
                    <div class="stat-label">Highest Stipend / Mo</div>
                    <div class="stat-sublabel">Paid Industrial Stipend</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">85%</div>
                    <div class="stat-label">PPO Conversion</div>
                    <div class="stat-sublabel">Pre-Placement Offers</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Corporate Partners</div>
                    <div class="stat-sublabel">Recruitment Drives</div>
                </div>
            </div>
        </div>
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

        <!-- Featured Announcement Banner -->
        <div class="company-announcement-card">
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
                <div class="student-card">
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
                <div class="student-card">
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
                <div class="student-card">
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
                <div class="student-card">
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
                <div class="student-card">
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

<!-- Visual Showcase & Extracted Photo Gallery -->
<section class="gallery-section">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">
                <i class="fas fa-images me-2"></i> Visual Records
            </span>
            <h2 class="section-title mb-2">Departmental <span style="color: #d97706;">Visual Showcase</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 650px; margin: 0 auto;">
                Extracted directly from official departmental placement records and internship brochures. Click any image to view in full resolution.
            </p>

            <!-- Gallery Category Navigation Tabs -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4" id="galleryTabs">
                <button type="button" class="gallery-nav-btn active" data-filter="all">
                    <i class="fas fa-border-all me-1"></i> All Visual Records
                </button>
                <button type="button" class="gallery-nav-btn" data-filter="internships">
                    <i class="fas fa-laptop-code me-1"></i> Industrial Internships (9 Slides)
                </button>
                <button type="button" class="gallery-nav-btn" data-filter="placements">
                    <i class="fas fa-trophy me-1"></i> Placement Records (29 Slides)
                </button>
            </div>
        </div>

        <!-- Dynamic Photo Cards Grid -->
        <div class="row g-4" id="photoGrid">

            <!-- INTERNSHIPS SLIDES -->
            <?php 
            $internship_slides = [
                ['page' => 1, 'title' => 'CSD & CSIT Industrial Internship Overview 2025-26'],
                ['page' => 2, 'title' => 'Top Stipend Offers & Corporate Partner Companies'],
                ['page' => 3, 'title' => 'Domain-wise Internship Statistics & Student Breakdown'],
                ['page' => 4, 'title' => 'Software Engineering & Cloud Development Internships'],
                ['page' => 5, 'title' => 'AI, Machine Learning & Data Analytics Programs'],
                ['page' => 6, 'title' => 'Cybersecurity Operations & Network Defense Roles'],
                ['page' => 7, 'title' => 'Full Stack & Mobile Application Development Internships'],
                ['page' => 8, 'title' => 'Student Testimonials & Pre-Placement Offer (PPO) Success'],
                ['page' => 9, 'title' => 'Corporate Liaison & Placement Training Committee']
            ];
            foreach ($internship_slides as $item): 
                $img_url = "assets/internships_pages/page_" . $item['page'] . ".png";
            ?>
                <div class="col-md-6 col-lg-4 gallery-item" data-category="internships">
                    <div class="photo-card">
                        <div class="photo-thumb-wrap" onclick="openLightbox('<?php echo $img_url; ?>', '<?php echo addslashes($item['title']); ?>', 'Industrial Internship Report - Page <?php echo $item['page']; ?>')">
                            <img src="<?php echo $img_url; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                            <div class="photo-overlay">
                                <span class="zoom-badge"><i class="fas fa-search-plus"></i> View Full Slide</span>
                            </div>
                        </div>
                        <div class="photo-info">
                            <span class="photo-category-tag"><i class="fas fa-briefcase me-1"></i> Internship Slide <?php echo $item['page']; ?></span>
                            <h4 class="photo-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top">
                                <small class="text-muted"><i class="fas fa-file-image me-1"></i> High Resolution</small>
                                <span class="text-warning fw-bold small"><i class="fas fa-expand me-1"></i> Click to Zoom</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- PLACEMENTS SLIDES (Sample subset of top key pages for fast loading) -->
            <?php 
            $placement_slides = [
                ['page' => 2, 'title' => 'Annual Package Distribution & Salary Tier Analytics'],
                ['page' => 3, 'title' => 'Highest Package Offers & Marquee Recruiters'],
                ['page' => 4, 'title' => 'Company-wise Selection Count & Recruitment Records'],
                ['page' => 5, 'title' => 'Tier-1 Product Companies & Tech Giants'],
                ['page' => 6, 'title' => 'Core Computer Science & Design Placement Statistics'],
                ['page' => 7, 'title' => 'Information Technology Department Placements Summary'],
                ['page' => 8, 'title' => 'Alumni Career Growth & Global Placement Network']
            ];
            foreach ($placement_slides as $item): 
                $img_url = "assets/placements_pages/page_" . $item['page'] . ".png";
            ?>
                <div class="col-md-6 col-lg-4 gallery-item" data-category="placements">
                    <div class="photo-card">
                        <div class="photo-thumb-wrap" onclick="openLightbox('<?php echo $img_url; ?>', '<?php echo addslashes($item['title']); ?>', 'Official Placement Record - Page <?php echo $item['page']; ?>')">
                            <img src="<?php echo $img_url; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                            <div class="photo-overlay">
                                <span class="zoom-badge"><i class="fas fa-search-plus"></i> View Full Slide</span>
                            </div>
                        </div>
                        <div class="photo-info">
                            <span class="photo-category-tag" style="color: #b45309;"><i class="fas fa-trophy me-1"></i> Placement Slide <?php echo $item['page']; ?></span>
                            <h4 class="photo-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top">
                                <small class="text-muted"><i class="fas fa-file-image me-1"></i> Verified Record</small>
                                <span class="text-warning fw-bold small"><i class="fas fa-expand me-1"></i> Click to Zoom</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<!-- Top Corporate Internship Partners & Recruiters Section -->
<section class="recruiters-section" style="background: #fdfbf7; padding: 75px 0; border-top: 1px solid #f3eae1;">
    <div class="container">
        <div class="text-center mb-4">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">
                <i class="fas fa-building me-2"></i> Corporate Ecosystem
            </span>
            <h2 class="section-title mb-2" style="color: #1a0d06; font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; text-align: center;">
                Top Internship <span style="color: #d97706;">Partners & Recruiters</span>
            </h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 650px; margin: 0 auto; text-align: center;">
                Leading tech MNCs, innovation hubs, and enterprise partners hiring CSD & CSIT students for paid industrial internships and pre-placement offers.
            </p>
        </div>

        <!-- ReactBits Interactive 3D Circular Gallery for Internship Partners -->
        <div id="internshipCompaniesCircularGallery" style="height: 520px; width: 100%; position: relative; overflow: hidden; background: #ffffff; border-radius: 24px; border: 1px solid #f3eae1; box-shadow: 0 15px 40px rgba(0,0,0,0.07); margin-top: 20px; margin-bottom: 10px;"></div>
    </div>
</section>

<!-- Lightbox Modal for Photo Zooming -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content lightbox-modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="modal-title font-outfit fw-bold text-warning" id="lightboxTitle">Image Title</h4>
                    <small class="text-white-50" id="lightboxSub">Category Details</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <div class="lightbox-img-wrap">
                    <img id="lightboxImage" src="" alt="Full Resolution Visual Record">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <small class="text-white-50">SRKREC CSD & CSIT Department Official Visual Records</small>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/circular-gallery.js"></script>
<script>
function openLightbox(imgSrc, title, subtitle) {
    document.getElementById('lightboxImage').src = imgSrc;
    document.getElementById('lightboxTitle').textContent = title;
    document.getElementById('lightboxSub').textContent = subtitle;
    
    var modalElement = document.getElementById('imageLightboxModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Category Tab Filtering
document.addEventListener('DOMContentLoaded', function() {
    var tabBtns = document.querySelectorAll('#galleryTabs .gallery-nav-btn');
    var items = document.querySelectorAll('#photoGrid .gallery-item');

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            var filter = this.getAttribute('data-filter');
            items.forEach(function(item) {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include "footer.php"; ?>
</body>
</html>
