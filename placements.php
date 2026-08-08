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
    margin-bottom: 0;
}

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

.recruiters-section {
    padding: 60px 0;
    background: #fdfbf7;
}

.section-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    text-align: center;
    margin-bottom: 30px;
    color: #1a0d06;
}

.company-card {
    border-radius: 8px;
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
    margin-bottom: 16px;
    height: 100%;
}

.company-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.company-logo {
    height: 158px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    background: #f8fafc;
    border-radius: 6px;
}

.company-logo img {
    max-width: 180px;
    max-height: 258px;
    object-fit: contain;
}

.company-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    text-align: center;
    line-height: 1.3;
}

.company-domain {
    color: var(--text-secondary);
    font-size: 0.75rem;
    text-align: center;
    margin-bottom: 8px;
}

.package-info {
    font-weight: 500;
    color: var(--text-primary);
    text-align: center;
    margin-bottom: 8px;
    font-size: 0.75rem;
}

.offer-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.625rem;
    font-weight: 500;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-primary { background: #dbeafe; color: #1d4ed8; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-secondary { background: #f1f5f9; color: #475569; }
.badge-info { background: #cffafe; color: #0891b2; }

.view-all-btn {
    background: var(--primary);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.view-all-btn:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
}

.remaining-cards {
    display: none;
}

.process-section {
    padding: 40px 0;
    background: var(--bg-light);
}

.process-step {
    background: var(--white);
    padding: 20px 16px;
    border-radius: 8px;
    text-align: center;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    transition: all 0.2s ease;
    position: relative;
    height: 100%;
    margin-bottom: 20px;
}

.process-step:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.step-number {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    width: 24px;
    height: 24px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.step-icon {
    color: var(--primary);
    font-size: 1.5rem;
    margin: 8px 0 12px 0;
}

.step-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.step-description {
    color: var(--text-secondary);
    font-size: 0.75rem;
    line-height: 1.4;
}

.contact-section {
    padding: 40px 0;
    background: var(--primary);
    color: white;
}

.contact-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 20px 16px;
    border-radius: 8px;
    text-align: center;
    transition: all 0.2s ease;
    margin-bottom: 16px;
}

.contact-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.contact-icon {
    font-size: 1.5rem;
    margin-bottom: 8px;
    color: white;
    opacity: 0.9;
}

.contact-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 4px;
}

.contact-info {
    font-size: 0.75rem;
    opacity: 0.8;
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

.fade-in:nth-child(1) { animation-delay: 0.1s; }
.fade-in:nth-child(2) { animation-delay: 0.2s; }
.fade-in:nth-child(3) { animation-delay: 0.3s; }

@media (max-width: 768px) {
    .hero-section { padding: 40px 0 30px; }
    .stats-section, .recruiters-section, .process-section, .contact-section { padding: 30px 0; }
    .hero-title { font-size: 1.5rem; }
    .section-title { font-size: 1.25rem; }
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title fade-in">Placements & Careers</h1>
                <p class="hero-subtitle fade-in">Connecting talent with top opportunities</p>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-number">₹12L</div>
                        <div class="stat-label">Highest Package</div>
                        <div class="stat-sublabel">Microsoft India</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-number">₹5.1L</div>
                        <div class="stat-label">Average Package</div>
                        <div class="stat-sublabel">Industry Leading</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-number">66%</div>
                        <div class="stat-label">Placement Rate</div>
                        <div class="stat-sublabel">Students Placed</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Recruiters Section -->
    <section class="recruiters-section" style="background: #fdfbf7; padding: 60px 0;">
        <div class="container">
            <h2 class="section-title" style="color: #1a0d06; font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; text-align: center; margin-bottom: 20px;">Top <span style="color: #d97706;">Recruiters</span></h2>
            
            <!-- ReactBits Interactive Circular Gallery for Top Recruiters -->
            <div id="topRecruitersCircularGallery" style="height: 520px; width: 100%; position: relative; overflow: hidden; background: #ffffff; border-radius: 24px; border: 1px solid #f3eae1; box-shadow: 0 15px 40px rgba(0,0,0,0.07); margin-top: 10px;"></div>
        </div>
    </section>





    <!-- 3D Placement Dome Gallery Section -->
    <section class="photo-gallery-section" style="padding: 80px 0; background: #1a0d06; color: white;">
        <div class="container">
            <div class="section-title text-center" style="margin-bottom: 25px;">
                <h2 id="galleryDynamicTitle" style="font-family: var(--font-display); font-size: 2.8rem; font-weight: 900; margin-bottom: 15px; line-height: 1.1; color: white;">Placement <span style="color: var(--amber-gold);">2021-25 Batch Gallery</span></h2>
                <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto 25px; color: #e5d5c5; font-family: var(--font-heading);">Drag to rotate the interactive 3D sphere dome. Click any placement poster to expand.</p>

                <!-- Batch Switcher Slide Controls -->
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
                    <button class="batch-tab-btn" id="btnBatch2021" onclick="switchBatchGallery('2021-25')" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; border: 1px solid #b45309; padding: 10px 26px; border-radius: 50px; font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 6px 18px rgba(180, 83, 9, 0.3);">
                        <i class="fas fa-layer-group me-2"></i>2021-25 Batch Gallery
                    </button>
                    <button class="batch-tab-btn" id="btnBatch2022" onclick="switchBatchGallery('2022-26')" style="background: #fffbeb; color: #78350f; border: 1px solid #fde68a; padding: 10px 26px; border-radius: 50px; font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-layer-group me-2"></i>2022-26 Batch Gallery
                    </button>
                </div>
            </div>
            
            <div id="wrapper2021" class="batch-gallery-wrapper">
                <div id="placementDomeGallery"></div>
            </div>

            <div id="wrapper2022" class="batch-gallery-wrapper" style="display: none;">
                <div id="placement2022_26DomeGallery"></div>
            </div>
        </div>
    </section>

    <!-- Dome Gallery 3D & Circular Gallery Engines -->
    <script src="assets/js/dome-gallery.js"></script>
    <script src="assets/js/circular-gallery.js"></script>

    <script>
    function switchBatchGallery(batch) {
        const title = document.getElementById('galleryDynamicTitle');
        const btn2021 = document.getElementById('btnBatch2021');
        const btn2022 = document.getElementById('btnBatch2022');
        const wrap2021 = document.getElementById('wrapper2021');
        const wrap2022 = document.getElementById('wrapper2022');

        if (batch === '2021-25') {
            title.innerHTML = 'Placement <span style="color: var(--amber-gold);">2021-25 Batch Gallery</span>';
            btn2021.style.background = 'linear-gradient(135deg, #d97706 0%, #b45309 100%)';
            btn2021.style.color = '#ffffff';
            btn2021.style.borderColor = '#b45309';

            btn2022.style.background = '#fffbeb';
            btn2022.style.color = '#78350f';
            btn2022.style.borderColor = '#fde68a';

            wrap2021.style.display = 'block';
            wrap2022.style.display = 'none';
            if (typeof window.initDome2021 === 'function') window.initDome2021();
        } else if (batch === '2022-26') {
            title.innerHTML = 'Placement <span style="color: var(--amber-gold);">2022-26 Batch Gallery</span>';
            btn2022.style.background = 'linear-gradient(135deg, #d97706 0%, #b45309 100%)';
            btn2022.style.color = '#ffffff';
            btn2022.style.borderColor = '#b45309';

            btn2021.style.background = '#fffbeb';
            btn2021.style.color = '#78350f';
            btn2021.style.borderColor = '#fde68a';

            wrap2021.style.display = 'none';
            wrap2022.style.display = 'block';
            if (typeof window.initDome2022 === 'function') window.initDome2022();
        }
        window.dispatchEvent(new Event('resize'));
    }
    </script>

    <?php include "footer.php"; ?>
    
    <script>
        // Enhanced JavaScript with modern functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all fade-in elements
            document.querySelectorAll('.fade-in').forEach(el => {
                observer.observe(el);
            });

            // Recruiters toggle functionality
            const remainingCards = document.querySelector('.remaining-cards');
            const viewAllBtn = document.getElementById('viewAllBtn');
            let isExpanded = false;

            function toggleRecruiters() {
                isExpanded = !isExpanded;
                
                if (isExpanded) {
                    remainingCards.style.display = 'flex';
                    viewAllBtn.innerHTML = '<i class="fas fa-chevron-up me-2"></i>Show Less';
                    
                    // Animate in the new cards
                    setTimeout(() => {
                        remainingCards.querySelectorAll('.company-card').forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });
                    }, 50);
                    
                    // Smooth scroll to show new content
                    setTimeout(() => {
                        remainingCards.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest' 
                        });
                    }, 200);
                } else {
                    remainingCards.style.display = 'none';
                    viewAllBtn.innerHTML = '<i class="fas fa-chevron-down me-2"></i>View All Recruiters';
                    
                    // Scroll back to the button
                    viewAllBtn.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }

            // Initialize remaining cards state
            remainingCards.style.display = 'none';
            remainingCards.querySelectorAll('.company-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.4s ease';
            });

            viewAllBtn.innerHTML = '<i class="fas fa-chevron-down me-2"></i>View All Recruiters';
            viewAllBtn.addEventListener('click', toggleRecruiters);

            // Add smooth scrolling for any anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add loading states and micro-interactions
            document.querySelectorAll('.view-all-btn, .contact-card').forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Parallax effect for hero section (optional)
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const heroSection = document.querySelector('.hero-section');
                if (heroSection && scrolled < heroSection.offsetHeight) {
                    heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
            });
        });

        // Global function for backward compatibility
        function toggleRecruiters() {
            document.getElementById('viewAllBtn').click();
        }
    </script>
</body>
</html>
