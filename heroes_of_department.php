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

        /* CR Card Custom Styling */
        .cr-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cr-card:hover {
            transform: translateY(-8px) scale(1.015);
            border-color: #7c3aed;
            box-shadow: 0 20px 45px rgba(124, 58, 237, 0.18);
        }

        .cr-avatar-container {
            width: 140px;
            height: 140px;
        }

        .cr-avatar {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 20px;
            border: 4px solid #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .cr-card:hover .cr-avatar {
            transform: scale(1.05);
        }

        .cr-group-btn {
            border-radius: 999px;
            font-weight: 600;
            padding: 8px 20px;
            font-size: 0.88rem;
            transition: all 0.25s ease;
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
            Celebrating extraordinary student champions, class representatives, top innovators, house leaders, and distinguished faculty mentors driving technological excellence.
        </p>
    </div>
</section>

<!-- HEROES OF THE DEPARTMENT SECTION (TOP) -->
<section class="py-5 bg-light border-bottom" id="heroes-of-department">
    <div class="container py-4">
        <div class="section-header text-center mb-5">
            <span class="section-tag" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="fas fa-medal me-2"></i>Hall of Fame
            </span>
            <h2 class="section-title mt-2">Heroes of the Department</h2>
            <p class="lead text-secondary mx-auto mt-2" style="max-width: 680px; font-size: 1.05rem; line-height: 1.6;">
                Honoring exceptional student achievers, TEDx organizers, martial arts champions, classical dancers, and leaders representing CSD & CSIT departments with glory.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Hero Card 1: P.B.S Kruti -->
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden w-100 d-flex flex-column" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="position: relative; height: 260px; overflow: hidden; background: #0f172a;">
                        <img src="assets/achievements/classical-dance-1st-kruti.jpg" alt="P.B.S Kruti" style="width: 100%; height: 100%; object-fit: cover; image-rendering: -webkit-optimize-contrast;">
                        <span class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 m-3 rounded-pill fw-bold small shadow-sm">
                            🥇 1st Prize Classical Dance
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <!-- Row 1: Name -->
                        <h3 class="fw-extrabold text-dark font-outfit mb-1" style="font-size: 1.35rem; letter-spacing: -0.3px;">P.B.S Kruti</h3>
                        
                        <!-- Row 2: Register No -->
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong>25B91A0789</strong>
                            </span>
                        </div>

                        <!-- Row 3: 4 to 5 Line Context -->
                        <p class="text-secondary small mb-0 flex-grow-1" style="line-height: 1.6; font-size: 0.86rem; text-align: justify;">
                            P.B.S Kruti is an exceptional classical dancer who secured 1st Prize in Classical Dance Group Performance at the 45th Annual Day Celebrations of SRKREC. Renowned for her mesmerizing expressions, mudras, and devotion to traditional Indian arts, she balances academics with cultural leadership. Her artistic excellence and talent bring immense honor to our department. She stands as a proud cultural hero inspiring students across campus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hero Card 2: R. Lakshmi Prasanna -->
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden w-100 d-flex flex-column" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="position: relative; height: 260px; overflow: hidden; background: #0f172a;">
                        <img src="assets/achievements/classical-dance-2nd-lakshmi-prasanna.jpg" alt="R. Lakshmi Prasanna" style="width: 100%; height: 100%; object-fit: cover; image-rendering: -webkit-optimize-contrast;">
                        <span class="position-absolute top-0 end-0 bg-secondary text-white px-3 py-1 m-3 rounded-pill fw-bold small shadow-sm">
                            🥈 2nd Prize Classical Dance
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <!-- Row 1: Name -->
                        <h3 class="fw-extrabold text-dark font-outfit mb-1" style="font-size: 1.35rem; letter-spacing: -0.3px;">R. Lakshmi Prasanna</h3>
                        
                        <!-- Row 2: Register No -->
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong>24B91A6245</strong>
                            </span>
                        </div>

                        <!-- Row 3: 4 to 5 Line Context -->
                        <p class="text-secondary small mb-0 flex-grow-1" style="line-height: 1.6; font-size: 0.86rem; text-align: justify;">
                            R. Lakshmi Prasanna is a passionate performing artist who won 2nd Prize in Classical Dance Group Performance at the 45th Annual Day Celebrations of SRKREC. Celebrated for her technical precision, graceful stage presence, and expressive mudras, she illuminates every cultural event. She represents the department with immense pride, elegance, and artistic enthusiasm. Lakshmi Prasanna demonstrates that cutting-edge technology, design, and classical arts go hand in hand.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hero Card 3: D Pooja Sai Praveena -->
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden w-100 d-flex flex-column" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="position: relative; height: 260px; overflow: hidden; background: #0f172a;">
                        <img src="assets/achievements/karate-gold-pooja-sai-praveena.jpg" alt="D Pooja Sai Praveena" style="width: 100%; height: 100%; object-fit: cover; image-rendering: -webkit-optimize-contrast;">
                        <span class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 m-3 rounded-pill fw-bold small shadow-sm">
                            🥇 Gold Medalist Karate
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <!-- Row 1: Name -->
                        <h3 class="fw-extrabold text-dark font-outfit mb-1" style="font-size: 1.35rem; letter-spacing: -0.3px;">D Pooja Sai Praveena</h3>
                        
                        <!-- Row 2: Register No -->
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong>24B91A6218</strong>
                            </span>
                        </div>

                        <!-- Row 3: 4 to 5 Line Context -->
                        <p class="text-secondary small mb-0 flex-grow-1" style="line-height: 1.6; font-size: 0.86rem; text-align: justify;">
                            D Pooja Sai Praveena is a formidable martial artist who secured the Gold Medal 🥇 in the JNTUK Inter-Collegiate Karate Tournament with dominant performances. She proudly represented JNTUK University at the South-West Inter-University Karate Championship 2024–25 in Chennai. Her athletic power, discipline, and courage inspire students across our institution. She is a true department hero who proves that hard work, grit, and focus produce national champions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Hero Card 4: Preeti Avvula -->
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden w-100 d-flex flex-column" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="position: relative; height: 260px; overflow: hidden; background: #0f172a;">
                        <img src="assets/images/heroes/preeti-avvula.jpg" alt="Preeti Avvula" style="width: 100%; height: 100%; object-fit: cover; object-position: top center; image-rendering: -webkit-optimize-contrast;">
                        <span class="position-absolute top-0 end-0 bg-dark text-white border border-secondary px-3 py-1 m-3 rounded-pill fw-bold small shadow-sm">
                            🎙️ TEDx Organizer & Anchor
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column text-start">
                        <!-- Row 1: Name -->
                        <h3 class="fw-extrabold text-dark font-outfit mb-1" style="font-size: 1.35rem; letter-spacing: -0.3px;">Preeti Avvula</h3>
                        
                        <!-- Row 2: Register No -->
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong>24B91A0701</strong>
                            </span>
                        </div>

                        <!-- Row 3: 4 to 5 Line Context -->
                        <p class="text-secondary small mb-0 flex-grow-1" style="line-height: 1.6; font-size: 0.86rem; text-align: justify;">
                            Preeti Avvula is a dynamic student leader and master anchor who served as a core organizer for TEDx SRKR. Known for her powerful stage presence, eloquence, and exceptional event coordination, she led the independently organized TED event with distinction. Her leadership, communication skills, and passion for hosting campus conferences inspire students across the department. She exemplifies true public speaking excellence and organizational brilliance as a department hero.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CLASS REPRESENTATIVES SECTION (BOTTOM) -->
<section class="py-5 bg-white border-bottom" id="class-representatives">
    <div class="container py-3">
        <div class="section-header mb-4">
            <span class="section-tag" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                <i class="fas fa-user-shield me-2"></i>Class Leadership
            </span>
            <h2 class="section-title mt-2">Class Representatives</h2>
            <p class="lead text-secondary mx-auto mt-2" style="max-width: 650px; font-size: 1.05rem; line-height: 1.6;">
                Meet the student representatives who coordinate and represent our department classes.
            </p>
        </div>

        <?php
        // Centralized Reusable Data Structure for 2nd Year & 3rd Year CR Profiles
        $secondYearCRs = [
            [
                'id' => 'cr-5',
                'name' => 'JAVVADI MOHANA DURGA',
                'registrationNumber' => '25B91A6223',
                'className' => 'CSD - II Year',
                'branch' => 'CSD',
                'year' => 'II Year',
                'section' => '',
                'image' => 'public/images/cr/javvadi-mohana-durga.jpg',
                'badgeColor' => 'bg-success'
            ],
            [
                'id' => 'cr-6',
                'name' => 'VASA HARI NAGENDRA PRATAP',
                'registrationNumber' => '25B91A6263',
                'className' => 'CSD - II Year',
                'branch' => 'CSD',
                'year' => 'II Year',
                'section' => '',
                'image' => 'public/images/cr/vasa-hari-nagendra-pratap.jpg',
                'badgeColor' => 'bg-success'
            ],
            [
                'id' => 'cr-3',
                'name' => 'P HARSHA',
                'registrationNumber' => '25B91A0786',
                'className' => 'CSIT - II Year - Section A',
                'branch' => 'CSIT',
                'year' => 'II Year',
                'section' => 'Section A',
                'image' => 'public/images/cr/p-harsha.jpg',
                'badgeColor' => 'bg-info text-dark'
            ],
            [
                'id' => 'cr-4',
                'name' => 'B J S V D N ASRITHA',
                'registrationNumber' => '25B91A0711',
                'className' => 'CSIT - II Year - Section A',
                'branch' => 'CSIT',
                'year' => 'II Year',
                'section' => 'Section A',
                'image' => 'public/images/cr/b-j-s-v-d-n-asritha.jpg',
                'badgeColor' => 'bg-info text-dark'
            ],
            [
                'id' => 'cr-1',
                'name' => 'PAMU AMRUTHA',
                'registrationNumber' => '25B91A0782',
                'className' => 'CSIT - II Year - Section B',
                'branch' => 'CSIT',
                'year' => 'II Year',
                'section' => 'Section B',
                'image' => 'public/images/cr/pamu-amrutha.jpg',
                'badgeColor' => 'bg-primary'
            ],
            [
                'id' => 'cr-2',
                'name' => 'B PRASANNA VARUN',
                'registrationNumber' => '25B91A0717',
                'className' => 'CSIT - II Year - Section B',
                'branch' => 'CSIT',
                'year' => 'II Year',
                'section' => 'Section B',
                'image' => 'public/images/cr/b-prasanna-varun.jpg',
                'badgeColor' => 'bg-primary'
            ]
        ];

        $thirdYearCRs = [
            [
                'id' => 'cr-7',
                'name' => 'CHANDANI VIVEKANANDA',
                'registrationNumber' => '24B91A0720',
                'className' => 'CSIT - III Year - Section A',
                'branch' => 'CSIT',
                'year' => 'III Year',
                'section' => 'Section A',
                'image' => 'public/images/cr/chandani-vivekananda.jpg',
                'badgeColor' => 'bg-warning text-dark'
            ],
            [
                'id' => 'cr-8',
                'name' => 'THOTA JOHAN BENEDICT',
                'registrationNumber' => '24B91A07B7',
                'className' => 'CSIT - III Year - Section B',
                'branch' => 'CSIT',
                'year' => 'III Year',
                'section' => 'Section B',
                'image' => 'public/images/cr/thota-johan-benedict.jpg',
                'badgeColor' => 'bg-danger'
            ],
            [
                'id' => 'cr-9',
                'name' => 'S D RANI',
                'registrationNumber' => '24B91A07B3',
                'className' => 'CSIT - III Year - Section B',
                'branch' => 'CSIT',
                'year' => 'III Year',
                'section' => 'Section B',
                'image' => 'public/images/cr/s-d-rani.jpg',
                'badgeColor' => 'bg-danger'
            ]
        ];

        $fourthYearCRs = [
            [
                'id' => 'cr-10',
                'name' => 'P SAI HARSHA',
                'registrationNumber' => '23B81A6252',
                'className' => 'CSD - IV Year',
                'branch' => 'CSD',
                'year' => 'IV Year',
                'section' => '',
                'image' => 'public/images/cr/p-sai-harsha.jpg',
                'badgeColor' => 'bg-purple text-white',
                'customBadgeStyle' => 'background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);'
            ],
            [
                'id' => 'cr-11',
                'name' => 'P SWAPNA',
                'registrationNumber' => '23B91A6255',
                'className' => 'CSD - IV Year',
                'branch' => 'CSD',
                'year' => 'IV Year',
                'section' => '',
                'image' => 'public/images/cr/p-swapna.jpg',
                'badgeColor' => 'bg-purple text-white',
                'customBadgeStyle' => 'background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);'
            ]
        ];
        ?>

        <!-- 2nd Year CR Section -->
        <div class="cr-year-group mb-5" data-year-group="2ndYear">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">2nd</div>
                <div>
                    <h3 class="fw-extrabold text-dark font-outfit mb-0" style="font-size: 1.6rem;">2nd Year Class Representatives</h3>
                    <p class="text-muted small mb-0">CSIT Section A, CSIT Section B & CSD II Year Representatives</p>
                </div>
            </div>

            <!-- CR Cards Grid (2nd Year) -->
            <div class="row g-4 justify-content-start">
                <?php foreach ($secondYearCRs as $cr): 
                    $groupClass = str_replace(' ', '', $cr['branch'] . '-' . str_replace(' ', '', $cr['section'] ? $cr['section'] : $cr['year']));
                ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4 d-flex cr-item-card" data-group="<?= htmlspecialchars($groupClass) ?>" data-year="2ndYear">
                        <div class="cr-card w-100 p-4 rounded-4 shadow-sm border bg-white text-center position-relative transition-all d-flex flex-column align-items-center">
                            
                            <!-- Class Badge -->
                            <span class="badge <?= htmlspecialchars($cr['badgeColor']) ?> px-3 py-2 rounded-pill fw-bold mb-3 shadow-xs" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="fas fa-users-cog me-1"></i> <?= htmlspecialchars($cr['className']) ?>
                            </span>

                            <!-- CR Avatar -->
                            <div class="cr-avatar-container mb-3 position-relative">
                                <img src="<?= htmlspecialchars($cr['image']) ?>" 
                                     alt="<?= htmlspecialchars($cr['name']) ?>" 
                                     class="cr-avatar shadow"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($cr['name']) ?>&background=7c3aed&color=fff&size=150'">
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Verified Class Representative">
                                    <span class="visually-hidden">Verified</span>
                                </span>
                            </div>

                            <!-- CR Name -->
                            <h4 class="fw-extrabold text-dark mb-1 font-outfit" style="font-size: 1.25rem; letter-spacing: -0.3px;">
                                <?= htmlspecialchars($cr['name']) ?>
                            </h4>

                            <!-- Registration Number -->
                            <div>
                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                    <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong><?= htmlspecialchars($cr['registrationNumber']) ?></strong>
                                </span>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3rd Year CR Section (Below 2nd Years) -->
        <div class="cr-year-group mt-5 mb-5" data-year-group="3rdYear">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">3rd</div>
                <div>
                    <h3 class="fw-extrabold text-dark font-outfit mb-0" style="font-size: 1.6rem;">3rd Year Class Representatives</h3>
                    <p class="text-muted small mb-0">CSIT Section A & CSIT Section B Representatives</p>
                </div>
            </div>

            <!-- CR Cards Grid (3rd Year) -->
            <div class="row g-4 justify-content-start">
                <?php foreach ($thirdYearCRs as $cr): 
                    $groupClass = str_replace(' ', '', $cr['branch'] . '-' . str_replace(' ', '', $cr['section'] ? $cr['section'] : $cr['year']));
                    if ($cr['branch'] === 'CSIT' && $cr['year'] === 'III Year' && $cr['section'] === 'Section A') $groupClass = 'CSIT-III-A';
                    if ($cr['branch'] === 'CSIT' && $cr['year'] === 'III Year' && $cr['section'] === 'Section B') $groupClass = 'CSIT-III-B';
                ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4 d-flex cr-item-card" data-group="<?= htmlspecialchars($groupClass) ?>" data-year="3rdYear">
                        <div class="cr-card w-100 p-4 rounded-4 shadow-sm border bg-white text-center position-relative transition-all d-flex flex-column align-items-center">
                            
                            <!-- Class Badge -->
                            <span class="badge <?= htmlspecialchars($cr['badgeColor']) ?> px-3 py-2 rounded-pill fw-bold mb-3 shadow-xs" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($cr['className']) ?>
                            </span>

                            <!-- CR Avatar -->
                            <div class="cr-avatar-container mb-3 position-relative">
                                <img src="<?= htmlspecialchars($cr['image']) ?>" 
                                     alt="<?= htmlspecialchars($cr['name']) ?>" 
                                     class="cr-avatar shadow"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($cr['name']) ?>&background=d97706&color=fff&size=150'">
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Verified Class Representative">
                                    <span class="visually-hidden">Verified</span>
                                </span>
                            </div>

                            <!-- CR Name -->
                            <h4 class="fw-extrabold text-dark mb-1 font-outfit" style="font-size: 1.25rem; letter-spacing: -0.3px;">
                                <?= htmlspecialchars($cr['name']) ?>
                            </h4>

                            <!-- Registration Number -->
                            <div>
                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                    <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong><?= htmlspecialchars($cr['registrationNumber']) ?></strong>
                                </span>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4th Year CR Section (Below 3rd Years) -->
        <div class="cr-year-group mt-5" data-year-group="4thYear">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);">4th</div>
                <div>
                    <h3 class="fw-extrabold text-dark font-outfit mb-0" style="font-size: 1.6rem;">4th Year Class Representatives</h3>
                    <p class="text-muted small mb-0">CSD IV Year Class Representatives</p>
                </div>
            </div>

            <!-- CR Cards Grid (4th Year) -->
            <div class="row g-4 justify-content-start">
                <?php foreach ($fourthYearCRs as $cr): 
                    $groupClass = str_replace(' ', '', $cr['branch'] . '-' . str_replace(' ', '', $cr['section'] ? $cr['section'] : $cr['year']));
                ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4 d-flex cr-item-card" data-group="<?= htmlspecialchars($groupClass) ?>" data-year="4thYear">
                        <div class="cr-card w-100 p-4 rounded-4 shadow-sm border bg-white text-center position-relative transition-all d-flex flex-column align-items-center">
                            
                            <!-- Class Badge -->
                            <span class="badge <?= htmlspecialchars($cr['badgeColor']) ?> px-3 py-2 rounded-pill fw-bold mb-3 shadow-xs" style="font-size: 0.8rem; letter-spacing: 0.5px; <?= isset($cr['customBadgeStyle']) ? $cr['customBadgeStyle'] : '' ?>">
                                <i class="fas fa-crown me-1"></i> <?= htmlspecialchars($cr['className']) ?>
                            </span>

                            <!-- CR Avatar -->
                            <div class="cr-avatar-container mb-3 position-relative">
                                <img src="<?= htmlspecialchars($cr['image']) ?>" 
                                     alt="<?= htmlspecialchars($cr['name']) ?>" 
                                     class="cr-avatar shadow"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($cr['name']) ?>&background=7c3aed&color=fff&size=150'">
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Verified Class Representative">
                                    <span class="visually-hidden">Verified</span>
                                </span>
                            </div>

                            <!-- CR Name -->
                            <h4 class="fw-extrabold text-dark mb-1 font-outfit" style="font-size: 1.25rem; letter-spacing: -0.3px;">
                                <?= htmlspecialchars($cr['name']) ?>
                            </h4>

                            <!-- Registration Number -->
                            <div>
                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                    <i class="fas fa-id-card me-1 text-primary"></i> Reg: <strong><?= htmlspecialchars($cr['registrationNumber']) ?></strong>
                                </span>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<!-- Script for Filter Logic -->
<script>
function toggleCrPhone(btn) {
    const box = btn.closest('.cr-phone-box');
    const display = box.querySelector('.cr-phone-display');
    btn.classList.add('d-none');
    display.classList.remove('d-none');
}

function filterCRs(group, btnEl) {
    document.querySelectorAll('.cr-group-btn').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    btnEl.classList.remove('btn-outline-secondary');
    btnEl.classList.add('active', 'btn-primary');

    document.querySelectorAll('.cr-item-card').forEach(card => {
        const itemGroup = card.getAttribute('data-group');
        const itemYear = card.getAttribute('data-year');
        if (group === 'all' || itemGroup === group || itemYear === group) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });

    // Toggle year group titles visibility based on active filter
    document.querySelectorAll('.cr-year-group').forEach(yearSec => {
        const visibleCards = yearSec.querySelectorAll('.cr-item-card[style*="display: flex"], .cr-item-card:not([style*="display: none"])');
        if (visibleCards.length > 0) {
            yearSec.style.display = 'block';
        } else {
            yearSec.style.display = 'none';
        }
    });
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>
