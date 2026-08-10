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

/* Hero Banner */
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

/* Stats Highlight */
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

/* Category Filter Tabs */
.category-filter-btn {
    border: 1.5px solid #f3eae1;
    background: #ffffff;
    color: #6f5f54;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 9px 20px;
    border-radius: 999px;
    transition: all 0.25s ease;
    white-space: nowrap;
}

.category-filter-btn:hover, .category-filter-btn.active {
    background: #d97706;
    color: #ffffff;
    border-color: #d97706;
    box-shadow: 0 6px 18px rgba(217, 119, 6, 0.25);
}

/* Featured Achievement Card Styling */
.featured-achievement-card {
    background: #ffffff;
    border: 1.5px solid #f3eae1;
    border-top: 6px solid #d97706;
    border-radius: 28px;
    padding: 36px;
    box-shadow: 0 16px 45px rgba(180, 83, 9, 0.08);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.featured-achievement-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 60px rgba(217, 119, 6, 0.16);
    border-color: #f59e0b;
}

/* Dynamic Interactive Gallery Styling */
.achievement-gallery-main {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: #1a0d06;
    box-shadow: 0 16px 35px rgba(0, 0, 0, 0.16);
    cursor: pointer;
    aspect-ratio: 4/3;
    perspective: 1000px;
    transform-style: preserve-3d;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}

.achievement-gallery-main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), filter 0.3s ease;
    animation: kenBurnsZoom 10s ease-in-out infinite alternate;
}

.achievement-gallery-main.animating img {
    animation: imgFadeInZoom 0.55s ease-out forwards;
}

@keyframes kenBurnsZoom {
    0% { transform: scale(1); }
    50% { transform: scale(1.06) translate(-1%, -1%); }
    100% { transform: scale(1.02); }
}

@keyframes imgFadeInZoom {
    0% { opacity: 0.3; transform: scale(1.08); }
    100% { opacity: 1; transform: scale(1); }
}

.gallery-hover-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(217, 119, 6, 0.2), rgba(26, 13, 6, 0.6));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.35s ease;
    backdrop-filter: blur(3px);
    z-index: 5;
}

.achievement-gallery-main:hover .gallery-hover-overlay {
    opacity: 1;
}

/* Floating Sparkle Particles */
.sparkle-dot {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #f59e0b;
    border-radius: 50%;
    box-shadow: 0 0 10px #f59e0b;
    pointer-events: none;
    animation: floatSparkle 3s infinite ease-in-out;
    z-index: 6;
}

.sparkle-dot:nth-child(1) { top: 15%; left: 10%; animation-delay: 0s; }
.sparkle-dot:nth-child(2) { top: 70%; right: 15%; animation-delay: 1s; }
.sparkle-dot:nth-child(3) { bottom: 20%; left: 25%; animation-delay: 2s; }

@keyframes floatSparkle {
    0%, 100% { transform: translateY(0) scale(0.8); opacity: 0.3; }
    50% { transform: translateY(-12px) scale(1.4); opacity: 1; }
}

.gallery-thumbnail {
    width: 85px;
    height: 62px;
    border-radius: 14px;
    overflow: hidden;
    border: 2.5px solid transparent;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    opacity: 0.7;
    position: relative;
}

.gallery-thumbnail.active, .gallery-thumbnail:hover {
    border-color: #d97706;
    opacity: 1;
    transform: scale(1.08);
    box-shadow: 0 6px 16px rgba(217, 119, 6, 0.3);
}

.gallery-thumbnail.active::after {
    content: '✓';
    position: absolute;
    top: 4px;
    right: 4px;
    background: #d97706;
    color: white;
    font-size: 0.65rem;
    font-weight: 900;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Cash Award Spotlight Box */
.cash-award-spotlight {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #fde68a;
    border-radius: 20px;
    padding: 18px 24px;
    position: relative;
    overflow: hidden;
}

.cash-award-spotlight::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transform: rotate(30deg);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) rotate(30deg); }
    100% { transform: translateX(200%) rotate(30deg); }
}

/* Badge Styling */
.info-badge-item {
    background: #fdfbf7;
    border: 1px solid #f3eae1;
    border-radius: 14px;
    padding: 10px 16px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #1a0d06;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.info-badge-item:hover {
    background: #ffffff;
    border-color: #d97706;
    transform: translateY(-2px);
}

/* Pulse animation for trophy badge */
.badge-pulse {
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
    0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
    100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
}

/* Lightbox Modal */
.lightbox-modal-body {
    position: relative;
    background: #0d0603;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 480px;
}

.lightbox-img {
    max-height: 80vh;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.4s ease, opacity 0.3s ease;
}

.lightbox-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.25s ease;
    backdrop-filter: blur(4px);
    z-index: 10;
}

.lightbox-nav-btn:hover {
    background: rgba(217, 119, 6, 0.9);
    color: white;
}

.lightbox-nav-prev { left: 16px; }
.lightbox-nav-next { right: 16px; }
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

<!-- Featured Student Achievements Section -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f3eae1; border-bottom: 1px solid #f3eae1;">
    <div class="container py-3">
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Excellence Showcase</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">Featured <span style="color: #d97706;">Student Achievements</span></h2>
            <p style="color: #6f5f54; font-size: 1.05rem; max-width: 620px; margin: 0 auto;">Recognizing outstanding student accomplishments across national hackathons, technical contests, and research events.</p>
        </div>

        <!-- Category Filter Tabs Bar -->
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-5 px-2">
            <button class="category-filter-btn" data-category="all">🌟 All Achievements</button>
            <button class="category-filter-btn active" data-category="hackathons">🏆 Hackathons</button>
            <button class="category-filter-btn" data-category="competitions">🥇 Competitions</button>
            <button class="category-filter-btn" data-category="technical">💻 Technical Events</button>
            <button class="category-filter-btn" data-category="academics">📚 Academic Achievements</button>
            <button class="category-filter-btn" data-category="research">🔬 Research</button>
            <button class="category-filter-btn" data-category="conferences">🎤 Conferences</button>
        </div>

        <!-- Achievements Container (Rendered by JS / PHP Data) -->
        <div id="achievementsContainer">
            <!-- Dynamic achievements rendered here -->
        </div>
    </div>
</section>

<!-- Fullscreen Image Lightbox Modal -->
<div class="modal fade" id="achievementLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 overflow-hidden rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold" id="lightboxCategoryBadge">SMART INDIA HACKATHON 2025</span>
                    <span class="small text-muted" id="lightboxCounter">Image 1 of 2</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="lightbox-modal-body">
                <button class="lightbox-nav-btn lightbox-nav-prev" id="lightboxPrevBtn" aria-label="Previous Image">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img id="lightboxMainImage" src="" alt="Achievement Fullscreen Preview" class="lightbox-img">
                <button class="lightbox-nav-btn lightbox-nav-next" id="lightboxNextBtn" aria-label="Next Image">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="modal-footer border-0 bg-dark text-white p-3 justify-content-between">
                <div class="small fw-semibold text-warning" id="lightboxCaption">Team Ujjval — Distinguished Performer Award (Smart India Hackathon 2025)</div>
                <button type="button" class="btn btn-outline-light rounded-pill btn-sm px-4" data-bs-dismiss="modal">Close Preview</button>
            </div>
        </div>
    </div>
</div>

<script>
// Data Structure for Student Achievements
const studentAchievements = [
    {
        id: "sih-2025-team-ujjval",
        title: "Distinguished Performer Award",
        subtitle: "Smart India Hackathon 2025 — Grand Finale",
        event: "Smart India Hackathon 2025 — Grand Finale",
        category: "hackathons",
        categoryName: "Hackathon Achievement",
        categoryIcon: "fa-trophy",
        team: "Team Ujjval",
        award: "Distinguished Performer Award",
        cashAward: "₹25,000",
        date: "8th & 9th December 2025",
        duration: "36 Hours",
        department: "Department of CSD & CSIT, SRKR Engineering College",
        description: "Congratulations to Team Ujjval! A proud achievement for the Department of CSD & CSIT, SRKR Engineering College, as Team Ujjval receives the Distinguished Performer Award at the Smart India Hackathon 2025 Grand Finale. Their outstanding performance, innovative thinking, and relentless efforts were recognized with a ₹25,000 cash award.",
        images: [
            "assets/achievements/sih-2025-team-ujjval-1.jpg",
            "assets/achievements/sih-2025-team-ujjval-2.jpg"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-trophy", text: "Distinguished Performer", colorClass: "bg-warning text-dark" },
            { icon: "fas fa-laptop-code", text: "Hackathon", colorClass: "bg-primary text-white" },
            { icon: "fas fa-indian-rupee-sign", text: "₹25,000 Cash Award", colorClass: "bg-success text-white" },
            { icon: "fas fa-clock", text: "36 Hours", colorClass: "bg-info text-dark" },
            { icon: "fas fa-calendar-alt", text: "8th & 9th Dec 2025", colorClass: "bg-secondary text-white" },
            { icon: "fas fa-flag-checkered", text: "Grand Finale", colorClass: "bg-danger text-white" }
        ]
    }
];

let activeLightboxImages = [];
let activeImageIndex = 0;
let currentLightboxModal = null;
let autoSlideshowInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    renderAchievements('hackathons');
    
    // Lightbox modal instance
    const modalEl = document.getElementById('achievementLightboxModal');
    currentLightboxModal = new bootstrap.Modal(modalEl);

    // Lightbox Controls
    document.getElementById('lightboxPrevBtn').addEventListener('click', showPrevImage);
    document.getElementById('lightboxNextBtn').addEventListener('click', showNextImage);

    // Keyboard accessibility for Lightbox
    document.addEventListener('keydown', function(e) {
        if (!modalEl.classList.contains('show')) return;
        if (e.key === 'ArrowLeft') showPrevImage();
        if (e.key === 'ArrowRight') showNextImage();
    });

    // Category Filter Click Event
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-category');
            renderAchievements(category);
        });
    });
});

function renderAchievements(selectedCategory = 'all') {
    const container = document.getElementById('achievementsContainer');
    container.innerHTML = '';
    
    if (autoSlideshowInterval) {
        clearInterval(autoSlideshowInterval);
        autoSlideshowInterval = null;
    }

    const filtered = selectedCategory === 'all' 
        ? studentAchievements 
        : studentAchievements.filter(a => a.category === selectedCategory);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="row g-4 justify-content-center">
                <div class="col-12 text-center py-4">
                    <div class="p-5 rounded-4 shadow-sm bg-white border border-light d-inline-block mx-auto" style="max-width: 550px;">
                        <div class="mx-auto mb-3" style="width: 70px; height: 70px; font-size: 1.8rem; background: rgba(217, 119, 6, 0.1); color: #d97706; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h4 class="fw-bold text-dark font-outfit mb-2">No Achievements in this Category</h4>
                        <p class="text-secondary small mb-0">More student awards and accomplishments in this category will be updated soon.</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    filtered.forEach(item => {
        const cardCol = document.createElement('div');
        cardCol.className = 'mb-5';

        if (item.featured) {
            cardCol.innerHTML = `
                <div class="featured-achievement-card">
                    <div class="row g-4 align-items-center">
                        <!-- Left Column: Interactive Animated Image Gallery -->
                        <div class="col-lg-6">
                            <div class="achievement-gallery-main mb-3" id="mainGalleryBox-${item.id}" onclick="openLightbox('${item.id}', getCurrentIdx('${item.id}'))">
                                <span class="sparkle-dot"></span>
                                <span class="sparkle-dot"></span>
                                <span class="sparkle-dot"></span>
                                <img id="activeMainImg-${item.id}" src="${item.images[0]}" alt="${item.title}" loading="lazy">
                                <div class="gallery-hover-overlay">
                                    <span class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                                        <i class="fas fa-search-plus me-2 text-warning"></i> View Full Image
                                    </span>
                                </div>
                                <div class="position-absolute top-0 start-0 p-3 d-flex gap-2" style="z-index: 6;">
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.78rem;">
                                        🏆 ${item.award}
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 end-0 p-3" style="z-index: 6;">
                                    <span class="badge bg-dark bg-opacity-75 text-white px-3 py-1.5 rounded-pill small" style="backdrop-filter: blur(4px);">
                                        <i class="fas fa-images me-1"></i> ${item.images.length} Photos
                                    </span>
                                </div>
                            </div>

                            <!-- Image Thumbnail Strip -->
                            <div class="d-flex align-items-center justify-content-start gap-3">
                                ${item.images.map((imgSrc, idx) => `
                                    <div class="gallery-thumbnail ${idx === 0 ? 'active' : ''}" id="thumb-${item.id}-${idx}" onclick="switchGalleryImg('${item.id}', ${idx})">
                                        <img src="${imgSrc}" alt="Thumbnail ${idx + 1}">
                                    </div>
                                `).join('')}
                                <span class="small text-muted ms-2"><i class="fas fa-magic me-1 text-warning"></i> Auto Slideshow & 3D Tilt</span>
                            </div>
                        </div>

                        <!-- Right Column: Achievement Information -->
                        <div class="col-lg-6">
                            <!-- Top Header Badges -->
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold badge-pulse" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    <i class="fas fa-trophy me-1"></i> DISTINGUISHED PERFORMER
                                </span>
                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
                                    🎯 GRAND FINALE
                                </span>
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                                    <i class="fas fa-laptop-code me-1"></i> ${item.event}
                                </span>
                            </div>

                            <!-- Title & Subtitle -->
                            <h2 class="font-outfit fw-bold text-dark mb-2" style="font-size: 2.1rem; line-height: 1.25;">
                                ${item.title}
                            </h2>
                            
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="fw-bold fs-5 text-warning"><i class="fas fa-users me-1"></i> ${item.team}</span>
                                <span class="text-muted">•</span>
                                <span class="fw-semibold text-secondary small"><i class="fas fa-university me-1"></i> ${item.department}</span>
                            </div>

                            <!-- Cash Award Spotlight -->
                            <div class="cash-award-spotlight mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative z-1">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-warning text-dark p-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                            <i class="fas fa-gift fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="text-uppercase small fw-bold text-secondary d-block" style="letter-spacing: 1px; font-size: 0.75rem;">Cash Award Received</span>
                                            <h4 class="fw-extrabold text-dark mb-0 font-outfit" style="color: #b45309 !important; font-size: 1.6rem;">${item.cashAward}</h4>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-dark text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
                                            ⏱️ ${item.duration} Hackathon
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-secondary mb-4" style="font-size: 1.02rem; line-height: 1.75;">
                                ${item.description}
                            </p>

                            <!-- Information Badges Grid -->
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                ${item.badges.map(b => `
                                    <div class="info-badge-item">
                                        <i class="${b.icon} text-warning"></i> ${b.text}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        container.appendChild(cardCol);

        // Attach Interactive 3D Tilt Effect and Auto Slideshow for Item
        setupInteractiveGallery(item.id, item.images);
    });
}

const currentIdxMap = {};

function getCurrentIdx(itemId) {
    return currentIdxMap[itemId] || 0;
}

function setupInteractiveGallery(itemId, images) {
    let currentIdx = 0;
    currentIdxMap[itemId] = 0;

    // Auto slideshow every 4.5 seconds
    if (images.length > 1) {
        autoSlideshowInterval = setInterval(() => {
            currentIdx = (currentIdx + 1) % images.length;
            switchGalleryImg(itemId, currentIdx, true);
        }, 4500);
    }

    // 3D Parallax Tilt Effect on Mousemove
    const galleryEl = document.getElementById(`mainGalleryBox-${itemId}`);
    if (galleryEl) {
        galleryEl.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            const rotateX = (-y / rect.height) * 12;
            const rotateY = (x / rect.width) * 12;
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        });

        galleryEl.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)';
        });
    }
}

function switchGalleryImg(itemId, index, isAuto = false) {
    const item = studentAchievements.find(a => a.id === itemId);
    if (!item) return;

    currentIdxMap[itemId] = index;
    const galleryEl = document.getElementById(`mainGalleryBox-${itemId}`);
    const mainImg = document.getElementById(`activeMainImg-${itemId}`);

    if (mainImg) {
        galleryEl.classList.remove('animating');
        void galleryEl.offsetWidth; // Trigger reflow for animation reset
        mainImg.src = item.images[index];
        galleryEl.classList.add('animating');
    }
    
    item.images.forEach((_, idx) => {
        const thumb = document.getElementById(`thumb-${itemId}-${idx}`);
        if (thumb) {
            if (idx === index) thumb.classList.add('active');
            else thumb.classList.remove('active');
        }
    });
}

function openLightbox(itemId, index) {
    const item = studentAchievements.find(a => a.id === itemId);
    if (!item) return;

    activeLightboxImages = item.images;
    activeImageIndex = index;

    updateLightboxContent(item);
    currentLightboxModal.show();
}

function updateLightboxContent(item) {
    const currentItem = item || studentAchievements.find(a => a.images.includes(activeLightboxImages[0]));
    
    document.getElementById('lightboxMainImage').src = activeLightboxImages[activeImageIndex];
    document.getElementById('lightboxCounter').textContent = `Image ${activeImageIndex + 1} of ${activeLightboxImages.length}`;
    
    if (currentItem) {
        document.getElementById('lightboxCaption').textContent = `${currentItem.team} — ${currentItem.title} (${currentItem.event})`;
        document.getElementById('lightboxCategoryBadge').textContent = currentItem.event.toUpperCase();
    }
}

function showPrevImage() {
    if (activeLightboxImages.length === 0) return;
    activeImageIndex = (activeImageIndex - 1 + activeLightboxImages.length) % activeLightboxImages.length;
    updateLightboxContent();
}

function showNextImage() {
    if (activeLightboxImages.length === 0) return;
    activeImageIndex = (activeImageIndex + 1) % activeLightboxImages.length;
    updateLightboxContent();
}
</script>

<?php include "footer.php"; ?>
</body>
</html>
