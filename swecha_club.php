<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 
?>

<style>
body {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    background: #f8fafc;
    color: #334155;
    overflow-x: hidden;
}

/* Animated Hero Section */
.hero-section {
    background: linear-gradient(-45deg, #0f172a, #065f46, #047857, #0f172a);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    color: white;
    padding: 85px 0;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.7;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes floatBranch {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(-3deg); }
}

.hero-icon-container {
    width: 130px;
    height: 130px;
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(14px);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.18);
    animation: floatBranch 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.swecha-card {
    background: #ffffff;
    border-radius: 28px;
    padding: 35px;
    margin-bottom: 25px;
    transition: all 0.35s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
}

.swecha-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 40px rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.3);
}

.impact-card {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: #ffffff;
    padding: 35px 25px;
    border-radius: 28px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.25);
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

.activity-card {
    background: #ffffff;
    color: #1e293b;
    padding: 30px;
    border-radius: 24px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    transition: all 0.35s ease;
}

.activity-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 38px rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.3);
}

.section-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    text-align: center;
    margin-bottom: 45px;
    position: relative;
}

.section-header-title::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, #10b981, #059669);
    margin: 12px auto 0;
    border-radius: 2px;
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Animated Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #10b981; background: rgba(16, 185, 129, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-code-branch" style="margin-right: 6px;"></i>Open Source & Digital Freedom
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15;">Swecha Club</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #a7f3d0; max-width: 650px; line-height: 1.6;">Promoting Free & Open Source Software, collaborative learning, Linux systems, and community tech contributions.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-code-branch" style="font-size: 60px; color: #34d399; filter: drop-shadow(0 0 15px rgba(52, 211, 153, 0.6));"></i>
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
                    <div class="swecha-card h-100">
                        <h2 style="font-family: 'Outfit', sans-serif; color: #0f172a; font-weight: 800; font-size: 2rem; margin-bottom: 20px;">About Swecha Club</h2>
                        <p style="color: #475569; line-height: 1.85; margin-bottom: 20px; font-size: 1.05rem;">
                            Swecha Club at SRKREC is dedicated to promoting free software, open source culture, and digital freedom. 
                            We believe in the power of collaborative learning and sharing knowledge through open source contributions.
                        </p>
                        <p style="color: #475569; line-height: 1.85; margin: 0; font-size: 1.05rem;">
                            Our mission is to create awareness about free software alternatives, encourage students to contribute to 
                            open source projects, and build a community of tech enthusiasts who believe in digital freedom.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="impact-card h-100 d-flex flex-column justify-content-between">
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 25px; color: #ffffff;">Club Statistics</h4>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">200+</div>
                            <div class="impact-label">Active Members</div>
                        </div>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">30+</div>
                            <div class="impact-label">Open Source Projects</div>
                        </div>
                        <div style="margin: 10px 0;">
                            <div class="impact-num">40+</div>
                            <div class="impact-label">Workshops Conducted</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Focus Areas -->
    <section style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <h2 class="section-header-title">Our Focus Areas</h2>
            <div class="row g-4">
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-left: 5px solid #10b981;">
                        <div style="width: 60px; height: 60px; border-radius: 18px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fas fa-laptop-code" style="font-size: 26px; color: #10b981;"></i>
                        </div>
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; font-size: 1.35rem; margin-bottom: 8px;">Free Software</h4>
                        <p style="color: #64748b; margin-bottom: 15px; font-size: 0.95rem;">Promoting free and open source software alternatives across campus</p>
                        <ul style="color: #475569; font-size: 0.9rem; padding-left: 0; list-style: none;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i>Linux Operating Systems</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i>Open Source Software Tools</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i>Free Software Philosophy</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i>Software Freedom Advocacy</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-left: 5px solid #3b82f6;">
                        <div style="width: 60px; height: 60px; border-radius: 18px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fas fa-users" style="font-size: 26px; color: #3b82f6;"></i>
                        </div>
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; font-size: 1.35rem; margin-bottom: 8px;">Community Building</h4>
                        <p style="color: #64748b; margin-bottom: 15px; font-size: 0.95rem;">Creating a collaborative peer learning environment for developers</p>
                        <ul style="color: #475569; font-size: 0.9rem; padding-left: 0; list-style: none;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i>Knowledge Sharing Sessions</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i>Peer-to-Peer Learning</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i>Mentorship Programs</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i>Community Tech Events</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-left: 5px solid #ef4444;">
                        <div style="width: 60px; height: 60px; border-radius: 18px; background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fas fa-code" style="font-size: 26px; color: #ef4444;"></i>
                        </div>
                        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; font-size: 1.35rem; margin-bottom: 8px;">Open Source Dev</h4>
                        <p style="color: #64748b; margin-bottom: 15px; font-size: 0.95rem;">Contributing directly to global open source software projects</p>
                        <ul style="color: #475569; font-size: 0.9rem; padding-left: 0; list-style: none;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #ef4444; margin-right: 8px;"></i>Version Control (Git & GitHub)</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #ef4444; margin-right: 8px;"></i>Project Contributions & PRs</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #ef4444; margin-right: 8px;"></i>Code Reviews & Testing</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #ef4444; margin-right: 8px;"></i>Open Technical Documentation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activities -->
    <section style="padding: 60px 0; background: #ffffff;">
        <div class="container">
            <h2 class="section-header-title">Recent Activities</h2>
            <div class="row g-4">
                <div class="col-md-6 mb-4">
                    <div class="activity-card h-100">
                        <div class="activity-header mb-3 pb-3" style="border-bottom: 1px solid #e2e8f0;">
                            <span class="badge bg-success mb-2" style="padding: 6px 12px; border-radius: 99px;">FEST 2025</span>
                            <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;"><i class="fas fa-calendar-alt" style="color: #10b981; margin-right: 8px;"></i> Swecha Freedom Fest 2025</h4>
                            <p style="font-size: 0.9rem; color: #64748b; margin-top: 6px; font-weight: 600;">March 15-17, 2025 | SRKREC Campus</p>
                        </div>
                        <p style="color: #475569; line-height: 1.7; margin-bottom: 20px;">A three-day celebration of software freedom, digital rights, and open source innovation. An immersive experience of learning, collaboration, and technological advancement.</p>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                            <h6 style="color: #0f172a; font-weight: 800; margin-bottom: 12px; font-family: 'Outfit', sans-serif;">Event Highlights</h6>
                            <ul style="list-style: none; padding: 0; margin: 0; color: #334155; font-size: 0.9rem;">
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> Open Source Exhibition & Project Showcase</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> Tech Talks by Industry Experts</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> Hands-on Workshops on Latest Technologies</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> 36-Hour Open Source Hackathon</li>
                                <li><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> Community Networking Sessions</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="activity-card h-100">
                        <div class="activity-header mb-3 pb-3" style="border-bottom: 1px solid #e2e8f0;">
                            <span class="badge bg-primary mb-2" style="padding: 6px 12px; border-radius: 99px;">ONGOING</span>
                            <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin: 0;"><i class="fas fa-book" style="color: #3b82f6; margin-right: 8px;"></i> Mission Kithab</h4>
                            <p style="font-size: 0.9rem; color: #64748b; margin-top: 6px; font-weight: 600;">Ongoing Initiative | Digital Freedom in Education</p>
                        </div>
                        <p style="color: #475569; line-height: 1.7; margin-bottom: 20px;">A revolutionary digital library initiative making educational resources freely accessible to all. Supporting the vision of knowledge freedom and open education.</p>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                            <h6 style="color: #0f172a; font-weight: 800; margin-bottom: 12px; font-family: 'Outfit', sans-serif;">Key Features</h6>
                            <ul style="list-style: none; padding: 0; margin: 0; color: #334155; font-size: 0.9rem;">
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i> Comprehensive E-book Collection (10,000+ titles)</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i> Open Educational Resources & Study Materials</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i> Interactive Learning Platforms</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i> Collaborative Resource Development</li>
                                <li><i class="fas fa-check-circle" style="color: #3b82f6; margin-right: 8px;"></i> Mobile-Friendly Access</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Center -->
    <section style="padding: 60px 0; background: #f8fafc;">
        <div class="container">
            <h2 class="section-header-title">Swecha Learning Center</h2>
            <div class="row g-4">
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-top: 4px solid #ef4444;">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-top: 5px;">Training Programs</h4>
                        </div>
                        <ul style="color: #475569; line-height: 1.8; list-style: none; padding-left: 0; font-size: 0.9rem;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> Linux System Administration</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> Open Source Development</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> Modern Web Technologies</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> Python Programming & Apps</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> DevOps Tools & Practices</li>
                            <li><i class="fas fa-check" style="color: #ef4444; margin-right: 8px;"></i> Cloud Computing Open Source</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-top: 4px solid #10b981;">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-top: 5px;">Resources & Tools</h4>
                        </div>
                        <ul style="color: #475569; line-height: 1.8; list-style: none; padding-left: 0; font-size: 0.9rem;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Learning Materials Repository</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Interactive Video Tutorials</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Hands-on Practice Projects</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Technical Documentation</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Community Support Forums</li>
                            <li><i class="fas fa-check" style="color: #10b981; margin-right: 8px;"></i> Code Repositories & Examples</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="swecha-card h-100" style="border-top: 4px solid #6366f1;">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(99, 102, 241, 0.1); color: #6366f1; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h4 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-top: 5px;">Special Programs</h4>
                        </div>
                        <ul style="color: #475569; line-height: 1.8; list-style: none; padding-left: 0; font-size: 0.9rem;">
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Open Source Contribution Workshops</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Summer of Code Programs</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Tech Mentorship Initiatives</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Industry Expert Sessions</li>
                            <li style="margin-bottom: 8px;"><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Hackathons & Code Sprints</li>
                            <li><i class="fas fa-check" style="color: #6366f1; margin-right: 8px;"></i> Certification Programs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include "footer.php"; ?>
</body>
</html>
