<?php
session_start();
include './connect.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

$event_id = $_GET['event_id'] ?? null;
$success_message = '';
$error_message = '';

if (!$event_id) {
    header('Location: events_overview.php');
    exit();
}

// Handle event registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_event'])) {
    if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
        $error_message = 'You must be logged in as a student to register for events.';
    } else {
        $student_id = $_SESSION['student_id'];
        
        $event_check_query = "SELECT event_date, start_time FROM events WHERE event_id = ?";
        $event_check_stmt = mysqli_prepare($conn, $event_check_query);
        mysqli_stmt_bind_param($event_check_stmt, "i", $event_id);
        mysqli_stmt_execute($event_check_stmt);
        $event_check_result = mysqli_stmt_get_result($event_check_stmt);
        $event_info = mysqli_fetch_assoc($event_check_result);
        
        if (!$event_info) {
            $error_message = 'Event not found.';
        } else {
            $event_datetime = $event_info['event_date'] . ' ' . $event_info['start_time'];
            $current_datetime = date('Y-m-d H:i:s');
            
            if (strtotime($event_datetime) <= strtotime($current_datetime)) {
                $error_message = 'Registration is closed. This event has already started or ended.';
            } else {
                $check_query = "SELECT registered_id FROM registrations WHERE student_id = ? AND event_id = ?";
                $check_stmt = mysqli_prepare($conn, $check_query);
                mysqli_stmt_bind_param($check_stmt, "si", $student_id, $event_id);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
                
                if (mysqli_num_rows($check_result) > 0) {
                    $error_message = 'You are already registered for this event.';
                } else {
                    $register_query = "INSERT INTO registrations (student_id, event_id, status, registered_at) VALUES (?, ?, 'confirmed', NOW())";
                    $register_stmt = mysqli_prepare($conn, $register_query);
                    mysqli_stmt_bind_param($register_stmt, "si", $student_id, $event_id);
                    
                    if (mysqli_stmt_execute($register_stmt)) {
                        $success_message = 'Successfully registered for the event! You will receive participation points after attending.';
                    } else {
                        $error_message = 'Error registering for the event. Please try again.';
                    }
                }
            }
        }
    }
}

$event_data = null;
$feedback_data = [];
$organizers_data = [];
$participants_data = [];
$winners_data = [];

// Fetch main event details
$event_query = "SELECT * FROM events WHERE event_id = ?";
$stmt = mysqli_prepare($conn, $event_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$event_result = mysqli_stmt_get_result($stmt);
$event_data = mysqli_fetch_assoc($event_result);

if (!$event_data) {
    echo "<div class=\"alert alert-danger\">Event not found.</div>";
    include "./footer.php";
    exit();
}

// Fetch feedback
$feedback_query = "SELECT ef.*, s.name as student_name FROM event_feedback ef JOIN students s ON ef.student_id = s.student_id WHERE ef.event_id = ? ORDER BY ef.created_at DESC";
$stmt = mysqli_prepare($conn, $feedback_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$feedback_result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($feedback_result)) {
    $feedback_data[] = $row;
}

// Fetch organizers
$organizers_query = "SELECT o.*, s.name as student_name FROM organizers o JOIN students s ON o.student_id = s.student_id WHERE o.event_id = ? ORDER BY s.name ASC";
$stmt = mysqli_prepare($conn, $organizers_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$organizers_result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($organizers_result)) {
    $organizers_data[] = $row;
}

// Fetch participants
$participants_query = "SELECT p.*, s.name as student_name FROM participants p JOIN students s ON p.student_id = s.student_id WHERE p.event_id = ? ORDER BY s.name ASC";
$stmt = mysqli_prepare($conn, $participants_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$participants_result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($participants_result)) {
    $participants_data[] = $row;
}

// Fetch winners
$winners_query = "SELECT w.*, s.name as student_name FROM winners w JOIN students s ON w.student_id = s.student_id WHERE w.event_id = ? ORDER BY w.position ASC";
$stmt = mysqli_prepare($conn, $winners_query);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$winners_result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($winners_result)) {
    $winners_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title><?php echo htmlspecialchars($event_data['title']); ?> Details - SRKR Engineering College</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --accent-blue: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --accent-amber: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        body {
            background: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Header Section */
        .event-detail-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 50px 0 70px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(139, 92, 246, 0.25);
            margin-bottom: -35px;
        }

        .event-detail-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99, 102, 241, 0.25) 0%, transparent 60%),
                        radial-gradient(circle at 70% 60%, rgba(236, 72, 153, 0.2) 0%, transparent 55%);
            animation: pulseGlow 8s infinite alternate ease-in-out;
            pointer-events: none;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            100% { transform: scale(1.1) rotate(3deg); opacity: 1; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .hero-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0;
        }

        .back-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 50px !important;
            padding: 10px 24px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            text-decoration: none !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease !important;
        }

        .back-btn-pill:hover {
            background: #ffffff !important;
            color: #0f172a !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.25) !important;
        }

        .full-width-container {
            width: 100%;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        @media (max-width: 768px) {
            .full-width-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .hero-title {
                font-size: 2rem;
            }
        }

        /* Container Cards */
        .card-custom {
            background: #ffffff !important;
            border-radius: 22px !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
            overflow: hidden;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 20px 28px;
            color: #0f172a !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-card-header i {
            color: #4f46e5 !important;
        }

        .event-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .info-item-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            transition: all 0.3s ease;
        }

        .info-item-card:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
            transform: translateY(-3px);
        }

        .info-label {
            font-weight: 800;
            color: #4f46e5;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-value {
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .btn-register-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 12px 30px !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3) !important;
            transition: all 0.3s ease !important;
            cursor: pointer;
            text-decoration: none !important;
        }

        .btn-register-pill:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%) !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.45) !important;
            color: #ffffff !important;
        }

        /* Modern Tables */
        .table-responsive {
            background: transparent !important;
            border: none !important;
        }

        .table {
            background: transparent !important;
            color: #1e293b !important;
            margin: 0;
        }

        .table th {
            background: #f1f5f9 !important;
            color: #4f46e5 !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 16px 16px !important;
        }

        .table td {
            background: transparent !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #1e293b !important;
            padding: 16px !important;
            vertical-align: middle !important;
            font-weight: 500;
        }

        .table tbody tr {
            transition: all 0.3s ease !important;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.04) !important;
        }

        .pos-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            color: white;
        }

        .pos-1 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
        .pos-2 { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3); }
        .pos-3 { background: linear-gradient(135deg, #b45309 0%, #78350f 100%); box-shadow: 0 4px 12px rgba(180, 83, 9, 0.3); }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Header -->
    <div class="event-detail-hero mb-5">
        <div class="full-width-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="hero-title"><?php echo htmlspecialchars($event_data['title']); ?></h1>
                    <p class="hero-subtitle">Event Specifications, House Rewards & Participation Dashboard</p>
                </div>
                <div>
                    <a href="events_overview.php" class="back-btn-pill">
                        <i class="fas fa-arrow-left"></i> Back to Events Overview
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">

            <!-- Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 16px; background: #f0fdf4; border: 1.5px solid #86efac;">
                    <i class="fas fa-check-circle text-success me-2 fs-5"></i> <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 16px; background: #fef2f2; border: 1.5px solid #fca5a5;">
                    <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i> <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Event Main Details Card -->
            <div class="card-custom mb-5">
                <div class="detail-card-header">
                    <i class="fas fa-info-circle"></i> Event Specifications & Information
                </div>
                <div class="p-4 p-md-5">
                    <div class="event-info-grid mb-4">
                        <div class="info-item-card" style="grid-column: 1 / -1;">
                            <div class="info-label"><i class="fas fa-align-left"></i> Description</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($event_data['description'])); ?></div>
                        </div>
                        <div class="info-item-card">
                            <div class="info-label"><i class="fas fa-calendar-day"></i> Event Date</div>
                            <div class="info-value"><?php echo date('l, F j, Y', strtotime($event_data['event_date'])); ?></div>
                        </div>
                        <div class="info-item-card">
                            <div class="info-label"><i class="fas fa-clock"></i> Time Schedule</div>
                            <div class="info-value"><?php echo date('g:i A', strtotime($event_data['start_time'])); ?> - <?php echo date('g:i A', strtotime($event_data['end_time'])); ?></div>
                        </div>
                        <div class="info-item-card">
                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Venue Location</div>
                            <div class="info-value"><?php echo htmlspecialchars($event_data['venue']); ?></div>
                        </div>
                        <div class="info-item-card">
                            <div class="info-label"><i class="fas fa-users"></i> Registered Participants</div>
                            <div class="info-value">
                                <?php 
                                $count_query = "SELECT COUNT(*) as total FROM registrations WHERE event_id = ? AND status != 'cancelled'";
                                $count_stmt = mysqli_prepare($conn, $count_query);
                                mysqli_stmt_bind_param($count_stmt, "i", $event_id);
                                mysqli_stmt_execute($count_stmt);
                                $count_result = mysqli_stmt_get_result($count_stmt);
                                $registration_count = mysqli_fetch_assoc($count_result)['total'];
                                echo $registration_count . " student" . ($registration_count != 1 ? "s" : "");
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Registration Action Area -->
                    <div class="mt-4 pt-4 border-top">
                        <?php
                        $is_student_logged_in = isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'];
                        $is_registered = false;
                        $registration_status = '';
                        $is_registration_open = true;
                        
                        $event_datetime = $event_data['event_date'] . ' ' . $event_data['start_time'];
                        $current_datetime = date('Y-m-d H:i:s');
                        if (strtotime($event_datetime) <= strtotime($current_datetime)) {
                            $is_registration_open = false;
                        }
                        
                        if ($is_student_logged_in) {
                            $student_id = $_SESSION['student_id'];
                            $check_query = "SELECT status FROM registrations WHERE student_id = ? AND event_id = ?";
                            $check_stmt = mysqli_prepare($conn, $check_query);
                            mysqli_stmt_bind_param($check_stmt, "si", $student_id, $event_id);
                            mysqli_stmt_execute($check_stmt);
                            $check_result = mysqli_stmt_get_result($check_stmt);
                            
                            if ($row = mysqli_fetch_assoc($check_result)) {
                                $is_registered = true;
                                $registration_status = $row['status'];
                            }
                        }
                        ?>
                        
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="registration-info">
                                <?php if (!$is_student_logged_in): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2 text-primary fs-5"></i>
                                        <span class="fw-bold" style="color: #475569;">Log in as a student to register for this event.</span>
                                    </div>
                                <?php elseif ($is_registered): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                                        <span class="text-success fw-bold">You are registered for this event!</span>
                                        <span class="badge bg-success ms-2 px-3 py-2 rounded-pill">
                                            <?php echo ucfirst($registration_status); ?>
                                        </span>
                                    </div>
                                <?php elseif (!$is_registration_open): ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-danger me-2 fs-5"></i>
                                        <span class="text-danger fw-bold">Registration Closed — Event has already started or ended.</span>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-plus me-2 text-primary fs-5"></i>
                                        <span class="fw-bold" style="color: #0f172a;">Registration is Open! Ready to participate?</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="registration-action">
                                <?php if (!$is_student_logged_in && $is_registration_open): ?>
                                    <a href="login.php" class="btn-register-pill">
                                        <i class="fas fa-sign-in-alt"></i> Login to Register
                                    </a>
                                <?php elseif ($is_student_logged_in && !$is_registered && $is_registration_open): ?>
                                    <form method="POST">
                                        <button type="submit" name="register_event" class="btn-register-pill">
                                            <i class="fas fa-check-circle"></i> Register Now
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizers & Winners Section -->
            <div class="card-custom">
                <div class="detail-card-header">
                    <i class="fas fa-trophy"></i> Event Winners & Organizing Team
                </div>
                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <!-- Winners -->
                        <div class="col-lg-6">
                            <h5 class="fw-bold mb-3" style="color: #0f172a; font-family: 'Outfit', sans-serif;">
                                <i class="fas fa-trophy text-warning me-2"></i> Declared Winners
                            </h5>
                            <?php if (!empty($winners_data)): ?>
                                <div class="table-responsive rounded-4 border">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Student Name</th>
                                                <th>Points Awarded</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($winners_data as $w): ?>
                                                <?php 
                                                $pos_class = 'pos-1';
                                                if ($w['position'] == 2) $pos_class = 'pos-2';
                                                if ($w['position'] == 3) $pos_class = 'pos-3';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="pos-badge <?php echo $pos_class; ?>">
                                                            #<?php echo $w['position']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($w['student_name']); ?></td>
                                                    <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">+<?php echo $w['points']; ?> pts</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 rounded-4 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <p class="text-muted mb-0">No winners declared yet for this event.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Organizers -->
                        <div class="col-lg-6">
                            <h5 class="fw-bold mb-3" style="color: #0f172a; font-family: 'Outfit', sans-serif;">
                                <i class="fas fa-cogs text-indigo me-2" style="color: #6366f1;"></i> Event Organizers
                            </h5>
                            <?php if (!empty($organizers_data)): ?>
                                <div class="table-responsive rounded-4 border">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Role</th>
                                                <th>Student Name</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($organizers_data as $o): ?>
                                                <tr>
                                                    <td><span class="badge bg-primary px-3 py-2 rounded-pill"><?php echo htmlspecialchars($o['role']); ?></span></td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($o['student_name']); ?></td>
                                                    <td><span class="badge bg-info px-3 py-2 rounded-pill">+<?php echo $o['points']; ?> pts</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 rounded-4 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <p class="text-muted mb-0">No organizers assigned to this event.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
</body>
</html>