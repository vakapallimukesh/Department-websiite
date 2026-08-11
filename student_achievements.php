<?php 
if (session_status() == PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary-amber: #d97706;
    --amber-light: #fffbeb;
    --amber-border: #fde68a;
    --text-dark: #0f172a;
    --text-muted: #64748b;
    --bg-page: #f8fafc;
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--bg-page);
    color: var(--text-dark);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

/* Hero Banner */
.hero-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    color: white;
    padding: 75px 20px 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(245, 158, 11, 0.12) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.7;
    pointer-events: none;
}

.hero-tag {
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.12);
    padding: 6px 16px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 14px;
    border: 1px solid rgba(245, 158, 11, 0.25);
}

.hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 3rem;
    font-weight: 900;
    line-height: 1.15;
    margin-bottom: 16px;
}

.hero-title span {
    color: #f59e0b;
}

/* Stats Highlight Bar */
.stat-card {
    background: #ffffff;
    border: 1px solid var(--card-border);
    border-radius: 18px;
    padding: 22px 18px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    border-color: var(--primary-amber);
    box-shadow: 0 10px 25px rgba(217, 119, 6, 0.1);
}

.stat-number {
    font-family: 'Outfit', sans-serif;
    font-size: 2.3rem;
    font-weight: 900;
    color: var(--primary-amber);
}

.stat-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-top: 4px;
}

/* Category Filter Pills */
.category-filter-btn {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    font-weight: 600;
    font-size: 0.88rem;
    padding: 8px 18px;
    border-radius: 999px;
    transition: all 0.25s ease;
    white-space: nowrap;
}

.category-filter-btn:hover, .category-filter-btn.active {
    background: var(--primary-amber);
    color: #ffffff;
    border-color: var(--primary-amber);
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
}

/* Clean Editorial Achievement Card */
.editorial-card {
    background: #ffffff;
    border: 1px solid var(--card-border);
    border-radius: 20px;
    padding: 24px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.editorial-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

/* Crisp HD Image Frame */
.editorial-img-container {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    background: #0f172a;
    aspect-ratio: 16/10;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    margin-bottom: 16px;
}

.editorial-img-container img, .editorial-img-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    image-rendering: -webkit-optimize-contrast;
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.editorial-img-container:hover img {
    transform: scale(1.03);
}

/* Slide Arrow Navigation Overlay */
.slide-arrow-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.65);
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    opacity: 0;
    transition: all 0.25s ease;
    z-index: 5;
    backdrop-filter: blur(4px);
}

.editorial-img-container:hover .slide-arrow-btn {
    opacity: 1;
}

.slide-arrow-btn:hover {
    background: var(--primary-amber);
    color: #ffffff;
    transform: translateY(-50%) scale(1.1);
}

.slide-arrow-prev { left: 10px; }
.slide-arrow-next { right: 10px; }

.editorial-img-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.editorial-img-container:hover .editorial-img-overlay {
    opacity: 1;
}

/* Photo Badges Overlay */
.top-photo-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(255, 255, 255, 0.95);
    color: #0f172a;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 5px 12px;
    border-radius: 999px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    backdrop-filter: blur(4px);
    z-index: 2;
}

.bottom-photo-count {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(15, 23, 42, 0.85);
    color: #ffffff;
    font-weight: 600;
    font-size: 0.72rem;
    padding: 4px 10px;
    border-radius: 999px;
    backdrop-filter: blur(4px);
    z-index: 2;
}

/* Thumbnail Strip */
.editorial-thumb-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.editorial-thumb {
    width: 58px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    opacity: 0.55;
    transition: all 0.25s ease;
}

.editorial-thumb.active, .editorial-thumb:hover {
    border-color: var(--primary-amber);
    opacity: 1;
    transform: scale(1.05);
}

.editorial-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Cash Award Badge Box */
.editorial-award-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.editorial-award-amount {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1.35rem;
    color: #b45309;
}

/* Minimal Badge Item */
.mini-badge {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Lightbox Modal */
.lightbox-modal-body {
    position: relative;
    background: #020617;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 480px;
}

.lightbox-img, .lightbox-video {
    max-height: 82vh;
    max-width: 100%;
    object-fit: contain;
}

.lightbox-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.25s ease;
    backdrop-filter: blur(4px);
    z-index: 10;
}

.lightbox-nav-btn:hover {
    background: var(--primary-amber);
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
        <p class="lead mx-auto" style="max-width: 680px; color: #cbd5e1; font-size: 1.1rem; line-height: 1.6;">
            Celebrating national hackathon winners, project expo champions, research authors, and student leaders in CSD & CSIT departments.
        </p>
    </div>
</section>

<!-- Stats Highlight Bar -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row g-3">
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

<!-- Main Student Achievements Section -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-2">
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">Excellence Showcase</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.6rem; font-weight: 800; color: #0f172a;">Featured <span style="color: #d97706;">Student Achievements</span></h2>
            <p style="color: #64748b; font-size: 1.02rem; max-width: 600px; margin: 0 auto;">Recognizing outstanding student accomplishments across national hackathons, technical contests, project expos, and research events.</p>
        </div>

        <!-- Category Filter Pills Bar -->
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-5 px-2">
            <button class="category-filter-btn active" data-category="all">🌟 All Achievements</button>
            <button class="category-filter-btn" data-category="hackathons">🏆 Hackathons</button>
            <button class="category-filter-btn" data-category="competitions">🥇 Competitions & Expos</button>
            <button class="category-filter-btn" data-category="technical">💻 Technical Events</button>
            <button class="category-filter-btn" data-category="academics">📚 Academic Achievements</button>
            <button class="category-filter-btn" data-category="research">🔬 Research</button>
            <button class="category-filter-btn" data-category="conferences">🎤 Conferences</button>
        </div>

        <!-- Achievements Grid Container (Desktop 3-col, Tablet 2-col, Mobile 1-col) -->
        <div id="achievementsContainer">
            <!-- Rendered by JS -->
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="achievementLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 overflow-hidden rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" id="lightboxCategoryBadge">STUDENT ACHIEVEMENT</span>
                    <span class="small text-muted" id="lightboxCounter">Media Preview</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="lightbox-modal-body" id="lightboxMediaContainer">
                <button class="lightbox-nav-btn lightbox-nav-prev" id="lightboxPrevBtn" aria-label="Previous Image">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img id="lightboxMainImage" src="" alt="Achievement Fullscreen Preview" class="lightbox-img">
                <button class="lightbox-nav-btn lightbox-nav-next" id="lightboxNextBtn" aria-label="Next Image">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="modal-footer border-0 bg-dark text-white p-3 justify-content-between">
                <div class="small fw-semibold text-warning" id="lightboxCaption">Achievement Preview</div>
                <button type="button" class="btn btn-outline-light rounded-pill btn-sm px-4" data-bs-dismiss="modal">Close Preview</button>
            </div>
        </div>
    </div>
</div>

<script>
// Student Achievements Data Store
const studentAchievements = [
    {
        id: "sih-2022-winners",
        title: "National Level 1st Prize Winners — Smart India Hackathon 2022",
        event: "Smart India Hackathon 2022 — Grand Finale",
        category: "hackathons",
        categoryName: "Hackathon",
        team: "SRKREC Student Team",
        award: "🥇 National 1st Prize",
        cashAward: "₹1,00,000",
        stage: "Grand Finale",
        department: "Department of CSD & CSIT",
        description: "Winners of Smart India Hackathon 2022! SRKREC Students won the National Level First Prize worth ₹1 Lakh Rupees in the world's largest innovation contest, Smart India Hackathon 2022. Watch the promo video of their inspiring success journey.",
        video: "assets/achievements/sih-2022-promo.mp4",
        isVideo: true,
        images: [
            "assets/achievements/sih-2022-promo.mp4"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-trophy", text: "🥇 National 1st Prize" },
            { icon: "fas fa-laptop-code", text: "SIH 2022" },
            { icon: "fas fa-indian-rupee-sign", text: "₹1,00,000 Cash Award" },
            { icon: "fas fa-play-circle", text: "Promo Video" },
            { icon: "fas fa-flag-checkered", text: "Grand Finale" }
        ]
    },
    {
        id: "quantum-valley-2025",
        title: "1st Place — Amaravati Quantum Valley Hackathon 2025",
        event: "Amaravati Quantum Valley Hackathon 2025",
        category: "hackathons",
        categoryName: "Hackathon",
        team: "Team Entangled Coders",
        award: "🥇 1st Place",
        cashAward: "₹50,000",
        stage: "Grand Finale",
        department: "Department of CSD & CSIT",
        description: "Proud moment for Team Entangled Coders from the Department of CSD & CSIT, SRKR Engineering College, for securing 1st Place at the Amaravati Quantum Valley Hackathon 2025 – Grand Finale. Their innovation, teamwork, and dedication earned them the top honor along with a ₹50,000 Cash Award.",
        images: [
            "assets/achievements/quantum-valley-2025-team-entangled-coders-1.jpg"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-medal", text: "🥇 1st Place Winner" },
            { icon: "fas fa-laptop-code", text: "Hackathon" },
            { icon: "fas fa-indian-rupee-sign", text: "₹50,000 Cash Award" },
            { icon: "fas fa-flag-checkered", text: "Grand Finale" }
        ]
    },
    {
        id: "prakalp-project-expo-2024",
        title: "2nd Prize Winners @ Prakalp Project Expo!",
        event: "Prakalp Project Expo — Ramachandra College of Engg",
        category: "competitions",
        categoryName: "Project Expo",
        team: "Team OG",
        award: "🥈 2nd Prize",
        cashAward: "₹10,000",
        stage: "Project Expo",
        department: "3/4 CSD, Department of CSD & CSIT",
        description: "🏆 2nd Prize Winners @ Prakalp Project Expo! Out of 100+ competing teams across colleges, Team OG (Uma Sai Pavan, Mohan Siva, Prashanth) from 3/4 CSD, SRKR Engineering College, proudly secured 2nd place and won a ₹10,000 cash prize at Ramachandra College of Engineering, Eluru.",
        images: [
            "assets/achievements/prakalp-project-expo-2024-team-og-1.jpg",
            "assets/achievements/prakalp-project-expo-2024-team-og-2.jpg",
            "assets/achievements/prakalp-project-expo-2024-team-og-3.jpg"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-medal", text: "🥈 2nd Prize Winner" },
            { icon: "fas fa-project-diagram", text: "Prakalp Expo" },
            { icon: "fas fa-indian-rupee-sign", text: "₹10,000 Cash Award" },
            { icon: "fas fa-users", text: "100+ Teams" },
            { icon: "fas fa-user-graduate", text: "3/4 CSD" }
        ]
    },
    {
        id: "sih-2025-team-ujjval",
        title: "Distinguished Performer Award — Smart India Hackathon 2025",
        event: "Smart India Hackathon 2025",
        category: "hackathons",
        categoryName: "Hackathon",
        team: "Team Ujjval",
        award: "🏆 Distinguished Performer",
        cashAward: "₹25,000",
        date: "8th & 9th Dec 2025",
        duration: "36 Hours",
        department: "Department of CSD & CSIT",
        description: "Congratulations to Team Ujjval! A proud achievement for the Department of CSD & CSIT, SRKR Engineering College, as Team Ujjval receives the Distinguished Performer Award at the Smart India Hackathon 2025 Grand Finale. Their outstanding performance and innovative thinking were recognized with a ₹25,000 cash award.",
        images: [
            "assets/achievements/sih-2025-team-ujjval-1.jpg",
            "assets/achievements/sih-2025-team-ujjval-2.jpg"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-trophy", text: "Distinguished Performer" },
            { icon: "fas fa-laptop-code", text: "Hackathon" },
            { icon: "fas fa-indian-rupee-sign", text: "₹25,000 Cash Award" },
            { icon: "fas fa-clock", text: "36 Hours" },
            { icon: "fas fa-flag-checkered", text: "Grand Finale" }
        ]
    },
    {
        id: "prajwalan-2k23-team-virtual-bridge",
        title: "2nd Prize — Prajwalan 2K23 (24-Hour Hackathon)",
        event: "Prajwalan 2K23 — 24 Hour Hackathon",
        category: "hackathons",
        categoryName: "Hackathon",
        team: "Team Virtual Bridge",
        award: "🥈 2nd Prize",
        cashAward: "₹10,000",
        date: "23rd & 24th March 2023",
        duration: "24 Hours",
        department: "Department of CSD & CSIT",
        description: "Another Feather in the CSD Cap! Team Virtual Bridge (2/4 CSD) won 2nd Prize in Prajwalan 2K23, a 24-hour Hackathon conducted by CSE Dept of SRKREC. Out of 45 participating teams across AP colleges, Team Virtual Bridge (K. Sanju, Ch. Ravi Kumar, Ch. Anusha, Chaitanya Srujana, K. Puneeth, V. Siva Mani) stood 2nd winning ₹10,000 cash award for developing a Lunch Box service software.",
        images: [
            "assets/achievements/prajwalan-2k23-team-virtual-bridge-4.jpg",
            "assets/achievements/prajwalan-2k23-team-virtual-bridge-1.jpg",
            "assets/achievements/prajwalan-2k23-team-virtual-bridge-2.jpg",
            "assets/achievements/prajwalan-2k23-team-virtual-bridge-3.jpg",
            "assets/achievements/prajwalan-2k23-team-virtual-bridge-5.jpg"
        ],
        featured: true,
        badges: [
            { icon: "fas fa-medal", text: "🥈 2nd Prize Winner" },
            { icon: "fas fa-laptop-code", text: "24-Hour Hackathon" },
            { icon: "fas fa-indian-rupee-sign", text: "₹10,000 Cash Award" },
            { icon: "fas fa-users", text: "45 AP Teams" },
            { icon: "fas fa-calendar-alt", text: "23rd & 24th March 2023" }
        ]
    }
];

let activeLightboxImages = [];
let activeImageIndex = 0;
let currentLightboxModal = null;
const cardSlideshowTimers = {};

document.addEventListener('DOMContentLoaded', function() {
    renderAchievements('all');
    
    // Lightbox modal instance
    const modalEl = document.getElementById('achievementLightboxModal');
    currentLightboxModal = new bootstrap.Modal(modalEl);

    // Controls
    document.getElementById('lightboxPrevBtn').addEventListener('click', showPrevImage);
    document.getElementById('lightboxNextBtn').addEventListener('click', showNextImage);

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!modalEl.classList.contains('show')) return;
        if (e.key === 'ArrowLeft') showPrevImage();
        if (e.key === 'ArrowRight') showNextImage();
    });

    // Pause video when modal hides
    modalEl.addEventListener('hidden.bs.modal', function() {
        const vid = document.getElementById('lightboxVideoPlayer');
        if (vid) vid.pause();
    });

    // Category Filter Event
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-category');
            renderAchievements(category);
        });
    });
});

function clearAllSlideshowTimers() {
    Object.keys(cardSlideshowTimers).forEach(id => {
        clearInterval(cardSlideshowTimers[id]);
        delete cardSlideshowTimers[id];
    });
}

function renderAchievements(selectedCategory = 'all') {
    const container = document.getElementById('achievementsContainer');
    container.innerHTML = '';
    clearAllSlideshowTimers();

    const filtered = selectedCategory === 'all' 
        ? studentAchievements 
        : studentAchievements.filter(a => a.category === selectedCategory);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="row g-4 justify-content-center">
                <div class="col-12 text-center py-5">
                    <div class="p-5 rounded-4 bg-white border border-light d-inline-block mx-auto" style="max-width: 500px;">
                        <div class="mx-auto mb-3 text-warning fs-1"><i class="fas fa-trophy"></i></div>
                        <h4 class="fw-bold text-dark font-outfit mb-2">No Achievements Listed</h4>
                        <p class="text-muted small mb-0">Student awards and accomplishments in this category will be updated soon.</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    // Grid Layout: 3 columns on Desktop (col-lg-4), 2 on Tablet (col-md-6), 1 on Mobile (col-12)
    const rowGrid = document.createElement('div');
    rowGrid.className = 'row g-4 align-items-stretch';

    filtered.forEach(item => {
        const colCard = document.createElement('div');
        colCard.className = 'col-lg-4 col-md-6 col-12 d-flex';

        colCard.innerHTML = `
            <div class="editorial-card w-100" id="card-${item.id}">
                <!-- HD Media Container (Image or Video) -->
                <div class="editorial-img-container" id="imgBox-${item.id}">
                    <span class="top-photo-badge">${item.award}</span>
                    
                    ${item.isVideo ? `
                        <video src="${item.video}" controls preload="metadata" style="width:100%; height:100%; object-fit:cover; border-radius:14px;" onclick="openLightbox('${item.id}', 0)"></video>
                        <span class="bottom-photo-count"><i class="fas fa-video me-1 text-warning"></i> Promo Video</span>
                    ` : `
                        <img id="activeMainImg-${item.id}" src="${item.images[0]}" alt="${item.title}" loading="lazy" onclick="openLightbox('${item.id}', getCurrentIdx('${item.id}'))">
                        <div class="editorial-img-overlay">
                            <span class="btn btn-light rounded-pill px-3 py-1.5 fw-bold text-dark small shadow-sm">
                                <i class="fas fa-search-plus me-1 text-warning"></i> View Photo
                            </span>
                        </div>
                        ${item.images.length > 1 ? `
                            <!-- Left/Right Slide Arrows -->
                            <button class="slide-arrow-btn slide-arrow-prev" onclick="event.stopPropagation(); stepSlideshow('${item.id}', -1)" aria-label="Previous Slide">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="slide-arrow-btn slide-arrow-next" onclick="event.stopPropagation(); stepSlideshow('${item.id}', 1)" aria-label="Next Slide">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="bottom-photo-count" id="photoCount-${item.id}">
                                <i class="fas fa-images me-1 text-warning"></i> Photo 1 of ${item.images.length}
                            </span>
                        ` : ''}
                    `}
                </div>

                ${(!item.isVideo && item.images.length > 1) ? `
                    <!-- Thumbnail Strip with Auto Slideshow Indicator -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-semibold" style="font-size: 0.72rem;">
                            <i class="fas fa-play me-1 text-warning"></i> Auto Slideshow (${item.images.length} Photos)
                        </span>
                        <span class="small text-secondary" style="font-size: 0.7rem;">Click photo to expand</span>
                    </div>
                    <div class="editorial-thumb-strip">
                        ${item.images.map((imgSrc, idx) => `
                            <div class="editorial-thumb ${idx === 0 ? 'active' : ''}" id="thumb-${item.id}-${idx}" onclick="switchGalleryImg('${item.id}', ${idx})">
                                <img src="${imgSrc}" alt="Thumb ${idx + 1}">
                            </div>
                        `).join('')}
                    </div>
                ` : ''}

                <!-- Category Tag -->
                <div class="mb-2">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        ${item.categoryName.toUpperCase()} • ${item.stage || 'NATIONAL LEVEL'}
                    </span>
                </div>

                <!-- Card Title -->
                <h3 class="font-outfit fw-bold text-dark mb-2" style="font-size: 1.22rem; line-height: 1.35;">
                    ${item.event}
                </h3>

                <!-- Team & Dept -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="fw-bold text-warning small text-truncate"><i class="fas fa-users me-1"></i> ${item.team}</span>
                    <span class="text-muted">•</span>
                    <span class="text-secondary small text-truncate"><i class="fas fa-university me-1"></i> ${item.department}</span>
                </div>

                <!-- Cash Award Spotlight Box -->
                <div class="editorial-award-box">
                    <div>
                        <span class="text-uppercase text-muted d-block fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Cash Prize</span>
                        <div class="editorial-award-amount">${item.cashAward}</div>
                    </div>
                    <span class="badge bg-dark text-white rounded-pill px-3 py-1.5 small fw-bold">${item.award}</span>
                </div>

                <!-- Description -->
                <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.65; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                    ${item.description}
                </p>

                <!-- Footer Metadata Pills -->
                <div class="d-flex flex-wrap gap-1.5 pt-3 border-top mt-auto">
                    ${item.badges.map(b => `
                        <div class="mini-badge">
                            <i class="${b.icon} text-warning"></i> ${b.text}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        rowGrid.appendChild(colCard);
    });

    container.appendChild(rowGrid);

    // Initialize Automatic Slideshow for image items with multiple images
    filtered.forEach(item => {
        if (!item.isVideo && item.images.length > 1) {
            initAutoSlideshow(item.id, item.images.length);
        }
    });
}

const currentIdxMap = {};

function getCurrentIdx(itemId) {
    return currentIdxMap[itemId] || 0;
}

function initAutoSlideshow(itemId, totalPhotos) {
    currentIdxMap[itemId] = 0;
    
    // Cycle every 3.5 seconds
    cardSlideshowTimers[itemId] = setInterval(() => {
        let current = getCurrentIdx(itemId);
        let next = (current + 1) % totalPhotos;
        switchGalleryImg(itemId, next);
    }, 3500);
}

function stepSlideshow(itemId, direction) {
    const item = studentAchievements.find(a => a.id === itemId);
    if (!item || item.images.length <= 1) return;

    let current = getCurrentIdx(itemId);
    let next = (current + direction + item.images.length) % item.images.length;
    switchGalleryImg(itemId, next);
}

function switchGalleryImg(itemId, index) {
    const item = studentAchievements.find(a => a.id === itemId);
    if (!item) return;

    currentIdxMap[itemId] = index;
    const mainImg = document.getElementById(`activeMainImg-${itemId}`);
    if (mainImg) {
        mainImg.style.opacity = '0.4';
        setTimeout(() => {
            mainImg.src = item.images[index];
            mainImg.style.opacity = '1';
        }, 120);
    }

    const countEl = document.getElementById(`photoCount-${itemId}`);
    if (countEl) {
        countEl.innerHTML = `<i class="fas fa-images me-1 text-warning"></i> Photo ${index + 1} of ${item.images.length}`;
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

    const mediaContainer = document.getElementById('lightboxMediaContainer');
    
    if (item.isVideo) {
        mediaContainer.innerHTML = `
            <video id="lightboxVideoPlayer" src="${item.video}" controls autoplay style="max-height: 82vh; max-width: 100%; object-fit: contain;"></video>
        `;
        document.getElementById('lightboxCounter').textContent = `Video Promo`;
    } else {
        mediaContainer.innerHTML = `
            <button class="lightbox-nav-btn lightbox-nav-prev" id="lightboxPrevBtn" onclick="showPrevImage()" aria-label="Previous Image">
                <i class="fas fa-chevron-left"></i>
            </button>
            <img id="lightboxMainImage" src="${item.images[index]}" alt="${item.title}" class="lightbox-img">
            <button class="lightbox-nav-btn lightbox-nav-next" id="lightboxNextBtn" onclick="showNextImage()" aria-label="Next Image">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        document.getElementById('lightboxCounter').textContent = `Image ${index + 1} of ${item.images.length}`;
    }

    document.getElementById('lightboxCaption').textContent = `${item.team} — ${item.title} (${item.event})`;
    document.getElementById('lightboxCategoryBadge').textContent = item.event.toUpperCase();

    currentLightboxModal.show();
}

function showPrevImage() {
    if (activeLightboxImages.length === 0) return;
    activeImageIndex = (activeImageIndex - 1 + activeLightboxImages.length) % activeLightboxImages.length;
    const imgEl = document.getElementById('lightboxMainImage');
    if (imgEl) imgEl.src = activeLightboxImages[activeImageIndex];
    document.getElementById('lightboxCounter').textContent = `Image ${activeImageIndex + 1} of ${activeLightboxImages.length}`;
}

function showNextImage() {
    if (activeLightboxImages.length === 0) return;
    activeImageIndex = (activeImageIndex + 1) % activeLightboxImages.length;
    const imgEl = document.getElementById('lightboxMainImage');
    if (imgEl) imgEl.src = activeLightboxImages[activeImageIndex];
    document.getElementById('lightboxCounter').textContent = `Image ${activeImageIndex + 1} of ${activeLightboxImages.length}`;
}
</script>

<?php include "footer.php"; ?>
</body>
</html>
