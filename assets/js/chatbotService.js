/**
 * AI Department Assistant — Google Gemini API & Hybrid RAG Engine
 * SRKREC CSD & CSIT Departments
 *
 * Architecture:
 * 1. Master Faculty Roster & Exact Keyword-First Matcher
 * 2. Intent & Website Relevance Detector
 * 3. Hybrid RAG Vector Retrieval (Exact Faculty Match FIRST, Semantic RAG SECOND)
 * 4. Post-Retrieval Faculty Profile Verification Guard
 * 5. Secure Backend Gemini API Proxy Integration (`api/gemini_chat.php` reading `.env`)
 * 6. Client-side Gemini API Integration Support (`window.GEMINI_API_KEY`)
 * 7. Multi-turn History Context Memory
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
     * 1. MASTER FACULTY ROSTER & SEARCHABLE ALIAS MATRIX (Requirement #8)
     * Every faculty member has a unique, searchable name and alias set.
     * =========================================================================
     */
    const MASTER_FACULTY_ROSTER = [
        {
            id: 'faculty_satyam',
            name: 'ANGARA SATYAM',
            searchableNames: ['satyam', 'angara satyam', 'a satyam', 'a. satyam', 'satyam sir', 'satyam madam', 'dr satyam', 'prof satyam', 'satyam mudunuri'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'asatyam@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Artificial Intelligence & Intelligent Systems',
            subjects: ['Artificial Intelligence', 'Python Programming'],
            content: `Faculty Profile — ANGARA SATYAM (Satyam Sir):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2016) | Experience: 7+ Years
• Specialization: Artificial Intelligence, Intelligent Systems, Python & Automation Frameworks
• Subjects Taught: Artificial Intelligence, Python Programming
• Contact Email: asatyam@srkrec.ac.in
• Achievements: AI Coding Contest Coach, Intelligent Automation Mentor.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_trinadh',
            name: 'K V V Satya Trinadh Naidu',
            searchableNames: ['trinadh', 'trinadh naidu', 'satya trinadh', 'k v v satya trinadh naidu', 'trinadh sir', 'trinadh madam', 'dr trinadh', 'prof trinadh', 'kvvstnaidu', 'satya trinadh naidu'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'kvvstnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Cyber Security, Java, Python Application Development',
            subjects: ['Cyber Security', 'Java Programming', 'Python'],
            content: `Faculty Profile — K V V Satya Trinadh Naidu (Trinadh Sir):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2016) | Experience: 7+ Years
• Specialization: Cyber Security, Java, Python Application Development
• Subjects Taught: Cyber Security, Java Programming, Python
• Contact Email: kvvstnaidu@srkrec.ac.in
• Achievements: Lead Cybersecurity Advisor (8+ Publications, 9+ Projects).`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_suresh_babu',
            name: 'Dr. Suresh Babu Mudunuri',
            searchableNames: ['suresh', 'suresh babu', 'm suresh babu', 'dr suresh babu', 'mudunuri suresh babu', 'suresh babu mudunuri', 'suresh sir', 'suresh babu sir', 'dr suresh'],
            designation: 'Professor & Head of Department (CSD)',
            department: 'CSD',
            email: 'suresh.mudunuri@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (JNTU, 2010)',
            specialization: 'AI, Machine Learning & Cloud Infrastructure',
            subjects: ['Machine Learning', 'Cloud Computing', 'Artificial Intelligence'],
            content: `Faculty Profile — Dr. Suresh Babu Mudunuri (Suresh Babu Sir):
• Designation: Professor & Head of Department (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: Ph.D in Computer Science (JNTU, 2010) | Experience: 20+ Years
• Specialization: Artificial Intelligence, Machine Learning & Cloud Infrastructure
• Contact Email: suresh.mudunuri@srkrec.ac.in
• Achievements: Head of Department (CSD), 35+ Research Publications, 15+ Funded Projects.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_ngk_murthy',
            name: 'Dr. N. Gopala Krishna Murthy',
            searchableNames: ['ngk murthy', 'gopala krishna', 'gopala krishna murthy', 'dr ngk murthy', 'n gopala krishna murthy', 'murthy', 'murthy sir', 'gopala krishna sir', 'ngk murthy sir'],
            designation: 'Professor & Head of Department (CSIT)',
            department: 'CSIT',
            email: 'gopinukala@gmail.com',
            qualification: 'Ph.D in Information Technology (JNTU, 2011)',
            specialization: 'Information Technology Systems & Enterprise Networks',
            subjects: ['Information Technology', 'Network Security'],
            content: `Faculty Profile — Dr. N. Gopala Krishna Murthy (NGK Murthy Sir):
• Designation: Professor & Head of Department (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: Ph.D in Information Technology (JNTU, 2011) | Experience: 18+ Years
• Specialization: Information Technology Systems & Enterprise Data Networks
• Contact Email: gopinukala@gmail.com
• Achievements: Head of Department (CSIT), 30+ Research Publications, 18+ Projects.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_manoj',
            name: 'P MANOJ',
            searchableNames: ['manoj', 'p manoj', 'pericherla manoj', 'manoj sir', 'manoj madam', 'dr manoj', 'prof manoj'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'manoj.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Prompt Engineering & Generative AI',
            subjects: ['Prompt Engineering', 'Generative AI', 'Python'],
            content: `Faculty Profile — P MANOJ (Manoj Sir):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2018) | Experience: 5+ Years
• Specialization: Prompt Engineering, Generative AI Models, LLM Fine-tuning
• Subjects Taught: Prompt Engineering, Generative AI, Python
• Contact Email: manoj.p@srkrec.ac.in
• Achievements: Generative AI Workshop Lead, 6+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aswini_priyanka',
            name: 'A. Aswini Priyanka',
            searchableNames: ['aswini', 'priyanka', 'aswini priyanka', 'a aswini priyanka', 'aswini madam', 'aswini sir', 'priyanka madam', 'dr aswini'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'aapriyanka@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2015)',
            specialization: 'Cloud Computing & Web Technologies',
            subjects: ['Cloud Computing', 'Web Technologies'],
            content: `Faculty Profile — A. Aswini Priyanka (Aswini Priyanka Madam):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2015) | Experience: 8+ Years
• Specialization: Cloud Computing, Virtualized Systems & Distributed Architectures
• Subjects Taught: Cloud Computing, Web Technologies
• Contact Email: aapriyanka@srkrec.ac.in
• Achievements: Cloud Certified Educator, 10+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mohan_krishna',
            name: 'S. Mohan Krishna',
            searchableNames: ['mohan krishna', 'mohan', 's mohan krishna', 'mohan krishna sir', 'mohan sir', 'krishna sir'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'mohanakrishna.seerla@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'AI, Machine Learning & Computer Vision',
            subjects: ['Artificial Intelligence', 'Machine Learning'],
            content: `Faculty Profile — S. Mohan Krishna (Mohan Krishna Sir):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2016) | Experience: 7+ Years
• Specialization: Artificial Intelligence, Machine Learning & Deep Learning Neural Networks
• Subjects Taught: Artificial Intelligence, Machine Learning
• Contact Email: mohanakrishna.seerla@srkrec.ac.in
• Achievements: AI & ML Research Mentor, 8+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surya_kumar',
            name: 'P S V SURYA KUMAR',
            searchableNames: ['surya kumar', 'surya', 'p s v surya kumar', 'surya kumar sir', 'psv surya kumar', 'surya sir'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'psvsuryakumar@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'IoT (Internet of Things) & Embedded Systems',
            subjects: ['IoT Architecture', 'Embedded Systems'],
            content: `Faculty Profile — P S V SURYA KUMAR (Surya Kumar Sir):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2017) | Experience: 6+ Years
• Specialization: Internet of Things (IoT), Smart Sensors & Real-time Edge Computing
• Subjects Taught: IoT Architecture, Embedded Systems
• Contact Email: psvsuryakumar@srkrec.ac.in
• Achievements: IoT Hardware Lab Director, 7+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinivasa_rao',
            name: 'Dr. K. Srinivasa Rao',
            searchableNames: ['srinivasa rao', 'srinivasa', 'dr k srinivasa rao', 'k srinivasa rao', 'srinivasa rao sir', 'dr srinivasa', 'srinivasa sir'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'ksrinivasarao@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (Andhra University, 2018)',
            specialization: 'Computer Networks & Security',
            subjects: ['Computer Networks', 'Information Security'],
            content: `Faculty Profile — Dr. K. Srinivasa Rao (Srinivasa Rao Sir):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: Ph.D in Computer Science (Andhra University, 2018) | Experience: 10+ Years
• Specialization: Computer Networks, Wireless Sensor Networks & Cyber Security
• Subjects Taught: Computer Networks, Information Security
• Contact Email: ksrinivasarao@srkrec.ac.in
• Achievements: Ph.D Doctorate Holder, 15+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_bhanu_rajesh',
            name: 'K. Bhanu Rajesh Naidu',
            searchableNames: ['bhanu rajesh', 'bhanu', 'bhanu rajesh naidu', 'k bhanu rajesh naidu', 'bhanu sir', 'rajesh sir', 'bhanu rajesh sir'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'kbrnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Cloud Computing & DevOps Systems',
            subjects: ['Cloud Computing', 'DevOps Systems'],
            content: `Faculty Profile — K. Bhanu Rajesh Naidu (Bhanu Rajesh Sir):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2017) | Experience: 6+ Years
• Specialization: Cloud Computing, DevOps Automation Pipelines & Containerized Applications
• Subjects Taught: Cloud Computing, DevOps Systems
• Contact Email: kbrnaidu@srkrec.ac.in
• Achievements: AWS Certified Solution Architect, 5+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aneela',
            name: 'N. Aneela',
            searchableNames: ['aneela', 'n aneela', 'aneela madam', 'aneela sir', 'dr aneela'],
            designation: 'Assistant Professor',
            department: 'CSD',
            email: 'aneela@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Data Mining',
            subjects: ['Machine Learning', 'Data Mining'],
            content: `Faculty Profile — N. Aneela (Aneela Madam):
• Designation: Assistant Professor (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (JNTUK, 2018) | Experience: 5+ Years
• Specialization: Machine Learning, Predictive Analytics & Natural Language Processing
• Subjects Taught: Machine Learning, Data Mining
• Contact Email: aneela@srkrec.ac.in
• Achievements: Data Science Mentor, 6+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sai_madhuri',
            name: 'M Sai Madhuri',
            searchableNames: ['sai madhuri', 'madhuri', 'm sai madhuri', 'madhuri madam'],
            designation: 'Teaching Assistant',
            department: 'CSD',
            email: 'madhuryamudundi@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Programming',
            subjects: ['Machine Learning Lab', 'Python Lab'],
            content: `Faculty Profile — M Sai Madhuri (Sai Madhuri Madam):
• Designation: Teaching Assistant (CSD)
• Department: Computer Science & Design (CSD)
• Qualification: M.Tech in CSE (SRKR, 2021) | Experience: 3+ Years
• Specialization: Machine Learning Lab & Python Programming Fundamentals
• Contact Email: madhuryamudundi@gmail.com
• Achievements: Lab Coordinator, 2+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_navya',
            name: 'N. NAVYA',
            searchableNames: ['navya', 'n navya', 'navya nallaparaju', 'navya madam', 'navya sir'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'navyanallaparaju@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Structures',
            subjects: ['Machine Learning', 'Data Structures'],
            content: `Faculty Profile — N. NAVYA (Navya Madam):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2017) | Experience: 6+ Years
• Specialization: Machine Learning, Predictive Analytics & Computer Vision
• Subjects Taught: Machine Learning, Data Structures
• Contact Email: navyanallaparaju@srkrec.ac.in
• Achievements: Active Research Scholar, 7+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_praveen',
            name: 'NETI PRAVEEN',
            searchableNames: ['praveen', 'neti praveen', 'n praveen', 'praveen sir', 'praveen madam'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'npraveen@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Machine Learning & Database Management',
            subjects: ['Machine Learning', 'Database Management Systems'],
            content: `Faculty Profile — NETI PRAVEEN (Praveen Sir):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2016) | Experience: 7+ Years
• Specialization: Machine Learning Models, Data Analytics & Computational Intelligence
• Subjects Taught: Machine Learning, Database Management Systems
• Contact Email: npraveen@srkrec.ac.in
• Achievements: Student Project Coordinator, 8+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sunil_varma',
            name: 'K V SUNIL VARMA',
            searchableNames: ['sunil varma', 'sunil', 'k v sunil varma', 'sunil varma sir', 'sunil sir'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'kvsunilvarma@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Software Engineering',
            subjects: ['Machine Learning', 'Software Engineering'],
            content: `Faculty Profile — K V SUNIL VARMA (Sunil Varma Sir):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2017) | Experience: 6+ Years
• Specialization: Machine Learning Algorithms, Statistical Data Analysis & Big Data
• Subjects Taught: Machine Learning, Software Engineering
• Contact Email: kvsunilvarma@srkrec.ac.in
• Achievements: Software Systems Mentor, 6+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mouna',
            name: 'P MOUNA',
            searchableNames: ['mouna', 'p mouna', 'penmetsa mouna', 'mouna madam', 'mouna sir'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'mouna.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Neural Networks',
            subjects: ['Machine Learning', 'Object Oriented Programming'],
            content: `Faculty Profile — P MOUNA (Mouna Madam):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2018) | Experience: 5+ Years
• Specialization: Machine Learning, Pattern Recognition & Neural Network Optimization
• Subjects Taught: Machine Learning, Object Oriented Programming
• Contact Email: mouna.p@srkrec.ac.in
• Achievements: Innovative Teaching Award, 5+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_krishna_veni',
            name: 'ANUSURI KRISHNA VENI',
            searchableNames: ['krishna veni', 'a krishna veni', 'akveni', 'anusuri krishna veni', 'krishna veni madam'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'akveni@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Mining',
            subjects: ['Machine Learning', 'Data Structures'],
            content: `Faculty Profile — ANUSURI KRISHNA VENI (Krishna Veni Madam):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2017) | Experience: 6+ Years
• Specialization: Machine Learning, Data Mining & Predictive Modeling
• Subjects Taught: Machine Learning, Data Structures
• Contact Email: akveni@srkrec.ac.in
• Achievements: Academic Excellence Mentor, 6+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_parvathi',
            name: 'D Parvathi',
            searchableNames: ['parvathi', 'd parvathi', 'parvathi madam', 'parvathi sir'],
            designation: 'Assistant Professor',
            department: 'CSIT',
            email: 'parvathiram21@gmail.com',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & C Programming',
            subjects: ['Machine Learning', 'C Programming'],
            content: `Faculty Profile — D Parvathi (Parvathi Madam):
• Designation: Assistant Professor (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (JNTUK, 2018) | Experience: 5+ Years
• Specialization: Machine Learning Algorithms, Statistical Pattern Recognition
• Subjects Taught: Machine Learning, C Programming
• Contact Email: parvathiram21@gmail.com
• Achievements: Faculty Publication Award, 5+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_vignya',
            name: 'K Sri Vigyna',
            searchableNames: ['vignya', 'vigyna', 'sri vignya', 'k sri vigyna', 'vignya madam', 'vignya sir'],
            designation: 'Teaching Assistant',
            department: 'CSIT',
            email: 'vignyak@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Lab',
            subjects: ['Machine Learning Lab', 'Python Lab'],
            content: `Faculty Profile — K Sri Vigyna (Vignya Madam):
• Designation: Teaching Assistant (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Qualification: M.Tech in CSE (SRKR, 2021) | Experience: 3+ Years
• Specialization: Machine Learning Lab & Python Practical Laboratories
• Contact Email: vignyak@gmail.com
• Achievements: Practical Lab Facilitator, 2+ Publications.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinu',
            name: 'M. SRINU',
            searchableNames: ['srinu', 'm srinu', 'mullu srinu', 'srinu sir'],
            designation: 'Faculty Member',
            department: 'CSIT',
            email: 'msrinu@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Computer Science & Information Technology',
            subjects: ['Computer Science', 'Information Technology'],
            content: `Faculty Profile — M. SRINU (Srinu Sir):
• Designation: Faculty Member (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Contact Email: msrinu@srkrec.edu.in
• Specialization: Computer Science & Information Technology Application Development.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surendra',
            name: 'J. MOHAN SURENDRA',
            searchableNames: ['mohan surendra', 'surendra', 'j mohan surendra', 'surendra sir'],
            designation: 'Faculty Member',
            department: 'CSIT',
            email: 'mohansurendra.j@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Software Systems & Information Technology',
            subjects: ['Software Systems', 'Information Technology'],
            content: `Faculty Profile — J. MOHAN SURENDRA (Surendra Sir):
• Designation: Faculty Member (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Contact Email: mohansurendra.j@srkrec.edu.in
• Specialization: Software Systems & Information Technology Education.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sudhakar',
            name: 'G. SUDHAKAR',
            searchableNames: ['sudhakar', 'g sudhakar', 'sudhakar sir'],
            designation: 'Faculty Member',
            department: 'CSIT',
            email: 'sudhakar.g@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Computer Science & Software Engineering',
            subjects: ['Software Engineering', 'Computer Science'],
            content: `Faculty Profile — G. SUDHAKAR (Sudhakar Sir):
• Designation: Faculty Member (CSIT)
• Department: Computer Science & Information Technology (CSIT)
• Contact Email: sudhakar.g@srkrec.edu.in
• Specialization: Computer Science & Software Engineering.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_girichar',
            name: 'K. GIRICHAR',
            searchableNames: ['girichar', 'giridhar', 'k girichar', 'girichar sir'],
            designation: 'Faculty Member',
            department: 'CSD',
            email: 'girichar.k@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSD',
            specialization: 'Computer Science & Design',
            subjects: ['Computer Science', 'Design Thinking'],
            content: `Faculty Profile — K. GIRICHAR (Girichar Sir):
• Designation: Faculty Member (CSD)
• Department: Computer Science & Design (CSD)
• Contact Email: girichar.k@srkrec.edu.in
• Specialization: Computer Science & Design Thinking.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        }
    ];

    /**
     * =========================================================================
     * 2. GENERAL WEBSITE GRANULAR KNOWLEDGE MATRIX
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
            keywords: ['hod', 'hods', 'who is hod', 'who are hods', 'head of department', 'department head', 'head of dept', 'in charge', 'who heads'],
            tokens: ['hod', 'hods', 'head of department', 'department head', 'head of dept', 'in charge', 'who heads'],
            content: `Heads of Department (HODs):
• Computer Science & Design (CSD): Dr. M. Suresh Babu — Professor & HOD (Ph.D JNTU, 20+ Yrs Exp, suresh.mudunuri@srkrec.ac.in).
• Computer Science & Information Technology (CSIT): Dr. N. Gopala Krishna Murthy — Professor & HOD (Ph.D JNTU, 18+ Yrs Exp, gopinukala@gmail.com).`,
            url: 'faculty.php',
            ctaText: 'View Faculty Directory →'
        },
        {
            id: 'faculty_directory',
            category: 'Faculty',
            title: 'Complete Faculty Roster',
            keywords: ['faculty', 'faculties', 'who are faculty members', 'tell me about the faculty', 'who teaches', 'who works', 'professors', 'teachers', 'faculty list', 'teaching staff'],
            tokens: ['faculty', 'faculties', 'professors', 'teachers', 'staff', 'teaching staff', 'who teaches', 'who works', 'faculty list'],
            content: `Department Faculty Directory:
Our department has highly qualified faculty members across CSD and CSIT led by HODs Dr. M. Suresh Babu and Dr. NGK Murthy. Key faculty members include Trinadh Sir, Satyam Sir, Manoj Sir, Aswini Priyanka Madam, S. Mohan Krishna Sir, Dr. K. Srinivasa Rao Sir, N. Navya Madam, and Neti Praveen Sir.`,
            url: 'faculty.php',
            ctaText: 'View Complete Faculty Directory →'
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
     * 3. FACULTY EXTRACTION & EXACT MATCHING ENGINE (Requirements #1, #2, #3, #4, #5, #6, #7, #9)
     * =========================================================================
     */

    /**
     * Extract target faculty query name from raw input
     */
    function extractFacultyQueryName(rawQuery) {
        if (!rawQuery) return { isFacultyQuery: false, extractedName: '', rawQuery: '' };

        const lower = rawQuery.toLowerCase().trim();

        const facultyTriggers = [
            'who is', 'tell me about', 'about', 'faculty', 'professor', 'teacher',
            'sir', 'madam', 'ma\'am', 'mam', 'dr.', 'dr', 'prof.', 'prof',
            'profile of', 'details of', 'info on', 'information about', 'who teaches',
            'details about', 'qualification of', 'email of', 'contact of', 'who'
        ];

        let isFacultyQuery = facultyTriggers.some(t => lower.includes(t));

        // Also check if any known faculty alias is present in the query
        if (!isFacultyQuery) {
            for (const fac of MASTER_FACULTY_ROSTER) {
                for (const alias of fac.searchableNames) {
                    if (alias.length >= 3 && lower.includes(alias.toLowerCase())) {
                        isFacultyQuery = true;
                        break;
                    }
                }
                if (isFacultyQuery) break;
            }
        }

        if (!isFacultyQuery) {
            return { isFacultyQuery: false, extractedName: '', rawQuery: lower };
        }

        // Strip question phrases and honorifics
        let cleaned = lower;
        cleaned = cleaned.replace(/^(who is|tell me about|information about|info on|details of|details about|profile of|who|tell me|about|give details of|show profile of)/gi, '');
        cleaned = cleaned.replace(/\b(dr\.|dr|prof\.|prof|mr\.|mr|mrs\.|mrs|ms\.|ms|miss)\b/gi, '');
        cleaned = cleaned.replace(/\b(sir|madam|ma'am|mam|teacher|faculty|professor|profile|details|info)\b/gi, '');
        cleaned = cleaned.replace(/[^a-z0-9\s]/g, ' ').trim();

        return {
            isFacultyQuery: true,
            extractedName: cleaned,
            rawQuery: lower
        };
    }

    /**
     * Exact & Keyword-First Faculty Matcher
     */
    function matchFacultyFromRoster(extractedInfo) {
        if (!extractedInfo || !extractedInfo.isFacultyQuery) {
            return null;
        }

        const target = extractedInfo.extractedName;
        const rawQuery = extractedInfo.rawQuery;

        let bestMatch = null;
        let maxScore = 0;

        for (const fac of MASTER_FACULTY_ROSTER) {
            let score = 0;

            for (const alias of fac.searchableNames) {
                const aliasLower = alias.toLowerCase().trim();

                // 1. Exact match with extracted target name
                if (target && target === aliasLower) {
                    score = Math.max(score, 1000);
                }

                // 2. Exact match of alias in full raw query
                if (rawQuery.includes(aliasLower)) {
                    score = Math.max(score, 950);
                }

                // 3. Word boundary regex match
                if (aliasLower.length >= 3) {
                    const escaped = aliasLower.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(`\\b${escaped}\\b`, 'i');
                    if (target && regex.test(target)) {
                        score = Math.max(score, 900);
                    }
                    if (regex.test(rawQuery)) {
                        score = Math.max(score, 850);
                    }
                }

                // 4. Token substring match
                if (target.length >= 3 && aliasLower.length >= 3) {
                    if (target.includes(aliasLower) || aliasLower.includes(target)) {
                        score = Math.max(score, 600);
                    }
                }
            }

            // Check main faculty name parts (e.g. Satyam, Trinadh, Suresh, Manoj, Navya, etc.)
            const nameParts = fac.name.toLowerCase().split(/\s+/);
            for (const part of nameParts) {
                if (part.length >= 4) {
                    const escapedPart = part.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const partRegex = new RegExp(`\\b${escapedPart}\\b`, 'i');
                    if (target && partRegex.test(target)) {
                        score = Math.max(score, 880);
                    }
                    if (partRegex.test(rawQuery)) {
                        score = Math.max(score, 750);
                    }
                }
            }

            if (score > maxScore) {
                maxScore = score;
                bestMatch = fac;
            }
        }

        if (maxScore >= 500 && bestMatch) {
            return {
                found: true,
                faculty: bestMatch,
                chunk: {
                    id: bestMatch.id,
                    category: 'Faculty',
                    title: `${bestMatch.name} — Faculty Profile`,
                    content: bestMatch.content,
                    url: bestMatch.url,
                    ctaText: bestMatch.ctaText,
                    targetFacultyName: bestMatch.name
                }
            };
        }

        // Faculty member was NOT found in department records (Requirement #6)
        let displayRequestedName = rawQuery;
        displayRequestedName = displayRequestedName.replace(/^(who is|tell me about|information about|info on|details of|details about|profile of|who|about)/gi, '').trim();
        displayRequestedName = displayRequestedName.replace(/[\?\!\.\,]/g, '').trim();
        if (!displayRequestedName) displayRequestedName = target || 'the requested faculty member';

        displayRequestedName = displayRequestedName.split(' ')
            .map(w => w.charAt(0).toUpperCase() + w.slice(1))
            .join(' ');

        return {
            found: false,
            requestedName: displayRequestedName,
            notFoundMessage: `I couldn't find a faculty member named ${displayRequestedName} in the department faculty records.`,
            chunk: {
                id: 'faculty_not_found',
                category: 'Faculty',
                title: 'Faculty Member Not Found',
                isNotFound: true,
                requestedName: displayRequestedName,
                content: `I couldn't find a faculty member named ${displayRequestedName} in the department faculty records.`,
                url: 'faculty.php',
                ctaText: 'View Complete Faculty Directory →'
            }
        };
    }

    /**
     * =========================================================================
     * 4. RELEVANCE VECTOR RERANKING ENGINE & HYBRID SEARCH (Requirement #9)
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
            if (chunk.tokens && chunk.tokens.includes(token)) score += 30;
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
        // Step A: EXACT / KEYWORD FACULTY MATCHING FIRST (Requirement #9)
        const facultyExtraction = extractFacultyQueryName(rawQuery);
        if (facultyExtraction.isFacultyQuery) {
            const facultyMatch = matchFacultyFromRoster(facultyExtraction);
            if (facultyMatch) {
                if (facultyMatch.found) {
                    console.log('[CHATBOT RAG] Exact Faculty Match Found:', facultyMatch.faculty.name);
                    return facultyMatch.chunk;
                } else {
                    console.log('[CHATBOT RAG] Requested Faculty Not Found in Records:', facultyMatch.requestedName);
                    return facultyMatch.chunk;
                }
            }
        }

        // Step B: GENERAL KNOWLEDGE MATRIX RAG SEARCH SECOND
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
     * =========================================================================
     * 5. POST-RETRIEVAL VERIFICATION GUARD (Requirement #10)
     * Verifies that the returned profile matches the requested faculty name.
     * =========================================================================
     */
    function verifyAndEnforceFacultyResponse(userInput, matchedChunk, rawAnswer) {
        const facultyExtraction = extractFacultyQueryName(userInput);
        if (!facultyExtraction.isFacultyQuery) {
            return rawAnswer; // Non-faculty queries pass through unchanged
        }

        const facultyMatch = matchFacultyFromRoster(facultyExtraction);

        // Enforce exact requirement #6 for non-existent faculty
        if (facultyMatch && !facultyMatch.found) {
            return facultyMatch.notFoundMessage;
        }

        // Enforce exact requirement #10: verify returned profile matches requested faculty
        if (facultyMatch && facultyMatch.found) {
            const targetName = facultyMatch.faculty.name.toLowerCase();
            const targetTokens = targetName.split(/\s+/).filter(t => t.length >= 4);
            const answerLower = (rawAnswer || '').toLowerCase();

            const mentionsTarget = targetTokens.some(token => answerLower.includes(token)) ||
                facultyMatch.faculty.searchableNames.some(alias => alias.length >= 4 && answerLower.includes(alias.toLowerCase()));

            if (!mentionsTarget) {
                console.warn('[CHATBOT VERIFICATION] Mismatch detected! Overwriting API response with target profile:', facultyMatch.faculty.name);
                return `<strong>${facultyMatch.chunk.title}:</strong><br><br>${facultyMatch.chunk.content.replace(/\n/g, '<br>')}`;
            }
        }

        return rawAnswer;
    }

    /**
     * Client-side Gemini API Invocation
     */
    async function callGeminiDirect(userInput, matchedChunk, apiKey) {
        const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

        let systemInstruction = `You are the official AI Assistant for the Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram.\n\n`;
        if (matchedChunk) {
            if (matchedChunk.isNotFound) {
                systemInstruction += `NOTICE: The requested faculty member '${matchedChunk.requestedName}' is NOT in department records.\nAnswer ONLY: 'I couldn't find a faculty member named ${matchedChunk.requestedName} in the department faculty records.'\n\n`;
            } else {
                systemInstruction += `VERIFIED WEBSITE RAG CONTEXT:\nTitle: ${matchedChunk.title}\nContent: ${matchedChunk.content}\n\n`;
                systemInstruction += `Instructions: Answer using the verified website context above. Do not invent fake names or dates. Format output using clean HTML/Markdown.`;
            }
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

        if (matchedChunk.isNotFound) {
            return {
                answer: matchedChunk.content,
                ctaLinks: [{ text: matchedChunk.ctaText, url: matchedChunk.url }],
                suggestions: ['Who is Satyam Sir?', 'Who is Trinadh Sir?', 'Who is the HOD?']
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

            // Step 2: Perform Hybrid RAG Search
            let timeRetrievalStart = performance.now();
            let matchedChunk = searchKnowledgeVector(userInput);
            const timeRetrievalEnd = performance.now();
            console.log(`[CHATBOT] Retrieval: ${(timeRetrievalEnd - timeRetrievalStart).toFixed(2)} ms`);

            // Step 2.1: Non-existent faculty member immediate intercept (Requirement #6)
            if (matchedChunk && matchedChunk.isNotFound) {
                const notFoundRes = synthesizeLocalAnswer(matchedChunk, userInput);
                responseCache.set(normalizedQuery, notFoundRes);
                return notFoundRes;
            }

            let finalResponse = null;

            // Step 2.5: Smart Gemini Bypassing for Factual Queries
            if (matchedChunk) {
                const wordCount = normalizedQuery.split(/\s+/).length;
                const requiresReasoning = /\b(why|how|difference|compare|explain|give|example|what is|skills|need|help)\b/i.test(normalizedQuery);

                if (matchedChunk.category === 'Faculty' || (wordCount <= 6 && !requiresReasoning)) {
                    console.log('[CHATBOT] Smart Bypass: Factual/Faculty query. Skipping Gemini to save API quota.');
                    finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                    finalResponse.answer = verifyAndEnforceFacultyResponse(userInput, matchedChunk, finalResponse.answer);
                    responseCache.set(normalizedQuery, finalResponse);

                    const timeEnd = performance.now();
                    console.log(`[CHATBOT] Total: ${(timeEnd - timeStart).toFixed(2)} ms`);
                    return finalResponse;
                }
            }

            // Step 3: Try Backend PHP Proxy (api/gemini_chat.php reading .env)
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
                        const verifiedReply = verifyAndEnforceFacultyResponse(userInput, matchedChunk, proxyData.reply);

                        conversationContext.history.push({ role: 'user', text: userInput });
                        conversationContext.history.push({ role: 'model', text: verifiedReply });

                        finalResponse = {
                            answer: verifiedReply.replace(/\n/g, '<br>'),
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

            // Step 4: Try Direct Client API Key if set
            if (!finalResponse) {
                const clientKey = userApiKey || config.apiKey || (typeof window !== 'undefined' ? (window.GEMINI_API_KEY || (typeof localStorage !== 'undefined' ? localStorage.getItem('gemini_api_key') : null)) : null);
                if (clientKey) {
                    try {
                        const geminiText = await callGeminiDirect(userInput, matchedChunk, clientKey);
                        const verifiedText = verifyAndEnforceFacultyResponse(userInput, matchedChunk, geminiText);

                        conversationContext.history.push({ role: 'user', text: userInput });
                        conversationContext.history.push({ role: 'model', text: verifiedText });

                        finalResponse = {
                            answer: verifiedText.replace(/\n/g, '<br>'),
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

            // Step 5: Local RAG Fallback
            if (!finalResponse) {
                finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                finalResponse.answer = verifyAndEnforceFacultyResponse(userInput, matchedChunk, finalResponse.answer);

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
        getMasterFacultyRoster: function () { return MASTER_FACULTY_ROSTER; },
        getContextState: function () { return conversationContext; },
        resetContext: function () {
            conversationContext = { activeEntity: null, activeTopic: null, lastQuery: null, history: [] };
        }
    };
})();
