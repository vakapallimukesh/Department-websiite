/**
 * CSD & CSIT AI Assistant Bot Engine
 * Comprehensive Website Knowledge Search & Chatbot
 */

(function () {
    'use strict';

    // Comprehensive Knowledge Base for CSD & CSIT Departments
    const knowledgeBase = [
        {
            id: "overview_programs",
            keywords: ["csd", "csit", "department", "about", "btech", "course", "programs", "degree", "intake", "computer science"],
            title: "🎓 CSD & CSIT Programs Overview",
            stats: [
                { val: "2 Programs", lbl: "B.Tech CSE & CSIT" },
                { val: "90%+", lbl: "Placement Rate" }
            ],
            content: `
                <p>The <strong>CSD (Computer Science & Design / Data Science)</strong> and <strong>CSIT (Computer Science & Information Technology)</strong> departments at SRKR Engineering College deliver world-class technical education focused on innovation and industry readiness.</p>
                <ul>
                    <li><strong>B.Tech in CSE:</strong> Deep focus on Data Structures, Algorithms, AI/ML, Cloud Architecture, and Software Engineering.</li>
                    <li><strong>B.Tech in CSIT:</strong> Specialized in Full-Stack Web Technologies, Cybersecurity, Networks, and Information Systems.</li>
                    <li><strong>Key Highlights:</strong> Modern curriculum, hands-on GPU lab sessions, active technical clubs, and top tier corporate placements.</li>
                </ul>
            `,
            links: [
                { text: "Explore Academics", url: "explore.php" },
                { text: "View Syllabus", url: "syllabus.php" },
                { text: "Students Info", url: "students_overview.php" }
            ]
        },
        {
            id: "faculty_leadership",
            keywords: ["faculty", "hod", "head", "professors", "teachers", "staff", "guide", "appreciation", "mentors"],
            title: "👨‍🏫 Faculty & Department Leadership",
            stats: [
                { val: "30+", lbl: "Expert Faculty" },
                { val: "15+", lbl: "Ph.D. Scholars" }
            ],
            content: `
                <p>Our departments are guided by highly qualified professors and industry-experienced faculty members dedicated to excellence in teaching and research.</p>
                <ul>
                    <li><strong>Head of Department (HOD):</strong> Leads academic initiatives, research grants, and industry collaborations.</li>
                    <li><strong>Specializations:</strong> Artificial Intelligence, Machine Learning, Cybersecurity, Cloud Computing, IoT, and Big Data Analytics.</li>
                    <li><strong>Faculty Appreciations:</strong> Regular awards for research publications, patent filings, and exceptional teaching quality.</li>
                </ul>
            `,
            links: [
                { text: "Faculty Directory", url: "faculty.php" },
                { text: "Faculty Portal Login", url: "login.php" }
            ]
        },
        {
            id: "aiml_labs",
            keywords: ["lab", "labs", "ai lab", "ml lab", "gpu", "infrastructure", "ai & ml", "computers", "equipment", "ai-ml-lab"],
            title: "🔬 Advanced AI & Machine Learning Lab",
            stats: [
                { val: "NVIDIA GPUs", lbl: "High Performance" },
                { val: "100+ Systems", lbl: "High-Speed Workstations" }
            ],
            content: `
                <p>The state-of-the-art <strong>AI & ML Research Lab</strong> is equipped to handle deep learning training, computer vision models, and natural language processing tasks.</p>
                <ul>
                    <li><strong>Hardware:</strong> High-end NVIDIA RTX GPU Workstations with 64GB+ RAM.</li>
                    <li><strong>Software Stack:</strong> PyTorch, TensorFlow, CUDA toolkit, Anaconda, Jupyter Hub, OpenCV, and ROS.</li>
                    <li><strong>Student Projects:</strong> Autonomous systems, medical image diagnostics, LLM fine-tuning, and real-time smart surveillance.</li>
                </ul>
            `,
            links: [
                { text: "AI & ML Lab Details", url: "ai-ml-lab.php" },
                { text: "Explore All Labs", url: "explore.php" }
            ]
        },
        {
            id: "house_system",
            keywords: ["house", "houses", "aakash", "agni", "jal", "prithvi", "points", "leaderboard", "shield", "competition"],
            title: "🛡️ Department House System",
            stats: [
                { val: "4 Houses", lbl: "Aakash, Agni, Jal, Prithvi" },
                { val: "Annual Shield", lbl: "Championship Trophy" }
            ],
            content: `
                <p>The student body is divided into 4 prestigious Houses to foster healthy competition, teamwork, and leadership:</p>
                <ul>
                    <li><strong style="color:#0284c7;">Aakash (Sky Blue):</strong> Symbolizes vision, high ambitions, and cloud innovation.</li>
                    <li><strong style="color:#ef4444;">Agni (Fire Red):</strong> Symbolizes passion, competitive coding, and energy.</li>
                    <li><strong style="color:#06b6d4;">Jal (Ocean Blue):</strong> Symbolizes depth of knowledge, fluidity, and teamwork.</li>
                    <li><strong style="color:#10b981;">Prithvi (Earth Green):</strong> Symbolizes strength, stability, and open-source contributions.</li>
                </ul>
                <p>Points are awarded for attendance, coding contests, sports, hackathons, and cultural competitions.</p>
            `,
            links: [
                { text: "House Leaderboard", url: "houses_dashboard.php" },
                { text: "Section House Points", url: "section_house_points.php" }
            ]
        },
        {
            id: "student_clubs",
            keywords: ["club", "clubs", "sdc", "startup", "swecha", "activities", "events"],
            title: "🚀 Student Technical & Innovation Clubs",
            stats: [
                { val: "3 Active Clubs", lbl: "Student-Led" },
                { val: "30+ Events", lbl: "Hosted Yearly" }
            ],
            content: `
                <p>CSD & CSIT feature vibrant student clubs encouraging practical skills and community building:</p>
                <ul>
                    <li><strong>SDC (Software Dev Club):</strong> Building real-world web/mobile applications for the college.</li>
                    <li><strong>Startup & Innovation Club:</strong> Entrepreneurship, ideation, and pitch competitions.</li>
                    <li><strong>Swecha Club:</strong> Promoting Free and Open Source Software (FOSS) and Linux.</li>
                </ul>
            `,
            links: [
                { text: "SDC Club", url: "sdc_club.php" },
                { text: "Startup Club", url: "startup_club.php" },
                { text: "Swecha Club", url: "swecha_club.php" }
            ]
        },
        {
            id: "placements_careers",
            keywords: ["placement", "placements", "jobs", "package", "salary", "companies", "recruiters", "amazon", "tcs", "hiring", "offers"],
            title: "🏆 Placements & Career Success",
            stats: [
                { val: "44+ LPA", lbl: "Highest Package" },
                { val: "6.5 LPA", lbl: "Average Package" }
            ],
            content: `
                <p>Our students achieve outstanding placements across leading global tech enterprises and high-growth startups.</p>
                <ul>
                    <li><strong>Top Recruiters:</strong> Amazon, TCS Digital, Infosys, Wipro, Accenture, Cognizant, Virtusa, Hexaware, and Tech Mahindra.</li>
                    <li><strong>Preparation Program:</strong> Dedicated mock technical interviews, aptitude training, and coding bootcamps starting from 3rd year.</li>
                </ul>
            `,
            links: [
                { text: "Placement Overview", url: "placements.php" },
                { text: "Explore Achievements", url: "explore.php" }
            ]
        },
        {
            id: "attendance_portals",
            keywords: ["attendance", "portal", "login", "marks", "leave", "student login", "calendar", "timetable", "check attendance"],
            title: "📊 Attendance & Student Portals",
            stats: [
                { val: "Real-Time", lbl: "Attendance Tracking" },
                { val: "24/7", lbl: "Portal Availability" }
            ],
            content: `
                <p>Students and faculty can seamlessly track attendance, manage leave applications, and view section timetables:</p>
                <ul>
                    <li><strong>Attendance Entry & Tracking:</strong> Real-time subject-wise and monthly attendance reports.</li>
                    <li><strong>Leave Management:</strong> Online leave application submission with instant faculty/HOD approvals.</li>
                    <li><strong>Academic Calendar:</strong> Timetables, exam schedules, and holiday notifications.</li>
                </ul>
            `,
            links: [
                { text: "Check Attendance", url: "check_attendance.php" },
                { text: "Student Login", url: "login.php" },
                { text: "Academic Calendar", url: "academic-calendar.php" }
            ]
        }
    ];

    // UI Controller Class
    class AIBotController {
        constructor() {
            this.triggerBtn = document.getElementById('aiBotTrigger');
            this.modal = document.getElementById('aiBotModal');
            this.closeBtn = document.getElementById('aiBotClose');
            this.clearBtn = document.getElementById('aiBotClear');
            this.chatBody = document.getElementById('aiBotBody');
            this.searchForm = document.getElementById('aiSearchForm');
            this.searchInput = document.getElementById('aiSearchInput');

            this.isOpen = false;
            this.initEvents();
        }

        initEvents() {
            if (!this.triggerBtn || !this.modal) return;

            // Toggle modal
            this.triggerBtn.addEventListener('click', () => this.toggleModal());
            if (this.closeBtn) this.closeBtn.addEventListener('click', () => this.closeModal());
            if (this.clearBtn) this.clearBtn.addEventListener('click', () => this.clearChat());

            // Handle search submit
            if (this.searchForm) {
                this.searchForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleUserQuery();
                });
            }

            // Quick chip clicks delegate
            document.addEventListener('click', (e) => {
                const chip = e.target.closest('.ai-chip');
                if (chip && chip.dataset.query) {
                    this.searchInput.value = chip.dataset.query;
                    this.handleUserQuery();
                }
            });
        }

        toggleModal() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.modal.classList.add('active');
                this.searchInput.focus();
            } else {
                this.modal.classList.remove('active');
            }
        }

        closeModal() {
            this.isOpen = false;
            this.modal.classList.remove('active');
        }

        clearChat() {
            // Keep welcome message & quick chips
            const welcome = this.chatBody.querySelector('.ai-msg-welcome');
            this.chatBody.innerHTML = '';
            if (welcome) {
                this.chatBody.appendChild(welcome);
            } else {
                this.addWelcomeMessage();
            }
        }

        addWelcomeMessage() {
            const welcomeHTML = `
                <div class="ai-msg bot ai-msg-welcome">
                    <div class="ai-msg-bubble">
                        <h6>🤖 Welcome to CSD & CSIT AI Search!</h6>
                        <p>Ask me anything about CSD & CSIT courses, faculty, labs, house points, clubs, or placement statistics!</p>
                        <div class="ai-quick-prompts">
                            <span class="ai-quick-title">Quick Topics:</span>
                            <div class="ai-chips-wrapper">
                                <button class="ai-chip" data-query="CSD CSIT BTech Programs">🎓 Programs Offered</button>
                                <button class="ai-chip" data-query="Faculty HOD Details">👨‍🏫 Faculty & HOD</button>
                                <button class="ai-chip" data-query="Placements Packages Recruiters">🏆 Placement Stats</button>
                                <button class="ai-chip" data-query="House System Points Leaderboard">🛡️ House System</button>
                                <button class="ai-chip" data-query="Student Clubs SDC Startup Swecha">🚀 Student Clubs</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            this.chatBody.innerHTML = welcomeHTML;
        }

        async handleUserQuery() {
            const queryText = this.searchInput.value.trim();
            if (!queryText) return;

            // Render User Message
            this.appendUserMessage(queryText);
            this.searchInput.value = '';

            // Show Typing Indicator
            const typingId = this.showTypingIndicator();

            try {
                // Fetch accurate live data from original MySQL database API
                const res = await fetch('api/ai_search.php?q=' + encodeURIComponent(queryText));
                const dbData = await res.json();

                this.removeTypingIndicator(typingId);

                if (dbData && dbData.success) {
                    this.appendBotResponse(dbData);
                } else {
                    const noDataMsg = (dbData && dbData.message) ? dbData.message : `No matching results found for '${this.escapeHTML(queryText)}'.`;
                    this.appendBotResponse({
                        title: "🔍 Database Search Result",
                        stats: [{ val: "0 Matches", lbl: "Database Query" }],
                        content: `<p>${noDataMsg}</p>`,
                        links: [
                            { text: "Explore Dashboard", url: "explore.php" },
                            { text: "Students Info", url: "students_overview.php" }
                        ]
                    });
                }
            } catch (err) {
                console.warn("Live DB Search error:", err);
                this.removeTypingIndicator(typingId);
                this.appendBotResponse({
                    title: "🔍 Database Search Result",
                    stats: [{ val: "0 Matches", lbl: "Database Query" }],
                    content: `<p>No matching results found for '${this.escapeHTML(queryText)}'.</p>`,
                    links: [
                        { text: "Explore Dashboard", url: "explore.php" }
                    ]
                });
            }
        }

        appendUserMessage(text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'ai-msg user';
            msgDiv.innerHTML = `
                <div class="ai-msg-bubble">
                    ${this.escapeHTML(text)}
                </div>
            `;
            this.chatBody.appendChild(msgDiv);
            this.scrollToBottom();
        }

        showTypingIndicator() {
            const typingId = 'typing_' + Date.now();
            const typingDiv = document.createElement('div');
            typingDiv.className = 'ai-msg bot';
            typingDiv.id = typingId;
            typingDiv.innerHTML = `
                <div class="ai-msg-bubble">
                    <div class="ai-typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            this.chatBody.appendChild(typingDiv);
            this.scrollToBottom();
            return typingId;
        }

        removeTypingIndicator(id) {
            const elem = document.getElementById(id);
            if (elem) elem.remove();
        }

        findBestMatch(query) {
            const lowerQuery = query.toLowerCase();
            const tokens = lowerQuery.split(/\s+/).filter(t => t.length > 2);

            let bestMatch = null;
            let highestScore = 0;

            knowledgeBase.forEach(item => {
                let score = 0;
                item.keywords.forEach(kw => {
                    if (lowerQuery.includes(kw)) {
                        score += 3;
                    }
                    tokens.forEach(tok => {
                        if (kw.includes(tok)) score += 1;
                    });
                });

                if (score > highestScore) {
                    highestScore = score;
                    bestMatch = item;
                }
            });

            if (bestMatch && highestScore > 0) {
                return bestMatch;
            }

            // Fallback Response when no database record matches
            return {
                title: "🔍 Database Search",
                stats: [
                    { val: "0 Matches", lbl: "Database Query" }
                ],
                content: `
                    <p>No matching results found in SRKREC CSD & CSIT database for <em>"${this.escapeHTML(query)}"</em>.</p>
                    <p>You can try searching for:</p>
                    <ul>
                        <li><strong>Student Names / Roll Numbers:</strong> e.g., <em>"24B91A0749"</em> or <em>"Keta Purna Pavan"</em></li>
                        <li><strong>Faculty Members & HOD:</strong> e.g., <em>"Suresh Babu"</em>, <em>"Bhanu Rajesh"</em></li>
                        <li><strong>House Standings & Points:</strong> e.g., <em>"least house points"</em>, <em>"Aakash House"</em></li>
                        <li><strong>Events & Competitions:</strong> e.g., <em>"Jaitra 2k26"</em></li>
                    </ul>
                `,
                links: [
                    { text: "Explore Dashboard", url: "explore.php" },
                    { text: "Faculty Directory", url: "faculty.php" },
                    { text: "Students Info", url: "students_overview.php" }
                ]
            };
        }

        appendBotResponse(item) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'ai-msg bot';

            let statsHTML = '';
            if (item.stats && item.stats.length > 0) {
                statsHTML = `
                    <div class="ai-stat-grid">
                        ${item.stats.map(s => `
                            <div class="ai-stat-card">
                                <div class="val">${s.val}</div>
                                <div class="lbl">${s.lbl}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            let linksHTML = '';
            if (item.links && item.links.length > 0) {
                linksHTML = `
                    <div class="ai-links-container">
                        ${item.links.map(l => `
                            <a href="${l.url}" class="ai-action-btn">
                                <i class="fas fa-arrow-right"></i> ${l.text}
                            </a>
                        `).join('')}
                    </div>
                `;
            }

            let dbBadgeHTML = (item.source === 'live_db') ? `<span style="font-size: 10px; background: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.4); padding: 2px 8px; border-radius: 10px; margin-left: 6px; font-weight: 600; vertical-align: middle;">Live Database</span>` : '';

            msgDiv.innerHTML = `
                <div class="ai-msg-bubble">
                    <h6>${item.title} ${dbBadgeHTML}</h6>
                    ${statsHTML}
                    ${item.content}
                    ${linksHTML}
                </div>
            `;

            this.chatBody.appendChild(msgDiv);
            this.scrollToBottom();
        }

        scrollToBottom() {
            this.chatBody.scrollTop = this.chatBody.scrollHeight;
        }

        escapeHTML(str) {
            return str.replace(/[&<>'"]/g, 
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag] || tag)
            );
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => new AIBotController());
    } else {
        new AIBotController();
    }

})();
