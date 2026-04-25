<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if session expired
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $_SESSION['expire_time'])) {
    session_unset();
    session_destroy();
    header("Location: login.php?session_expired=true");
    exit();
}

$_SESSION['last_activity'] = time(); // Reset session timer
require '../utils/connect.php';
$toastMessage = '';
$toastType = '';
$username = $_SESSION['username'];

// Fetch aid and hid from admins table
$query = "SELECT admin_id, hid FROM admins WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $aid = $row['admin_id'];
    $house_id = $row['hid'];
} else {
    $toastMessage = "Admin not found.";
    $toastType = "error";
    echo "<script>alert('$toastMessage');</script>";
    exit();
}

// Fetch events for the admin's house
$query = "SELECT * FROM events WHERE hid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $house_id);
$stmt->execute();
$events_result = $stmt->get_result();

// Fetch registrations from participants table for the events
$events = [];  // Initialize the events array

// Fetch events
while ($row = $events_result->fetch_assoc()) {
    $eid = $row['event_id'];

    // Fetch organizers from the new organizers table
    $organizers_query = "SELECT s.student_id as username, s.name 
                        FROM organizers o
                        JOIN students s ON o.student_id = s.student_id
                        WHERE o.event_id = ?";
    $org_stmt = $conn->prepare($organizers_query);
    $org_stmt->bind_param("i", $eid);
    $org_stmt->execute();
    $org_result = $org_stmt->get_result();
    
    $organisers_details = [];
    while ($org_row = $org_result->fetch_assoc()) {
        $organisers_details[] = [
            'username' => $org_row['username'],
            'name' => $org_row['name']
        ];
    }

    // Prepare and execute the query to fetch registrations for the current event
    $registrations_query = "SELECT student_id FROM registrations WHERE event_id = ? AND status != 'cancelled'";
    $reg_stmt = $conn->prepare($registrations_query);
    $reg_stmt->bind_param("i", $eid);
    $reg_stmt->execute();
    $reg_result = $reg_stmt->get_result();

    $registrants = [];

    // Fetch registration details
    while ($reg_row = $reg_result->fetch_assoc()) {
        $student_id = $reg_row['student_id'];

        // Fetch student details for each registrant
        $student_query = "SELECT name, student_id FROM students WHERE student_id = ?";
        $student_stmt = $conn->prepare($student_query);
        $student_stmt->bind_param("s", $student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();

        if ($student_result->num_rows > 0) {
            $student_row = $student_result->fetch_assoc();

            // Add the registrant to the registrants list
            $registrants[] = [
                'name' => $student_row['name'],
                'registration_number' => $student_row['student_id']
            ];
        }
    }

    // Add the event details along with the list of participants and organizers
    $events[] = array_merge($row, ['registrationsList' => $registrants, 'organisers' => $organisers_details]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Dashboard</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Notyf for notifications -->
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/events/style.css">
</head>
<body>
    <?php include '../utils/sidenavbar.php'; ?>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-0">
                    <i class="bi bi-calendar-event text-primary"></i> Events Dashboard
                </h2>
                <p class="text-muted">Comprehensive overview of upcoming and past events</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" onclick="exportAllRegistrations()">
                    <i class="bi bi-download"></i> Export All Registrations
                </button>
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search event name">
                </div>
            </div>
            <div class="col-md-2">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="past">Past</option>
                </select>
            </div>
        </div>
        <BR>

        <div class="row" id="eventsGrid">
            <!-- Dynamic events will be populated here -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
         const events = <?php echo json_encode($events); ?>;
         
         // Export all registrations to Excel
         function exportAllRegistrations() {
             if (events.length === 0) {
                 alert('No events data available to export.');
                 return;
             }
             
             const workbook = XLSX.utils.book_new();
             const allRegistrations = [];
             let totalRegistrations = 0;
             
             events.forEach(event => {
                 if (event.registrationsList && event.registrationsList.length > 0) {
                     event.registrationsList.forEach(student => {
                         allRegistrations.push([
                             event.title,
                             new Date(event.event_date).toLocaleDateString(),
                             event.start_time + ' - ' + event.end_time,
                             event.venue,
                             student.name,
                             student.registration_number
                         ]);
                         totalRegistrations++;
                     });
                 }
             });
             
             if (allRegistrations.length === 0) {
                 alert('No registration data found to export.');
                 return;
             }
             
             const summaryData = [
                 ['Events Registration Report'],
                 ['Generated on:', new Date().toLocaleDateString()],
                 ['Total Events:', events.length],
                 ['Total Registrations:', totalRegistrations],
                 [''],
                 ['Event Title', 'Event Date', 'Event Time', 'Venue', 'Student Name', 'Student ID'],
                 ...allRegistrations
             ];
             
             const ws = XLSX.utils.aoa_to_sheet(summaryData);
             ws['!cols'] = [{wch: 30}, {wch: 15}, {wch: 20}, {wch: 25}, {wch: 25}, {wch: 15}];
             XLSX.utils.book_append_sheet(workbook, ws, 'All Registrations');
             
             const fileName = 'Events_Registrations_' + new Date().toISOString().split('T')[0] + '.xlsx';
             XLSX.writeFile(workbook, fileName);
             alert('Registration data exported successfully! File: ' + fileName);
         }
         
         // Export single event registrations  
         function exportEventRegistrations(eventId) {
             const event = events.find(e => e.event_id == eventId);
             
             if (!event || !event.registrationsList || event.registrationsList.length === 0) {
                 alert('No registrations found for this event.');
                 return;
             }
             
             const workbook = XLSX.utils.book_new();
             const eventData = [
                 [event.title + ' - Registrations'],
                 ['Event Date:', new Date(event.event_date).toLocaleDateString()],
                 ['Time:', event.start_time + ' - ' + event.end_time],
                 ['Venue:', event.venue],
                 ['Total Registrations:', event.registrationsList.length],
                 [''],
                 ['S.No', 'Student Name', 'Student ID']
             ];
             
             event.registrationsList.forEach((student, index) => {
                 eventData.push([index + 1, student.name, student.registration_number]);
             });
             
             const ws = XLSX.utils.aoa_to_sheet(eventData);
             ws['!cols'] = [{wch: 8}, {wch: 30}, {wch: 15}];
             XLSX.utils.book_append_sheet(workbook, ws, 'Registrations');
             
             const fileName = event.title.replace(/[^a-zA-Z0-9]/g, '_') + '_Registrations.xlsx';
             XLSX.writeFile(workbook, fileName);
             alert(event.title + ' registration data exported successfully!');
         }
    </script>
    <script src="../js/events/script.js"></script>
    
</body>
</html>