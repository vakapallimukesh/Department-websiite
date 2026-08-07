<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'head.php'; 
?>

<style>
body {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    background: #f8fafc;
    color: #334155;
    overflow-x: hidden;
}

/* Animated Hero Section */
.hero-section {
    background: linear-gradient(-45deg, #0f172a, #1e1b4b, #312e81, #0f172a);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
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
    background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.7;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
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
    border: 1px solid rgba(255, 255, 255, 0.18);
    animation: floatCalendar 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.calendar-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid #e2e8f0;
    transition: all 0.35s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.calendar-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.3);
}

.download-btn {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    font-weight: 700;
    padding: 12px 24px;
    border-radius: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);
}

.download-btn:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(99, 102, 241, 0.4);
}
</style>

<body>
    <?php include 'nav.php'; ?>

    <!-- Animated Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #6366f1; background: rgba(99, 102, 241, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(99, 102, 241, 0.3);">
                        <i class="fas fa-calendar-check" style="margin-right: 6px;"></i>Official Schedule
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Academic Calendar</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #cbd5e1; max-width: 650px;">Official academic schedules, semester start dates, examination schedules, and holidays for 2025-26.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-calendar-alt" style="font-size: 60px; color: #818cf8; filter: drop-shadow(0 0 15px rgba(129, 140, 248, 0.6));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="main-content container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;">2025-26 Downloads</h2>
            <a href="syllabus.php" class="btn btn-outline-primary px-4 py-2" style="border-radius: 14px; font-weight: 700;">
                <i class="fas fa-clipboard-list me-2"></i>Go to Syllabus
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(99, 102, 241, 0.1); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;">II &amp; III B.Tech Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #64748b;">Official schedule for 2nd and 3rd year students</span>
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
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;">IV B.Tech Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #64748b;">Official schedule for final year students</span>
                        </div>
                    </div>
                    <a href="files/IV_B.Tech_Academic_Calendar_2025-26.pdf" class="download-btn" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; border-radius: 16px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h5 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;">1st Year Academic Calendar 2025-26</h5>
                            <span style="font-size: 0.88rem; color: #64748b;">Official schedule for 1st year students</span>
                        </div>
                    </div>
                    <a href="files/1_btech_ac.pdf" class="download-btn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
