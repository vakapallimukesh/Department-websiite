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
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
    color: white;
    padding: 85px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.6;
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

.bo-card { border-color: #3b82f6; }
.lb-card { border-color: #ef4444; }
.bd-card { border-color: #0ea5e9; }
.sw-card { border-color: #14b8a6; }
.nd-card { border-color: #22c55e; }
.co-card { border-color: #2563eb; }
.bf-card { border-color: #f59e0b; }
</style>
<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #fbbf24; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.85rem; display: inline-block; margin-bottom: 12px;">Student Innovation Hub</span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.2rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Startup Club</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #e2e8f0; max-width: 620px; font-weight: 500;">Empowering student entrepreneurs to build innovative solutions, access seed mentorship, and launch real-world ventures.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.08); display: inline-flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-lightbulb" style="font-size: 70px; color: #fbbf24; filter: drop-shadow(0 0 20px rgba(251, 191, 36, 0.5));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Club Overview -->
    <section style="padding: 60px 0;">
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

<body>
    <?php include "nav.php"; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #fbbf24; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.85rem; display: inline-block; margin-bottom: 12px;">Student Innovation Hub</span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.2rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Startup Club</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #e2e8f0; max-width: 620px; font-weight: 500;">Empowering student entrepreneurs to build innovative solutions, access seed mentorship, and launch real-world ventures.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.08); display: inline-flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-lightbulb" style="font-size: 70px; color: #fbbf24; filter: drop-shadow(0 0 20px rgba(251, 191, 36, 0.5));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Club Overview -->
    <section style="padding: 60px 0;">
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

    .co-card {
        border-color: #2563eb;
    }

    .bf-card {
        border-color: #f59e0b;
    }
    </style>

=======
>>>>>>> Stashed changes
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
                <div class="orbit-item" data-startup="lunch-box" style="cursor: pointer;" title="Lunchbox - Food Delivery">
                    <img class="orbit-image" src="public/startups/lunch-box/lunch-box.png" alt="Lunchbox">
                </div>

                <!-- Orbit Item 2: Bhimavaram Online -->
                <div class="orbit-item" data-startup="bhimavaram-online" style="cursor: pointer;" title="Bhimavaram Online - E-Commerce Platform">
                    <img class="orbit-image" src="public/startups/bhimavaram-online/bhimavaramonline.png" alt="Bhimavaram Online">
                </div>

                <!-- Orbit Item 3: Bhimavaram Digitals -->
                <div class="orbit-item" data-startup="bhimavaram-digitals" style="cursor: pointer;" title="Bhimavaram Digitals - Digital Marketing">
                    <img class="orbit-image" src="public/startups/bhimavaram-digital/bhimavaram-digitals.png" alt="Bhimavaram Digitals">
                </div>

                <!-- Orbit Item 4: Smart Wash -->
                <div class="orbit-item" data-startup="smart-wash" style="cursor: pointer;" title="Smart Wash - Laundry Services">
                    <img class="orbit-image" src="public/startups/smart-wash/hero.png" alt="Smart Wash">
                </div>

                <!-- Orbit Item 5: Campus Online -->
                <div class="orbit-item" data-startup="campus-online" style="cursor: pointer;" title="Campus Online - Learning Portal">
                    <img class="orbit-image" src="public/startups/campus-online/campus-online.png" alt="Campus Online">
                </div>

                <!-- Orbit Item 6: NutriDelight -->
                <div class="orbit-item" data-startup="nutridelight" style="cursor: pointer;" title="NutriDelight - Health Food Delivery">
                    <img class="orbit-image" src="public/startups/nutridelight/hero.png" alt="NutriDelight">
                </div>

                <!-- Orbit Item 7: Bhimavaram Online Foods -->
                <div class="orbit-item" data-startup="bhimavaram-foods" style="cursor: pointer;" title="Bhimavaram Online Foods - ONDC Enabled Platform">
                    <img class="orbit-image" src="public/startups/bhimavaram-foods/hero.png" alt="Bhimavaram Online Foods">
                </div>
            </div>

            <!-- Orbit Center Content Card -->
            <div class="orbit-center-content">
                <div class="orbit-center-card">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(37, 99, 235, 0.1); color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 12px;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Startups Hub</h3>
                    <p>7+ Active Ventures</p>
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
                <div class="startup-scroll-card bo-card" data-startup="bhimavaram-online" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/bhimavaram-online/bhimavaramonline.png" alt="Bhimavaram Online" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Bhimavaram Online</h5>
                        <p class="startup-subtitle">Hyperlocal E-Commerce App</p>
                    </div>
                    <p class="startup-description">
                        First ONDC-enabled hyperlocal marketplace app in AP & Telangana. Order A-Z in Bhimavaram: Restaurant food, groceries, fruits & veggies, meat & fish.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-primary">Hyperlocal</span>
                        <span class="badge bg-info">ONDC</span>
                    </div>
                </div>

                <!-- Startup 2: Lunch Box -->
                <div class="startup-scroll-card lb-card" data-startup="lunch-box" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/lunch-box/lunch-box.png" alt="Lunch Box" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Lunch Box</h5>
                        <p class="startup-subtitle">School Lunch Delivery</p>
                    </div>
                    <p class="startup-description">
                        Delivering 200+ lunch boxes daily from home to school. Monthly subscription model starting from ₹499/- per month.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-success">School Lunch</span>
                        <span class="badge bg-warning">Delivery</span>
                    </div>
                </div>

                <!-- Startup 3: Bhimavaram Digitals -->
                <div class="startup-scroll-card bd-card" data-startup="bhimavaram-digitals" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/bhimavaram-digital/bhimavaram-digitals.png" alt="Bhimavaram Digitals" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Bhimavaram Digitals</h5>
                        <p class="startup-subtitle">Digital Marketing Startup</p>
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
                <div class="startup-scroll-card sw-card" data-startup="smart-wash" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/smart-wash/hero.png" alt="Smart Wash" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Smart Wash</h5>
                        <p class="startup-subtitle">Smart Laundry & Fabric Care</p>
                    </div>
                    <p class="startup-description">
                        Serving Bhimavaram with Top-Quality Laundry Solutions! Doorstep pickup, clean care, and affordable student rates.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-success">Laundry</span>
                        <span class="badge bg-warning">Doorstep Care</span>
                    </div>
                </div>

                <!-- Startup 5: Campus Online -->
                <div class="startup-scroll-card co-card" data-startup="campus-online" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/campus-online/co.jpg" alt="Campus Online" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Campus Online</h5>
                        <p class="startup-subtitle">Campus E-Commerce, Fun & Learning, Communication</p>
                    </div>
                    <p class="startup-description">
                        A campus-focused digital platform combining e-commerce, learning, fun, and communication to create a connected digital campus experience.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-danger">E-Commerce</span>
                        <span class="badge bg-primary">Fun & Learning</span>
                    </div>
                </div>

                <!-- Startup 6: NutriDelight -->
                <div class="startup-scroll-card nd-card" data-startup="nutridelight" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/nutridelight/hero.png" alt="NutriDelight" class="startup-logo">
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

                <!-- Startup 7: Bhimavaram Online Foods -->
                <div class="startup-scroll-card bf-card" data-startup="bhimavaram-foods" style="cursor: pointer;">
                    <div class="startup-logo-container">
                        <img src="public/startups/bhimavaram-foods/hero.png" alt="Bhimavaram Online Foods" class="startup-logo">
                    </div>
                    <div class="startup-info">
                        <h5 class="startup-title">Bhimavaram Online Foods</h5>
                        <p class="startup-subtitle">ONDC Enabled E-Commerce Platform</p>
                    </div>
                    <p class="startup-description">
                        An ONDC-enabled e-commerce platform offering authentic Bhimavaram sweets, hots, pickles, and spice powders.
                    </p>
                    <div class="startup-tags">
                        <span class="badge bg-warning text-dark">ONDC Platform</span>
                        <span class="badge bg-danger">Sweets & Pickles</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Startup Details Modal -->
    <div class="modal fade" id="startupModal" tabindex="-1" aria-labelledby="startupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
                <div class="modal-header border-0 position-relative p-4" id="startupModalHeader" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-white rounded-4 shadow-sm" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <img id="startupModalLogo" src="assets/company_logos/logos/23.png" alt="Startup Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <span id="startupModalCategory" class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">LAUNDRY SERVICES</span>
                            <h3 id="startupModalTitle" class="modal-title font-outfit fw-bold mt-1 mb-0" style="color: #ffffff; font-size: 1.8rem;">Smart Wash</h3>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background: #f8fafc;">
                    <div class="card border-0 rounded-4 p-4 shadow-sm mb-4" style="background: #ffffff; border-left: 5px solid #0284c7 !important;">
                        <h5 class="fw-bold text-primary mb-2" style="font-size: 1.15rem; color: #0284c7 !important;" id="startupModalTagline">
                            "Serving Bhimavaram with Top-Quality Laundry Solutions!"
                        </h5>
                        <p class="text-secondary mb-0" style="font-size: 1.05rem; line-height: 1.7;" id="startupModalDescription">
                            Get the best laundry services in Bhimavaram! We pick up your clothes, clean them with care, and deliver them back to you, all at affordable rates. Trust us to take care of your clothes like they're our own!
                        </p>
                    </div>
                    
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-star text-warning me-2"></i>Key Highlights & Features</h6>
                    <div class="row g-3" id="startupModalFeatures">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 d-flex justify-content-between" style="background: #ffffff;">
                    <a id="startupModalPageLink" href="smart_wash.php" class="btn btn-primary px-4 rounded-pill fw-bold">
                        <i class="fas fa-external-link-alt me-2"></i> Visit Smart Wash Dedicated Page
                    </a>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ReactBits OrbitImages Component Engine -->
    <script src="assets/js/orbit-images.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const startupData = {
            'smart-wash': {
                title: 'Smart Wash',
                category: 'LAUNDRY SERVICES',
                logo: 'assets/company_logos/logos/23.png',
                tagline: '"Serving Bhimavaram with Top-Quality Laundry Solutions!"',
                description: 'Get the best laundry services in Bhimavaram! We pick up your clothes, clean them with care, and deliver them back to you, all at affordable rates. Trust us to take care of your clothes like they\'re our own!',
                headerGradient: 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                accentColor: '#0284c7',
                pageUrl: 'smart_wash.php',
                features: [
                    { icon: 'fas fa-truck-pickup', color: 'primary', title: 'Doorstep Pickup & Delivery', desc: 'Hassle-free collection & return across Bhimavaram' },
                    { icon: 'fas fa-tshirt', color: 'success', title: 'Gentle & Quality Washing', desc: 'Careful cleaning techniques tailored to garment types' },
                    { icon: 'fas fa-tag', color: 'warning', title: 'Affordable Student Rates', desc: 'Budget-friendly laundry packages for campus students' },
                    { icon: 'fas fa-sparkles', color: 'info', title: 'Specialized Care', desc: 'Dry cleaning, shoe cleaning & saree rolling services' }
                ]
            },
            'bhimavaram-online': {
                title: 'Bhimavaram Online',
                category: 'E-COMMERCE & ONDC',
                logo: 'assets/company_logos/logos/22.png',
                tagline: '"First ONDC-Enabled Hyperlocal Marketplace in AP & Telangana!"',
                description: 'A one-stop portal for shopping, food ordering, and local services in Bhimavaram, empowering local vendors and offering seamless delivery.',
                headerGradient: 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)',
                accentColor: '#2563eb',
                features: [
                    { icon: 'fas fa-shopping-bag', color: 'primary', title: 'ONDC Network Integration', desc: 'Connected with national open digital commerce network' },
                    { icon: 'fas fa-utensils', color: 'success', title: 'Food & Local Groceries', desc: 'Instant doorstep delivery from top local vendors' }
                ]
            },
            'lunch-box': {
                title: 'Lunch Box',
                category: 'FOOD TECH & LOGISTICS',
                logo: 'assets/company_logos/logos/25.png',
                tagline: '"Delivering Fresh Home-Cooked Meals Daily!"',
                description: 'Monthly subscription-based school and college lunch delivery bringing nutritious meals directly from home kitchens to campus.',
                headerGradient: 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)',
                accentColor: '#dc2626',
                features: [
                    { icon: 'fas fa-box', color: 'danger', title: 'Daily Deliveries', desc: '200+ lunchboxes delivered daily on exact schedule' },
                    { icon: 'fas fa-heart', color: 'warning', title: 'Home Cooked Goodness', desc: 'Healthy, hygienic, and home-prepared meals' }
                ]
            },
            'bhimavaram-digitals': {
                title: 'Bhimavaram Digitals',
                category: 'DIGITAL MARKETING',
                logo: 'assets/company_logos/logos/20.png',
                tagline: '"Transforming Local Businesses with Digital Presence!"',
                description: 'Digital marketing startup specializing in digital billboards, SEO, social media management, and content creation.',
                headerGradient: 'linear-gradient(135deg, #0d9488 0%, #0f766e 100%)',
                accentColor: '#0d9488',
                features: [
                    { icon: 'fas fa-bullhorn', color: 'info', title: 'Digital Billboards', desc: 'High-visibility LED billboard advertising across city' },
                    { icon: 'fas fa-chart-line', color: 'primary', title: 'Social Media & SEO', desc: 'Targeted marketing campaigns and brand growth' }
                ]
            },
            'campus-online': {
                title: 'Campus Online',
                category: 'EDTECH PLATFORM',
                logo: 'assets/company_logos/logos/21.png',
                tagline: '"Empowering Next-Gen Campus Learning!"',
                description: 'Comprehensive learning management portal connecting faculty and students with interactive course materials and academic tracking.',
                headerGradient: 'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)',
                accentColor: '#7c3aed',
                features: [
                    { icon: 'fas fa-graduation-cap', color: 'primary', title: 'Course Management', desc: 'Seamless assignment & material distribution' },
                    { icon: 'fas fa-laptop-code', color: 'success', title: 'Interactive Learning', desc: 'Digital quizzes, notes & faculty connectivity' }
                ]
            },
            'nutridelight': {
                title: 'NutriDelight',
                category: 'HEALTH & WELLNESS',
                logo: 'public/startups/nutridelight/hero.png',
                tagline: '"Making Bhimavaram Healthy with Nutritious Meals!"',
                description: 'Health-focused cloud kitchen startup delivering nutritious meals crafted with fresh, locally-sourced ingredients.',
                headerGradient: 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)',
                accentColor: '#16a34a',
                pageUrl: 'startup_details.php?id=nutridelight',
                features: [
                    { icon: 'fas fa-leaf', color: 'success', title: 'Fresh & Healthy', desc: 'Locally-sourced ingredients prepared daily' },
                    { icon: 'fas fa-apple-alt', color: 'warning', title: 'Calorie Balanced', desc: 'Custom diet plans tailored for fitness & wellness' }
                ]
            }
        };

        const modalElement = document.getElementById('startupModal');
        const modal = new bootstrap.Modal(modalElement);

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
    <?php include "footer.php"; ?>
</body>
</html>
