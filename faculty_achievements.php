<?php 
if (session_status() == PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --primary-gold: #d97706;
    --primary-amber: #f59e0b;
    --rich-espresso: #1a0d06;
    --card-bg: #ffffff;
    --cream-bg: #fdfbf7;
    --text-dark: #0f172a;
    --text-muted: #64748b;
    --border-subtle: #f1e5d8;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--cream-bg);
    color: var(--text-dark);
    line-height: 1.6;
}

/* Hero Section */
.hero-banner {
    background: linear-gradient(135deg, #1a0d06 0%, #2c1509 50%, #421e0b 100%);
    color: white;
    padding: 85px 20px 70px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(245, 158, 11, 0.18) 1.2px, transparent 1.2px);
    background-size: 28px 28px;
    opacity: 0.75;
    pointer-events: none;
}

.hero-tag {
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--primary-amber);
    background: rgba(245, 158, 11, 0.12);
    padding: 7px 20px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 16px;
    border: 1px solid rgba(245, 158, 11, 0.3);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15);
}

.hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 3.2rem;
    font-weight: 900;
    line-height: 1.15;
    margin-bottom: 18px;
}

.hero-title span {
    color: var(--primary-amber);
    background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Stat Card */
.stat-card {
    background: #ffffff;
    border: 1.5px solid var(--border-subtle);
    border-radius: 20px;
    padding: 26px 20px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(217, 119, 6, 0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-gold);
    box-shadow: 0 15px 35px rgba(217, 119, 6, 0.12);
}

.stat-number {
    font-family: 'Outfit', sans-serif;
    font-size: 2.6rem;
    font-weight: 900;
    color: var(--primary-gold);
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-top: 4px;
}

/* NPTEL Star Achievement Card - Decreased size by 25% */
.nptel-card {
    background: #ffffff;
    border: 1.5px solid #f1e5d8;
    border-radius: 18px;
    overflow: hidden;
    height: 100%;
    max-width: 320px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.nptel-card:hover {
    transform: translateY(-6px);
    border-color: var(--primary-gold);
    box-shadow: 0 16px 35px rgba(217, 119, 6, 0.16);
}

.nptel-img-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 4.8;
    max-height: 250px;
    background: #1a0d06;
    overflow: hidden;
    cursor: pointer;
}

.nptel-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top center;
    transition: transform 0.5s ease;
}

.nptel-card:hover .nptel-img {
    transform: scale(1.05);
}

.nptel-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(26, 13, 6, 0.85) 100%);
    display: flex;
    align-items: flex-end;
    padding: 14px;
    opacity: 0.9;
    transition: opacity 0.3s ease;
}

.zoom-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(26, 13, 6, 0.75);
    backdrop-filter: blur(8px);
    color: #ffffff;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
}

.nptel-card:hover .zoom-badge {
    background: var(--primary-gold);
    border-color: #ffffff;
}

.nptel-body {
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.star-title-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.4px;
    margin-bottom: 10px;
    width: fit-content;
}

.badge-domain {
    background: #fff7ed;
    color: #c2410c;
    border: 1.5px solid #ffedd5;
}

.badge-discipline {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1.5px solid #dbeafe;
}

.faculty-name {
    font-family: 'Outfit', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 2px;
}

.faculty-role {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--primary-gold);
    margin-bottom: 10px;
}

.achievement-desc {
    font-size: 0.82rem;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 14px;
    flex-grow: 1;
}

.meta-footer {
    padding-top: 12px;
    border-top: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    font-weight: 700;
}

.meta-period {
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-issuer {
    color: #16a34a;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Lightbox Modal */
.modal-content {
    border-radius: 24px;
    border: none;
    overflow: hidden;
}

.modal-header {
    background: #1a0d06;
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px 28px;
}

.modal-title {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1.3rem;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* General Faculty Card */
.faculty-card {
    background: #ffffff;
    border: 1.5px solid var(--border-subtle);
    border-radius: 24px;
    padding: 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.faculty-card:hover {
    transform: translateY(-8px);
    border-color: var(--primary-gold);
    box-shadow: 0 20px 40px rgba(217, 119, 6, 0.15);
}

.badge-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: #fdfbf7;
    border: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: var(--primary-gold);
}
</style>

<?php include "nav.php"; ?>

<!-- Hero Section -->
<section class="hero-banner">
    <div class="container position-relative z-1">
        <span class="hero-tag"><i class="fas fa-award me-2"></i> Faculty Hall of Excellence</span>
        <h1 class="hero-title">Faculty <span>Achievements</span></h1>
        <p class="lead mx-auto" style="max-width: 720px; color: #e5d5c5; font-size: 1.15rem; line-height: 1.65;">
            Celebrating national NPTEL Domain & Discipline Stars, research grants, patents, authored books, and academic honors of our CSD & CSIT faculty members.
        </p>
    </div>
</section>



<!-- NPTEL Stars Feature Showcase Section -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #f1e5d8; border-bottom: 1px solid #f1e5d8;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="hero-tag" style="background: rgba(217, 119, 6, 0.08); color: #d97706; border-color: rgba(217, 119, 6, 0.2);">National Recognition</span>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06;">NPTEL <span style="color: #d97706;">Star Recognitions</span></h2>
            <p style="color: #64748b; font-size: 1.05rem; max-width: 650px; margin: 0 auto;">Honoring CSD & CSIT faculty members recognized by NPTEL, SWAYAM & IIT Madras for national-level domain expertise and continuous learning.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- NPTEL Card 1 -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="nptel-card">
                    <div class="nptel-img-wrapper" onclick="openCertificateModal('assets/faculty_achievements/nptel_domain_star_trinadh_naidu.png', 'K V V S Trinadh Naidu - NPTEL Domain Star')">
                        <img src="assets/faculty_achievements/nptel_domain_star_trinadh_naidu.png" alt="K V V S Trinadh Naidu - NPTEL Domain Star" class="nptel-img">
                        <div class="zoom-badge"><i class="fas fa-search-plus"></i> View Poster</div>
                        <div class="nptel-img-overlay">
                            <span class="text-white fw-bold small"><i class="fas fa-certificate me-1 text-warning"></i> SWAYAM NPTEL Certificate</span>
                        </div>
                    </div>
                    <div class="nptel-body">
                        <span class="star-title-badge badge-domain">
                            <i class="fas fa-star text-warning"></i> NPTEL DOMAIN STAR
                        </span>
                        <h3 class="faculty-name">K V V S Trinadh Naidu</h3>
                        <div class="faculty-role">Asst. Professor, CSIT Department</div>
                        <p class="achievement-desc">
                            Awarded the prestigious <strong>NPTEL Domain Star</strong> in <em>Programming (Computer Science)</em> by IIT Madras for demonstrating exceptional domain mastery across multiple core software engineering certifications.
                        </p>
                        <div class="meta-footer">
                            <span class="meta-period"><i class="far fa-calendar-alt text-amber"></i> April 2025</span>
                            <span class="meta-issuer"><i class="fas fa-check-circle"></i> IIT Madras / NPTEL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NPTEL Card 2 -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="nptel-card">
                    <div class="nptel-img-wrapper" onclick="openCertificateModal('assets/faculty_achievements/nptel_discipline_star_neti_praveen.png', 'Neti Praveen - NPTEL Discipline Star')">
                        <img src="assets/faculty_achievements/nptel_discipline_star_neti_praveen.png" alt="Neti Praveen - NPTEL Discipline Star" class="nptel-img">
                        <div class="zoom-badge"><i class="fas fa-search-plus"></i> View Poster</div>
                        <div class="nptel-img-overlay">
                            <span class="text-white fw-bold small"><i class="fas fa-certificate me-1 text-warning"></i> SWAYAM NPTEL Certificate</span>
                        </div>
                    </div>
                    <div class="nptel-body">
                        <span class="star-title-badge badge-discipline">
                            <i class="fas fa-star text-primary"></i> NPTEL DISCIPLINE STAR
                        </span>
                        <h3 class="faculty-name">Neti Praveen</h3>
                        <div class="faculty-role">Asst. Professor, CSIT Department</div>
                        <p class="achievement-desc">
                            Conferred the national <strong>NPTEL Discipline Star</strong> title in <em>Computer Science & Engineering</em> for completing 4+ advanced courses in a single 12-month period with elite scoring performance.
                        </p>
                        <div class="meta-footer">
                            <span class="meta-period"><i class="far fa-calendar-alt"></i> Jan - Apr 2025</span>
                            <span class="meta-issuer"><i class="fas fa-check-circle"></i> IIT Madras / NPTEL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NPTEL Card 3 -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="nptel-card">
                    <div class="nptel-img-wrapper" onclick="openCertificateModal('assets/faculty_achievements/nptel_discipline_star_angara_satyam.png', 'Angara Satyam - NPTEL Discipline Star')">
                        <img src="assets/faculty_achievements/nptel_discipline_star_angara_satyam.png" alt="Angara Satyam - NPTEL Discipline Star" class="nptel-img">
                        <div class="zoom-badge"><i class="fas fa-search-plus"></i> View Poster</div>
                        <div class="nptel-img-overlay">
                            <span class="text-white fw-bold small"><i class="fas fa-certificate me-1 text-warning"></i> SWAYAM NPTEL Certificate</span>
                        </div>
                    </div>
                    <div class="nptel-body">
                        <span class="star-title-badge badge-discipline">
                            <i class="fas fa-star text-primary"></i> NPTEL DISCIPLINE STAR
                        </span>
                        <h3 class="faculty-name">Angara Satyam</h3>
                        <div class="faculty-role">Asst. Professor, CSD Department</div>
                        <p class="achievement-desc">
                            Honored with the national <strong>NPTEL Discipline Star</strong> award in <em>Computer Science and Engineering</em> by IIT Madras for outstanding academic achievements across core computer science tracks.
                        </p>
                        <div class="meta-footer">
                            <span class="meta-period"><i class="far fa-calendar-alt"></i> Jul - Dec 2024</span>
                            <span class="meta-issuer"><i class="fas fa-check-circle"></i> IIT Madras / NPTEL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NPTEL Card 4 -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="nptel-card">
                    <div class="nptel-img-wrapper" onclick="openCertificateModal('assets/faculty_achievements/nptel_discipline_star_kasagana_srinivasa_rao.png', 'Kasagana Srinivasa Rao - NPTEL Discipline Star')">
                        <img src="assets/faculty_achievements/nptel_discipline_star_kasagana_srinivasa_rao.png" alt="Kasagana Srinivasa Rao - NPTEL Discipline Star" class="nptel-img">
                        <div class="zoom-badge"><i class="fas fa-search-plus"></i> View Poster</div>
                        <div class="nptel-img-overlay">
                            <span class="text-white fw-bold small"><i class="fas fa-certificate me-1 text-warning"></i> SWAYAM NPTEL Certificate</span>
                        </div>
                    </div>
                    <div class="nptel-body">
                        <span class="star-title-badge badge-discipline">
                            <i class="fas fa-star text-primary"></i> NPTEL DISCIPLINE STAR
                        </span>
                        <h3 class="faculty-name">Kasagana Srinivasa Rao</h3>
                        <div class="faculty-role">Asst. Professor, CSD Department</div>
                        <p class="achievement-desc">
                            Recognized as an <strong>NPTEL Discipline Star</strong> by NPTEL & SWAYAM for exemplary performance in advanced CSE coursework, reinforcing pedagogical mastery and technical leadership.
                        </p>
                        <div class="meta-footer">
                            <span class="meta-period"><i class="far fa-calendar-alt"></i> Jan - Apr 2026</span>
                            <span class="meta-issuer"><i class="fas fa-check-circle"></i> IIT Madras / NPTEL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NPTEL Card 5 -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="nptel-card">
                    <div class="nptel-img-wrapper" onclick="openCertificateModal('assets/faculty_achievements/nptel_discipline_star_trinadh_naidu.png', 'K V V S Trinadh Naidu - NPTEL Discipline Star')">
                        <img src="assets/faculty_achievements/nptel_discipline_star_trinadh_naidu.png" alt="K V V S Trinadh Naidu - NPTEL Discipline Star" class="nptel-img">
                        <div class="zoom-badge"><i class="fas fa-search-plus"></i> View Poster</div>
                        <div class="nptel-img-overlay">
                            <span class="text-white fw-bold small"><i class="fas fa-certificate me-1 text-warning"></i> SWAYAM NPTEL Certificate</span>
                        </div>
                    </div>
                    <div class="nptel-body">
                        <span class="star-title-badge badge-discipline">
                            <i class="fas fa-star text-primary"></i> NPTEL DISCIPLINE STAR
                        </span>
                        <h3 class="faculty-name">K V V S Trinadh Naidu</h3>
                        <div class="faculty-role">Asst. Professor, CSIT Department</div>
                        <p class="achievement-desc">
                            Honored with his second national recognition as an <strong>NPTEL Discipline Star</strong> in <em>Computer Science & Engineering</em>, setting a remarkable milestone for continuous learning excellence.
                        </p>
                        <div class="meta-footer">
                            <span class="meta-period"><i class="far fa-calendar-alt"></i> Jul - Dec 2025</span>
                            <span class="meta-issuer"><i class="fas fa-check-circle"></i> IIT Madras / NPTEL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Certificate Poster Lightbox Modal -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificateModalLabel"><i class="fas fa-award text-warning me-2"></i> Faculty Achievement Certificate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-dark text-center">
                <img id="modalCertificateImg" src="" alt="Certificate" class="img-fluid w-100" style="max-height: 82vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
function openCertificateModal(imgSrc, title) {
    document.getElementById('modalCertificateImg').src = imgSrc;
    document.getElementById('certificateModalLabel').innerHTML = '<i class="fas fa-award text-warning me-2"></i> ' + title;
    var modal = new bootstrap.Modal(document.getElementById('certificateModal'));
    modal.show();
}
</script>

<?php include "footer.php"; ?>
</body>
</html>
