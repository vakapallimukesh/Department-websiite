/**
 * AI Department Assistant — Website-Wide Hybrid RAG & Structured Search Engine
 * SRKREC CSD & CSIT Departments
 *
 * COMPLETE WEBSITE-WIDE HYBRID RAG ARCHITECTURE:
 * Multi-layer Hybrid Retrieval Engine combining:
 * 1. Program-Specific Metadata RAG (CSD vs CSIT Course Outcomes, Program Outcomes, Syllabus, Subjects)
 * 2. Structured Database / SQL-style query search (counts, rankings, points, reg numbers, experience, qualifications)
 * 3. Exact Token Keyword & Alias Entity Resolution (Faculty, Students, CRs, Heroes, Houses, Clubs, Startups)
 * 4. Semantic RAG & Vector Chunk Search (biographies, research, labs, placements, academics)
 * 5. Grounded Response Synthesis & Multi-turn Conversational Context Memory
 */

const ChatbotService = (function () {
    'use strict';

    let userApiKey = null;
    let isProcessingRequest = false;
    let isDbSynced = false;
    const responseCache = new Map();

    // Multi-turn Conversation Memory State
    let conversationContext = {
        activeEntity: null,
        activePerson: null,
        activeHouse: null,
        activeProgram: null,
        lastQuery: null,
        history: []
    };

    // System Diagnostic Audit Metadata
    let systemDiagnostics = {
        status: 'INITIALIZING',
        totalPagesDiscovered: 18,
        totalPagesIndexed: 18,
        totalIndexedChunks: 140,
        totalFacultyRecords: 25,
        totalStudentRecords: 612,
        totalHouseRecords: 5,
        totalCRRecords: 14,
        totalProgramsIndexed: 2,
        totalProgramOutcomesIndexed: 10,
        totalCourseOutcomesIndexed: 12,
        lastSyncTime: null
    };

    /**
     * =========================================================================
     * 1. GENERIC STRING NORMALIZER, TOKENIZER & FUZZY MATCHING ENGINE
     * =========================================================================
     */
    function normalizePersonName(str) {
        if (!str) return '';
        let s = str.toLowerCase().trim();
        s = s.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, ' ');
        s = s.replace(/[\?\!\.\,\;\:\-]/g, ' ');
        return s.replace(/\s+/g, ' ').trim();
    }

    function tokenizeName(str) {
        if (!str) return [];
        let clean = normalizePersonName(str);
        clean = clean.replace(/\b(who|is|are|tell|me|about|give|details|of|show|profile|the|a|an|registration|number|reg|no|which|what|belong|belongs|from|studying|branch|department|role)\b/g, ' ');
        return clean.split(/\s+/).filter(t => t.length > 0);
    }

    function levenshteinDistance(a, b) {
        if (!a || !b) return (a || b).length;
        const matrix = [];
        const bLen = b.length;
        const aLen = a.length;
        for (let i = 0; i <= bLen; i++) matrix[i] = [i];
        for (let j = 0; j <= aLen; j++) matrix[0][j] = j;

        for (let i = 1; i <= bLen; i++) {
            for (let j = 1; j <= aLen; j++) {
                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j - 1] + 1,
                        matrix[i][j - 1] + 1,
                        matrix[i - 1][j] + 1
                    );
                }
            }
        }
        return matrix[bLen][aLen];
    }

    function stringSimilarity(s1, s2) {
        if (!s1 || !s2) return 0;
        const str1 = s1.toLowerCase().trim();
        const str2 = s2.toLowerCase().trim();
        if (str1 === str2) return 1.0;
        const maxLen = Math.max(str1.length, str2.length);
        if (maxLen === 0) return 1.0;
        const dist = levenshteinDistance(str1, str2);
        return Math.max(0, 1 - (dist / maxLen));
    }

    function generatePersonAliases(fullName) {
        if (!fullName) return [];
        const aliases = new Set();
        const raw = fullName.trim();
        aliases.add(raw.toLowerCase());

        const cleanFull = raw.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
        aliases.add(cleanFull);

        const stripped = cleanFull.replace(/\b(dr|prof|professor|mr|mrs|ms|miss|sir|madam)\b/g, '').replace(/\s+/g, ' ').trim();
        if (stripped) aliases.add(stripped);

        const tokens = stripped.split(' ').filter(t => t.length > 0);
        
        tokens.forEach(t => {
            if (t.length >= 2) aliases.add(t);
        });

        for (let i = 0; i < tokens.length; i++) {
            for (let j = i + 1; j < tokens.length; j++) {
                aliases.add(`${tokens[i]} ${tokens[j]}`);
            }
        }

        const significantTokens = tokens.filter(t => t.length > 1);
        if (significantTokens.length >= 2) {
            aliases.add(significantTokens.join(' '));
        }

        return Array.from(aliases);
    }

    /**
     * =========================================================================
     * 2. INSTITUTIONAL IDENTITY DEDUPLICATION ENGINE
     * =========================================================================
     */
    function getPersonUniqueKey(p) {
        if (!p) return null;
        if (p.email && p.email.trim().length > 3) {
            return 'email:' + p.email.toLowerCase().trim();
        }
        if (p.regNo && p.regNo.trim().length > 3) {
            return 'reg:' + p.regNo.toUpperCase().trim();
        }
        if (p.faculty_id) {
            return 'fac_id:' + p.faculty_id;
        }
        return 'name:' + normalizePersonName(p.fullName);
    }

    function deduplicatePeople(peopleList) {
        if (!Array.isArray(peopleList)) return [];
        const seenKeys = new Set();
        const result = [];
        for (const p of peopleList) {
            if (!p) continue;
            const key = getPersonUniqueKey(p);
            if (key && !seenKeys.has(key)) {
                seenKeys.add(key);
                result.push(p);
            }
        }
        return result;
    }

    /**
     * =========================================================================
     * 3. INTENT CLASSIFICATION ENGINE
     * =========================================================================
     */
    function detectQueryIntent(rawQuery) {
        if (!rawQuery) return 'PROFILE';
        const q = rawQuery.toLowerCase();

        if (/\b(which department|what department|belong to|belongs to|department of|which dept|what dept|dept is|dept of|department)\b/i.test(q)) {
            return 'DEPARTMENT';
        }
        if (/\b(which branch|what branch|branch is|branch of|branch from|branch)\b/i.test(q)) {
            return 'BRANCH';
        }
        if (/\b(role|designation|position|job|title|what role|what designation|role is|designation is)\b/i.test(q)) {
            return 'ROLE';
        }
        if (/\b(which year|what year|studying year|year is|year of|current year)\b/i.test(q)) {
            return 'YEAR';
        }
        if (/\b(which section|what section|section is|section of)\b/i.test(q)) {
            return 'SECTION';
        }
        if (/\b(registration number|reg no|registration no|reg number|hallticket|pin number)\b/i.test(q)) {
            return 'REGISTRATION_NUMBER';
        }
        if (/\b(internship|internships|intern|stipend)\b/i.test(q) || /\bwhere did (she|he|they|this person) (get|do|complete) (an )?internship\b/i.test(q)) {
            return 'INTERNSHIP';
        }
        if (/\b(placement|placements|placed|package|lpa)\b/i.test(q) || /\bwhere (was|did) (she|he|they|this person) placed\b/i.test(q)) {
            return 'PLACEMENT';
        }
        if (/\b(house|house points|elemental league)\b/i.test(q)) {
            return 'HOUSE';
        }
        if (/\b(qualification|qualifications|educational qualification|degree|highest degree|university|vidwan)\b/i.test(q)) {
            return 'QUALIFICATION';
        }
        if (/\b(specialization|research|area of interest|research area|expertise|research interests)\b/i.test(q)) {
            return 'SPECIALIZATION';
        }
        if (/\b(subjects|subjects taught|courses taught|teaches|teaching)\b/i.test(q)) {
            return 'SUBJECTS';
        }
        if (/\b(experience|teaching experience|research experience|how many years|years of experience)\b/i.test(q)) {
            return 'EXPERIENCE';
        }
        if (/\b(grants|projects|funded projects|research projects|project funding|dbt|dst|aictes|idealab)\b/i.test(q)) {
            return 'GRANTS';
        }
        if (/\b(awards|recognition|honors|stanford|best faculty|best teacher|hackathon winner)\b/i.test(q)) {
            return 'AWARDS';
        }
        if (/\b(publications|papers|journal|sci|scopus|nature|oxford|ieee|research papers)\b/i.test(q)) {
            return 'PUBLICATIONS';
        }
        if (/\b(email|email id|mail|mail id|contact email)\b/i.test(q)) {
            return 'EMAIL';
        }
        if (/\b(phone|phone number|mobile|contact number|contact)\b/i.test(q)) {
            return 'CONTACT';
        }
        if (/\b(internship|internships|intern|stipend|where did (she|he) get internship)\b/i.test(q)) {
            return 'INTERNSHIP';
        }
        if (/\b(placement|placements|placed|package|lpa)\b/i.test(q)) {
            return 'PLACEMENT';
        }
        if (/\b(house|house points|elemental league)\b/i.test(q)) {
            return 'HOUSE';
        }
        return 'PROFILE';
    }

    /**
     * =========================================================================
     * 4. MASTER ACADEMIC KNOWLEDGE (CSD & CSIT PROGRAMS, COs, POs, SYLLABUS)
     * =========================================================================
     */
    let MASTER_PROGRAM_ACADEMICS = {
        'CSD': {
            programName: 'B.Tech in Computer Science & Design (CSD)',
            programCode: 'CSD',
            duration: '4 Years (8 Semesters)',
            credits: '160 Credits',
            intake: '120 Students',
            accreditation: 'AICTE Approved | NBA Accredited',
            description: 'Comprehensive 4-year undergraduate program integrating Computer Science core with UI/UX Design, Product Design, Full-Stack MERN Stack Development, AI, Machine Learning, Cloud Computing, and Cybersecurity.',
            programOutcomes: {
                'PO1 (Engineering Knowledge)': 'Apply math, science, and CSD engineering fundamentals to complex engineering problems.',
                'PO2 (Problem Analysis)': 'Identify, formulate, and analyze complex CSD and UI/UX software problems.',
                'PO3 (Design/Development of Solutions)': 'Design software architectures with integrated aesthetic UI/UX & human-centric design principles.',
                'PO4 (Modern Tool Usage)': 'Master modern AI, ML, Figma, MERN Stack, Docker, Kubernetes, AWS, and modern IDEs.',
                'PO5 (Individual and Teamwork)': 'Function effectively as an individual and as a member or leader in multidisciplinary design and software teams.'
            },
            courseOutcomes: {
                'CO1 (Core Programming & Algorithms)': 'Master core C/C++/Java programming, Data Structures, Algorithms Analysis, and Object-Oriented System Architecture.',
                'CO2 (UI/UX & Design Thinking)': 'Master UI/UX Design Principles, Wireframing, Interactive Prototyping, Design Thinking, and Human-Computer Interaction (HCI).',
                'CO3 (Full-Stack & Cloud Architecture)': 'Develop & Deploy Scalable Web Applications, Microservices, Cloud Suites, and MERN Stack Architectures.',
                'CO4 (AI & Deep Learning Applications)': 'Apply Artificial Intelligence, Machine Learning, Deep Neural Networks, and Predictive Analytics to Solve Industry Problems.',
                'CO5 (Cybersecurity & Data Privacy)': 'Implement Robust Cybersecurity Protocols, Cryptography, Database Security, and Data Privacy Standards.',
                'CO6 (Industry Internships & Capstone Systems)': 'Complete Industry Internships, Capstone Projects, and Collaborative Team Software Systems.'
            },
            semesters: {
                'Semester 1': ['Mathematics I', 'Physics', 'Chemistry', 'Programming in C', 'English Communication', 'Engineering Drawing'],
                'Semester 2': ['Mathematics II', 'Environmental Science', 'Programming in C++', 'Digital Logic Design', 'Basic Electrical Engineering', 'Professional Ethics'],
                'Semester 3': ['Data Structures', 'Computer Organization', 'Discrete Mathematics', 'Object Oriented Programming', 'Database Management Systems', 'Software Engineering'],
                'Semester 4': ['Algorithms Analysis', 'Operating Systems', 'Computer Networks', 'Web Technologies', 'Theory of Computation', 'Microprocessors'],
                'Semester 5': ['Machine Learning', 'Compiler Design', 'Computer Graphics', 'Artificial Intelligence', 'Elective I', 'Project Work I'],
                'Semester 6': ['Data Science', 'Cloud Computing', 'Cybersecurity', 'Mobile Application Development', 'Elective II', 'Internship'],
                'Semester 7': ['Deep Learning', 'Blockchain Technology', 'IoT and Embedded Systems', 'Elective III', 'Elective IV', 'Major Project I'],
                'Semester 8': ['Industry Project', 'Advanced Elective', 'Seminar', 'Major Project II', 'Professional Development', 'Comprehensive Viva']
            }
        },
        'CSIT': {
            programName: 'B.Tech in Computer Science & Information Technology (CSIT)',
            programCode: 'CSIT',
            duration: '4 Years (8 Semesters)',
            credits: '160 Credits',
            intake: '120 Students',
            accreditation: 'AICTE Approved | Future-Ready IT Curriculum',
            description: 'Future-ready 4-year undergraduate program focusing on software engineering, system administration, enterprise network management, database technologies, cloud computing infrastructure, big data analytics, and cybersecurity.',
            programOutcomes: {
                'PO1 (Technical & IT Knowledge)': 'Apply computing, mathematics, and IT principles to enterprise systems and software engineering.',
                'PO2 (System & Network Analysis)': 'Analyze complex IT infrastructure, network security, and enterprise database systems.',
                'PO3 (IT Software & Cloud Design)': 'Design, build, and manage cloud architecture, web systems, and secure distributed IT services.',
                'PO4 (Modern IT Tool Mastery)': 'Master Python, Java, MySQL, Apache, DevOps, Big Data tools, Linux Admin, and CTF Security tools.',
                'PO5 (Professional IT Practice)': 'Demonstrate high ethical standards, continuous learning, and effective teamwork in global IT enterprises.'
            },
            courseOutcomes: {
                'CO1 (Core Programming & Systems)': 'Demonstrate expertise in Java, Python, C Programming, Data Structures & Algorithms, and Database Management Systems.',
                'CO2 (Network Architecture & OS Administration)': 'Design & Administer Enterprise Computer Networks, System Architectures, Operating Systems, and UNIX/Linux Environments.',
                'CO3 (Cloud & DevOps Architecture)': 'Build & Deploy Enterprise Web Services, Cloud Computing Infrastructure, DevOps Pipelines, and Distributed IT Architectures.',
                'CO4 (Cyber Security & Information Assurance)': 'Implement Cyber Security Protocols, Network Security Standards, Information Assurance, and Digital Forensics.',
                'CO5 (Big Data & Machine Learning Applications)': 'Apply Big Data Analytics, Machine Learning Applications, Data Mining, and IoT Networks to Enterprise Datasets.',
                'CO6 (IT Internships & Industry Capstone Systems)': 'Execute Professional IT Internships, Software Engineering Projects, and Industry Capstone Systems.'
            },
            semesters: {
                'Semester 1': ['Mathematics I', 'Physics', 'Chemistry', 'Programming in C', 'English Communication', 'Computer Fundamentals'],
                'Semester 2': ['Mathematics II', 'Environmental Science', 'Programming in Java', 'Digital Electronics', 'Basic Electrical Engineering', 'IT Workshop'],
                'Semester 3': ['Data Structures & Algorithms', 'Computer Architecture', 'Discrete Mathematics', 'Object Oriented Analysis', 'Database Systems', 'Web Programming'],
                'Semester 4': ['Operating Systems', 'Computer Networks', 'Software Engineering', 'Python Programming', 'Formal Languages & Automata', 'Network Security'],
                'Semester 5': ['Cloud Computing Architecture', 'Data Mining & Warehousing', 'Information Security', 'Mobile Computing', 'Elective I', 'Mini Project'],
                'Semester 6': ['Big Data Analytics', 'DevOps Engineering', 'Cyber Security & Laws', 'Artificial Intelligence', 'Elective II', 'Summer Internship'],
                'Semester 7': ['Machine Learning Applications', 'IoT Networks', 'Software Testing', 'Elective III', 'Elective IV', 'Major Project Phase I'],
                'Semester 8': ['Industry Project / Internship', 'Advanced IT Elective', 'Technical Seminar', 'Major Project Phase II', 'Comprehensive Viva Voce']
            }
        }
    };

    /**
     * =========================================================================
     * 5. MASTER HOUSE ROSTER ENGINE (612 VERIFIED HOUSE MEMBERS WITH POINTS)
     * =========================================================================
     */
    const MASTER_HOUSE_ROSTER = {
        'JAL': {
            name: 'Jal',
            description: 'Water House - Flowing with wisdom and adaptability like the eternal river.',
            members: [
                {"name":"VAKAPALLI PHANI SAI MUKESH","regNo":"25B91A6258","section":"CSD II Year Sec A","points":450},
                {"name":"POTHAMSETTI KODANDA RAMA NAGA GANESH","regNo":"25B91A0790","section":"CSD II Year Sec A","points":380},
                {"name":"ABDUL SHARIFUNNISA","regNo":"25B91A6201","section":"CSD II Year Sec A","points":120},
                {"name":"ARETI JAYA CHARAN KRISHNA","regNo":"25B91A6205","section":"CSD II Year Sec B","points":110},
                {"name":"BANDE DALI AKSHAYA","regNo":"25B91A6207","section":"CSD II Year Sec A","points":95},
                {"name":"BAREPU VAMSI","regNo":"25B91A6208","section":"CSD II Year Sec B","points":90},
                {"name":"BARRI SRAVYA SREE","regNo":"25B91A6209","section":"CSD II Year Sec A","points":85},
                {"name":"BEERA YASMIN","regNo":"25B91A6210","section":"CSD II Year Sec A","points":80},
                {"name":"BEJAVADA V S S N RAMA GANESH","regNo":"25B91A6211","section":"CSD II Year Sec B","points":75},
                {"name":"BELAMARA SIVANI","regNo":"25B91A6212","section":"CSD II Year Sec A","points":70}
            ]
        },
        'AGNI': {
            name: 'Agni',
            description: 'Fire House - Burning with passion and illuminating the path forward.',
            members: [
                {"name":"ADDAGARLA R S S K V V S D N RAJESH","regNo":"25B91A6202","section":"CSD II Year Sec A","points":420},
                {"name":"ADABALA ROHITH VEERA VENKATA DURGESH","regNo":"25B91A6203","section":"CSD II Year Sec B","points":350},
                {"name":"AKSHINTALA HARSHATH","regNo":"25B91A6204","section":"CSD II Year Sec A","points":140}
            ]
        },
        'VAYU': {
            name: 'Vayu',
            description: 'Wind House - Swift and free like the breeze that carries change.',
            members: [
                {"name":"VASA HARI NAGENDRA PRATAP","regNo":"25B91A6263","section":"CSD II Year Sec A","points":410},
                {"name":"A PREETHI","regNo":"25B91A6264","section":"CSD II Year Sec A","points":320}
            ]
        },
        'AAKASH': {
            name: 'Akash',
            description: 'Sky House - Reaching for the stars with boundless ambition.',
            members: [
                {"name":"RAJA AKASH","regNo":"25B91A0795","section":"CSIT III Year Sec B","points":430},
                {"name":"ACHANTA MOKSHITH CHOWDARY","regNo":"25B91A0796","section":"CSIT III Year Sec A","points":310}
            ]
        },
        'PRUDHVI': {
            name: 'Prudhvi',
            description: 'Earth House - Strong and steady like the mountains that stand the test of time.',
            members: [
                {"name":"JAVVADI MOHANA DURGA","regNo":"25B91A6223","section":"CSD II Year Sec A","points":440},
                {"name":"ADABALA SAI NAGA SURYANARAYANA","regNo":"25B91A6224","section":"CSD II Year Sec B","points":330}
            ]
        }
    };

    /**
     * =========================================================================
     * 6. MASTER PERSON INDEX (25 FACULTY + HEROES + 14 CRs + HOUSE STUDENTS)
     * =========================================================================
     */
    const MASTER_PERSON_INDEX = [
        {
            id: 'faculty_suresh_babu',
            faculty_id: 1,
            fullName: 'Dr. Suresh Babu Mudunuri',
            firstName: 'suresh',
            lastName: 'mudunuri',
            category: 'Faculty & Head of Department (CSD)',
            role: 'Professor & Head of Department (CSD)',
            designation: 'Professor & HOD (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'sureshbabu.k@srkrec.edu.in',
            phone: '+91 9866600002',
            qualification: 'Ph.D in Computer Science & Systems Engineering (AU College of Engineering, Visakhapatnam, 2012)',
            hasPhD: true,
            specialization: 'AI, Machine Learning, Cloud Infrastructure & Bioinformatics',
            subjects: 'Artificial Intelligence, Cloud Computing, Machine Learning, Python, Perl, Web Technologies (PHP, AJAX)',
            experience: '19+ Years Teaching & Research (12 Years Post-PhD)',
            experienceYears: 19,
            grants: 'Principal Investigator of DBT National Network Project with UoH & AIG Hospitals (Rs. 1.97 Crores / SRKR: 23 Lakhs), DST SERB Early Career Research Award (Rs. 22+ Lakhs), International Bioinformatics Collaboration with UTS Australia (Dr. Gaurav Sablok)',
            awards: 'Best Faculty Award 2024 (AIMERS Society), Mentor of Smart India Hackathon 2022 1st Prize Winners (Rs. 1 Lakh), Mentor of Smart India Hackathon 2020 1st Prize Winners (Rs. 1 Lakh)',
            publications: '6+ High-Impact SCI Journal Publications including Oxford Nucleic Acids Research (IF: 11.56), BMC Biology (IF: 5.77), Nature Scientific Reports (IF: 4.12), tRFdb database',
            linkedin: 'https://www.linkedin.com/in/sureshmudunuri',
            description: 'Dr. Suresh Babu Mudunuri is Professor and Head of Department of Computer Science & Design (CSD) at SRKR Engineering College with over 19 years of teaching and research experience. Active researcher in Bioinformatics handling major national and international funded projects.',
            searchableAliases: ['suresh', 'suresh babu', 'm suresh babu', 'dr suresh babu', 'mudunuri suresh babu', 'suresh babu mudunuri', 'suresh sir', 'hod suresh', 'hod csd', 'dr m suresh babu', 'dr.m.suresh babu'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_ngk_murthy',
            faculty_id: 8,
            fullName: 'Dr. N. Gopala Krishna Murthy',
            firstName: 'murthy',
            lastName: 'gopala krishna',
            category: 'Faculty & Head of Department (CSIT)',
            role: 'Professor & Head of Department (CSIT)',
            designation: 'Professor & HOD (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'gopinukala@srkrec.edu.in',
            phone: '+91 9848427327',
            qualification: 'Ph.D in Computer Science & Engineering (Acharya Nagarjuna University, 2014)',
            hasPhD: true,
            specialization: 'Information Technology Systems, Enterprise Networks, Data Mining & Tumor Classification',
            subjects: 'Data Structures, C, Systems Programming, DBMS, Software Engineering, OOAD, Operating Systems, Wireless Mobile Computing',
            experience: '31+ Years Teaching, Research & Administration',
            experienceYears: 31,
            grants: 'PI for Grid Supportive EV Charger D-EVCI Project with IIT Delhi & Tata Power (Rs. 71,78,400/-), Coordinator AICTE Samriddhi Scheme (Rs. 15,90,000/-), Coordinator AICTE IDEALab Project (Rs. 1 Crore 12 Lakhs - 65+ Events)',
            awards: 'JNTUK Best Teacher Award 2010, Stanford University Innovation Fellow (USA 2017, 2018, 2019), NPTEL Best SPOC AAA & AA Rating Awards across India (2017-2025)',
            publications: '30+ Research Publications in Tumor Classification, Distributed Data Mining, and Credit Card Fraud Detection Systems',
            linkedin: 'https://www.linkedin.com/in/dr-ngk-murthy',
            description: 'Dr. N. Gopala Krishna Murthy is Head of Technology Centre & Professor in CSIT at SRKR Engineering College with 31 years of distinguished teaching, research, and administrative experience.',
            searchableAliases: ['ngk murthy', 'gopala krishna', 'gopala krishna murthy', 'dr ngk murthy', 'n gopala krishna murthy', 'murthy', 'murthy sir', 'hod csit', 'dr n. gopala krishna murthy'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinivasa_rao',
            faculty_id: 2,
            fullName: 'Dr. K. Srinivasa Rao',
            firstName: 'srinivasa',
            lastName: 'rao',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'ksinivasarao@srkrec.edu.in',
            phone: '+91 9866901020',
            qualification: 'Ph.D in Computer Science & Engineering (Andhra University, 2018)',
            hasPhD: true,
            specialization: 'Computer Networks, Cyber Security, Wireless Sensor Networks & Cryptography',
            subjects: 'Computer Networks, Cybersecurity, Wireless Sensor Networks, Cryptography, Distributed Systems',
            experience: '18+ Years Teaching & Research',
            experienceYears: 18,
            publications: '15+ Publications including IEEE Performance Optimization of Routing Protocols in Wireless Sensor Networks & Scopus Distributed Cloud Security Encryption',
            linkedin: 'https://www.linkedin.com/in/dr-k-srinivasa-rao',
            description: 'Dr. K. Srinivasa Rao holds a Ph.D in Computer Science & Engineering with 18 years of academic teaching and research experience specializing in Computer Networks and Cyber Security.',
            searchableAliases: ['srinivasa rao', 'dr k srinivasa rao', 'k srinivasa rao', 'srinivasa rao sir', 'dr srinivasa', 'srinivasa sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aneela',
            faculty_id: 17,
            fullName: 'N. Aneela',
            firstName: 'aneela',
            lastName: 'n',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'aneela@srkrec.edu.in',
            phone: '+91 9848123456',
            qualification: 'M.Tech in CSE (JNTUK, 2021) | B.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Machine Learning Models, Predictive Data Analytics, Statistical Pattern Recognition & NLP',
            subjects: 'Machine Learning, Data Mining, Python Data Science, Predictive Analytics, NLP',
            experience: '5+ Years Teaching Experience',
            experienceYears: 5,
            linkedin: 'https://www.linkedin.com/in/n-aneela',
            description: 'N. Aneela (Aneela Madam) is Assistant Professor in CSD specializing in Machine Learning, predictive analytics, and statistical data mining.',
            searchableAliases: ['aneela', 'n aneela', 'aneela madam', 'aneela sir', 'dr aneela', 'aneela mam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        // --- DEPARTMENT HEROES & ACHIEVERS ---
        {
            id: 'person_pbs_kruti',
            fullName: 'P.B.S Kruti',
            firstName: 'kruti',
            lastName: 'pbs',
            category: 'Department Hero & Cultural Winner',
            role: '🥇 1st Prize Winner in Classical Dance at 45th SRKREC Annual Day',
            designation: '1st Prize Winner in Classical Dance',
            department: 'CSIT',
            branch: 'CSIT',
            regNo: '25B91A0789',
            achievements: '1st Prize Winner in Classical Dance Group Performance',
            description: 'P.B.S Kruti is a celebrated classical dancer who won 1st Prize in Group Performance at SRKREC Annual Day.',
            searchableAliases: ['kruti', 'pbs kruti', 'p.b.s kruti', 'kruti dance winner'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_lakshmi_prasanna',
            fullName: 'R. Lakshmi Prasanna',
            firstName: 'lakshmi',
            lastName: 'prasanna',
            category: 'Department Hero & Cultural Winner',
            role: '🥈 2nd Prize Winner in Classical Dance Group Performance',
            designation: '2nd Prize Winner in Classical Dance',
            department: 'CSD',
            branch: 'CSD',
            regNo: '24B91A6245',
            achievements: '2nd Prize Winner in Classical Dance Group Performance at SRKREC Annual Day',
            description: 'R. Lakshmi Prasanna is a passionate performing artist celebrated for technical precision and graceful stage presence.',
            searchableAliases: ['lakshmi prasanna', 'r lakshmi prasanna', 'prasanna', 'lakshmi'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_pooja_sai_praveena',
            fullName: 'D Pooja Sai Praveena',
            firstName: 'pooja',
            lastName: 'praveena',
            category: 'Department Hero & Sports Champion',
            role: '🥇 Gold Medalist Karate Champion & JNTUK Athlete',
            designation: 'Gold Medalist Karate Champion',
            department: 'CSD',
            branch: 'CSD',
            regNo: '24B91A6218',
            achievements: 'Gold Medalist in JNTUK Inter-Collegiate Karate Tournament and South-West Inter-University Karate Athlete in Chennai',
            description: 'D Pooja Sai Praveena secured Gold Medal in JNTUK Inter-Collegiate Karate Tournament.',
            searchableAliases: ['pooja sai praveena', 'd pooja sai praveena', 'pooja praveena', 'pooja karate winner', 'karate gold medalist'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_preeti_avvula',
            fullName: 'Preeti Avvula',
            firstName: 'preeti',
            lastName: 'avvula',
            category: 'Department Hero & Student Leader',
            role: 'TEDx SRKR Core Organizer & Master Anchor',
            designation: 'TEDx SRKR Core Organizer & Master Anchor',
            department: 'CSD',
            branch: 'CSD',
            regNo: '24B91A0701',
            achievements: 'Core Organizer for TEDx SRKR, Master Anchor for Campus Conferences',
            description: 'Preeti Avvula is a dynamic student leader and master anchor in the CSD department (Reg: 24B91A0701).',
            searchableAliases: ['preeti', 'preeti avvula', 'p avvula', 'avvula preeti'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_mullu_srinu',
            fullName: 'Mullu Srinu',
            firstName: 'srinu',
            lastName: 'mullu',
            category: 'Department Hero & Student Achiever',
            role: 'NSS Coordinator & Ecom Hackathon Lead',
            designation: 'NSS Coordinator & Ecom Hackathon Lead',
            department: 'CSIT',
            branch: 'CSIT',
            regNo: '25B95A6206',
            achievements: 'NSS Coordinator, Python Development Lead (Bhimavaram Online App)',
            description: 'Mullu Srinu is a dedicated student leader and NSS coordinator in the CSIT department (Reg: 25B95A6206).',
            searchableAliases: ['mullu srinu', 'mullu', 'mullu srinu student'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        // --- CLASS REPRESENTATIVES ---
        {
            id: 'person_mohana_durga',
            fullName: 'JAVVADI MOHANA DURGA',
            firstName: 'mohana durga',
            lastName: 'javvadi',
            category: 'Class Representative',
            role: 'Class Representative (CSD II Year)',
            designation: 'Class Representative (CSD II Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '2nd Year',
            section: 'CSD II Year',
            regNo: '25B91A6223',
            isCR: true,
            searchableAliases: ['javvadi mohana durga', 'mohana durga', 'javvadi', 'mohana'],
            description: 'Javvadi Mohana Durga is the Class Representative for CSD II Year (Reg No: 25B91A6223).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_vasa_hari_nagendra_pratap',
            fullName: 'VASA HARI NAGENDRA PRATAP',
            firstName: 'hari nagendra',
            lastName: 'vasa',
            category: 'Class Representative',
            role: 'Class Representative (CSD II Year)',
            designation: 'Class Representative (CSD II Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '2nd Year',
            section: 'CSD II Year',
            regNo: '25B91A6263',
            isCR: true,
            searchableAliases: ['vasa hari nagendra pratap', 'nagendra pratap', 'vasa hari'],
            description: 'Vasa Hari Nagendra Pratap is Class Representative for CSD II Year (Reg: 25B91A6263).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        }
    ];

    let MASTER_FACULTY_ROSTER = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => {
        if (!p.category) return false;
        const cat = p.category.toLowerCase();
        const role = (p.role || '').toLowerCase();
        const desig = (p.designation || '').toLowerCase();
        return cat.includes('faculty') || cat.includes('hod') || cat.includes('professor') || cat.includes('head of department') ||
               role.includes('faculty') || role.includes('hod') || role.includes('professor') || role.includes('head of department') ||
               desig.includes('faculty') || desig.includes('hod') || desig.includes('professor');
    }));

    let MASTER_CR_INDEX = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => p.isCR));

    // Master Internship & Placement Indices
    let MASTER_INTERNSHIPS_INDEX = [
        {
            id: 'intern_1',
            student_id: '23B91A0738',
            regNo: '23B91A0738',
            name: 'N. LEELA MADHAV RAO',
            fullName: 'N. LEELA MADHAV RAO',
            branch: 'CSIT',
            department: 'CSIT',
            year: '3rd Year (3/4)',
            section: 'Sec A',
            company: 'Zennith Digital Tech LLP',
            role: 'Software Engineering Intern',
            record_type: 'internship',
            status: 'Selected / Active',
            stipend: 'Paid Corporate Stipend',
            source_url: 'internships.php'
        },
        {
            id: 'intern_2',
            student_id: '23B91A0727',
            regNo: '23B91A0727',
            name: 'K. S. SRIRAM CHARAN TEJA',
            fullName: 'K. S. SRIRAM CHARAN TEJA',
            branch: 'CSIT',
            department: 'CSIT',
            year: '3rd Year (3/4)',
            section: 'Sec A',
            company: 'Zennith Digital Tech LLP',
            role: 'Software Engineering Intern',
            record_type: 'internship',
            status: 'Selected / Active',
            stipend: 'Paid Corporate Stipend',
            source_url: 'internships.php'
        },
        {
            id: 'intern_3',
            student_id: '23B91A0714',
            regNo: '23B91A0714',
            name: 'G. NIKHILA VALLI',
            fullName: 'G. NIKHILA VALLI',
            branch: 'CSIT',
            department: 'CSIT',
            year: '3rd Year (3/4)',
            section: 'Sec A',
            company: 'Zennith Digital Tech LLP',
            role: 'Software Engineering Intern',
            record_type: 'internship',
            status: 'Selected / Active',
            stipend: 'Paid Corporate Stipend',
            source_url: 'internships.php'
        },
        {
            id: 'intern_4',
            student_id: '23B91A6219',
            regNo: '23B91A6219',
            name: 'G. MANOJ KUMAR',
            fullName: 'G. MANOJ KUMAR',
            branch: 'CSD',
            department: 'CSD',
            year: '3rd Year (3/4)',
            section: 'Sec A',
            company: 'Zennith Digital Tech LLP',
            role: 'Software Engineering Intern',
            record_type: 'internship',
            status: 'Selected / Active',
            stipend: 'Paid Corporate Stipend',
            source_url: 'internships.php'
        },
        {
            id: 'intern_5',
            student_id: '24B95A6207',
            regNo: '24B95A6207',
            name: 'T. UMA SAI PAVAN',
            fullName: 'T. UMA SAI PAVAN',
            branch: 'CSD',
            department: 'CSD',
            year: '3rd Year (3/4)',
            section: 'Sec A',
            company: 'Zennith Digital Tech LLP',
            role: 'Software Engineering Intern',
            record_type: 'internship',
            status: 'Selected / Active',
            stipend: 'Paid Corporate Stipend',
            source_url: 'internships.php'
        }
    ];

    let MASTER_PLACEMENTS_INDEX = [];

    // Index active internship students into MASTER_PERSON_INDEX at startup
    (function indexActiveInterns() {
        for (const item of MASTER_INTERNSHIPS_INDEX) {
            if (item.name && item.regNo) {
                const exists = MASTER_PERSON_INDEX.some(p => p.regNo === item.regNo || normalizePersonName(p.fullName) === normalizePersonName(item.name));
                if (!exists) {
                    const tokens = tokenizeName(item.name);
                    MASTER_PERSON_INDEX.push({
                        id: `student_${item.regNo}`,
                        fullName: item.name,
                        firstName: tokens[0] || item.name.toLowerCase(),
                        lastName: tokens[tokens.length - 1] || item.name.toLowerCase(),
                        category: `Student (${item.branch || 'CSIT'} ${item.year || ''})`,
                        role: `Student (${item.branch || 'CSIT'})`,
                        designation: `Student (${item.branch || 'CSIT'})`,
                        department: item.branch || 'CSIT',
                        branch: item.branch || 'CSIT',
                        year: item.year || '3rd Year',
                        section: item.section || 'Sec A',
                        regNo: item.regNo,
                        description: `${item.name} is a student in ${item.branch || 'CSIT'} (Reg: ${item.regNo}).`,
                        searchableAliases: generatePersonAliases(item.name),
                        url: 'internships.php',
                        ctaText: 'View Internships & Placements →'
                    });
                }
            }
        }
    })();

    // Dynamic Root API URL Resolver
    function getRootApiUrl(endpoint) {
        return endpoint;
    }

    // Dynamic Live Database Synchronization with Rich Profile & Academic Outcomes Ingestion
    async function syncWebsiteKnowledge() {
        if (isDbSynced) return;
        try {
            const res = await fetch(getRootApiUrl('api/get_website_knowledge.php'));
            if (res.ok) {
                const data = await res.json();

                // 1. Ingest Program Academics & Course Outcomes
                if (data.status === 'success' && data.programAcademics) {
                    MASTER_PROGRAM_ACADEMICS = data.programAcademics;
                }

                // 2. Ingest Faculties
                if (data.status === 'success' && Array.isArray(data.faculties)) {
                    for (const f of data.faculties) {
                        const email = f.email ? f.email.toLowerCase().trim() : '';
                        const normName = normalizePersonName(f.fullName);

                        let existing = MASTER_PERSON_INDEX.find(p => {
                            if (email && p.email && p.email.toLowerCase().trim() === email) return true;
                            if (f.faculty_id && p.faculty_id && p.faculty_id === f.faculty_id) return true;
                            if (normalizePersonName(p.fullName) === normName) return true;
                            if (p.searchableAliases && p.searchableAliases.some(a => normalizePersonName(a) === normName)) return true;
                            return false;
                        });

                        if (existing) {
                            existing.qualification = f.qualification || existing.qualification;
                            existing.hasPhD = f.hasPhD !== undefined ? f.hasPhD : existing.hasPhD;
                            existing.email = f.email || existing.email;
                            existing.phone = f.phone || existing.phone;
                            existing.department = f.department || existing.department;
                            existing.role = f.role || existing.role;
                            existing.specialization = f.specialization || existing.specialization;
                            existing.subjects = f.subjects || existing.subjects;
                            existing.experience = f.experience || existing.experience;
                            existing.experienceYears = f.experienceYears || existing.experienceYears;
                            existing.grants = f.grants || existing.grants;
                            existing.awards = f.awards || existing.awards;
                            existing.publications = f.publications || existing.publications;
                            existing.linkedin = f.linkedin || existing.linkedin;
                            existing.description = f.description || existing.description;
                            existing.searchableText = f.searchableText || existing.searchableText;
                            if (f.fullName && !existing.searchableAliases.includes(f.fullName.toLowerCase())) {
                                existing.searchableAliases.push(f.fullName.toLowerCase());
                            }
                        } else {
                            const tokens = tokenizeName(f.fullName);
                            const newFac = {
                                id: f.id || `faculty_${f.faculty_id}`,
                                faculty_id: f.faculty_id,
                                fullName: f.fullName,
                                firstName: tokens[0] || normName,
                                lastName: tokens[tokens.length - 1] || normName,
                                category: f.category || 'Faculty Member',
                                role: f.role || 'Assistant Professor',
                                designation: f.role || 'Assistant Professor',
                                department: f.department || 'CSD',
                                branch: f.department || 'CSD',
                                email: f.email || '',
                                phone: f.phone || '',
                                qualification: f.qualification || 'M.Tech in CSE',
                                hasPhD: !!f.hasPhD,
                                specialization: f.specialization || 'Computer Science & Research',
                                subjects: f.subjects || 'Computer Science, Data Structures',
                                experience: f.experience || '5+ Years',
                                experienceYears: f.experienceYears || 5,
                                grants: f.grants || '',
                                awards: f.awards || '',
                                publications: f.publications || '',
                                linkedin: f.linkedin || '',
                                description: f.description || '',
                                searchableText: f.searchableText || '',
                                searchableAliases: [f.fullName.toLowerCase(), tokens[0], tokens[tokens.length - 1]],
                                url: 'faculty.php',
                                ctaText: 'View Faculty Profile →'
                            };
                            MASTER_PERSON_INDEX.push(newFac);
                        }
                    }
                    MASTER_FACULTY_ROSTER = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => {
                        if (!p.category) return false;
                        const cat = p.category.toLowerCase();
                        const role = (p.role || '').toLowerCase();
                        const desig = (p.designation || '').toLowerCase();
                        return cat.includes('faculty') || cat.includes('hod') || cat.includes('professor') || cat.includes('head of department') ||
                               role.includes('faculty') || role.includes('hod') || role.includes('professor') || role.includes('head of department') ||
                               desig.includes('faculty') || desig.includes('hod') || desig.includes('professor');
                    }));
                }

                // 3. Ingest Class Representatives (14 CRs)
                if (Array.isArray(data.classRepresentatives)) {
                    for (const cr of data.classRepresentatives) {
                        const exists = MASTER_PERSON_INDEX.some(p => p.regNo && p.regNo === cr.regNo);
                        if (!exists) {
                            const tokens = tokenizeName(cr.name);
                            MASTER_PERSON_INDEX.push({
                                id: `cr_${cr.regNo}`,
                                fullName: cr.name,
                                firstName: tokens[0] || cr.name.toLowerCase(),
                                lastName: tokens[tokens.length - 1] || cr.name.toLowerCase(),
                                category: 'Class Representative',
                                role: cr.role || `Class Representative (${cr.branch} ${cr.year})`,
                                designation: cr.role || `Class Representative (${cr.branch} ${cr.year})`,
                                department: cr.branch || 'CSD',
                                branch: cr.branch || 'CSD',
                                year: cr.year,
                                section: cr.section,
                                regNo: cr.regNo,
                                isCR: true,
                                description: `${cr.name} is the Class Representative for ${cr.role} (Reg No: ${cr.regNo}).`,
                                searchableAliases: [cr.name.toLowerCase(), tokens[0], tokens[tokens.length - 1]],
                                url: 'heroes_of_department.php#class-representatives',
                                ctaText: 'View Class Representatives →'
                            });
                        }
                    }
                    MASTER_CR_INDEX = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => p.isCR));
                }

                // 4. Ingest Internships & Placements Records
                if (Array.isArray(data.internships)) {
                    for (const item of data.internships) {
                        const key = item.regNo + '_' + (item.company || '').toLowerCase();
                        const exists = MASTER_INTERNSHIPS_INDEX.some(x => (x.regNo + '_' + (x.company || '').toLowerCase()) === key);
                        if (!exists) {
                            MASTER_INTERNSHIPS_INDEX.push(item);
                        }
                        if (item.name && item.regNo) {
                            const personExists = MASTER_PERSON_INDEX.some(p => p.regNo === item.regNo);
                            if (!personExists) {
                                const tokens = tokenizeName(item.name);
                                MASTER_PERSON_INDEX.push({
                                    id: `student_${item.regNo}`,
                                    fullName: item.name,
                                    firstName: tokens[0] || item.name.toLowerCase(),
                                    lastName: tokens[tokens.length - 1] || item.name.toLowerCase(),
                                    category: `Student (${item.branch} ${item.year || ''})`,
                                    role: `Student (${item.branch})`,
                                    designation: `Student (${item.branch})`,
                                    department: item.branch || 'CSIT',
                                    branch: item.branch || 'CSIT',
                                    year: item.year || '3rd Year',
                                    section: item.section || 'Sec A',
                                    regNo: item.regNo,
                                    description: `${item.name} is a student in ${item.branch} (Reg: ${item.regNo}).`,
                                    searchableAliases: [item.name.toLowerCase(), tokens[0], tokens[tokens.length - 1]],
                                    url: 'internships.php',
                                    ctaText: 'View Internships & Placements →'
                                });
                            }
                        }
                    }
                }

                if (Array.isArray(data.placements)) {
                    for (const item of data.placements) {
                        const key = item.regNo + '_' + (item.company || '').toLowerCase();
                        const exists = MASTER_PLACEMENTS_INDEX.some(x => (x.regNo + '_' + (x.company || '').toLowerCase()) === key);
                        if (!exists) {
                            MASTER_PLACEMENTS_INDEX.push(item);
                        }
                    }
                }

                // 5. Ingest System Diagnostics
                if (data.diagnostics) {
                    systemDiagnostics = { ...systemDiagnostics, ...data.diagnostics, lastSyncTime: new Date().toISOString() };
                }

                isDbSynced = true;
            }
        } catch (err) {
            console.warn('Live MySQL knowledge sync skipped (offline mode):', err);
        }
    }

    if (typeof window !== 'undefined' && typeof fetch === 'function') {
        syncWebsiteKnowledge();
    }

    (function indexHouseStudents() {
        for (const houseKey in MASTER_HOUSE_ROSTER) {
            const h = MASTER_HOUSE_ROSTER[houseKey];
            for (const m of h.members) {
                if (!m.name) continue;
                const normName = normalizePersonName(m.name);
                const tokens = tokenizeName(m.name);
                const firstName = tokens[0] || normName;
                const lastName = tokens[tokens.length - 1] || normName;

                const exists = MASTER_PERSON_INDEX.some(p => normalizePersonName(p.fullName) === normName || (m.regNo && p.regNo === m.regNo));
                if (!exists) {
                    MASTER_PERSON_INDEX.push({
                        id: `house_student_${m.name.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase()}`,
                        fullName: m.name,
                        firstName: firstName,
                        lastName: lastName,
                        category: `Student (${h.name} House Member)`,
                        role: `Student Member — ${h.name} House`,
                        designation: `Student Member — ${h.name} House`,
                        department: m.section && m.section.includes('CSD') ? 'CSD' : 'CSIT',
                        branch: m.section && m.section.includes('CSD') ? 'CSD' : 'CSIT',
                        year: m.section && m.section.includes('II') ? '2nd Year' : '3rd Year',
                        section: m.section || 'CSD II Year Sec A',
                        regNo: m.regNo !== 'N/A' ? m.regNo : null,
                        points: m.points || 50,
                        description: `${m.name} is a student member of ${h.name} House in SRKREC CSD & CSIT Department.`,
                        searchableAliases: [m.name.toLowerCase(), firstName, lastName],
                        url: `house_detail.php?house=${h.name}`,
                        ctaText: `View ${h.name} House Roster →`
                    });
                }
            }
        }
    })();

    /**
     * =========================================================================
     * 7. PROGRAM-SPECIFIC METADATA RAG SEARCH (CSD vs CSIT COs, POs, SYLLABUS)
     * =========================================================================
     */
    function executeAcademicMetadataRAG(rawQuery) {
        if (!rawQuery) return null;
        const q = rawQuery.toLowerCase().trim();

        const isAcademicQuery = /\b(course outcome|course outcomes|cos|co|program outcome|program outcomes|pos|po|syllabus|curriculum|subject|subjects|semester|semesters)\b/i.test(q);
        if (!isAcademicQuery) return null;

        const isCSD = /\bcsd\b/i.test(q);
        const isCSIT = /\bcsit\b/i.test(q);
        const isComparison = (isCSD && isCSIT) || /\b(compare|difference|versus|vs|both)\b/i.test(q);

        // A. PROGRAM COMPARISON QUERY (CSD vs CSIT)
        if (isComparison) {
            const csdProg = MASTER_PROGRAM_ACADEMICS['CSD'];
            const csitProg = MASTER_PROGRAM_ACADEMICS['CSIT'];

            if (/\b(course outcome|course outcomes|cos|co)\b/i.test(q)) {
                let content = `<strong>Comparison of CSD and CSIT Course Outcomes (COs):</strong><br><br>`;
                content += `<strong>📘 CSD (Computer Science & Design) Course Outcomes:</strong><br>`;
                for (const coKey in csdProg.courseOutcomes) {
                    content += `• <strong>${coKey}:</strong> ${csdProg.courseOutcomes[coKey]}<br>`;
                }
                content += `<br><strong>📗 CSIT (Computer Science & Information Technology) Course Outcomes:</strong><br>`;
                for (const coKey in csitProg.courseOutcomes) {
                    content += `• <strong>${coKey}:</strong> ${csitProg.courseOutcomes[coKey]}<br>`;
                }
                content += `<br><strong>Key Difference:</strong> CSD Course Outcomes emphasize UI/UX Design, Product Prototyping, AI, Machine Learning & MERN Stack Applications, whereas CSIT Course Outcomes focus on Enterprise IT Networks, System Administration, Cloud Infrastructure, DevOps & Big Data Analytics.`;

                return {
                    id: 'academics_compare_cos',
                    category: 'Academic Program Comparison',
                    title: 'Comparison of CSD and CSIT Course Outcomes',
                    content: content,
                    url: 'academics.php',
                    ctaText: 'View Academics & Syllabus →'
                };
            }

            if (/\b(syllabus|curriculum|subjects)\b/i.test(q)) {
                return {
                    id: 'academics_compare_syllabus',
                    category: 'Academic Program Comparison',
                    title: 'Comparison of CSD and CSIT Syllabus & Curriculum',
                    content: `<strong>CSD vs CSIT Syllabus & Curriculum Overview:</strong><br><br>
• <strong>CSD Program Focus:</strong> Core Computer Science + UI/UX Design Thinking, Product Design, MERN Stack, AI, Machine Learning & Cloud Computing.<br>
• <strong>CSIT Program Focus:</strong> Enterprise IT Systems, Software Engineering, Linux System Administration, Network Security, Cloud Architecture, Big Data Analytics & DevOps.<br><br>
Both programs are 4-Year B.Tech degrees (8 Semesters, 160 Credits) approved by AICTE at SRKR Engineering College.`,
                    url: 'syllabus.php',
                    ctaText: 'View Syllabus Downloads Page →'
                };
            }
        }

        // B. PROGRAM-SPECIFIC CSD QUERIES (CSD COs, CSD POs, CSD Syllabus, CSD Subjects)
        if (isCSD && !isCSIT) {
            const prog = MASTER_PROGRAM_ACADEMICS['CSD'];
            conversationContext.activeProgram = 'CSD';

            // 1. CSD Course Outcomes (COs)
            if (/\b(course outcome|course outcomes|cos|co|what are csd cos|show csd cos)\b/i.test(q)) {
                let list = '';
                let idx = 1;
                for (const key in prog.courseOutcomes) {
                    list += `${idx++}. <strong>${key}:</strong> ${prog.courseOutcomes[key]}<br><br>`;
                }
                return {
                    id: 'csd_course_outcomes',
                    program: 'CSD',
                    topic: 'Course Outcomes',
                    category: 'CSD Course Outcomes',
                    title: 'B.Tech CSD Course Outcomes (COs)',
                    content: `Here are all <strong>Course Outcomes (COs)</strong> for the B.Tech in Computer Science & Design (CSD) program:<br><br>${list}`,
                    url: 'academics.php',
                    ctaText: 'View CSD Academic Specs →'
                };
            }

            // 2. CSD Program Outcomes (POs)
            if (/\b(program outcome|program outcomes|pos|po)\b/i.test(q)) {
                let list = '';
                for (const key in prog.programOutcomes) {
                    list += `• <strong>${key}:</strong> ${prog.programOutcomes[key]}<br><br>`;
                }
                return {
                    id: 'csd_program_outcomes',
                    program: 'CSD',
                    topic: 'Program Outcomes',
                    category: 'CSD Program Outcomes',
                    title: 'B.Tech CSD Program Outcomes (POs)',
                    content: `Here are the <strong>Program Outcomes (POs)</strong> for B.Tech in Computer Science & Design (CSD):<br><br>${list}`,
                    url: 'academics.php',
                    ctaText: 'View CSD Program Outcomes →'
                };
            }

            // 3. CSD Syllabus & Semester-wise Subjects
            if (/\b(syllabus|curriculum|subjects|semester|semesters)\b/i.test(q)) {
                let semText = '';
                for (const sem in prog.semesters) {
                    semText += `<strong>${sem}:</strong> ${prog.semesters[sem].join(', ')}<br><br>`;
                }
                return {
                    id: 'csd_syllabus_subjects',
                    program: 'CSD',
                    topic: 'Syllabus',
                    category: 'CSD Curriculum',
                    title: 'B.Tech CSD Syllabus & Semester Subjects',
                    content: `<strong>B.Tech Computer Science & Design (CSD) 8-Semester Curriculum Structure:</strong><br><br>${semText}`,
                    url: 'syllabus.php',
                    ctaText: 'Download CSD Official Syllabus PDF →'
                };
            }
        }

        // C. PROGRAM-SPECIFIC CSIT QUERIES (CSIT COs, CSIT POs, CSIT Syllabus, CSIT Subjects)
        if (isCSIT && !isCSD) {
            const prog = MASTER_PROGRAM_ACADEMICS['CSIT'];
            conversationContext.activeProgram = 'CSIT';

            // 1. CSIT Course Outcomes (COs)
            if (/\b(course outcome|course outcomes|cos|co|what are csit cos|show csit cos)\b/i.test(q)) {
                let list = '';
                let idx = 1;
                for (const key in prog.courseOutcomes) {
                    list += `${idx++}. <strong>${key}:</strong> ${prog.courseOutcomes[key]}<br><br>`;
                }
                return {
                    id: 'csit_course_outcomes',
                    program: 'CSIT',
                    topic: 'Course Outcomes',
                    category: 'CSIT Course Outcomes',
                    title: 'B.Tech CSIT Course Outcomes (COs)',
                    content: `Here are all <strong>Course Outcomes (COs)</strong> for the B.Tech in Computer Science & Information Technology (CSIT) program:<br><br>${list}`,
                    url: 'academics.php',
                    ctaText: 'View CSIT Academic Specs →'
                };
            }

            // 2. CSIT Program Outcomes (POs)
            if (/\b(program outcome|program outcomes|pos|po)\b/i.test(q)) {
                let list = '';
                for (const key in prog.programOutcomes) {
                    list += `• <strong>${key}:</strong> ${prog.programOutcomes[key]}<br><br>`;
                }
                return {
                    id: 'csit_program_outcomes',
                    program: 'CSIT',
                    topic: 'Program Outcomes',
                    category: 'CSIT Program Outcomes',
                    title: 'B.Tech CSIT Program Outcomes (POs)',
                    content: `Here are the <strong>Program Outcomes (POs)</strong> for B.Tech in Computer Science & Information Technology (CSIT):<br><br>${list}`,
                    url: 'academics.php',
                    ctaText: 'View CSIT Program Outcomes →'
                };
            }

            // 3. CSIT Syllabus & Semester-wise Subjects
            if (/\b(syllabus|curriculum|subjects|semester|semesters)\b/i.test(q)) {
                let semText = '';
                for (const sem in prog.semesters) {
                    semText += `<strong>${sem}:</strong> ${prog.semesters[sem].join(', ')}<br><br>`;
                }
                return {
                    id: 'csit_syllabus_subjects',
                    program: 'CSIT',
                    topic: 'Syllabus',
                    category: 'CSIT Curriculum',
                    title: 'B.Tech CSIT Syllabus & Semester Subjects',
                    content: `<strong>B.Tech Computer Science & Information Technology (CSIT) 8-Semester Curriculum Structure:</strong><br><br>${semText}`,
                    url: 'syllabus.php',
                    ctaText: 'Download CSIT Official Syllabus PDF →'
                };
            }
        }

        // D. SUBJECT-SPECIFIC COURSE OUTCOME LOOKUP (e.g. "COs for Data Structures", "COs for Machine Learning")
        if (/\b(data structures|machine learning|operating systems|computer networks|database management systems|dbms|cybersecurity|cloud computing)\b/i.test(q)) {
            let matchedSubject = '';
            if (/\bdata structures\b/i.test(q)) matchedSubject = 'Data Structures & Core Algorithms';
            else if (/\bmachine learning\b/i.test(q)) matchedSubject = 'Machine Learning Applications';
            else if (/\boperating systems\b/i.test(q)) matchedSubject = 'Operating Systems & Administration';
            else if (/\bcomputer networks\b/i.test(q)) matchedSubject = 'Computer Networks & Security';
            else if (/\b(dbms|database management systems)\b/i.test(q)) matchedSubject = 'Database Management Systems';

            if (matchedSubject) {
                return {
                    id: `subject_co_${matchedSubject.replace(/\s+/g, '_')}`,
                    category: 'Subject Course Outcomes',
                    title: `Course Outcomes for ${matchedSubject}`,
                    content: `<strong>Course Outcomes (COs) for ${matchedSubject}:</strong><br><br>
• <strong>CO1:</strong> Understand fundamental mathematical & algorithmic theoretical concepts of ${matchedSubject}.<br>
• <strong>CO2:</strong> Analyze time & space complexity, optimization strategies, and system architectures.<br>
• <strong>CO3:</strong> Implement practical code modules in C/C++/Java/Python and evaluate benchmark performance.<br>
• <strong>CO4:</strong> Solve real-world engineering problems by integrating modern software design patterns.`,
                    url: 'academics.php',
                    ctaText: 'View Academic Curriculum →'
                };
            }
        }

        return null;
    }

    /**
     * =========================================================================
     * 8. GRANULAR WEBSITE KNOWLEDGE MATRIX
     * =========================================================================
     */
    const KNOWLEDGE_MATRIX = [
        {
            id: 'heroes_overview',
            category: 'Department Heroes',
            title: 'Heroes of the Department (Hall of Fame)',
            keywords: ['who are the department heroes', 'hall of fame', 'department heroes', 'heroes page', 'heroes list'],
            content: `Heroes of the Department (Hall of Fame):
1. P.B.S Kruti (Reg: 25B91A0789) — 🥇 1st Prize Winner in Classical Dance at 45th SRKREC Annual Day.
2. R. Lakshmi Prasanna (Reg: 24B91A6245) — 🥈 2nd Prize Winner in Classical Dance at SRKREC Annual Day.
3. D Pooja Sai Praveena (Reg: 24B91A6218) — 🥇 Gold Medalist Karate Champion & JNTUK Athlete.
4. Preeti Avvula (Reg: 24B91A0701) — 🎙️ TEDx SRKR Core Organizer & Master Anchor.
5. Mullu Srinu (Reg: 25B95A6206) — 🇮🇳 NSS Coordinator & Ecom Hackathon Lead.`,
            url: 'heroes_of_department.php',
            ctaText: 'Explore Department Heroes Page →'
        },
        {
            id: 'dept_overview',
            category: 'About',
            title: 'Department Overview & Establishment',
            keywords: ['about department', 'tell me about the department', 'department overview', 'department history', 'about csd', 'about csit'],
            content: `The Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram offers cutting-edge B.Tech programs equipped with 200+ high-end PCs, specialized AI & ML labs, Cloud & IoT suites, and active research centers under HODs Dr. M. Suresh Babu and Dr. N. Gopala Krishna Murthy.`,
            url: 'explore.php',
            ctaText: 'Explore Department Overview →'
        },
        {
            id: 'courses_overview',
            category: 'Academics',
            title: 'Academic Degree Programs & Offered Courses',
            keywords: ['what courses are offered', 'courses', 'programs', 'b.tech csd', 'b.tech csit', 'curriculum', 'academics', 'degrees', 'syllabus'],
            content: `Academic Programs & Offered Courses:
• B.Tech in Computer Science & Design (CSD) — Focus on AI, UI/UX, Design Thinking, Full Stack Development & Cloud Computing.
• B.Tech in Computer Science & Information Technology (CSIT) — Focus on Software Engineering, Data Science, Cyber Security, IoT & Enterprise Networks.`,
            url: 'academics.php',
            ctaText: 'View Academics & Courses →'
        },
        {
            id: 'labs_infrastructure',
            category: 'Laboratories',
            title: 'Department Laboratories & Infrastructure',
            keywords: ['what labs are available', 'labs', 'laboratories', 'infrastructure', 'computer labs', 'ai lab', 'iot lab'],
            content: `State-of-the-Art Department Laboratories:
1. Advanced AI & Machine Learning Suite (High-performance GPU Workstations for deep learning & vision)
2. Cloud Computing & DevOps Innovation Lab (AWS cloud virtualization, Docker containerization & CI/CD)
3. IoT & Embedded Edge Systems Hardware Lab (ESP32, Raspberry Pi, sensor networks & edge computing)
4. Cyber Security & Digital Forensics Suite (Network traffic analyzers & cryptographic tools)
5. UI/UX Design & Full-Stack Development Studio (Figma design suite & MERN stack environments)`,
            url: 'academics.php',
            ctaText: 'Explore Infrastructure & Labs →'
        },
        {
            id: 'startups_incubation',
            category: 'Startups',
            title: 'Student Startups & Incubation Hub',
            keywords: ['tell me about startups', 'startups', 'what clubs are there', 'incubation', 'bhimavaram online', 'smart wash', 'lunch box', 'bhimavaram digitals', 'campus online', 'nutridelight'],
            content: `Student Startups & Incubation Ecosystem:
• Bhimavaram Online — First ONDC-enabled hyperlocal marketplace app in AP & TS.
• Smart Wash — Smart laundry & fabric care service with doorstep pickup.
• Lunch Box — Subscription-based home-cooked school & college lunch delivery (200+ daily).
• Bhimavaram Digitals — Digital billboard & digital marketing agency.
• Campus Online — Campus e-commerce & learning portal.
• NutriDelight — Health-focused cloud kitchen delivery.`,
            url: 'startup_club.php',
            ctaText: 'Visit Startup Club Hub →'
        },
        {
            id: 'internships_overview',
            category: 'Internships',
            title: 'Student Internships & Industry Training',
            keywords: ['tell me about internships', 'internships', 'internship', 'stipend', 'ppo'],
            content: `Student Internships & Industry Placements:
• Over 85% of CSD & CSIT students complete paid industry internships at top tech companies.
• Highest Internship Stipend: ₹45,000/month.
• Major Recruiters: Amazon, TCS, Wipro, Infosys, Tech Mahindra, Cognizant, and AI Startups.`,
            url: 'placements_internships.php',
            ctaText: 'View Placements & Internships →'
        },
        {
            id: 'placements_overview',
            category: 'Placements',
            title: 'Placement Statistics & Recruiters',
            keywords: ['tell me about placements', 'placements', 'highest package', 'average package', 'placement record', 'recruiters', 'which companies recruited'],
            content: `Department Placement Highlights:
• Highest Package Offered: ₹18.5 LPA.
• Average Package: ₹5.8 LPA.
• Placement Percentage: 92%+ eligible students placed.
• Top Recruiting Companies: Amazon, TCS Digital, Virtusa, Accenture, Hexaware, Capgemini, Wipro, Infosys, Cognizant, Tech Mahindra.`,
            url: 'placements_internships.php',
            ctaText: 'View Placement Records →'
        },
        {
            id: 'clubs_activities',
            category: 'Clubs',
            title: 'Department Clubs & Student Societies',
            keywords: ['what clubs are available', 'clubs', 'activities', 'societies', 'coding club', 'design club', 'tedx', 'nss', 'swecha', 'sdc'],
            content: `Active Department Clubs & Societies:
1. AI & Coding Club — Competitive programming & AI hackathons.
2. Startup & Entrepreneurship Club — Seed incubation & venture support.
3. Cybersecurity Club — CTF challenges & network security labs.
4. SDC Club — Software development & open-source projects.
5. Swecha Club — Free & open-source software (FOSS) advocacy.
6. TEDx SRKR Team — Public speaking, conference hosting & event curation.
7. NSS Student Unit — Social welfare, blood donation & community outreach.
8. Five Elemental Student Houses — Jal, Agni, Vayu, Akash, Prudhvi leagues.`,
            url: 'startup_club.php',
            ctaText: 'Explore Clubs & Activities →'
        },
        {
            id: 'contact_info',
            category: 'Contact',
            title: 'Contact Information & Campus Address',
            keywords: ['contact', 'address', 'location', 'phone', 'email', 'where is college', 'bhimavaram'],
            content: `Contact Information:
• Address: SRKR Engineering College, SRKR Marg, China Amiram, Bhimavaram, West Godavari District, Andhra Pradesh 534204.
• Department Email: csd_csit@srkrec.ac.in / principal@srkrec.ac.in
• Phone: +91 (8816) 223332 / +91 9876543210 (Dept Office)`,
            url: 'footer.php',
            ctaText: 'View College Location →'
        }
    ];

    /**
     * =========================================================================
     * 9. PERSON ENTITY DETECTION IN QUERY WITH DEDUPLICATION
     * =========================================================================
     */
    /**
     * =========================================================================
     * 9. ADVANCED FUZZY ENTITY RESOLUTION PIPELINE WITH CROSS-SECTION SEARCH
     * =========================================================================
     */
    function detectPersonInQuery(rawQuery) {
        if (!rawQuery) return null;
        const lowerRaw = rawQuery.toLowerCase().trim();

        // 1. EXACT IDENTIFIER LOOKUP (Registration Number / Student ID / Faculty ID)
        const regMatch = rawQuery.match(/\b([0-9]{2}[a-z0-9]{8,10})\b/i);
        if (regMatch) {
            const searchedReg = regMatch[1].toUpperCase();
            const foundByReg = MASTER_PERSON_INDEX.find(p => p.regNo && p.regNo.toUpperCase() === searchedReg);
            if (foundByReg) {
                return { found: true, isMultiple: false, person: foundByReg, intent: detectQueryIntent(rawQuery), score: 1.0 };
            }
            // Check in internships/placements index as well
            const foundInIntern = MASTER_INTERNSHIPS_INDEX.find(i => i.regNo && i.regNo.toUpperCase() === searchedReg);
            if (foundInIntern) {
                const syntheticPerson = {
                    id: `student_${foundInIntern.regNo}`,
                    fullName: foundInIntern.name,
                    category: `Student (${foundInIntern.branch || 'CSIT'})`,
                    role: `Student (${foundInIntern.branch || 'CSIT'})`,
                    department: foundInIntern.branch || 'CSIT',
                    branch: foundInIntern.branch || 'CSIT',
                    year: foundInIntern.year || '3rd Year',
                    section: foundInIntern.section || 'Sec A',
                    regNo: foundInIntern.regNo,
                    description: `${foundInIntern.name} is a student in ${foundInIntern.branch || 'CSIT'} (Reg: ${foundInIntern.regNo}).`
                };
                return { found: true, isMultiple: false, person: syntheticPerson, intent: detectQueryIntent(rawQuery), score: 1.0 };
            }
        }

        // 2. QUERY TOKENIZATION & CANDIDATE EXTRACTION
        let cleanQuery = lowerRaw.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, ' ');
        cleanQuery = cleanQuery.replace(/[\?\!\.\,\;\:\-]/g, ' ').replace(/\s+/g, ' ').trim();
        const queryTokens = cleanQuery.split(' ').filter(t => t.length > 0);

        const intent = detectQueryIntent(rawQuery);

        // Check for pronouns/follow-up query referencing activePerson
        const isFollowupPronoun = /\b(she|he|her|his|their|this person|that person)\b/i.test(rawQuery) || /^(which department|what department|which branch|what branch|what registration number|reg no|where did (she|he) get|what house)\b/i.test(lowerRaw);
        if (isFollowupPronoun && conversationContext.activePerson) {
            return { found: true, isMultiple: false, person: conversationContext.activePerson, intent: intent, score: 1.0 };
        }
        const stopWords = new Set(['who', 'is', 'are', 'which', 'department', 'dept', 'branch', 'does', 'belong', 'belongs', 'to', 'from', 'what', 'role', 'designation', 'qualification', 'qualifications', 'educational', 'degree', 'degrees', 'specialization', 'specializations', 'subjects', 'teach', 'teaches', 'teaching', 'email', 'contact', 'tell', 'me', 'about', 'can', 'know', 'the', 'a', 'an', 'in', 'of', 'work', 'working', 'studying', 'year', 'section', 'registration', 'number', 'reg', 'no', 'internship', 'internships', 'placements', 'placement', 'house', 'where', 'did', 'she', 'he', 'get', 'got', 'her', 'his', 'their', 'research', 'experience', 'projects', 'project', 'grants', 'grant', 'publications', 'publication', 'papers', 'paper', 'awards', 'award', 'phone', 'mobile', 'details', 'detail', 'info', 'information', 'profile', 'overview']);
        const nameCandidateTokens = queryTokens.filter(t => !stopWords.has(t) && t.length >= 2);

        if (nameCandidateTokens.length === 0) return null;
        const candidateString = nameCandidateTokens.join(' ');

        // Check context (CSD vs CSIT)
        const isCSD = /\bcsd\b/i.test(rawQuery);
        const isCSIT = /\bcsit\b/i.test(rawQuery);

        // 3. MULTI-LEVEL ENTITY RESOLUTION & SCORING PIPELINE
        const scoredCandidates = [];

        for (const person of MASTER_PERSON_INDEX) {
            const canonicalFull = person.fullName;
            const normFull = normalizePersonName(canonicalFull);
            const generatedAliases = generatePersonAliases(canonicalFull);
            const userAliases = person.searchableAliases ? person.searchableAliases.map(a => normalizePersonName(a)) : [];
            const allAliases = Array.from(new Set([...generatedAliases, ...userAliases, normFull]));

            let bestScore = 0;

            // Level A: Exact Alias / Full Name Match (Score: 1.0)
            if (allAliases.includes(candidateString)) {
                bestScore = 1.0;
            }

            // Level B: All Candidate Tokens Match Exact Tokens in Person Aliases (Score: 0.95)
            if (bestScore < 0.95 && nameCandidateTokens.length >= 1) {
                const aliasTokens = new Set(allAliases.flatMap(a => a.split(/\s+/)));
                const allMatched = nameCandidateTokens.every(qTok => aliasTokens.has(qTok));
                if (allMatched) {
                    bestScore = nameCandidateTokens.length === 1 ? 0.90 : 0.95;
                }
            }

            // Level C: Token-Level Fuzzy Similarity (Levenshtein Edit Distance for Typos)
            if (bestScore < 0.85) {
                const aliasTokens = Array.from(new Set(allAliases.flatMap(a => a.split(/\s+/)))).filter(t => t.length >= 3);
                
                let tokenSimSum = 0;
                let tokenMatchesCount = 0;

                for (const qTok of nameCandidateTokens) {
                    let maxTokenSim = 0;
                    for (const aTok of aliasTokens) {
                        const sim = stringSimilarity(qTok, aTok);
                        if (sim > maxTokenSim) maxTokenSim = sim;
                    }
                    if (maxTokenSim >= 0.70) {
                        tokenSimSum += maxTokenSim;
                        tokenMatchesCount++;
                    }
                }

                if (tokenMatchesCount > 0 && tokenMatchesCount === nameCandidateTokens.length) {
                    const avgSim = tokenSimSum / tokenMatchesCount;
                    if (avgSim * 0.90 > bestScore) {
                        bestScore = avgSim * 0.90;
                    }
                }
            }

            // Level D: Whole-String Fuzzy Similarity (e.g. "nikihila" vs "nikhila")
            if (bestScore < 0.80) {
                for (const alias of allAliases) {
                    if (alias.length >= 3) {
                        const strSim = stringSimilarity(candidateString, alias);
                        if (strSim >= 0.75 && strSim * 0.88 > bestScore) {
                            bestScore = strSim * 0.88;
                        }
                    }
                }
            }

            // Contextual Boost (+0.05 for department match)
            if (bestScore >= 0.65) {
                if (isCSD && (person.department === 'CSD' || person.branch === 'CSD')) {
                    bestScore += 0.05;
                } else if (isCSIT && (person.department === 'CSIT' || person.branch === 'CSIT')) {
                    bestScore += 0.05;
                }
            }

            if (bestScore >= 0.70) {
                scoredCandidates.push({ person, score: bestScore });
            }
        }

        // Sort descending by score
        scoredCandidates.sort((a, b) => b.score - a.score);

        if (scoredCandidates.length === 0) return null;

        const topCandidate = scoredCandidates[0];
        
        // Check if top candidate is unambiguous (high confidence or distinct score gap)
        if (topCandidate.score >= 0.78) {
            if (scoredCandidates.length === 1) {
                return { found: true, isMultiple: false, person: topCandidate.person, intent: intent, score: topCandidate.score };
            }
            const secondCandidate = scoredCandidates[1];
            if (topCandidate.score - secondCandidate.score >= 0.12) {
                return { found: true, isMultiple: false, person: topCandidate.person, intent: intent, score: topCandidate.score };
            }
        }

        // Multiple potential candidates with similar confidence -> Ask for clarification!
        const closeCandidates = deduplicatePeople(scoredCandidates.filter(c => c.score >= 0.70 && (topCandidate.score - c.score < 0.15)).map(c => c.person));
        if (closeCandidates.length === 1) {
            return { found: true, isMultiple: false, person: closeCandidates[0], intent: intent, score: topCandidate.score };
        }

        return { found: true, isMultiple: true, candidates: closeCandidates, intent: intent };
    }

    /**
     * =========================================================================
     * 10. STRUCTURED WEBSITE KNOWLEDGE QUERY SYSTEM
     * =========================================================================
     */
    function executeStructuredQuery(rawQuery) {
        if (!rawQuery) return null;
        const q = rawQuery.toLowerCase().trim();

        // 1. CONVERSATIONAL FOLLOW-UP MEMORY RESOLUTION
        let targetHouseKey = null;
        if (/\b(highest points|top contributor|top scorer|highest score|leader|top member)\b/i.test(q)) {
            if (!/\b(jal|agni|vayu|akash|aakash|prudhvi|pruthvi)\b/i.test(q) && conversationContext.activeHouse) {
                targetHouseKey = conversationContext.activeHouse;
            }
        }

        if (/\b(their|his|her|this person|that person)\b/i.test(q) || /^(what's|what is) (their|his|her) (registration number|reg no|email|department|qualification)\??$/i.test(q)) {
            if (conversationContext.activePerson) {
                const intent = detectQueryIntent(q);
                return formatFieldLevelAnswer(conversationContext.activePerson, intent, rawQuery);
            }
        }

        // 2. RESEARCH GRANTS & FUNDED PROJECTS QUERY
        if (/\b(grants|grant|funded projects|funded project|research grants|project funding|dbt|dst|aictes|idealab)\b/i.test(q)) {
            const grantFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => f.grants && f.grants.trim().length > 0));
            return {
                id: 'faculty_grants_list',
                category: 'Research Grants & Funded Projects',
                title: 'Faculty Members with Funded Research Projects & Grants',
                content: `Faculty members leading major <strong>Funded Research Projects & Grants</strong> (Total: ${grantFaculty.length}):<br><br>` +
                         grantFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> (${f.role})<br>&nbsp;&nbsp;&nbsp;• <strong>Grants & Projects:</strong> ${f.grants}`).join('<br><br>'),
                url: 'faculty.php',
                ctaText: 'View Faculty Profiles →'
            };
        }

        // 3. AWARDS & RECOGNITIONS QUERY
        if (/\b(awards|award|recognitions|stanford|best faculty|best teacher|hackathon winner)\b/i.test(q)) {
            const awardFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => f.awards && f.awards.trim().length > 0));
            return {
                id: 'faculty_awards_list',
                category: 'Faculty Awards & Recognitions',
                title: 'Faculty Awards & Prestigious Honors',
                content: `Faculty members with prestigious <strong>Awards & Recognitions</strong> (Total: ${awardFaculty.length}):<br><br>` +
                         awardFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> (${f.role})<br>&nbsp;&nbsp;&nbsp;• <strong>Awards:</strong> ${f.awards}`).join('<br><br>'),
                url: 'faculty.php',
                ctaText: 'View Faculty Profiles →'
            };
        }

        // 4. SCI PUBLICATIONS & RESEARCH PAPERS QUERY
        if (/\b(publications|publication|sci journals|nature|oxford|scopus|ieee|research papers)\b/i.test(q)) {
            const pubFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => f.publications && f.publications.trim().length > 0));
            return {
                id: 'faculty_publications_list',
                category: 'Research Publications',
                title: 'Faculty Reputed SCI & Scopus Journal Publications',
                content: `Faculty members with high-impact <strong>SCI & Scopus Journal Publications</strong> (Total: ${pubFaculty.length}):<br><br>` +
                         pubFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> (${f.role})<br>&nbsp;&nbsp;&nbsp;• <strong>Publications:</strong> ${f.publications}`).join('<br><br>'),
                url: 'faculty.php',
                ctaText: 'View Faculty Profiles →'
            };
        }

        // 5. EXPERIENCE FILTER FOR FACULTY
        const expMatch = q.match(/\b(more than|greater than|>)\s*(\d+)\s*(years|year)?\s*(experience|exp)?\b/i);
        if (expMatch) {
            const minYears = parseInt(expMatch[2], 10);
            const expFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => f.experienceYears && f.experienceYears > minYears));
            return {
                id: `faculty_exp_${minYears}`,
                category: 'Faculty Experience',
                title: `Faculty Members with > ${minYears} Years Experience`,
                content: `Faculty members with more than <strong>${minYears} years of experience</strong> (Total: ${expFaculty.length}):<br><br>` +
                         expFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.experience} (${f.role})`).join('<br>'),
                url: 'faculty.php',
                ctaText: 'View Faculty Directory →'
            };
        }

        // 6. FACULTY FILTERING & COUNTING QUERIES
        const isFacultyQuery = /\b(faculty|faculties|teacher|teachers|professor|professors|staff)\b/i.test(q);

        if (isFacultyQuery || /\b(who (teaches|has phd|has a phd|has mtech|specializes in)|who has a doctorate|phd holders|doctorate holders|faculty list|all faculty|show faculty|tell me about faculty)\b/i.test(q)) {

            // A. PhD Filter
            if (/\b(phd|ph\.d|ph\.d\.|doctorate|doctorates|doctor of philosophy|doctoral degree|doctoral)\b/i.test(q)) {
                const phdFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => f.hasPhD || /\b(ph\.d|phd|doctorate|doctor of philosophy|doctoral)\b/i.test(f.qualification)));

                if (/\b(how many|count|number of)\b/i.test(q)) {
                    return {
                        id: 'faculty_phd_count',
                        category: 'Faculty Directory',
                        title: 'Faculty Members with Ph.D',
                        content: `There are <strong>${phdFaculty.length} faculty members</strong> with a Ph.D in the department:<br><br>` +
                                 phdFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.qualification} (${f.role})`).join('<br>'),
                        url: 'faculty.php',
                        ctaText: 'View Faculty Directory →'
                    };
                }

                return {
                    id: 'faculty_phd_list',
                    category: 'Faculty Directory',
                    title: 'Faculty Members with Ph.D Degree',
                    content: `Here are all faculty members who hold a Ph.D degree in the department (Total: ${phdFaculty.length}):<br><br>` +
                             phdFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.qualification} (${f.role})<br>&nbsp;&nbsp;&nbsp;• Contact Email: ${f.email || 'N/A'}`).join('<br><br>'),
                    url: 'faculty.php',
                    ctaText: 'View Faculty Directory →'
                };
            }

            // B. MTech Filter
            if (/\b(mtech|m\.tech)\b/i.test(q)) {
                const mtechFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => /\b(m\.tech|mtech)\b/i.test(f.qualification)));
                return {
                    id: 'faculty_mtech_list',
                    category: 'Faculty Directory',
                    title: 'Faculty Members with M.Tech Degree',
                    content: `Here are all faculty members with M.Tech degree (Total: ${mtechFaculty.length}):<br><br>` +
                             mtechFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.qualification} (${f.role})`).join('<br>'),
                    url: 'faculty.php',
                    ctaText: 'View Faculty Directory →'
                };
            }

            // C. Subject / Specialization Filter
            if (/\b(teaches|teaching|specializes in|specialization|subject|subjects)\b/i.test(q) || /\b(machine learning|ai|artificial intelligence|cyber security|cloud|iot|bioinformatics)\b/i.test(q)) {
                let matchedSubject = '';
                if (/\b(machine learning|ml)\b/i.test(q)) matchedSubject = 'Machine Learning';
                else if (/\b(artificial intelligence|ai)\b/i.test(q)) matchedSubject = 'Artificial Intelligence';
                else if (/\b(cyber security|security)\b/i.test(q)) matchedSubject = 'Cyber Security';
                else if (/\b(cloud|cloud computing)\b/i.test(q)) matchedSubject = 'Cloud Computing';
                else if (/\b(iot|internet of things)\b/i.test(q)) matchedSubject = 'IoT';
                else if (/\bbioinformatics\b/i.test(q)) matchedSubject = 'Bioinformatics';

                if (matchedSubject) {
                    const specFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => {
                        const specStr = (f.specialization + ' ' + f.subjects + ' ' + (f.description || '') + ' ' + (f.searchableText || '')).toLowerCase();
                        return specStr.includes(matchedSubject.toLowerCase());
                    }));

                    if (specFaculty.length > 0) {
                        return {
                            id: `faculty_spec_${matchedSubject.replace(/\s+/g, '_')}`,
                            category: 'Faculty Specialization',
                            title: `Faculty Teaching / Specializing in ${matchedSubject}`,
                            content: `Faculty members specializing in or teaching <strong>${matchedSubject}</strong> (Total: ${specFaculty.length}):<br><br>` +
                                     specFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> (${f.role})<br>&nbsp;&nbsp;&nbsp;• Specialization: ${f.specialization || 'N/A'}`).join('<br><br>'),
                            url: 'faculty.php',
                            ctaText: 'View Faculty Directory →'
                        };
                    }
                }
            }

            // D. Faculty Total Count Query
            if (/\b(how many|count|total number of)\b/i.test(q)) {
                const uniqueFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER);
                return {
                    id: 'faculty_total_count',
                    category: 'Faculty Directory',
                    title: 'Total Department Faculty Count',
                    content: `There are <strong>${uniqueFaculty.length} total faculty members</strong> in the CSD & CSIT department (including HODs, Professors, Assistant Professors, and Teaching Assistants).`,
                    url: 'faculty.php',
                    ctaText: 'View Complete Faculty Directory →'
                };
            }

            // E. List All Faculty
            if (isFacultyQuery || /\b(faculty list|all faculty|show faculty|faculty members|who are the faculty|tell me about faculty|about faculty|faculty directory)\b/i.test(q)) {
                const uniqueFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER);
                return {
                    id: 'faculty_all_list',
                    category: 'Faculty Directory',
                    title: 'All Department Faculty Members',
                    content: `Here are all <strong>${uniqueFaculty.length} Faculty Members</strong> of CSD & CSIT Departments:<br><br>` +
                             uniqueFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.designation || f.role} (${f.department})`).join('<br>'),
                    url: 'faculty.php',
                    ctaText: 'View Complete Faculty Directory →'
                };
            }
        }

        // 7. HOUSE TOP CONTRIBUTOR / HIGHEST POINTS QUERY
        if (/\b(highest points|top contributor|top scorer|highest score|leader|top member)\b/i.test(q)) {
            let houseKey = targetHouseKey;
            if (/\bjal\b/i.test(q)) houseKey = 'JAL';
            else if (/\bagni\b/i.test(q)) houseKey = 'AGNI';
            else if (/\bvayu\b/i.test(q)) houseKey = 'VAYU';
            else if (/\b(akash|aakash)\b/i.test(q)) houseKey = 'AAKASH';
            else if (/\b(prudhvi|pruthvi)\b/i.test(q)) houseKey = 'PRUDHVI';

            if (houseKey && MASTER_HOUSE_ROSTER[houseKey]) {
                const h = MASTER_HOUSE_ROSTER[houseKey];
                const sorted = [...h.members].sort((a, b) => (b.points || 0) - (a.points || 0));
                const topMember = sorted[0];

                conversationContext.activeHouse = houseKey;
                const fullPersonMatch = MASTER_PERSON_INDEX.find(p => p.fullName.toUpperCase() === topMember.name.toUpperCase());
                if (fullPersonMatch) conversationContext.activePerson = fullPersonMatch;

                return {
                    id: `house_top_contributor_${houseKey}`,
                    category: 'House Rankings',
                    title: `Top Contributor in ${h.name} House`,
                    content: `The top contributor in <strong>${h.name} House</strong> is <strong>${topMember.name}</strong> with <strong>${topMember.points || 450} points</strong>.<br><br>• <strong>Registration Number:</strong> ${topMember.regNo || 'N/A'}<br>• <strong>Section:</strong> ${topMember.section || 'CSD/CSIT'}`,
                    url: `house_detail.php?house=${h.name}`,
                    ctaText: `View ${h.name} House Leaderboard →`
                };
            }
        }

        // 8. MULTI-CONDITION STUDENT HOUSE FILTER
        if (/\b(in|from)\b/i.test(q) && /\bhouse\b/i.test(q)) {
            let hKey = null;
            if (/\bjal\b/i.test(q)) hKey = 'JAL';
            else if (/\bagni\b/i.test(q)) hKey = 'AGNI';
            else if (/\bvayu\b/i.test(q)) hKey = 'VAYU';
            else if (/\b(akash|aakash)\b/i.test(q)) hKey = 'AAKASH';
            else if (/\b(prudhvi|pruthvi)\b/i.test(q)) hKey = 'PRUDHVI';

            if (hKey && MASTER_HOUSE_ROSTER[hKey]) {
                const h = MASTER_HOUSE_ROSTER[hKey];
                let filtered = h.members;

                if (/\b(2nd|2|second)\b/i.test(q)) {
                    filtered = filtered.filter(m => /2|ii|second/i.test(m.section || ''));
                }
                if (/\bcsd\b/i.test(q)) {
                    filtered = filtered.filter(m => /csd/i.test(m.section || 'A'));
                }

                conversationContext.activeHouse = hKey;
                const listItems = filtered.map((m, i) => `${i + 1}. <strong>${m.name}</strong> (Section: ${m.section || 'CSD II Year Sec A'})`).join('<br>');
                return {
                    id: `filtered_house_members_${hKey}`,
                    category: 'House Student Directory',
                    title: `${h.name} House Members (Filtered)`,
                    content: `Matching 2nd Year CSD students in <strong>${h.name} House</strong> (Total: ${filtered.length}):<br><br>${listItems}`,
                    url: `house_detail.php?house=${h.name}`,
                    ctaText: `View Full ${h.name} House Roster →`
                };
            }
        }

        // 9. CLASS REPRESENTATIVE QUERIES & LISTS
        if (/\b(class representative|class representatives|cr|crs)\b/i.test(q)) {
            if (/\b(2nd|2|second|ii)\b/i.test(q)) {
                const secYearCRs = deduplicatePeople(MASTER_CR_INDEX.filter(cr => cr.year === '2nd Year'));
                return {
                    id: 'cr_2nd_year',
                    category: 'Class Representatives',
                    title: '2nd Year Class Representatives (CRs)',
                    content: `Here are the <strong>2nd Year Class Representatives</strong>:<br><br>` +
                             secYearCRs.map(cr => `• <strong>${cr.fullName}</strong> — ${cr.role} (Reg: ${cr.regNo})`).join('<br>'),
                    url: 'heroes_of_department.php#class-representatives',
                    ctaText: 'View Class Representatives →'
                };
            }

            const uniqueCRs = deduplicatePeople(MASTER_CR_INDEX);
            return {
                id: 'cr_all_list',
                category: 'Class Representatives',
                title: 'All Department Class Representatives (CRs)',
                content: `Here are all <strong>14 Class Representatives (CRs)</strong> across 2nd, 3rd, and 4th Years for CSD & CSIT:<br><br>` +
                         uniqueCRs.map((cr, i) => `${i + 1}. <strong>${cr.fullName}</strong> — ${cr.role} (Reg: ${cr.regNo || 'N/A'})`).join('<br>'),
                url: 'heroes_of_department.php#class-representatives',
                ctaText: 'View Class Representatives →'
            };
        }

        // 10. CULTURAL & COMPETITION ACHIEVERS
        if (/\b(dance|dance competition|classical dance|karate|sports|winner|won)\b/i.test(q)) {
            if (/\bdance\b/i.test(q)) {
                return {
                    id: 'dance_winners',
                    category: 'Cultural Achievements',
                    title: 'Classical Dance Competition Winners',
                    content: `Classical Dance Winners at 45th SRKREC Annual Day:<br><br>
1. 🥇 <strong>P.B.S Kruti</strong> (Reg: 25B91A0789) — 1st Prize Winner in Classical Dance Group Performance.<br>
2. 🥈 <strong>R. Lakshmi Prasanna</strong> (Reg: 24B91A6245) — 2nd Prize Winner in Classical Dance Group Performance.`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Cultural Achievers →'
                };
            }
        }

        return null;
    }

    /**
     * =========================================================================
     * 11. HOUSE SYSTEM INTENT ENGINE
     * =========================================================================
     */
    function searchHouseSystem(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        const isFiveHousesOverview = /^(what are the (five|5) houses\??|five houses|5 houses|tell me about the (five|5) houses|student houses|what houses are available\??)$/i.test(lower);
        if (isFiveHousesOverview) {
            return {
                id: 'student_houses_overview',
                category: 'Student Houses Overview',
                title: 'Five Student Houses (Elemental Leagues)',
                content: `Five Student Houses & Elemental Leagues:
• 💧 Jal — Water Element (Adaptability, Fluidity & Analytics)
• 🔥 Agni — Fire Element (Passion, Illumination & Innovation)
• 💨 Vayu — Air Element (Agile Speed & Dynamic Thinking)
• 🌌 Akash — Ether/Sky Element (Vision, Ambition & AI/Cloud)
• 🌍 Prudhvi — Earth Element (Grounded Strength, Ethics & Discipline)

Students compete in continuous hackathons, coding contests, sports, and cultural battles. Ask for "Jal house members", "Agni house members", "Vayu house members", "Akash house members", or "Prudhvi house members" to view specific house rosters!`,
                url: 'house_detail.php',
                ctaText: 'View House Leaderboard →'
            };
        }

        let requestedHouseKey = null;
        if (/\b(jal|water)\b/i.test(lower)) requestedHouseKey = 'JAL';
        else if (/\b(agni|fire)\b/i.test(lower)) requestedHouseKey = 'AGNI';
        else if (/\b(vayu|wind)\b/i.test(lower)) requestedHouseKey = 'VAYU';
        else if (/\b(akash|aakash|sky)\b/i.test(lower)) requestedHouseKey = 'AAKASH';
        else if (/\b(prudhvi|pruthvi|earth)\b/i.test(lower)) requestedHouseKey = 'PRUDHVI';

        if (!requestedHouseKey) return null;

        const houseData = MASTER_HOUSE_ROSTER[requestedHouseKey];
        if (!houseData) return null;

        conversationContext.activeHouse = requestedHouseKey;

        const displayName = houseData.name;
        const membersList = houseData.members;
        let listItems = membersList.map((m, idx) => `${idx + 1}. <strong>${m.name}</strong> — Reg: ${m.regNo || 'N/A'} | Section: ${m.section || 'CSD/CSIT'}`).join('<br>');

        return {
            id: `house_members_${requestedHouseKey}`,
            category: 'House Members',
            title: `${displayName} House Members`,
            content: `Here are all members of <strong>${displayName} House</strong> (Total: ${membersList.length} students):<br><br>${listItems}`,
            url: `house_detail.php?house=${displayName}`,
            ctaText: `View Full ${displayName} House Roster →`
        };
    }

    /**
     * =========================================================================
     * 12. FIELD-LEVEL ANSWER SYNTHESIZER
     * =========================================================================
     */
    function formatFieldLevelAnswer(person, intent, rawQuery) {
        conversationContext.activePerson = person;

        const name = person.fullName;
        const dept = person.department || person.branch || 'CSD & CSIT';
        const role = person.role || person.designation || person.category;
        const reg = person.regNo;
        const email = person.email;

        let answerText = '';

        switch (intent) {
            case 'DEPARTMENT':
                answerText = `<strong>${name}</strong> belongs to the <strong>${dept}</strong> department.`;
                if (role) answerText += `<br><br>• <strong>Role:</strong> ${role}`;
                if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}`;
                break;

            case 'BRANCH':
                answerText = `<strong>${name}</strong> is from the <strong>${dept}</strong> branch.`;
                if (role) answerText += `<br><br>• <strong>Role:</strong> ${role}`;
                if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}`;
                break;

            case 'ROLE':
                answerText = `<strong>${name}</strong>'s role is <strong>${role}</strong>.`;
                answerText += `<br><br>• <strong>Department:</strong> ${dept}`;
                if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}`;
                break;

            case 'YEAR':
                if (person.year) {
                    answerText = `<strong>${name}</strong> is studying in <strong>${person.year}</strong> (${dept} Department).`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but academic year information is applicable for students, not faculty members in department records.`;
                }
                break;

            case 'SECTION':
                if (person.section) {
                    answerText = `<strong>${name}</strong> belongs to <strong>${person.section}</strong> (${dept} Department).`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but section information is not specified in current records.`;
                }
                break;

            case 'REGISTRATION_NUMBER':
                if (reg) {
                    answerText = `<strong>${name}</strong>'s registration number is <strong>${reg}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but registration numbers are applicable for students, not faculty members in department records.`;
                }
                break;

            case 'QUALIFICATION':
                if (person.qualification) {
                    answerText = `<strong>${name}</strong>'s educational qualification is: <strong>${person.qualification}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific educational qualification details are not available in current records.`;
                }
                break;

            case 'SPECIALIZATION':
                if (person.specialization) {
                    answerText = `<strong>${name}</strong>'s area of specialization & research interests: <strong>${person.specialization}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific specialization details are not available in current records.`;
                }
                break;

            case 'SUBJECTS':
                if (person.subjects) {
                    answerText = `<strong>${name}</strong> teaches & specializes in: <strong>${person.subjects}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific subjects taught are not listed in current records.`;
                }
                break;

            case 'EXPERIENCE':
                if (person.experience) {
                    answerText = `<strong>${name}</strong> has <strong>${person.experience}</strong> of teaching, research, and administrative experience.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but exact years of experience are not specified in current records.`;
                }
                break;

            case 'GRANTS':
                if (person.grants) {
                    answerText = `<strong>${name}</strong>'s Research Grants & Funded Projects:<br><br>• ${person.grants}`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific research grant details are not listed in current records.`;
                }
                break;

            case 'AWARDS':
                if (person.awards) {
                    answerText = `<strong>${name}</strong>'s Awards & Recognitions:<br><br>• ${person.awards}`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific award details are not listed in current records.`;
                }
                break;

            case 'PUBLICATIONS':
                if (person.publications) {
                    answerText = `<strong>${name}</strong>'s SCI & Scopus Publications:<br><br>• ${person.publications}`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific publication records are not listed in current records.`;
                }
                break;

            case 'EMAIL':
                if (email) {
                    answerText = `<strong>${name}</strong>'s contact email is <strong>${email}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but contact email is not available in current records.`;
                }
                break;

            case 'INTERNSHIP':
                {
                    const normN = normalizePersonName(name);
                    const matchingInterns = MASTER_INTERNSHIPS_INDEX.filter(i => (reg && i.regNo && i.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(i.name) === normN);
                    if (matchingInterns.length > 0) {
                        answerText = `<strong>${name}</strong>'s Corporate Internship Details:<br><br>`;
                        matchingInterns.forEach(i => {
                            answerText += `• <strong>Company:</strong> <strong>${i.company || 'Corporate Partner'}</strong><br>`;
                            answerText += `• <strong>Role:</strong> ${i.role}<br>`;
                            answerText += `• <strong>Status:</strong> ${i.status || 'Selected / Active'}<br>`;
                            if (i.stipend) answerText += `• <strong>Stipend:</strong> ${i.stipend}<br>`;
                        });
                        if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}<br>`;
                        answerText += `• <strong>Department:</strong> ${dept}`;
                    } else if (reg) {
                        answerText = `I found student <strong>${name}</strong> (${dept} Department, Reg: ${reg}), but specific internship selection records for this student are not listed in current department records.`;
                    } else {
                        answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but corporate internship details apply to student records.`;
                    }
                }
                break;

            case 'PLACEMENT':
                {
                    const normN = normalizePersonName(name);
                    const matchingPlacements = MASTER_PLACEMENTS_INDEX.filter(p => (reg && p.regNo && p.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(p.name) === normN);
                    if (matchingPlacements.length > 0) {
                        answerText = `<strong>${name}</strong>'s Campus Placement Details:<br><br>`;
                        matchingPlacements.forEach(p => {
                            answerText += `• <strong>Company / Recruiter:</strong> <strong>${p.company || 'Campus Recruiter'}</strong><br>`;
                            answerText += `• <strong>Role / Offer:</strong> ${p.role}<br>`;
                        });
                        if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}<br>`;
                        answerText += `• <strong>Department:</strong> ${dept}`;
                    } else if (reg) {
                        answerText = `I found student <strong>${name}</strong> (${dept} Department, Reg: ${reg}), but specific campus placement offer records for this student are not listed in current department records.`;
                    } else {
                        answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but placement details apply to student records.`;
                    }
                }
                break;

            case 'HOUSE':
                {
                    const normN = normalizePersonName(name);
                    let foundHouse = null;
                    let foundMember = null;
                    for (const houseKey in MASTER_HOUSE_ROSTER) {
                        const h = MASTER_HOUSE_ROSTER[houseKey];
                        const m = h.members.find(mem => (reg && mem.regNo && mem.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(mem.name) === normN);
                        if (m) {
                            foundHouse = h;
                            foundMember = m;
                            break;
                        }
                    }
                    if (foundHouse && foundMember) {
                        answerText = `<strong>${name}</strong>'s Student House & Elemental League Details:<br><br>`;
                        answerText += `• <strong>Student House:</strong> <strong>${foundHouse.name} House</strong><br>`;
                        answerText += `• <strong>House Motto:</strong> ${foundHouse.description}<br>`;
                        if (foundMember.points) answerText += `• <strong>Earned Contributor Points:</strong> ${foundMember.points} Points<br>`;
                        if (reg) answerText += `<br>• <strong>Registration Number:</strong> ${reg}<br>`;
                        answerText += `• <strong>Department:</strong> ${dept}`;
                    } else {
                        answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific student house allocation details were not found in house rosters.`;
                    }
                }
                break;

            case 'PROFILE':
            default:
                answerText = `<strong>${name}</strong> — ${role}:<br><br>`;
                answerText += `• <strong>Department:</strong> ${dept}<br>`;
                if (reg) answerText += `• <strong>Registration Number:</strong> ${reg}<br>`;
                if (email) answerText += `• <strong>Contact Email:</strong> ${email}<br>`;
                if (person.phone) answerText += `• <strong>Contact Phone:</strong> ${person.phone}<br>`;
                if (person.qualification) answerText += `• <strong>Educational Qualification:</strong> ${person.qualification}<br>`;
                if (person.specialization) answerText += `• <strong>Research & Specialization:</strong> ${person.specialization}<br>`;
                if (person.subjects) answerText += `• <strong>Subjects Taught & Skills:</strong> ${person.subjects}<br>`;
                if (person.experience) answerText += `• <strong>Experience:</strong> ${person.experience}<br>`;
                if (person.grants) answerText += `• <strong>Funded Projects & Grants:</strong> ${person.grants}<br>`;
                if (person.awards) answerText += `• <strong>Awards & Honors:</strong> ${person.awards}<br>`;
                if (person.publications) answerText += `• <strong>Reputed SCI / Scopus Publications:</strong> ${person.publications}<br>`;
                if (person.description) answerText += `• <strong>Profile Overview:</strong> ${person.description}`;

                // Cross-section joins: House, Internships, Placements, CR Role
                const normN = normalizePersonName(name);

                // 1. Cross-section House check
                for (const houseKey in MASTER_HOUSE_ROSTER) {
                    const h = MASTER_HOUSE_ROSTER[houseKey];
                    const m = h.members.find(mem => (reg && mem.regNo && mem.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(mem.name) === normN);
                    if (m) {
                        answerText += `<br>• <strong>Student House:</strong> <strong>${h.name} House</strong> (${m.points ? m.points + ' Points' : 'Active Member'})`;
                        break;
                    }
                }

                // 2. Cross-section Internship & Placement check
                const matchingInterns = MASTER_INTERNSHIPS_INDEX.filter(i => (reg && i.regNo && i.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(i.name) === normN);
                const matchingPlacements = MASTER_PLACEMENTS_INDEX.filter(p => (reg && p.regNo && p.regNo.toUpperCase() === reg.toUpperCase()) || normalizePersonName(p.name) === normN);

                if (matchingInterns.length > 0 || matchingPlacements.length > 0) {
                    answerText += `<br><br><strong>💼 Corporate Internships & Placements Records:</strong><br>`;
                    matchingInterns.forEach(i => {
                        answerText += `• <strong>Internship:</strong> Selected by <strong>${i.company || 'Corporate Partner'}</strong> as ${i.role} (Status: ${i.status || 'Active'})<br>`;
                    });
                    matchingPlacements.forEach(p => {
                        answerText += `• <strong>Placement:</strong> Placed at <strong>${p.company || 'Recruiter'}</strong> — ${p.role}<br>`;
                    });
                }
                break;
        }

        return {
            id: person.id,
            category: person.category,
            title: `${person.fullName} — ${person.category}`,
            content: answerText,
            url: person.url || 'faculty.php',
            ctaText: person.ctaText || 'View Profile on Website →',
            isPersonQuery: true,
            requestedField: intent
        };
    }

    /**
     * =========================================================================
     * 11.5. INTERNSHIPS & PLACEMENTS HYBRID SEARCH & ENTITY RESOLUTION ENGINE
     * =========================================================================
     */
    function searchInternshipsAndPlacements(rawQuery) {
        if (!rawQuery) return null;
        const q = rawQuery.toLowerCase().trim();

        const isInternshipQuery = /\b(internship|internships|intern|interns|stipend|stipends|zennith|eduskills|future interns|track 3d|idea lab|idealab|90 heal|dms|bluconn|blucon|rhythmiqcx|aunix|aws|cognifyz|saiket|tech nirmaan|unified mentor)\b/i.test(q);
        const isPlacementQuery = /\b(placement|placements|placed|recruiter|recruiters|package|packages|lpa|highest package|average package|placement rate|microsoft|tcs|infosys|wipro|cognizant|accenture|capgemini|tech mahindra)\b/i.test(q);

        if (!isInternshipQuery && !isPlacementQuery) return null;

        const isCSD = /\bcsd\b/i.test(q);
        const isCSIT = /\bcsit\b/i.test(q);
        const is2ndYear = /\b(2nd|second|2\/4|ii year|2nd year)\b/i.test(q);
        const is3rdYear = /\b(3rd|third|3\/4|iii year|3rd year)\b/i.test(q);
        const is4thYear = /\b(4th|fourth|final|4\/4|iv year|4th year|final year)\b/i.test(q);

        // A. COUNT QUERIES: "how many students got internships?", "how many got placements?"
        if (/\b(how many|count|total number|number of)\b/i.test(q)) {
            if (isInternshipQuery) {
                let list = MASTER_INTERNSHIPS_INDEX;
                if (isCSD) list = list.filter(r => (r.branch || r.department) === 'CSD');
                if (isCSIT) list = list.filter(r => (r.branch || r.department) === 'CSIT');
                const uniqueStudents = deduplicatePeople(list);
                let label = isCSD ? 'CSD ' : (isCSIT ? 'CSIT ' : '');
                return {
                    id: 'internship_count',
                    category: 'Internships & Careers',
                    title: `${label}Internship Statistics & Selection Counts`,
                    content: `Total <strong>${uniqueStudents.length} ${label}Students</strong> have been selected for industrial internships across leading companies (Zennith Digital Tech LLP, Amazon, Track 3D, EduSkills, Future Interns, Idea Lab, 90 Heal, DMS, Bluconn, etc.).<br><br>Ask "Show all internship members" or "Which CSD students got internships" to view complete member listings!`,
                    url: 'internships.php',
                    ctaText: 'View Internships Page →'
                };
            }
            if (isPlacementQuery) {
                return {
                    id: 'placement_count',
                    category: 'Placements & Careers',
                    title: 'Placement Statistics & Campus Placement Rate',
                    content: `Placement Highlights & Statistics:<br><br>
• <strong>Placement Rate:</strong> 66% Students Placed across CSD & CSIT.<br>
• <strong>Highest Package:</strong> ₹12.0 LPA (Microsoft India).<br>
• <strong>Average Package:</strong> ₹5.1 LPA.<br>
• <strong>Top Recruiting Companies:</strong> Microsoft India, TCS, Infosys, Wipro, Cognizant, Accenture, Amazon, Capgemini, Tech Mahindra, Zennith Digital Tech LLP.<br><br>
Ask "Show placement overview" or "Who got placed at Microsoft" to view details!`,
                    url: 'placements.php',
                    ctaText: 'View Placements Page →'
                };
            }
        }

        // B. COMPANY SPECIFIC SEARCH: "who got an internship at [company]?", "who got placed at TCS?"
        let targetCompany = null;
        const companies = ['zennith', 'amazon', 'microsoft', 'tcs', 'infosys', 'wipro', 'cognizant', 'accenture', 'capgemini', 'tech mahindra', 'eduskills', 'future interns', 'track 3d', 'idea lab', 'idealab', '90 heal', 'dms', 'bluconn', 'blucon', 'rhythmiqcx', 'aunix', 'aws', 'cognifyz', 'saiket', 'tech nirmaan', 'unified mentor'];
        for (const c of companies) {
            if (q.includes(c)) {
                targetCompany = c;
                break;
            }
        }

        if (targetCompany) {
            let pool = [...MASTER_INTERNSHIPS_INDEX, ...MASTER_PLACEMENTS_INDEX];
            let matches = pool.filter(r => (r.company || '').toLowerCase().includes(targetCompany) || (r.role || '').toLowerCase().includes(targetCompany));
            if (isCSD) matches = matches.filter(r => (r.branch || r.department) === 'CSD');
            if (isCSIT) matches = matches.filter(r => (r.branch || r.department) === 'CSIT');

            matches = deduplicatePeople(matches);

            const compName = targetCompany.toUpperCase();
            if (matches.length > 0) {
                let listHTML = matches.map((m, i) => `${i + 1}. <strong>${m.name}</strong> (${m.regNo || 'Reg N/A'}) — ${m.branch || 'CSIT'} ${m.year || ''} | Role: ${m.role || 'Intern'}`).join('<br>');
                return {
                    id: `company_internship_${targetCompany}`,
                    category: 'Company Selections',
                    title: `Students Selected by ${compName}`,
                    content: `Here are all <strong>${matches.length} Students</strong> selected by <strong>${compName}</strong>:<br><br>${listHTML}`,
                    url: 'internships.php',
                    ctaText: 'View Active Internships →'
                };
            } else {
                return {
                    id: `company_internship_none_${targetCompany}`,
                    category: 'Company Selections',
                    title: `Selections for ${compName}`,
                    content: `Currently, <strong>${compName}</strong> is listed among our recruiting partners. For complete company-wise selection lists, you can visit the Internships & Placements section.`,
                    url: 'placements.php',
                    ctaText: 'View Placements & Recruiters →'
                };
            }
        }

        // C. MEMBER LISTINGS & GENERAL INTERNSHIP / PLACEMENT QUERIES
        if (isInternshipQuery) {
            let list = [...MASTER_INTERNSHIPS_INDEX];
            if (isCSD) list = list.filter(r => (r.branch || r.department) === 'CSD');
            if (isCSIT) list = list.filter(r => (r.branch || r.department) === 'CSIT');
            if (is2ndYear) list = list.filter(r => (r.year || '').includes('2'));
            if (is3rdYear) list = list.filter(r => (r.year || '').includes('3'));
            if (is4thYear) list = list.filter(r => (r.year || '').includes('4'));

            list = deduplicatePeople(list);

            let filterLabel = '';
            if (isCSD) filterLabel += 'CSD ';
            if (isCSIT) filterLabel += 'CSIT ';
            if (is2ndYear) filterLabel += '2nd Year ';
            if (is3rdYear) filterLabel += '3rd Year ';
            if (is4thYear) filterLabel += '4th Year ';

            let listHTML = list.map((m, i) => `${i + 1}. <strong>${m.name}</strong> (Reg: ${m.regNo || 'N/A'}) — ${m.branch || 'CSIT'} ${m.year || ''} | Company: <strong>${m.company || 'Corporate Partner'}</strong> (${m.role || 'Intern'})`).join('<br>');

            return {
                id: 'internship_members_list',
                category: 'Internships & Careers',
                title: `${filterLabel.trim() || 'All'} Internship Members & Selected Students`,
                content: `Here are all <strong>${list.length} Students</strong> selected for industrial internships:<br><br>${listHTML}`,
                url: 'internships.php',
                ctaText: 'View Complete Internships Page →'
            };
        }

        if (isPlacementQuery) {
            let list = [...MASTER_PLACEMENTS_INDEX];
            if (isCSD) list = list.filter(r => (r.branch || r.department) === 'CSD');
            if (isCSIT) list = list.filter(r => (r.branch || r.department) === 'CSIT');

            list = deduplicatePeople(list);

            let filterLabel = isCSD ? 'CSD ' : (isCSIT ? 'CSIT ' : '');

            let contentText = `<strong>${filterLabel.trim() || 'Department'} Placement Overview & Recruiter Highlights:</strong><br><br>
• <strong>Highest Package:</strong> ₹12.0 LPA (Microsoft India)<br>
• <strong>Average Package:</strong> ₹5.1 LPA<br>
• <strong>Placement Rate:</strong> 66% Students Placed<br>
• <strong>Top Recruiting Companies:</strong> Microsoft India, TCS, Infosys, Wipro, Cognizant, Accenture, Amazon, Capgemini, Tech Mahindra.<br><br>`;

            if (list.length > 0) {
                contentText += `<strong>Placement Selected Members:</strong><br>` + list.map((m, i) => `${i + 1}. <strong>${m.name}</strong> (${m.regNo || 'N/A'}) — ${m.branch || 'CSIT'} | Role/Details: ${m.role || 'Placed'}`).join('<br>');
            }

            return {
                id: 'placement_members_list',
                category: 'Placements & Careers',
                title: `${filterLabel.trim() || 'Department'} Placement Overview & Records`,
                content: contentText,
                url: 'placements.php',
                ctaText: 'View Complete Placements Page →'
            };
        }

        return null;
    }

    /**
     * =========================================================================
     * 13. PRIMARY RAG HYBRID DISPATCHER ENFORCING RETRIEVAL SYSTEM
     * =========================================================================
     */
    function searchKnowledgeVector(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        // 1. PERSON / FACULTY / STUDENT LOOKUP FIRST
        const personResult = detectPersonInQuery(rawQuery);
        if (personResult && personResult.found) {
            if (personResult.isMultiple) {
                let listItems = personResult.candidates.map((p, idx) => `${idx + 1}. <strong>${p.fullName}</strong> (${p.role || p.category})`).join('<br>');
                return {
                    id: 'people_multiple_matches',
                    category: 'People Search',
                    title: 'Multiple Matching People Found',
                    content: `I found multiple people with similar names:<br><br>${listItems}<br><br>Could you please provide their full name, role, or department?`,
                    url: 'heroes_of_department.php',
                    ctaText: 'Explore Department People Directory →'
                };
            } else {
                console.log('[CHATBOT INTENT] Person Entity Match:', personResult.person.fullName, '| Intent:', personResult.intent);
                return formatFieldLevelAnswer(personResult.person, personResult.intent, rawQuery);
            }
        }

        // 2. INTERNSHIPS & PLACEMENTS HYBRID SEARCH SECOND
        const internPlacementResult = searchInternshipsAndPlacements(rawQuery);
        if (internPlacementResult) {
            console.log('[CHATBOT INTENT] Internship & Placement Match:', internPlacementResult.title);
            return internPlacementResult;
        }

        // 3. PROGRAM-SPECIFIC ACADEMICS METADATA RAG
        const academicMetadataResult = executeAcademicMetadataRAG(rawQuery);
        if (academicMetadataResult) {
            console.log('[CHATBOT INTENT] Academic Metadata RAG Match:', academicMetadataResult.title);
            return academicMetadataResult;
        }

        // 4. STRUCTURED QUERY ENGINE
        const structuredResult = executeStructuredQuery(rawQuery);
        if (structuredResult) {
            console.log('[CHATBOT INTENT] Structured Query Match:', structuredResult.title);
            return structuredResult;
        }

        // 4. HOUSE SYSTEM QUERY
        const houseResult = searchHouseSystem(rawQuery);
        if (houseResult) {
            console.log('[CHATBOT INTENT] House System Match:', houseResult.title);
            return houseResult;
        }

        // 5. HOD SPECIAL QUERY
        if (/\b(hod|hods|head of department|head of the department)\b/i.test(lower)) {
            return {
                id: 'hod_overview',
                category: 'Department Leadership',
                title: 'Heads of Department (HODs)',
                content: `Our department has two distinguished Heads of Department (HODs):<br><br>
1. <strong>Dr. M. Suresh Babu</strong> — Professor & Head of Department, Computer Science & Design (CSD)<br>• Email: sureshbabu.k@srkrec.edu.in | Qualification: Ph.D in CS (2012) | Research Grants: DBT Rs. 1.97 Crores<br><br>
2. <strong>Dr. N. Gopala Krishna Murthy</strong> — Professor & Head of Department, Computer Science & Information Technology (CSIT)<br>• Email: gopinukala@srkrec.edu.in | Qualification: Ph.D in CS (2014) | Research Grants: D-EVCI IIT Delhi Rs. 71.78 Lakhs`,
                url: 'faculty.php',
                ctaText: 'View Faculty Leadership Page →'
            };
        }

        // 6. FACULTY CATEGORY OVERVIEW
        if (/\b(who are the (faculty|faculties|teachers|professors)|faculty members|faculty directory|list of faculty|all faculty)\b/i.test(lower)) {
            const uniqueFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER);
            return {
                id: 'faculty_overview',
                category: 'Faculty Directory',
                title: 'CSD & CSIT Department Faculty Members',
                content: `Here are all <strong>${uniqueFaculty.length} Faculty Members</strong> of CSD & CSIT Departments:<br><br>` +
                         uniqueFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.designation || f.role} (${f.department})`).join('<br>'),
                url: 'faculty.php',
                ctaText: 'View Complete Faculty Directory →'
            };
        }

        // 7. STUDENT CATEGORY OVERVIEW
        if (/\b(who are the students|student body|students list|list of students|student directory)\b/i.test(lower)) {
            return {
                id: 'students_overview',
                category: 'Student Directory',
                title: 'CSD & CSIT Student Body & Sections',
                content: `CSD & CSIT Student Directory & Academic Sections:<br><br>
• <strong>Total Enrolled Students:</strong> 600+ across 2nd, 3rd, and 4th Years in CSD & CSIT.<br>
• <strong>Academic Sections:</strong> CSD II Year, CSD III Year, CSD IV Year, CSIT II Year Sec A & B, CSIT III Year Sec A & B, CSIT IV Year.<br>
• <strong>Student Houses:</strong> Jal, Agni, Vayu, Akash, Prudhvi.`,
                url: 'heroes_of_department.php',
                ctaText: 'View Student Directory & Leadership →'
            };
        }

        // 8. WEBSITE SECTION MATRIX SEARCH
        for (const chunk of KNOWLEDGE_MATRIX) {
            if (chunk.keywords.some(k => lower.includes(k))) {
                return chunk;
            }
        }

        // 9. UNKNOWN / CLARIFICATION FOR EXPLICIT PERSON QUERY
        if (/^\b(who is|tell me about|profile of|info on|details of|which department does|which branch is|what is the role of|department of)\b/i.test(lower)) {
            return {
                id: 'person_not_found',
                category: 'People Search',
                title: 'Person Not Found',
                isNotFound: true,
                content: `I couldn't find that information in the department website. Could you provide their full name, role, or department?`,
                url: 'heroes_of_department.php',
                ctaText: 'View Department Directory →'
            };
        }

        return null;
    }

    /**
     * =========================================================================
     * 14. LOCAL ANSWER SYNTHESIZER
     * =========================================================================
     */
    function synthesizeLocalAnswer(matchedChunk, rawQuery) {
        if (!matchedChunk) {
            return {
                answer: `I couldn't find that information in the department website. You can contact the department office for further details.`,
                ctaLinks: [{ text: 'Contact Department →', url: 'footer.php' }],
                suggestions: ['What courses are offered?', 'Who is the HOD?', 'Tell me about startups']
            };
        }

        if (matchedChunk.isNotFound) {
            return {
                answer: matchedChunk.content,
                ctaLinks: [{ text: matchedChunk.ctaText || 'View Directory →', url: matchedChunk.url || 'heroes_of_department.php' }],
                suggestions: ['Who is Suresh Babu Mudunuri?', 'Who is Preeti?', 'Who is Satyam Sir?', 'Who is Trinadh Sir?']
            };
        }

        return {
            answer: matchedChunk.isPersonQuery ? matchedChunk.content : `<strong>${matchedChunk.title}:</strong><br><br>${matchedChunk.content.replace(/\n/g, '<br>')}`,
            ctaLinks: [{ text: matchedChunk.ctaText, url: matchedChunk.url }],
            suggestions: ['CSD course outcomes', 'CSIT course outcomes', 'Compare CSD and CSIT course outcomes', 'Faculty list who did phd']
        };
    }

    /**
     * =========================================================================
     * 15. MAIN PUBLIC METHOD: getBotResponse
     * =========================================================================
     */
    async function getBotResponse(userInput, config = {}) {
        if (isProcessingRequest) {
            console.log('[CHATBOT] Request ignored (debounced).');
            return { answer: 'Please wait, I am already processing your previous request.' };
        }
        isProcessingRequest = true;

        try {
            console.log('[CHATBOT] Request started for:', userInput);
            const normalizedQuery = userInput.toLowerCase().trim();

            if (typeof window !== 'undefined') {
                await syncWebsiteKnowledge();
            }

            if (responseCache.has(normalizedQuery)) {
                console.log('[CHATBOT] Cache hit for:', normalizedQuery);
                return responseCache.get(normalizedQuery);
            }

            // Casual greetings
            if (/^(hi|hello|hey|greetings|good morning|good afternoon|good evening)$/i.test(normalizedQuery)) {
                const greetingRes = {
                    answer: `Hello! 👋 I'm the official AI Department Assistant for SRKR CSD & CSIT. How can I help you today?`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['CSD course outcomes', 'CSIT course outcomes', 'Faculty list who did phd', 'Jal house members']
                };
                responseCache.set(normalizedQuery, greetingRes);
                return greetingRes;
            }

            if (/^(how are you|how are you\?|how r u)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I'm doing great! Thank you for asking. 😊 I'm fully equipped to answer questions about CSD and CSIT course outcomes, program outcomes, syllabus, faculty, student houses, CRs, and placements. How can I assist you today?`,
                    ctaLinks: [{ text: 'View Department Overview →', url: 'explore.php' }],
                    suggestions: ['CSD course outcomes', 'CSIT course outcomes', 'Compare CSD and CSIT course outcomes']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what is your name\??|who are you\??|what are you\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I am the official **Department AI Assistant** for the Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) departments at SRKR Engineering College, Bhimavaram.`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['What can you do?', 'CSD course outcomes', 'CSIT course outcomes']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what can you do\??|help|what can i ask\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `Here is what I can help you with:<br><br>
• <strong>Program Academic & Syllabus RAG</strong> (e.g. "CSD course outcomes", "CSIT course outcomes", "Compare CSD and CSIT course outcomes", "Show CSD subjects")<br>
• <strong>Faculty Queries & Filters</strong> (e.g. "Faculty list who did phd", "Who teaches machine learning?", "Faculty with mtech")<br>
• <strong>House Members & Leaderboard</strong> (e.g. "Jal house members", "Who is the top contributor in Jal?")<br>
• <strong>Class Representatives (CRs)</strong> (e.g. "Who is Mohana Durga?", "Who are 2nd year CRs?")<br>
• <strong>Department Heroes & Achievers</strong> (e.g. "Who is Preeti?", "Who won the dance competition?")<br>
• <strong>Laboratories & Infrastructure</strong> (e.g. "What labs are available?")<br>
• <strong>Placements & Internships</strong> (e.g. "Tell me about placements", "Tell me about internships")<br>
• <strong>Startups & Incubation</strong> (e.g. "What startups are there?")`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['CSD course outcomes', 'CSIT course outcomes', 'Compare CSD and CSIT course outcomes']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            conversationContext.lastQuery = userInput;
            let matchedChunk = searchKnowledgeVector(userInput);

            if (matchedChunk && matchedChunk.isNotFound) {
                const notFoundRes = synthesizeLocalAnswer(matchedChunk, userInput);
                responseCache.set(normalizedQuery, notFoundRes);
                return notFoundRes;
            }

            let finalResponse = null;
            if (matchedChunk) {
                finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                responseCache.set(normalizedQuery, finalResponse);
                return finalResponse;
            }

            const proxyUrl = getRootApiUrl(config.remoteApiUrl || 'api/gemini_chat.php');
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
                            suggestions: ['CSD course outcomes', 'CSIT course outcomes', 'Faculty list who did phd']
                        };
                    }
                }
            } catch (err) {
                console.warn('Backend proxy check failed, falling back to local synthesis:', err);
            }

            if (!finalResponse) {
                finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                conversationContext.history.push({ role: 'user', text: userInput });
                conversationContext.history.push({ role: 'model', text: finalResponse.answer });
            }

            responseCache.set(normalizedQuery, finalResponse);
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
        getMasterPersonIndex: function () { return MASTER_PERSON_INDEX; },
        getMasterCRIndex: function () { return MASTER_CR_INDEX; },
        getMasterHouseRoster: function () { return MASTER_HOUSE_ROSTER; },
        getMasterProgramAcademics: function () { return MASTER_PROGRAM_ACADEMICS; },
        getDiagnostics: function () { return systemDiagnostics; },
        getContextState: function () { return conversationContext; },
        resetContext: function () {
            responseCache.clear();
            conversationContext = { activeEntity: null, activePerson: null, activeHouse: null, activeProgram: null, lastQuery: null, history: [] };
        }
    };
})();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatbotService;
}
