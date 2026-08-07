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

@keyframes floatSyllabus {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(-3deg); }
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
    animation: floatSyllabus 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.syllabus-table-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 35px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid #e2e8f0;
}

.syllabus-table-card h3 {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #0f172a;
    font-size: 1.5rem;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.table-custom {
    border-collapse: separate;
    border-spacing: 0 10px;
}

.table-custom th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
    padding: 16px 20px;
    border: none;
}

.table-custom td {
    background: #ffffff;
    padding: 16px 20px;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.table-custom td:first-child {
    border-left: 1px solid #f1f5f9;
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}

.table-custom td:last-child {
    border-right: 1px solid #f1f5f9;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}

.pdf-link {
    color: #4f46e5;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 10px;
    background: rgba(79, 70, 229, 0.08);
    transition: all 0.25s ease;
}

.pdf-link:hover {
    color: #ffffff;
    background: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
</style>

<body>
    <?php include 'nav.php'; ?>

    <!-- Animated Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #818cf8; background: rgba(129, 140, 248, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(129, 140, 248, 0.3);">
                        <i class="fas fa-clipboard-list" style="margin-right: 6px;"></i>Curriculum & Evaluation
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Course Syllabus</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #cbd5e1; max-width: 650px;">Complete regulation syllabus files, course outcomes, and model question papers for CSIT and CSD programs.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-clipboard-list" style="font-size: 60px; color: #818cf8; filter: drop-shadow(0 0 15px rgba(129, 140, 248, 0.6));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="main-content container py-4">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;">Syllabus Downloads</h2>
            <a href="academic-calendar.php" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 14px; font-weight: 700;">
                <i class="fas fa-arrow-left me-2"></i>Back to Academic Calendar
            </a>
        </div>

        <div class="syllabus-table-card">
            <h3><i class="fas fa-book-open" style="color: #6366f1;"></i> CSIT and CSD Syllabus &amp; Model Papers (1st to 3rd Year R23)</h3>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>CSIT Syllabus</th>
                            <th>CSD Syllabus</th>
                            <th>CSIT Model Papers</th>
                            <th>CSD Model Papers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1st Year -->
                        <tr>
                            <td class="fw-bold text-dark">Year 1 (R23)</td>
                            <td><a href="./files/B.Tech_R23_I_Year_CSIT_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Syllabus</a></td>
                            <td><a href="./files/B.Tech_R23_I_Year_CSG_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Syllabus</a></td>
                            <td><a href="./files/B.Tech_R23_I_Year_CSIT_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Model Papers</a></td>
                            <td><a href="./files/B.Tech_R23_I_Year_CSG_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> I Year Model Papers</a></td>
                        </tr>

                        <!-- 2nd Year -->
                        <tr>
                            <td class="fw-bold text-dark">Year 2 (R23)</td>
                            <td><a href="./files/B.Tech_R23_II_Year_CSIT_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Syllabus</a></td>
                            <td><a href="./files/B.Tech_R23_II_Year_CSG_Syllabus_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Syllabus</a></td>
                            <td><a href="./files/B.Tech_R23_II_Year_CSIT_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Model Papers</a></td>
                            <td><a href="./files/B.Tech_R23_II_Year_CSG_MQP_FINAL.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> II Year Model Papers</a></td>
                        </tr>

                        <!-- 3rd Year -->
                        <tr>
                            <td class="fw-bold text-dark">Year 3 (R23)</td>
                            <td><a href="./files/R23_3rd_YEAR_CSIT_SYLLABUS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Syllabus</a></td>
                            <td><a href="./files/R23_3RD_YEAR_CSD_SYLLABUS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Syllabus</a></td>
                            <td><a href="./files/R23_3RD_YEAR_CSIT_MQPS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Model Papers</a></td>
                            <td><a href="./files/R23_3RD_YEAR_CSD_MQPS.pdf" class="pdf-link" target="_blank"><i class="fas fa-file-pdf"></i> III Year Model Papers</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="syllabus-table-card">
            <h3><i class="fas fa-award" style="color: #10b981;"></i> CSD IV (R20) Syllabus &amp; Model Papers</h3>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Year & Regulation</th>
                            <th>Syllabus</th>
                            <th>Model Papers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-dark">Year 4 (R20)</td>
                            <td><a href="./files/B.Tech R20 IV Year CSG Syllabus FINAL ws.pdf" class="pdf-link" style="color: #10b981; background: rgba(16, 185, 129, 0.08);" target="_blank"><i class="fas fa-file-pdf"></i> IV Year R20 Syllabus</a></td>
                            <td><a href="./files/B.Tech R20 IV Year CSG MQP FINAL ws.pdf" class="pdf-link" style="color: #10b981; background: rgba(16, 185, 129, 0.08);" target="_blank"><i class="fas fa-file-pdf"></i> IV Year R20 Model Papers</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
