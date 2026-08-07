<?php
session_start();
include './connect.php';

// Check database connection
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Pagination settings
$events_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $events_per_page;

// Get total count of events
$count_query = "SELECT COUNT(*) as total FROM events";
$count_result = mysqli_query($conn, $count_query);
$total_events = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_events / $events_per_page);

// Fetch events
$events = [];
$query = "SELECT event_id, title, event_date, venue, description, image_path, start_time, end_time 
          FROM events 
          ORDER BY event_date DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./head.php"; ?>
    <title>House Events Overview - SRKR Engineering College</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --accent-blue: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --accent-amber: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --accent-rose: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
        }

        body {
            background: #f8fafc !important;
            font-family: 'Outfit', 'Poppins', sans-serif !important;
            color: #1e293b !important;
            min-height: 100vh;
        }

        /* Hero Header Section */
        .events-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31103f 100%);
            padding: 50px 0 70px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid rgba(139, 92, 246, 0.25);
            margin-bottom: -35px;
        }

        .events-hero::before {
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
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .hero-title i {
            color: #818cf8;
            animation: floatIcon 3s ease-in-out infinite;
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
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

        /* Stats Cards */
        .stat-card-fancy {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 20px 22px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
        }

        .stat-card-fancy::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-gradient, var(--primary-gradient));
            border-radius: 20px 20px 0 0;
        }

        .stat-card-fancy:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.12);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .stat-icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--icon-bg, rgba(79, 70, 229, 0.1));
            color: var(--icon-color, #4f46e5);
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .stat-card-fancy:hover .stat-icon-wrap {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-val-num {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            background: var(--card-gradient, var(--primary-gradient));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }

        .stat-lbl-name {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Filter Box */
        .filters-card-fancy {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            margin-bottom: 35px;
        }

        .filters-header-fancy {
            padding: 18px 26px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-header-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-header-title i {
            color: #4f46e5;
        }

        .btn-clear-fancy {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 7px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-clear-fancy:hover {
            background: #4f46e5;
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        .filter-label-fancy {
            font-size: 0.78rem;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .search-input-fancy, .filter-select-fancy {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            border-radius: 14px !important;
            padding: 12px 18px !important;
            font-weight: 500;
            transition: all 0.3s ease !important;
        }

        .search-input-fancy:focus, .filter-select-fancy:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
            outline: none;
        }

        .results-box-fancy {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
        }

        .results-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: #4f46e5;
        }

        /* Event Grid Cards */
        .events-grid-fancy {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 26px;
        }

        .event-card-fancy {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
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

        .event-card-fancy:hover {
            transform: translateY(-10px) scale(1.015);
            box-shadow: 0 25px 50px rgba(79, 70, 229, 0.18);
            border-color: #818cf8;
        }

        .event-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 22px 0 22px;
        }

        .date-badge-fancy {
            background: linear-gradient(135deg, #e0e7ff 0%, #eef2ff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 14px;
            padding: 8px 16px;
            text-align: center;
            min-width: 65px;
        }

        .date-month {
            font-size: 0.75rem;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .date-day {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-top: 2px;
        }

        .status-upcoming-fancy {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .status-completed-fancy {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .event-img-wrap {
            height: 180px;
            margin: 16px 22px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }

        .event-img-element {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-card-fancy:hover .event-img-element {
            transform: scale(1.08);
        }

        .event-img-fallback {
            height: 100%;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .event-img-fallback i {
            font-size: 2.8rem;
            color: #6366f1;
        }

        .event-card-body-fancy {
            padding: 0 22px 22px 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .event-title-fancy {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .meta-tag-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: #475569;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .meta-tag-row i {
            color: #6366f1;
        }

        .event-desc-fancy {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn-view-event {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            border: none;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .btn-view-event i {
            transition: transform 0.3s ease;
        }

        .event-card-fancy:hover .btn-view-event {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
            color: #ffffff;
        }

        .event-card-fancy:hover .btn-view-event i {
            transform: translateX(6px);
        }
    </style>
</head>
<body>
    <?php include "nav.php"; ?>

    <!-- Hero Header -->
    <div class="events-hero mb-5">
        <div class="full-width-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="hero-title"><i class="fas fa-calendar-alt"></i> House Events Overview</h1>
                    <p class="hero-subtitle">Discover, track, and explore all house events, tournaments, and student competitions.</p>
                </div>
                <div>
                    <a href="houses_dashboard.php" class="back-btn-pill">
                        <i class="fas fa-arrow-left"></i> Back to Houses Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Full Width Content -->
    <div class="main-content pb-5">
        <div class="full-width-container">

            <!-- Event Statistics Section -->
            <div class="stats-container mb-5">
                <?php
                $total_events_count = count($events);
                $today = date('Y-m-d');
                $upcoming_events = 0;
                foreach ($events as $ev) {
                    if ($ev['event_date'] >= $today) $upcoming_events++;
                }
                
                $participants_query = "SELECT COUNT(*) as total FROM participants";
                $participants_result = mysqli_query($conn, $participants_query);
                $total_participants = $participants_result ? mysqli_fetch_assoc($participants_result)['total'] : 0;
                
                $winners_query = "SELECT COUNT(*) as total FROM winners";
                $winners_result = mysqli_query($conn, $winners_query);
                $total_winners = $winners_result ? mysqli_fetch_assoc($winners_result)['total'] : 0;
                
                $organizers_query = "SELECT COUNT(*) as total FROM organizers";
                $organizers_result = mysqli_query($conn, $organizers_query);
                $total_organizers = $organizers_result ? mysqli_fetch_assoc($organizers_result)['total'] : 0;
                
                $stats = [
                    ['title' => 'Total Events', 'value' => $total_events_count, 'icon' => 'fas fa-calendar-alt', 'gradient' => 'var(--primary-gradient)', 'bg' => 'rgba(79, 70, 229, 0.1)', 'color' => '#4f46e5'],
                    ['title' => 'Upcoming Events', 'value' => $upcoming_events, 'icon' => 'fas fa-clock', 'gradient' => 'var(--accent-emerald)', 'bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
                    ['title' => 'Total Participants', 'value' => number_format($total_participants), 'icon' => 'fas fa-users', 'gradient' => 'var(--accent-blue)', 'bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6'],
                    ['title' => 'Total Winners', 'value' => number_format($total_winners), 'icon' => 'fas fa-trophy', 'gradient' => 'var(--accent-amber)', 'bg' => 'rgba(245, 158, 11, 0.1)', 'color' => '#f59e0b'],
                    ['title' => 'Total Organizers', 'value' => number_format($total_organizers), 'icon' => 'fas fa-cogs', 'gradient' => 'var(--accent-rose)', 'bg' => 'rgba(236, 72, 153, 0.1)', 'color' => '#ec4899']
                ];
                ?>
                
                <div class="row g-3">
                    <?php foreach ($stats as $stat): ?>
                        <div class="col-xl-2-4 col-lg-4 col-md-6">
                            <div class="stat-card-fancy" style="--card-gradient: <?php echo $stat['gradient']; ?>; --icon-bg: <?php echo $stat['bg']; ?>; --icon-color: <?php echo $stat['color']; ?>;">
                                <div class="stat-icon-wrap">
                                    <i class="<?php echo $stat['icon']; ?>"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-val-num"><?php echo $stat['value']; ?></div>
                                    <div class="stat-lbl-name"><?php echo $stat['title']; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-card-fancy">
                <div class="filters-header-fancy">
                    <h5 class="filter-header-title">
                        <i class="fas fa-filter"></i> Filter & Search Events
                    </h5>
                    <button class="btn-clear-fancy" onclick="clearAllFilters()">
                        <i class="fas fa-times me-1"></i> Clear Filters
                    </button>
                </div>
                
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <div>
                                <label class="filter-label-fancy">Search Events</label>
                                <input type="text" id="searchInput" class="search-input-fancy form-control" placeholder="Search by event name or description...">
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <label class="filter-label-fancy">Organizing House</label>
                                <select id="houseFilter" class="filter-select-fancy form-select">
                                    <option value="">All Houses</option>
                                    <option value="Aakash">Aakash House</option>
                                    <option value="Jal">Jal House</option>
                                    <option value="Vayu">Vayu House</option>
                                    <option value="PRUDHVI">PRUDHVI House</option>
                                    <option value="Agni">Agni House</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <label class="filter-label-fancy">Event Status</label>
                                <select id="statusFilter" class="filter-select-fancy form-select">
                                    <option value="">All Statuses</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-6">
                            <div>
                                <label class="filter-label-fancy">Results</label>
                                <div class="results-box-fancy">
                                    <span id="resultsCount" class="results-val">0 of <?php echo count($events); ?></span>
                                    <div class="text-muted small text-uppercase fw-bold">Events</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="events-grid-fancy" id="eventsGrid">
                <!-- Dynamically populated by JS -->
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="pagination-wrapper mt-5">
                <nav aria-label="Events pagination">
                    <ul class="pagination justify-content-center" id="paginationList">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>

    <script>
        const allEvents = <?php echo json_encode($events); ?>;
        let filteredEvents = [...allEvents];
        
        const eventsPerPage = 9;
        let currentPage = 1;
        let totalPages = 1;

        const searchInput = document.getElementById('searchInput');
        const houseFilter = document.getElementById('houseFilter');
        const statusFilter = document.getElementById('statusFilter');
        const eventsGrid = document.getElementById('eventsGrid');
        const resultsCount = document.getElementById('resultsCount');

        searchInput.addEventListener('input', applyFilters);
        houseFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedStatus = statusFilter.value;
            const today = new Date().toISOString().split('T')[0];

            filteredEvents = allEvents.filter(event => {
                const matchesSearch = !searchTerm || 
                    event.title.toLowerCase().includes(searchTerm) ||
                    (event.description && event.description.toLowerCase().includes(searchTerm));

                let matchesStatus = true;
                if (selectedStatus) {
                    const isUpcoming = event.event_date >= today;
                    if (selectedStatus === 'upcoming' && !isUpcoming) matchesStatus = false;
                    else if (selectedStatus === 'completed' && isUpcoming) matchesStatus = false;
                }

                return matchesSearch && matchesStatus;
            });

            currentPage = 1;
            updatePagination();
            renderEvents();
            updateResultsCount();
        }

        function renderEvents() {
            if (filteredEvents.length === 0) {
                eventsGrid.innerHTML = `
                    <div class="text-center py-5" style="grid-column: 1 / -1;">
                        <i class="fas fa-search" style="font-size: 3.5rem; color: #6366f1; margin-bottom: 15px;"></i>
                        <h4 class="fw-bold" style="color: #0f172a;">No Matching Events Found</h4>
                        <p class="text-muted">Try clearing search parameters or status filters</p>
                    </div>
                `;
                document.getElementById('paginationContainer').style.display = 'none';
                return;
            }

            const startIndex = (currentPage - 1) * eventsPerPage;
            const endIndex = startIndex + eventsPerPage;
            const paginatedEvents = filteredEvents.slice(startIndex, endIndex);

            eventsGrid.innerHTML = paginatedEvents.map((event, index) => {
                const eventDate = new Date(event.event_date);
                const today = new Date();
                today.setHours(0,0,0,0);
                const isUpcoming = eventDate >= today;
                
                const month = eventDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
                const day = eventDate.getDate().toString().padStart(2, '0');
                
                const statusBadge = isUpcoming 
                    ? '<span class="status-upcoming-fancy"><i class="fas fa-clock me-1"></i> Upcoming</span>'
                    : '<span class="status-completed-fancy"><i class="fas fa-check-circle me-1"></i> Completed</span>';

                const description = event.description || '';

                const imageHtml = event.image_path 
                    ? `<img src="${event.image_path}" alt="${event.title}" class="event-img-element">`
                    : `<div class="event-img-fallback">
                         <i class="fas fa-calendar-alt"></i>
                       </div>`;

                return `
                    <div class="event-card-fancy" style="animation-delay: ${index * 0.05}s;" onclick="window.location.href='event_detail.php?event_id=${event.event_id}'">
                        <div class="event-card-top">
                            <div class="date-badge-fancy">
                                <div class="date-month">${month}</div>
                                <div class="date-day">${day}</div>
                            </div>
                            <div>${statusBadge}</div>
                        </div>

                        <div class="event-img-wrap">
                            ${imageHtml}
                        </div>
                        
                        <div class="event-card-body-fancy">
                            <h3 class="event-title-fancy">${event.title}</h3>
                            
                            ${event.venue ? `
                            <div class="meta-tag-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>${event.venue}</span>
                            </div>
                            ` : ''}
                            
                            <p class="event-desc-fancy">${description}</p>
                            
                            <button class="btn-view-event">
                                <span>View Event Details</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('paginationContainer').style.display = filteredEvents.length > eventsPerPage ? 'flex' : 'none';
        }

        function updatePagination() {
            totalPages = Math.ceil(filteredEvents.length / eventsPerPage);
            const paginationList = document.getElementById('paginationList');
            
            if (totalPages <= 1) {
                paginationList.innerHTML = '';
                return;
            }

            let html = '';
            if (currentPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;"><i class="fas fa-chevron-left"></i></a></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a></li>`;
            }

            if (currentPage < totalPages) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;"><i class="fas fa-chevron-right"></i></a></li>`;
            }

            paginationList.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            renderEvents();
            updatePagination();
            updateResultsCount();
        }

        function updateResultsCount() {
            const start = filteredEvents.length === 0 ? 0 : (currentPage - 1) * eventsPerPage + 1;
            const end = Math.min(currentPage * eventsPerPage, filteredEvents.length);
            resultsCount.textContent = `${start}-${end} of ${filteredEvents.length}`;
        }

        function clearAllFilters() {
            searchInput.value = '';
            houseFilter.value = '';
            statusFilter.value = '';
            applyFilters();
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyFilters();
        });
    </script>
</body>
</html>