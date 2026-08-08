<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<link rel="stylesheet" href="./assets/css/orbit-images.css">
<style>
body {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    background: #f8fafc;
    color: #334155;
}

.hero-section {
    position: relative;
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #0f172a;
    color: white;
    padding: 100px 20px 80px;
    overflow: hidden;
}

.hero-bg-video {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: 0;
    transform: translate(-50%, -50%);
    object-fit: cover;
    opacity: 0.92;
    pointer-events: none;
}

.hero-video-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.35) 0%, rgba(15, 23, 42, 0.55) 100%);
    z-index: 1;
    pointer-events: none;
}

.hero-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.2;
    z-index: 1;
    pointer-events: none;
}

.hero-badge-container {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(251, 191, 36, 0.2);
    border: 1px solid rgba(251, 191, 36, 0.5);
    color: #fbbf24;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-size: 0.85rem;
    padding: 8px 22px;
    border-radius: 50px;
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.hero-main-title {
    font-family: 'Outfit', sans-serif;
    font-size: clamp(2.8rem, 6vw, 4.5rem);
    font-weight: 900;
    margin-bottom: 20px;
    color: #ffffff;
    line-height: 1.1;
    letter-spacing: -0.5px;
    text-shadow: 0 4px 20px rgba(0,0,0,0.6);
}

.hero-main-subtitle {
    font-size: clamp(1.1rem, 2vw, 1.35rem);
    color: #f1f5f9;
    max-width: 720px;
    margin: 0 auto 36px;
    font-weight: 500;
    line-height: 1.6;
    text-shadow: 0 2px 10px rgba(0,0,0,0.6);
}

.hero-lightbulb-box {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.35);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(12px);
    border: 1.5px solid rgba(251, 191, 36, 0.5);
    box-shadow: 0 0 35px rgba(251, 191, 36, 0.4);
    margin-bottom: 24px;
}

.scroll-down-hint {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    color: rgba(255, 255, 255, 0.85);
    font-size: 1.5rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
    40% { transform: translate(-50%, -10px); }
    60% { transform: translate(-50%, -5px); }
}

.startup-card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.startup-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(79, 70, 229, 0.12);
    border-color: rgba(79, 70, 229, 0.3);
}

.impact-card {
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    color: #ffffff;
    padding: 35px 25px;
    border-radius: 24px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(79, 70, 229, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.impact-num {
    font-family: 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 900;
    color: #fbbf24;
    line-height: 1;
    margin-bottom: 4px;
}

.impact-label {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    opacity: 0.95;
    margin: 0;
}

.program-stage {
    background: white;
    color: #1e293b;
    padding: 25px;
    border-radius: 20px;
    margin-bottom: 25px;
    text-align: center;
    border: 2px solid;
    transition: all 0.3s ease;
}

.program-stage:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Full-Screen 100vh High-Clarity Background Video Hero Section -->
    <section class="hero-section">
        <video autoplay loop muted playsinline class="hero-bg-video">
            <source src="assets/videos/web.mp4" type="video/mp4">
        </video>
        <div class="hero-video-overlay"></div>

        <div class="container position-relative text-center" style="z-index: 2;">
            <div class="hero-lightbulb-box">
                <i class="fas fa-lightbulb" style="font-size: 45px; color: #fbbf24; filter: drop-shadow(0 0 15px rgba(251, 191, 36, 0.8));"></i>
            </div>
            <br>
            <div class="hero-badge-container">
                <i class="fas fa-rocket"></i> Student Innovation Hub
            </div>
            <h1 class="hero-main-title">
                Startup <span style="color: #fbbf24;">Club</span>
            </h1>
            <p class="hero-main-subtitle">
                Empowering student entrepreneurs to build innovative solutions, access seed mentorship, and launch real-world ventures.
            </p>
            <div class="d-flex align-items-center justify-content-center">
                <a href="#about-startup" class="btn px-5 py-3 rounded-pill fw-bold" style="background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: #0f172a; font-size: 1.05rem; border: none; box-shadow: 0 10px 25px rgba(251, 191, 36, 0.4);">
                    Explore More <i class="fas fa-arrow-down ms-2"></i>
                </a>
            </div>
        </div>

        <a href="#about-startup" class="scroll-down-hint">
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    <!-- Club Overview -->
    <section id="about-startup" style="padding: 80px 0;">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-md-8">
                    <div class="startup-card h-100">
                        <h2 style="font-family: 'Outfit', sans-serif; color: #0f172a; font-weight: 800; font-size: 2rem; margin-bottom: 20px;">About Our Startup Club</h2>
                        <p style="color: #475569; line-height: 1.85; margin-bottom: 20px; font-size: 1.05rem;">
                            The SRKREC Startup Club is a dynamic ecosystem designed to foster innovation and entrepreneurship 
                            among students and faculty. We provide comprehensive support including mentorship, funding guidance, workspace, 
                            and resources to transform innovative ideas into successful businesses.
                        </p>
                        <p style="color: #475569; line-height: 1.85; margin: 0; font-size: 1.05rem;">
                            Our mission is to create a culture of entrepreneurship and innovation that contributes to economic development 
                            and societal progress through technology-driven solutions. We help students take their first steps into 
                            the startup world and connect them with the right resources and opportunities.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="impact-card h-100 d-flex flex-column justify-content-between">
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 25px; color: #ffffff;">Our Impact</h4>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">5+</div>
                            <div class="impact-label">Active Startups</div>
                        </div>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">200+</div>
                            <div class="impact-label">Daily Customers</div>
                        </div>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">3+</div>
                            <div class="impact-label">Industry Sectors</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs & Activities -->
    

    <style>
    .startups-section {
        background: linear-gradient(to bottom, #f8fafc, #ffffff);
        padding: 80px 0;
    }

    .section-title {
        font-family: 'Outfit', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 50px;
        position: relative;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, #4f46e5, #3b82f6);
        margin: 12px auto 0;
        border-radius: 2px;
    }

    .startup-logo-container {
        width: 100%;
        height: 140px;
        margin: 0 0 18px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        overflow: hidden;
    }
    
    .startup-logo {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.06));
        transition: transform 0.3s ease;
    }

    .startup-logo-container:hover .startup-logo {
        transform: scale(1.08);
    }

    .startup-info {
        text-align: center;
        margin-bottom: 20px;
    }

    .startup-title {
        margin: 0;
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .startup-subtitle {
        margin-top: 5px;
        color: #64748b;
        font-size: 1rem;
    }

    .startup-description {
        color: #64748b;
        margin-bottom: 20px;
        flex-grow: 1;
        line-height: 1.6;
    }

    .startup-tags {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .startup-tag {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .bo-card {
        border-color: #3b82f6;
    }
    
    .lb-card {
        border-color: #ef4444;
    }
    
    .bd-card {
        border-color: #0ea5e9;
    }
    
    .sw-card {
        border-color: #14b8a6;
    }
    
    .nd-card {
        border-color: #22c55e;
    }

    .row-eq-height {
        display: flex;
        flex-wrap: wrap;
    }

    .row-eq-height > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
    </style>

    <!-- Featured Startups (ReactBits OrbitImages Component) -->
    <section class="orbit-section">
        <div class="container text-center mb-4">
            <h2 class="section-title">Our Successful Startups</h2>
            <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: -30px auto 30px;">
                Explore student-led ventures launched through our incubation program
            </p>
        </div>

        <!-- ReactBits OrbitImages Engine Container -->
        <div class="orbit-container">
            <div class="orbit-rotation-wrapper">
                <svg class="orbit-path-svg">
                    <path fill="none" stroke="rgba(37, 99, 235, 0.2)" stroke-width="2" stroke-dasharray="6 6" />
                </svg>

                <!-- Orbit Item 1: Lunchbox -->
                <div class="orbit-item" title="Lunchbox - School Lunch Delivery">
                    <img class="orbit-image" src="assets/company_logos/logos/25.png" alt="Lunchbox">
                </div>

                <!-- Orbit Item 2: Bhimavaram Online -->
                <div class="orbit-item" title="Bhimavaram Online - E-Commerce Platform">
                    <img class="orbit-image" src="assets/company_logos/logos/22.png" alt="Bhimavaram Online">
                </div>

                <!-- Orbit Item 3: Bhimavaram Digitals -->
                <div class="orbit-item" title="Bhimavaram Digitals - Digital Marketing">
                    <img class="orbit-image" src="assets/company_logos/logos/20.png" alt="Bhimavaram Digitals">
                </div>

                <!-- Orbit Item 4: Smart Wash -->
                <div class="orbit-item" title="Smart Wash - Laundry Services">
                    <img class="orbit-image" src="assets/company_logos/logos/23.png" alt="Smart Wash">
                </div>

                <!-- Orbit Item 5: Campus Online -->
                <div class="orbit-item" title="Campus Online - Learning Portal">
                    <img class="orbit-image" src="assets/company_logos/logos/21.png" alt="Campus Online">
                </div>

                <!-- Orbit Item 6: NutriDelight -->
                <div class="orbit-item" title="NutriDelight - Health Food Delivery">
                    <img class="orbit-image" src="assets/company_logos/logos/26.png" alt="NutriDelight">
                </div>
            </div>

            <!-- Orbit Center Content Card -->
            <div class="orbit-center-content">
                <div class="orbit-center-card">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(37, 99, 235, 0.1); color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 12px;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Startups Hub</h3>
                    <p>6+ Active Ventures</p>
                </div>
            </div>
        </div>

        <!-- Horizontal Scrollable Row for ALL Startups Below -->
        <div class="startups-horizontal-container">
            <div class="startups-header-bar">
                <div>
                    <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1e293b; margin: 0; font-size: 1.5rem;">Explore All Incubated Startups</h3>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 4px 0 0;">Swipe or click left/right arrows to scroll through all student ventures</p>
                </div>
                <div class="startups-scroll-controls">
                    <button type="button" class="startups-scroll-btn startups-scroll-prev" aria-label="Scroll left">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="startups-scroll-btn startups-scroll-next" aria-label="Scroll right">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="startups-scroll-row">
                <!-- Startup 1: Bhimavaram Online -->
                <div class="startup-scroll-card bo-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/22.png" alt="Bhimavaram Online" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Bhimavaram Online</h5>
                        <p class="startup-subtitle">E-Commerce Platform</p>
                    </div>
                    <p class="startup-description">
                        First ONDC enabled app in AP & Telangana. A one-stop portal for shopping, food ordering, and local services in Bhimavaram.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-primary">E-Commerce</span>
                        <span class="badge bg-info">ONDC</span>
                    </div>
                </div>

                <!-- Startup 2: Lunch Box -->
                <div class="startup-scroll-card lb-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/25.png" alt="Lunch Box" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Lunch Box</h5>
                        <p class="startup-subtitle">School Lunch Delivery</p>
                    </div>
                    <p class="startup-description">
                        Delivering 200+ lunchboxes daily. Monthly subscription-based school lunch delivery from home to school.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-success">FoodTech</span>
                        <span class="badge bg-warning">Logistics</span>
                    </div>
                </div>

                <!-- Startup 3: Bhimavaram Digitals -->
                <div class="startup-scroll-card bd-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/20.png" alt="Bhimavaram Digitals" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Bhimavaram Digitals</h5>
                        <p class="startup-subtitle">Digital Marketing</p>
                    </div>
                    <p class="startup-description">
                        Digital marketing startup specializing in digital billboards, SEO, social media management, and content creation.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-primary">Marketing</span>
                        <span class="badge bg-info">Digital</span>
                    </div>
                </div>

                <!-- Startup 4: Smart Wash -->
                <div class="startup-scroll-card sw-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/23.png" alt="Smart Wash" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Smart Wash</h5>
                        <p class="startup-subtitle">Laundry Services</p>
                    </div>
                    <p class="startup-description">
                        Student-run laundry startup offering dry cleaning, shoe cleaning, and saree rolling with eco-friendly methods.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-success">Service</span>
                        <span class="badge bg-warning">EcoFriendly</span>
                    </div>
                </div>

                <!-- Startup 5: Campus Online -->
                <div class="startup-scroll-card bo-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/21.png" alt="Campus Online" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Campus Online</h5>
                        <p class="startup-subtitle">EdTech Startup</p>
                    </div>
                    <p class="startup-description">
                        Comprehensive learning management portal connecting faculty and students with interactive course materials.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-primary">EdTech</span>
                        <span class="badge bg-info">Platform</span>
                    </div>
                </div>

                <!-- Startup 6: NutriDelight -->
                <div class="startup-scroll-card nd-card">
                    <div class="startup-logo-container">
                        <img src="assets/company_logos/logos/26.png" alt="NutriDelight" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">NutriDelight</h5>
                        <p class="startup-subtitle">Health Food Delivery</p>
                    </div>
                    <p class="startup-description">
                        Health-focused cloud kitchen startup delivering nutritious meals using fresh, locally-sourced ingredients.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-success">FoodTech</span>
                        <span class="badge bg-info">Health</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources -->
    
    

    <!-- ReactBits OrbitImages Component Engine -->
    <script src="assets/js/orbit-images.js"></script>
    <?php include "footer.php"; ?>
</body>
</html>
