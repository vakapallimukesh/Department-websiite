/**
 * AI Department Assistant — Google Gemini API & Semantic RAG Engine
 * SRKREC CSD & CSIT Departments
 *
 * Architecture:
 * 1. Intent & Website Relevance Detector
 * 2. Vector Knowledge Matrix Retrieval (RAG Context Injection)
 * 3. Secure Backend Gemini API Proxy Integration (`api/gemini_chat.php` reading `.env`)
 * 4. Client-side Gemini API Integration Support (`window.GEMINI_API_KEY`)
 * 5. Multi-turn History Context Memory
 * 6. 100% Reliable Offline Vector RAG Fallback
 */

const ChatbotService = (function () {
    'use strict';

    let userApiKey = null;
    let isProcessingRequest = false;

    // Multi-turn Conversation Memory State
    let conversationContext = {
        activeEntity: null,
        activeTopic: null,
        lastQuery: null,
        history: [] // Array of { role: 'user'|'model', text: string }
    };

    /**
     * =========================================================================
     * 1. COMPLETE WEBSITE GRANULAR KNOWLEDGE MATRIX
     * =========================================================================
     */
    const KNOWLEDGE_MATRIX = [
        {
            id: 'dept_overview',
            category: 'About',
            title: 'Department Overview & Establishment',
            keywords: ['about department', 'tell me about the department', 'department overview', 'department history', 'what is this department', 'about csd', 'about csit'],
            tokens: ['about', 'overview', 'history', 'establishment', 'csd', 'csit', 'srkrec', 'bhimavaram'],
            content: `The Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram offers cutting-edge B.Tech programs equipped with 200+ high-end PCs, specialized AI & ML labs, Cloud & IoT suites, and active research centers under HODs Dr. M. Suresh Babu and Dr. N. Gopala Krishna Murthy.`,
            url: 'explore.php',
            ctaText: 'Explore Department Overview →'
        },
        {
            id: 'dept_vision',
            category: 'Vision',
            title: 'Department Vision',
            keywords: ['vision', 'department vision', 'what is vision', 'aim', 'goal'],
            tokens: ['vision', 'aim', 'future vision', 'goal'],
            content: `Department Vision: To evolve into a premier center of academic excellence and research in Computer Science, Design Thinking, and Information Technology, empowering students to become globally competent professionals and innovative leaders.`,
            url: 'explore.php',
            ctaText: 'View Department Overview →'
        },
        {
            id: 'dept_mission',
            category: 'Mission',
            title: 'Department Mission Statements',
            keywords: ['mission', 'department mission', 'what is mission', 'objectives', 'goals'],
            tokens: ['mission', 'objectives', 'goals', 'pillars'],
            content: `Department Mission Pillars:
• M1: Provide robust foundational and advanced education in CSD & CSIT through semester-updated curricula.
• M2: Establish state-of-the-art laboratory infrastructure and research centers.
• M3: Foster industry-institute collaboration, hackathons, and entrepreneurial startup incubation.
• M4: Impart ethical engineering practices, leadership, and lifelong learning capabilities.`,
            url: 'explore.php',
            ctaText: 'View Department Overview →'
        },
        {
            id: 'admission_process',
            category: 'Admissions',
            title: 'Admission Process & Application Procedure',
            keywords: ['how to apply', 'admission process', 'application process', 'how to join', 'how do i apply', 'how can i apply', 'eapcet', 'ecet', 'convenor quota', 'management quota', 'admission procedure'],
            tokens: ['admission', 'admissions', 'apply', 'application', 'eapcet', 'eamcet', 'ecet', 'convenor', 'management', 'process', 'procedure', 'form'],
            content: `Admission Process & Application Procedure:
• B.Tech Regular (1st Year): Pass in 10+2 / Intermediate with Physics, Chemistry & Mathematics (MPC). Admission is based on rank scored in AP EAPCET (EAMCET) counselling.
• Lateral Entry (2nd Year): Diploma holders or B.Sc graduates can join directly into 2nd year through AP ECET counselling.
• Seat Allocation & Quotas: 70% Category-A (Convenor Quota via EAPCET counselling) & 30% Category-B (Management / NRI Quota).`,
            url: 'academics.php',
            ctaText: 'View Admissions Info Page →'
        },
        {
            id: 'admission_eligibility',
            category: 'Admissions',
            title: 'Academic Eligibility Requirements',
            keywords: ['eligibility', 'eligible', 'academic requirement', 'qualification required', 'marks', 'percentage', 'criteria', 'am i eligible'],
            tokens: ['eligibility', 'eligible', 'academic requirement', 'qualification required', 'marks', 'percentage', 'criteria'],
            content: `Academic Eligibility Requirements:
• B.Tech Regular (4 Years): Minimum 45% aggregate marks in 10+2 / Intermediate with Physics, Chemistry & Mathematics (MPC) + valid rank in AP EAPCET.
• B.Tech Lateral Entry (3 Years): 3-Year Diploma in Engineering/Technology or B.Sc degree + valid rank in AP ECET.`,
            url: 'academics.php',
            ctaText: 'View Admissions Info Page →'
        },
        {
            id: 'hod_profiles',
            category: 'Leadership',
            title: 'Heads of Department (HODs)',
            keywords: ['hod', 'hods', 'who is hod', 'who are hods', 'head of department', 'department head', 'head of dept', 'suresh babu', 'gopala krishna', 'ngk murthy', 'in charge', 'who heads'],
            tokens: ['hod', 'hods', 'head of department', 'department head', 'head of dept', 'suresh babu', 'gopala krishna', 'ngk murthy', 'in charge', 'who heads'],
            content: `Heads of Department (HODs):
• Computer Science & Design (CSD): Dr. M. Suresh Babu — Professor & HOD (Ph.D JNTU, 20+ Yrs Exp, suresh.mudunuri@srkrec.ac.in).
• Computer Science & Information Technology (CSIT): Dr. N. Gopala Krishna Murthy — Professor & HOD (Ph.D JNTU, 18+ Yrs Exp, gopinukala@gmail.com).`,
            url: 'faculty.php',
            ctaText: 'View Faculty Directory →'
        },
        {
            id: 'faculty_directory',
            category: 'Faculty',
            title: 'Complete 19-Member Faculty Roster',
            keywords: ['faculty', 'faculties', 'who are faculty members', 'tell me about the faculty', 'who teaches', 'who works', 'professors', 'teachers', 'faculty list', 'teaching staff'],
            tokens: ['faculty', 'faculties', 'professors', 'teachers', 'staff', 'teaching staff', 'who teaches', 'who works', 'faculty list'],
            content: `Department Faculty Directory (19 Members):
Our department has 19 highly qualified faculty members across CSD and CSIT led by HODs Dr. M. Suresh Babu and Dr. NGK Murthy. Key faculty members include Trinadh Sir (Cyber Security & Python Lead), P. Manoj Sir (GenAI Specialist), A. Aswini Priyanka Madam (Cloud Lead), S. Mohan Krishna Sir (AI/ML), Dr. K. Srinivasa Rao Sir (Networks), N. Navya Madam, and Neti Praveen Sir.`,
            url: 'faculty.php',
            ctaText: 'View Complete Faculty Directory →'
        },
        {
            id: 'trinadh_profile',
            category: 'Faculty',
            title: 'Trinadh Sir Faculty Profile',
            keywords: ['trinadh', 'trinadh sir', 'who is trinadh', 'tell me about trinadh', 'what does trinadh teach', 'trinadh qualification'],
            tokens: ['trinadh', 'trinadh sir', 'satya trinadh', 'kvvstnaidu', 'trinadh naidu'],
            content: `Faculty Profile — K V V Satya Trinadh Naidu (Trinadh Sir):
• Designation: Assistant Professor (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2016) | Experience: 7+ Years
• Specialization: Cyber Security, Java, Python Application Development
• Subjects Taught: Cyber Security, Java Programming, Python
• Contact Email: kvvstnaidu@srkrec.ac.in
• Achievements: Lead Cybersecurity Advisor (8+ Publications, 9+ Projects).`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'courses_offered',
            category: 'Academics',
            title: 'B.Tech Degree Programs Offered',
            keywords: ['courses', 'programs', 'btech', 'degree', 'curriculum', 'syllabus', 'what courses are offered', 'branches', 'csd', 'csit'],
            tokens: ['courses', 'programs', 'btech', 'degree', 'curriculum', 'syllabus', 'branch', 'csd', 'csit', 'subjects offered'],
            content: `Degree Programs Offered:
• B.Tech in Computer Science & Design (CSD): Combines core computer science with UI/UX, game design, multimedia computing, and software architecture.
• B.Tech in Computer Science & Information Technology (CSIT): Focuses on IT infrastructure, cloud computing, cybersecurity, database administration, and web applications.`,
            url: 'academics.php',
            ctaText: 'View Academics & Syllabus →'
        },
        {
            id: 'placements_info',
            category: 'Placements',
            title: 'Placements & Career Opportunities',
            keywords: ['placements', 'recruiters', 'companies', 'jobs', 'salary', 'packages', 'hiring', 'tcs', 'infosys', 'wipro', 'amazon', 'cognizant', 'accenture', 'tell me about placements'],
            tokens: ['placements', 'recruiters', 'companies', 'jobs', 'salary', 'packages', 'hiring', 'tcs', 'infosys', 'wipro', 'amazon', 'cognizant', 'accenture'],
            content: `Placements & Career Opportunities:
Students placed in top MNCs including TCS, Infosys, Wipro, Cognizant, Accenture, Amazon, Capgemini, Tech Mahindra, ValueLabs, and Hexaware. The Placement Cell conducts coding bootcamps, mock interviews, and internship drives.`,
            url: 'placements.php',
            ctaText: 'View Placement Records →'
        },
        {
            id: 'facilities_labs',
            category: 'Facilities',
            title: 'Department Laboratories & Infrastructure',
            keywords: ['facilities', 'labs', 'laboratory', 'infrastructure', 'ai ml lab', 'mac lab', 'cloud lab', 'library', 'computers', 'what facilities are available', 'what labs do we have'],
            tokens: ['facilities', 'labs', 'laboratory', 'infrastructure', 'ai ml lab', 'mac lab', 'cloud lab', 'library', 'computers'],
            content: `Department Facilities & Labs:
• AI & ML Specialized Lab: High-performance computing workstations configured for deep learning.
• Cloud & IoT Lab: Equipped with smart hardware kits, Raspberry Pi, and AWS cloud suites.
• Modern Computer Labs: 200+ high-end systems connected with gigabit fiber internet.
• Department Library: Collection of textbooks and IEEE digital subscriptions.`,
            url: 'ai-ml-lab.php',
            ctaText: 'Explore AI & ML Lab →'
        },
        {
            id: 'events_overview',
            category: 'Events',
            title: 'Department Events, Hackathons & Workshops',
            keywords: ['events', 'hackathons', 'workshops', 'seminars', 'fest', 'jaitra', 'pitchathon', 'competitions', 'what events are there', 'latest event'],
            tokens: ['events', 'hackathons', 'workshops', 'seminars', 'fest', 'jaitra', 'pitchathon', 'competitions'],
            content: `Department Events & Activities:
• Jaitra 2k26 (Annual Fest): March 15, 2026 | Main Campus Auditorium
• National Level Technical Hackathon 2026: March 20, 2026 (24-Hour Overnight)
• Ethical Hacking Workshop: February 28, 2026 (Led by Trinadh Sir)
• Inter-House Technical Championship: March 12, 2026 (Jal, Agni, Vayu, Akash, Prudhvi)`,
            url: 'events_overview.php',
            ctaText: 'View Events Overview Page →'
        },
        {
            id: 'student_houses',
            category: 'Student Houses',
            title: 'Five Student Houses (Jal, Agni, Vayu, Akash, Prudhvi)',
            keywords: ['houses', 'jal', 'agni', 'vayu', 'akash', 'prudhvi', 'student houses', 'what houses are there', 'five houses'],
            tokens: ['houses', 'jal', 'agni', 'vayu', 'akash', 'prudhvi', 'student houses', 'house leaderboard'],
            content: `Five Student Houses:
• 💧 Jal — Water Element (Adaptability & Analytics)
• 🔥 Agni — Fire Element (Passion & Innovation)
• 💨 Vayu — Air Element (Agile Development & Speed)
• 🌌 Akash — Ether/Sky Element (Vision & AI/Cloud)
• 🌍 Prudhvi — Earth Element (Ethics & Discipline)`,
            url: 'houses_dashboard.php',
            ctaText: 'House Dashboard & Standings →'
        },
        {
            id: 'contact_info',
            category: 'Contact',
            title: 'Contact Information & Campus Address',
            keywords: ['contact', 'address', 'location', 'phone', 'email', 'where is college', 'reach out'],
            tokens: ['contact', 'address', 'location', 'phone', 'email', 'bhimavaram', 'srkrec'],
            content: `Contact Information:
• Address: SRKR Engineering College, SRKR Marg, Bhimavaram, Andhra Pradesh 534204
• Department Email: csd_csit@srkrec.ac.in
• Phone: +91 9876543210 (Dept Office)`,
            url: 'footer.php',
            ctaText: 'View College Location →'
        },
        {
            id: 'startup_ecosystem',
            category: 'Startups & Innovation',
            title: 'Startup Club & Student Innovation Hub',
            keywords: ['startups', 'startup', 'startup club', 'innovation', 'entrepreneurship', 'companies', 'business', 'ecosystem', 'tell me about startups', 'what startups are there'],
            tokens: ['startups', 'startup', 'innovation', 'entrepreneur', 'entrepreneurship', 'hub', 'business', 'incubator', 'seed'],
            content: `Startup Club & Innovation Ecosystem:
The SRKREC Startup Club empowers student entrepreneurs to build innovative solutions and launch real-world ventures. 
• Impact: 5+ active student startups, 200+ daily customers served, operating in 3+ industry sectors.
• Activities: We provide mentorship, funding guidance, workspace, and resources to transform ideas into successful businesses.`,
            url: 'startup_club.php',
            ctaText: 'Explore Startup Ecosystem →'
        },
        {
            id: 'coding_club',
            category: 'Clubs',
            title: 'Coding Club',
            keywords: ['coding club', 'programming club', 'competitive programming', 'hackathons', 'open source', 'coders'],
            tokens: ['coding', 'programming', 'codechef', 'hackerRank', 'github', 'hackathon'],
            content: `Coding Club:
A vibrant community of 500+ active members passionate about programming.
• Activities: Weekly competitive programming contests, ACM ICPC training, 24-48 hour hackathons, and open source collaboration on GitHub.
• Stats: Organized 50+ events and won 25+ national hackathons.`,
            url: 'coding-club.php',
            ctaText: 'Join the Coding Club →'
        },
        {
            id: 'cybersecurity_club',
            category: 'Clubs',
            title: 'Cybersecurity Club',
            keywords: ['cybersecurity club', 'security', 'ethical hacking', 'hacking', 'cyber club', 'infosec'],
            tokens: ['cybersecurity', 'security', 'hacking', 'ethical', 'cyber', 'infosec'],
            content: `Cybersecurity Club:
Focuses on ethical hacking, network security, and information security (InfoSec). We conduct workshops on penetration testing, CTF (Capture The Flag) competitions, and secure application development.`,
            url: 'cybersecurity-club.php',
            ctaText: 'View Cybersecurity Club →'
        },
        {
            id: 'swecha_club',
            category: 'Clubs',
            title: 'Swecha Club',
            keywords: ['swecha', 'swecha club', 'free software', 'open source software', 'linux'],
            tokens: ['swecha', 'free software', 'linux', 'open source'],
            content: `Swecha Club:
Dedicated to promoting Free and Open Source Software (FOSS). Students learn about Linux administration, open-source technologies, and participate in state-wide Swecha workshops and camps.`,
            url: 'swecha_club.php',
            ctaText: 'View Swecha Club →'
        },
        {
            id: 'sdc_club',
            category: 'Clubs',
            title: 'Skill Development Center (SDC)',
            keywords: ['sdc', 'skill development center', 'apssdc', 'skills', 'training'],
            tokens: ['sdc', 'skill', 'development', 'apssdc', 'training', 'certification'],
            content: `Skill Development Center (SDC):
Collaborates with APSSDC and industry partners to provide specialized certification courses, technical training programs, and hands-on workshops to make students industry-ready.`,
            url: 'sdc_club.php',
            ctaText: 'View SDC Details →'
        },
        {
            id: 'students_overview',
            category: 'Students',
            title: 'Student Activities & Academic Life',
            keywords: ['students', 'student activities', 'tell me about students', 'what do students do', 'student life', 'student portal', 'student dashboard'],
            tokens: ['students', 'student', 'activities', 'dashboard', 'portal', 'life'],
            content: `Students & Activities:
Our students are actively engaged in rigorous academics, innovative technical clubs (Coding, Swecha, Cybersecurity, SDC, Startup), and the House system (Jal, Agni, Vayu, Akash, Prudhvi). 
They manage their attendance, internal marks, and leave applications via a centralized Student Dashboard, fostering a highly disciplined and tech-driven academic environment.`,
            url: 'students_overview.php',
            ctaText: 'View Students Overview →'
        },
        {
            id: 'internships_overview',
            category: 'Internships',
            title: 'Student Internships & Training',
            keywords: ['internships', 'internship', 'intern', 'interns', 'stipend', 'ppo', 'training', 'pre-placement', 'where can students get internships', 'internship opportunities', 'internship programs'],
            tokens: ['internships', 'internship', 'intern', 'interns', 'stipend', 'ppo', 'training', 'corporate', 'opportunities'],
            content: `Student Internships & Training:
We build real-world engineering skills through corporate internships, paid industrial stipends, and pre-placement training programs.
• Stats: 120+ Students Interning, 85% PPO (Pre-Placement Offer) Conversion, 45+ Corporate Partners. The highest stipend is ₹50K/month.
• Featured Opportunities: Software Development Intern (Amazon, ₹45k/mo), Full Stack Web Developer (TCS, ₹35k/mo), Data Science & AI Intern (Wipro, ₹50k/mo).`,
            url: 'internships.php',
            ctaText: 'View Internship Opportunities →'
        }
    ];

    /**
     * =========================================================================
     * 2. RELEVANCE VECTOR RERANKING ENGINE
     * =========================================================================
     */
    function calculateChunkScore(chunk, rawQueryStr) {
        let score = 0;
        const queryLower = rawQueryStr.toLowerCase().trim();

        for (const kw of chunk.keywords) {
            if (queryLower.includes(kw.toLowerCase())) {
                score += 200;
            }
        }

        const tokens = queryLower.replace(/[^a-z0-9\s]/g, ' ').split(/\s+/).filter(t => t.length > 2);
        for (const token of tokens) {
            if (chunk.tokens.includes(token)) score += 30;
            if (new RegExp(`\\b${token}\\b`, 'i').test(chunk.title)) score += 40;
            if (new RegExp(`\\b${token}\\b`, 'i').test(chunk.category)) score += 35;
        }

        if (/\b(eligibility|eligible)\b/i.test(queryLower)) {
            if (chunk.id === 'admission_eligibility') score += 150;
            else score -= 80;
        }

        if (/\b(apply|admission process|how to apply|how to join)\b/i.test(queryLower)) {
            if (chunk.id === 'admission_process') score += 150;
            if (chunk.id === 'dept_mission' || chunk.id === 'contact_info') score -= 200;
        }

        if (/\b(contact|address|location|phone|email)\b/i.test(queryLower)) {
            if (chunk.id === 'contact_info') score += 150;
        }

        if (/\b(about department|tell me about department|overview)\b/i.test(queryLower) && !/\b(faculty|events|houses|courses|facilities|placements)\b/i.test(queryLower)) {
            if (chunk.id === 'dept_overview') score += 150;
        }

        return score;
    }

    function searchKnowledgeVector(rawQuery) {
        let scoredChunks = KNOWLEDGE_MATRIX.map(chunk => {
            return {
                chunk: chunk,
                score: calculateChunkScore(chunk, rawQuery)
            };
        });

        scoredChunks.sort((a, b) => b.score - a.score);

        const top = scoredChunks[0];
        if (top && top.score > 20) {
            return top.chunk;
        }

        return null;
    }

    /**
     * Client-side Gemini API Invocation
     */
    async function callGeminiDirect(userInput, matchedChunk, apiKey) {
        const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

        let systemInstruction = `You are the official AI Assistant for the Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram.\n\n`;
        if (matchedChunk) {
            systemInstruction += `VERIFIED WEBSITE RAG CONTEXT:\nTitle: ${matchedChunk.title}\nContent: ${matchedChunk.content}\n\n`;
            systemInstruction += `Instructions: Use the verified website context above to answer accurately. Do not invent fake names or dates. Format output using clean HTML/Markdown.`;
        } else {
            systemInstruction += `Instructions: Respond naturally and helpfully to general computer science, coding, placement, or casual conversation questions as a friendly department AI assistant.`;
        }

        const contents = [];
        contents.push({ role: 'user', parts: [{ text: systemInstruction }] });
        contents.push({ role: 'model', parts: [{ text: 'Understood. Ready to assist.' }] });

        conversationContext.history.slice(-4).forEach(msg => {
            contents.push({
                role: msg.role === 'user' ? 'user' : 'model',
                parts: [{ text: msg.text }]
            });
        });

        contents.push({ role: 'user', parts: [{ text: userInput }] });

        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contents: contents })
        });

        if (!response.ok) {
            throw new Error(`Gemini Direct API status ${response.status}`);
        }

        const data = await response.json();
        const replyText = data.candidates?.[0]?.content?.parts?.[0]?.text;
        if (!replyText) {
            throw new Error('Invalid Gemini API payload');
        }

        return replyText;
    }

    /**
     * Local Synthesis Fallback Engine
     */
    function synthesizeLocalAnswer(matchedChunk, rawQuery) {
        if (!matchedChunk) {
            return {
                answer: `I couldn't find that specific information on the department website, and my AI connection is currently offline. You can contact the department office for further details.`,
                ctaLinks: [{ text: 'Contact Department →', url: 'footer.php' }],
                suggestions: ['What courses are offered?', 'Who is the HOD?', 'How to apply for admission?']
            };
        }

        return {
            answer: `<strong>${matchedChunk.title}:</strong><br><br>${matchedChunk.content.replace(/\n/g, '<br>')}`,
            ctaLinks: [{ text: matchedChunk.ctaText, url: matchedChunk.url }],
            suggestions: ['What courses are offered?', 'Who is the HOD?', 'Tell me about placements', 'How to apply for admission?']
        };
    }

    // Response Cache for repeated questions
    const responseCache = new Map();

    /**
     * Public API Interface
     */
    async function getBotResponse(userInput, config = {}) {
        if (isProcessingRequest) {
            console.log('[CHATBOT] Request ignored (debounced).');
            return { answer: 'Please wait, I am already processing your previous request.' };
        }
        isProcessingRequest = true;
        
        try {
            const timeStart = performance.now();
            console.log('[CHATBOT] Request started for:', userInput);

            const normalizedQuery = userInput.toLowerCase().trim();
            if (responseCache.has(normalizedQuery)) {
                console.log('[CHATBOT] Cache hit for:', normalizedQuery);
                const cachedRes = responseCache.get(normalizedQuery);
                const timeEnd = performance.now();
                console.log(`[CHATBOT] Total: ${(timeEnd - timeStart).toFixed(2)} ms`);
                return cachedRes;
            }

            // Step 1: Fast Greeting Intercept (Saves Gemini Quota)
            const isGreeting = /^(hi|hello|hey|greetings|good morning|good afternoon|good evening)$/i.test(normalizedQuery);
            if (isGreeting) {
                console.log('[CHATBOT] Greeting detected. Handling locally to save API quota.');
                const greetingRes = {
                    answer: `Hey! 👋 I'm the Department Assistant. How can I help you today?`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['What courses are offered?', 'Who is the HOD?', 'How to apply for admission?']
                };
                responseCache.set(normalizedQuery, greetingRes);
                
                const timeTotalEnd = performance.now();
                console.log(`[CHATBOT] Total: ${(timeTotalEnd - timeStart).toFixed(2)} ms`);
                return greetingRes;
            }

            conversationContext.lastQuery = userInput;

            // Step 2: Perform RAG Search on Website Knowledge Matrix
            let timeRetrievalStart = performance.now();
            let matchedChunk = searchKnowledgeVector(userInput);
            const timeRetrievalEnd = performance.now();
            console.log(`[CHATBOT] Retrieval: ${(timeRetrievalEnd - timeRetrievalStart).toFixed(2)} ms`);

            let finalResponse = null;

            // Step 2.5: Smart Gemini Bypassing for Factual Queries
            if (matchedChunk) {
                const wordCount = normalizedQuery.split(/\s+/).length;
                const requiresReasoning = /\b(why|how|difference|compare|explain|give|example|what is|skills|need|help)\b/i.test(normalizedQuery);
                
                if (wordCount <= 6 && !requiresReasoning) {
                    console.log('[CHATBOT] Smart Bypass: Factual query. Skipping Gemini to save API quota.');
                    finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                    responseCache.set(normalizedQuery, finalResponse);
                    
                    const timeEnd = performance.now();
                    console.log(`[CHATBOT] Total: ${(timeEnd - timeStart).toFixed(2)} ms`);
                    return finalResponse;
                }
            }

        // Step 2: Try Backend PHP Proxy (api/gemini_chat.php reading .env)
        const timeGeminiStart = performance.now();
        const proxyUrl = config.remoteApiUrl || 'api/gemini_chat.php';
        try {
            const proxyResponse = await fetch(proxyUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    prompt: userInput,
                    context: matchedChunk,
                    history: conversationContext.history.slice(-4),
                    apiKey: userApiKey || config.apiKey
                })
            });

            if (proxyResponse.ok) {
                const proxyData = await proxyResponse.json();
                if (proxyData.status === 'success' && proxyData.reply) {
                    conversationContext.history.push({ role: 'user', text: userInput });
                    conversationContext.history.push({ role: 'model', text: proxyData.reply });

                    finalResponse = {
                        answer: proxyData.reply.replace(/\n/g, '<br>'),
                        ctaLinks: matchedChunk ? [{ text: matchedChunk.ctaText, url: matchedChunk.url }] : [],
                        suggestions: ['What courses are offered?', 'Who is the HOD?', 'Tell me about placements']
                    };
                } else if (proxyData.status === 'api_error' && proxyData.message && proxyData.message.includes('429')) {
                    finalResponse = {
                        answer: `I am currently receiving too many requests (API Rate Limit). Please wait a moment, or ask me a direct question about the department which I can answer from my local database!`,
                        ctaLinks: [],
                        suggestions: ['Who is the HOD?', 'Tell me about placements', 'What are the five houses?']
                    };
                } else if (proxyData.status === 'api_error') {
                    finalResponse = {
                        answer: `<strong style="color: #ef4444;">API Error:</strong> ${proxyData.message}`,
                        ctaLinks: [],
                        suggestions: []
                    };
                }
            }
        } catch (err) {
            console.warn('Backend proxy check failed, checking direct Gemini client key...', err);
        }

        // Step 3: Try Direct Client API Key if set
        if (!finalResponse) {
            const clientKey = userApiKey || config.apiKey || (typeof window !== 'undefined' ? (window.GEMINI_API_KEY || localStorage.getItem('gemini_api_key')) : null);
            if (clientKey) {
                try {
                    const geminiText = await callGeminiDirect(userInput, matchedChunk, clientKey);
                    conversationContext.history.push({ role: 'user', text: userInput });
                    conversationContext.history.push({ role: 'model', text: geminiText });

                    finalResponse = {
                        answer: geminiText.replace(/\n/g, '<br>'),
                        ctaLinks: matchedChunk ? [{ text: matchedChunk.ctaText, url: matchedChunk.url }] : [],
                        suggestions: ['What courses are offered?', 'Who is the HOD?', 'Tell me about placements']
                    };
                } catch (err) {
                    console.warn('Client Gemini call failed, executing local RAG Engine:', err);
                }
            }
        }

        const timeGeminiEnd = performance.now();
        console.log(`[CHATBOT] Gemini: ${(timeGeminiEnd - timeGeminiStart).toFixed(2)} ms`);

        // Step 4: Local RAG Fallback
        if (!finalResponse) {
            finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
            conversationContext.history.push({ role: 'user', text: userInput });
            conversationContext.history.push({ role: 'model', text: finalResponse.answer });
        }

        // Cache the result
        responseCache.set(normalizedQuery, finalResponse);

        const timeTotalEnd = performance.now();
        console.log(`[CHATBOT] Total: ${(timeTotalEnd - timeStart).toFixed(2)} ms`);
        
        return finalResponse;
        } finally {
            isProcessingRequest = false;
        }
    }

    return {
        getBotResponse: getBotResponse,
        setApiKey: function (key) { userApiKey = key; },
        getKnowledgeMatrix: function () { return KNOWLEDGE_MATRIX; },
        getContextState: function () { return conversationContext; },
        resetContext: function () {
            conversationContext = { activeEntity: null, activeTopic: null, lastQuery: null, history: [] };
        }
    };
})();
