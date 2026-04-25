<?php
session_start();
include "../utils/connect.php";

// Check if super admin is logged in
if (!isset($_SESSION['superadmin_logged_in']) || $_SESSION['superadmin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $_SESSION['expire_time'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(145deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .content-area {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
        }
        .modal-content {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user-shield"></i> Super Admin</h4>
                        <p class="text-white-50">Welcome, <?php echo $_SESSION['superadmin_username']; ?></p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showSection('dashboard')">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('classes')">
                                <i class="fas fa-school"></i> Manage Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('students')">
                                <i class="fas fa-users"></i> Manage Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('events')">
                                <i class="fas fa-calendar-check"></i> Manage Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('bulk-points')">
                                <i class="fas fa-upload"></i> Bulk Points Upload
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('view-students')">
                                <i class="fas fa-list"></i> View All Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('reports')">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="super_admin_logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-area">
                <div class="pt-3 pb-2 mb-3">
                    
                    <!-- Dashboard Section -->
                    <div id="dashboard-section" class="content-section">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-school fa-2x mb-2"></i>
                                        <h4 id="total-classes"><?php
                                            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM classes");
                                            echo mysqli_fetch_assoc($result)['count'];
                                        ?></h4>
                                        <p class="mb-0">Total Classes</p>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <h4 id="total-students"><?php
                                            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
                                            echo mysqli_fetch_assoc($result)['count'];
                                        ?></h4>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-home fa-2x mb-2"></i>
                                        <h4 id="total-houses"><?php
                                            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM houses");
                                            echo mysqli_fetch_assoc($result)['count'];
                                        ?></h4>
                                        <p class="mb-0">Total Houses</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                        <h4 id="total-events"><?php
                                            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM events");
                                            echo mysqli_fetch_assoc($result)['count'];
                                        ?></h4>
                                        <p class="mb-0">Total Events</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5><i class="fas fa-trophy"></i> Top Students by Points</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="top-students-list">
                                            <?php
                                            $query = "SELECT s.student_id, s.name, 
                                                     COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points,
                                                     h.name as house_name
                                                     FROM students s
                                                     LEFT JOIN appreciations a ON s.student_id = a.student_id
                                                     LEFT JOIN organizers o ON s.student_id = o.student_id
                                                     LEFT JOIN participants p ON s.student_id = p.student_id
                                                     LEFT JOIN houses h ON s.hid = h.hid
                                                     GROUP BY s.student_id
                                                     ORDER BY total_points DESC
                                                     LIMIT 10";
                                            $result = mysqli_query($conn, $query);
                                            $rank = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<div class='d-flex justify-content-between align-items-center mb-2'>
                                                        <div>
                                                            <span class='badge bg-primary me-2'>#{$rank}</span>
                                                            <strong>{$row['name']}</strong>
                                                            <small class='text-muted'>({$row['house_name']})</small>
                                                        </div>
                                                        <span class='badge bg-success'>{$row['total_points']} pts</span>
                                                      </div>";
                                                $rank++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5><i class="fas fa-home"></i> House Points Overview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="house-points-list">
                                            <?php
                                            $query = "SELECT h.name as house_name,
                                                     COALESCE(SUM(a.points), 0) + COALESCE(SUM(o.points), 0) + COALESCE(SUM(p.points), 0) as total_points
                                                     FROM houses h
                                                     LEFT JOIN students s ON h.hid = s.hid
                                                     LEFT JOIN appreciations a ON s.student_id = a.student_id
                                                     LEFT JOIN organizers o ON s.student_id = o.student_id
                                                     LEFT JOIN participants p ON s.student_id = p.student_id
                                                     GROUP BY h.hid
                                                     ORDER BY total_points DESC";
                                            $result = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $color_class = ['primary', 'success', 'warning', 'info', 'secondary'][array_rand(['primary', 'success', 'warning', 'info', 'secondary'])];
                                                echo "<div class='d-flex justify-content-between align-items-center mb-2'>
                                                        <strong>{$row['house_name']}</strong>
                                                        <span class='badge bg-{$color_class}'>{$row['total_points']} pts</span>
                                                      </div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Classes Management Section -->
                    <div id="classes-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-school"></i> Manage Classes</h1>
                            <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                <i class="fas fa-plus"></i> Add New Class
                            </button>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="classesTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Class ID</th>
                                                <th>Academic Year</th>
                                                <th>Year</th>
                                                <th>Semester</th>
                                                <th>Branch</th>
                                                <th>Section</th>
                                                <th>Students Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="classesTableBody">
                                            <!-- Classes will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Management Section -->
                    <div id="students-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-users"></i> Manage Students</h1>
                            <div>
                                <button class="btn btn-warning btn-custom me-2" data-bs-toggle="modal" data-bs-target="#addPointsModal">
                                    <i class="fas fa-plus-circle"></i> Add Points
                                </button>
                                <button class="btn btn-success btn-custom me-2" data-bs-toggle="modal" data-bs-target="#bulkStudentModal">
                                    <i class="fas fa-upload"></i> Bulk Add Students
                                </button>
                                <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                    <i class="fas fa-user-plus"></i> Add New Student
                                </button>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="searchStudent" placeholder="Search students...">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterClass">
                                            <option value="">Filter by Class</option>
                                            <!-- Classes will be populated here -->
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterHouse">
                                            <option value="">Filter by House</option>
                                            <!-- Houses will be populated here -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="studentsTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Branch</th>
                                                <th>Section</th>
                                                <th>House</th>
                                                <th>Total Points</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsTableBody">
                                            <!-- Students will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Management Section -->
                    <div id="events-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-calendar-check"></i> Manage Events & Participants</h1>
                            <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#createEventModal">
                                <i class="fas fa-plus"></i> Create New Event
                            </button>
                        </div>
                        
                        <!-- Event Selection Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-calendar"></i> Select Event</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <select class="form-select" id="eventSelect" onchange="selectEvent()">
                                            <option value="">Select an Event</option>
                                            <!-- Events will be populated here -->
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-info" onclick="loadEvents()">
                                            <i class="fas fa-refresh"></i> Refresh Events
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Event Details -->
                        <div id="selectedEventCard" class="card mb-4" style="display: none;">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Event Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 id="eventTitle"></h6>
                                        <p id="eventDescription" class="text-muted"></p>
                                        <small><strong>Date:</strong> <span id="eventDate"></span> | <strong>Type:</strong> <span id="eventType"></span></small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                                                <i class="fas fa-user-plus"></i> Add Participant
                                            </button>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#bulkParticipantsModal">
                                                <i class="fas fa-upload"></i> Bulk Add
                                            </button>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bulkParticipantPointsModal">
                                                <i class="fas fa-award"></i> Bulk Points
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Participants Management -->
                        <div id="participantsSection" style="display: none;">
                            <div class="row">
                                <!-- Current Participants -->
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5><i class="fas fa-users"></i> Event Participants</h5>
                                            <span class="badge bg-primary" id="participantCount">0 participants</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <input type="text" class="form-control" id="searchParticipants" placeholder="Search participants...">
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Student ID</th>
                                                            <th>Name</th>
                                                            <th>House</th>
                                                            <th>Points</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="participantsTableBody">
                                                        <!-- Participants will be loaded here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Event Statistics -->
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6><i class="fas fa-chart-pie"></i> Event Statistics</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <small class="text-muted">Total Participants:</small>
                                                <span class="float-end" id="totalParticipants">0</span>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Total Points Awarded:</small>
                                                <span class="float-end" id="totalPointsAwarded">0</span>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Average Points:</small>
                                                <span class="float-end" id="averagePoints">0</span>
                                            </div>
                                            <hr>
                                            <div id="houseParticipation">
                                                <!-- House-wise participation will be shown here -->
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-tools"></i> Quick Actions</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-success btn-sm" onclick="addPointsToAllParticipants()">
                                                    <i class="fas fa-gift"></i> Award Points to All
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="exportParticipants()">
                                                    <i class="fas fa-download"></i> Export Participants
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="clearAllParticipants()" data-bs-toggle="tooltip" title="Remove all participants from this event">
                                                    <i class="fas fa-trash"></i> Clear All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Points Upload Section -->
                    <div id="bulk-points-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-upload"></i> Bulk Points Upload</h1>
                            <a href="download_sample_excel.php" class="btn btn-success btn-custom">
                                <i class="fas fa-download"></i> Download Sample Excel
                            </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-file-excel"></i> Upload Excel File</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="bulkUploadForm" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="excelFile" class="form-label">Select Excel File:</label>
                                                <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx,.xls" required>
                                                <div class="form-text">Please upload an Excel file (.xlsx or .xls) with the student points data.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="pointsReason" class="form-label">Reason for Points:</label>
                                                <input type="text" class="form-control" id="pointsReason" name="pointsReason" placeholder="e.g., Academic Excellence, Event Participation" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-custom">
                                                <i class="fas fa-upload"></i> Upload and Process
                                            </button>
                                        </form>
                                        <div id="uploadResult" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-info-circle"></i> Instructions</h5>
                                    </div>
                                    <div class="card-body">
                                        <ol>
                                            <li>Download the sample Excel template</li>
                                            <li>Fill in the student IDs and points</li>
                                            <li>Ensure all student IDs are valid</li>
                                            <li>Points can be positive or negative</li>
                                            <li>Upload the completed file</li>
                                        </ol>
                                        <div class="alert alert-warning">
                                            <small><strong>Note:</strong> Please verify all data before uploading as this action cannot be undone.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View All Students Section -->
                    <div id="view-students-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-list"></i> All Students Overview</h1>
                            <button class="btn btn-success btn-custom" onclick="exportStudentsData()">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="searchAllStudents" placeholder="Search students...">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="filterAllClass">
                                            <option value="">All Classes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="filterAllHouse">
                                            <option value="">All Houses</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="filterAllBranch">
                                            <option value="">All Branches</option>
                                            <option value="CSD">CSD</option>
                                            <option value="CSIT">CSIT</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="sortBy">
                                            <option value="name">Sort by Name</option>
                                            <option value="points">Sort by Points</option>
                                            <option value="student_id">Sort by ID</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-outline-secondary" onclick="applyFilters()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Branch</th>
                                                <th>Section</th>
                                                <th>Class</th>
                                                <th>House</th>
                                                <th>Total Points</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="allStudentsTableBody">
                                            <!-- Students data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="pagination" class="mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Section -->
                    <div id="reports-section" class="content-section" style="display: none;">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2"><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-home"></i> House Performance</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="houseChart" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-graduation-cap"></i> Branch-wise Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="branchChart" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-calendar-alt"></i> Monthly Activity Report</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="activityChart" style="max-height: 400px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addClassForm">
                        <div class="mb-3">
                            <label for="academicYear" class="form-label">Academic Year:</label>
                            <input type="text" class="form-control" id="academicYear" name="academicYear" placeholder="2024-2028" required>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year:</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester:</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="branch" class="form-label">Branch:</label>
                            <select class="form-select" id="branch" name="branch" required>
                                <option value="">Select Branch</option>
                                <option value="CSD">Computer Science & Design (CSD)</option>
                                <option value="CSIT">Computer Science & Information Technology (CSIT)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="section" class="form-label">Section:</label>
                            <input type="text" class="form-control" id="section" name="section" placeholder="A" maxlength="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addClass()">Add Class</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="studentId" class="form-label">Student ID:</label>
                                    <input type="text" class="form-control" id="studentId" name="studentId" placeholder="23B91A0701" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="studentName" class="form-label">Student Name:</label>
                                    <input type="text" class="form-control" id="studentName" name="studentName" placeholder="Enter full name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="studentEmail" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="studentEmail" name="studentEmail" placeholder="student@srkrec.edu.in" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="studentPassword" class="form-label">Password:</label>
                                    <input type="text" class="form-control" id="studentPassword" name="studentPassword" placeholder="Default: Student ID" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="studentBranch" class="form-label">Branch:</label>
                                    <select class="form-select" id="studentBranch" name="studentBranch" required>
                                        <option value="">Select Branch</option>
                                        <option value="CSD">CSD</option>
                                        <option value="CSIT">CSIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="studentSection" class="form-label">Section:</label>
                                    <select class="form-select" id="studentSection" name="studentSection" required>
                                        <option value="">Select Section</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="studentClass" class="form-label">Class:</label>
                                    <select class="form-select" id="studentClass" name="studentClass" required>
                                        <option value="">Select Class</option>
                                        <!-- Classes will be populated here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="studentHouse" class="form-label">House:</label>
                            <select class="form-select" id="studentHouse" name="studentHouse" required>
                                <option value="">Select House</option>
                                <?php
                                $houses_result = mysqli_query($conn, "SELECT * FROM houses ORDER BY name");
                                while ($house = mysqli_fetch_assoc($houses_result)) {
                                    echo "<option value='{$house['hid']}'>{$house['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addStudent()">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Student Upload Modal -->
    <div class="modal fade" id="bulkStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload"></i> Bulk Add Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form id="bulkStudentForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="studentExcelFile" class="form-label">Select Excel/CSV File:</label>
                                    <input type="file" class="form-control" id="studentExcelFile" name="studentExcelFile" accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">Upload Excel (.xlsx, .xls) or CSV file with student data.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="defaultClass" class="form-label">Default Class (Optional):</label>
                                    <select class="form-select" id="defaultClass" name="defaultClass">
                                        <option value="">Select Default Class</option>
                                        <!-- Classes will be populated here -->
                                    </select>
                                    <div class="form-text">If no class specified in file, this will be used as default.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="defaultHouse" class="form-label">Default House (Optional):</label>
                                    <select class="form-select" id="defaultHouse" name="defaultHouse">
                                        <option value="">Select Default House</option>
                                        <?php
                                        mysqli_data_seek($houses_result, 0);
                                        while ($house = mysqli_fetch_assoc($houses_result)) {
                                            echo "<option value='{$house['hid']}'>{$house['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">If no house specified in file, this will be used as default.</div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="skipDuplicates" checked>
                                        <label class="form-check-label" for="skipDuplicates">
                                            Skip duplicate Student IDs
                                        </label>
                                        <div class="form-text">If checked, existing student IDs will be skipped instead of causing errors.</div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="fas fa-upload"></i> Upload Students
                                </button>
                            </form>
                            <div id="bulkStudentResult" class="mt-3"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-download"></i> Download Template</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="download_student_template.php?format=csv" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-csv"></i> CSV Template
                                        </a>
                                        <a href="download_student_template.php?format=excel" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-excel"></i> Excel Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6><i class="fas fa-info-circle"></i> File Format</h6>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <strong>Required Columns:</strong><br>
                                        • Student ID<br>
                                        • Name<br>
                                        • Email<br>
                                        • Branch (CSD/CSIT)<br>
                                        • Section (A/B)<br><br>
                                        
                                        <strong>Optional Columns:</strong><br>
                                        • Class ID<br>
                                        • House ID<br>
                                        • Password<br><br>
                                        
                                        <strong>Notes:</strong><br>
                                        • Password defaults to Student ID<br>
                                        • Use default values if not in file<br>
                                        • First row should be headers
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Points Modal -->
    <div class="modal fade" id="addPointsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add Points to Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPointsForm">
                        <div class="mb-3">
                            <label for="pointsStudentId" class="form-label">Student ID:</label>
                            <input type="text" class="form-control" id="pointsStudentId" name="pointsStudentId" placeholder="Enter or search student ID" required>
                            <div class="form-text">Start typing to search for students</div>
                            <div id="studentSuggestions" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="selectedStudentInfo" class="form-label">Selected Student:</label>
                            <div id="selectedStudentInfo" class="alert alert-info d-none">
                                <strong id="selectedStudentName"></strong><br>
                                <small id="selectedStudentDetails"></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pointsValue" class="form-label">Points:</label>
                            <input type="number" class="form-control" id="pointsValue" name="pointsValue" placeholder="Enter points (positive or negative)" step="0.1" required>
                            <div class="form-text">Enter positive points for rewards, negative for penalties</div>
                        </div>
                        <div class="mb-3">
                            <label for="pointsReason" class="form-label">Reason:</label>
                            <input type="text" class="form-control" id="pointsReasonManual" name="pointsReason" placeholder="e.g., Academic Excellence, Good Behavior" required>
                            <div class="form-text">Provide a clear reason for the points allocation</div>
                        </div>
                        <div class="mb-3">
                            <label for="pointsDate" class="form-label">Date:</label>
                            <input type="date" class="form-control" id="pointsDate" name="pointsDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addPointsToStudent()">Add Points</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="editStudentId" name="editStudentId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editStudentName" class="form-label">Student Name:</label>
                                    <input type="text" class="form-control" id="editStudentName" name="editStudentName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editStudentEmail" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="editStudentEmail" name="editStudentEmail" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editStudentBranch" class="form-label">Branch:</label>
                                    <select class="form-select" id="editStudentBranch" name="editStudentBranch" required>
                                        <option value="CSD">CSD</option>
                                        <option value="CSIT">CSIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editStudentSection" class="form-label">Section:</label>
                                    <select class="form-select" id="editStudentSection" name="editStudentSection" required>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editStudentClass" class="form-label">Class:</label>
                                    <select class="form-select" id="editStudentClass" name="editStudentClass" required>
                                        <!-- Classes will be populated here -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentHouse" class="form-label">House:</label>
                            <select class="form-select" id="editStudentHouse" name="editStudentHouse" required>
                                <?php
                                mysqli_data_seek($houses_result, 0);
                                while ($house = mysqli_fetch_assoc($houses_result)) {
                                    echo "<option value='{$house['hid']}'>{$house['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="resetPassword">
                                <label class="form-check-label" for="resetPassword">
                                    Reset password to Student ID
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateStudent()">Update Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createEventForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Event Title:</label>
                            <input type="text" class="form-control" id="eventTitle" name="eventTitle" placeholder="e.g., Sports Day 2024" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Description:</label>
                            <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3" placeholder="Brief description of the event"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventDate" class="form-label">Event Date:</label>
                                    <input type="date" class="form-control" id="eventDate" name="eventDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventType" class="form-label">Event Type:</label>
                                    <select class="form-select" id="eventType" name="eventType" required>
                                        <option value="">Select Type</option>
                                        <option value="Sports">Sports</option>
                                        <option value="Cultural">Cultural</option>
                                        <option value="Academic">Academic</option>
                                        <option value="Technical">Technical</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createEvent()">Create Event</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Participant Modal -->
    <div class="modal fade" id="addParticipantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add Participant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addParticipantForm">
                        <input type="hidden" id="participantEventId" name="participantEventId">
                        <div class="mb-3">
                            <label for="participantStudentId" class="form-label">Student ID:</label>
                            <input type="text" class="form-control" id="participantStudentId" name="participantStudentId" placeholder="Enter or search student ID" required>
                            <div class="form-text">Start typing to search for students</div>
                            <div id="participantSuggestions" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="selectedParticipantInfo" class="form-label">Selected Student:</label>
                            <div id="selectedParticipantInfo" class="alert alert-info d-none">
                                <strong id="selectedParticipantName"></strong><br>
                                <small id="selectedParticipantDetails"></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="participantPoints" class="form-label">Points:</label>
                            <input type="number" class="form-control" id="participantPoints" name="participantPoints" placeholder="Enter points (optional)" step="0.1" value="0">
                            <div class="form-text">Points can be awarded immediately or later</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addParticipant()">Add Participant</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Add Participants Modal -->
    <div class="modal fade" id="bulkParticipantsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload"></i> Bulk Add Participants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form id="bulkParticipantsForm" enctype="multipart/form-data">
                                <input type="hidden" id="bulkParticipantEventId" name="bulkParticipantEventId">
                                <div class="mb-3">
                                    <label for="participantsFile" class="form-label">Select Excel/CSV File:</label>
                                    <input type="file" class="form-control" id="participantsFile" name="participantsFile" accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">Upload Excel or CSV file with participant data.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="defaultParticipantPoints" class="form-label">Default Points:</label>
                                    <input type="number" class="form-control" id="defaultParticipantPoints" name="defaultParticipantPoints" placeholder="Default points for all participants" step="0.1" value="0">
                                    <div class="form-text">Points awarded to participants not specified in file.</div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="fas fa-upload"></i> Upload Participants
                                </button>
                            </form>
                            <div id="bulkParticipantsResult" class="mt-3"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-download"></i> Download Template</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="download_participants_template.php?format=csv" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-csv"></i> CSV Template
                                        </a>
                                        <a href="download_participants_template.php?format=excel" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-excel"></i> Excel Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6><i class="fas fa-info-circle"></i> File Format</h6>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <strong>Required Column:</strong><br>
                                        • Student ID<br><br>
                                        
                                        <strong>Optional Column:</strong><br>
                                        • Points<br><br>
                                        
                                        <strong>Notes:</strong><br>
                                        • One student ID per row<br>
                                        • Points default to value above<br>
                                        • First row should be headers
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Participant Points Modal -->
    <div class="modal fade" id="bulkParticipantPointsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-award"></i> Bulk Award Points to Participants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkParticipantPointsForm">
                        <input type="hidden" id="bulkPointsEventId" name="bulkPointsEventId">
                        <div class="mb-3">
                            <label for="bulkPointsValue" class="form-label">Points to Award:</label>
                            <input type="number" class="form-control" id="bulkPointsValue" name="bulkPointsValue" placeholder="Enter points for all participants" step="0.1" required>
                            <div class="form-text">This will be added to all current participants</div>
                        </div>
                        <div class="mb-3">
                            <label for="bulkPointsReason" class="form-label">Reason:</label>
                            <input type="text" class="form-control" id="bulkPointsReason" name="bulkPointsReason" placeholder="e.g., Participation, Winner, Runner-up" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="overwritePoints">
                                <label class="form-check-label" for="overwritePoints">
                                    Overwrite existing points
                                </label>
                                <div class="form-text">If checked, replaces current points. Otherwise, adds to existing points.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="bulkAwardPoints()">Award Points</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="super_admin_script.js"></script>
</body>
</html>