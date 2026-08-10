<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "connect.php";

// Define the house names with enhanced color schemes
$houses = [
    'Aakash' => [
        'name' => 'Aakash', 
        'color' => '#4A90E2', 
        'gradient' => 'linear-gradient(135deg, #4A90E2 0%, #357ABD 100%)',
        'light_color' => '#E3F2FD',
        'icon' => 'fas fa-cloud',
        'description' => 'Sky House - Reaching for the stars with boundless ambition and limitless potential. Members of Aakash House are known for their visionary thinking and ability to soar above challenges.',
        'img' => 'img/house1.png'
    ],
    'Jal' => [
        'name' => 'Jal', 
        'color' => '#2196F3', 
        'gradient' => 'linear-gradient(135deg, #2196F3 0%, #1976D2 100%)',
        'light_color' => '#E1F5FE',
        'icon' => 'fas fa-water',
        'description' => 'Water House - Flowing with wisdom and adaptability like the eternal river. Jal House members embody fluidity, persistence, and the power to shape their path through any obstacle.',
        'img' => 'img/house2.png'
    ],
    'Vayu' => [
        'name' => 'Vayu', 
        'color' => '#4CAF50', 
        'gradient' => 'linear-gradient(135deg, #4CAF50 0%, #388E3C 100%)',
        'light_color' => '#E8F5E8',
        'icon' => 'fas fa-wind',
        'description' => 'Wind House - Swift and free like the breeze that carries change across the world. Vayu House students are dynamic, innovative, and bring fresh perspectives to every challenge.',
        'img' => 'img/house3.png'
    ],
    'PRUDHVI' => [
        'name' => 'PRUDHVI', 
        'color' => '#8D6E63', 
        'gradient' => 'linear-gradient(135deg, #8D6E63 0%, #6D4C41 100%)',
        'light_color' => '#EFEBE9',
        'icon' => 'fas fa-mountain',
        'description' => 'Earth House - Strong and steady like the mountains that stand the test of time. Pruthvi House members are grounded, reliable, and provide the solid foundation upon which great achievements are built.',
        'img' => 'img/house4.png'    
    ],
    'Agni' => [
        'name' => 'Agni', 
        'color' => '#F44336', 
        'gradient' => 'linear-gradient(135deg, #F44336 0%, #D32F2F 100%)',
        'light_color' => '#FFEBEE',
        'icon' => 'fas fa-fire',
        'description' => 'Fire House - Burning with passion and illuminating the path forward with fierce determination. Agni House students are energetic, passionate, and ignite inspiration in everyone around them.',
        'img' => 'img/house5.png'
    ]
];

// Get selected house from URL parameter with case-insensitive matching
$raw_house = isset($_GET['house']) ? trim($_GET['house']) : '';
$selected_house = '';

if ($raw_house) {
    if (array_key_exists($raw_house, $houses)) {
        $selected_house = $raw_house;
    } else {
        foreach ($houses as $k => $v) {
            if (strcasecmp($k, $raw_house) === 0) {
                $selected_house = $k;
                break;
            }
        }
    }
}

// Fallback alias lookup
if (!$selected_house && $raw_house) {
    if (strcasecmp($raw_house, 'PRUDHVI') === 0 || strcasecmp($raw_house, 'Prudhvi') === 0 || strcasecmp($raw_house, 'Pruthvi') === 0) {
        $selected_house = 'PRUDHVI';
    }
}

// Redirect to houses display if no house selected or invalid house
if (!$selected_house || !array_key_exists($selected_house, $houses)) {
    header('Location: houses_dashboard.php');
    exit();
}

include "./head.php"; 

$house_info = $houses[$selected_house];

// Get students for selected house
$students = [];
$no_house_points_table = false;
$using_new_schema = false;

$house_name_param = $selected_house;

// First try legacy house_points table
$stmt_hp_exists = $conn->prepare("SHOW TABLES LIKE 'house_points'");
if ($stmt_hp_exists) {
    $stmt_hp_exists->execute();
    $hp_exists_result = $stmt_hp_exists->get_result();
    if ($hp_exists_result && $hp_exists_result->num_rows > 0) {
        $sql = "SELECT * FROM house_points WHERE house_name = ? ORDER BY total_points DESC, name ASC";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $house_name_param);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $students[] = $row;
                }
            }
            $stmt->close();
        } else {
            error_log("Failed to prepare legacy house points query: " . $conn->error);
        }
    }
    $stmt_hp_exists->close();
} else {
    error_log("Failed to prepare table existence check for house_points: " . $conn->error);
}

// If no students found in legacy table, try new schema
if (empty($students)) {
    $stmt_houses_exists = $conn->prepare("SHOW TABLES LIKE 'houses'");
    $stmt_students_exists = $conn->prepare("SHOW TABLES LIKE 'students'");

    if ($stmt_houses_exists && $stmt_students_exists) {
        $stmt_houses_exists->execute();
        $houses_exists_result = $stmt_houses_exists->get_result();
        $stmt_houses_exists->close();

        $stmt_students_exists->execute();
        $students_exists_result = $stmt_students_exists->get_result();
        $stmt_students_exists->close();
        
        if ($houses_exists_result && $houses_exists_result->num_rows > 0 && 
            $students_exists_result && $students_exists_result->num_rows > 0) {
            
            $using_new_schema = true;
            
            // Try to find house by exact or case-insensitive name match first
            $house_sql = "SELECT hid FROM houses WHERE UPPER(name) = UPPER(?) OR name = ?";
            $stmt_house = $conn->prepare($house_sql);
            $hid = null;
            
            if ($stmt_house) {
                $stmt_house->bind_param("ss", $house_name_param, $house_name_param);
                $stmt_house->execute();
                $house_result = $stmt_house->get_result();
                
                if ($house_result && $house_result->num_rows > 0) {
                    $house_row = mysqli_fetch_assoc($house_result);
                    $hid = $house_row['hid'];
                }
                $stmt_house->close();
            } else {
                error_log("Failed to prepare house lookup query: " . $conn->error);
            }

            // If not found by exact name, try house name mapping
            if (!$hid) {
                $house_mapping = [
                    'Aakash' => ['Alpha House', 'Aakash House', 'Sky House', 'AAKASH'],
                    'Jal' => ['Beta House', 'Jal House', 'Water House', 'JAL'],
                    'Vayu' => ['Gamma House', 'Vayu House', 'Wind House', 'VAYU'],
                    'Pruthvi' => ['Delta House', 'Pruthvi House', 'Earth House', 'PRUDHVI', 'Prudhvi'],
                    'PRUDHVI' => ['Delta House', 'Pruthvi House', 'Earth House', 'PRUDHVI', 'Prudhvi', 'Pruthvi'],
                    'Prudhvi' => ['Delta House', 'Pruthvi House', 'Earth House', 'PRUDHVI', 'Prudhvi', 'Pruthvi'],
                    'Agni' => ['Epsilon House', 'Agni House', 'Fire House', 'AGNI']
                ];
                
                if (isset($house_mapping[$selected_house])) {
                    foreach ($house_mapping[$selected_house] as $possible_name) {
                        $stmt_house_map = $conn->prepare("SELECT hid FROM houses WHERE UPPER(name) = UPPER(?) OR name = ?");
                        if ($stmt_house_map) {
                            $stmt_house_map->bind_param("ss", $possible_name, $possible_name);
                            $stmt_house_map->execute();
                            $house_result_map = $stmt_house_map->get_result();
                            
                            if ($house_result_map && $house_result_map->num_rows > 0) {
                                $house_row = mysqli_fetch_assoc($house_result_map);
                                $hid = $house_row['hid'];
                                $stmt_house_map->close();
                                break;
                            }
                            $stmt_house_map->close();
                        } else {
                            error_log("Failed to prepare mapped house lookup query: " . $conn->error);
                        }
                    }
                }
            }
            
            // Get students for this house with exact points calculated
            if ($hid) {
                $sql = "SELECT 
                    s.student_id as regd_no,
                    s.name,
                    CONCAT(s.branch, ' - ', s.section) as year_section,
                    s.branch,
                    s.section,
                    (
                        COALESCE((SELECT SUM(p.points) FROM participants p WHERE p.student_id = s.student_id), 0) +
                        COALESCE((SELECT SUM(w.points) FROM winners w WHERE w.student_id = s.student_id), 0) +
                        COALESCE((SELECT SUM(o.points) FROM organizers o WHERE o.student_id = s.student_id), 0) +
                        COALESCE((SELECT SUM(a.points) FROM appreciations a WHERE a.student_id = s.student_id), 0) -
                        COALESCE((SELECT SUM(pen.points) FROM penalties pen WHERE pen.student_id = s.student_id), 0)
                    ) as total_points
                FROM students s 
                WHERE s.hid = ? 
                ORDER BY total_points DESC, s.name ASC";
                
                $stmt_students = $conn->prepare($sql);
                if ($stmt_students) {
                    $stmt_students->bind_param("i", $hid);
                    $stmt_students->execute();
                    $result = $stmt_students->get_result();
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $students[] = $row;
                        }
                    } else {
                        error_log("Error fetching students for house: " . $stmt_students->error);
                    }
                    $stmt_students->close();
                } else {
                    error_log("Failed to prepare students for house query: " . $conn->error);
                }
            }
        } else {
            $no_house_points_table = true;
        }
    } else {
        error_log("Failed to prepare table existence checks for houses/students: " . $conn->error);
        $no_house_points_table = true;
    }
}

// Compute all house total points for relative comparison and ranking
$all_house_totals = [];
foreach ($houses as $hk => $hv) {
    $hname_esc = mysqli_real_escape_string($conn, $hv['name']);
    $h_id = null;
    $res_h = mysqli_query($conn, "SELECT hid FROM houses WHERE name = '$hname_esc'");
    if ($res_h && mysqli_num_rows($res_h) > 0) {
        $row_h = mysqli_fetch_assoc($res_h);
        $h_id = $row_h['hid'];
    }
    $h_total = 0;
    if ($h_id) {
        $q = "SELECT 
                (COALESCE((SELECT SUM(p.points) FROM participants p JOIN students s ON p.student_id = s.student_id WHERE s.hid = $h_id), 0) +
                 COALESCE((SELECT SUM(w.points) FROM winners w JOIN students s ON w.student_id = s.student_id WHERE s.hid = $h_id), 0) +
                 COALESCE((SELECT SUM(o.points) FROM organizers o JOIN students s ON o.student_id = s.student_id WHERE s.hid = $h_id), 0) +
                 COALESCE((SELECT SUM(a.points) FROM appreciations a JOIN students s ON a.student_id = s.student_id WHERE s.hid = $h_id), 0) -
                 COALESCE((SELECT SUM(pen.points) FROM penalties pen JOIN students s ON pen.student_id = s.student_id WHERE s.hid = $h_id), 0)) as total";
        $r = mysqli_query($conn, $q);
        if ($r && $row = mysqli_fetch_assoc($r)) {
            $h_total = (int)$row['total'];
        }
    }
    $all_house_totals[$hk] = $h_total;
}

arsort($all_house_totals);
$house_ranks = array_keys($all_house_totals);
$current_house_rank = array_search($selected_house, $house_ranks) !== false ? array_search($selected_house, $house_ranks) + 1 : 1;
$leader_house_key = $house_ranks[0];
$leader_house_points = $all_house_totals[$leader_house_key];

// Calculate house statistics
$house_stats = [
    'student_count' => count($students),
    'total_points' => 0,
    'avg_points' => 0.0,
    'max_points' => 0
];
?>

<style>
.hero-section {
    background: <?php echo $house_info['gradient']; ?>;
    color: white;
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.stats-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-4px);
}

.member-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.member-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.back-btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <?php if ($selected_house === 'Aakash'): ?>
    <!-- Premium Full-Screen Hero Section with Animated Blue Sky for Aakash House -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800;900&display=swap');

        .aakash-sky-hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(30, 136, 229, 0.45) 0%, rgba(79, 195, 247, 0.40) 60%, rgba(129, 212, 250, 0.50) 100%), url('assets/img/aakash_bg.jpg') center/cover no-repeat;
            box-sizing: border-box;
            margin-top: -72px;
            padding-top: 72px;
        }

        /* Sun Rays top-right corner */
        .aakash-sun-glow {
            position: absolute;
            top: -120px;
            right: -120px;
            width: 850px;
            height: 850px;
            background: radial-gradient(circle at 75% 25%, rgba(255, 255, 255, 0.65) 0%, rgba(100, 181, 246, 0.35) 45%, transparent 75%);
            pointer-events: none;
            z-index: 1;
            animation: sunGlowPulse 6s ease-in-out infinite alternate;
        }

        .aakash-sun-rays {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.07) 0px,
                rgba(255, 255, 255, 0.07) 40px,
                transparent 40px,
                transparent 120px
            );
            mask-image: radial-gradient(circle at 85% 15%, black 25%, transparent 70%);
            -webkit-mask-image: radial-gradient(circle at 85% 15%, black 25%, transparent 70%);
            pointer-events: none;
            z-index: 1;
        }

        @keyframes sunGlowPulse {
            0% { opacity: 0.8; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.04); }
        }

        /* Animated Drifting Cumulus Clouds */
        .aakash-clouds-wrapper {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
            overflow: hidden;
        }

        .aakash-cloud-shape {
            position: absolute;
            background: #F8FBFF;
            border-radius: 200px;
            filter: drop-shadow(0 15px 30px rgba(255, 255, 255, 0.55));
            opacity: 0.88;
            will-change: transform;
        }

        .aakash-cloud-shape::before, .aakash-cloud-shape::after {
            content: '';
            position: absolute;
            background: inherit;
            border-radius: 50%;
        }

        /* Cloud 1 - Top layer */
        .aakash-cloud-1 {
            width: 360px;
            height: 110px;
            top: 18%;
            left: -400px;
            animation: driftCloud 48s linear infinite;
        }
        .aakash-cloud-1::before {
            width: 160px;
            height: 160px;
            top: -75px;
            left: 50px;
        }
        .aakash-cloud-1::after {
            width: 120px;
            height: 120px;
            top: -50px;
            left: 180px;
        }

        /* Cloud 2 - Mid layer */
        .aakash-cloud-2 {
            width: 480px;
            height: 130px;
            top: 42%;
            left: -520px;
            animation: driftCloud 68s linear infinite 14s;
            opacity: 0.78;
        }
        .aakash-cloud-2::before {
            width: 210px;
            height: 210px;
            top: -95px;
            left: 70px;
        }
        .aakash-cloud-2::after {
            width: 150px;
            height: 150px;
            top: -65px;
            left: 240px;
        }

        /* Cloud 3 - Lower layer */
        .aakash-cloud-3 {
            width: 550px;
            height: 150px;
            bottom: 6%;
            left: -600px;
            animation: driftCloud 88s linear infinite 6s;
            opacity: 0.92;
        }
        .aakash-cloud-3::before {
            width: 240px;
            height: 240px;
            top: -110px;
            left: 90px;
        }
        .aakash-cloud-3::after {
            width: 180px;
            height: 180px;
            top: -80px;
            left: 280px;
        }

        @keyframes driftCloud {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(calc(100vw + 700px), 0, 0); }
        }

        /* Dust particles canvas */
        #aakashDustCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Birds flying slowly */
        .aakash-birds-flock {
            position: absolute;
            top: 22%;
            left: -120px;
            z-index: 3;
            pointer-events: none;
            animation: flyFlock 26s linear infinite;
        }

        .aakash-bird-item {
            position: absolute;
            width: 32px;
            height: 18px;
        }

        .aakash-bird-svg {
            width: 100%;
            height: 100%;
            fill: rgba(255, 255, 255, 0.9);
            filter: drop-shadow(0 2px 4px rgba(30, 136, 229, 0.3));
            animation: wingFlap 0.75s ease-in-out infinite alternate;
        }

        @keyframes flyFlock {
            0% { transform: translate3d(-120px, 0, 0) scale(0.65); }
            50% { transform: translate3d(50vw, -45px, 0) scale(0.85); }
            100% { transform: translate3d(calc(100vw + 120px), -90px, 0) scale(0.65); }
        }

        @keyframes wingFlap {
            0% { transform: scaleY(1); }
            100% { transform: scaleY(0.25); }
        }

        /* Breeze Atmosphere */
        .aakash-breeze-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 40%, transparent 50%, rgba(248, 251, 255, 0.15) 100%);
            pointer-events: none;
            z-index: 3;
            animation: breezePulse 7s ease-in-out infinite alternate;
        }

        @keyframes breezePulse {
            0% { opacity: 0.5; }
            100% { opacity: 0.85; }
        }

        /* Centered Hero Content */
        .aakash-hero-card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 960px;
            margin: auto;
        }

        .aakash-house-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #FFFFFF;
            padding: 8px 22px;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .aakash-hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 64px;
            color: #FFFFFF;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.22), 0 2px 8px rgba(30, 136, 229, 0.35);
            letter-spacing: -0.5px;
        }

        .aakash-hero-tagline {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: #FFFFFF;
            opacity: 0.96;
            margin-bottom: 38px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }

        /* Premium Glassmorphism Explore Button */
        .aakash-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 50px;
            padding: 16px 44px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .aakash-explore-btn:hover {
            background: rgba(255, 255, 255, 0.42);
            border-color: #FFFFFF;
            color: #FFFFFF;
            box-shadow: 0 12px 45px rgba(100, 181, 246, 0.65), inset 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-4px);
            text-decoration: none;
        }

        .aakash-explore-btn i {
            transition: transform 0.3s ease;
        }

        .aakash-explore-btn:hover i {
            transform: translateY(4px);
        }

        /* Back link button on hero */
        .aakash-back-link {
            position: absolute;
            top: 90px;
            left: 30px;
            z-index: 12;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .aakash-back-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            text-decoration: none;
        }

        /* Responsive Typography */
        @media (max-width: 991px) {
            .aakash-hero-title {
                font-size: 42px;
            }
            .aakash-hero-tagline {
                font-size: 18px;
            }
            .aakash-back-link {
                top: 85px;
                left: 15px;
            }
        }

        @media (max-width: 576px) {
            .aakash-hero-title {
                font-size: 32px;
            }
            .aakash-hero-tagline {
                font-size: 16px;
            }
            .aakash-explore-btn {
                padding: 14px 34px;
                font-size: 1rem;
            }
        }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .aakash-cloud-1, .aakash-cloud-2, .aakash-cloud-3, .aakash-birds-flock, .aakash-sun-glow {
                animation: none !important;
            }
            #aakashDustCanvas {
                display: none !important;
            }
        }
    </style>

    <div class="aakash-sky-hero">
        <a href="houses_dashboard.php" class="aakash-back-link d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Houses
        </a>

        <!-- Sun rays -->
        <div class="aakash-sun-glow"></div>
        <div class="aakash-sun-rays"></div>

        <!-- Drifting clouds -->
        <div class="aakash-clouds-wrapper">
            <div class="aakash-cloud-shape aakash-cloud-1"></div>
            <div class="aakash-cloud-shape aakash-cloud-2"></div>
            <div class="aakash-cloud-shape aakash-cloud-3"></div>
        </div>

        <!-- Dust particles canvas -->
        <canvas id="aakashDustCanvas"></canvas>

        <!-- Flying birds -->
        <div class="aakash-birds-flock">
            <div class="aakash-bird-item" style="top: 0; left: 0;">
                <svg class="aakash-bird-svg" viewBox="0 0 50 30"><path d="M 0 15 Q 12 0 25 15 Q 38 0 50 15 Q 38 10 25 22 Q 12 10 0 15 Z"/></svg>
            </div>
            <div class="aakash-bird-item" style="top: -18px; left: 38px; transform: scale(0.7);">
                <svg class="aakash-bird-svg" viewBox="0 0 50 30"><path d="M 0 15 Q 12 0 25 15 Q 38 0 50 15 Q 38 10 25 22 Q 12 10 0 15 Z"/></svg>
            </div>
        </div>

        <!-- Breeze atmosphere -->
        <div class="aakash-breeze-glow"></div>

        <!-- Hero Content -->
        <div class="aakash-hero-card">
            <div class="aakash-house-badge">
                <i class="fas fa-cloud"></i> Aakash House (Sky)
            </div>
            <h1 class="aakash-hero-title">SRKR CSD-CSIT Department</h1>
            <p class="aakash-hero-tagline">Where Learning Meets Innovation</p>
            <a href="#aakash-details-section" id="aakashExploreBtn" class="aakash-explore-btn">
                Explore
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 60FPS Particle Canvas for Dust Shimmer
        const canvas = document.getElementById('aakashDustCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const particles = [];
            const count = 35;
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    radius: Math.random() * 2.2 + 0.5,
                    alpha: Math.random() * 0.7 + 0.2,
                    speedY: - (Math.random() * 0.35 + 0.1),
                    speedX: Math.random() * 0.3 - 0.15,
                    pulseSpeed: Math.random() * 0.03 + 0.01
                });
            }

            function renderParticles() {
                ctx.clearRect(0, 0, width, height);

                particles.forEach(p => {
                    p.y += p.speedY;
                    p.x += p.speedX;
                    p.alpha += Math.sin(Date.now() * p.pulseSpeed) * 0.01;

                    if (p.y < -10) p.y = height + 10;
                    if (p.x < -10) p.x = width + 10;
                    if (p.x > width + 10) p.x = -10;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255, 255, 255, ${Math.max(0.1, Math.min(0.95, p.alpha))})`;
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = '#64B5F6';
                    ctx.fill();
                });

                requestAnimationFrame(renderParticles);
            }

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                renderParticles();
            }
        }

        // Smooth Scroll on Explore Button Click
        const btn = document.getElementById('aakashExploreBtn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('aakash-details-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
    </script>
    <?php elseif ($selected_house === 'Agni'): ?>
    <!-- Premium Full-Screen Hero Section with Animated Fire & Embers for Agni House -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800;900&display=swap');

        .agni-fire-hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(183, 28, 28, 0.50) 0%, rgba(229, 57, 53, 0.45) 60%, rgba(245, 124, 0, 0.50) 100%), url('assets/img/agni_bg.jpg') center/cover no-repeat;
            box-sizing: border-box;
            margin-top: -72px;
            padding-top: 72px;
        }

        /* Fire Glow Overlay */
        .agni-fire-glow {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 600px;
            background: radial-gradient(ellipse at 50% 100%, rgba(255, 112, 67, 0.55) 0%, rgba(244, 67, 54, 0.25) 50%, transparent 80%);
            pointer-events: none;
            z-index: 1;
            animation: fireGlowPulse 4s ease-in-out infinite alternate;
        }

        @keyframes fireGlowPulse {
            0% { opacity: 0.7; transform: translateX(-50%) scale(1); }
            100% { opacity: 1; transform: translateX(-50%) scale(1.08); }
        }

        /* Ember Particles Canvas */
        #agniEmberCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Hero Content */
        .agni-hero-card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 960px;
            margin: auto;
        }

        .agni-house-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(244, 67, 54, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #FFFFFF;
            padding: 8px 22px;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(244, 67, 54, 0.4);
        }

        .agni-hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 64px;
            color: #FFFFFF;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(213, 0, 0, 0.6);
            letter-spacing: -0.5px;
        }

        .agni-hero-tagline {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: #FFFFFF;
            opacity: 0.96;
            margin-bottom: 38px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        /* Glassmorphism Explore Button for Agni */
        .agni-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 50px;
            padding: 16px 44px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(183, 28, 28, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .agni-explore-btn:hover {
            background: rgba(255, 255, 255, 0.42);
            border-color: #FFFFFF;
            color: #FFFFFF;
            box-shadow: 0 12px 45px rgba(255, 112, 67, 0.65), inset 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-4px);
            text-decoration: none;
        }

        .agni-explore-btn i {
            transition: transform 0.3s ease;
        }

        .agni-explore-btn:hover i {
            transform: translateY(4px);
        }

        /* Back link button */
        .agni-back-link {
            position: absolute;
            top: 90px;
            left: 30px;
            z-index: 12;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .agni-back-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .agni-hero-title { font-size: 42px; }
            .agni-hero-tagline { font-size: 18px; }
            .agni-back-link { top: 85px; left: 15px; }
        }

        @media (max-width: 576px) {
            .agni-hero-title { font-size: 32px; }
            .agni-hero-tagline { font-size: 16px; }
            .agni-explore-btn { padding: 14px 34px; font-size: 1rem; }
        }
    </style>

    <div class="agni-fire-hero">
        <a href="houses_dashboard.php" class="agni-back-link d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Houses
        </a>

        <!-- Fire Glow -->
        <div class="agni-fire-glow"></div>

        <!-- Ember particles canvas -->
        <canvas id="agniEmberCanvas"></canvas>

        <!-- Hero Content -->
        <div class="agni-hero-card">
            <div class="agni-house-badge">
                <i class="fas fa-fire"></i> Agni House (Fire)
            </div>
            <h1 class="agni-hero-title">SRKR CSD-CSIT Department</h1>
            <p class="agni-hero-tagline">Where Passion Ignites Innovation</p>
            <a href="#aakash-details-section" id="agniExploreBtn" class="agni-explore-btn">
                Explore
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ember Particles Animation Canvas
        const canvas = document.getElementById('agniEmberCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const embers = [];
            const count = 45;
            for (let i = 0; i < count; i++) {
                embers.push({
                    x: Math.random() * width,
                    y: height + Math.random() * 100,
                    radius: Math.random() * 2.5 + 1,
                    color: Math.random() > 0.4 ? '#FF7043' : '#FFD54F',
                    alpha: Math.random() * 0.8 + 0.2,
                    speedY: - (Math.random() * 1.2 + 0.4),
                    speedX: Math.random() * 0.8 - 0.4,
                    pulseSpeed: Math.random() * 0.04 + 0.02
                });
            }

            function renderEmbers() {
                ctx.clearRect(0, 0, width, height);

                embers.forEach(e => {
                    e.y += e.speedY;
                    e.x += e.speedX;
                    e.alpha += Math.sin(Date.now() * e.pulseSpeed) * 0.02;

                    if (e.y < -20) {
                        e.y = height + Math.random() * 20;
                        e.x = Math.random() * width;
                    }

                    ctx.beginPath();
                    ctx.arc(e.x, e.y, e.radius, 0, Math.PI * 2);
                    ctx.fillStyle = e.color;
                    ctx.globalAlpha = Math.max(0.1, Math.min(0.95, e.alpha));
                    ctx.shadowBlur = 12;
                    ctx.shadowColor = e.color;
                    ctx.fill();
                });
                ctx.globalAlpha = 1;

                requestAnimationFrame(renderEmbers);
            }

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                renderEmbers();
            }
        }

        // Smooth Scroll on Explore Button Click
        const btn = document.getElementById('agniExploreBtn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('aakash-details-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
    </script>
    <?php elseif (strtoupper($selected_house) === 'PRUDHVI'): ?>
    <!-- Premium Full-Screen Hero Section with Earth Theme for Prudhvi House -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800;900&display=swap');

        .prudhvi-earth-hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(78, 52, 46, 0.45) 0%, rgba(109, 76, 65, 0.40) 60%, rgba(141, 110, 99, 0.50) 100%), url('assets/img/prudhvi_bg.jpg') center/cover no-repeat;
            box-sizing: border-box;
            margin-top: -72px;
            padding-top: 72px;
        }

        /* Earth Ambient Glow Overlay */
        .prudhvi-earth-glow {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 600px;
            background: radial-gradient(ellipse at 50% 100%, rgba(141, 110, 99, 0.55) 0%, rgba(109, 76, 65, 0.25) 50%, transparent 80%);
            pointer-events: none;
            z-index: 1;
            animation: earthGlowPulse 5s ease-in-out infinite alternate;
        }

        @keyframes earthGlowPulse {
            0% { opacity: 0.7; transform: translateX(-50%) scale(1); }
            100% { opacity: 1; transform: translateX(-50%) scale(1.06); }
        }

        /* Leaf/Dust Particles Canvas */
        #prudhviParticleCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Hero Content */
        .prudhvi-hero-card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 960px;
            margin: auto;
        }

        .prudhvi-house-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(141, 110, 99, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #FFFFFF;
            padding: 8px 22px;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(109, 76, 65, 0.4);
        }

        .prudhvi-hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 64px;
            color: #FFFFFF;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(78, 52, 46, 0.6);
            letter-spacing: -0.5px;
        }

        .prudhvi-hero-tagline {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: #FFFFFF;
            opacity: 0.96;
            margin-bottom: 38px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        /* Glassmorphism Explore Button for Prudhvi */
        .prudhvi-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 50px;
            padding: 16px 44px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(78, 52, 46, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .prudhvi-explore-btn:hover {
            background: rgba(255, 255, 255, 0.42);
            border-color: #FFFFFF;
            color: #FFFFFF;
            box-shadow: 0 12px 45px rgba(141, 110, 99, 0.65), inset 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-4px);
            text-decoration: none;
        }

        .prudhvi-explore-btn i {
            transition: transform 0.3s ease;
        }

        .prudhvi-explore-btn:hover i {
            transform: translateY(4px);
        }

        /* Back link button */
        .prudhvi-back-link {
            position: absolute;
            top: 90px;
            left: 30px;
            z-index: 12;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .prudhvi-back-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .prudhvi-hero-title { font-size: 42px; }
            .prudhvi-hero-tagline { font-size: 18px; }
            .prudhvi-back-link { top: 85px; left: 15px; }
        }

        @media (max-width: 576px) {
            .prudhvi-hero-title { font-size: 32px; }
            .prudhvi-hero-tagline { font-size: 16px; }
            .prudhvi-explore-btn { padding: 14px 34px; font-size: 1rem; }
        }
    </style>

    <div class="prudhvi-earth-hero">
        <a href="houses_dashboard.php" class="prudhvi-back-link d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Houses
        </a>

        <!-- Earth Glow -->
        <div class="prudhvi-earth-glow"></div>

        <!-- Particles canvas -->
        <canvas id="prudhviParticleCanvas"></canvas>

        <!-- Hero Content -->
        <div class="prudhvi-hero-card">
            <div class="prudhvi-house-badge">
                <i class="fas fa-mountain"></i> Prudhvi House (Earth)
            </div>
            <h1 class="prudhvi-hero-title">SRKR CSD-CSIT Department</h1>
            <p class="prudhvi-hero-tagline">Grounded in Values, Building the Future</p>
            <a href="#aakash-details-section" id="prudhviExploreBtn" class="prudhvi-explore-btn">
                Explore
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Shimmering Golden/Earthy Leaf Particles Canvas
        const canvas = document.getElementById('prudhviParticleCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const particles = [];
            const count = 40;
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    radius: Math.random() * 2.2 + 0.6,
                    color: Math.random() > 0.4 ? '#D7CCC8' : '#A1887F',
                    alpha: Math.random() * 0.7 + 0.2,
                    speedY: - (Math.random() * 0.4 + 0.1),
                    speedX: Math.random() * 0.4 - 0.2,
                    pulseSpeed: Math.random() * 0.03 + 0.01
                });
            }

            function renderParticles() {
                ctx.clearRect(0, 0, width, height);

                particles.forEach(p => {
                    p.y += p.speedY;
                    p.x += p.speedX;
                    p.alpha += Math.sin(Date.now() * p.pulseSpeed) * 0.015;

                    if (p.y < -10) p.y = height + 10;
                    if (p.x < -10) p.x = width + 10;
                    if (p.x > width + 10) p.x = -10;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.globalAlpha = Math.max(0.1, Math.min(0.9, p.alpha));
                    ctx.shadowBlur = 8;
                    ctx.shadowColor = p.color;
                    ctx.fill();
                });
                ctx.globalAlpha = 1;

                requestAnimationFrame(renderParticles);
            }

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                renderParticles();
            }
        }

        // Smooth Scroll on Explore Button Click
        const btn = document.getElementById('prudhviExploreBtn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('aakash-details-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
    </script>
    <?php elseif ($selected_house === 'Jal' || strtoupper($selected_house) === 'JAL'): ?>
    <!-- Premium Full-Screen Hero Section with Water/Ocean Theme for Jal House -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800;900&display=swap');

        .jal-water-hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(13, 71, 161, 0.45) 0%, rgba(21, 101, 192, 0.40) 60%, rgba(30, 136, 229, 0.50) 100%), url('assets/img/jal_bg.jpg') center/cover no-repeat;
            box-sizing: border-box;
            margin-top: -72px;
            padding-top: 72px;
        }

        /* Ocean Ambient Glow Overlay */
        .jal-water-glow {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 600px;
            background: radial-gradient(ellipse at 50% 100%, rgba(79, 195, 247, 0.55) 0%, rgba(25, 118, 210, 0.25) 50%, transparent 80%);
            pointer-events: none;
            z-index: 1;
            animation: waterGlowPulse 5s ease-in-out infinite alternate;
        }

        @keyframes waterGlowPulse {
            0% { opacity: 0.7; transform: translateX(-50%) scale(1); }
            100% { opacity: 1; transform: translateX(-50%) scale(1.06); }
        }

        /* Water Bubbles / Ripples Particles Canvas */
        #jalBubbleCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Hero Content */
        .jal-hero-card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 960px;
            margin: auto;
        }

        .jal-house-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(33, 150, 243, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #FFFFFF;
            padding: 8px 22px;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(33, 150, 243, 0.4);
        }

        .jal-hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 64px;
            color: #FFFFFF;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(13, 71, 161, 0.6);
            letter-spacing: -0.5px;
        }

        .jal-hero-tagline {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: #FFFFFF;
            opacity: 0.96;
            margin-bottom: 38px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        /* Glassmorphism Explore Button for Jal */
        .jal-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 50px;
            padding: 16px 44px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(13, 71, 161, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .jal-explore-btn:hover {
            background: rgba(255, 255, 255, 0.42);
            border-color: #FFFFFF;
            color: #FFFFFF;
            box-shadow: 0 12px 45px rgba(79, 195, 247, 0.65), inset 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-4px);
            text-decoration: none;
        }

        .jal-explore-btn i {
            transition: transform 0.3s ease;
        }

        .jal-explore-btn:hover i {
            transform: translateY(4px);
        }

        /* Back link button */
        .jal-back-link {
            position: absolute;
            top: 90px;
            left: 30px;
            z-index: 12;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .jal-back-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .jal-hero-title { font-size: 42px; }
            .jal-hero-tagline { font-size: 18px; }
            .jal-back-link { top: 85px; left: 15px; }
        }

        @media (max-width: 576px) {
            .jal-hero-title { font-size: 32px; }
            .jal-hero-tagline { font-size: 16px; }
            .jal-explore-btn { padding: 14px 34px; font-size: 1rem; }
        }
    </style>

    <div class="jal-water-hero">
        <a href="houses_dashboard.php" class="jal-back-link d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Houses
        </a>

        <!-- Ocean Glow -->
        <div class="jal-water-glow"></div>

        <!-- Bubbles canvas -->
        <canvas id="jalBubbleCanvas"></canvas>

        <!-- Hero Content -->
        <div class="jal-hero-card">
            <div class="jal-house-badge">
                <i class="fas fa-water"></i> Jal House (Water)
            </div>
            <h1 class="jal-hero-title">SRKR CSD-CSIT Department</h1>
            <p class="jal-hero-tagline">Flowing with Wisdom, Shaping the Future</p>
            <a href="#aakash-details-section" id="jalExploreBtn" class="jal-explore-btn">
                Explore
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Floating Bubbles Particles Canvas
        const canvas = document.getElementById('jalBubbleCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const bubbles = [];
            const count = 45;
            for (let i = 0; i < count; i++) {
                bubbles.push({
                    x: Math.random() * width,
                    y: height + Math.random() * 100,
                    radius: Math.random() * 3 + 1,
                    color: Math.random() > 0.4 ? '#E0F7FA' : '#80DEEA',
                    alpha: Math.random() * 0.7 + 0.2,
                    speedY: - (Math.random() * 1.0 + 0.3),
                    speedX: Math.random() * 0.6 - 0.3,
                    pulseSpeed: Math.random() * 0.03 + 0.015
                });
            }

            function renderBubbles() {
                ctx.clearRect(0, 0, width, height);

                bubbles.forEach(b => {
                    b.y += b.speedY;
                    b.x += b.speedX + Math.sin(Date.now() * 0.002 + b.radius) * 0.3;
                    b.alpha += Math.sin(Date.now() * b.pulseSpeed) * 0.015;

                    if (b.y < -15) {
                        b.y = height + Math.random() * 20;
                        b.x = Math.random() * width;
                    }

                    ctx.beginPath();
                    ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);
                    ctx.fillStyle = b.color;
                    ctx.globalAlpha = Math.max(0.1, Math.min(0.9, b.alpha));
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = b.color;
                    ctx.fill();
                });
                ctx.globalAlpha = 1;

                requestAnimationFrame(renderBubbles);
            }

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                renderBubbles();
            }
        }

        // Smooth Scroll on Explore Button Click
        const btn = document.getElementById('jalExploreBtn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('aakash-details-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
    </script>
    <?php elseif ($selected_house === 'Vayu' || strtoupper($selected_house) === 'VAYU'): ?>
    <!-- Premium Full-Screen Hero Section with Wind/Air Theme for Vayu House -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800;900&display=swap');

        .vayu-wind-hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(46, 125, 50, 0.45) 0%, rgba(56, 142, 60, 0.40) 60%, rgba(76, 175, 80, 0.50) 100%), url('assets/img/vayu_bg.png') center bottom/cover no-repeat;
            box-sizing: border-box;
            margin-top: -72px;
            padding-top: 72px;
        }

        /* Breeze Ambient Glow Overlay */
        .vayu-wind-glow {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 600px;
            background: radial-gradient(ellipse at 50% 100%, rgba(129, 199, 132, 0.55) 0%, rgba(56, 142, 60, 0.25) 50%, transparent 80%);
            pointer-events: none;
            z-index: 1;
            animation: windGlowPulse 5s ease-in-out infinite alternate;
        }

        @keyframes windGlowPulse {
            0% { opacity: 0.7; transform: translateX(-50%) scale(1); }
            100% { opacity: 1; transform: translateX(-50%) scale(1.06); }
        }

        /* Swirling Wind Streams Particles Canvas */
        #vayuWindCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Hero Content */
        .vayu-hero-card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 960px;
            margin: auto;
        }

        .vayu-house-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(76, 175, 80, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #FFFFFF;
            padding: 8px 22px;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.4);
        }

        .vayu-hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 64px;
            color: #FFFFFF;
            line-height: 1.15;
            margin-bottom: 18px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(46, 125, 50, 0.6);
            letter-spacing: -0.5px;
        }

        .vayu-hero-tagline {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: #FFFFFF;
            opacity: 0.96;
            margin-bottom: 38px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }

        /* Glassmorphism Explore Button for Vayu */
        .vayu-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 50px;
            padding: 16px 44px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(46, 125, 50, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .vayu-explore-btn:hover {
            background: rgba(255, 255, 255, 0.42);
            border-color: #FFFFFF;
            color: #FFFFFF;
            box-shadow: 0 12px 45px rgba(129, 199, 132, 0.65), inset 0 0 20px rgba(255, 255, 255, 0.6);
            transform: translateY(-4px);
            text-decoration: none;
        }

        .vayu-explore-btn i {
            transition: transform 0.3s ease;
        }

        .vayu-explore-btn:hover i {
            transform: translateY(4px);
        }

        /* Back link button */
        .vayu-back-link {
            position: absolute;
            top: 90px;
            left: 30px;
            z-index: 12;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .vayu-back-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .vayu-hero-title { font-size: 42px; }
            .vayu-hero-tagline { font-size: 18px; }
            .vayu-back-link { top: 85px; left: 15px; }
        }

        @media (max-width: 576px) {
            .vayu-hero-title { font-size: 32px; }
            .vayu-hero-tagline { font-size: 16px; }
            .vayu-explore-btn { padding: 14px 34px; font-size: 1rem; }
        }
    </style>

    <div class="vayu-wind-hero">
        <a href="houses_dashboard.php" class="vayu-back-link d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Houses
        </a>

        <!-- Breeze Glow -->
        <div class="vayu-wind-glow"></div>

        <!-- Wind Streams canvas -->
        <canvas id="vayuWindCanvas"></canvas>

        <!-- Hero Content -->
        <div class="vayu-hero-card">
            <div class="vayu-house-badge">
                <i class="fas fa-wind"></i> Vayu House (Wind)
            </div>
            <h1 class="vayu-hero-title">SRKR CSD-CSIT Department</h1>
            <p class="vayu-hero-tagline">Swift and Free, Carrying Change Across the World</p>
            <a href="#aakash-details-section" id="vayuExploreBtn" class="vayu-explore-btn">
                Explore
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Drifting Wind & Breeze Stream Canvas
        const canvas = document.getElementById('vayuWindCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const particles = [];
            const count = 45;
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    length: Math.random() * 40 + 15,
                    radius: Math.random() * 1.8 + 0.5,
                    color: Math.random() > 0.35 ? '#C8E6C9' : '#E8F5E9',
                    alpha: Math.random() * 0.6 + 0.2,
                    speedX: Math.random() * 2.2 + 0.8,
                    speedY: Math.random() * 0.4 - 0.2,
                    pulseSpeed: Math.random() * 0.03 + 0.015
                });
            }

            function renderWind() {
                ctx.clearRect(0, 0, width, height);

                particles.forEach(p => {
                    p.x += p.speedX;
                    p.y += p.speedY;
                    p.alpha += Math.sin(Date.now() * p.pulseSpeed) * 0.015;

                    if (p.x > width + p.length) {
                        p.x = -p.length;
                        p.y = Math.random() * height;
                    }

                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p.x - p.length, p.y);
                    ctx.strokeStyle = p.color;
                    ctx.lineWidth = p.radius;
                    ctx.lineCap = 'round';
                    ctx.globalAlpha = Math.max(0.1, Math.min(0.85, p.alpha));
                    ctx.shadowBlur = 8;
                    ctx.shadowColor = p.color;
                    ctx.stroke();
                });
                ctx.globalAlpha = 1;

                requestAnimationFrame(renderWind);
            }

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                renderWind();
            }
        }

        // Smooth Scroll on Explore Button Click
        const btn = document.getElementById('vayuExploreBtn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('aakash-details-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
    </script>
    <?php else: ?>
    <!-- Hero Section for other houses -->
    <div class="hero-section">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="houses_dashboard.php" class="back-btn mb-3 d-inline-flex align-items-center">
                        <i class="fas fa-arrow-left me-2"></i> Back to Houses
                    </a>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-4" style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 16px;">
                            <i class="<?php echo $house_info['icon']; ?>" style="font-size: 3rem;"></i>
                        </div>
                        <div>
                            <h1 class="mb-2" style="font-size: 3rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                <?php echo $house_info['name']; ?>
                            </h1>
                            <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 400; opacity: 0.9;">
                                House
                            </h2>
                        </div>
                    </div>
                    <p class="lead mb-0" style="font-size: 1.2rem; opacity: 0.9; line-height: 1.6;">
                        <?php echo $house_info['description']; ?>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; backdrop-filter: blur(10px);">
                        <i class="<?php echo $house_info['icon']; ?>" style="font-size: 5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Section -->
    <div id="aakash-details-section" class="container" style="margin-top: <?php echo $selected_house === 'Aakash' ? '40px' : '-40px'; ?>; position: relative; z-index: 2;">
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-sm-4">
                <div class="stats-card text-center p-3 h-100 shadow-sm border-0" style="border-top: 4px solid <?php echo $house_info['color']; ?> !important;">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2rem; margin-bottom: 8px;">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 800; margin-bottom: 2px; font-size: 1.5rem;">
                        #<?php echo $current_house_rank; ?>
                    </h3>
                    <p class="text-muted small mb-0 font-semibold">House Rank</p>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="stats-card text-center p-3 h-100 shadow-sm border-0" style="border-top: 4px solid <?php echo $house_info['color']; ?> !important;">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2rem; margin-bottom: 8px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 800; margin-bottom: 2px; font-size: 1.5rem;">
                        <?php echo $house_stats['student_count']; ?>
                    </h3>
                    <p class="text-muted small mb-0 font-semibold">Total Members</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-4">
                <div class="stats-card text-center p-3 h-100 shadow-sm border-0" style="border-top: 4px solid <?php echo $house_info['color']; ?> !important;">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2rem; margin-bottom: 8px;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 800; margin-bottom: 2px; font-size: 1.5rem;">
                        <?php echo number_format($house_stats['total_points']); ?>
                    </h3>
                    <p class="text-muted small mb-0 font-semibold">Total Points</p>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="stats-card text-center p-3 h-100 shadow-sm border-0" style="border-top: 4px solid <?php echo $house_info['color']; ?> !important;">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2rem; margin-bottom: 8px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 800; margin-bottom: 2px; font-size: 1.5rem;">
                        <?php echo number_format($house_stats['avg_points'], 1); ?>
                    </h3>
                    <p class="text-muted small mb-0 font-semibold">Avg / Member</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card text-center p-3 h-100 shadow-sm border-0" style="border-top: 4px solid <?php echo $house_info['color']; ?> !important;">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2rem; margin-bottom: 8px;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 800; margin-bottom: 2px; font-size: 1.5rem;">
                        <?php echo number_format($house_stats['max_points']); ?>
                    </h3>
                    <p class="text-muted small mb-0 font-semibold">Top Scorer Score</p>
                </div>
            </div>
        </div>

        <!-- Leaderboard Comparison Banner -->
        <?php 
        $leader_info = $houses[$leader_house_key];
        $point_gap = $leader_house_points - $house_stats['total_points'];
        $leader_pct = $leader_house_points > 0 ? round(($house_stats['total_points'] / $leader_house_points) * 100) : 100;
        ?>
        <div class="card border-0 shadow-sm rounded-4 mb-5 p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-left: 6px solid <?php echo $house_info['color']; ?> !important;">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 48px; height: 48px; background: <?php echo $house_info['gradient']; ?>; font-size: 1.4rem;">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 font-bold" style="color: #1e293b; font-weight: 700;">
                                House Leaderboard Standing: Rank #<?php echo $current_house_rank; ?>
                            </h5>
                            <p class="mb-0 text-muted small">
                                <?php if ($current_house_rank == 1): ?>
                                    🎉 <strong><?php echo $house_info['name']; ?></strong> is currently leading all houses on the department leaderboard!
                                <?php else: ?>
                                    📊 Currently trailing <strong><?php echo $leader_info['name']; ?> House</strong> (#1) by <strong><?php echo number_format($point_gap); ?> points</strong> (<?php echo $leader_pct; ?>% of lead score).
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="p-2 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between small font-semibold mb-1 text-muted">
                            <span>Score vs Leader (<?php echo $leader_info['name']; ?>)</span>
                            <span><?php echo $leader_pct; ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 5px; background: #e2e8f0;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $leader_pct; ?>%; background: <?php echo $house_info['gradient']; ?>;" aria-valuenow="<?php echo $leader_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 3 House Contributors Spotlight -->
        <?php if (!empty($students)): 
            $top_3 = array_slice($students, 0, 3);
        ?>
            <div class="mb-5">
                <h4 class="font-bold mb-3" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-crown text-warning me-2"></i>Top 3 House Contributors
                </h4>
                <div class="row g-3">
                    <?php 
                    $t_rank = 1;
                    $t_colors = [1 => '#ffc107', 2 => '#adb5bd', 3 => '#cd7f32'];
                    $t_badges = [1 => '🥇 1st Top Contributor', 2 => '🥈 2nd Top Contributor', 3 => '🥉 3rd Top Contributor'];
                    foreach ($top_3 as $ts): 
                    ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-top: 4px solid <?php echo $t_colors[$t_rank]; ?> !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-bold" style="width: 44px; height: 44px; background: <?php echo $t_colors[$t_rank]; ?>; font-size: 1.2rem; font-weight: 800;">
                                        #<?php echo $t_rank; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="badge mb-1" style="background: <?php echo $t_colors[$t_rank]; ?>20; color: <?php echo $t_colors[$t_rank]; ?>; font-weight: 700; font-size: 0.75rem;">
                                            <?php echo $t_badges[$t_rank]; ?>
                                        </div>
                                        <h6 class="mb-0 font-bold" style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($ts['name']); ?></h6>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($ts['regd_no']); ?> &bull; <?php echo htmlspecialchars($ts['year_section']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="font-bold d-block" style="color: <?php echo $house_info['color']; ?>; font-weight: 800; font-size: 1.1rem;">
                                            <?php echo 0; ?>
                                        </span>
                                        <small class="text-muted">pts</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $t_rank++;
                    endforeach; 
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Members Section -->
    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 style="color: #212529; font-weight: 700;">House Members</h2>
                    <div class="d-flex gap-2">
                    <div class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search members..." style="border-radius: 0 8px 8px 0; max-width: 250px;">
                        </div>
                    </div>
                    </div>
                </div>

                <?php if (!empty($students)): ?>
                    <div class="table-responsive">
                        <table class="table" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <thead style="background: <?php echo $house_info['light_color']; ?>;">
                                <tr>
                                    <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>;">
                                        Rank
                                    </th>
                                    <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>;">
                                        Student Name
                                    </th>
                                    <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>;">
                                        Registration No.
                                    </th>
                                    <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>;">
                                        Branch
                                    </th>
                                    <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>;">
                                        Section
                                    </th>
                                    <?php if (!$using_new_schema): ?>
                                        <th style="padding: 16px 20px; border: none; font-weight: 600; color: <?php echo $house_info['color']; ?>; text-align: right;">
                                            Points
                                        </th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                foreach ($students as $student): 
                                    // Parse year_section to get branch and section
                                    $year_section_parts = explode(' - ', $student['year_section']);
                                    $branch = isset($year_section_parts[0]) ? $year_section_parts[0] : 'N/A';
                                    $section = isset($year_section_parts[1]) ? $year_section_parts[1] : 'N/A';
                                ?>
                                    <tr style="border-bottom: 1px solid #f1f3f4;" class="student-row" 
                                        data-branch="<?php echo htmlspecialchars($branch); ?>" 
                                        data-section="<?php echo htmlspecialchars($section); ?>">
                                        <td style="padding: 16px 20px; border: none;">
                                            <div class="d-flex align-items-center">
                                                <span style="background: <?php echo $house_info['color']; ?>; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem;">
                                                    <?php echo $rank; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td style="padding: 16px 20px; border: none;">
                                            <div style="font-weight: 600; color: #212529; font-size: 1rem;">
                                                <?php echo htmlspecialchars($student['name']); ?>
                                            </div>
                                        </td>
                                        <td style="padding: 16px 20px; border: none;">
                                            <span style="background: #f8f9fa; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 0.9rem; color: #495057;">
                                                <?php echo htmlspecialchars($student['regd_no']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 16px 20px; border: none;">
                                            <span style="background: <?php echo $house_info['light_color']; ?>; color: <?php echo $house_info['color']; ?>; padding: 6px 12px; border-radius: 20px; font-weight: 500; font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($branch); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 16px 20px; border: none; color: #6c757d; font-weight: 500;">
                                            <?php echo htmlspecialchars($section); ?>
                                        </td>
                                        <?php if (!$using_new_schema): ?>
                                            <td style="padding: 16px 20px; border: none; text-align: right;">
                                                <span style="font-weight: 700; font-size: 1.1rem; color: <?php echo $house_info['color']; ?>;">
                                                    <?php echo 0; ?>
                                                </span>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php 
                                    $rank++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div style="color: #6c757d; font-size: 4rem; margin-bottom: 20px;">
                            <i class="fas fa-users-slash"></i>
                        </div>
                        <h4 style="color: #6c757d; margin-bottom: 12px;">No Members Found</h4>
                        <p class="text-muted">This house doesn't have any members assigned yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');
        let hasResults = false;
        let visibleRank = 1;
        
        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const regNo = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const branch = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if (name.includes(searchText) || regNo.includes(searchText) || branch.includes(searchText)) {
                row.style.display = '';
                // Update rank
                const rankCell = row.querySelector('td:first-child span');
                if (rankCell) rankCell.textContent = visibleRank++;
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Handle no results message
        const tbody = document.querySelector('tbody');
        let noResultsRow = document.getElementById('no-search-results');
        
        if (!hasResults) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-search-results';
                noResultsRow.innerHTML = `
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-search me-2"></i> No members found matching "${this.value}"
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            } else {
                noResultsRow.style.display = '';
                noResultsRow.querySelector('td').innerHTML = `
                    <i class="fas fa-search me-2"></i> No members found matching "${this.value}"
                `;
            }
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    });
    </script>
</body>
</html>
