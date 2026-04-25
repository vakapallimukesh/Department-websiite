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
    'Pruthvi' => [
        'name' => 'Pruthvi', 
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

// Get selected house from URL parameter
$selected_house = isset($_GET['house']) ? $_GET['house'] : '';

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
            
            // Try to find house by exact name match first
            $house_sql = "SELECT hid FROM houses WHERE name = ?";
            $stmt_house = $conn->prepare($house_sql);
            $hid = null;
            
            if ($stmt_house) {
                $stmt_house->bind_param("s", $house_name_param);
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
                    'Aakash' => ['Alpha House', 'Aakash House', 'Sky House'],
                    'Jal' => ['Beta House', 'Jal House', 'Water House'],
                    'Vayu' => ['Gamma House', 'Vayu House', 'Wind House'],
                    'Pruthvi' => ['Delta House', 'Pruthvi House', 'Earth House'],
                    'Agni' => ['Epsilon House', 'Agni House', 'Fire House']
                ];
                
                if (isset($house_mapping[$selected_house])) {
                    foreach ($house_mapping[$selected_house] as $possible_name) {
                        $stmt_house_map = $conn->prepare("SELECT hid FROM houses WHERE name = ?");
                        if ($stmt_house_map) {
                            $stmt_house_map->bind_param("s", $possible_name);
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
            
            // Get students for this house
            if ($hid) {
                $sql = "SELECT 
                    s.student_id as regd_no,
                    s.name,
                    CONCAT(s.branch, ' - ', s.section) as year_section,
                    0 as total_points
                FROM students s 
                WHERE s.hid = ? 
                ORDER BY s.name ASC";
                
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

// Calculate house statistics
$house_stats = [
    'student_count' => count($students),
    'total_points' => array_sum(array_column($students, 'total_points')),
    'avg_points' => count($students) > 0 ? array_sum(array_column($students, 'total_points')) / count($students) : 0,
    'max_points' => count($students) > 0 ? max(array_column($students, 'total_points')) : 0
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
    
    <!-- Hero Section -->
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

    <!-- Stats Section -->
    <div class="container" style="margin-top: -40px; position: relative; z-index: 2;">
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2.5rem; margin-bottom: 12px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 700; margin-bottom: 4px;">
                        <?php echo $house_stats['student_count']; ?>
                    </h3>
                    <p class="text-muted mb-0">Total Members</p>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="stats-card text-center">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2.5rem; margin-bottom: 12px;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 700; margin-bottom: 4px;">
                        <?php echo number_format($house_stats['total_points']); ?>
                    </h3>
                    <p class="text-muted mb-0">Total Points</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2.5rem; margin-bottom: 12px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 700; margin-bottom: 4px;">
                        <?php echo number_format($house_stats['avg_points'], 1); ?>
                    </h3>
                    <p class="text-muted mb-0">Average Points</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div style="color: <?php echo $house_info['color']; ?>; font-size: 2.5rem; margin-bottom: 12px;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 style="color: #212529; font-weight: 700; margin-bottom: 4px;">
                        <?php echo number_format($house_stats['max_points']); ?>
                    </h3>
                    <p class="text-muted mb-0">Highest Score</p>
                </div>
            </div> -->
        </div>
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
                                                    <?php echo number_format($student['total_points']); ?>
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
