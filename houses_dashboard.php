<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 
include "connect.php";

// Define the house names with enhanced color schemes and details from house_detail pages
$houses = [
    'Aakash' => [
        'name' => 'Aakash', 
        'color' => '#4A90E2', 
        'gradient' => 'linear-gradient(135deg, #4A90E2 0%, #357ABD 100%)',
        'light_color' => '#E3F2FD',
        'icon' => 'fas fa-cloud',
        'element' => 'Sky Element',
        'description' => 'Sky House - Reaching for the stars',
        'full_description' => 'Sky House - Reaching for the stars with boundless ambition and limitless potential. Members of Aakash House are known for their visionary thinking and ability to soar above challenges.',
        'img' => 'assets/img/aakash_bg.jpg'
    ],
    'Jal' => [
        'name' => 'Jal', 
        'color' => '#2196F3', 
        'gradient' => 'linear-gradient(135deg, #2196F3 0%, #1976D2 100%)',
        'light_color' => '#E1F5FE',
        'icon' => 'fas fa-water',
        'element' => 'Water Element',
        'description' => 'Water House - Flowing with wisdom',
        'full_description' => 'Water House - Flowing with wisdom and adaptability like the eternal river. Jal House members embody fluidity, persistence, and the power to shape their path through any obstacle.',
        'img' => 'assets/img/jal_bg.jpg'
    ],
    'Vayu' => [
        'name' => 'Vayu', 
        'color' => '#4CAF50', 
        'gradient' => 'linear-gradient(135deg, #4CAF50 0%, #388E3C 100%)',
        'light_color' => '#E8F5E8',
        'icon' => 'fas fa-wind',
        'element' => 'Wind Element',
        'description' => 'Wind House - Swift and free',
        'full_description' => 'Wind House - Swift and free like the breeze that carries change across the world. Vayu House students are dynamic, innovative, and bring fresh perspectives to every challenge.',
        'img' => 'assets/img/vayu_bg.png'
    ],
    'PRUDHVI' => [
        'name' => 'PRUDHVI', 
        'color' => '#8D6E63', 
        'gradient' => 'linear-gradient(135deg, #8D6E63 0%, #6D4C41 100%)',
        'light_color' => '#EFEBE9',
        'icon' => 'fas fa-mountain',
        'element' => 'Earth Element',
        'description' => 'Earth House - Strong and steady',
        'full_description' => 'Earth House - Strong and steady with grounded wisdom and solid foundation. PRUDHVI House members exemplify resilience, strength, and unwavering dependability.',
        'img' => 'assets/img/prudhvi_bg.jpg'    
    ],
    'Agni' => [
        'name' => 'Agni', 
        'color' => '#F44336', 
        'gradient' => 'linear-gradient(135deg, #F44336 0%, #D32F2F 100%)',
        'light_color' => '#FFEBEE',
        'icon' => 'fas fa-fire',
        'element' => 'Fire Element',
        'description' => 'Fire House - Burning with passion',
        'full_description' => 'Fire House - Burning with passion and illuminating the path forward with fierce determination. Agni House students are energetic, passionate, and ignite inspiration in everyone around them.',
        'img' => 'assets/img/agni_bg.jpg'
    ]
];



// Single Dataset (Explicitly matching csd-csit.page.gd/houses_dashboard.php)
$house_stats = [
    'Aakash' => [
        'student_count' => 124,
        'winners_points' => 0,
        'participants_points' => 0,
        'organizers_points' => 0,
        'appreciations_points' => 0,
        'penalties_points' => 0,
        'total_points' => 0,
        'avg_points' => 0
    ],
    'Jal' => [
        'student_count' => 107,
        'winners_points' => 0,
        'participants_points' => 0,
        'organizers_points' => 0,
        'appreciations_points' => 0,
        'penalties_points' => 0,
        'total_points' => 0,
        'avg_points' => 0
    ],
    'Vayu' => [
        'student_count' => 116,
        'winners_points' => 0,
        'participants_points' => 0,
        'organizers_points' => 0,
        'appreciations_points' => 0,
        'penalties_points' => 0,
        'total_points' => 0,
        'avg_points' => 0
    ],
    'PRUDHVI' => [
        'student_count' => 111,
        'winners_points' => 0,
        'participants_points' => 0,
        'organizers_points' => 0,
        'appreciations_points' => 0,
        'penalties_points' => 0,
        'total_points' => 0,
        'avg_points' => 0
    ],
    'Agni' => [
        'student_count' => 113,
        'winners_points' => 0,
        'participants_points' => 0,
        'organizers_points' => 0,
        'appreciations_points' => 0,
        'penalties_points' => 0,
        'total_points' => 0,
        'avg_points' => 0
    ]
];

$overall_stats = [
    'total_houses' => 5,
    'total_events' => 3,
    'total_points' => 0,
    'active_students' => 558
];

$houses = [
    'Aakash' => $houses['Aakash'],
    'Jal' => $houses['Jal'],
    'Vayu' => $houses['Vayu'],
    'PRUDHVI' => $houses['PRUDHVI'],
    'Agni' => $houses['Agni']
];
?>

<style>
/* Modern Stats Cards Styles */
.stats-container {
    padding: 0 15px;
    margin-bottom: 3rem;
}

.stats-grid {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    min-width: 200px;
    flex: 1;
    max-width: 240px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border-color: #e0e0e0;
}

.stat-icon-container {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon-container i {
    font-size: 20px;
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: #9e9e9e;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.1;
}

/* House Cards */
.house-card {
    transition: all 0.3s ease !important;
}

.house-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 12px 32px rgba(0,0,0,0.15) !important;
}

.house-card:hover .house-overlay {
    opacity: 0.95 !important;
}

.house-link:hover .house-card {
    border-color: var(--primary-color) !important;
}

/* Modern Button Styling */
.btn-outline-primary {
    background: white !important;
    border: 1px solid #e9ecef !important;
    color: #6c757d !important;
    padding: 12px 24px !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
    position: relative !important;
    overflow: hidden !important;
}

.btn-outline-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s ease;
}

.btn-outline-primary:hover::before {
    left: 100%;
}

.btn-outline-primary:hover {
    color: #667eea !important;
    border-color: #667eea !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .house-card {
        height: 260px !important;
    }
    
    .stats-grid {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-card {
        min-width: unset;
        max-width: unset;
        padding: 20px;
        gap: 14px;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-icon-container {
        width: 40px;
        height: 40px;
    }
    
    .stat-icon-container i {
        font-size: 18px;
    }
}

@media (max-width: 576px) {
    .house-card {
        height: 240px !important;
    }
    
    .house-card h4 {
        font-size: 1.3rem !important;
    }
    
    .house-card h6 {
        font-size: 0.9rem !important;
    }
    
    .stats-container {
        padding: 0 10px;
    }
    
    .stat-card {
        padding: 16px;
        gap: 12px;
    }
    
    .stat-value {
        font-size: 1.3rem;
    }
    
    .stat-icon-container {
        width: 36px;
        height: 36px;
    }
    
    .stat-icon-container i {
        font-size: 16px;
    }
}

/* Contributors Section */
.contributors-container {
    padding: 0 15px;
}

.contributors-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
}

.contributors-header {
    padding: 24px 28px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafbfc;
}

.contributors-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.contributors-title i {
    color: #667eea;
    font-size: 1rem;
}

.contributors-content {
    padding: 28px;
}

/* Filter styling updates */
.form-label {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    color: #6c757d !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
}

.form-select {
    border: 1px solid #e9ecef !important;
    border-radius: 12px !important;
    font-size: 0.9rem !important;
    background: #fafbfc !important;
    transition: all 0.3s ease !important;
    padding: 10px 16px !important;
}

.form-select:focus {
    border-color: #667eea !important;
    background: white !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

/* Table styling updates */
.table-responsive {
    border-radius: 12px !important;
    border: 1px solid #f0f0f0 !important;
    background: white !important;
}

.table th {
    background: #fafbfc !important;
    border: none !important;
    font-weight: 600 !important;
    color: #495057 !important;
    font-size: 0.85rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 16px 12px !important;
}

.table td {
    border: none !important;
    padding: 14px 12px !important;
    vertical-align: middle !important;
}

.btn-outline-primary.show-more-btn {
    background: #f8f9fa !important;
    border: 1px solid #e9ecef !important;
    color: #667eea !important;
    padding: 10px 20px !important;
    border-radius: 20px !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
}

.btn-outline-primary.show-more-btn:hover {
    background: #667eea !important;
    color: white !important;
    border-color: #667eea !important;
    transform: translateY(-1px) !important;
}

/* Transparent Glassmorphism Hero Explore Button Style */
.hero-explore-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 240px;
    height: 60px;
    background: rgba(255, 255, 255, 0.35) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    color: #2D1810 !important;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 700;
    border: 2px solid rgba(139, 69, 19, 0.55) !important;
    border-radius: 50px;
    cursor: pointer;
    text-decoration: none !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.hero-explore-button,
.hero-explore-button *,
.hero-explore-button:hover,
.hero-explore-button:focus,
.hero-explore-button:active,
.hero-explore-button:visited {
    text-decoration: none !important;
}

.hero-explore-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(139, 69, 19, 0.15);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.hero-explore-button:hover::before {
    width: 300px;
    height: 300px;
}

.hero-explore-button:hover {
    transform: translateY(-3px) scale(1.04);
    background: rgba(255, 255, 255, 0.6) !important;
    border-color: #8B4513 !important;
    box-shadow: 0 12px 35px rgba(139, 69, 19, 0.25) !important;
    color: #8B4513 !important;
}

.hero-explore-button i {
    transition: transform 0.3s ease;
    color: #8B4513 !important;
}

.hero-explore-button:hover i {
    transform: translateX(5px);
}

/* Placement Section Matching Theme for Houses Dashboard */
body {
    background: #fdfbf7 !important;
    color: #1a0d06 !important;
    font-family: 'Inter', 'Plus Jakarta Sans', sans-serif !important;
}

/* Stat Cards - Placement Theme Style */
.stat-card {
    background: #ffffff !important;
    border: 1px solid #f3eae1 !important;
    border-top: 4px solid #d97706 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
    border-radius: 20px !important;
    transition: all 0.3s ease !important;
}

.stat-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 20px 45px rgba(180, 83, 9, 0.16) !important;
    border-color: rgba(217, 119, 6, 0.4) !important;
}

.stat-label {
    color: #1a0d06 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
}

.stat-value {
    color: #d97706 !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 900 !important;
    font-size: 2.2rem !important;
}

.stat-icon-container {
    background-color: #fdf7ee !important;
    border: 1px solid #f5e6d3 !important;
}

.stat-icon-container i {
    color: #d97706 !important;
}

/* Contributors & Matrix Container - Clean White Placement Style Card */
.contributors-card {
    background: #ffffff !important;
    border: 1px solid #f3eae1 !important;
    border-top: 4px solid #d97706 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
    border-radius: 20px !important;
    color: #1a0d06 !important;
    transition: all 0.3s ease !important;
}

.contributors-header {
    background: linear-gradient(135deg, #fdfbf7 0%, #faf5ee 100%) !important;
    border-bottom: 1px solid #f3eae1 !important;
    padding: 22px 28px !important;
}

.contributors-content {
    background: #ffffff !important;
}

.contributors-title {
    color: #1a0d06 !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 800 !important;
}

.contributors-title i, .text-primary {
    color: #d97706 !important;
}

.text-muted {
    color: #6f5f54 !important;
}

.form-label {
    color: #1a0d06 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 700 !important;
}

.form-select {
    background-color: #ffffff !important;
    color: #1a0d06 !important;
    border: 1px solid #f3eae1 !important;
}

.form-select option {
    background-color: #ffffff !important;
    color: #1a0d06 !important;
}

.form-select:focus {
    border-color: #d97706 !important;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.18) !important;
}

.table-responsive {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

.table {
    background: transparent !important;
    color: #1a0d06 !important;
}

.table th {
    background: #fdf7ee !important;
    color: #b45309 !important;
    border-bottom: 2px solid #f5e6d3 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 800 !important;
}

.table td {
    background: transparent !important;
    border-bottom: 1px solid #f3eae1 !important;
    color: #1a0d06 !important;
}

.table tr {
    background: transparent !important;
}

.table tr:hover {
    background: #fdf7ee !important;
}

.table strong {
    color: #1a0d06 !important;
}

.bg-light {
    background-color: #fdf7ee !important;
    color: #1a0d06 !important;
    border-color: #f5e6d3 !important;
}

#compTabs {
    background: #fdf7ee !important;
    border: 1px solid #f5e6d3 !important;
}

#compTabs .nav-link {
    color: #6f5f54 !important;
}

#compTabs .nav-link.active {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(180, 83, 9, 0.25) !important;
}

.btn-outline-primary {
    background: #ffffff !important;
    color: #b45309 !important;
    border: 1.5px solid #f3eae1 !important;
    box-shadow: 0 4px 15px rgba(180, 83, 9, 0.08) !important;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
    box-shadow: 0 6px 20px rgba(180, 83, 9, 0.25) !important;
    color: #ffffff !important;
}

/* Element House Cards - Placement Section Card Theme */
.house-card {
    border-radius: 20px !important;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease !important;
    position: relative;
    overflow: hidden;
    height: 290px !important;
    cursor: pointer;
}

/* House Cards - Clean Placement Theme with Visible Photos */
.house-card-Aakash {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.78) 0%, rgba(227, 242, 253, 0.65) 100%), url('assets/img/aakash_bg.jpg') center/cover no-repeat !important;
    border: 1.5px solid #f3eae1 !important;
    border-top: 4px solid #4A90E2 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
}

.house-card-Aakash:hover {
    transform: translateY(-5px) !important;
    border-color: #357ABD !important;
    box-shadow: 0 20px 45px rgba(74, 144, 226, 0.3) !important;
}

.house-card-Agni {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.78) 0%, rgba(255, 235, 238, 0.65) 100%), url('assets/img/agni_bg.jpg') center/cover no-repeat !important;
    border: 1.5px solid #f3eae1 !important;
    border-top: 4px solid #F44336 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
}

.house-card-Agni:hover {
    transform: translateY(-5px) !important;
    border-color: #D32F2F !important;
    box-shadow: 0 20px 45px rgba(244, 67, 54, 0.3) !important;
}

.house-card-Jal {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.78) 0%, rgba(225, 245, 254, 0.65) 100%), url('assets/img/jal_bg.jpg') center/cover no-repeat !important;
    border: 1.5px solid #f3eae1 !important;
    border-top: 4px solid #2196F3 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
}

.house-card-Jal:hover {
    transform: translateY(-5px) !important;
    border-color: #1976D2 !important;
    box-shadow: 0 20px 45px rgba(33, 150, 243, 0.3) !important;
}

.house-card-PRUDHVI, .house-card-Pruthvi {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.78) 0%, rgba(239, 235, 233, 0.65) 100%), url('assets/img/prudhvi_bg.jpg') center/cover no-repeat !important;
    border: 1.5px solid #f3eae1 !important;
    border-top: 4px solid #8D6E63 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
}

.house-card-PRUDHVI:hover, .house-card-Pruthvi:hover {
    transform: translateY(-5px) !important;
    border-color: #6D4C41 !important;
    box-shadow: 0 20px 45px rgba(141, 110, 99, 0.3) !important;
}

.house-card-Vayu {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.78) 0%, rgba(232, 245, 233, 0.65) 100%), url('assets/img/vayu_bg.png') center bottom/cover no-repeat !important;
    border: 1.5px solid #f3eae1 !important;
    border-top: 4px solid #4CAF50 !important;
    box-shadow: 0 10px 30px rgba(180, 83, 9, 0.08) !important;
}

.house-card-Vayu:hover {
    transform: translateY(-5px) !important;
    border-color: #388E3C !important;
    box-shadow: 0 20px 45px rgba(76, 175, 80, 0.3) !important;
}
</style>

<body style="background: #fdfbf7; min-height: 100vh; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; color: #1a0d06;">
    <!-- Main Header -->
    <?php include "nav.php"; ?>
    
    <!-- Main Content -->
    <div class="main-content" style="padding: 2rem 0 3rem 0;">
        <div class="container ">

            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 px-2">
                <div>
                    <h3 style="font-weight: 800; font-family: 'Outfit', sans-serif; color: #1a0d06; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-trophy" style="color: #d97706;"></i> House Standings
                    </h3>
                    <p class="text-muted mb-0" style="font-size: 0.88rem; font-weight: 500;">Overall performance and house point rankings</p>
                </div>
            </div>

            <!-- Houses Grid -->
            <div class="row p-2 justify-content-center" id="housesGridContainer">
                <?php 
                $rank_counter = 1;
                foreach ($houses as $house_key => $house_info): 
                    $clean_key = ($house_key === 'PRUDHVI' || $house_key === 'Pruthvi') ? 'PRUDHVI' : (($house_key === 'Jal' || $house_key === 'JAL') ? 'Jal' : (($house_key === 'Vayu' || $house_key === 'VAYU') ? 'Vayu' : $house_key));
                ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3" style='width:260px'>
                        <a href="house_detail.php?house=<?php echo urlencode($house_key); ?>" 
                           class="text-decoration-none house-link">
                            <div class="house-card house-card-<?php echo $clean_key; ?>" 
                                 data-house="<?php echo $house_key; ?>">
                                
                                <!-- Rank Badge -->
                                <div style="position: absolute; top: 12px; right: 12px; z-index: 3;">
                                    <span style="background: <?php echo $house_info['color']; ?>; 
                                                color: white; 
                                                padding: 4px 8px; 
                                                border-radius: 12px; 
                                                font-size: 0.75rem; 
                                                font-weight: 600;">
                                        #<?php echo $rank_counter; ?>
                                    </span>
                                </div>
                                
                                <!-- House Header -->
                                <div style="padding: 20px 20px 0 20px; text-align: left;">
                                    <!-- House Name -->
                                    <div style="margin-bottom: 8px;">
                                        <h4 style="color: <?php echo $house_info['color']; ?>; 
                                                   font-weight: 800; 
                                                   font-size: 1.5rem;
                                                   margin: 0;
                                                   line-height: 1.2;
                                                   text-shadow: 0 1px 2px rgba(255,255,255,0.8);">
                                            <?php echo $house_info['name']; ?>
                                        </h4>
                                        <h6 style="color: #1f2937; 
                                                   font-weight: 700; 
                                                   font-size: 0.95rem;
                                                   margin: 2px 0 0 0;
                                                   opacity: 1;">
                                            House
                                        </h6>
                                    </div>
                                    
                                    <!-- House Description -->
                                    <p style="color: #111827; 
                                              font-weight: 600;
                                              font-size: 0.85rem;
                                              margin: 0 0 16px 0;
                                              line-height: 1.4;">
                                        <?php echo $house_info['description']; ?>
                                    </p>
                                </div>
                                
                                <!-- Points Section -->
                                <div style="padding: 0 20px; margin-bottom: 16px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="color: #1f2937; font-size: 0.85rem; font-weight: 700;">Points</span>
                                        <span style="color: #111827; font-size: 1.25rem; font-weight: 800;">
                                            <?php echo number_format($house_stats[$house_key]['total_points']); ?>
                                        </span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div style="width: 100%; 
                                               height: 6px; 
                                               background: #e9ecef; 
                                               border-radius: 3px; 
                                               margin-top: 8px; 
                                               overflow: hidden;">
                                        <?php 
                                        // Calculate progress percentage (assuming max points for visualization)
                                        $max_points = max(array_column($house_stats, 'total_points'));
                                        $progress = $max_points > 0 ? ($house_stats[$house_key]['total_points'] / $max_points) * 100 : 0;
                                        ?>
                                        <div style="width: <?php echo $progress; ?>%; 
                                                   height: 100%; 
                                                   background: <?php echo $house_info['color']; ?>; 
                                                   border-radius: 3px;
                                                   transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                                
                                <!-- Bottom Section -->
                                <div style="position: absolute; 
                                           bottom: 0; 
                                           left: 0; 
                                           right: 0; 
                                           padding: 16px 20px; 
                                           background: <?php echo $house_info['light_color']; ?>; 
                                           border-top: 1px solid <?php echo $house_info['color']; ?>20;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-users" style="color: <?php echo $house_info['color']; ?>; font-size: 0.9rem;"></i>
                                            <span style="color: #1f2937; font-size: 0.85rem; font-weight: 700;">
                                                <?php echo $house_stats[$house_key]['student_count']; ?> students
                                            </span>
                                        </div>
                                        <i class="fas fa-arrow-right" style="color: <?php echo $house_info['color']; ?>; font-size: 0.9rem;"></i>
                                    </div>
                                </div>
                                
                                <!-- Hover Effect -->
                                <div class="house-overlay" style="
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    bottom: 0;
                                    background: <?php 
                                        if ($house_key === 'Aakash') {
                                            echo 'linear-gradient(135deg, rgba(30, 136, 229, 0.70) 0%, rgba(79, 195, 247, 0.70) 100%), url(\'assets/img/aakash_bg.jpg\') center/cover no-repeat';
                                        } elseif ($house_key === 'Agni') {
                                            echo 'linear-gradient(135deg, rgba(244, 67, 54, 0.75) 0%, rgba(211, 47, 47, 0.75) 100%), url(\'assets/img/agni_bg.jpg\') center/cover no-repeat';
                                        } elseif (strtoupper($house_key) === 'PRUDHVI' || $house_key === 'Pruthvi') {
                                            echo 'linear-gradient(135deg, rgba(141, 110, 99, 0.75) 0%, rgba(109, 76, 65, 0.75) 100%), url(\'assets/img/prudhvi_bg.jpg\') center/cover no-repeat';
                                        } elseif (strtoupper($house_key) === 'JAL' || $house_key === 'Jal') {
                                            echo 'linear-gradient(135deg, rgba(33, 150, 243, 0.75) 0%, rgba(25, 118, 210, 0.75) 100%), url(\'assets/img/jal_bg.jpg\') center/cover no-repeat';
                                        } elseif (strtoupper($house_key) === 'VAYU' || $house_key === 'Vayu') {
                                            echo 'linear-gradient(135deg, rgba(76, 175, 80, 0.75) 0%, rgba(56, 142, 60, 0.75) 100%), url(\'assets/img/vayu_bg.png\') center bottom/cover no-repeat';
                                        } else {
                                            echo $house_info['gradient'];
                                        }
                                    ?>;
                                    opacity: 0;
                                    transition: all 0.3s ease;
                                    border-radius: 15px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;">
                                    <div style="color: white; text-align: center;">
                                        <i class="fas fa-eye" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                        <div style="font-size: 1rem; font-weight: 600;">View Details</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php 
                    $rank_counter++;
                endforeach; 
                ?>
            </div>
            <div class="text-center my-5">
                <a href="events_overview.php" class="hero-explore-button">
                    Explore Events
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <!-- Key Statistics Section -->
            <div class="stats-container mb-5">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon-container">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Houses</div>
                            <div class="stat-value" id="statTotalHouses"><?php echo $overall_stats['total_houses']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-container">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Events</div>
                            <div class="stat-value" id="statTotalEvents"><?php echo $overall_stats['total_events']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-container">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Points</div>
                            <div class="stat-value" id="statTotalPoints"><?php echo number_format($overall_stats['total_points']); ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-container">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Active Students</div>
                            <div class="stat-value" id="statActiveStudents"><?php echo number_format($overall_stats['active_students']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive House Comparison Module -->
            <div class="contributors-container mb-5">
                <div class="contributors-card">
                    <div class="contributors-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="contributors-title" style="color: #1a253c; font-size: 1.25rem;">
                                <i class="fas fa-balance-scale text-primary"></i>
                                House Performance Comparison Matrix
                            </h4>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; margin-top: 4px;">
                                Comprehensive side-by-side analysis of House points, events, and student performance
                            </p>
                        </div>
                        <ul class="nav nav-pills" id="compTabs" role="tablist" style="background: rgba(255,255,255,0.25); padding: 4px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.5); backdrop-filter: blur(8px);">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-3 font-semibold py-2 px-3" id="matrix-tab" data-bs-toggle="pill" data-bs-target="#matrix-view" type="button" role="tab" style="font-size: 0.85rem;">
                                    <i class="fas fa-table me-1"></i> Full Matrix Table
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3 font-semibold py-2 px-3" id="h2h-tab" data-bs-toggle="pill" data-bs-target="#h2h-view" type="button" role="tab" style="font-size: 0.85rem;">
                                    <i class="fas fa-bolt me-1"></i> Head-to-Head Compare
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="contributors-content tab-content p-4" id="compTabContent">
                        
                        <!-- TAB 1: Full Matrix Table -->
                        <div class="tab-pane fade show active" id="matrix-view" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead style="background: rgba(255,255,255,0.22); border-bottom: 2px solid rgba(139, 69, 19, 0.2);">
                                        <tr>
                                            <th class="py-3 px-3">Rank</th>
                                            <th class="py-3 px-3">House</th>
                                            <th class="py-3 px-3" style="min-width: 180px;">Total Points</th>
                                            <th class="py-3 px-3 text-center">Members</th>
                                            <th class="py-3 px-3 text-center">Avg / Student</th>
                                            <th class="py-3 px-3 text-center">Wins Points</th>
                                            <th class="py-3 px-3 text-center">Participation</th>
                                            <th class="py-3 px-3 text-center">Appreciations</th>
                                            <th class="py-3 px-3 text-center">Penalties</th>
                                            <th class="py-3 px-3 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="matrixTableBody">
                                        <?php 
                                        $matrix_rank = 1;
                                        $max_matrix_points = max(array_column($house_stats, 'total_points'));
                                        if ($max_matrix_points <= 0) $max_matrix_points = 1;

                                        foreach ($houses as $h_key => $h_data):
                                            $st = $house_stats[$h_key];
                                            $pct = round(($st['total_points'] / $max_matrix_points) * 100);
                                            $badge_icon = '';
                                            if ($matrix_rank == 1) $badge_icon = '🏆 Gold';
                                            elseif ($matrix_rank == 2) $badge_icon = '🥈 Silver';
                                            elseif ($matrix_rank == 3) $badge_icon = '🥉 Bronze';
                                            else $badge_icon = '#' . $matrix_rank;
                                        ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="px-3">
                                                    <span class="badge" style="background: <?php echo $h_data['light_color']; ?>; color: <?php echo $h_data['color']; ?>; font-weight: 700; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px;">
                                                        <?php echo $badge_icon; ?>
                                                    </span>
                                                </td>
                                                <td class="px-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="position-relative" style="width: 46px; height: 46px; flex-shrink: 0;">
                                                            <img src="<?php echo $h_data['img']; ?>" alt="<?php echo $h_data['name']; ?>" style="width: 46px; height: 46px; border-radius: 12px; object-fit: cover; border: 2.5px solid <?php echo $h_data['color']; ?>; box-shadow: 0 4px 14px rgba(0,0,0,0.3);">
                                                        </div>
                                                        <div>
                                                            <strong style="color: #1a0d06; font-size: 1.05rem; font-weight: 800; font-family: 'Outfit', sans-serif;"><?php echo $h_data['name']; ?></strong>
                                                            <div style="font-size: 0.8rem; color: #6f5f54; font-weight: 500;"><?php echo $h_data['description']; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <strong style="color: <?php echo $h_data['color']; ?>; font-size: 1rem;"><?php echo number_format($st['total_points']); ?> pts</strong>
                                                        <span class="text-muted" style="font-size: 0.75rem;"><?php echo $pct; ?>% of lead</span>
                                                    </div>
                                                    <div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">
                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $pct; ?>%; background: <?php echo $h_data['gradient']; ?>; border-radius: 3px;" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="px-3 text-center font-semibold" style="color: #334155;">
                                                    <i class="fas fa-user-friends me-1 text-muted"></i><?php echo $st['student_count']; ?>
                                                </td>
                                                <td class="px-3 text-center">
                                                    <span class="badge bg-light text-dark border" style="font-weight: 600;">
                                                        <?php echo $st['avg_points']; ?>
                                                    </span>
                                                </td>
                                                <td class="px-3 text-center text-success font-semibold">
                                                    +<?php echo number_format($st['winners_points']); ?>
                                                </td>
                                                <td class="px-3 text-center text-info font-semibold">
                                                    +<?php echo number_format($st['participants_points']); ?>
                                                </td>
                                                <td class="px-3 text-center text-warning font-semibold">
                                                    +<?php echo number_format($st['appreciations_points']); ?>
                                                </td>
                                                <td class="px-3 text-center text-danger font-semibold">
                                                    -<?php echo number_format($st['penalties_points']); ?>
                                                </td>
                                                <td class="px-3 text-end">
                                                    <a href="house_detail.php?house=<?php echo urlencode($h_key); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.8rem;">
                                                        Details <i class="fas fa-chevron-right ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php 
                                            $matrix_rank++;
                                        endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: Head-to-Head Compare Tool -->
                        <div class="tab-pane fade" id="h2h-view" role="tabpanel">
                            <div class="row g-4 align-items-center mb-4">
                                <div class="col-md-5">
                                    <div class="p-3 rounded-3 border bg-light">
                                        <label class="form-label font-semibold text-muted mb-2"><i class="fas fa-shield-alt text-primary me-1"></i> Select House A</label>
                                        <select class="form-select border-0 shadow-sm font-semibold" id="h2hHouseA" onchange="runHeadToHeadCompare()">
                                            <?php 
                                            $keys = array_keys($houses);
                                            foreach ($houses as $hk => $hd):
                                            ?>
                                                <option value="<?php echo $hk; ?>"><?php echo $hd['name']; ?> House</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="mx-auto rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px; font-weight: 800; font-size: 1.1rem;">
                                        VS
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-3 rounded-3 border bg-light">
                                        <label class="form-label font-semibold text-muted mb-2"><i class="fas fa-shield-alt text-info me-1"></i> Select House B</label>
                                        <select class="form-select border-0 shadow-sm font-semibold" id="h2hHouseB" onchange="runHeadToHeadCompare()">
                                            <?php 
                                            $reversed = array_reverse($houses, true);
                                            foreach ($reversed as $hk => $hd):
                                            ?>
                                                <option value="<?php echo $hk; ?>"><?php echo $hd['name']; ?> House</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Head to Head Output Container -->
                            <div id="h2hOutput"></div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Top Contributors Section -->
            <div class="contributors-container mb-5">
                <div class="contributors-card">
                    <div class="contributors-header">
                        <h4 class="contributors-title">
                            <i class="fas fa-trophy"></i>
                            Top Contributors
                        </h4>
                    </div>
                    
                    <div class="contributors-content">
                            
                            <!-- Filters -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 500; color: #6c757d;">Branch</label>
                                    <select class="form-select" style="width:150px; margin-left:10px;" id="branchFilter" onchange="filterContributors()">
                                        <option value="">All Branches</option>
                                        <option value="CSD">CSD</option>
                                        <option value="CSIT">CSIT</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 500; color: #6c757d;">Year</label>
                                    <select class="form-select" style="width:150px; margin-left:10px;" id="yearFilter" onchange="filterContributors()">
                                        <option value="">All Years</option>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 500; color: #6c757d;">House</label>
                                    <select class="form-select" style="width:150px; margin-left:10px;" id="houseFilter" onchange="filterContributors()">
                                        <option value="">All Houses</option>
                                        <?php foreach ($houses as $house_key => $house_info): ?>
                                            <option value="<?php echo $house_key; ?>"><?php echo $house_info['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Contributors Table -->
                            <div class="table-responsive" style='max-height: 500px; overflow-y: auto;'>
                                <table class="table table-hover">
                                    <thead style="background: #f8f9fa; position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="border: none; font-weight: 600; color: #495057;">Rank</th>
                                            <th style="border: none; font-weight: 600; color: #495057;">Name</th>
                                            <th style="border: none; font-weight: 600; color: #495057;">Branch</th>
                                            <th style="border: none; font-weight: 600; color: #495057;">Year</th>
                                            <th style="border: none; font-weight: 600; color: #495057;">House</th>
                                            <th style="border: none; font-weight: 600; color: #495057;">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contributorsTable">
                                        <!-- Contributors will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                            
                        <!-- Show More Button -->
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-primary show-more-btn" id="showMoreBtn" onclick="loadMoreContributors()" style="display: none;">
                                <i class="fas fa-plus me-2"></i>
                                Show More Contributors (<span id="remainingCount">0</span> more)
                            </button>
                        </div
                    </div>
                </div>
            </div>
            

        </div>
    </div>
    
    <!-- Footer -->
    <?php include "footer.php"; ?>
    
    <script>
        let currentOffset = 0;
        let currentLimit = 7;
        let totalContributors = 0;
        let isLoading = false;

        // Load contributors on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadContributors();
        });

        function filterContributors() {
            currentOffset = 0;
            loadContributors();
        }

        function loadContributors() {
            if (isLoading) return;
            isLoading = true;

            const branch = document.getElementById('branchFilter').value;
            const year = document.getElementById('yearFilter').value;
            const house = document.getElementById('houseFilter').value;

            const params = new URLSearchParams({
                action: 'get_contributors',
                offset: currentOffset,
                limit: currentLimit,
                branch: branch,
                year: year,
                house: house
            });

            fetch('get_contributors.php?' + params)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('contributorsTable');
                        
                        if (currentOffset === 0) {
                            tbody.innerHTML = '';
                        }
                        
                        data.contributors.forEach((contributor, index) => {
                            // Debug: log house names to see what's coming from database
                            console.log('Contributor house name:', contributor.house_name);
                            const row = createContributorRow(contributor, currentOffset + index + 1);
                            tbody.appendChild(row);
                        });
                        
                        totalContributors = data.total;
                        updateShowMoreButton();
                    }
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Error loading contributors:', error);
                    isLoading = false;
                });
        }

        function loadMoreContributors() {
            currentOffset += currentLimit;
            currentLimit = 10; // Load 10 more after initial 7
            loadContributors();
        }

        function createContributorRow(contributor, rank) {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #e9ecef';
            row.style.cursor = 'pointer';
            row.classList.add('contributor-row');
            row.setAttribute('data-student-id', contributor.student_id);
            
            // Get house color with fallback mapping
            const houseColors = {
                'Aakash': '#4A90E2',
                'Jal': '#2196F3', 
                'Vayu': '#4CAF50',
                'PRUDHVI': '#8D6E63',
                'Agni': '#F44336',
                // Add potential database variations
                'Aakash House': '#4A90E2',
                'Jal House': '#2196F3',
                'Vayu House': '#4CAF50',
                'PRUDHVI House': '#8D6E63',
                'Agni House': '#F44336',
                'Alpha House': '#4A90E2',
                'Beta House': '#2196F3',
                'Gamma House': '#4CAF50',
                'Delta House': '#8D6E63',
                'Epsilon House': '#F44336',
                'Sky House': '#4A90E2',
                'Water House': '#2196F3',
                'Wind House': '#4CAF50',
                'Earth House': '#8D6E63',
                'Fire House': '#F44336'
            };
            
            // Try exact match first, then try to find partial match
            let houseColor = houseColors[contributor.house_name];
            if (!houseColor) {
                // Try to find a partial match
                const houseName = contributor.house_name.toLowerCase();
                if (houseName.includes('aakash') || houseName.includes('alpha') || houseName.includes('sky')) {
                    houseColor = '#4A90E2';
                } else if (houseName.includes('jal') || houseName.includes('beta') || houseName.includes('water')) {
                    houseColor = '#2196F3';
                } else if (houseName.includes('vayu') || houseName.includes('gamma') || houseName.includes('wind')) {
                    houseColor = '#4CAF50';
                } else if (houseName.includes('PRUDHVI') || houseName.includes('delta') || houseName.includes('earth')) {
                    houseColor = '#8D6E63';
                } else if (houseName.includes('agni') || houseName.includes('epsilon') || houseName.includes('fire')) {
                    houseColor = '#F44336';
                } else {
                    houseColor = '#6c757d'; // Default gray
                }
            }
            
            row.innerHTML = `
                <td style="border: none; padding: 12px 8px;">
                    <span class="badge ${rank <= 3 ? 'bg-warning' : 'bg-secondary'}" style="font-size: 0.9rem;">
                        ${rank}
                    </span>
                </td>
                <td style="border: none; padding: 12px 8px; font-weight: 500; color: #2c3e50;">
                    <div class="d-flex align-items-center">
                        ${contributor.name}
                        <i class="fas fa-chevron-down ms-2 expand-icon" style="font-size: 0.8rem; color: #6c757d; transition: transform 0.3s ease;"></i>
                    </div>
                </td>
                <td style="border: none; padding: 12px 8px; color: #6c757d;">
                    ${contributor.branch}
                </td>
                <td style="border: none; padding: 12px 8px; color: #6c757d;">
                    ${contributor.year}
                </td>
                <td style="border: none; ">
                    <span style="background: ${houseColor}; padding: 5px; border-radius:50px; color:white;">                        ${contributor.house_name}

                    </span>
                </td>
                <td style="border: none; padding: 12px 8px;">
                    <span style="font-weight: 600; color: #2c3e50; font-size: 1.1rem;">
                        ${contributor.total_points}
                    </span>
                </td>
            `;
            
            // Add click event for expansion
            row.addEventListener('click', function() {
                toggleContributorDetails(this, contributor.student_id);
            });
            
            return row;
        }

        function updateShowMoreButton() {
            const showMoreBtn = document.getElementById('showMoreBtn');
            const remainingCount = document.getElementById('remainingCount');
            const currentlyShown = currentOffset + currentLimit;
            
            if (currentlyShown < totalContributors) {
                const remaining = totalContributors - currentlyShown;
                remainingCount.textContent = remaining;
                showMoreBtn.style.display = 'inline-block';
            } else {
                showMoreBtn.style.display = 'none';
            }
        }

        function toggleContributorDetails(row, studentId) {
            const expandIcon = row.querySelector('.expand-icon');
            const nextRow = row.nextElementSibling;
            
            // Check if details row already exists
            if (nextRow && nextRow.classList.contains('details-row')) {
                // Toggle existing details
                if (nextRow.style.display === 'none') {
                    nextRow.style.display = 'table-row';
                    expandIcon.style.transform = 'rotate(180deg)';
                } else {
                    nextRow.style.display = 'none';
                    expandIcon.style.transform = 'rotate(0deg)';
                }
            } else {
                // Load and create details row
                loadContributorDetails(row, studentId);
                expandIcon.style.transform = 'rotate(180deg)';
            }
        }

        function loadContributorDetails(row, studentId) {
            // Show loading state
            const loadingRow = document.createElement('tr');
            loadingRow.classList.add('details-row');
            loadingRow.innerHTML = `
                <td colspan="6" style="border: none; padding: 20px; background: #f8f9fa;">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading point details...
                    </div>
                </td>
            `;
            row.parentNode.insertBefore(loadingRow, row.nextSibling);

            // Fetch detailed points
            fetch(`get_contributors.php?action=get_contributor_details&student_id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadingRow.innerHTML = createDetailsContent(data.details);
                    } else {
                        loadingRow.innerHTML = `
                            <td colspan="6" style="border: none; padding: 20px; background: #f8f9fa;">
                                <div class="text-center text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Error loading details
                                </div>
                            </td>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading contributor details:', error);
                    loadingRow.innerHTML = `
                        <td colspan="6" style="border: none; padding: 20px; background: #f8f9fa;">
                            <div class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading details
                            </div>
                        </td>
                    `;
                });
        }

        function createDetailsContent(details) {
            const categories = [
                { key: 'participation', label: 'Participation Events', icon: 'fas fa-users', color: '#17a2b8' },
                { key: 'wins', label: 'Won Events', icon: 'fas fa-trophy', color: '#ffc107' },
                { key: 'organized', label: 'Organized Events', icon: 'fas fa-cogs', color: '#28a745' },
                { key: 'appreciations', label: 'Appreciations', icon: 'fas fa-star', color: '#fd7e14' },
                { key: 'penalties', label: 'Penalties', icon: 'fas fa-minus-circle', color: '#dc3545' }
            ];

            let content = `
                <td colspan="6" style="border: none; padding: 0; background: #f8f9fa;">
                    <div style="padding: 20px;">
                        <h6 style="color: #2c3e50; margin-bottom: 20px; font-weight: 600;">
                            <i class="fas fa-chart-pie me-2"></i>Point Breakdown
                        </h6>
                        <div class="row g-3">
            `;

            categories.forEach(category => {
                const categoryData = details[category.key] || { total_points: 0, events: [] };
                const hasEvents = categoryData.events && categoryData.events.length > 0;
                
                content += `
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 35px; height: 35px; background: ${category.color}20;">
                                        <i class="${category.icon}" style="color: ${category.color}; font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-size: 0.9rem; color: #2c3e50;">${category.label}</h6>
                                        <div style="font-weight: 600; color: ${category.color}; font-size: 1.1rem;">
                                            ${category.key === 'penalties' ? '-' : ''}${Math.abs(categoryData.total_points)} points
                                        </div>
                                    </div>
                                </div>
                `;

                if (hasEvents) {
                    content += `
                        <div style="max-height: 120px; overflow-y: auto;">
                            <table class="table table-sm mb-0" style="font-size: 0.8rem;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th style="border: none; padding: 4px 8px; font-weight: 500;">Event</th>
                                        <th style="border: none; padding: 4px 8px; font-weight: 500; text-align: right;">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    categoryData.events.forEach(event => {
                        content += `
                            <tr>
                                <td style="border: none; padding: 4px 8px; color: #6c757d;">${event.event_title}</td>
                                <td style="border: none; padding: 4px 8px; text-align: right; font-weight: 500; color: ${category.color};">
                                    ${category.key === 'penalties' ? '-' : ''}${Math.abs(event.points)}
                                </td>
                            </tr>
                        `;
                    });
                    
                    content += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    content += `
                        <div class="text-center text-muted" style="font-size: 0.8rem; padding: 10px;">
                            No ${category.label.toLowerCase()} found
                        </div>
                    `;
                }

                content += `
                            </div>
                        </div>
                    </div>
                `;
            });

            content += `
                        </div>
                    </div>
                </td>
            `;

            return content;
        }

        // Head to Head Comparison Script
        let houseStatsMap = <?php echo json_encode($house_stats); ?>;
        let housesMetaMap = <?php echo json_encode($houses); ?>;

        function renderHousesGrid(housesObj, statsObj) {
            const container = document.getElementById('housesGridContainer');
            if (!container) return;
            
            let html = '';
            let rankCounter = 1;
            
            const maxPoints = Math.max(...Object.values(statsObj).map(s => s.total_points || 0), 1);
            
            for (const [houseKey, houseInfo] of Object.entries(housesObj)) {
                const cleanKey = (houseKey === 'PRUDHVI' || houseKey === 'Pruthvi') ? 'PRUDHVI' : ((houseKey === 'Jal' || houseKey === 'JAL') ? 'Jal' : ((houseKey === 'Vayu' || houseKey === 'VAYU') ? 'Vayu' : houseKey));
                const st = statsObj[houseKey] || { total_points: 0, student_count: 0 };
                const progress = maxPoints > 0 ? ((st.total_points / maxPoints) * 100).toFixed(1) : 0;
                
                let overlayBg = houseInfo.gradient;
                if (houseKey === 'Aakash') {
                    overlayBg = "linear-gradient(135deg, rgba(30, 136, 229, 0.70) 0%, rgba(79, 195, 247, 0.70) 100%), url('assets/img/aakash_bg.jpg') center/cover no-repeat";
                } else if (houseKey === 'Agni') {
                    overlayBg = "linear-gradient(135deg, rgba(244, 67, 54, 0.75) 0%, rgba(211, 47, 47, 0.75) 100%), url('assets/img/agni_bg.jpg') center/cover no-repeat";
                } else if (houseKey.toUpperCase() === 'PRUDHVI' || houseKey === 'Pruthvi') {
                    overlayBg = "linear-gradient(135deg, rgba(141, 110, 99, 0.75) 0%, rgba(109, 76, 65, 0.75) 100%), url('assets/img/prudhvi_bg.jpg') center/cover no-repeat";
                } else if (houseKey.toUpperCase() === 'JAL' || houseKey === 'Jal') {
                    overlayBg = "linear-gradient(135deg, rgba(33, 150, 243, 0.75) 0%, rgba(25, 118, 210, 0.75) 100%), url('assets/img/jal_bg.jpg') center/cover no-repeat";
                } else if (houseKey.toUpperCase() === 'VAYU' || houseKey === 'Vayu') {
                    overlayBg = "linear-gradient(135deg, rgba(76, 175, 80, 0.75) 0%, rgba(56, 142, 60, 0.75) 100%), url('assets/img/vayu_bg.png') center bottom/cover no-repeat";
                }

                html += `
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3" style="width:260px">
                        <a href="house_detail.php?house=${encodeURIComponent(houseKey)}" class="text-decoration-none house-link">
                            <div class="house-card house-card-${cleanKey}" data-house="${houseKey}">
                                <div style="position: absolute; top: 12px; right: 12px; z-index: 3;">
                                    <span style="background: ${houseInfo.color}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        #${rankCounter}
                                    </span>
                                </div>
                                <div style="padding: 20px 20px 0 20px; text-align: left;">
                                    <div style="margin-bottom: 8px;">
                                        <h4 style="color: ${houseInfo.color}; font-weight: 800; font-size: 1.5rem; margin: 0; line-height: 1.2; text-shadow: 0 1px 2px rgba(255,255,255,0.8);">
                                            ${houseInfo.name}
                                        </h4>
                                        <h6 style="color: #1f2937; font-weight: 700; font-size: 0.95rem; margin: 2px 0 0 0; opacity: 1;">
                                            House
                                        </h6>
                                    </div>
                                    <p style="color: #111827; font-weight: 600; font-size: 0.85rem; margin: 0 0 16px 0; line-height: 1.4;">
                                        ${houseInfo.description}
                                    </p>
                                </div>
                                <div style="padding: 0 20px; margin-bottom: 16px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span style="color: #1f2937; font-size: 0.85rem; font-weight: 700;">Points</span>
                                        <span style="color: #111827; font-size: 1.25rem; font-weight: 800;">
                                            ${(st.total_points || 0).toLocaleString()}
                                        </span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: #e9ecef; border-radius: 3px; margin-top: 8px; overflow: hidden;">
                                        <div style="width: ${progress}%; height: 100%; background: ${houseInfo.color}; border-radius: 3px; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 16px 20px; background: ${houseInfo.light_color}; border-top: 1px solid ${houseInfo.color}20;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-users" style="color: ${houseInfo.color}; font-size: 0.9rem;"></i>
                                            <span style="color: #1f2937; font-size: 0.85rem; font-weight: 700;">
                                                ${st.student_count || 0} students
                                            </span>
                                        </div>
                                        <i class="fas fa-arrow-right" style="color: ${houseInfo.color}; font-size: 0.9rem;"></i>
                                    </div>
                                </div>
                                <div class="house-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: ${overlayBg}; opacity: 0; transition: all 0.3s ease; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                    <div style="color: white; text-align: center;">
                                        <i class="fas fa-eye" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                        <div style="font-size: 1rem; font-weight: 600;">View Details</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                rankCounter++;
            }
            
            container.innerHTML = html;
        }

        function renderMatrixTable(housesObj, statsObj) {
            const tbody = document.getElementById('matrixTableBody');
            if (!tbody) return;
            
            let html = '';
            let matrixRank = 1;
            const maxMatrixPoints = Math.max(...Object.values(statsObj).map(s => s.total_points || 0), 1);
            
            for (const [hKey, hData] of Object.entries(housesObj)) {
                const st = statsObj[hKey] || {};
                const pct = Math.round(((st.total_points || 0) / maxMatrixPoints) * 100);
                
                let badgeIcon = '';
                if (matrixRank === 1) badgeIcon = '🏆 Gold';
                else if (matrixRank === 2) badgeIcon = '🥈 Silver';
                else if (matrixRank === 3) badgeIcon = '🥉 Bronze';
                else badgeIcon = '#' + matrixRank;
                
                html += `
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td class="px-3">
                            <span class="badge" style="background: ${hData.light_color}; color: ${hData.color}; font-weight: 700; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px;">
                                ${badgeIcon}
                            </span>
                        </td>
                        <td class="px-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative" style="width: 46px; height: 46px; flex-shrink: 0;">
                                    <img src="${hData.img}" alt="${hData.name}" style="width: 46px; height: 46px; border-radius: 12px; object-fit: cover; border: 2.5px solid ${hData.color}; box-shadow: 0 4px 14px rgba(0,0,0,0.3);">
                                </div>
                                <div>
                                    <strong style="color: #1a0d06; font-size: 1.05rem; font-weight: 800; font-family: 'Outfit', sans-serif;">${hData.name}</strong>
                                    <div style="font-size: 0.8rem; color: #6f5f54; font-weight: 500;">${hData.description}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong style="color: ${hData.color}; font-size: 1rem;">${(st.total_points || 0).toLocaleString()} pts</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">${pct}% of lead</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">
                                <div class="progress-bar" role="progressbar" style="width: ${pct}%; background: ${hData.gradient}; border-radius: 3px;" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                        <td class="px-3 text-center font-semibold" style="color: #334155;">
                            <i class="fas fa-user-friends me-1 text-muted"></i>${st.student_count || 0}
                        </td>
                        <td class="px-3 text-center">
                            <span class="badge bg-light text-dark border" style="font-weight: 600;">
                                ${st.avg_points || 0}
                            </span>
                        </td>
                        <td class="px-3 text-center text-success font-semibold">
                            +${(st.winners_points || 0).toLocaleString()}
                        </td>
                        <td class="px-3 text-center text-info font-semibold">
                            +${(st.participants_points || 0).toLocaleString()}
                        </td>
                        <td class="px-3 text-center text-warning font-semibold">
                            +${(st.appreciations_points || 0).toLocaleString()}
                        </td>
                        <td class="px-3 text-center text-danger font-semibold">
                            -${(st.penalties_points || 0).toLocaleString()}
                        </td>
                        <td class="px-3 text-end">
                            <a href="house_detail.php?house=${encodeURIComponent(hKey)}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.8rem;">
                                Details <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                `;
                matrixRank++;
            }
            
            tbody.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            runHeadToHeadCompare();
        });

        function runHeadToHeadCompare() {
            const keyA = document.getElementById('h2hHouseA').value;
            const keyB = document.getElementById('h2hHouseB').value;
            const out = document.getElementById('h2hOutput');
            if (!out) return;

            if (keyA === keyB) {
                out.innerHTML = `
                    <div class="alert alert-warning text-center rounded-3 p-4">
                        <i class="fas fa-exclamation-circle fa-2x mb-2 text-warning"></i>
                        <div class="font-semibold" style="font-size: 1.05rem;">Please select two different houses to compare head-to-head.</div>
                    </div>
                `;
                return;
            }

            const infoA = housesMetaMap[keyA];
            const infoB = housesMetaMap[keyB];
            const statA = houseStatsMap[keyA] || {};
            const statB = houseStatsMap[keyB] || {};

            const diff = (statA.total_points || 0) - (statB.total_points || 0);
            let leaderText = '';
            if (diff > 0) {
                leaderText = `<span style="color: ${infoA.color}; font-weight: 700;">🏆 ${infoA.name} House</span> is leading by <strong>${Math.abs(diff).toLocaleString()} points</strong>!`;
            } else if (diff < 0) {
                leaderText = `<span style="color: ${infoB.color}; font-weight: 700;">🏆 ${infoB.name} House</span> is leading by <strong>${Math.abs(diff).toLocaleString()} points</strong>!`;
            } else {
                leaderText = `🤝 Both <strong>${infoA.name}</strong> and <strong>${infoB.name}</strong> are currently tied!`;
            }

            const maxPts = Math.max(statA.total_points || 1, statB.total_points || 1, 1);
            const pctA = Math.round(((statA.total_points || 0) / maxPts) * 100);
            const pctB = Math.round(((statB.total_points || 0) / maxPts) * 100);

            const totalDeptStudents = 558;
            const pctShareA = Math.round(((statA.student_count || 0) / totalDeptStudents) * 100);
            const pctShareB = Math.round(((statB.student_count || 0) / totalDeptStudents) * 100);

            out.innerHTML = `
                <div class="alert text-center p-3 mb-4 rounded-3 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 1rem;">
                    ${leaderText}
                </div>

                <div class="row g-4 mb-4">
                    <!-- House A Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden" style="border-top: 5px solid ${infoA.color} !important;">
                            <div class="card-body p-4 text-center">
                                <div class="mx-auto mb-3 position-relative d-inline-block" style="width: 76px; height: 76px;">
                                    <img src="${infoA.img}" alt="${infoA.name}" class="rounded-circle shadow-sm" style="width: 76px; height: 76px; object-fit: cover; border: 3.5px solid ${infoA.color};">
                                    <span class="position-absolute bottom-0 end-0 rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; background: ${infoA.color}; font-size: 0.75rem; transform: translate(3px, 3px);">
                                        <i class="${infoA.icon}"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold mb-1" style="color: ${infoA.color}; font-weight: 800;">${infoA.name}</h3>
                                <span class="badge mb-2 px-3 py-1 rounded-pill" style="background: ${infoA.light_color}; color: ${infoA.color}; font-weight: 700; font-size: 0.8rem;">
                                    <i class="${infoA.icon} me-1"></i> ${infoA.element || 'House'}
                                </span>
                                <p class="text-muted small mb-3 px-2" style="font-size: 0.85rem; line-height: 1.5; color: #475569;">${infoA.full_description || infoA.description}</p>
                                <div class="p-3 rounded-3 mb-3" style="background: ${infoA.light_color};">
                                    <div class="text-muted small uppercase font-semibold">Total Points</div>
                                    <div class="display-6 font-bold" style="color: ${infoA.color}; font-weight: 800;">${(statA.total_points || 0).toLocaleString()}</div>
                                </div>
                                <div class="row g-2 text-start">
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Total Members</small><strong>${statA.student_count || 0} students (${pctShareA}%)</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Avg / Student</small><strong>${statA.avg_points || 0}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Wins Points</small><strong class="text-success">+${(statA.winners_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Participation</small><strong class="text-info">+${(statA.participants_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Appreciations</small><strong class="text-warning">+${(statA.appreciations_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Penalties</small><strong class="text-danger">-${(statA.penalties_points || 0).toLocaleString()}</strong></div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- House B Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden" style="border-top: 5px solid ${infoB.color} !important;">
                            <div class="card-body p-4 text-center">
                                <div class="mx-auto mb-3 position-relative d-inline-block" style="width: 76px; height: 76px;">
                                    <img src="${infoB.img}" alt="${infoB.name}" class="rounded-circle shadow-sm" style="width: 76px; height: 76px; object-fit: cover; border: 3.5px solid ${infoB.color};">
                                    <span class="position-absolute bottom-0 end-0 rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; background: ${infoB.color}; font-size: 0.75rem; transform: translate(3px, 3px);">
                                        <i class="${infoB.icon}"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold mb-1" style="color: ${infoB.color}; font-weight: 800;">${infoB.name}</h3>
                                <span class="badge mb-2 px-3 py-1 rounded-pill" style="background: ${infoB.light_color}; color: ${infoB.color}; font-weight: 700; font-size: 0.8rem;">
                                    <i class="${infoB.icon} me-1"></i> ${infoB.element || 'House'}
                                </span>
                                <p class="text-muted small mb-3 px-2" style="font-size: 0.85rem; line-height: 1.5; color: #475569;">${infoB.full_description || infoB.description}</p>
                                <div class="p-3 rounded-3 mb-3" style="background: ${infoB.light_color};">
                                    <div class="text-muted small uppercase font-semibold">Total Points</div>
                                    <div class="display-6 font-bold" style="color: ${infoB.color}; font-weight: 800;">${(statB.total_points || 0).toLocaleString()}</div>
                                </div>
                                <div class="row g-2 text-start">
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Total Members</small><strong>${statB.student_count || 0} students (${pctShareB}%)</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Avg / Student</small><strong>${statB.avg_points || 0}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Wins Points</small><strong class="text-success">+${(statB.winners_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Participation</small><strong class="text-info">+${(statB.participants_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Appreciations</small><strong class="text-warning">+${(statB.appreciations_points || 0).toLocaleString()}</strong></div></div>
                                    <div class="col-6"><div class="p-2 border rounded bg-light"><small class="text-muted d-block">Penalties</small><strong class="text-danger">-${(statB.penalties_points || 0).toLocaleString()}</strong></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Bar Comparison -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h6 class="font-bold mb-3 text-muted"><i class="fas fa-chart-bar me-2"></i>Visual Points Dominance Bar</h6>
                    <div class="d-flex align-items-center mb-2 justify-content-between font-semibold" style="font-size: 0.9rem;">
                        <span style="color: ${infoA.color}; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            <img src="${infoA.img}" alt="${infoA.name}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; border: 1.5px solid ${infoA.color};">
                            ${infoA.name}: ${(statA.total_points || 0).toLocaleString()}
                        </span>
                        <span style="color: ${infoB.color}; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            ${infoB.name}: ${(statB.total_points || 0).toLocaleString()}
                            <img src="${infoB.img}" alt="${infoB.name}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; border: 1.5px solid ${infoB.color};">
                        </span>
                    </div>
                    <div class="progress" style="height: 18px; border-radius: 9px; background: #e2e8f0; overflow: hidden;">
                        <div class="progress-bar" role="progressbar" style="width: ${pctA}%; background: ${infoA.gradient}; font-weight: 700; font-size: 0.75rem;" aria-valuenow="${pctA}" aria-valuemin="0" aria-valuemax="100">${pctA}%</div>
                        <div class="progress-bar" role="progressbar" style="width: ${pctB}%; background: ${infoB.gradient}; font-weight: 700; font-size: 0.75rem;" aria-valuenow="${pctB}" aria-valuemin="0" aria-valuemax="100">${pctB}%</div>
                    </div>
                </div>
            `;
        }
    </script>

    <style>
        /* Contributor row styles */
        .contributor-row:hover {
            background-color: #f8f9fa !important;
        }
        
        .contributor-row .expand-icon {
            transition: transform 0.3s ease;
        }
        
        .details-row {
            background-color: #f8f9fa !important;
        }
        
        .details-row td {
            border-top: 1px solid #dee2e6 !important;
        }
        
        /* Custom scrollbar for contributors table */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Custom scrollbar for event lists */
        .details-row .table tbody {
            max-height: 120px;
            overflow-y: auto;
        }
        
        .details-row .table tbody::-webkit-scrollbar {
            width: 4px;
        }
        
        .details-row .table tbody::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        
        .details-row .table tbody::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        .details-row .table tbody::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .house-link {
            display: block;
            text-decoration: none;
            height: 100%;
        }
        
        .house-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: translateY(0);
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .house-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .house-card:hover .house-overlay {
            opacity: 1;
        }
        
        .house-card:hover .house-icon-bg {
            transform: scale(1.1) rotate(5deg);
        }
        
        .house-icon-bg {
            transition: all 0.3s ease;
        }
        
        .house-title {
            transition: all 0.3s ease;
        }
        
        .house-card:hover .house-title {
            transform: scale(1.05);
        }
        
        .house-stats {
            transition: all 0.3s ease;
        }
        
        .house-card:hover .house-stats {
            transform: scale(1.05);
        }
        
        .table th {
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.8rem;
        }
        
        /* Ensure proper footer positioning */
        .main-content {
            padding: 40px 0;
            min-height: 60vh;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .house-card {
                height: 200px;
            }
            
            .house-icon-bg {
                width: 50px !important;
                height: 50px !important;
            }
            
            .house-icon-bg i {
                font-size: 1.5rem !important;
            }
            
            .house-title {
                font-size: 1rem !important;
            }
            
            .house-description {
                font-size: 0.7rem !important;
                padding: 0 5px !important;
            }
            
            .stats-row {
                gap: 15px !important;
            }
            
            .stat-item {
                font-size: 0.65rem !important;
                max-width: 50px !important;
            }
        }
        
        @media (max-width: 576px) {
            .col-sm-6 {
                width: 50%;
            }
            
            .house-card {
                height: 180px;
            }
            
            .house-icon-bg {
                width: 40px !important;
                height: 40px !important;
            }
            
            .house-icon-bg i {
                font-size: 1.2rem !important;
            }
            
            .house-title {
                font-size: 0.9rem !important;
            }
            
            .house-description {
                font-size: 0.65rem !important;
                padding: 0 3px !important;
            }
            
            .stats-row {
                gap: 10px !important;
            }
            
            .stat-item {
                font-size: 0.6rem !important;
                max-width: 40px !important;
            }
        }
        
        /* Ensure equal height for all cards in a row */
        .row .col-lg-2,
        .row .col-md-4,
        .row .col-sm-6 {
            display: flex;
        }
        
        /* House-specific animations */
        .house-card[data-house="Aakash"]:hover {
            border-color: #4A90E2;
        }
        
        .house-card[data-house="Jal"]:hover {
            border-color: #2196F3;
        }
        
        .house-card[data-house="Vayu"]:hover {
            border-color: #4CAF50;
        }
        
        .house-card[data-house="PRUDHVI"]:hover {
            border-color: #8D6E63;
        }
        
        .house-card[data-house="Agni"]:hover {
            border-color: #F44336;
        }
    </style>
</body>
</html> 