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
    --insta-gradient: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
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

.insta-badge-banner {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-radius: 24px;
    padding: 22px 30px;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: -35px;
    position: relative;
    z-index: 10;
}

.insta-btn {
    background: var(--insta-gradient);
    color: #ffffff !important;
    font-weight: 700;
    padding: 10px 24px;
    border-radius: 999px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 20px rgba(220, 39, 67, 0.3);
    transition: transform 0.3s ease;
}

.insta-btn:hover {
    transform: translateY(-3px);
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

/* Event Post Card Box Styling */
.news-card {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-radius: 24px;
    padding: 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.news-card:hover {
    transform: translateY(-8px);
    border-color: #d97706;
    box-shadow: 0 20px 40px rgba(217, 119, 6, 0.15);
}

.post-box-header {
    background: linear-gradient(135deg, #1a0d06 0%, #3d1e0e 100%);
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.post-icon-badge {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: var(--insta-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.4rem;
    box-shadow: 0 4px 15px rgba(220, 39, 67, 0.3);
}

.date-pill {
    background: rgba(217, 119, 6, 0.1);
    color: #b45309;
    font-weight: 800;
    font-size: 0.8rem;
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid rgba(217, 119, 6, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.insta-handle-tag {
    font-size: 0.8rem;
    font-weight: 700;
    color: #e6683c;
}

.announcement-card {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    box-shadow: 0 6px 20px rgba(180, 83, 9, 0.04);
    transition: all 0.3s ease;
}

.announcement-card:hover {
    transform: translateX(8px);
    border-color: #d97706;
    box-shadow: 0 10px 25px rgba(217, 119, 6, 0.12);
}

.news-icon-container {
    width: 54px;
    height: 54px;
    min-width: 54px;
    border-radius: 16px;
    background: rgba(217, 119, 6, 0.1);
    border: 1px solid rgba(217, 119, 6, 0.2);
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.direct-post-btn {
    background: rgba(217, 119, 6, 0.1);
    color: #d97706 !important;
    border: 1px solid rgba(217, 119, 6, 0.3);
    font-weight: 800;
    font-size: 0.82rem;
    padding: 8px 16px;
    border-radius: 999px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.25s ease;
}

.direct-post-btn:hover {
    background: var(--insta-gradient);
    color: #ffffff !important;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(220, 39, 67, 0.3);
    transform: translateY(-2px);
}
</style>

<?php include "nav.php"; ?>

<!-- Hero Section -->
<section class="hero-banner">
    <div class="container position-relative z-1">
        <span class="hero-tag"><i class="fab fa-instagram me-2"></i> Official @srkrcsdcsit Feed</span>
        <h1 class="hero-title">News & <span>Events Hub</span></h1>
        <p class="lead mx-auto" style="max-width: 680px; color: #e5d5c5; font-size: 1.15rem; line-height: 1.6;">
            Discover events organized by CSD & CSIT departments alongside official press releases, circulars, and academic news.
        </p>
    </div>
</section>

<!-- Official Instagram Profile Header Banner -->
<div class="container">
    <div class="insta-badge-banner">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 55px; height: 55px; border-radius: 50%; background: var(--insta-gradient); padding: 3px; display: flex; align-items: center; justify-content: center;">
                <div style="width: 100%; height: 100%; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; color: #dc2743; font-size: 1.5rem;">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: #1a0d06;">CSD CSIT SRKREC (@srkrcsdcsit)</h5>
                <span class="insta-handle-tag"><i class="fas fa-at"></i>srkrcsdcsit</span>
                <span class="text-muted small ms-2">• 452 Posts • SRKR Engineering College</span>
            </div>
        </div>
        <div>
            <a href="https://www.instagram.com/srkrcsdcsit/" target="_blank" rel="noopener noreferrer" class="insta-btn">
                <i class="fab fa-instagram me-1"></i> Open Instagram Channel
            </a>
        </div>
    </div>
</div>

<!-- Stats Highlight -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">452</div>
                    <div class="stat-label">Instagram Posts</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">784+</div>
                    <div class="stat-label">Student Followers</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">294</div>
                    <div class="stat-label">Following</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Official Page</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 1: Events Organised by CSD & CSIT -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f3eae1; border-bottom: 1px solid #f3eae1;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Events & Workshops</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">Events Organised by <span style="color: #d97706;">CSD & CSIT</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 650px; margin: 0 auto;">Official hackathons, workshops, and sports meets. Click on <strong>"Open Direct Post"</strong> to view the post directly on <a href="https://www.instagram.com/srkrcsdcsit/" target="_blank" style="color: #e6683c; font-weight: 700; text-decoration: none;">@srkrcsdcsit</a>.</p>
        </div>

        <div class="row g-4">
            <!-- Box 1: Potluck Event Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
                            <source src="assets/videos/pot_luck.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> Food & Bonding</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Potluck Event</h3>
                    <p class="text-secondary small mb-4">A joyful community celebration featuring delicious homemade food, fun activities, and memorable bonding moments.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 1,850 Likes</span>
                        <a href="https://www.instagram.com/reel/C_VGop-ydyj/?igsh=MWZvdWozeGZ5a2pjag==" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Box 2: Alumni Talk Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
                            <source src="assets/videos/alumini_talk.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> Alumni Interaction</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Alumni Talk & Interaction</h3>
                    <p class="text-secondary small mb-4">An inspiring interactive session with distinguished department alumni sharing industry insights, career guidance, and success stories.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 1,940 Likes</span>
                        <a href="https://www.instagram.com/reel/Dawv3OfIiKO/?igsh=MWY5OXBjMGxhcTFkcw==" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Box 3: Startup Meet Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
                            <source src="assets/videos/startup_meet.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> Entrepreneurship</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Startup Meet & Summit</h3>
                    <p class="text-secondary small mb-4">An energetic startup summit bringing student entrepreneurs, founders, and industry leaders together to showcase innovative ideas.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 2,150 Likes</span>
                        <a href="https://www.instagram.com/reel/DPOt9iCDHDm/?utm_source=ig_web_button_share_sheet" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Box 4: NextGen Labs Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
                            <source src="assets/videos/nextgen_labs.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> AI & Emerging Tech</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">NextGen Labs Showcase</h3>
                    <p class="text-secondary small mb-4">NextGen research labs showcase featuring cutting-edge artificial intelligence, robotics, high-performance computing, and student project prototypes.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 2,420 Likes</span>
                        <a href="https://www.instagram.com/reel/DNik1_yK1K1/?utm_source=ig_web_button_share_sheet" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Box 5: Freedom Fest Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
                            <source src="assets/videos/freedom_fest.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> Culture & Festivities</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Freedom Fest Celebration</h3>
                    <p class="text-secondary small mb-4">A grand patriotic celebration filled with vibrant cultural performances, student talent showcases, and memorable campus festivities.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 2,890 Likes</span>
                        <a href="https://www.instagram.com/reel/Cxz_7RyMhDM/?igsh=djhubnJwZzAyenpm" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Box 6: Freshers CSD CSIT Video Card -->
            <div class="col-md-6 col-lg-4">
                <div class="news-card" style="border: 1.5px solid #fecdd3; background: #ffffff; padding: 22px; border-radius: 24px;">
                    <!-- Background Video Container -->
                    <div style="position: relative; border-radius: 18px; overflow: hidden; height: 210px; margin-bottom: 18px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; transform: rotate(-90deg) scale(1.78); display: block; pointer-events: none;">
                            <source src="assets/videos/freshers_csd_csit.mp4" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fab fa-instagram me-1" style="color: #e11d48;"></i> Reel Video
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="date-pill" style="background: rgba(225, 29, 72, 0.1); color: #be123c; border-color: rgba(225, 29, 72, 0.2);"><i class="far fa-calendar-alt"></i> Freshers & Student Life</span>
                        <span class="insta-handle-tag" style="color: #e11d48;"><i class="fab fa-instagram me-1"></i> Instagram Reel</span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">CSD & CSIT Freshers Welcome</h3>
                    <p class="text-secondary small mb-4">Grand welcoming event for incoming CSD & CSIT batches with exciting performances, fun icebreakers, and student celebrations.</p>

                    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"><i class="far fa-heart me-1" style="color: #dc2743;"></i> 3,120 Likes</span>
                        <a href="https://www.instagram.com/reel/CxDklpPxovv/?igsh=MXY1a3Z5dGxibTJhYg==" target="_blank" rel="noopener noreferrer" class="direct-post-btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: #ffffff !important;">
                            <i class="fab fa-instagram me-1"></i> Watch Insta Reel <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
</body>
</html>
