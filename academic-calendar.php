<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'head.php'; 
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
    margin-bottom: 40px;
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

@keyframes floatCalendar {
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
    animation: floatCalendar 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.calendar-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.06);
    border: 1px solid #f3eae1;
    transition: all 0.35s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.calendar-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(217, 119, 6, 0.15);
    border-color: rgba(217, 119, 6, 0.3);
}

.download-btn {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: #ffffff;
    font-weight: 700;
    padding: 12px 24px;
    border-radius: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 16px rgba(180, 83, 9, 0.25);
}

.download-btn:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(180, 83, 9, 0.4);
}
</style>

<body>
    <?php include 'nav.php'; ?>

    <!-- Animated Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(251, 191, 36, 0.3);">
                        <i class="fas fa-calendar-check" style="margin-right: 6px;"></i>Official Schedule
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Academic Calendar</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #e5d5c5; max-width: 650px;">Official academic schedules, semester start dates, examination schedules, and holidays for 2025-26.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-calendar-alt" style="font-size: 60px; color: #fbbf24; filter: drop-shadow(0 0 15px rgba(251, 191, 36, 0.6));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="main-content container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1a0d06; margin: 0;">2025-26 Downloads</h2>
            <a href="syllabus.php" class="btn btn-warning px-4 py-2" style="border-radius: 14px; font-weight: 700; background: #d97706; border-color: #d97706; color: white;">
                <i class="fas fa-clipboard-list me-2"></i>Go to Syllabus
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1a0d06; margin: 0;">II &amp; III B.Tech Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #6f5f54;">Official schedule for 2nd and 3rd year students</span>
                        </div>
                    </div>
                    <a href="files/II_III_B.Tech_Academic_Calendar_2025-26.pdf" class="download-btn" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1a0d06; margin: 0;">IV B.Tech Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #6f5f54;">Official schedule for final year students</span>
                        </div>
                    </div>
                    <a href="files/IV_B.Tech_Academic_Calendar_2025-26.pdf" class="download-btn" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1a0d06; margin: 0;">1st Year Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #6f5f54;">Official schedule for 1st year students</span>
                        </div>
                    </div>
                    <a href="files/1_btech_ac.pdf" class="download-btn" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
