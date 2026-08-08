<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "connect.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heroes of Department - SRKR CSD & CSIT</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --accent: #f59e0b;
            --bg-dark: #0f172a;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Hero Header */
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31104b 100%);
            color: #ffffff;
            padding: 90px 0 70px;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(124, 58, 237, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero-title span {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Section Styling */
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-tag {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.08);
            padding: 6px 18px;
            border-radius: 999px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Hero Cards */
        .hero-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px 24px;
            text-align: center;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-card:hover {
            transform: translateY(-10px);
            border-color: #7c3aed;
            box-shadow: 0 20px 45px rgba(124, 58, 237, 0.18);
        }

        .hero-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.2);
            margin-bottom: 20px;
        }

        .hero-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .hero-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .hero-role {
            font-size: 0.9rem;
            font-weight: 600;
            color: #7c3aed;
            margin-bottom: 14px;
        }

        .hero-desc {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .hero-stats {
            margin-top: auto;
            width: 100%;
            display: flex;
            justify-content: space-around;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0f172a;
        }

        .stat-label {
            font-size: 0.72rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<!-- Hero Banner Section -->
<section class="hero-banner">
    <div class="container text-center">
        <span class="section-tag" style="background: rgba(255, 255, 255, 0.15); color: #fbbf24;">Hall of Fame</span>
        <h1 class="hero-title">Heroes of <span>CSD & CSIT</span></h1>
        <p class="lead mx-auto" style="max-width: 680px; color: #cbd5e1; font-size: 1.1rem; line-height: 1.6;">
            Celebrating extraordinary student champions, top innovators, house leaders, and distinguished faculty mentors driving technological excellence.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="section-header">
            <span class="section-tag">Department Champions</span>
            <h2 class="section-title">Star Contributors & Achievers</h2>
        </div>

        <div class="row g-4">
            <!-- Hero 1: Agni House Captain -->
            <div class="col-md-6 col-lg-4">
                <div class="hero-card">
                    <span class="hero-badge"><i class="fas fa-crown me-1"></i> House Captain</span>
                    <img src="./assets/logos/3.jpg" class="hero-avatar" alt="Agni Champion">
                    <h3 class="hero-name">Agni House Champions</h3>
                    <div class="hero-role">Fire House Leaders & Innovators</div>
                    <p class="hero-desc">Leading national hackathons and departmental coding competitions with fiery passion and perseverance.</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-value">1,250</div>
                            <div class="stat-label">Points</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">1st</div>
                            <div class="stat-label">Rank</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">12</div>
                            <div class="stat-label">Events Won</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero 2: Top Student Achiever -->
            <div class="col-md-6 col-lg-4">
                <div class="hero-card">
                    <span class="hero-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="fas fa-star me-1"></i> Top Student</span>
                    <img src="./assets/logos/allhouses.webp" class="hero-avatar" alt="Top Innovator">
                    <h3 class="hero-name">Student Innovators</h3>
                    <div class="hero-role">AI & Software Engineering Stars</div>
                    <p class="hero-desc">Published top IEEE research papers, created scalable open-source projects, and earned global certifications.</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-value">9.8</div>
                            <div class="stat-label">CGPA</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">15+</div>
                            <div class="stat-label">Projects</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">4</div>
                            <div class="stat-label">Patents</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero 3: Faculty Mentor -->
            <div class="col-md-6 col-lg-4">
                <div class="hero-card">
                    <span class="hero-badge" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);"><i class="fas fa-award me-1"></i> Faculty Mentor</span>
                    <img src="./assets/logos/2.jpg" class="hero-avatar" alt="Distinguished Mentor">
                    <h3 class="hero-name">Faculty Mentors</h3>
                    <div class="hero-role">CSD & CSIT Department Guides</div>
                    <p class="hero-desc">Empowering students through continuous research guidance, hackathon mentorship, and career building.</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-value">50+</div>
                            <div class="stat-label">Papers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">100%</div>
                            <div class="stat-label">Mentorship</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">8+</div>
                            <div class="stat-label">Awards</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="students_overview.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow" style="font-size: 0.95rem; font-weight: 700;">
                <i class="fas fa-trophy me-2"></i> View Full Leaderboard & Achievements
            </a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
