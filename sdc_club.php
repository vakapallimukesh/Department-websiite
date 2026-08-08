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
    background: linear-gradient(-45deg, #1e1b4b, #312e81, #4338ca, #581c87);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    color: white;
    padding: 190px 0 160px;
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

@keyframes floatCode {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(3deg); }
}

@keyframes pulseBadge {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 0 0 14px rgba(99, 102, 241, 0); }
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
    animation: floatCode 6s ease-in-out infinite;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.contribution-card {
    background: #ffffff;
    border-radius: 28px;
    padding: 35px;
    margin-bottom: 35px;
    transition: all 0.35s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
}

.contribution-card:hover {
    box-shadow: 0 18px 40px rgba(99, 102, 241, 0.1);
}

.contribution-header {
    font-family: 'Outfit', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.project-area, .workshop-area {
    border: none;
    background: #ffffff;
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
    border-left: 4px solid #6366f1;
    border-top: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, border-color 0.3s ease;
}

.workshop-area {
    border-left: 4px solid #ec4899;
}

.project-area:hover, .workshop-area:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 35px rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.3);
}

.project-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.3rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 10px;
}

.project-badge {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 4px 12px;
    border-radius: 99px;
    display: inline-block;
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
    margin-top: 10px;
}

.workshop-badge {
    background: rgba(236, 72, 153, 0.1);
    color: #ec4899;
}

.row {
    display: flex;
    flex-wrap: wrap;
}

.row > [class*='col-'] {
    display: flex;
    flex-direction: column;
}
</style>

<body>
    <?php include "nav.php"; ?>
    
    <!-- Animated Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span style="color: #6366f1; background: rgba(99, 102, 241, 0.15); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.82rem; display: inline-block; padding: 6px 16px; border-radius: 99px; margin-bottom: 16px; border: 1px solid rgba(99, 102, 241, 0.3);">
                        <i class="fas fa-terminal" style="margin-right: 6px;"></i>SRKREC SDC Lab
                    </span>
                    <h1 style="font-family: 'Outfit', sans-serif; font-size: 3.4rem; font-weight: 900; margin-bottom: 15px; color: #ffffff; line-height: 1.15; text-shadow: none;">Software Development Club</h1>
                    <p style="font-size: 1.25rem; opacity: 0.92; color: #cbd5e1; max-width: 650px; line-height: 1.6;">Empowering students through practical software engineering, real-world fullstack projects, and industry-level technical workshops.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="hero-icon-container">
                        <i class="fas fa-code" style="font-size: 60px; color: #818cf8; filter: drop-shadow(0 0 15px rgba(129, 140, 248, 0.6));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contributions -->
    <section style="padding: 50px 0;">
        <div class="container">
           
            <!-- Website Development Projects -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-laptop-code" style="color: #6366f1;"></i> Website Development Projects</h3>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>SVCET College Website</h4>
                            <p><strong>Website:</strong> svcet</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Magical DB Website</h4>
                            <p><strong>Website:</strong> Magical DB</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>SRKR College Website</h4>
                            <ul>
                                <li>Technical assistance in updating and maintaining the website</li>
                                <li>Enhanced UI/UX and added new features</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Houses Website</h4>
                            <ul>
                                <li>Designed to manage student house-based activities</li>
                                <li>Implemented a dynamic dashboard for easy navigation</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Panchayati Website</h4>
                            <p>Development of a Panchayati website for the BVRM locality, featuring WhatsApp integration for direct interaction.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Smart Wash Website</h4>
                            <ul>
                                <li>Development with assistance of 2nd years</li>
                                <li>Platform for users to manage their laundry services</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>LICRS Website</h4>
                            <p>Website developed for the International Conference on Intra- and Inter-Cellular Regulatory Systems 2024 at the University of Hyderabad.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Fest Website Development</h4>
                            <p>Designed a dedicated website for fest registrations and updates.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workshops and Training -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-graduation-cap" style="color: #ec4899;"></i> Workshops and Training Programs</h3>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>Web Development Workshop</h4>
                            <p>Covered HTML, CSS, JavaScript, and responsive design techniques.</p>
                            <p><strong>Scope:</strong> Entire College</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>C Lab and Internal Evaluation</h4>
                            <ul>
                                <li>Regular Lab sessions for fundamental concepts</li>
                                <li>C Internal Assessment using Hacker Rank</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>Full Stack Workshop</h4>
                            <p><strong>Scope:</strong> Department Level (2 Weeks)</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>Summer Coding Classes</h4>
                            <p><strong>Location:</strong> Westberry School</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>GitHub Workshop</h4>
                            <ul>
                                <li>Training on version control and collaborative coding</li>
                                <li>Enhanced project management skills</li>
                            </ul>
                            <p><strong>Scope:</strong> Department Level</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>C Classes for Juniors</h4>
                            <p>Sessions covering fundamental programming concepts</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>Full Stack Academic Class for Juniors</h4>
                            <p>Core web development concepts, including frontend, backend, and database management</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>MERN Workshop</h4>
                            <p>In-depth training on MongoDB, Express.js, React.js, and Node.js</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="workshop-area h-100">
                            <h4>MERN Academic Classes (Parallels)</h4>
                            <p>Parallel sessions conducted to accommodate different student batches</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Organization and Technical Support -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-cogs" style="color: #3b82f6;"></i> Event Organization & Technical Support</h3>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>SpellBee Organization</h4>
                            <p>Active role in planning, coordinating, and executing the event.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Fest Organization - Technical Support</h4>
                            <h5>KBC Software</h5>
                            <p>Developed a custom software system for conducting a quiz event.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Software for Free Fire</h4>
                            <p>Created a tracking and management system for a gaming event</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="project-area h-100">
                            <h4>Fest Organization (End-to-End)</h4>
                            <ul>
                                <li>Non-Tech Events organization</li>
                                <li>Finance management</li>
                                <li>Overall organization coordination</li>
                                <li>Artwork design</li>
                                <li>Technical aspects coordination</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hackathons and Events -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-trophy" style="color: #f59e0b;"></i> Hackathons and Events</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="project-area h-100">
                            <h4>WEBTECH Hackathon (Department Level)</h4>
                            <ul>
                                <li>Organized and mentored participants</li>
                                <li>Encouraged innovation and hands-on problem-solving</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="project-area h-100">
                            <h4>GDG Hackathon (College Level)</h4>
                            <ul>
                                <li>Active participation and contribution</li>
                                <li>Real-world problem-solving experience</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Internship Guidance -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-user-tie" style="color: #10b981;"></i> Internship Guidance</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="workshop-area h-100">
                            <h4>VB Alpha</h4>
                            <p>Focus on stock market domain</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="workshop-area h-100">
                            <h4>FalconX</h4>
                            <p>Guiding two interns from FalconX company</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekend Sessions -->
            <div class="contribution-card">
                <h3 class="contribution-header"><i class="fas fa-calendar-alt" style="color: #8b5cf6;"></i> Weekend Sessions</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="workshop-area h-100">
                            <h4>AI Fridays</h4>
                            <p>Brief introduction about AI's use in Corporate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>
</html>
