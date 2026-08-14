<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php";
?>
<link rel="stylesheet" href="./index.css">
<link rel="stylesheet" href="./premium-hero.css">
<link rel="stylesheet" href="./assets/css/depth-carousel.css">
<link rel="stylesheet" href="./assets/css/scroll-stack.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<style>
    /* Ensure consistent font family and body styling with nav.php */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #1a0d06;
        background: #fdfbf7;
        padding-top: 80px;
        margin-top: 0px;
    }


    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .hero-bg-effects {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
    }


    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 600px;
    }

    .hero-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .hero-logo img {
        height: 40px;
        width: auto;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.1;
        margin-bottom: 30px;
        color: #1e293b;
    }

    .hero-title .highlight {
        color: var(--house-aakash);
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .houses-section {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 50px 20px;
    }

    .houses-container {
        position: relative;
        width: 500px;
        height: 500px;
    }

    .houses-orbit-ring {
        position: absolute;
        inset: 0;
        width: 500px;
        height: 500px;
        animation: houseOrbitRotate 20s linear infinite;
        transform-origin: 250px 250px;
    }

    .houses-container:hover .houses-orbit-ring,
    .houses-container:hover .house-item {
        animation-play-state: paused;
    }

    @keyframes houseOrbitRotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes houseItemCounterRotate {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(-360deg);
        }
    }

    .houses-circle {
        position: absolute;
        inset: 0;
        border: 1px dashed rgba(124, 58, 237, 0.3);
        border-radius: 50%;
        pointer-events: none;
    }

    .center-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 20;
        cursor: pointer;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .center-logo:hover {
        transform: translate(-50%, -50%) scale(1.12);
        filter: drop-shadow(0 0 15px rgba(217, 119, 6, 0.6));
    }

    .center-logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #374151;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s ease;
    }

    .center-logo:hover img {
        border-color: #d97706;
    }

    .house-item {
        position: absolute;
        transform: translate(-50%, -50%);
        z-index: 50;
        transition: all 0.3s ease;
        animation: houseItemCounterRotate 20s linear infinite;
        transform-origin: center center;
        text-decoration: none !important;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        pointer-events: auto;
    }

    .house-button {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .house-button:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .house-button img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .house-name {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 10px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        color: #374151;
    }

    .connecting-lines {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 1;
    }

    .line {
        position: absolute;
        top: 50%;
        left: 50%;
        height: 2px;
        background: linear-gradient(to right, rgba(107, 114, 128, 0.3), transparent);
        transform-origin: 0 50%;
        opacity: 0.6;
        pointer-events: none;
    }

    /* House positioning - Perfect 72° spacing (Pentagon Circle Orbit) */
    /* Center at 250px, 250px with radius of 180px */
    .house-agni {
        top: 70px;
        left: 250px;
    }

    /* 0° - Top */
    .house-vayu {
        top: 194px;
        left: 421px;
    }

    /* 72° - Top Right */
    .house-prudhvi {
        top: 396px;
        left: 356px;
    }

    /* 144° - Bottom Right */
    .house-jal {
        top: 396px;
        left: 144px;
    }

    /* 216° - Bottom Left */
    .house-aakash {
        top: 194px;
        left: 79px;
    }

    /* 288° - Top Left */

    .house-agni .house-button {
        background-color: var(--house-agni);
    }

    .house-vayu .house-button {
        background-color: var(--house-vayu);
    }

    .house-prudhvi .house-button {
        background-color: var(--house-prudhvi);
    }

    .house-jal .house-button {
        background-color: var(--house-jal);
    }

    .house-aakash .house-button {
        background-color: var(--house-aakash);
    }

    /* Hover effects for better visual feedback */
    .houses-container:hover .line {
        opacity: 1;
        background: linear-gradient(to right, rgba(107, 114, 128, 0.6), transparent);
    }

    .house-item:hover {
        z-index: 20;
    }

    .house-item:hover .house-name {
        color: #1e293b;
        font-weight: 700;
    }

    .section-title {
        text-align: center;
        margin: 60px 0 40px;
    }

    .section-title h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #1e293b;
    }

    .section-title p {
        color: #64748b;
        max-width: 600px;
        margin: 0 auto;
    }

    .leaderboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 60px;
    }

    .house-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-top: 4px solid;
    }

    .house-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .house-card.rank-1 {
        border-top-color: #ffd700;
    }

    .house-card.rank-2 {
        border-top-color: #c0c0c0;
    }

    .house-card.rank-3 {
        border-top-color: #cd7f32;
    }

    .house-card.rank-4 {
        border-top-color: #64748b;
    }

    .house-card.rank-5 {
        border-top-color: #94a3b8;
    }

    .house-card h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #1e293b;
    }

    .house-card .points {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .house-card .rank {
        color: #64748b;
        font-size: 0.9rem;
    }

    .stats-section {
        background: rgba(0, 0, 0, 0.02);
        padding: 60px 0;
        margin: 60px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 24px;
    }

    .stat-icon.blue {
        background: #dbeafe;
        color: #3b82f6;
    }

    .stat-icon.red {
        background: #fee2e2;
        color: #ef4444;
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #22c55e;
    }

    .stat-content h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #1e293b;
    }

    .stat-content p {
        color: #64748b;
        font-size: 0.9rem;
    }

    .contributors-section {
        padding: 60px 0;
    }

    .contributors-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .contributors-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .contributors-table th {
        background: #f8fafc;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .contributors-table td {
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
    }

    .contributors-table tr:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .house-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
    }

    .house-badge.aakash {
        background: var(--house-aakash);
    }

    .house-badge.agni {
        background: var(--house-agni);
    }

    .house-badge.vayu {
        background: var(--house-vayu);
    }

    .house-badge.jal {
        background: var(--house-jal);
    }

    .house-badge.prudhvi {
        background: var(--house-prudhvi);
    }

    .view-more-btn {
        text-align: center;
        padding: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .view-more-btn button {
        background: #f3f4f6;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #374151;
    }

    .view-more-btn button:hover {
        background: #e5e7eb;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes center-spin {
        from {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    @keyframes orbit {
        from {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    @keyframes icon-spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-section {
            flex-direction: column;
            text-align: center;
        }

        .houses-container {
            width: 400px;
            height: 400px;
        }

        .hero-title {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .houses-container {
            width: 350px;
            height: 350px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-buttons {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .houses-container {
            width: 300px;
            height: 300px;
        }

        .house-button {
            width: 60px;
            height: 60px;
        }

        .house-button img {
            width: 45px;
            height: 45px;
        }

        .contributors-table {
            overflow-x: auto;
        }

        /* Hover effects for combined overview section */
        .overview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .hod-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(102, 126, 234, 0.4) !important;
        }

        /* Modern highlight cards */
        .highlight-card {
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .highlight-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .highlight-card.large-card:hover {
            transform: translateY(-10px) scale(1.02);
        }

        .highlight-card.featured:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(30, 41, 59, 0.4) !important;
        }

        .bottom-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(34, 197, 94, 0.3) !important;
        }

        /* Responsive grid */
        @media (max-width: 768px) {
            .highlights-masonry {
                grid-template-columns: 1fr !important;
            }

            .bottom-feature {
                grid-template-columns: 1fr !important;
                text-align: center;
            }
        }
    }

    /* Mobile CSS - Hide houses section completely and remove all spacing */
    @media (max-width: 768px) {
        .houses-section {
            display: none !important;
            padding: 0 !important;
            margin: 0 !important;
            height: 0 !important;
        }
        
        /* Also hide any nested houses content and remove spacing */
        section.houses-section,
        .houses-container,
        .house-item,
        .house-button {
            display: none !important;
            padding: 0 !important;
            margin: 0 !important;
            height: 0 !important;
        }

        /* Remove spacing from the parent hero section containing houses */
        .hero-section:has(.houses-section) {
            padding: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            min-height: auto !important;
        }

        /* Target the specific hero section with houses by its inline styles */
        section[style*="padding: 150px"],
        section[style*="margin-top: -180px"] {
        padding: 0 !important;
            margin: 0 !important;
            height: 0 !important;
        }
    }

    /* ========================================== */
    /* SPLIT SCROLL REVEAL SECTION STYLES        */
    /* ========================================== */
    .reveal-showcase-section {
        position: relative;
        background: transparent; /* seamless integration */
        height: 260vh; /* scrollable distance */
        overflow: visible;
        margin: 0;
        padding: 0;
        position: relative;
    }

    /* Pillar Navigation Tabs Header */
    .pillar-tabs-container {
        position: sticky;
        top: 85px;
        z-index: 100;
        text-align: center;
        padding: 15px 0;
        background: transparent;
        pointer-events: auto;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform: translate3d(0, 0, 0);
        will-change: transform;
    }

    .pillar-tabs-pill {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 9999px;
        background: #ffffff !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(217, 119, 6, 0.25);
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform: translate3d(0, 0, 0);
    }

    .btn-pillar-tab {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 8px 18px;
        border-radius: 9999px;
        color: #475569;
        border: none;
        background: transparent;
        transition: all 0.25s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-pillar-tab:hover {
        color: #d97706;
        background: rgba(217, 119, 6, 0.1);
    }

    .btn-pillar-tab.active {
        color: #ffffff !important;
        background: #d97706 !important;
        box-shadow: 0 4px 15px rgba(217, 119, 6, 0.35);
    }

    /* Desktop View Layout */
    .showcase-desktop-view {
        display: block;
        width: 100%;
        height: 100%;
    }

    .showcase-desktop-view .container {
        height: 100%;
    }

    .showcase-desktop-view .row {
        height: 100%;
    }

    /* Left Column: Narrative Content */
    .showcase-text-col {
        position: relative;
        height: 100%;
    }

    /* Sticky text container */
    .showcase-sticky-text-wrapper {
        position: sticky;
        top: 20vh;
        height: 60vh;
        width: 100%;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    /* Scroll items stacked absolute in same spot */
    .showcase-scroll-item {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0;
        opacity: 0;
        transform: translateX(-150px); /* entering from the left */
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    /* Active state centered */
    .showcase-scroll-item.active {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
        z-index: 5;
    }

    /* Exiting state: offset to the right */
    .showcase-scroll-item.exit {
        opacity: 0;
        transform: translateX(150px);
        pointer-events: none;
        z-index: 1;
    }

    .showcase-card-badge {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #2563eb;
        letter-spacing: 2px;
        margin-bottom: 12px;
    }

    .showcase-card-title {
        font-size: 2.1rem;
        font-weight: 800;
        margin-bottom: 16px;
        color: #0f172a;
        line-height: 1.25;
    }

    .showcase-card-text {
        font-size: 0.95rem;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .showcase-card-stats {
        display: flex;
        gap: 24px;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding-top: 20px;
    }

    .showcase-stat-box {
        flex: 1;
    }

    .showcase-stat-val {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
    }

    .showcase-stat-lbl {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
    }

    /* Right Column: Sticky Graphic Viewport */
    .showcase-graphic-col {
        position: relative;
        height: 100%;
    }

    .showcase-sticky-graphic {
        position: sticky;
        top: 20vh;
        height: 60vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Soft ambient blobs that change color */
    .showcase-ambient-glow {
        position: absolute;
        top: 20%;
        left: 20%;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.12;
        transition: background 1.2s ease, transform 1.2s ease;
        z-index: 1;
        pointer-events: none;
    }

    .showcase-sticky-graphic.step-0 .showcase-ambient-glow {
        background: #10b981;
        transform: translate(0, 0) scale(1);
    }
    .showcase-sticky-graphic.step-1 .showcase-ambient-glow {
        background: #f97316;
        transform: translate(-40px, 40px) scale(1.1);
    }
    .showcase-sticky-graphic.step-2 .showcase-ambient-glow {
        background: #3b82f6;
        transform: translate(40px, -40px) scale(0.95);
    }
    .showcase-sticky-graphic.step-3 .showcase-ambient-glow {
        background: #8b5cf6;
        transform: translate(0, 40px) scale(1.05);
    }

    .showcase-graphic-item {
        position: absolute;
        width: 100%;
        max-width: 440px;
        height: 390px;
        opacity: 0;
        transform: scale(0.92) translateY(20px);
        filter: blur(10px);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), filter 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .showcase-graphic-item.active {
        opacity: 1;
        transform: scale(1) translateY(0);
        filter: blur(0);
        pointer-events: auto;
    }

    /* Graphic 0: SDC Dashboard styling */
    .showcase-dash-card {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: 24px;
        padding: 28px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .showcase-dash-header {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 12px;
    }

    .showcase-dash-header i {
        font-size: 1.1rem;
    }

    .showcase-project-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0, 0, 0, 0.015);
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .project-info {
        display: flex;
        flex-direction: column;
    }

    .project-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
    }

    .project-status {
        font-size: 0.7rem;
        font-weight: 700;
        color: #10b981;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .project-status.font-amber { color: #f59e0b; }
    .project-status.font-blue { color: #3b82f6; }

    .project-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .project-indicator.progress-green { background: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.5); }
    .project-indicator.progress-amber { background: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.5); }
    .project-indicator.progress-blue { background: #3b82f6; box-shadow: 0 0 8px rgba(59, 130, 246, 0.5); }

    .showcase-dash-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 12px;
    }

    .gauge-container {
        position: relative;
        width: 56px;
        height: 56px;
    }

    .gauge-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
        font-weight: 800;
        color: #10b981;
    }

    .footer-stats {
        text-align: right;
    }

    .fs-num {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .fs-lbl {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
    }

    /* Graphic 1: Houses Orbit styling */
    .showcase-houses-circle-wrapper {
        position: relative;
        width: 290px;
        height: 290px;
    }

    .showcase-houses-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 76px;
        height: 76px;
        background: #ffffff;
        border-radius: 50%;
        padding: 5px;
        border: 2px solid rgba(0, 0, 0, 0.04);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12);
        z-index: 5;
    }

    .showcase-houses-center img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .showcase-houses-orbit-ring {
        position: absolute;
        inset: 0;
        border: 2px dashed rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        transition: transform 0.1s linear;
    }

    .showcase-house-node {
        position: absolute;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        padding: 2px;
        background: #ffffff;
        transform: translate(-50%, -50%);
        box-shadow: 0 5px 12px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .showcase-house-node img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .showcase-house-node:hover {
        transform: translate(-50%, -50%) scale(1.15);
        z-index: 10;
    }

    .node-agni { top: calc(50% - 130px * 1); left: 50%; border: 2px solid #ef4444; box-shadow: 0 0 15px rgba(239, 68, 68, 0.2); }
    .node-vayu { top: calc(50% - 130px * 0.309); left: calc(50% + 130px * 0.951); border: 2px solid #eab308; box-shadow: 0 0 15px rgba(234, 179, 8, 0.2); }
    .node-prudhvi { top: calc(50% + 130px * 0.809); left: calc(50% + 130px * 0.588); border: 2px solid #f97316; box-shadow: 0 0 15px rgba(249, 115, 22, 0.2); }
    .node-jal { top: calc(50% + 130px * 0.809); left: calc(50% - 130px * 0.588); border: 2px solid #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.2); }
    .node-aakash { top: calc(50% - 130px * 0.309); left: calc(50% - 130px * 0.951); border: 2px solid #06b6d4; box-shadow: 0 0 15px rgba(6, 182, 212, 0.2); }

    /* Graphic 2: Placements card styling */
    .showcase-placements-card {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: 24px;
        padding: 24px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sp-card-title {
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 12px;
    }

    .sp-card-title i {
        margin-right: 8px;
    }

    .sp-chart-area {
        height: 150px;
        width: 100%;
        position: relative;
    }

    .sp-stats-row {
        display: flex;
        gap: 16px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 12px;
    }

    .sp-stat-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: rgba(0, 0, 0, 0.015);
        padding: 8px 12px;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .sp-label {
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .sp-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #2563eb;
    }

    .sp-value.color-indigo {
        color: #4f46e5;
    }

    /* Graphic 3: Startups Ecosystem styling */
    .showcase-startups-card {
        position: relative;
        width: 380px;
        height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ss-core {
        position: relative;
        width: 90px;
        height: 90px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, rgba(139, 92, 246, 0.02) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        border: 2px solid rgba(139, 92, 246, 0.2);
        box-shadow: 0 0 25px rgba(139, 92, 246, 0.15);
    }

    .ss-core i {
        font-size: 2.2rem;
        color: #8b5cf6;
        animation: pulseBulb 2s ease-in-out infinite;
    }

    .ss-wave-rings {
        position: absolute;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .ss-wave {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.2);
        width: 380px;
        height: 380px;
        border: 1px solid rgba(139, 92, 246, 0.06);
        border-radius: 50%;
        opacity: 1;
        animation: pulseRing 4s linear infinite;
    }

    .ss-wave:nth-child(2) { animation-delay: 2s; }
    .ss-bubble {
        position: absolute;
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ss-bubble img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.06));
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ss-bubble:hover img {
        transform: scale(1.1);
        filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.12));
    }
    .ss-b1 { top: 10px; left: 5px; animation: bubbleFloat 5s ease-in-out infinite; }
    .ss-b2 { top: 20px; right: -15px; animation: bubbleFloat 6s ease-in-out infinite 1s; }
    .ss-b3 { bottom: 10px; left: 20px; animation: bubbleFloat 5.5s ease-in-out infinite 0.5s; }
    .ss-b4 { bottom: 20px; right: 5px; animation: bubbleFloat 6.5s ease-in-out infinite 1.5s; }

    @keyframes bubbleFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(3deg); }
    }

    @keyframes pulseBulb {
        0%, 100% { opacity: 0.8; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.08); filter: drop-shadow(0 0 15px rgba(139, 92, 246, 0.6)); }
    }

    @keyframes pulseRing {
        0% { transform: translate(-50%, -50%) scale(0.2); opacity: 0.8; }
        100% { transform: translate(-50%, -50%) scale(1.1); opacity: 0; }
    }

    /* Show/Hide Views */
    .showcase-desktop-view {
        display: block;
        width: 100%;
    }

    .showcase-mobile-view {
        display: none;
    }

    /* Mobile styles */
    @media (max-width: 991px) {
        .showcase-desktop-view {
            display: none !important;
        }

        .showcase-mobile-view {
            display: block;
            padding: 80px 20px;
            background: #f8fafc;
        }

        .showcase-mobile-title-block {
            text-align: center;
            margin-bottom: 40px;
        }

        .showcase-mobile-title-block h3 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
        }

        .showcase-mobile-title-block p {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        .showcase-mobile-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 24px;
            padding: 35px 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .showcase-mobile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        }

        .showcase-mobile-card:last-child {
            margin-bottom: 0;
        }
    }
</style>

<body>
    <?php include "nav.php"; ?>

    <style>
        /* Scoped Homepage Hero Styles */
        #homepage-hero.hero-section {
            min-height: 90vh !important;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: #fafafa;
            padding: 80px 0;
        }

        #homepage-hero .hero-bg-effects {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        #homepage-hero .blur-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            mix-blend-mode: multiply;
            animation: floatCircle 20s infinite alternate ease-in-out;
        }

        #homepage-hero .circle-1 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, rgba(5, 150, 105, 0.1) 70%);
            top: 10%;
            left: 15%;
            animation-duration: 25s;
        }

        #homepage-hero .circle-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.35) 0%, rgba(8, 145, 178, 0.1) 70%);
            bottom: 15%;
            right: 15%;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        #homepage-hero .circle-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(79, 70, 229, 0.05) 70%);
            top: 40%;
            left: 45%;
            animation-duration: 22s;
            animation-delay: -10s;
        }

        @keyframes floatCircle {
            0% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(40px, -60px) scale(1.15);
            }
            100% {
                transform: translate(-30px, 40px) scale(0.9);
            }
        }

        #homepage-hero .hero-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.06);
            padding: 8px 18px;
            border-radius: 30px;
            margin-bottom: 25px;
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #homepage-hero .badge-tag {
            color: #dc2626;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        #homepage-hero .badge-divider {
            color: #cbd5e1;
            font-size: 0.85rem;
        }

        #homepage-hero .badge-text {
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        #homepage-hero .hero-main-title {
            font-family: 'Poppins', sans-serif;
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.5px;
            color: #0f172a;
            margin-bottom: 25px;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
        }

        #homepage-hero .hero-main-title .gradient-text {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
        }

        #homepage-hero .hero-description-text {
            font-size: 1.25rem;
            color: #475569;
            line-height: 1.6;
            max-width: 680px;
            margin: 0 auto 35px auto;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
        }

        #homepage-hero .hero-cta-wrapper {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
        }

        #homepage-hero .btn-explore-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff !important;
            padding: 16px 42px;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none !important;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: none;
        }

        #homepage-hero .btn-explore-pill:hover {
            transform: scale(1.04) translateY(-2px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.35);
        }

        #homepage-hero .btn-explore-pill:active {
            transform: scale(0.98) translateY(0);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            #homepage-hero .hero-main-title {
                font-size: 3.5rem;
                letter-spacing: -1px;
            }
            #homepage-hero .hero-description-text {
                font-size: 1.15rem;
            }
        }

        @media (max-width: 768px) {
            #homepage-hero.hero-section {
                min-height: 80vh !important;
                padding: 60px 0;
            }
            #homepage-hero .hero-main-title {
                font-size: 2.8rem;
                letter-spacing: -0.5px;
            }
            #homepage-hero .hero-description-text {
                font-size: 1.05rem;
                padding: 0 15px;
            }
            #homepage-hero .btn-explore-pill {
                padding: 14px 36px;
                font-size: 1rem;
            }
        }
    </style>

    <!-- PREMIUM HERO SECTION -->
    <section class="premium-hero-section">
        <!-- Animated Background Elements -->
        <div class="hero-background">
            <!-- Background Video Player -->
            <video id="bgHeroVideo" class="hero-video-bg" autoplay muted loop playsinline preload="auto">
                <source src="assets/videos/hero-background-opt.mp4?v=2" type="video/mp4">
                <source src="assets/videos/hero-background.mp4?v=2" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <script>
            (function() {
                function initHeroVideo() {
                    var v = document.getElementById('bgHeroVideo');
                    if (!v) return;
                    v.muted = true;
                    v.defaultMuted = true;
                    v.playsInline = true;
                    
                    function tryPlay() {
                        if (v.paused) {
                            var p = v.play();
                            if (p && p.catch) {
                                p.catch(function(e) {
                                    function forcePlay() {
                                        v.play();
                                        window.removeEventListener('click', forcePlay);
                                        window.removeEventListener('touchstart', forcePlay);
                                        window.removeEventListener('scroll', forcePlay);
                                    }
                                    window.addEventListener('click', forcePlay);
                                    window.addEventListener('touchstart', forcePlay);
                                    window.addEventListener('scroll', forcePlay);
                                });
                            }
                        }
                    }
                    
                    tryPlay();
                    v.addEventListener('canplay', tryPlay);
                    v.addEventListener('loadeddata', tryPlay);
                    window.addEventListener('load', tryPlay);
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initHeroVideo);
                } else {
                    initHeroVideo();
                }
            })();
            </script>

            <!-- Gradient & Dark Overlay Veil for crisp text readability -->
            <div class="hero-overlay-veil"></div>

            <!-- Floating Particles -->
            <div class="hero-particle particle-blue"></div>
            <div class="hero-particle particle-cyan"></div>
            <div class="hero-particle particle-green"></div>
            
            <!-- Glowing Circles -->
            <div class="glow-circle glow-1"></div>
            <div class="glow-circle glow-2"></div>
            
            <!-- Animated Blurred Blobs -->
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>

        <!-- Hero Content -->
        <div class="hero-content-wrapper">
            <!-- Main Heading with Blur Text Effect -->
            <h1 class="hero-main-heading blur-text-animate">
                <span class="srkrec-text">SRKREC</span>
                <span class="csd-csit-text">CSD-CSIT</span>
                <span class="department-text">Department</span>
            </h1>

            <!-- Secondary Heading -->
            <h2 class="hero-secondary-heading blur-text-animate" data-delay="300">
                <span class="where-learning-text">Where Learning</span>
                <br>
                <span class="meets-innovation-text" data-text="Meets Innovation">Meets Innovation</span>
            </h2>

            <!-- Subtitle -->
            <p class="hero-subtitle blur-text-animate" data-delay="600">
                Empowering future innovators through technology, research, creativity and industry-focused education.
            </p>

            <!-- Explore Button -->
            <a href="#pillars-section" id="exploreBtn" class="hero-explore-button" style="text-decoration: none !important;">
                Explore Department
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- ReactBits BlurText Animation Script -->
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const blurContainers = document.querySelectorAll('.blur-text-animate');
            blurContainers.forEach(container => {
                const baseDelay = parseInt(container.getAttribute('data-delay') || '0', 10);
                const spans = container.querySelectorAll('span');
                
                if (spans.length > 0) {
                    spans.forEach((span, idx) => {
                        span.classList.add('blur-word');
                        span.style.animationDelay = `${(baseDelay + (idx * 200)) / 1000}s`;
                    });
                } else {
                    const text = container.innerText.trim();
                    const words = text.split(/\s+/);
                    container.innerHTML = words.map((w, idx) => 
                        `<span class="blur-word" style="animation-delay: ${(baseDelay + (idx * 150)) / 1000}s">${w}</span>`
                    ).join(' ');
                }
            });
        });
        </script>
    </section>



    <style>
    /* Scroll-Triggered ReactBits BlurText Animation matching Hero Section */
    .scroll-blur-animate {
        opacity: 1;
    }

    .scroll-blur-word {
        display: inline-block;
        opacity: 0;
        filter: blur(14px);
        transform: translateY(-28px);
        will-change: transform, filter, opacity;
    }

    .scroll-blur-animate.animated-in .scroll-blur-word {
        animation: blurTextInScroll 0.75s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    @keyframes blurTextInScroll {
        0% {
            filter: blur(14px);
            opacity: 0;
            transform: translateY(-28px);
        }
        50% {
            filter: blur(5px);
            opacity: 0.6;
            transform: translateY(-5px);
        }
        100% {
            filter: blur(0px);
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>

    <style>
    /* Compact Timeline & Leadership Section */
    .combined-overview-section {
        padding: 60px 0;
        background: #fdfbf7;
    }

    .compact-timeline-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px 32px;
        border: 1px solid #f3eae1;
        box-shadow: 0 10px 30px rgba(180, 83, 9, 0.04);
    }

    .compact-timeline {
        position: relative;
        padding-left: 26px;
    }

    .compact-timeline::before {
        content: '';
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 7px;
        width: 2px;
        background: #e6c280;
    }

    .compact-timeline-item {
        position: relative;
        margin-bottom: 18px;
    }

    .compact-timeline-item:last-child {
        margin-bottom: 0;
    }

    .compact-node {
        position: absolute;
        left: -26px;
        top: 6px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid #d97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
        transition: all 0.3s ease;
        z-index: 2;
    }

    .compact-timeline-item:hover .compact-node {
        background: #d97706;
        transform: scale(1.2);
    }

    .timeline-box {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        border: 1px solid #f3eae1;
        transition: all 0.3s ease;
    }

    .timeline-box:hover {
        background: #ffffff;
        transform: translateX(4px);
        box-shadow: 0 8px 20px rgba(180, 83, 9, 0.06);
        border-color: rgba(217, 119, 6, 0.3);
    }

    /* Live Updates Pulse Dot */
    @keyframes livePulse {
        0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }

    .live-pulse-dot {
        width: 10px;
        height: 10px;
        background-color: #e11d48;
        border-radius: 50%;
        display: inline-block;
        animation: livePulse 1.8s infinite;
        box-shadow: 0 0 10px rgba(225, 29, 72, 0.8);
    }

    .live-updates-card {
        border: 1.5px solid rgba(244, 63, 94, 0.38) !important;
        box-shadow: 0 10px 25px rgba(225, 29, 72, 0.08) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .live-updates-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 16px 36px rgba(225, 29, 72, 0.22) !important;
        border-color: rgba(225, 29, 72, 0.70) !important;
    }
    </style>

    <section id="faculty-section" class="combined-overview-section">
        <div class="container">

            <!-- Founder & SRKR Heritage Section (Placed Above Timeline & Leadership) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div style="background: linear-gradient(135deg, #ffffff 0%, #fdfbf7 100%); border: 1.5px solid #f3eae1; border-radius: 18px; padding: 18px 24px; box-shadow: 0 6px 24px rgba(180, 83, 9, 0.05); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: linear-gradient(180deg, #d97706 0%, #b45309 100%);"></div>
                        <div class="row align-items-center g-3">
                            <div class="col-md-3 text-center">
                                <div style="position: relative; display: inline-block;">
                                    <img src="./assets/logos/srkr_founder.jpg" alt="Sri Sagi Ramakrishnam Raju - Founder SRKREC" style="width: 140px; height: 170px; object-fit: cover; border-radius: 14px; border: 3px solid #ffffff; box-shadow: 0 8px 20px rgba(26, 13, 6, 0.15), 0 0 0 2px #d97706;">
                                    <span style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #1a0d06 0%, #361a0c 100%); color: #f59e0b; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.64rem; font-weight: 800; letter-spacing: 0.8px; padding: 3px 12px; border-radius: 999px; border: 1px solid #d97706; white-space: nowrap; box-shadow: 0 3px 8px rgba(0,0,0,0.2);">
                                        SRI SAGI RAMAKRISHNAM RAJU
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.72rem; font-weight: 800; letter-spacing: 2px; color: #b45309; text-transform: uppercase; display: block; margin-bottom: 4px;">FOUNDER &amp; LEGACY</span>
                                <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.45rem; color: #1a0d06; margin-bottom: 8px; line-height: 1.2;">
                                    S.R.K.R. Engineering <span style="color: #d97706;">College</span>
                                </h3>
                                <p style="font-size: 0.88rem; color: #475569; line-height: 1.55; margin: 0;">
                                    Founded in 1980 by visionary philanthropist <strong style="color: #1a0d06;">Sri Sagi Ramakrishnam Raju</strong>, S.R.K.R. Engineering College (SRKREC) stands as a premier NAAC 'A+' autonomous institution in Bhimavaram. Spanning a lush 30-acre campus with advanced AI laboratories and research centers, the institution is dedicated to academic excellence, innovation, and global student success.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-stretch g-4">
                <!-- Timeline - 60% Width (col-md-7) -->
                <div class="col-md-7">
                    <div class="compact-timeline-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-4">
                                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.85rem; font-weight: 800; letter-spacing: 2.5px; color: #b45309; text-transform: uppercase; display: block; margin-bottom: 6px;">TIMELINE</span>
                                <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2rem; color: #1a0d06; margin: 0;">How the department got here</h3>
                            </div>

                            <div class="compact-timeline">
                                <!-- 2022 -->
                                <div class="compact-timeline-item">
                                    <div class="compact-node"></div>
                                    <div class="timeline-box">
                                        <span style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 700; color: #d97706; display: block; margin-bottom: 2px;">2022</span>
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1a0d06; margin-bottom: 4px;">CSD Program Launched</h5>
                                        <p style="font-size: 0.9rem; color: #6f5f54; margin: 0; line-height: 1.5;">SRKR introduced Computer Science & Design, one of the earliest such programs in the region.</p>
                                    </div>
                                </div>

                                <!-- 2023 -->
                                <div class="compact-timeline-item">
                                    <div class="compact-node"></div>
                                    <div class="timeline-box">
                                        <span style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 700; color: #d97706; display: block; margin-bottom: 2px;">2023</span>
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1a0d06; margin-bottom: 4px;">CSIT Program Launched</h5>
                                        <p style="font-size: 0.9rem; color: #6f5f54; margin: 0; line-height: 1.5;">SRKR introduced Computer Science & Information Technology (CSIT) program.</p>
                                    </div>
                                </div>

                                <!-- 2024 -->
                                <div class="compact-timeline-item">
                                    <div class="compact-node"></div>
                                    <div class="timeline-box">
                                        <span style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 700; color: #d97706; display: block; margin-bottom: 2px;">2024</span>
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1a0d06; margin-bottom: 4px;">CSIT Intake Doubled & Student House System Introduced</h5>
                                        <p style="font-size: 0.9rem; color: #6f5f54; margin: 0; line-height: 1.5;">Growing industry demand led to a second CSIT section expanding intake to 120 seats, while Prudhvi, Vayu, Agni, Aakash, and Jal launched to drive mentorship and events.</p>
                                    </div>
                                </div>

                                <!-- 2025 -->
                                <div class="compact-timeline-item">
                                    <div class="compact-node"></div>
                                    <div class="timeline-box">
                                        <span style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 700; color: #d97706; display: block; margin-bottom: 2px;">2025</span>
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1a0d06; margin-bottom: 4px;">Department-wide Digital Platform</h5>
                                        <p style="font-size: 0.9rem; color: #6f5f54; margin: 0; line-height: 1.5;">A unified points, events, and appreciation system rolled out across every batch and section.</p>
                                    </div>
                                </div>

                                <!-- 2026 -->
                                <div class="compact-timeline-item">
                                    <div class="compact-node"></div>
                                    <div class="timeline-box">
                                        <span style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 700; color: #d97706; display: block; margin-bottom: 2px;">2026</span>
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: #1a0d06; margin-bottom: 4px;">Jaitra 2k26</h5>
                                        <p style="font-size: 0.9rem; color: #6f5f54; margin: 0; line-height: 1.5;">The department co-anchored SRKR's flagship annual fest, its largest student showcase yet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message from Leadership & Live Updates - 40% Width (col-md-5) -->
                <div class="col-md-5 d-flex flex-column justify-content-between">
                    <!-- Leadership Card -->
                    <div class="hod-card d-flex flex-column" style="padding: 26px 30px; border-radius: 20px; border: 1px solid #f3eae1; background: #ffffff; box-shadow: 0 10px 30px rgba(180, 83, 9, 0.04);">
                        <div>
                            <div class="text-center mb-3">
                                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.8rem; font-weight: 800; letter-spacing: 2.5px; color: #b45309; text-transform: uppercase; display: block; margin-bottom: 4px;">LEADERSHIP</span>
                                <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.8rem; color: #1a0d06; margin: 0;">Message from Leadership</h3>
                            </div>

                            <!-- Leadership Members Side by Side -->
                            <div style="display: flex; gap: 16px; justify-content: center; margin-bottom: 16px;">
                                <!-- Leadership Section -->
                                <div class="leadership-member" style="text-align: center; flex: 1;">
                                    <div class="member-image-container" style="position: relative; display: inline-block; margin-bottom: 10px;">
                                        <img src="./assets/logos/sureshsir.png" alt="Program Coordinator"
                                            style="width: 95px; height: 95px; border-radius: 50%; border: 3.5px solid #d97706; object-fit: cover; box-shadow: 0 6px 16px rgba(217, 119, 6, 0.2);">
                                    </div>
                                    <h6 style="color: #1a0d06; margin-bottom: 3px; font-weight: 700; font-size: 0.9rem;">Dr. M Suresh Babu</h6>
                                    <p style="color: #6f5f54; font-size: 0.78rem; font-weight: 600; margin-bottom: 0;">Program Coordinator - CSD</p>
                                </div>

                                <!-- Second Leadership Member -->
                                <div class="leadership-member" style="text-align: center; flex: 1;">
                                    <div class="member-image-container" style="position: relative; display: inline-block; margin-bottom: 10px;">
                                        <img src="./assets/faculty_imgs/4.jpg" alt="Associate Head"
                                            style="width: 95px; height: 95px; border-radius: 50%; border: 3.5px solid #b45309; object-fit: cover; box-shadow: 0 6px 16px rgba(180, 83, 9, 0.2);">
                                    </div>
                                    <h6 style="color: #1a0d06; margin-bottom: 3px; font-weight: 700; font-size: 0.9rem;">Dr. N. Gopala Krishna Murthy</h6>
                                    <p style="color: #6f5f54; font-size: 0.78rem; font-weight: 600; margin-bottom: 0;">Program Coordinator - CSIT</p>
                                </div>
                            </div>
                        </div>

                        <!-- Combined Quote -->
                        <div style="padding: 16px 20px; background: linear-gradient(135deg, #fdfbf7 0%, #f3eae1 100%); border-radius: 16px; border-left: 4px solid #d97706;">
                            <blockquote style="font-size: 0.88rem; line-height: 1.55; font-style: italic; margin: 0; color: #1a0d06; font-weight: 500;">
                                "We nurture innovative minds and create technology leaders who will shape the future through excellence in education and innovation in research."
                            </blockquote>
                        </div>
                    </div>

                    <!-- Live Updates Box -->
                    <div class="live-updates-card" style="flex: 1; margin-top: 16px; padding: 24px 26px; border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #fdfbf7 100%); border: 1.5px solid #f3eae1; box-shadow: 0 10px 25px rgba(26, 13, 6, 0.05); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="live-pulse-dot" style="background: #94a3b8; box-shadow: 0 0 8px rgba(148, 163, 184, 0.5);"></span>
                                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.8rem; font-weight: 800; letter-spacing: 2px; color: #64748b; text-transform: uppercase;">LIVE UPDATES</span>
                                </div>
                                <span style="background: #f1f5f9; color: #64748b; font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 12px; font-family: 'Outfit', sans-serif; border: 1px solid #e2e8f0;">SCHEDULE</span>
                            </div>

                            <!-- No Events Box -->
                            <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px; padding: 32px 20px; text-align: center;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 12px;">
                                    <i class="far fa-calendar-times"></i>
                                </div>
                                <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem; color: #334155; margin: 0 0 6px 0;">
                                    No Live Events Currently
                                </h4>
                                <p style="font-size: 0.84rem; color: #64748b; margin: 0; line-height: 1.5;">
                                    There are no active or scheduled live events at the moment. Please check back later for future department announcements!
                                </p>
                            </div>
                        </div>

                        <!-- Status Footer Info -->
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 18px; background: #fdfbf7; border: 1px solid #f3eae1; padding: 8px 14px; border-radius: 12px;">
                            <i class="fas fa-info-circle" style="color: #d97706; font-size: 0.85rem;"></i>
                            <span style="font-size: 0.78rem; color: #6f5f54; font-weight: 600;">Stay tuned for upcoming hackathons, workshops, and tech fests.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Department Pillars Section (ReactBits ScrollStack Component) -->
    <section class="pillars-section" id="pillars-section" style="padding: 80px 0 0; background: #ffffff;">
        <div class="container">
            <div class="section-title text-center mb-4">
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.8rem; font-weight: 900; color: #1a0d06; margin-bottom: 10px;">Department <span style="color: #d97706;">Pillars</span></h2>
                <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.1rem; color: #64748b; max-width: 650px; margin: 0 auto;">Nurturing innovation, student culture, career excellence, and entrepreneurial ventures</p>
            </div>
        </div>

        <div class="scroll-stack-wrapper">
            <div class="scroll-stack-scroller">
                <div class="scroll-stack-inner">
                    <!-- Pillar 1: SDC Card -->
                    <div class="scroll-stack-card">
                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <span class="showcase-card-badge" style="color: #d97706; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem;">Pillar 1: Innovation Hub</span>
                                <h2 class="showcase-card-title" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #1a0d06; margin: 10px 0 15px;">Software Development Centre (SDC)</h2>
                                <p class="showcase-card-text" style="color: #475569; font-size: 1rem; line-height: 1.7; margin-bottom: 25px;">Step into our 50-seated software engineering lab. CSD & CSIT students build real-world, production-ready applications that power local department functions, while earning hands-on paid internships.</p>
                                <div class="d-flex gap-4">
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">20+</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Apps Built</div>
                                    </div>
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">50+</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Paid Internships</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="showcase-dash-card" style="background: #ffffff; border-radius: 24px; padding: 30px; border: 1px solid #f3eae1; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                                    <div class="showcase-dash-header mb-3 d-flex align-items-center">
                                        <i class="fas fa-cubes text-[#10b981]" style="color: #10b981; margin-right: 10px; font-size: 1.2rem;"></i>
                                        <span style="font-weight: 800; color: #1a0d06; font-size: 1.05rem;">SDC Project Board</span>
                                    </div>
                                    <div class="showcase-dash-body mb-3">
                                        <div class="showcase-project-row d-flex justify-content-between align-items-center p-3 mb-2" style="background: #f8fafc; border-radius: 12px;">
                                            <div>
                                                <div style="font-weight: 700; color: #1e293b;">Attendance Portal</div>
                                                <div style="font-size: 0.75rem; color: #10b981; font-weight: 700;">LIVE</div>
                                            </div>
                                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
                                        </div>
                                        <div class="showcase-project-row d-flex justify-content-between align-items-center p-3 mb-2" style="background: #f8fafc; border-radius: 12px;">
                                            <div>
                                                <div style="font-weight: 700; color: #1e293b;">House League Tracker</div>
                                                <div style="font-size: 0.75rem; color: #f59e0b; font-weight: 700;">TESTING</div>
                                            </div>
                                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></div>
                                        </div>
                                        <div class="showcase-project-row d-flex justify-content-between align-items-center p-3" style="background: #f8fafc; border-radius: 12px;">
                                            <div>
                                                <div style="font-weight: 700; color: #1e293b;">Faculty Appraisals System</div>
                                                <div style="font-size: 0.75rem; color: #3b82f6; font-weight: 700;">DEPLOYING</div>
                                            </div>
                                            <div style="width: 12px; height: 12px; border-radius: 50%; background: #3b82f6;"></div>
                                        </div>
                                    </div>
                                    <div class="showcase-dash-footer d-flex justify-content-between align-items-center pt-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 900; color: #10b981;">92%</div>
                                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Completion Rate</div>
                                        </div>
                                        <div class="text-end">
                                            <div style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 900; color: #1a0d06;">24</div>
                                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Active Devs</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 2: House System Card -->
                    <div class="scroll-stack-card">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                                <span class="showcase-card-badge" style="color: #d97706; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem;">Pillar 2: Student Culture</span>
                                <h2 class="showcase-card-title" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #1a0d06; margin: 10px 0 15px;">Vibrant House System</h2>
                                <p class="showcase-card-text" style="color: #475569; font-size: 1rem; line-height: 1.7; margin-bottom: 25px;">Belong to one of our five elemental leagues: Agni, Vayu, Prudhvi, Jal, or Aakash. Compete in continuous hackathons, coding contests, sports, and cultural battles for the annual championship shield.</p>
                                <div class="d-flex gap-4">
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">5</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Active Houses</div>
                                    </div>
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">10+</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Events Semwise</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 order-lg-1">
                                <div style="background: #fdfbf7; border-radius: 24px; padding: 30px; border: 1px solid #f3eae1; text-align: center; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                                    <img src="./assets/logos/allhouses.webp" alt="All Houses" style="max-height: 250px; width: auto; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 3: Placements Card -->
                    <div class="scroll-stack-card">
                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <span class="showcase-card-badge" style="color: #d97706; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem;">Pillar 3: Careers</span>
                                <h2 class="showcase-card-title" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #1a0d06; margin: 10px 0 15px;">Exceptional Placements</h2>
                                <p class="showcase-card-text" style="color: #475569; font-size: 1rem; line-height: 1.7; margin-bottom: 25px;">Align with global tech standards. Leverage our extensive placement training and industry partnerships to secure core engineering positions with top multinational partners.</p>
                                <div class="d-flex gap-4">
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">₹12 LPA</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Highest Pkg</div>
                                    </div>
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">₹5.1 LPA</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Average CTC</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="showcase-placements-card" style="background: #ffffff; border-radius: 24px; padding: 30px; border: 1px solid #f3eae1; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                                    <div class="sp-card-title mb-3" style="font-weight: 800; color: #1a0d06; font-size: 1.05rem;"><i class="fas fa-chart-line" style="color: #3b82f6; margin-right: 8px;"></i>Salary Packages Trend</div>
                                    <div class="sp-chart-area mb-3" style="height: 140px;">
                                        <svg width="100%" height="100%" viewBox="0 0 400 150">
                                            <defs>
                                                <linearGradient id="glowGradNorm" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" stop-color="#3b82f6" />
                                                    <stop offset="100%" stop-color="#8b5cf6" />
                                                </linearGradient>
                                            </defs>
                                            <line x1="10" y1="130" x2="390" y2="130" stroke="rgba(0,0,0,0.06)" stroke-width="1" />
                                            <line x1="10" y1="85" x2="390" y2="85" stroke="rgba(0,0,0,0.06)" stroke-width="1" />
                                            <line x1="10" y1="40" x2="390" y2="40" stroke="rgba(0,0,0,0.06)" stroke-width="1" />
                                            <path d="M 10 120 Q 80 110 150 70 T 290 40 T 390 15" fill="none" stroke="url(#glowGradNorm)" stroke-width="4" stroke-linecap="round" />
                                            <circle cx="390" cy="15" r="7" fill="#ffffff" stroke="#6366f1" stroke-width="3" />
                                        </svg>
                                    </div>
                                    <div class="sp-stats-row d-flex justify-content-between pt-2">
                                        <div>
                                            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600; display: block;">Highest Package</span>
                                            <span style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; color: #1a0d06;">₹12.0 LPA</span>
                                        </div>
                                        <div class="text-end">
                                            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600; display: block;">Average CTC</span>
                                            <span style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; color: #6366f1;">₹5.1 LPA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 4: Ventures Card -->
                    <div class="scroll-stack-card">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                                <span class="showcase-card-badge" style="color: #d97706; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem;">Pillar 4: Ventures</span>
                                <h2 class="showcase-card-title" style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #1a0d06; margin: 10px 0 15px;">Startup Incubation</h2>
                                <p class="showcase-card-text" style="color: #475569; font-size: 1rem; line-height: 1.7; margin-bottom: 25px;">Launch your ideas. Our incubation program provides legal support, workspace facilities, technical mentorship, and connects you with early stage capital to launch student-led startups.</p>
                                <div class="d-flex gap-4">
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">6+</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Incubated Teams</div>
                                    </div>
                                    <div style="background: #fdfbf7; padding: 16px 28px; border-radius: 16px; border: 1px solid #f3eae1;">
                                        <div style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: #d97706;">3</div>
                                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700;">Alumni Startups</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 order-lg-1">
                                <div style="background: #fdfbf7; border-radius: 24px; padding: 40px 30px; border: 1px solid #f3eae1; text-align: center; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #f59e0b; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 20px; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1a0d06; margin-bottom: 10px;">Innovation & Incubation Hub</h4>
                                    <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 0;">Providing mentorship, seed capital, legal guidance, and office infrastructure for student entrepreneurs.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End Marker for Pin release -->
                    <div class="scroll-stack-end"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Partners Section -->
    <section class="company-partners-section" style="padding: 80px 0; margin-top: -100px;">
        <div class="container">
            <div class="section-title">
                <h2>Our Industry Partners</h2>
                <p>Leading companies that trust our talent</p>
            </div>

            <div class="companies-carousel-wrapper" style="overflow: hidden; ">
                <!-- First Row -->
                <div class="carousel-row" style="display: flex; animation: scroll-left 30s linear infinite; margin-bottom: -40px; ">
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/1.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/2.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/3.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/4.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/5.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/6.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/7.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/8.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/9.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/10.png" alt="Company Partner">
                    </div>
                    <!-- Duplicate items for seamless loop -->
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/1.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/2.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/3.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/4.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/5.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/6.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/7.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/8.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/9.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/10.png" alt="Company Partner">
                    </div>
                </div>

                <!-- Second Row -->
                <div class="carousel-row" style="display: flex; animation: scroll-right 30s linear infinite; ">
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/11.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/12.png?v=2" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/13.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/14.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/15.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/16.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/17.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/18.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/19.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/20.png" alt="Company Partner">
                    </div>
                    <!-- Duplicate items for seamless loop -->
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/11.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/12.png?v=2" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/13.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/14.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/15.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/16.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/17.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/18.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/19.png" alt="Company Partner">
                    </div>
                    <div class="company-item">
                        <img src="./assets/company_logos/logos/20.png" alt="Company Partner">
                    </div>
                </div>
            </div>

            <style>
                .carousel-row {
                    display: flex;
                    width: max-content;
                    gap: 30px;
                }

                .company-item {
                    flex: 0 0 270px;
                    height: 140px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: transparent !important;
                    border: none !important;
                    box-shadow: none !important;
                    outline: none !important;
                    padding: 10px;
                }

                .company-item img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                    border: none !important;
                    box-shadow: none !important;
                    outline: none !important;
                }

                @keyframes scroll-left {
                    0% {
                        transform: translateX(0);
                    }

                    100% {
                        transform: translateX(-50%);
                    }
                }

                @keyframes scroll-right {
                    0% {
                        transform: translateX(-50%);
                    }

                    100% {
                        transform: translateX(0);
                    }
                }

                .carousel-row:hover {
                    animation-play-state: paused;
                }

                .company-item:hover {
                    transform: translateY(-5px);
                    transition: all 0.3s ease;
                }

                /* PLACEMENTS ZOOM-REVEAL SECTION STYLES */
                /* PLACEMENTS SECTION STYLES */
                /* ========================================== */
                .placement-scroll-container {
                    position: relative;
                    height: auto;
                    background: #ffffff;
                    margin: 0;
                    padding: 80px 0;
                }

                .placement-sticky-viewport {
                    position: relative;
                    height: auto;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    overflow: visible;
                    z-index: 5;
                    background: #ffffff;
                }

                .placement-content-wrapper {
                    width: 100%;
                    position: relative;
                    z-index: 10;
                }

                .placement-zoom-card-wrapper {
                    position: relative;
                    margin-top: 30px;
                    width: 100%;
                    max-width: 440px;
                    height: auto;
                }

                .placement-zoom-card {
                    position: relative;
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 24px;
                    padding: 24px;
                    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
                    width: 100%;
                    max-width: 440px;
                    z-index: 20;
                }

                .zoom-card-header {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-weight: 700;
                    color: #0f172a;
                    font-size: 0.95rem;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
                    padding-bottom: 12px;
                    margin-bottom: 16px;
                }

                .zoom-card-header i {
                    color: #ef4444;
                    font-size: 1.1rem;
                }

                .growth-chart-container {
                    position: relative;
                    width: 100%;
                }

                .zoom-card-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-top: 1px solid rgba(0, 0, 0, 0.06);
                    padding-top: 12px;
                    margin-top: 16px;
                    font-size: 0.82rem;
                    font-weight: 600;
                    color: #64748b;
                }

                .placement-text-reveal {
                    opacity: 1 !important;
                    transform: none !important;
                }

                .placement-meta {
                    font-size: 0.75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    color: #ef4444;
                    letter-spacing: 2px;
                    margin-bottom: 12px;
                    display: inline-block;
                }

                .placement-metrics-reveal {
                    opacity: 1 !important;
                    transform: none !important;
                    display: flex;
                    flex-direction: column;
                    gap: 40px;
                    width: 100%;
                }

                .revenue-display {
                    text-align: right;
                }

                .revenue-amount {
                    font-size: 100px;
                    font-weight: 800;
                    color: #0f172a;
                    line-height: 1;
                    letter-spacing: -0.02em;
                }

                .revenue-label {
                    font-size: 15px;
                    color: #6b7280;
                    margin-top: 5px;
                    font-weight: 500;
                }

                .metrics-container {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px 40px;
                    width: 100%;
                }

                .metric-box-reveal {
                    text-align: left;
                    opacity: 1 !important;
                    transform: none !important;
                }

                .metric-value {
                    font-size: 36px;
                    font-weight: 700;
                    color: #000;
                    line-height: 1.2;
                    margin-bottom: 5px;
                }

                .metric-description {
                    font-size: 14px;
                    color: #6b7280;
                    font-weight: 400;
                }

                /* Mobile responsive adjustments */
                @media (max-width: 991px) {
                    .placement-scroll-container {
                        height: auto !important;
                    }

                    .placement-sticky-viewport {
                        position: relative !important;
                        height: auto !important;
                        overflow: visible !important;
                        padding: 80px 0 !important;
                    }

                    .placement-zoom-card-wrapper {
                        height: auto !important;
                        margin-top: 20px !important;
                    }

                    .placement-zoom-card {
                        position: relative !important;
                        transform: none !important;
                        margin: 0 auto !important;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
                    }

                    .placement-text-reveal,
                    .placement-metrics-reveal,
                    .metric-box-reveal {
                        opacity: 1 !important;
                        transform: none !important;
                    }

                    .placement-right-col {
                        margin-top: 50px !important;
                        align-items: flex-start !important;
                    }

                    .revenue-display {
                        text-align: left !important;
                    }
                }
            </style>

            <div class="text-center">
                <p style="color: #64748b; font-style: italic;">And many more leading companies...</p>
            </div>
        </div>
    </section>


    <!-- Startup Partners & Incubation Ecosystem Section -->
    <section class="startup-testimonials-section" style="padding: 80px 0 60px; position: relative; background: #f8fafc; overflow: hidden;">
        <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
            <!-- Header Text -->
            <div class="text-center" style="margin-bottom: 50px;">
                <p style="color: #7c3aed; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Startup Ecosystem</p>
                <h2 style="color: #0f172a; font-size: 3.2rem; font-weight: 800; margin-bottom: 15px; line-height: 1.15;">
                    Empowering <span style="color: #7c3aed;">entrepreneurs</span>
                </h2>
                <h3 style="color: #475569; font-size: 1.8rem; font-weight: 500; margin-bottom: 20px;">
                    across diverse sectors
                </h3>
                <p style="color: #64748b; font-size: 1.1rem; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Discover how our startup incubation program nurtures innovative student ideas and transforms them into successful ventures across various industries.
                </p>
            </div>

            <!-- ReactBits Interactive 3D DepthCarousel Component -->
            <div class="depth-carousel-wrapper" style="height: 540px; position: relative;">
                <div class="depth-carousel">
                    <div class="depth-carousel__stage">
                        <!-- Card 1: Lunchbox -->
                        <div class="depth-carousel__card" data-startup="lunch-box" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/25.png" alt="Lunchbox">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">Lunchbox</h3>
                                <span class="depth-carousel__badge">Incubated Venture</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>

                        <!-- Card 2: Campus Online -->
                        <div class="depth-carousel__card" data-startup="campus-online" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/26.png" alt="Campus Online">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">Campus Online</h3>
                                <span class="depth-carousel__badge">EdTech Startup</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>

                        <!-- Card 3: Bhimavaram Foods -->
                        <div class="depth-carousel__card" data-startup="bhimavaram-foods" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/21.png" alt="Bhimavaram Foods">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">Bhimavaram Foods</h3>
                                <span class="depth-carousel__badge">Food Tech</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>

                        <!-- Card 4: Smart Wash -->
                        <div class="depth-carousel__card" data-startup="smart-wash" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/23.png" alt="Smart Wash">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">Smart Wash</h3>
                                <span class="depth-carousel__badge">Services Platform</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>

                        <!-- Card 5: Bhimavaram Online -->
                        <div class="depth-carousel__card" data-startup="bhimavaram-online" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/22.png" alt="Bhimavaram Online">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">Bhimavaram Online</h3>
                                <span class="depth-carousel__badge">Local Commerce</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>

                        <!-- Card 6: NutriDelight -->
                        <div class="depth-carousel__card" data-startup="nutridelight" style="cursor: pointer;">
                            <div class="depth-carousel__logo-box">
                                <img class="depth-carousel__img" src="./assets/company_logos/logos/24.png" alt="NutriDelight">
                            </div>
                            <div class="depth-carousel__content">
                                <h3 class="depth-carousel__title">NutriDelight</h3>
                                <span class="depth-carousel__badge">Health & Wellness</span>
                            </div>
                            <span class="depth-carousel__tint" style="background: #7b5900;"></span>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <button type="button" class="depth-carousel__arrow depth-carousel__arrow--prev" aria-label="Previous slide">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                            <path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="depth-carousel__arrow depth-carousel__arrow--next" aria-label="Next slide">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                            <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- Indicators -->
                    <div class="depth-carousel__dots" role="tablist">
                        <button type="button" class="depth-carousel__dot is-active" aria-label="Slide 1"></button>
                        <button type="button" class="depth-carousel__dot" aria-label="Slide 2"></button>
                        <button type="button" class="depth-carousel__dot" aria-label="Slide 3"></button>
                        <button type="button" class="depth-carousel__dot" aria-label="Slide 4"></button>
                        <button type="button" class="depth-carousel__dot" aria-label="Slide 5"></button>
                        <button type="button" class="depth-carousel__dot" aria-label="Slide 6"></button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .startup-logos-orderly-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .startup-logo-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 20px;
                padding: 30px 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 210px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .startup-logo-card:hover {
                transform: translateY(-8px);
                border-color: #7c3aed;
                box-shadow: 0 15px 35px rgba(124, 58, 237, 0.18);
            }

            .startup-logo-card img {
                max-width: 90%;
                max-height: 115px;
                object-fit: contain;
                margin-bottom: 15px;
                transition: transform 0.3s ease;
            }

            .startup-logo-card:hover img {
                transform: scale(1.1);
            }

            .startup-name {
                font-size: 1rem;
                font-weight: 700;
                color: #1e293b;
                text-align: center;
            }

            @media (max-width: 992px) {
                .startup-logos-orderly-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 576px) {
                .startup-logos-orderly-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>


        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-startup]').forEach(item => {
                item.addEventListener('click', function(e) {
                    const startupKey = this.getAttribute('data-startup');
                    if (startupKey === 'smart-wash') {
                        window.location.href = 'smart_wash.php';
                        return;
                    }
                    if (startupKey === 'nutridelight') {
                        window.location.href = 'startup_details.php?id=nutridelight';
                        return;
                    }
                    if (startupKey === 'bhimavaram-digitals' || startupKey === 'bhimavaram-digital') {
                        window.location.href = 'startup_details.php?id=bhimavaram-digitals';
                        return;
                    }
                    if (startupKey === 'bhimavaram-online' || startupKey === 'bhimavaramonline') {
                        window.location.href = 'startup_details.php?id=bhimavaram-online';
                        return;
                    }
                    if (startupKey === 'lunch-box' || startupKey === 'lunchbox') {
                        window.location.href = 'startup_details.php?id=lunch-box';
                        return;
                    }
                    if (startupKey === 'campus-online' || startupKey === 'campusonline') {
                        window.location.href = 'startup_details.php?id=campus-online';
                        return;
                    }
                    if (startupKey === 'bhimavaram-foods' || startupKey === 'bhimavaramfoods' || startupKey === 'bhimavaram-online-foods') {
                        window.location.href = 'startup_details.php?id=bhimavaram-foods';
                        return;
                    }
                    
                    window.location.href = 'startup_details.php?id=' + startupKey;
                });
            });
        });
        </script>
    </section>

           <!-- Zoom Reveal Placements Section -->
    <section class="placement-scroll-container">
        <div class="placement-sticky-viewport">
            <div class="container main-container placement-content-wrapper">
                <div class="row align-items-center">
                    <!-- Left Column: Title and Zoom Card -->
                    <div class="col-lg-6 placement-left-col">
                        <div class="placement-text-reveal">
                            <span class="placement-meta">Academics & Careers</span>
                            <h1 style="font-size: 3.2rem; font-weight: 800; color: #0f172a; line-height: 1.15; margin-bottom: 8px;">Exceptional Placement</h1>
                            <h2 style="font-size: 2.2rem; font-weight: 700; color: #475569; margin-bottom: 20px;">Record at CSD & CSIT</h2>
                            <p style="font-size: 1rem; color: #64748b; line-height: 1.6; max-width: 480px; margin-bottom: 30px;">66% placement rate, 50+ students placed in top MNCs out of 70, and average CTC of ₹5.1L with highest reaching ₹12L.</p>
                        </div>
                        
                        <div class="placement-zoom-card-wrapper">
                            <div class="placement-zoom-card">
                                <div class="zoom-card-header">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Placement Growth Index</span>
                                </div>
                                <div class="growth-chart-container">
                                    <svg class="placement-growth-svg" viewBox="0 0 310 100" style="width: 100%; height: 120px; overflow: visible;">
                                        <defs>
                                            <linearGradient id="lineGradReveal" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" style="stop-color:#ef4444;" />
                                                <stop offset="100%" style="stop-color:#f97316;" />
                                            </linearGradient>
                                            <linearGradient id="areaGradReveal" x1="0%" y1="0%" x2="0%" y2="100%">
                                                <stop offset="0%" style="stop-color:#ef4444;stop-opacity:0.25" />
                                                <stop offset="100%" style="stop-color:#ef4444;stop-opacity:0" />
                                            </linearGradient>
                                        </defs>
                                        <path d="M 0 80 Q 50 60 100 45 T 200 35 T 310 20 L 310 100 L 0 100 Z" fill="url(#areaGradReveal)"></path>
                                        <path class="growth-path-reveal" d="M 0 80 Q 50 60 100 45 T 200 35 T 310 20" stroke="url(#lineGradReveal)" stroke-width="4.5" fill="none" stroke-linecap="round"></path>
                                        <circle cx="0" cy="80" r="5.5" fill="#ef4444"></circle>
                                        <circle cx="100" cy="45" r="5.5" fill="#f97316"></circle>
                                        <circle cx="200" cy="35" r="5.5" fill="#f97316"></circle>
                                        <circle cx="310" cy="20" r="5.5" fill="#f97316"></circle>
                                    </svg>
                                </div>
                                <div class="zoom-card-footer">
                                    <span>Department Growth Trend</span>
                                    <span class="badge bg-success" style="background-color: #10b981 !important;">+24%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Metrics -->
                    <div class="col-lg-6 placement-right-col d-flex flex-column align-items-lg-end justify-content-center">
                        <div class="placement-metrics-reveal" style="width: 100%; max-width: 440px;">
                            <div class="revenue-display" style="text-align: right; margin-bottom: 40px;">
                                <div class="revenue-amount" style="font-size: 110px; font-weight: 700; color: #000; line-height: 1; letter-spacing: -0.02em;">₹5.1L</div>
                                <div class="revenue-label" style="font-size: 14px; color: #6b7280; margin-top: 5px; font-weight: 400;">Average CTC</div>
                            </div>
                            
                            <div class="metrics-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px 40px; width: 100%;">
                                <div class="metric-box-reveal">
                                    <div class="metric-value" style="font-size: 36px; font-weight: 700; color: #000; line-height: 1.2; margin-bottom: 5px;">500+</div>
                                    <div class="metric-description" style="font-size: 14px; color: #6b7280; font-weight: 400;">Students</div>
                                </div>
                                <div class="metric-box-reveal">
                                    <div class="metric-value" style="font-size: 36px; font-weight: 700; color: #000; line-height: 1.2; margin-bottom: 5px;">50+</div>
                                    <div class="metric-description" style="font-size: 14px; color: #6b7280; font-weight: 400;">Internships from 2nd year</div>
                                </div>
                                <div class="metric-box-reveal" style="grid-column: span 2;">
                                    <div class="metric-value" style="font-size: 36px; font-weight: 700; color: #000; line-height: 1.2; margin-bottom: 5px;">20+</div>
                                    <div class="metric-description" style="font-size: 14px; color: #6b7280; font-weight: 400;">Top Faculty</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Simple slide-in animation on scroll
        const observerSettings = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const scrollObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-in');
                }
            });
        }, observerSettings);

        document.querySelectorAll('.metric-box').forEach(element => {
            scrollObserver.observe(element);
        });
    </script>

    <!-- Department Highlights Section -->
    <section class="dept-highlights-section" style="padding: 60px 0; margin-top: -70px;">
        <div class="container">
            <div class="section-title text-center mb-4">
                <h2 style="color: #1e293b; font-size: 2.2rem; font-weight: 700; margin-bottom: 15px;">Best Practices of CSD & CSIT</h2>
                <p style="color: #64748b; font-size: 1rem; max-width: 600px; margin: 0 auto;">
                    Empowering students with software development, startup culture, research, and holistic learning.
                </p>
            </div>

            <div class="highlights-masonry" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 30px;">
                <!-- Large Feature Card -->
                <div class="highlight-card large-card" style="grid-row: span 2; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 15px; padding: 25px; position: relative; overflow: hidden;transition: all 0.3s ease;">
                    <div style="margin-top: 20px;">
                        <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 12px; line-height: 1.2;">50 Seated Software Development Centre</h3>
                        <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 20px; line-height: 1.4;">
                            20+ software applications developed by students with 50+ internships/live projects from startups.
                        </p>
                    </div>
                </div>

                <!-- Side Cards Column 1 -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="highlight-card" style="background: linear-gradient(135deg, #e0f2fe, #b3e5fc); color: #0277bd; border-radius: 12px; padding: 20px; transition: all 0.3s ease;">
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 10px;">Earn While Studying</h4>
                        <p style="font-size: 0.8rem; margin-bottom: 15px; line-height: 1.4;">
                            On-campus paid internships, consultancy projects, and part-time jobs in campus startups.
                        </p>
                    </div>

                    <div class="highlight-card" style="background: linear-gradient(135deg, #f3e5f5, #e1bee7); color: #7b1fa2; border-radius: 12px; padding: 20px;">
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 10px;">Strong Startup Culture</h4>
                        <p style="font-size: 0.8rem; margin-bottom: 15px; line-height: 1.4;">
                            3 startups owned by alumni with a strong 20+ member Startup Club executing on-campus ventures.
                        </p>
                    </div>

                    <div class="highlight-card" style="background: linear-gradient(135deg, #fff8e1, #ffecb3); color: #f57c00; border-radius: 12px; padding: 20px;">
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 10px;">Stress Free Education</h4>
                        <p style="font-size: 0.8rem; margin-bottom: 15px; line-height: 1.4;">
                            Learning by doing, joyful learning, and holistic development with focus on sports, culturals, and arts.
                        </p>
                    </div>
                </div>

                <!-- Side Cards Column 2 -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="highlight-card featured" style="background: linear-gradient(135deg, #1e293b 0%, #374151 100%); color: white; border-radius: 12px; padding: 25px; position: relative; overflow: hidden; transition: all 0.3s ease;">
                        <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 10px; margin-top: 15px;">Industry Connect</h3>
                        <p style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 18px; line-height: 1.4;">
                            Exclusive tie-ups with 6+ startups and MNCs, regular industrial visits, expert talks & interactions.
                        </p>
                    </div>

                    <div class="highlight-card" style="background: linear-gradient(135deg, #f0f9ff, #dbeafe); color: #1e40af; border-radius: 12px; padding: 20px; position: relative;">
                        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; margin-top: 15px;">Research & Consultancy</h4>
                        <p style="font-size: 0.8rem; margin-bottom: 15px; line-height: 1.4;">
                            Faculty hold funded research projects worth ₹1.2 crore and consultancy raised through startups.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Feature Section -->
            <div class="bottom-feature" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-radius: 20px; padding: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
                <div>
                    <h2 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin-bottom: 15px; line-height: 1.2;">Skill Development & Holistic Learning</h2>
                    <p style="font-size: 0.9rem; color: #4b5563; margin-bottom: 20px; line-height: 1.5;">
                        MOUs with Swecha AP, Wadhwani Foundation, AICTE IDEALab, KAIZEN, PurpleLane. Student-led clubs and houses focusing on emotional intelligence, leadership, design thinking, and entrepreneurship.
                    </p>
                </div>
                <div style="position: relative;">
                    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                <i class="fas fa-users" style="color: white; font-size: 16px;"></i>
                            </div>
                            <h4 style="margin: 0; color: #1e293b; font-weight: 700; font-size: 1rem;">Skills Focused</h4>
                        </div>
                        <p style="color: #6b7280; margin: 0; line-height: 1.4; font-size: 0.8rem;">
                            Creativity, design & development, critical thinking, innovation, problem-solving, leadership, and self-learning.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="hero-section" style="flex: 1; padding: 150px; margin-top: -180px; pointer-events: none;">
        <div class="hero-content">


            <h1 class="hero-title">
                Where Talent Meets <span class="highlight">Competition</span>
            </h1>

            <div class="hero-buttons" style="pointer-events: auto;">
                <a href="houses_dashboard.php" class="btn btn-primary">
                    <i class="fas fa-trophy"></i> House Activites </a>
                <a href="heroes_of_department.php" class="btn" style="background: #7c3aed; color: white;">
                    <i class="fas fa-user-shield me-1"></i> Class Representatives
                </a>
                <a href="students_overview.php" class="btn" style="background: #f1f5f9; color: #475569;">
                    Students Overview
                </a>
            </div>
        </div>

        <!-- Houses Section -->
        <section class="houses-section" style="flex: 1;">
            <div class="houses-container">
                <!-- Center logo -->
                <div class="center-logo" onclick="window.location.href='houses_dashboard.php'" style="cursor: pointer;" title="Open House Activities">
                    <img src="./assets/logos/allhouses.webp" alt="All Houses - Click for House Activities">
                </div>

                <!-- Orbiting Ring container -->
                <div class="houses-orbit-ring">
                    <!-- Rotating circle border -->
                    <div class="houses-circle"></div>

                    <!-- Connecting lines (pointer-events disabled so they never block house clicks) -->
                    <div class="connecting-lines" style="pointer-events: none;">
                        <div class="line" style="width: 180px; transform: rotate(-90deg); pointer-events: none;"></div>
                        <div class="line" style="width: 180px; transform: rotate(-18deg); pointer-events: none;"></div>
                        <div class="line" style="width: 180px; transform: rotate(54deg); pointer-events: none;"></div>
                        <div class="line" style="width: 180px; transform: rotate(126deg); pointer-events: none;"></div>
                        <div class="line" style="width: 180px; transform: rotate(198deg); pointer-events: none;"></div>
                    </div>

                    <!-- House items (AFTER connecting-lines so they are on top in stacking order) -->
                    <a href="house_detail.php?house=Agni" class="house-item house-agni" style="cursor: pointer; text-decoration: none; display: block; z-index: 100;">
                        <div class="house-button">
                            <img src="./assets/logos/3.jpg" alt="Agni">
                        </div>
                        <div class="house-name">Agni</div>
                    </a>

                    <a href="house_detail.php?house=Vayu" class="house-item house-vayu" style="cursor: pointer; text-decoration: none; display: block; z-index: 100;">
                        <div class="house-button">
                            <img src="./assets/logos/2.jpg" alt="Vayu">
                        </div>
                        <div class="house-name">Vayu</div>
                    </a>

                    <a href="house_detail.php?house=Prudhvi" class="house-item house-prudhvi" style="cursor: pointer; text-decoration: none; display: block; z-index: 100;">
                        <div class="house-button">
                            <img src="./assets/logos/4.jpg" alt="Prudhvi">
                        </div>
                        <div class="house-name">Prudhvi</div>
                    </a>

                    <a href="house_detail.php?house=Jal" class="house-item house-jal" style="cursor: pointer; text-decoration: none; display: block; z-index: 100;">
                        <div class="house-button">
                            <img src="./assets/logos/1.jpg" alt="Jal">
                        </div>
                        <div class="house-name">Jal</div>
                    </a>

                    <a href="house_detail.php?house=Aakash" class="house-item house-aakash" style="cursor: pointer; text-decoration: none; display: block; z-index: 100;">
                        <div class="house-button">
                            <img src="./assets/logos/5.jpg" alt="Aakash">
                        </div>
                        <div class="house-name">Aakash</div>
                    </a>
                </div>
            </div>
        </section>
    </section>


    <!-- Clubs and Activities Section -->
    <section class="clubs-activities-section" style="padding: 80px 0; position: relative; overflow: hidden; margin-top: -220px;">
        <!-- Background Decorative Elements -->
        <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; opacity: 0.6;"></div>
        <div style="position: absolute; bottom: -75px; right: -75px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-title text-center" style="margin-bottom: 60px;">
                <p style=" font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;">Student Life</p>
                <h2 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 15px; line-height: 1.1;">Clubs & <span style="color: #ffd700;">Activities</span></h2>
                <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.5;">Join our vibrant community through diverse student-led clubs and activities</p>
            </div>

            <!-- Clubs Grid -->
            <div class="clubs-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; max-width: 1400px; margin: 0 auto;">

                <!-- Startup Club -->
                <div class="club-card" style="border-radius: 20px; padding: 30px; text-align: center; border: #000 1px solid; transition: all 0.3s ease; backdrop-filter: blur(15px);">

                    <h3 style="color: #1e293b; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Startup Club</h3>
                    <h4 style="color: #19547b; font-size: 1rem; font-weight: 600; margin-bottom: 15px;">Entrepreneurship Hub</h4>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Turn ideas into reality with mentors, investors, and entrepreneurs.</p>

                </div>

                <!-- SDC Club -->
                <div class="club-card" style="border-radius: 20px; padding: 30px; text-align: center; border: #000 1px solid; transition: all 0.3s ease; backdrop-filter: blur(15px);">

                    <h3 style="color: #1e293b; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">SDC</h3>
                    <h4 style="color: #667eea; font-size: 1rem; font-weight: 600; margin-bottom: 15px;">Software Development</h4>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Build innovative solutions with cutting-edge technologies and real-world projects.</p>

                </div>

                <!-- CDC Club -->
                <div class="club-card" style="border-radius: 20px; padding: 30px; text-align: center; border: #000 1px solid; transition: all 0.3s ease; backdrop-filter: blur(15px);">

                    <h3 style="color: #1e293b; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">CDC</h3>
                    <h4 style="color: #f093fb; font-size: 1rem; font-weight: 600; margin-bottom: 15px;">Content Development</h4>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Create compelling content, design graphics, and manage digital platforms.</p>

                </div>

                <!-- Swecha Club -->
                <div class="club-card" style="border-radius: 20px; padding: 30px; text-align: center; border: #000 1px solid; transition: all 0.3s ease; backdrop-filter: blur(15px);">

                    <h3 style="color: #1e293b; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Swecha</h3>
                    <h4 style="color: #4facfe; font-size: 1rem; font-weight: 600; margin-bottom: 15px;">Free Software Movement</h4>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Promote open source technologies and contribute to digital freedom.</p>

                </div>

                <!-- IEI Club -->
                <div class="club-card" style="border-radius: 20px; padding: 30px; text-align: center; border: #000 1px solid; transition: all 0.3s ease; backdrop-filter: blur(15px);">

                    <h3 style="color: #1e293b; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">IEI</h3>
                    <h4 style="color: #ff6b6b; font-size: 1rem; font-weight: 600; margin-bottom: 15px;">Institution of Engineers</h4>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">Professional engineering society fostering technical excellence.</p>

                </div>

            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 60px;">
                <p style="font-size: 1.1rem; margin-bottom: 25px; font-style: italic;">Ready to make your mark? Join a club that matches your passion!</p>
                <!-- <a href="#" style="display: inline-flex; align-items: center; background: rgba(0, 0, 0, 0.2); color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px);">
                    <span>Explore All Clubs</span>
                    <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 0.8rem;"></i>
                </a> -->
            </div>
        </div>

        <style>
            .club-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35) !important;
            }

            .club-card:hover .fas,
            .club-card:hover .fab {
                transform: scale(1.1);
            }

            @media (max-width: 1200px) {
                .clubs-grid {
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
                }
            }

            @media (max-width: 768px) {
                .clubs-grid {
                    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
                    gap: 15px !important;
                }

                .club-card {
                    padding: 25px !important;
                }
            }

            @media (max-width: 576px) {
                .clubs-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </section>



    <!-- Recent News & Events -->
    <!-- <section class="news-events-section" style="padding: 80px 0; margin-top: -100px;">
        <div class="container">
            <div class="section-title">
                <h2>Latest News & Events</h2>
                <p>Stay updated with the latest happenings in our department</p>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <div class="news-card" style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #dc2626; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <div style="width: 8px; height: 8px; background: #dc2626; border-radius: 50%;"></div>
                            <span style="color: #dc2626; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Breaking</span>
                            <span style="color: #94a3b8; font-size: 0.7rem;">• 2 hours ago</span>
                        </div>
                        <h5 style="color: #1e293b; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">Students Win National Hackathon</h5>
                        <p style="color: #64748b; font-size: 0.8rem; margin: 0; line-height: 1.4;">Team secured first place in AI Hackathon 2024, competing against 200+ teams nationwide.</p>
                    </div>
                </div>

                <div class="col mb-3">
                    <div class="news-card" style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #059669; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <div style="width: 8px; height: 8px; background: #059669; border-radius: 50%;"></div>
                            <span style="color: #059669; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Event</span>
                            <span style="color: #94a3b8; font-size: 0.7rem;">• April 20, 2024</span>
                        </div>
                        <h5 style="color: #1e293b; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">Tech Symposium 2024</h5>
                        <p style="color: #64748b; font-size: 0.8rem; margin: 0; line-height: 1.4;">Annual technical symposium featuring industry speakers and innovation showcase.</p>
                    </div>
                </div>

                <div class="col mb-3">
                    <div class="news-card" style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #3b82f6; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%;"></div>
                            <span style="color: #3b82f6; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Research</span>
                            <span style="color: #94a3b8; font-size: 0.7rem;">• March 28, 2024</span>
                        </div>
                        <h5 style="color: #1e293b; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">New Research Publication</h5>
                        <p style="color: #64748b; font-size: 0.8rem; margin: 0; line-height: 1.4;">Faculty publishes breakthrough research in machine learning at top-tier conference.</p>
                    </div>
                </div>

                <div class="col mb-3">
                    <div class="news-card" style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #f59e0b; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div>
                            <span style="color: #f59e0b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Industry</span>
                            <span style="color: #94a3b8; font-size: 0.7rem;">• March 25, 2024</span>
                        </div>
                        <h5 style="color: #1e293b; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">New Industry Partnership</h5>
                        <p style="color: #64748b; font-size: 0.8rem; margin: 0; line-height: 1.4;">Strategic partnership with tech giant for internships and collaborative projects.</p>
                    </div>
                </div>

                <div class="col mb-3">
                    <div class="news-card" style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #8b5cf6; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <div style="width: 8px; height: 8px; background: #8b5cf6; border-radius: 50%;"></div>
                            <span style="color: #8b5cf6; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Alumni</span>
                            <span style="color: #94a3b8; font-size: 0.7rem;">• March 20, 2024</span>
                        </div>
                        <h5 style="color: #1e293b; font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">Alumni Success Story</h5>
                        <p style="color: #64748b; font-size: 0.8rem; margin: 0; line-height: 1.4;">Graduate lands prestigious role at Google, inspiring current students.</p>
                    </div>
                </div>
            </div>

            <style>
                .news-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
                }

                @media (max-width: 1200px) {
                    .row .col {
                        flex: 0 0 50%;
                        max-width: 50%;
                    }
                }

                @media (max-width: 768px) {
                    .row .col {
                        flex: 0 0 100%;
                        max-width: 100%;
                    }
                }
            </style>
        </div>
    </section> -->


    <!-- Photo Gallery Section -->
    <section class="photo-gallery-section" style="padding: 80px 0; margin-top: -80px;">
        <div class="container">
            <!-- Header with Right-Side Purple "See All Images" Button -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
                <div class="text-start">
                    <h2 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 8px; line-height: 1.1;">Moments & <span style="color: #3b82f6;">Memories</span></h2>
                    <p style="font-size: 1.1rem; color: #64748b; margin: 0;">A glimpse into the vibrant life of our department through photos</p>
                </div>
                <div>
                    <button class="see-all-photos-btn" data-bs-toggle="modal" data-bs-target="#allPhotosModal">
                        <i class="fas fa-images"></i>
                        <span>See All Images</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="gallery-carousel-wrapper" style="overflow: hidden; margin-bottom: 40px;">
                <!-- First Row -->
                <div class="gallery-carousel-row" style="display: flex; animation: gallery-scroll-left 25s linear infinite; margin-bottom: 20px; gap: 15px;">
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/1.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/1.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/2.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/2.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/3.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/3.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/4.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/4.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/5.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/5.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <!-- Duplicate items for seamless loop -->
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/6.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/6.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/9.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/9.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                </div>

                <!-- Second Row -->
                <div class="gallery-carousel-row" style="display: flex; animation: gallery-scroll-right 25s linear infinite; margin-bottom: 20px; gap: 15px;">
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/10.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/10.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/11.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/11.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/12.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/12.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/13.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/13.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/14.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/14.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <!-- Duplicate items for seamless loop -->
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/17.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/17.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/18.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/18.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                </div>

                <!-- Third Row -->
                <div class="gallery-carousel-row" style="display: flex; animation: gallery-scroll-left 25s linear infinite; gap: 15px;">
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/19.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/19.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/20.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/20.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/21.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/21.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/1.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/1.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/2.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/2.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <!-- Duplicate items for seamless loop -->
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/3.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/3.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="gallery-item" onclick="openSinglePhotoModal('./assets/memories/4.jpg')" style="position: relative; overflow: hidden; border-radius: 12px; cursor: pointer; min-width: 300px; height: 200px; transition: all 0.3s ease;">
                        <img src="./assets/memories/4.jpg" alt="Department Memory" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                </div>
            </div>

            <style>
                /* Matching Purple Theme See All Images Button */
                .see-all-photos-btn {
                    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
                    color: #ffffff !important;
                    border: none !important;
                    padding: 13px 28px !important;
                    border-radius: 50px !important;
                    font-family: 'Outfit', sans-serif !important;
                    font-size: 1.02rem !important;
                    font-weight: 700 !important;
                    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35) !important;
                    cursor: pointer !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 10px !important;
                    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
                    text-decoration: none !important;
                }

                .see-all-photos-btn:hover {
                    background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%) !important;
                    transform: translateY(-3px) scale(1.03) !important;
                    box-shadow: 0 15px 35px rgba(124, 58, 237, 0.5) !important;
                    color: #ffffff !important;
                }

                .see-all-photos-btn i {
                    font-size: 0.95rem;
                    transition: transform 0.3s ease !important;
                }

                .see-all-photos-btn:hover i.fa-arrow-right {
                    transform: translateX(4px) !important;
                }

                @keyframes gallery-scroll-left {
                    0% {
                        transform: translateX(0);
                    }

                    100% {
                        transform: translateX(-50%);
                    }
                }

                @keyframes gallery-scroll-right {
                    0% {
                        transform: translateX(-50%);
                    }

                    100% {
                        transform: translateX(0);
                    }
                }

                .gallery-carousel-row:hover {
                    animation-play-state: paused;
                }

                .gallery-item:hover {
                    transform: translateY(-10px) scale(1.1);
                    z-index: 10;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                }

                .gallery-item:hover img {
                    transform: scale(1.1);
                }

                @media (max-width: 768px) {
                    .gallery-item {
                        min-width: 250px !important;
                        height: 180px !important;
                    }
                }
            </style>
        </div>

        <!-- Full Gallery Lightbox Modal -->
        <div class="modal fade" id="allPhotosModal" tabindex="-1" aria-labelledby="allPhotosModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);">
                    <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; padding: 20px 30px; border-bottom: none;">
                        <h5 class="modal-title" id="allPhotosModalLabel" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.5rem;">
                            <i class="fas fa-camera-retro me-2"></i> Department Memories Gallery (All Images)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background: #fdfbf7; padding: 30px;">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                            <?php 
                            $all_photos = [1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 13, 14, 17, 18, 19, 20, 21];
                            foreach ($all_photos as $p):
                            ?>
                                <div class="col">
                                    <div class="gallery-modal-card" onclick="openSinglePhotoModal('./assets/memories/<?php echo $p; ?>.jpg')" style="border-radius: 16px; overflow: hidden; position: relative; cursor: pointer; aspect-ratio: 4/3; box-shadow: 0 6px 18px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                        <img src="./assets/memories/<?php echo $p; ?>.jpg" alt="Memory <?php echo $p; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                                        <div class="gallery-modal-overlay" style="position: absolute; inset: 0; background: rgba(124, 58, 237, 0.65); opacity: 0; display: flex; align-items: center; justify-content: center; color: white; transition: all 0.3s ease;">
                                            <i class="fas fa-search-plus" style="font-size: 1.8rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Image Zoom Lightbox Modal -->
        <div class="modal fade" id="singlePhotoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="background: transparent; border: none;">
                    <div class="modal-body text-center p-0 position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index: 1055; background-color: rgba(0,0,0,0.5); padding: 10px; border-radius: 50%;"></button>
                        <img id="zoomPhotoImg" src="" alt="Enlarged Memory" style="max-width: 100%; max-height: 85vh; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>

        <script>
        function openSinglePhotoModal(imgSrc) {
            document.getElementById('zoomPhotoImg').src = imgSrc;
            const singleModal = new bootstrap.Modal(document.getElementById('singlePhotoModal'));
            singleModal.show();
        }
        </script>
        <style>
            .gallery-modal-card:hover {
                transform: translateY(-5px) scale(1.03);
                box-shadow: 0 15px 35px rgba(124, 58, 237, 0.3) !important;
            }
            .gallery-modal-card:hover .gallery-modal-overlay {
                opacity: 1 !important;
            }
            .gallery-modal-card:hover img {
                transform: scale(1.1);
            }
        </style>
        <style>
            .gallery-item:hover img {
                transform: scale(1.1);
            }
        </style>

    <!-- Scroll to Top Styles -->
    <style>
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: rgba(0,0,0,0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .progress-circle {
            position: absolute;
            top: 0;
            left: 0;
        }

        .progress-bar {
            transition: stroke-dashoffset 0.3s ease;
            transform-origin: center;
            transform: rotate(-90deg);
        }

        .scroll-to-top i {
            color: white;
            font-size: 20px;
            z-index: 1;
            position: relative;
        }

        .scroll-to-top:hover {
            background: rgba(0,0,0,0.9);
            transform: scale(1.1);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .scroll-to-top.bounce {
            animation: bounce 0.6s ease;
        }
    </style>



    <?php include "footer.php"; ?>

    <!-- Scroll to Top Button -->
    <div id="scroll-to-top" class="scroll-to-top">
        <svg class="progress-circle" width="60" height="60">
            <circle cx="30" cy="30" r="25" stroke="#e0e0e0" stroke-width="4" fill="none"></circle>
            <circle cx="30" cy="30" r="25" stroke="#007bff" stroke-width="4" fill="none" stroke-dasharray="157" stroke-dashoffset="157" class="progress-bar"></circle>
        </svg>
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // Add hover effects for company cards
        document.querySelectorAll('.company-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add hover effects for startup cards
        document.querySelectorAll('.startup-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Tab switching functionality
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and panes
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                    btn.style.background = '#e2e8f0';
                    btn.style.color = '#64748b';
                    btn.style.borderBottom = 'none';
                });

                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active');
                    pane.style.display = 'none';
                });

                // Add active class to clicked button
                this.classList.add('active');
                this.style.background = '#16a085';
                this.style.color = 'white';
                this.style.borderBottom = '3px solid #16a085';

                // Show corresponding tab content
                const tabId = this.getAttribute('data-tab') + '-tab';
                const targetTab = document.getElementById(tabId);
                if (targetTab) {
                    targetTab.classList.add('active');
                    targetTab.style.display = 'block';
                }
            });
        });

        // Know More button hover effect
        document.querySelectorAll('.know-more-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.color = '#0d9488';
                const arrow = this.querySelector('i');
                if (arrow) arrow.style.transform = 'translateX(5px)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.color = '#16a085';
                const arrow = this.querySelector('i');
                if (arrow) arrow.style.transform = 'translateX(0)';
            });
        });

        // Highlight cards hover effects
        document.querySelectorAll('.highlight-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const arrows = this.querySelectorAll('.fas.fa-arrow-right');
                arrows.forEach(arrow => {
                    arrow.style.transform = 'translateX(5px)';
                });
            });
            card.addEventListener('mouseleave', function() {
                const arrows = this.querySelectorAll('.fas.fa-arrow-right');
                arrows.forEach(arrow => {
                    arrow.style.transform = 'translateX(0)';
                });
            });
        });

        // Bottom feature button hover
        document.querySelector('.bottom-feature [style*="background: #22c55e"]')?.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.background = '#16a34a';
        });

        document.querySelector('.bottom-feature [style*="background: #22c55e"]')?.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.background = '#22c55e';
        });

        // Add animation for statistics
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-item h3');
            statNumbers.forEach(stat => {
                const finalNumber = stat.textContent;
                let currentNumber = 0;
                const increment = parseInt(finalNumber) / 100;

                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= parseInt(finalNumber)) {
                        stat.textContent = finalNumber;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(currentNumber) + (finalNumber.includes('%') ? '%' : finalNumber.includes('+') ? '+' : finalNumber.includes('₹') ? 'L' : '');
                    }
                }, 20);
            });
        }

        // Trigger animation when stats section is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Scroll to Top Functionality
        const scrollButton = document.getElementById('scroll-to-top');
        const progressBar = document.querySelector('.progress-bar');

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            const offset = 157 - (157 * scrollPercent / 100);
            progressBar.style.strokeDashoffset = offset;

            if (scrollTop > 100) {
                scrollButton.classList.add('show');
            } else {
                scrollButton.classList.remove('show');
            }

            // Bottom animation - bounce when reaching bottom
            if (scrollTop + window.innerHeight >= document.documentElement.scrollHeight - 10) {
                scrollButton.classList.add('bounce');
                setTimeout(() => {
                    scrollButton.classList.remove('bounce');
                }, 600);
            }
        });

        scrollButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ==========================================
        // SPLIT SCROLL REVEAL OBSERVER
        // ==========================================
        window.switchPillarStep = function(stepIndex) {
            const showcaseSection = document.querySelector('.reveal-showcase-section');
            if (!showcaseSection) return;

            const sectionRect = showcaseSection.getBoundingClientRect();
            const sectionHeight = sectionRect.height;
            const viewportHeight = window.innerHeight;
            const scrollableDist = sectionHeight - viewportHeight;

            if (scrollableDist > 0) {
                const currentScrollY = window.pageYOffset || document.documentElement.scrollTop;
                const sectionTopDoc = currentScrollY + sectionRect.top;
                const targetScroll = sectionTopDoc + (stepIndex / 3) * scrollableDist;
                window.scrollTo({ top: targetScroll, behavior: 'smooth' });
            }
        };

        const initScrollReveal = () => {
            const showcaseSection = document.querySelector('.reveal-showcase-section');
            const scrollItems = document.querySelectorAll('.showcase-scroll-item');
            const graphicItems = document.querySelectorAll('.showcase-graphic-item');
            const stickyGraphic = document.querySelector('.showcase-sticky-graphic');
            const pillarTabs = document.querySelectorAll('.btn-pillar-tab');
            
            if (!showcaseSection || scrollItems.length === 0 || graphicItems.length === 0) return;

            function updateScrollReveal() {
                // If screen width is less than 992px, reset classes and return
                if (window.innerWidth < 992) {
                    scrollItems.forEach(c => c.classList.remove('active', 'exit'));
                    graphicItems.forEach(g => g.classList.remove('active'));
                    return;
                }

                const sectionRect = showcaseSection.getBoundingClientRect();
                const sectionHeight = sectionRect.height;
                const viewportHeight = window.innerHeight;

                const scrollStart = sectionRect.top;
                const scrollableDist = sectionHeight - viewportHeight;

                if (scrollableDist <= 0) return;

                let progress = -scrollStart / scrollableDist;
                progress = Math.max(0, Math.min(1, progress));

                // Determine active step
                const totalSteps = 4;
                let activeStep = Math.floor(progress * totalSteps);
                if (activeStep >= totalSteps) activeStep = totalSteps - 1;

                // Sync Tab Buttons Active State
                pillarTabs.forEach((tab, idx) => {
                    if (idx === activeStep) tab.classList.add('active');
                    else tab.classList.remove('active');
                });

                // 2. Set step-X classes on the sticky graphic container for background shift
                if (stickyGraphic) {
                    stickyGraphic.className = `showcase-sticky-graphic step-${activeStep}`;
                }

                // 3. Toggle active/exit classes for horizontal slide conveyor
                scrollItems.forEach((el, idx) => {
                    el.classList.remove('active', 'exit');
                    if (idx === activeStep) {
                        el.classList.add('active');
                    } else if (idx < activeStep) {
                        el.classList.add('exit');
                    }
                });

                graphicItems.forEach((el, idx) => {
                    if (idx === activeStep) el.classList.add('active');
                    else el.classList.remove('active');
                });

                // 4. Rotate houses orbit ring dynamically based on progress in step 1 (progress: 0.25 to 0.5)
                const orbitRing = document.querySelector('.showcase-houses-orbit-ring');
                if (orbitRing) {
                    const stepProgress = (progress - 0.25) / 0.25;
                    const clampedProgress = Math.max(0, Math.min(1, stepProgress));
                    const angle = clampedProgress * 180; // Rotate up to 180 degrees
                    orbitRing.style.transform = `rotate(${angle}deg)`;
                    
                    const houseNodes = orbitRing.querySelectorAll('.showcase-house-node');
                    houseNodes.forEach(node => {
                        const img = node.querySelector('img');
                        if (img) img.style.transform = `rotate(${-angle}deg)`;
                    });
                }

                // 5. Draw placements path chart dynamically based on progress in step 2 (progress: 0.5 to 0.75)
                const chartPath = document.querySelector('.sp-chart-path');
                const chartTip = document.querySelector('.sp-chart-tip');
                if (chartPath && chartTip) {
                    const stepProgress = (progress - 0.5) / 0.25;
                    const clampedProgress = Math.max(0, Math.min(1, stepProgress));

                    const pathLength = 600;
                    chartPath.style.strokeDasharray = pathLength;
                    chartPath.style.strokeDashoffset = pathLength * (1 - clampedProgress);

                    try {
                        const actualLength = chartPath.getTotalLength ? chartPath.getTotalLength() : pathLength;
                        const point = chartPath.getPointAtLength(actualLength * Math.min(0.98, clampedProgress));
                        chartTip.setAttribute('cx', point.x);
                        chartTip.setAttribute('cy', point.y);
                    } catch(err) {
                        const posX = 10 + 380 * clampedProgress;
                        const posY = 120 - 105 * Math.sin(clampedProgress * Math.PI / 2);
                        chartTip.setAttribute('cx', posX);
                        chartTip.setAttribute('cy', posY);
                    }
                }
            }

            window.addEventListener('scroll', updateScrollReveal);
            window.addEventListener('resize', updateScrollReveal);
            updateScrollReveal(); // Run initial calculation on page load
        };

        // ==========================================
        // PLACEMENTS ZOOM-REVEAL OBSERVER
        // ==========================================
        const initPlacementZoomReveal = () => {
            const scrollContainer = document.querySelector('.placement-scroll-container');
            const zoomCard = document.querySelector('.placement-zoom-card');
            const cardWrapper = document.querySelector('.placement-zoom-card-wrapper');
            const textReveal = document.querySelector('.placement-text-reveal');
            const metricsReveal = document.querySelector('.placement-metrics-reveal');
            const metricBoxes = document.querySelectorAll('.metric-box-reveal');
            const pathReveal = document.querySelector('.growth-path-reveal');
            
            if (!scrollContainer || !zoomCard || !cardWrapper) return;

            let tx = 0;
            let ty = 0;

            function calculateOffsets() {
                // Save current transform to restore after measurement
                const originalTransform = zoomCard.style.transform;
                zoomCard.style.transform = 'none';

                const viewportCenterX = window.innerWidth / 2;
                const viewportCenterY = window.innerHeight / 2;

                const wrapperRect = cardWrapper.getBoundingClientRect();
                const containerRect = scrollContainer.getBoundingClientRect();

                // Compute exact position of card relative to container (static layout coordinates)
                const stickyCardCenterX = (wrapperRect.left - containerRect.left) + wrapperRect.width / 2;
                const stickyCardCenterY = (wrapperRect.top - containerRect.top) + wrapperRect.height / 2;

                tx = viewportCenterX - stickyCardCenterX;
                ty = viewportCenterY - stickyCardCenterY;

                // Restore original transform
                zoomCard.style.transform = originalTransform;
            }

            function setProgressStyle(el, progress, start, end, translateYStart = 30) {
                if (!el) return;
                let p = (progress - start) / (end - start);
                p = Math.max(0, Math.min(1, p));
                
                const opacity = p;
                const ty = translateYStart * (1 - p);
                el.style.opacity = opacity;
                el.style.transform = `translateY(${ty}px)`;
            }

            function updatePlacementReveal() {
                if (window.innerWidth < 992) {
                    // Reset mobile overrides
                    zoomCard.style.transform = '';
                    if (textReveal) { textReveal.style.opacity = ''; textReveal.style.transform = ''; }
                    if (metricsReveal) { metricsReveal.style.opacity = ''; metricsReveal.style.transform = ''; }
                    metricBoxes.forEach(box => { box.style.opacity = ''; box.style.transform = ''; });
                    if (pathReveal) {
                        pathReveal.style.strokeDasharray = '';
                        pathReveal.style.strokeDashoffset = '';
                    }
                    return;
                }

                const containerRect = scrollContainer.getBoundingClientRect();
                const containerHeight = containerRect.height;
                const viewportHeight = window.innerHeight;

                const scrollStart = containerRect.top;
                const scrollableDist = containerHeight - viewportHeight;

                if (scrollableDist <= 0) return;

                let progress = -scrollStart / scrollableDist;
                progress = Math.max(0, Math.min(1, progress));

                // 1. Zoom Out and Translate Card (using cached static offsets)
                let zoomProgress = progress / 0.6; // zoom ends at 60%
                zoomProgress = Math.max(0, Math.min(1, zoomProgress));

                const easedZoom = 1 - Math.pow(1 - zoomProgress, 3); // cubic ease-out

                const currentScale = 3.0 - (3.0 - 1.0) * easedZoom;
                const currentTx = tx * (1 - easedZoom);
                const currentTy = ty * (1 - easedZoom);

                zoomCard.style.transform = `translate(${currentTx}px, ${currentTy}px) scale(${currentScale})`;

                // 2. Staggered Reveals
                setProgressStyle(textReveal, progress, 0.45, 0.75);
                setProgressStyle(metricsReveal, progress, 0.45, 0.75);
                
                // Stagger individual metric boxes
                metricBoxes.forEach((box, idx) => {
                    const startRange = 0.55 + idx * 0.08;
                    const endRange = startRange + 0.25;
                    setProgressStyle(box, progress, startRange, endRange, 20);
                });

                // 3. Draw Path on Scroll
                if (pathReveal) {
                    let drawProgress = (progress - 0.5) / 0.35; // draws between 50% and 85%
                    drawProgress = Math.max(0, Math.min(1, drawProgress));
                    const pathLength = 400;
                    pathReveal.style.strokeDasharray = pathLength;
                    pathReveal.style.strokeDashoffset = pathLength * (1 - drawProgress);
                }
            }

            // Calculate static offsets initially and on window resize
            calculateOffsets();
            window.addEventListener('resize', () => {
                calculateOffsets();
                updatePlacementReveal();
            });

            window.addEventListener('scroll', updatePlacementReveal);
            updatePlacementReveal();
        };

        const initParticles = () => {
            const canvas = document.getElementById('particleCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;
            
            window.addEventListener('resize', () => {
                if (canvas.offsetWidth && canvas.offsetHeight) {
                    width = canvas.width = canvas.offsetWidth;
                    height = canvas.height = canvas.offsetHeight;
                }
            });
            
            const particles = [];
            const particleCount = 40;
            
            class Particle {
                constructor() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.size = Math.random() * 2 + 1;
                    this.xSpeed = Math.random() * 0.4 - 0.2;
                    this.ySpeed = Math.random() * 0.4 - 0.2;
                    this.opacity = Math.random() * 0.5 + 0.2;
                }
                
                update() {
                    this.x += this.xSpeed;
                    this.y += this.ySpeed;
                    
                    if (this.x < 0) this.x = width;
                    if (this.x > width) this.x = 0;
                    if (this.y < 0) this.y = height;
                    if (this.y > height) this.y = 0;
                }
                
                draw() {
                    ctx.fillStyle = `rgba(16, 185, 129, ${this.opacity})`;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
            
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
            
            const animate = () => {
                ctx.clearRect(0, 0, width, height);
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                requestAnimationFrame(animate);
            };
            
            animate();
        };

        const initExploreBtn = () => {
            const exploreBtn = document.getElementById('exploreBtn') || document.querySelector('.hero-explore-button');
            if (!exploreBtn) return;
            
            exploreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Smoothly scroll down to the Department Pillars & Industry Partners section
                const targetSection = document.getElementById('pillars-section') || document.querySelector('.pillars-section');
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initParticles();
                initExploreBtn();
                initScrollReveal();
                initPlacementZoomReveal();
            });
        } else {
            initParticles();
            initExploreBtn();
            initScrollReveal();
            initPlacementZoomReveal();
        }
    <!-- Interactive 3D Lanyard Badge Script -->
    <script src="assets/js/lanyard.js"></script>
    <!-- Interactive ReactBits DepthCarousel Component Engine -->
    <script src="assets/js/depth-carousel.js"></script>
    <!-- Interactive ReactBits ScrollStack Component Engine -->
    <script src="assets/js/scroll-stack.js"></script>

</body>

</html>