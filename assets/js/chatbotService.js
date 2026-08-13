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

        if (/\b(house|house points|elemental league)\b/i.test(q)) {
            return 'HOUSE';
        }
        if (/\b(which department|what department|department of|which dept|what dept|dept is|dept of|department)\b/i.test(q) || (/\b(belong to|belongs to)\b/i.test(q) && !/\b(house|league)\b/i.test(q))) {
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
                {"name":"MOTUPALLI MEENA PHANI SRI","regNo":"25B91A0770","section":"CSIT Sec B","points":150},
                {"name":"GUDAPATI LALITHA DEVI SRI","regNo":"25B91A6220","section":"CSD II Year Sec A","points":100},
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
     * 6. FACULTY TEACHING ASSIGNMENTS (FROM MYSQL faculties.class_id + classes)
     *    Built from live database: faculty_id → year/branch/section assignments
     * =========================================================================
     * Key: faculty_id (integer from DB)
     * years: normalized year labels matching student year metadata
     * branches: 'CSD' or 'CSIT'
     * classes: human-readable class descriptions
     */
    const FACULTY_TEACHING_ASSIGNMENTS = {
        1:  { name: 'Dr. Suresh Babu Mudunuri',   years: ['4th Year', '2nd Year'], branches: ['CSD'],         classes: ['4 Year CSD Sec A', '2 Year CSD Sec A'] },
        2:  { name: 'Dr. K. Srinivasa Rao',        years: ['4th Year'],             branches: ['CSD'],         classes: ['4 Year CSD Sec A'] },
        3:  { name: 'Mr. K. Bhanu Rajesh Naidu',   years: ['4th Year', '3rd Year', '2nd Year'], branches: ['CSIT', 'CSD'], classes: ['4 Year CSIT Sec A', '3 Year CSIT Sec B', '2 Year CSIT Sec A', '2 Year CSIT Sec B'] },
        4:  { name: 'A. Aswini Priyanka',          years: ['4th Year', '2nd Year'], branches: ['CSD', 'CSIT'], classes: ['4 Year CSD Sec A', '4 Year CSIT Sec A', '2 Year CSIT Sec A', '2 Year CSIT Sec B', '2 Year CSD Sec A'] },
        5:  { name: 'Angara Satyam',               years: ['4th Year', '3rd Year', '2nd Year'], branches: ['CSD', 'CSIT'], classes: ['4 Year CSD Sec A', '3 Year CSD Sec A', '2 Year CSIT Sec A', '2 Year CSD Sec A'] },
        6:  { name: 'S. Mohan Krishna',            years: ['5th Year', '4th Year', '3rd Year'], branches: ['CSD', 'CSIT'], classes: ['5 Year CSD Sec A', '4 Year CSD Sec A', '4 Year CSIT Sec A', '3 Year CSD Sec A', '3 Year CSIT Sec A', '3 Year CSIT Sec B'] },
        7:  { name: 'P S V Surya Kumar',           years: ['4th Year'],             branches: ['CSIT'],        classes: ['4 Year CSIT Sec A'] },
        8:  { name: 'Dr. N. Gopala Krishna Murthy',years: ['4th Year', '2nd Year'], branches: ['CSIT'],        classes: ['4 Year CSIT Sec A', '2 Year CSIT Sec A', '2 Year CSIT Sec B'] },
        10: { name: 'Navya Nallaparaju',            years: ['3rd Year'],             branches: ['CSD'],         classes: ['3 Year CSD Sec A'] },
        11: { name: 'N. Praveen',                  years: ['4th Year'],             branches: ['CSD'],         classes: ['4 Year CSD Sec A'] },
        12: { name: 'A. Krishna Veni',             years: ['3rd Year'],             branches: ['CSIT'],        classes: ['3 Year CSIT Sec A'] },
        13: { name: 'Mr. K.V.V.S. Trinadh Naidu', years: ['5th Year', '4th Year', '3rd Year', '2nd Year'], branches: ['CSD', 'CSIT'], classes: ['5 Year CSD Sec A', '4 Year CSD Sec A', '4 Year CSIT Sec A', '3 Year CSD Sec A', '3 Year CSIT Sec A', '3 Year CSIT Sec B', '2 Year CSIT Sec B'] },
        14: { name: 'Penmetsa Mouna',              years: ['5th Year', '4th Year', '3rd Year'], branches: ['CSD', 'CSIT'], classes: ['5 Year CSD Sec A', '4 Year CSD Sec A', '4 Year CSIT Sec A', '3 Year CSD Sec A', '3 Year CSIT Sec A', '3 Year CSIT Sec B'] },
        15: { name: 'Pericherla Manoj',            years: ['4th Year'],             branches: ['CSIT'],        classes: ['4 Year CSIT Sec A'] },
        16: { name: 'K. V. Sunil Varma',           years: ['5th Year', '4th Year', '3rd Year'], branches: ['CSD', 'CSIT'], classes: ['5 Year CSD Sec A', '4 Year CSD Sec A', '4 Year CSIT Sec A', '3 Year CSD Sec A', '3 Year CSIT Sec A', '3 Year CSIT Sec B'] },
        17: { name: 'N. Aneela',                   years: ['5th Year', '4th Year', '3rd Year', '2nd Year'], branches: ['CSD', 'CSIT'], classes: ['5 Year CSD Sec A', '4 Year CSD Sec A', '4 Year CSIT Sec A', '3 Year CSD Sec A', '3 Year CSIT Sec A', '3 Year CSIT Sec B', '2 Year CSIT Sec A', '2 Year CSIT Sec B'] },
        18: { name: 'M. S. Suseela',              years: ['3rd Year'],             branches: ['CSD'],         classes: ['3 Year CSD Sec A'] },
        19: { name: 'M. Srinu',                   years: ['2nd Year'],             branches: ['CSIT', 'CSD'], classes: ['2 Year CSIT Sec A', '2 Year CSD Sec A'] },
        20: { name: 'J. Mohan Surendra',          years: ['2nd Year'],             branches: ['CSIT'],        classes: ['2 Year CSIT Sec A'] },
        21: { name: 'G. Sudhakar',                years: ['2nd Year'],             branches: ['CSIT'],        classes: ['2 Year CSIT Sec B'] },
        22: { name: 'D. Parvathi',                years: ['2nd Year'],             branches: ['CSIT', 'CSD'], classes: ['2 Year CSIT Sec A', '2 Year CSIT Sec B', '2 Year CSD Sec A'] },
        23: { name: 'M. Maduriya',               years: ['2nd Year'],             branches: ['CSIT'],        classes: ['2 Year CSIT Sec B'] },
        24: { name: 'K. Girichar',               years: ['2nd Year'],             branches: ['CSD'],         classes: ['2 Year CSD Sec A'] },
        25: { name: 'K. Vignya',                 years: ['2nd Year'],             branches: ['CSD'],         classes: ['2 Year CSD Sec A'] }
    };

    /**
     * =========================================================================
     * 7. STUDENT YEAR-DEPARTMENT ANALYTICS (from MySQL classes + students tables)
     *    Source: classes table joined with students table
     *    Format: year label → branch → count of students
     * =========================================================================
     */
    const STUDENT_YEAR_DEPT_ANALYTICS = {
        '2nd Year': {
            'CSD':  { sections: ['Sec A'], totalStudents: null, classIds: [9] },
            'CSIT': { sections: ['Sec A', 'Sec B'], totalStudents: null, classIds: [7, 8] }
        },
        '3rd Year': {
            'CSD':  { sections: ['Sec A'], totalStudents: null, classIds: [4] },
            'CSIT': { sections: ['Sec A', 'Sec B'], totalStudents: null, classIds: [5, 6] }
        },
        '4th Year': {
            'CSD':  { sections: ['Sec A'], totalStudents: null, classIds: [2] },
            'CSIT': { sections: ['Sec A'], totalStudents: null, classIds: [3] }
        }
    };

    // Helper: normalize year string to canonical label
    function normalizeYearLabel(raw) {
        if (!raw) return null;
        const r = raw.toLowerCase().trim();
        if (/^(1st|first|i|1)\s*(year|yr)?$/.test(r)) return '1st Year';
        if (/^(2nd|second|ii|2)\s*(year|yr)?$/.test(r)) return '2nd Year';
        if (/^(3rd|third|iii|3)\s*(year|yr)?$/.test(r)) return '3rd Year';
        if (/^(4th|fourth|iv|4)\s*(year|yr)?$/.test(r)) return '4th Year';
        return null;
    }

    // Helper: extract year + branch from a query string
    function parseYearAndBranchFromQuery(q) {
        const lower = q.toLowerCase();
        const yearMatch = lower.match(/\b(1st|first|2nd|second|3rd|third|4th|fourth|i|ii|iii|iv)\s*(year|yr)?\b/i);
        const year = yearMatch ? normalizeYearLabel(yearMatch[1]) : null;
        const branch = /\bcsd\b/i.test(lower) ? 'CSD' : (/\bcsit\b/i.test(lower) ? 'CSIT' : null);
        return { year, branch };
    }

    /**
     * =========================================================================
     * 8. MASTER PERSON INDEX (25 FACULTY + HEROES + 14 CRs + HOUSE STUDENTS)
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
            person_type: 'student',
            firstName: 'srinu',
            lastName: 'mullu',
            category: 'Department Hero & Student Achiever (CSD)',
            role: 'NSS Coordinator & Ecom Hackathon Lead (CSD)',
            designation: 'NSS Coordinator & Ecom Hackathon Lead (CSD)',
            department: 'CSD',
            branch: 'CSD',
            regNo: '25B95A6206',
            achievements: 'NSS Coordinator, Python Development Lead (Bhimavaram Online App)',
            description: 'Mullu Srinu is a dedicated student leader and NSS coordinator in the CSD department (Reg: 25B95A6206).',
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
            id: 'student_25B91A0770',
            fullName: 'MOTUPALLI MEENA PHANI SRI',
            person_type: 'student',
            firstName: 'motupalli',
            lastName: 'sri',
            category: 'Student (CSIT Department, Jal House Member)',
            role: 'Student Member — Jal House (CSIT)',
            designation: 'Student Member — Jal House (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'CSIT Sec B',
            regNo: '25B91A0770',
            house: 'Jal',
            searchableAliases: ['motupalli meena phani sri', 'motupalli meena', 'meena phani sri', 'motupalli', 'meena', 'phani sri', '25b91a0770'],
            description: 'MOTUPALLI MEENA PHANI SRI is an enrolled student in CSIT Department (Jal House, Reg: 25B91A0770).',
            url: 'heroes_of_department.php',
            ctaText: 'View Student Directory →'
        },
        {
            id: 'person_chandani_vivekananda',
            fullName: 'CHANDANI VIVEKANANDA',
            firstName: 'chandani',
            lastName: 'vivekananda',
            category: 'Class Representative',
            role: 'Class Representative (CSIT III Year Sec A)',
            designation: 'Class Representative (CSIT III Year Sec A)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'CSIT III Year Sec A',
            regNo: '24B91A0720',
            isCR: true,
            searchableAliases: ['chandani vivekananda', 'vivekananda', 'chandani', 'c vivekananda'],
            description: 'Chandani Vivekananda is Class Representative for CSIT III Year Sec A (Reg: 24B91A0720).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_thota_johan_benedict',
            fullName: 'THOTA JOHAN BENEDICT',
            firstName: 'johan benedict',
            lastName: 'thota',
            category: 'Class Representative',
            role: 'Class Representative (CSIT III Year Sec B)',
            designation: 'Class Representative (CSIT III Year Sec B)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'CSIT III Year Sec B',
            regNo: '24B91A07B7',
            isCR: true,
            searchableAliases: ['thota johan benedict', 'johan benedict', 'johan', 'thota johan'],
            description: 'Thota Johan Benedict is Class Representative for CSIT III Year Sec B (Reg: 24B91A07B7).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_s_d_rani',
            fullName: 'S D RANI',
            firstName: 'rani',
            lastName: 's d',
            category: 'Class Representative',
            role: 'Class Representative (CSIT III Year Sec B)',
            designation: 'Class Representative (CSIT III Year Sec B)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'CSIT III Year Sec B',
            regNo: '24B91A07B3',
            isCR: true,
            searchableAliases: ['s d rani', 'sd rani', 'rani'],
            description: 'S D Rani is Class Representative for CSIT III Year Sec B (Reg: 24B91A07B3).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_pulavarthi_mohana_madhu_lasya_sri',
            fullName: 'PULAVARTHI MOHANA MADHU LASYA SRI',
            firstName: 'lasya sri',
            lastName: 'pulavarthi',
            category: 'Class Representative',
            role: 'Class Representative (CSD III Year)',
            designation: 'Class Representative (CSD III Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '3rd Year',
            section: 'CSD III Year',
            regNo: '25B95A6208',
            isCR: true,
            searchableAliases: ['pulavarthi mohana madhu lasya sri', 'lasya sri', 'mohana lasya sri', 'lasya'],
            description: 'Pulavarthi Mohana Madhu Lasya Sri is Class Representative for CSD III Year (Reg: 25B95A6208).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_p_sai_harsha',
            fullName: 'P SAI HARSHA',
            firstName: 'sai harsha',
            lastName: 'p',
            category: 'Class Representative',
            role: 'Class Representative (CSD IV Year)',
            designation: 'Class Representative (CSD IV Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '4th Year',
            section: 'CSD IV Year',
            regNo: '23B81A6252',
            isCR: true,
            searchableAliases: ['p sai harsha', 'sai harsha', 'harsha'],
            description: 'P Sai Harsha is Class Representative for CSD IV Year (Reg: 23B81A6252).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_p_swapna',
            fullName: 'P SWAPNA',
            firstName: 'swapna',
            lastName: 'p',
            category: 'Class Representative',
            role: 'Class Representative (CSD IV Year)',
            designation: 'Class Representative (CSD IV Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '4th Year',
            section: 'CSD IV Year',
            regNo: '23B91A6255',
            isCR: true,
            searchableAliases: ['p swapna', 'swapna'],
            description: 'P Swapna is Class Representative for CSD IV Year (Reg: 23B91A6255).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_r_divya_jyothika',
            fullName: 'R DIVYA JYOTHIKA',
            firstName: 'divya jyothika',
            lastName: 'r',
            category: 'Class Representative',
            role: 'Class Representative (CSIT IV Year)',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '4th Year',
            section: 'CSIT IV Year',
            regNo: '23B91A0747',
            isCR: true,
            searchableAliases: ['r divya jyothika', 'divya jyothika', 'divya'],
            description: 'R Divya Jyothika is Class Representative for CSIT IV Year (Reg: 23B91A0747).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_ch_sai_vikas',
            fullName: 'CH SAI VIKAS',
            firstName: 'sai vikas',
            lastName: 'ch',
            category: 'Class Representative',
            role: 'Class Representative (CSIT IV Year)',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '4th Year',
            section: 'CSIT IV Year',
            regNo: '23B91A0706',
            isCR: true,
            searchableAliases: ['ch sai vikas', 'sai vikas', 'vikas'],
            description: 'CH Sai Vikas is Class Representative for CSIT IV Year (Reg: 23B91A0706).',
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

    // =========================================================================
    // STATIC BOOTSTRAP: ALL 625 MYSQL STUDENTS + ALL 25 FACULTY
    // Pre-loaded at startup so COMPLETE-LIST queries work without HTTP sync.
    // Auto-generated 2026-08-13T06:20:03.184Z
    // Students: 625 | Faculty: 25
    // FORMAT: [regNo, name, branch, year, section, house, firstName, lastName]
    // =========================================================================
    (function _bootstrapAllPeople() {
        const _S=[['25B91A6201','ABDUL SHARIFUNNISA','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'abdul','sharifunnisa'],['25B91A6202','ADABALA GANGA PRAVEEN KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'adabala','kumar'],['25B91A6203','ADDAGARLA LAKSHMI DEVI','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'addagarla','devi'],['25B91A6204','BAGGU MOHITH KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'baggu','kumar'],['25B91A6205','BELAMARA SIVANI','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'belamara','sivani'],['25B91A6206','BELLAMKONDA JOSHITHA SHANMUKHI','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'bellamkonda','shanmukhi'],['25B91A6207','BODASINGI SHANMUKHA SAI','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'bodasingi','sai'],['25B91A6208','BOKKA LIKHITHA','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'bokka','likhitha'],['25B91A6209','CHALAMALASETTI SAI DURGA','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'chalamalasetti','durga'],['25B91A6210','DAIDA RANI','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'daida','rani'],['25B91A6211','DOMMETI SAI NIKHITHA','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'dommeti','nikhitha'],['25B91A6212','DONGA CHANDINI','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'donga','chandini'],['25B91A6213','DONGA MADHURI','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'donga','madhuri'],['25B91A6214','DUVVADA VINAY','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'duvvada','vinay'],['25B91A6215','GADAMSETTY VENKATA SAI HARISH','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'gadamsetty','harish'],['25B91A6216','GANDRETI KALYANI','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'gandreti','kalyani'],['25B91A6217','GAYAKAWADA PALLAVI','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'gayakawada','pallavi'],['25B91A6218','GHANTASALA DEEVEN KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'ghantasala','kumar'],['25B91A6219','GOWTHU LEELA RUKMINI','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'gowthu','rukmini'],['25B91A6220','GUDDETI DATHRI SRI SAI ANVITHA','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'guddeti','anvitha'],['25B91A6221','GUNDUMOGULA SARUPYA','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'gundumogula','sarupya'],['25B91A6222','JAKKAMPUDI REVANTH','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'jakkampudi','revanth'],['25B91A6223','JAVVADI MOHANA DURGA','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'javvadi','durga'],['25B91A6224','JOGI ABISHAI','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'jogi','abishai'],['25B91A6225','JOGI PRASANTH KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'jogi','kumar'],['25B91A6226','KANCHARLA N V L DURGA NIHARIKA','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'kancharla','niharika'],['25B91A6227','KAROTHI SAI MANIKANTA','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'karothi','manikanta'],['25B91A6228','KATREDDI BHANU TEJA SRI','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'katreddi','sri'],['25B91A6229','KETHINEDI SRI RAM','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'kethinedi','ram'],['25B91A6230','KODE NARASIMHA NAIDU','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'kode','naidu'],['25B91A6231','KOMMULA DIVYA MANOGNA','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'kommula','manogna'],['25B91A6232','KORANGI TRINADH','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'korangi','trinadh'],['25B91A6233','KOREDLA MEDHO SAI ASESH','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'koredla','asesh'],['25B91A6234','KOSETTI AHARON KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'kosetti','kumar'],['25B91A6235','KOTHAPALLI CHINMAY SATYA KRISHNA','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'kothapalli','krishna'],['25B91A6236','KUCHIMANCHI PRANAV','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'kuchimanchi','pranav'],['25B91A6237','KUMMARAPURUGU SAIRAM','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'kummarapurugu','sairam'],['25B91A6238','MAKKA SAI GOWR','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'makka','gowr'],['25B91A6239','MANDELA MUKUNDA PADMA PRIYA','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'mandela','priya'],['25B91A6240','MEDIDI BENNYBABU','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'medidi','bennybabu'],['25B91A6241','MEESALA RAJANIKUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'meesala','rajanikumar'],['25B91A6242','MUNGARA LOHITH','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'mungara','lohith'],['25B91A6243','MUNGARA LOKESH KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'mungara','kumar'],['25B91A6244','MUPPIDI AMAR DATTA REDDY','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'muppidi','reddy'],['25B91A6245','NANDE D V V SIVA SWAMY ARAVINDH','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'nande','aravindh'],['25B91A6246','PECHETTI LAKSHMI TANUJA','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'pechetti','tanuja'],['25B91A6247','PERICHERLA ROHAN KRISHNA VARMA','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'pericherla','varma'],['25B91A6248','RAJ KAMALINI MEENAKSHI BALABHADRA','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'raj','balabhadra'],['25B91A6249','RAMISETTY SANHITHA SRI','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'ramisetty','sri'],['25B91A6250','RODDA VENKATA SIVA SAI','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'rodda','sai'],['25B91A6251','SAMBANGI VENKATA JASWANTH','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'sambangi','jaswanth'],['25B91A6252','SANDHI SHAMM ROY','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'sandhi','roy'],['25B91A6253','SHAIK AFZAL DANISH','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'shaik','danish'],['25B91A6254','SUNKARA CHAITANYA VEERA BHAIRAV','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'sunkara','bhairav'],['25B91A6255','SUNKARA KETHAN SAI','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'sunkara','sai'],['25B91A6256','SUNKARA SWATHI','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'sunkara','swathi'],['25B91A6257','SUTHAPALLI SRI PAVAN KRISHNA','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'suthapalli','krishna'],['25B91A6258','SWAMYREDDY SAI DURGA SAGAR','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'swamyreddy','sagar'],['25B91A6259','TANINKI SREEDHAR','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'taninki','sreedhar'],['25B91A6260','THOTA DEVI SRI SAI SREEKAR','CSD','2nd Year','CSD 2nd Year Sec A',"AAKASH",'thota','sreekar'],['25B91A6261','VADDIMUKKALA KRANTHI KUMAR','CSD','2nd Year','CSD 2nd Year Sec A',"AGNI",'vaddimukkala','kumar'],['25B91A6262','VAKAPALLI PHANI SAI MUKESH','CSD','2nd Year','CSD 2nd Year Sec A',"JAL",'vakapalli','mukesh'],['25B91A6263','VASA HARI NAGENDRA PRATAP','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'vasa','pratap'],['25B91A6264','VEERAVARAPU NAGA VENKATA JASWANTH','CSD','2nd Year','CSD 2nd Year Sec A',"PRUDHVI",'veeravarapu','jaswanth'],['25B91A6265','YIRRI BHANU NAGA PRAKASH','CSD','2nd Year','CSD 2nd Year Sec A',"VAYU",'yirri','prakash'],['24B91A6201','ALLURI BHUVAN SAI TEJA MANI VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'alluri','varma'],['24B91A6202','ASILETI JAHNAVI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'asileti','jahnavi'],['24B91A6203','BOGA NISHANTH','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'boga','nishanth'],['24B91A6204','BOKINALA MANJUSHA','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'bokinala','manjusha'],['24B91A6205','BOMMI VENKATA SAI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'bommi','sai'],['24B91A6206','BONAM ADI LAKSHAMMA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'bonam','lakshamma'],['24B91A6207','BURRA MANI CHANDU KUTA RAO','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'burra','rao'],['24B91A6208','CHINNAM LAKSHMI SANTHOSHI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'chinnam','santhoshi'],['24B91A6209','CHINNAM NIKHILESH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'chinnam','nikhilesh'],['24B91A6210','CHOKKA ARYAN SANTHOSH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'chokka','santhosh'],['24B91A6211','DIRISIMILLI MAHI AVINASH','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'dirisimilli','avinash'],['24B91A6212','DODDIPATLA DANA VENKATA SIVASANKAR','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'doddipatla','sivasankar'],['24B91A6213','EDA PRASANTH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'eda','prasanth'],['24B91A6214','EDIMUDI SURIBABU','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'edimudi','suribabu'],['24B91A6215','EVANA CHANDU VENKATA SAI GANESH','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'evana','ganesh'],['25B95A6201','GEDDAM NIHAR','CSD','3rd Year','CSD 3rd Year Sec A',null,'geddam','nihar'],['24B91A6216','GUNTAMUKKALA SHAILESH','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'guntamukkala','shailesh'],['24B91A6217','GURRAM VIKAS','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'gurram','vikas'],['24B91A6218','GUTTULA CHAITANYA AKSHAY','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'guttula','akshay'],['24B91A6219','JAKKAMPUDI JAHNAVI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'jakkampudi','jahnavi'],['25B95A6202','JOGA GOWRI DEEPIKA','CSD','3rd Year','CSD 3rd Year Sec A',null,'joga','deepika'],['24B91A6220','KACHETTI RUCHITA LAKSHMI','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'kachetti','lakshmi'],['24B91A6221','KADALI BHANU','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'kadali','bhanu'],['25B95A6203','KAMUJU RAMYADEEPTHI','CSD','3rd Year','CSD 3rd Year Sec A',null,'kamuju','ramyadeepthi'],['24B91A6222','KILLADA DAVID ENOSH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'killada','enosh'],['24B91A6223','KOLLATI SAGAR','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'kollati','sagar'],['24B91A6224','KOPPARTHI DURGA BHAVANI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'kopparthi','bhavani'],['24B91A6225','KUNCHE SRI NAGA GANESH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'kunche','ganesh'],['24B91A6226','KUTIKUPPALA CHARAN TEJA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'kutikuppala','teja'],['24B91A6227','LALITHA MANOJNA VELIVELA','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'lalitha','velivela'],['24B91A6228','MALLABATTULA SIVA KRISHNA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'mallabattula','krishna'],['25B95A6204','MAMIDIPALLI NAGA VIGNESH','CSD','3rd Year','CSD 3rd Year Sec A',null,'mamidipalli','vignesh'],['25B95A6205','MERLA ASRITHA RAM','CSD','3rd Year','CSD 3rd Year Sec A',null,'merla','ram'],['24B91A6229','MOHAMMAD ROOFIYA TASNEEM','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'mohammad','tasneem'],['24B91A6230','MORTHA ANUSRI','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'mortha','anusri'],['25B95A6206','MULLU SRINU','CSD','3rd Year','CSD 3rd Year Sec A',null,'mullu','srinu'],['24B91A6231','MUNDRI RAKESH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'mundri','rakesh'],['24B91A6232','NADIMPALLI BABAJI AMRUTHA VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'nadimpalli','varma'],['24B91A6233','NAGISETTY VISHNUVARDHAN','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'nagisetty','vishnuvardhan'],['24B91A6234','NALLA TANOJ SITHARAM','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'nalla','sitharam'],['24B91A6235','NAMUDURI MAHESH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'namuduri','mahesh'],['24B91A6236','NANDIKA LIKHITHA','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'nandika','likhitha'],['24B91A6237','NANDURI SURYA NAGA VENKATA SAI VIGNESH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'nanduri','vignesh'],['24B91A6238','NARISETTY AKSHAYA NAIDU','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'narisetty','naidu'],['24B91A6239','NELAPOGULA SRI POSI LAKSHMI','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'nelapogula','lakshmi'],['24B91A6240','PABBINEEDI SRI RAMA SATYA MAHESH','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'pabbineedi','mahesh'],['24B91A6241','PADAVALA GANIF RAJU','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'padavala','raju'],['24B91A6242','PAIDI TANUJA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'paidi','tanuja'],['24B91A6243','PALIVELA BALA BHASKARA PRADEEP','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'palivela','pradeep'],['24B91A6244','PANKAJ NARAYAN TYADA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'pankaj','tyada'],['24B91A6245','PENAPOTHU JOHARIKA','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'penapothu','joharika'],['24B91A6246','PENMETSA PUJITH NAGA SANJAY VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'penmetsa','varma'],['25B95A6207','PEPETI THARUN','CSD','3rd Year','CSD 3rd Year Sec A',null,'pepeti','tharun'],['24B91A6247','PERICHERLA VIGNESH VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'pericherla','varma'],['24B91A6248','PERURI V V S L VINAY','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'peruri','vinay'],['24B91A6249','PILLI MEGHANA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'pilli','meghana'],['24B91A6250','PONNALA VAISHNAVI PRIYADARSHINI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'ponnala','priyadarshini'],['24B91A6251','POTHINEEDI TEJA NAGA VENKATA SAI PAVAN','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'pothineedi','pavan'],['25B95A6208','PULAVARTHI MOHANA MADHU LASYA SRI','CSD','3rd Year','CSD 3rd Year Sec A',null,'pulavarthi','sri'],['24B91A6252','PULIDINDI BLOOMY CHRIS ANGEL','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'pulidindi','angel'],['24B91A6253','SALUMURI JYOTHI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'salumuri','jyothi'],['24B91A6254','SANKU VEERA VENKATA SANTOSH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'sanku','santosh'],['24B91A6255','SAYED AMEENA FIRDOUS','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'sayed','firdous'],['24B91A6256','SHAIK SANIYA BEGUM','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'shaik','begum'],['24B91A6257','SIDDA MAHESH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'sidda','mahesh'],['24B91A6258','SINGAMSETTI SAI SHANKAR','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'singamsetti','shankar'],['25B95A6209','TELUKULA SAGAR','CSD','3rd Year','CSD 3rd Year Sec A',null,'telukula','sagar'],['23B91A6262','THIRUMALARAJU VENKATA SATYA PAVAN RAJU','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'thirumalaraju','raju'],['24B91A6259','VASIMTHA SATYA SAI KALYANI MALLAPAREDY','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'vasimtha','mallaparedy'],['24B91A6260','VEERAVALLI LEELA NAGA BABU','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'veeravalli','babu'],['24B91A6261','VUNNAM RAVINDRA BABU','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'vunnam','babu'],['25B95A6210','YALAKAPATI SURESH','CSD','3rd Year','CSD 3rd Year Sec A',null,'yalakapati','suresh'],['24B91A6262','YENUGAPALLI DIVYA MADHURI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'yenugapalli','madhuri'],['23B91A6201','ADDAGARLA SRI VIDYA SAGAR','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'addagarla','sagar'],['23B91A6202','AKSHINTALA HARSHATH','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'akshintala','harshath'],['23B91A6203','BANDARU BHANU SATYA PRAKASH','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'bandaru','prakash'],['23B91A6204','BODDETI DEVI NAGA VENKATA SAI DEEPAK','CSD','4th Year','CSD 4th Year Sec A',"JAL",'boddeti','deepak'],['23B91A6206','BOLISETTY KEDARESWARI','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'bolisetty','kedareswari'],['24B95A6201','BOMMIDI JAHNAVI','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'bommidi','jahnavi'],['23B91A6207','BORRA TERESSA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'borra','teressa'],['23B91A6208','CHAGANTI DHANESH KUMAR','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'chaganti','kumar'],['23B91A6209','CHELLABOYINA YAMINI','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'chellaboyina','yamini'],['23B91A6210','CHINDADA JYOTHI','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'chindada','jyothi'],['23B91A6211','CHINTADA NISSY SUDEEPTHI','CSD','4th Year','CSD 4th Year Sec A',"JAL",'chintada','sudeepthi'],['23B91A6212','CHINTAPALLI NAGA SYAMALA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'chintapalli','syamala'],['23B91A6213','CHINTAPALLI PREM TEJA','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'chintapalli','teja'],['23B91A6214','CHODAGAM SHANMUKHA SIVA SRI VENKAT','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'chodagam','venkat'],['23B91A6215','DAGGU ROHITH SUBRAHMANYA SAI','CSD','4th Year','CSD 4th Year Sec A',"JAL",'daggu','sai'],['23B91A6216','DODDI NIVEDITHA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'doddi','niveditha'],['23B91A6217','DODDIPATLA POOJA SAI PRAVEENA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'doddipatla','praveena'],['24B95A6202','DONGA JHANSI','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'donga','jhansi'],['23B91A6218','DONTHU VIJAYA SRI','CSD','4th Year','CSD 4th Year Sec A',"JAL",'donthu','sri'],['23B91A6219','GADDAM MANOJ KUMAR','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'gaddam','kumar'],['23B91A6220','GANDROJU ESWAR SRI KALI KRISHNA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'gandroju','krishna'],['23B91A6221','GANTA HARSHINI','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'ganta','harshini'],['23B91A6222','GEDDAM JACINTHA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'geddam','jacintha'],['23B91A6223','GUBBALA RESHMA GANGAVATHI','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'gubbala','gangavathi'],['23B91A6224','GUMMALLA NAGA GAYATHRI','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'gummalla','gayathri'],['23B91A6225','INDIGIMELLI RESHMA SUDEEPA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'indigimelli','sudeepa'],['23B91A6226','JILLELA VINAY','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'jillela','vinay'],['23B91A6227','JONNALAGADDA LAKSHMI MOUNIKA','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'jonnalagadda','mounika'],['23B91A6228','KALIGITA SIDDHU','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'kaligita','siddhu'],['23B91A6229','KARATAM SANTHOSH KUMAR','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'karatam','kumar'],['23B91A6230','KARIMERAKA DOLLY GANYA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'karimeraka','ganya'],['23B91A6231','KARRI REVANTH RATAN REDDY','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'karri','reddy'],['23B91A6232','KARUMANCHI SUNEEL','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'karumanchi','suneel'],['23B91A6233','KOLLATI SAILAJA','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'kollati','sailaja'],['23B91A6234','KOLLEPARA PREM','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'kollepara','prem'],['24B95A6203','KOMARADA KIRAN KISHORE','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'komarada','kishore'],['23B91A6235','KUKKALA SUDHEERA','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'kukkala','sudheera'],['23B91A6236','KUSAMPUDI VENKATA SATYA SAI TEJAS VARMA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'kusampudi','varma'],['23B91A6237','MADABHUSHI SRI RANGA SUDARSAN','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'madabhushi','sudarsan'],['23B91A7237','MADABHUSHI SRI RANGA SUDARSAN','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'madabhushi','sudarsan'],['23B91A6238','MAMIDISETTI VASUDHA BHANU','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'mamidisetti','bhanu'],['23B91A6239','MANCHALA SHANMUKA LAKSHMI DEEPIKA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'manchala','deepika'],['23B91A6240','MANDA TANMAY VENKATA SAI LALA GUPTA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'manda','gupta'],['23B91A6241','MANGENA SAI VENKATA VENU GOPALA CHARAN','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'mangena','charan'],['23B91A6242','MATTAPARTHI REETHIKA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'mattaparthi','reethika'],['23B91A6243','MEESALA KARTHIK RAJ KUMAR','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'meesala','kumar'],['23B91A6244','MOHAMMAD IBRAHIM KHAN','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'mohammad','khan'],['23B91A6245','MUCHU MAHADEV','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'muchu','mahadev'],['23B91A6246','MURALA NEETHI SURYA','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'murala','surya'],['24B95A6204','NADIKUPPALA THANUSH','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'nadikuppala','thanush'],['23B91A6247','NAKKA SUNISCHAL','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'nakka','sunischal'],['23B91A6248','NOUPADA LIKHITHA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'noupada','likhitha'],['23B91A6249','NUKALA CHARAN JASWANTH','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'nukala','jaswanth'],['23B91A6250','NUKALA KAUSHAL','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'nukala','kaushal'],['23B91A6251','NUKALA NAGA HARSHINI','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'nukala','harshini'],['23B91A6252','PABOLU SAI HARSHA','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'pabolu','harsha'],['23B91A6253','PAREPALLI RAMA HARI NAIDU','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'parepalli','naidu'],['24B95A6205','PENTAKOTA LEELA SRI','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'pentakota','sri'],['23B91A6254','PERICHARLA HEMA ASWANI','CSD','4th Year','CSD 4th Year Sec A',"VAYU",'pericharla','aswani'],['23B91A6255','POLIMERA SWAPNA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'polimera','swapna'],['24B95A6206','PONAMANDI PRASHANTH','CSD','4th Year','CSD 4th Year Sec A',"AGNI",'ponamandi','prashanth'],['23B91A6256','RELLU LAKSHMI PRASANNA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'rellu','prasanna'],['23B91A6257','RUDRAKSHULA PRAVEENA','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'rudrakshula','praveena'],['23B91A6258','SARELLA VINCY ANGELINE','CSD','4th Year','CSD 4th Year Sec A',"JAL",'sarella','angeline'],['23B91A6259','SHAIK ILIYAS','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'shaik','iliyas'],['23B91A6260','SURARAPU HASINI','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'surarapu','hasini'],['23B91A6261','SYED MANSOOR','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'syed','mansoor'],['24B95A6207','TANUKULA UMA SAI PAVAN','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'tanukula','pavan'],['24B95A6208','THOTA MOHAN SIVA','CSD','4th Year','CSD 4th Year Sec A',"AAKASH",'thota','siva'],['23B91A6263','YALLA CHANDANA','CSD','4th Year','CSD 4th Year Sec A',"JAL",'yalla','chandana'],['23B91A6264','YARAMALA MOHAN BHAGAVAN NARASIMHA','CSD','4th Year','CSD 4th Year Sec A',"PRUDHVI",'yaramala','narasimha'],['23B95A6201','ANDE NAGA SATYA SAI VAMSI KIRAN','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'ande','kiran'],['22B91A6201','ARNEPALLI MEGANA','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'arnepalli','megana'],['22B91A6202','BAYYE JOSEPH KUMAR','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'bayye','kumar'],['22B91A6203','BHAVANAM LAKSHMAN KUMAR REDDY','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'bhavanam','reddy'],['22B91A6204','BORRA AVINASH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'borra','avinash'],['22B91A6205','BORRA HIMA SRI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'borra','sri'],['22B91A6206','BUDDE VENKATA SATYA TEJESH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'budde','tejesh'],['22B91A6207','CHIKILE RAJESH','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'chikile','rajesh'],['22B91A6208','CHILAKALAPUDI ABHI RAAMA PHANINDRA','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'chilakalapudi','phanindra'],['22B91A6209','CHIMAKURTHI TEJA RUPAK','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'chimakurthi','rupak'],['22B91A6210','DAKKUMALLA VARSHA','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'dakkumalla','varsha'],['22B91A6211','DONAVALLI REVATHI','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'donavalli','revathi'],['21B91A6216','G UDAY KIRAN','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'g','kiran'],['22B91A6212','GEDELA SAI ABHINAY','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'gedela','abhinay'],['22B91A6213','GOTTUMUKKALA BHARGAVI','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'gottumukkala','bhargavi'],['23B95A6202','GUTTULA TEJASWI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'guttula','tejaswi'],['22B91A6214','INUMARTHI SRINAVYA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'inumarthi','srinavya'],['22B91A6215','JADDU JYOTHIRMAI INDIRA PRIYADARSINI DEVI','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'jaddu','devi'],['22B91A6216','JAKKAMSETTI SANJANI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'jakkamsetti','sanjani'],['22B91A6217','JOGI PAVAN TEJA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'jogi','teja'],['22B91A6218','KAMBHAMPATI SHALANI SINDHU SRI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'kambhampati','sri'],['22B91A6219','KANUMURI RISHITHA VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'kanumuri','varma'],['22B91A6220','KAPUDASI SNIGDHA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'kapudasi','snigdha'],['22B91A6221','KARUMURI TEJA SIDDARDHA PAVAN KUMAR','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'karumuri','kumar'],['23B95A6203','KELLA CHAKRA VAMSI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'kella','vamsi'],['22B91A6222','KETHA SURYA PRAKASH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'ketha','prakash'],['22B91A6223','KOLA YESWANTH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'kola','yeswanth'],['22B91A6224','KOLATI STEPHEN SOUDH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'kolati','soudh'],['22B91A6225','KOLLABATHULA SHYAM BABU','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'kollabathula','babu'],['22B91A6226','KOLLATI VISHNU TEJA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'kollati','teja'],['22B91A6227','KOPPARTI HONEY NAGA SANDEEP','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'kopparti','sandeep'],['22B91A6228','LAKSHMI VENKATA NIKHITHA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'lakshmi','nikhitha'],['22B91A6229','MADDI AKSHAYA SRI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'maddi','sri'],['23B95A6204','MADDULA AAKASH NAGENDRA SAI PAVAN','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'maddula','pavan'],['22B91A6230','MANDANGI MOUNIKA','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'mandangi','mounika'],['22B91A6231','MANGENA JAHNAVI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'mangena','jahnavi'],['22B91A6232','MANGINETI MOHAN SATYA SIVA ROHITH KUMAR','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'mangineti','kumar'],['22B91A6233','MATTA BALA VEERRAJU','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'matta','veerraju'],['23B95A6205','MOHAMMAD SIKINDAR KHAN','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'mohammad','khan'],['22B91A6234','MOTURI SANDILYA','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'moturi','sandilya'],['22B91A6235','MUDUNURI MANOJ SAI ASWANTH VARMA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'mudunuri','varma'],['23B95A6206','NAKKINA GANESH','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'nakkina','ganesh'],['22B91A6236','NALLAM HEMA SAI SRI LAKSHMI','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'nallam','lakshmi'],['22B91A6237','PAILA NIKHIL','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'paila','nikhil'],['22B91A6238','PANAKALA RAMA NAGESWARA RAO','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'panakala','rao'],['22B91A6239','PEPETI GANESH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'pepeti','ganesh'],['22B91A6240','PERABATHULA SOMESWARA RAO','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'perabathula','rao'],['22B91A6241','PIPPALLA RUSHI GUNA SHANMUKH','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'pippalla','shanmukh'],['22B91A6242','POSIMSETTY SRI VISWA BHARATH','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'posimsetty','bharath'],['22B91A6243','POTHAMSETTI KODANDA RAMA NAGA GANESH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'pothamsetti','ganesh'],['22B91A6244','POTTURI GAYATRI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'potturi','gayatri'],['22B91A6245','PULI DURGA BHAVANI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'puli','bhavani'],['22B91A6246','PULLURU KRISHNA VAMSI','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'pulluru','vamsi'],['22B91A6247','PUTHINIDI JNANESWARI','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'puthinidi','jnaneswari'],['22B91A6248','RAAVI CHARWAK','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'raavi','charwak'],['22B91A6249','SETTI NARENDRA KUMAR','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'setti','kumar'],['22B91A6250','SHAIK AHMED','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'shaik','ahmed'],['22B91A6251','SHAIK KARIMUNNISA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'shaik','karimunnisa'],['23B95A6207','TANGUTURI S V NAGA PAVAN SAI','CSD','3rd Year','CSD 3rd Year Sec A',"AGNI",'tanguturi','sai'],['22B91A6252','TELLAKULA VEERA RAGHAVA','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'tellakula','raghava'],['23B95A6208','THOTA SUJAY BABU','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'thota','babu'],['22B91A6253','UNDAPALLI DIVYA','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'undapalli','divya'],['22B91A6254','UNDURTHI MANOJ','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'undurthi','manoj'],['22B91A6255','VAKAPALLI H V SAI SURYA SWAPANTH','CSD','3rd Year','CSD 3rd Year Sec A',"JAL",'vakapalli','swapanth'],['22B91A6256','VATAPALLI GNANA SEKHAR','CSD','3rd Year','CSD 3rd Year Sec A',"PRUDHVI",'vatapalli','sekhar'],['22B91A6257','VEERAVALLI SATYA VENKATA SRINADH','CSD','3rd Year','CSD 3rd Year Sec A',"VAYU",'veeravalli','srinadh'],['22B91A6259','VILLURI MOHINI MANGA LAKSHMI MANASA','CSD','3rd Year','CSD 3rd Year Sec A',"AAKASH",'villuri','manasa'],['25B91A0701','ADABALA ROHITH VEERA VENKATA DURGESH','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'adabala','durgesh'],['25B91A0702','ADABALA SAI NAGA SURYANARAYANA','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'adabala','suryanarayana'],['25B91A0703','ADINA VENKATA SURYA SAI VISHAL','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'adina','vishal'],['25B91A0704','AKULA BALA BHAGYA SRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'akula','sri'],['25B91A0705','ALAPATI ANASUYA DEVI','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'alapati','devi'],['25B91A0706','ARETI JAYA CHARAN KRISHNA','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'areti','krishna'],['25B91A0707','BARAMA NAVYA NAGA RAMYA SRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'barama','sri'],['25B91A0708','BAREPU VAMSI','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'barepu','vamsi'],['25B91A0709','BEERA YASMIN','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'beera','yasmin'],['25B91A0710','BEJAVADA V S S N RAMA GANESH','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'bejavada','ganesh'],['25B91A0711','BELLAPU J S VENKATA DURGA NAGA ASRITHA','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'bellapu','asritha'],['25B91A0712','BILLAKURTHI HARSHA VARDHAN SRINIVASU','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'billakurthi','srinivasu'],['25B91A0713','BIRUDUKOTA SATYA VARA PRASAD','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'birudukota','prasad'],['25B91A0714','BONDA YOGESH','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'bonda','yogesh'],['25B91A0715','BONIGALA RISHITHA','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'bonigala','rishitha'],['25B91A0716','BOTCHA AVINASH','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'botcha','avinash'],['25B91A0717','BOYAPATI PRASANNA VARUN','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'boyapati','varun'],['25B91A0718','BUDIDA NAGA VAISHNAVI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'budida','vaishnavi'],['25B91A0719','CHADALAVADA SHAKEENA','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'chadalavada','shakeena'],['25B91A0720','CHATRAGADDA TEJASWINI','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'chatragadda','tejaswini'],['25B91A0721','CHEGONDI HARSHINI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'chegondi','harshini'],['25B91A0722','CHELAMKURI LOHITH','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'chelamkuri','lohith'],['25B91A0723','CHETTU BHAVANA','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'chettu','bhavana'],['25B91A0724','CHIKKALA SHYAM KISHORE','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'chikkala','kishore'],['25B91A0725','CHUNDRU VISWA TEJA','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'chundru','teja'],['25B91A0726','DASARI KARTHIKEYA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'dasari','karthikeya'],['25B91A0727','DASARI MOHAN CHANDRA SHEKAR','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'dasari','shekar'],['25B91A0728','DASARI YUVA RAM','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'dasari','ram'],['25B91A0729','DIRSIPOM INDHU PRIYA','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'dirsipom','priya'],['25B91A0730','DURVASULA SITA SRI VYSHNAVI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'durvasula','vyshnavi'],['25B91A0731','DWARAMPUDI PURNA NAGA GOWTHAM REDDY','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'dwarampudi','reddy'],['25B91A0732','GANDREDDY RAM GANESH','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'gandreddy','ganesh'],['25B91A0733','GEDA HARI SAI','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'geda','sai'],['25B91A0734','GOLLAPALLI ROHAN SAMIT','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'gollapalli','samit'],['25B91A0735','GUBBALA GNAANA PRASANNA','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'gubbala','prasanna'],['25B91A0736','GUDAPALLI VEENA SRUTHI','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'gudapalli','sruthi'],['25B91A0737','GUDAPATI LALITHA DEVI SRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'gudapati','sri'],['25B91A0738','GUDDALA SAI CHARAN','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'guddala','charan'],['25B91A0739','GUDDATI DURGA NAGA LAKSHMI SHIVA SARANYA','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'guddati','saranya'],['25B91A0740','GUDURI KARTHIK SRI NAGA SAI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'guduri','sai'],['25B91A0741','GUNDEPALLI SNEHITH','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'gundepalli','snehith'],['25B91A0742','JALDANI ABHIRAM CHARAN','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'jaldani','charan'],['25B91A0743','JALLI SURENDRA VARMA','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'jalli','varma'],['25B91A0744','JAVVADI NEHA','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'javvadi','neha'],['25B91A0745','JITHENDRA VENKATA KANAKA SRI SURYA AYITHAM','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'jithendra','ayitham'],['25B91A0746','KADALI SRI SURYA SATYA SAI','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'kadali','sai'],['25B91A0747','KALIDINDI SAI VARMA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'kalidindi','varma'],['25B91A0748','KAMIREDDY SRI RAMA CHARAN SARESH KUMAR','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'kamireddy','kumar'],['25B91A0749','KANDANALA PURNASRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'kandanala','purnasri'],['25B91A0750','KANNIPAMULA TEJASWI','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'kannipamula','tejaswi'],['25B91A0751','KARRI LAKSHMI SRAVANTHI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'karri','sravanthi'],['25B91A0752','KATARI HASWANTH SIVA BHASKAR','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'katari','bhaskar'],['25B91A0753','KATIKI RAJANI','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'katiki','rajani'],['25B91A0754','KATTA DILEEP','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'katta','dileep'],['25B91A0755','KETHA BHAVYASRI SAILAKSHMI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'ketha','sailakshmi'],['25B91A0756','KOLLA RAMA SAI','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'kolla','sai'],['25B91A0757','KORLAPATI GEETHIKA RATNAM','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'korlapati','ratnam'],['25B91A0758','KOTA DEEPIKA','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'kota','deepika'],['25B91A0759','KOTA MADHU VENKATESH','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'kota','venkatesh'],['25B91A0760','KOTAPATI MAHENDRA REDDY','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'kotapati','reddy'],['25B91A0761','KUKUNOORI POORNA SRI CHANDRA SEKHAR','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'kukunoori','sekhar'],['25B91A0762','LAKKU NOMU NARASIMHA SAI PAVAN','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'lakku','pavan'],['25B91A0763','LAKSHMISETTI KAVYA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'lakshmisetti','kavya'],['25B91A0764','MADDALA MANI NAGA SAI NARASIMHA TRINADH','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'maddala','trinadh'],['25B91A0765','MAMUDURI PRABHAS','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'mamuduri','prabhas'],['25B91A0766','MANDA RAJA PRASANNA KUMAR','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'manda','kumar'],['25B91A0767','MEER IKRAAM HUSSAIN','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'meer','hussain'],['25B91A0768','MEESALA JAYA RAM','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'meesala','ram'],['25B91A0769','MOHAMMAD NUMAAN RAZA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'mohammad','raza'],['25B91A0770','MOTUPALLI MEENA PHANI SRI','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'motupalli','sri'],['25B91A0771','MUCHARLA MANI VENKATA SATYANARAYANA','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'mucharla','satyanarayana'],['25B91A0772','MUGADA DURGA PRASAD','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'mugada','prasad'],['25B91A0773','MULE ADILAKSHMI','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'mule','adilakshmi'],['25B91A0774','MUTCHARLA YASASWI','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'mutcharla','yasaswi'],['25B91A0775','MYLABATHULA SUPRIYA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'mylabathula','supriya'],['25B91A0776','NALLAM MANOGNYA DEVI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'nallam','devi'],['25B91A0777','NARKEDAMILLI TANISHA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'narkedamilli','tanisha'],['25B91A0778','NELAPUDI PRASANTH SEKHAR','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'nelapudi','sekhar'],['25B91A0779','NEPALA BESWANTH','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'nepala','beswanth'],['25B91A0780','NODAGALA NANDA GOPAL SWAMY','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'nodagala','swamy'],['25B91A0781','PALA THANUJA','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'pala','thanuja'],['25B91A0782','PAMU AMRUTHA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'pamu','amrutha'],['25B91A0783','PANDAVA MEGHANA CHOUDHARY','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'pandava','choudhary'],['25B91A0784','PAVULURI SAI KRISHNA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'pavuluri','krishna'],['25B91A0785','PENMATSA SAI SATHWIKA','CSIT','2nd Year','CSIT 2nd Year Sec B',"VAYU",'penmatsa','sathwika'],['25B91A0786','PENTAPATI HARSHA VARDHAN RAJU','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'pentapati','raju'],['25B91A0787','PENUGONDA ENMANUYEL','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'penugonda','enmanuyel'],['25B91A0788','PINNINTI SIVANI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'pinninti','sivani'],['25B91A0789','PIPPALLA MADHURI VENKATA NAGA DIVYA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'pippalla','divya'],['25B91A0790','PODAGATLA PRASANTH','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'podagatla','prasanth'],['25B91A0791','POGIRI BHANU PRASAD','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'pogiri','prasad'],['25B91A0792','PUPPALA JANARDHAN SAI','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'puppala','sai'],['25B91A0793','RAJA AKASH','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'raja','akash'],['25B91A0794','RANGISETTI HEMA SAHASRA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'rangisetti','sahasra'],['25B91A0795','REDDI GEETHIKA','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'reddi','geethika'],['25B91A0796','REDDY SRIJA','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'reddy','srija'],['25B91A0797','REDDY VENKATA SAKETH','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'reddy','saketh'],['25B91A0798','REKHAPALLI RUTHIKA AKSHAYA SAI SRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'rekhapalli','sri'],['25B91A0799','RELANGI JYOTHSNA SRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'relangi','sri'],['25B91A07A0','SAKHIMSETTI HARI SATYA PRIYA DEVI','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'sakhimsetti','devi'],['25B91A07A1','SAMAYAMANTHULA SRIVYSHNAVI ISWARYA LAKSHMI','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'samayamanthula','lakshmi'],['25B91A07A2','SATTINENI NIHITHA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'sattineni','nihitha'],['25B91A07A3','SAVARAM VENKATA SATYA NAGA DURGA SUBHASH','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'savaram','subhash'],['25B91A07A4','SHAIK DADA KHALANDER','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'shaik','khalander'],['25B91A07A5','SHAIK SAMEERA','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'shaik','sameera'],['25B91A07A6','SHAIK SUHANA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'shaik','suhana'],['25B91A07A7','SIRIPURAPU PARDHA SARADHI','CSIT','2nd Year','CSIT 2nd Year Sec B',"AGNI",'siripurapu','saradhi'],['25B91A07A8','SISTU SNEHA','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'sistu','sneha'],['25B91A07A9','SRIKAKULAPU SANTHI PRIYA','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'srikakulapu','priya'],['25B91A07B0','SUNKARA LOKESH VIJAY SAI','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'sunkara','sai'],['25B91A07B1','TADELA SUSMITHA','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'tadela','susmitha'],['25B91A07B2','TAMARANA SRUTHI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'tamarana','sruthi'],['25B91A07B3','TAPPETA GANESH REDDY','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'tappeta','reddy'],['25B91A07B4','TEKU DURGA SRINIVAS','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'teku','srinivas'],['25B91A07B5','TELU YUVA PRIYA MOULIKA','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'telu','moulika'],['25B91A07B6','UNGARALA RADHIKA AISHWARYA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'ungarala','aishwarya'],['25B91A07B7','UPPULURI VENKATA JASWANTH','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'uppuluri','jaswanth'],['25B91A07B8','UTTARILLI HARSHA VARDHAN','CSIT','2nd Year','CSIT 2nd Year Sec B',"JAL",'uttarilli','vardhan'],['25B91A07B9','VANUKURI SAI BHARADWAJA REDDY','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'vanukuri','reddy'],['25B91A07C0','VARADA NAGA SURYA LAKSHMI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'varada','lakshmi'],['25B91A07C1','VARIKUTI ANJALI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'varikuti','anjali'],['25B91A07C2','VARRE GEETHA NAGA VALLI','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'varre','valli'],['25B91A07C3','VEERAMALLA NAGAVALLI GANGOTHRI','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'veeramalla','gangothri'],['25B91A07C4','VEERAVALLI KUNDANA SAI SANTHI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'veeravalli','santhi'],['25B91A07C5','VEERLAPATI HARSHINI','CSIT','2nd Year','CSIT 2nd Year Sec A',"PRUDHVI",'veerlapati','harshini'],['25B91A07C6','VEERLAPATI HASINI','CSIT','2nd Year','CSIT 2nd Year Sec A',"AGNI",'veerlapati','hasini'],['25B91A07C7','VEMAVARAPU MADHU SARIKA','CSIT','2nd Year','CSIT 2nd Year Sec A',"JAL",'vemavarapu','sarika'],['25B91A07C8','VENNAPUSA MANISHA','CSIT','2nd Year','CSIT 2nd Year Sec B',"PRUDHVI",'vennapusa','manisha'],['25B91A07C9','VISSAPRAGADA RAMA PRANEETH','CSIT','2nd Year','CSIT 2nd Year Sec A',"AAKASH",'vissapragada','praneeth'],['25B91A07D0','VOONNA HEMANTH','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'voonna','hemanth'],['25B91A07D1','YALLA PRADEEP KUMAR','CSIT','2nd Year','CSIT 2nd Year Sec A',"VAYU",'yalla','kumar'],['25B91A07D2','YERICHERLA JOHN ELISHA','CSIT','2nd Year','CSIT 2nd Year Sec B',"AAKASH",'yericherla','elisha'],['24B91A0701','A PREETHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'a','preethi'],['24B91A0702','ACHANTA MOKSHITH CHOWDARY','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'achanta','chowdary'],['24B91A0703','ADDAGARLA HEMANTH NAGA MANIKANTA','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'addagarla','manikanta'],['24B91A0704','ADDAGARLA R S S K V V S D N RAJESH','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'addagarla','rajesh'],['24B91A0705','ALLADI DILEEP KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'alladi','kumar'],['24B91A0706','ATCHUTHUNI SAI SPURANTHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'atchuthuni','spuranthi'],['24B91A0707','BANAVATHU MALLIKARJUNA SAI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'banavathu','sai'],['24B91A0708','BANDE DALI AKSHAYA','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'bande','akshaya'],['24B91A0709','BANDI HARI KRISHNA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'bandi','krishna'],['24B91A0710','BASIVIREDDY HEMALATHA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'basivireddy','hemalatha'],['24B91A0711','BHOGIREDDY TEJASRI SAI VAISHNAVI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'bhogireddy','vaishnavi'],['24B91A0712','BODDETI SARVANI','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'boddeti','sarvani'],['25B95A0701','BOLEM PRAVALIKA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'bolem','pravalika'],['24B91A0713','BOPPINEEDI GEETHIKA','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'boppineedi','geethika'],['24B91A0714','BUDDIGA GAYATRI','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'buddiga','gayatri'],['24B91A0715','BUDITHI SAI ADARSH','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'budithi','adarsh'],['24B91A0716','CHALLA JITHENDRA ABHIRAM','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'challa','abhiram'],['24B91A0717','CHALLAGUNDLA HINDRIKA SRI','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'challagundla','sri'],['24B91A0718','CHAMARLAKOTA SIREESH VALI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'chamarlakota','vali'],['24B91A0719','CHANDAKA KEDARA SRINIVAS','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'chandaka','srinivas'],['24B91A0720','CHANDANI VIVEKANANDA','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'chandani','vivekananda'],['25B95A0702','CHEYYETI VENKATA SINDHU','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'cheyyeti','sindhu'],['24B91A0721','CHINTAPALLI VENKATA DURGESH','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'chintapalli','durgesh'],['24B91A0722','CHITAKANA RACHITHA','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'chitakana','rachitha'],['24B91A0723','CHITTALA DILEEP RAM KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'chittala','kumar'],['24B91A0724','CHUNDRU GOWTHAM KRISHNA','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'chundru','krishna'],['24B91A0725','DACHEPALLI BHANU UDAY','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'dachepalli','uday'],['24B91A0726','DAMMU PRANEETH KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'dammu','kumar'],['24B91A0727','DEVADA SRI VENKATESWARA SWAMY','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'devada','swamy'],['24B91A0728','DHANANI SRI LAKSHMI VENKATA AASHRITA','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'dhanani','aashrita'],['25B95A0703','DONGA MAHESH','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'donga','mahesh'],['24B91A0729','ESURU CHAITANYA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'esuru','chaitanya'],['24B91A0730','EUDU HARSHA VARDHAN','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'eudu','vardhan'],['24B91A0731','GADDAMUDI VENKATA GOPICHAND','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'gaddamudi','gopichand'],['24B91A0732','GANDHAM MAHATHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'gandham','mahathi'],['25B95A0704','GANJI JYOTHSNA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'ganji','jyothsna'],['24B91A0733','GANTA GOWTHAM','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'ganta','gowtham'],['24B91A0734','GAYATRI PADHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'gayatri','padhi'],['24B91A0735','GHANTA LIKITHA VENKATA RAGHU SAI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'ghanta','sai'],['24B91A0736','GOPINEEDI DIVIJA','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'gopineedi','divija'],['24B91A0737','GUDIMETLA JNANA SANDEEP REDDY','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'gudimetla','reddy'],['24B91A0738','GUNDU TARUN SAI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'gundu','sai'],['24B91A0739','JADDU LEELA PAVAN KRISHNA','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'jaddu','krishna'],['24B91A0740','JANAKI MADDALA','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'janaki','maddala'],['24B91A0741','KANDIBOYINA CHANDRASHEKAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'kandiboyina','chandrashekar'],['24B91A0742','KANUMURI DEEKSHITA','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'kanumuri','deekshita'],['24B91A0743','KAPAKAYALA NAGA SAI PAVAN','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'kapakayala','pavan'],['24B91A0744','KARIBANDI PAVAN RAVINDRA KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'karibandi','kumar'],['24B91A0745','KATRAGADDA ARJUN NAIDU','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'katragadda','naidu'],['24B91A0746','KAVURU GUNA SRAVANI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'kavuru','sravani'],['24B91A0747','KAYITHA LAHARI','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'kayitha','lahari'],['24B91A0748','KESANAKURTHI MANASA SATYA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'kesanakurthi','satya'],['24B91A0749','KETA PURNA PAVAN','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'keta','pavan'],['24B91A0750','KODETI SATISH','CSIT','3rd Year','CSIT 3rd Year Sec A',"JAL",'kodeti','satish'],['24B91A0751','KODI HEMANTH KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'kodi','kumar'],['24B91A0752','KOLLI VINEEL','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'kolli','vineel'],['24B91A0753','KOMATI JAYASRI LAKSHMI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'komati','lakshmi'],['24B91A0754','KONDAPALLI SUBHAKAR BHANCY RAJ','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'kondapalli','raj'],['24B91A0755','KONKEY BINDHU VASANTHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'konkey','vasanthi'],['24B91A0756','KOTLA VENKAT','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'kotla','venkat'],['24B91A0757','KUSUMA KOMALI','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'kusuma','komali'],['24B91A0758','MADAMANCHI MANIKANTA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'madamanchi','manikanta'],['24B91A0759','MADDALA VARSHINI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'maddala','varshini'],['24B91A0760','MADUPALLI JNANESH','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'madupalli','jnanesh'],['24B91A0761','MALLA DEEPANVITHA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'malla','deepanvitha'],['24B91A0762','MALLAVARAPU GANGOTHRI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'mallavarapu','gangothri'],['24B91A0763','MALLULA KAVERI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'mallula','kaveri'],['24B91A0764','MALLULA MADHU VARSHINI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AAKASH",'mallula','varshini'],['24B91A0765','MANDA KEERTHI','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'manda','keerthi'],['24B91A0766','MANDAGIRI SAI ASWITHA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'mandagiri','aswitha'],['24B91A0767','MANDAPATI VENKATA YAMINI','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'mandapati','yamini'],['24B91A0768','MANDAVA YAGNA AKHIL SAI','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'mandava','sai'],['24B91A0769','MANDAVALLI DHANA KARTHIKEYA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'mandavalli','karthikeya'],['24B91A0770','MARUBOINA KARTHIK VENKATA SRI SAI TEJA','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'maruboina','teja'],['24B91A0771','MEDABALIMI ADITHYA VARDHAN','CSIT','3rd Year','CSIT 3rd Year Sec A',"AGNI",'medabalimi','vardhan'],['24B91A0772','MEDIDI LALITH KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec A',"VAYU",'medidi','kumar'],['24B91A0773','MEDISETTI SRINIJA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'medisetti','srinija'],['24B91A0774','MULAGALA PRANATI SANDHYA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'mulagala','sandhya'],['24B91A0775','MURIKITHA ARCHANA SAI SRI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'murikitha','sri'],['25B95A0705','MUTHYALAPALLI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'muthyalapalli','muthyalapalli'],['24B91A0776','NALAMALA KEVIN RISHITH','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'nalamala','rishith'],['24B91A0777','NAMALA THANUSHA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'namala','thanusha'],['24B91A0778','NELLURI CHAITRIKA SRI NIDHI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'nelluri','nidhi'],['24B91A0779','NETHALA HEMA DURGA SAI KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'nethala','kumar'],['24B91A0780','NETHULA MAHESH','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'nethula','mahesh'],['24B91A0781','NIMMALA BHANU SRI HARSHA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'nimmala','harsha'],['24B91A0782','NIMMALA BHUVANA LAKSHMI','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'nimmala','lakshmi'],['25B95A0706','NIMMANA NARENDRA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'nimmana','narendra'],['24B91A0783','NULI LAKSHMI SAI LIKITH','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'nuli','likith'],['24B91A0784','OGURI LAKSHMI NARAYANA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'oguri','narayana'],['24B91A0785','PAKA RENITA JESSIE','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'paka','jessie'],['24B91A0786','PALANI BHUVANA SAI KRUTHI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'palani','kruthi'],['24B91A0787','PALAPARTHI SANTHOSH KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'palaparthi','kumar'],['24B91A0788','PALLAPU HARITHA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'pallapu','haritha'],['25B95A0707','PANDA SUJAN PRASAD','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'panda','prasad'],['24B91A0789','PANJA SOMARANGA SAI','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'panja','sai'],['24B91A0790','PARAVASTU VENKATA RAMA SURI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'paravastu','suri'],['25B95A0708','PATAN ABDUL RASHEED KHAN','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'patan','khan'],['24B91A0791','PENMETSA HARSHINI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'penmetsa','harshini'],['24B91A0792','PONNAGANTI JYOTHIKA SAI','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'ponnaganti','sai'],['24B91A0793','POTLA RAVI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'potla','ravi'],['24B91A0794','PULAPARTHI KALYAN VENKATA SAI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'pulaparthi','sai'],['24B91A0795','PULI MYTHILI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'puli','mythili'],['24B91A0796','PUVVALA SANJANA GAYATHRI','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'puvvala','gayathri'],['24B91A0797','RANGISETTI SAI PAVAN KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'rangisetti','kumar'],['25B95A0709','REBBA RAJESH','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'rebba','rajesh'],['24B91A0798','REDDEM LEELA MEGHANA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'reddem','meghana'],['24B91A0799','REDDY VENKATA SATYA SRAVANI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'reddy','sravani'],['24B91A07A0','ROMPILLI SATEESH','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'rompilli','sateesh'],['24B91A07A1','RONGALA SRINIVAS','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'rongala','srinivas'],['24B91A07A2','ROTTE SUSHANTH','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'rotte','sushanth'],['24B91A07A3','SAKHINETIPALLI CHAKRI ADITYA PAVAN KUMAR','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'sakhinetipalli','kumar'],['24B91A07A4','SAMUDRALA JESRAVAN MANIKANTA','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'samudrala','manikanta'],['24B91A07A5','SANA SHANMUKHA DURGA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'sana','durga'],['25B95A0710','SARIPALLI GNANESWAR','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'saripalli','gnaneswar'],['24B91A07A6','SEELABOYINA JEEVANA','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'seelaboyina','jeevana'],['24B91A07A7','SEELABOYINA JEEVIKA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'seelaboyina','jeevika'],['24B91A07A8','SHAIK ABDUL GAFOOR','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'shaik','gafoor'],['24B91A07A9','SHAIK AMEENA','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'shaik','ameena'],['24B91A07BO','Shaik madeena','CSIT','3rd Year','CSIT 3rd Year Sec A',"PRUDHVI",'shaik','madeena'],['24B91A07B0','SHAIK NAGUR MADEENA BEGAM','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'shaik','begam'],['24B91A07B1','SIDAGAM ABHIRAM','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'sidagam','abhiram'],['24B91A07B2','SIDDAMSETTI VIVEK SAI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'siddamsetti','sai'],['24B91A07B3','SIRRA DURGA RANI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'sirra','rani'],['24B91A07B4','SWARNA GOWTHAMI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'swarna','gowthami'],['24B91A07B5','SWARNA SAHITHI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'swarna','sahithi'],['24B91A07B6','TALARI JYOTHI','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'talari','jyothi'],['24B91A07B7','THOTA JOHAN BENEDICT','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'thota','benedict'],['24B91A07B8','TIRUMALASETTY SIDDARDHA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'tirumalasetty','siddardha'],['25B95A0711','TUMMA NAGA DURGA','CSIT','3rd Year','CSIT 3rd Year Sec B',null,'tumma','durga'],['25B95A0712','TUMMALAGUNTA SAHITHI LAKSHMI','CSIT','3rd Year','CSIT 3rd Year Sec B',null,'tummalagunta','lakshmi'],['25B95A0713','UNDRAJAVARAPU NAGA VENKATA RAGHU','CSIT','3rd Year','CSIT 3rd Year Sec B',null,'undrajavarapu','raghu'],['24B91A07B9','UPPALA ABHINAYA SREE','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'uppala','sree'],['24B91A07C0','VADREVU LAHARI DEVI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AGNI",'vadrevu','devi'],['24B91A07C1','VALAVALA RAMA LAKSHMI ANJANA','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'valavala','anjana'],['24B91A07C2','VANAPARTHI ASMITHA VYSHNAVI','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'vanaparthi','vyshnavi'],['24B91A07C3','VASE ASHITHA','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'vase','ashitha'],['24B91A07C4','VASKA JYOTHI','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'vaska','jyothi'],['24B91A07C5','VATHADI NAGAVINAY','CSIT','3rd Year','CSIT 3rd Year Sec B',"PRUDHVI",'vathadi','nagavinay'],['24B91A07C6','VATTIVELLA RAMKI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'vattivella','ramki'],['24B91A07C7','VENKATA NISHITHA REDDY DATLA','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'venkata','datla'],['24B91A07C8','YALLAPU TANUJA','CSIT','3rd Year','CSIT 3rd Year Sec B',"JAL",'yallapu','tanuja'],['24B91A07C9','YARLAGADDA TAMOGHNA','CSIT','3rd Year','CSIT 3rd Year Sec B',"VAYU",'yarlagadda','tamoghna'],['24B91A07D0','YENDA RASHMIKA','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'yenda','rashmika'],['24B91A07D1','YERRA YASVASI SATYA KAVERI','CSIT','3rd Year','CSIT 3rd Year Sec B',"AAKASH",'yerra','kaveri'],['24B95A0701','BANDARU MANOGNA NAGAVALLI','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'bandaru','nagavalli'],['23B91A0701','BARAKATA TARUN SWAMY','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'barakata','swamy'],['23B91A0702','BARRI SRAVYA SREE','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'barri','sree'],['23B91A0703','BEERA JNANENDRA VARMA','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'beera','varma'],['23B91A0704','BILLA SAHITHI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'billa','sahithi'],['24B95A0702','BOLLEDDU GIRIDHARA VENKATA SAI','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'bolleddu','sai'],['23B91A0705','CHADARAM BHANU VENKATA MANIKANTA','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'chadaram','manikanta'],['23B91A0706','CHEEPU SAI VIKAS','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'cheepu','vikas'],['24B95A0703','CHINIMILLI SAJEEVUDU','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'chinimilli','sajeevudu'],['24B95A0704','CHIRAPA ESWAR VENKATA SATYA NARAYANA','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'chirapa','narayana'],['24B95A0705','DANDUBOYINA VENKATA PRABHAS','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'danduboyina','prabhas'],['23B91A0707','DATTI VENKATA RAMANA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'datti','ramana'],['23B91A0708','DHARMAVARUPU CHANDANA','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'dharmavarupu','chandana'],['23B91A0709','DURU MERY SUNEETHA','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'duru','suneetha'],['23B91A0710','GADDAM CHANDRIKA SRI PRIYA','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'gaddam','priya'],['23B91A0711','GANESNA SATYA RAJESH','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'ganesna','rajesh'],['24B95A0706','GIDUGU NEHANTH SRIHARSHA NAVADEEP','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'gidugu','navadeep'],['23B91A0712','GIRIJALA PRASHANTH KUMAR','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'girijala','kumar'],['24B95A0707','GONAPALA SRI GOWTHAM','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'gonapala','gowtham'],['23B91A0713','GOPATHI KALYANI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'gopathi','kalyani'],['23B91A0714','GOTTUMUKKALA NIKHILA VALLI','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'gottumukkala','valli'],['23B91A0715','GOWRIPATNAM BHAGYAKIRAN','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'gowripatnam','bhagyakiran'],['24B95A0708','INDUKURI YASWANTH ACHYUTA VARMA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'indukuri','varma'],['24B95A0709','ITTA VASAVI','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'itta','vasavi'],['23B91A0716','JALDHI PRINCESS GLORY JASMINE','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'jaldhi','jasmine'],['23B91A0717','KADIYALA NAVYA SRI','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'kadiyala','sri'],['23B91A0718','KAGITHA BHANU DURGA PRASAD','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'kagitha','prasad'],['23B91A0719','KALLA GUNADEEP','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'kalla','gunadeep'],['23B91A0720','KANUBOINA VIJAYA LAKSHMI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'kanuboina','lakshmi'],['23B91A0721','KANUMURI SUDHA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'kanumuri','sudha'],['23B91A0722','KARRI LAKSHMI PRASANNA','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'karri','prasanna'],['23B91A0723','KATTA SRAVANI','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'katta','sravani'],['23B91A0724','KHANDAVALLI VYSHNAVI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'khandavalli','vyshnavi'],['23B91A0725','KOCHERLA YESWANTH','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'kocherla','yeswanth'],['23B91A0726','KODI VAISHNAVI','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'kodi','vaishnavi'],['23B91A0727','KOLLI SHANMUKHA SRIRAM CHARAN TEJA','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'kolli','teja'],['23B91A0728','KOTTA S N VASAVI SRIVALLI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'kotta','srivalli'],['23B91A0729','KURASALA HARSHA VARDHAN','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'kurasala','vardhan'],['24B95A0710','LINGAMPALLI VIJAY VARDHAN','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'lingampalli','vardhan'],['23B91A0730','LOKAM MAHITANJALI','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'lokam','mahitanjali'],['23B91A0731','MAILABATTULA LOUKYATHA','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'mailabattula','loukyatha'],['24B95A0711','MANAPARAPU DEEPIKA','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'manaparapu','deepika'],['23B91A0732','MANELLI SRAVANI','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'manelli','sravani'],['23B91A0733','MEDISETTI RAMA KRISHNA SAI','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'medisetti','sai'],['23B91A0734','MUTHABATHULA PUNEETH','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'muthabathula','puneeth'],['23B91A0735','NAKKA MOHITH SRI NAGA SAI PAVAN','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'nakka','pavan'],['23B91A0736','NANDAMURI BALA SESHA SATYA SRI','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'nandamuri','sri'],['23B91A0737','NANDRU VINAY BABU','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'nandru','babu'],['23B91A0738','NULAKANI LEELA MADHAVA RAO','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'nulakani','rao'],['23B91A0739','PACHIGOLLA RISHITHA MANASA SURYA GAYATRI','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'pachigolla','gayatri'],['23B91A0740','PANJA MUKUNDA SRI NAGA SANTOSH','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'panja','santosh'],['23B91A0741','PANJA NAGA VENKATA PRASAD RAJA','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'panja','raja'],['23B91A0742','PASUPULETI DAIVA PRASAD','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'pasupuleti','prasad'],['23B91A0743','PASUPULETI JASWANTH RAMANA TEJA','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'pasupuleti','teja'],['23B91A0744','PECHETTI SRI VINAYAK','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'pechetti','vinayak'],['24B95A0712','PEETHANI UDAYA SRI','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'peethani','sri'],['24B95A0713','PENMETSA SAI ANVESH VARMA','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'penmetsa','varma'],['23B91A0745','PETTA PRANATHI','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'petta','pranathi'],['24B95A0714','POTHURI SIVA SAI KRISHNA VARMA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'pothuri','varma'],['23B91A0746','PUVVALA DEVI AISHWARYA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'puvvala','aishwarya'],['23B91A0747','RAMANA DIVYA JYOTHIKA','CSIT','4th Year','CSIT 4th Year Sec A',"VAYU",'ramana','jyothika'],['23B91A0748','SEELABOINA RAMADEVI','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'seelaboina','ramadevi'],['23B91A0749','SEELABOINA SANTOSH KUMAR','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'seelaboina','kumar'],['23B91A0750','SHAIK REENAZ','CSIT','4th Year','CSIT 4th Year Sec A',"PRUDHVI",'shaik','reenaz'],['23B91A0751','SHAIK THAHIR BASHA','CSIT','4th Year','CSIT 4th Year Sec A',"AAKASH",'shaik','basha'],['23B91A0752','SIRAPARAPU PRANATHI SAI VARSHINI','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'siraparapu','varshini'],['23B91A0753','TAMMA LOKESH','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'tamma','lokesh'],['23B91A0754','TUMMA SRI HARSHA','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'tumma','harsha'],['23B91A0755','VALLABHANI SAHITHI','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'vallabhani','sahithi'],['23B91A0756','VEERANKI MAHESH BABU','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'veeranki','babu'],['23B91A0757','VETCHA G N V S L SAISREE','CSIT','4th Year','CSIT 4th Year Sec A',"AGNI",'vetcha','saisree'],['23B91A0758','YATHAM LAKSHMI PRASANNA','CSIT','4th Year','CSIT 4th Year Sec A',"JAL",'yatham','prasanna']];
        const _F=[[1,'Dr. Suresh Babu Mudunuri','CSD',true,'sureshbabu.k@srkrec.edu.in',["2nd Year","4th Year"],["CSD"]],[2,'Dr. K. Srinivasa Rao','CSD',true,'ksinivasarao@srkrec.edu.in',["4th Year"],["CSD"]],[3,'Mr. K. Bhanu Rajesh Naidu','CSIT',false,'bhanurajeshnaidu@srkrec.edu.in',["2nd Year","3rd Year","4th Year"],["CSIT"]],[4,'A. Aswini Priyanka','CSD',false,'aswini.areti@srkrec.edu.in',["2nd Year","4th Year"],["CSD","CSIT"]],[5,'Angara Satyam','CSD',false,'satyama@srkrec.edu.in',["2nd Year","3rd Year","4th Year"],["CSD","CSIT"]],[6,'S. Mohan Krishna','CSD',false,'mohankrishna.seerla@srkrec.edu.in',["3rd Year","4th Year","5"],["CSD","CSIT"]],[7,'P S V Surya Kumar','CSIT',false,'suryakumar.poduru@srkrec.edu.in',["4th Year"],["CSIT"]],[8,'Dr. N. Gopala Krishna Murthy','CSIT',true,'gopinukala@srkrec.edu.in',["2nd Year","4th Year"],["CSIT"]],[9,'Jonnapalli Tulasi Rajesh','CSD',false,'jtulasirajesh@srkrec.edu.in',[],[]],[10,'Navya Nallaparaju','CSD',false,'navyanallaparaju@srkrec.edu.in',["3rd Year"],["CSD"]],[11,'N. Praveen','CSD',false,'neti.praveen@srkrec.edu.in',["4th Year"],["CSD"]],[12,'A. Krishna Veni','CSIT',false,'krishnaveni@srkrec.edu.in',["3rd Year"],["CSIT"]],[13,'Mr. K.V.V.S. Trinadh Naidu','CSIT',false,'kvvstrinadhnaidu@srkrec.edu.in',["2nd Year","3rd Year","4th Year","5"],["CSD","CSIT"]],[14,'Penmetsa Mouna','CSIT',false,'mouna.nandyala@srkrec.edu.in',["3rd Year","4th Year","5"],["CSD","CSIT"]],[15,'Pericherla Manoj','CSIT',false,'manoj.p@srkrec.edu.in',["4th Year"],["CSIT"]],[16,'K. V. Sunil Varma','CSD',false,'sunil.kunuku@srkrec.edu.in',["3rd Year","4th Year","5"],["CSD","CSIT"]],[17,'N. Aneela','CSIT',false,'aneela@srkrec.edu.in',["2nd Year","3rd Year","4th Year","5"],["CSD","CSIT"]],[18,'M. S. Suseela','CSD',false,'m.s.suseela@srkrec.edu.in',["3rd Year"],["CSD"]],[19,'M. Srinu','CSIT',false,'msrinu@srkrec.edu.in',["2nd Year"],["CSD","CSIT"]],[20,'J. Mohan Surendra','CSIT',false,'mohansurendra.j@srkrec.edu.in',["2nd Year"],["CSIT"]],[21,'G. Sudhakar','CSIT',false,'sudhakar.g@srkrec.edu.in',["2nd Year"],["CSIT"]],[22,'D. Parvathi','CSIT',false,'parvathi.d@srkrec.edu.in',["2nd Year"],["CSD","CSIT"]],[23,'M. Maduriya','CSIT',false,'maduriya.m@srkrec.edu.in',["2nd Year"],["CSIT"]],[24,'K. Girichar','CSD',false,'girichar.k@srkrec.edu.in',["2nd Year"],["CSD"]],[25,'K. Vignya','CSD',false,'vignya.k@srkrec.edu.in',["2nd Year"],["CSD"]]];
        // Inject students
        for (const [regNo,name,branch,year,section,house,firstName,lastName] of _S) {
            if (!MASTER_PERSON_INDEX.some(p => p.regNo === regNo)) {
                MASTER_PERSON_INDEX.push({
                    id: 'student_'+regNo, person_type: 'student',
                    fullName: name, firstName, lastName,
                    category: 'Student ('+branch+' '+year+')',
                    role: 'Student ('+branch+')', designation: 'Student ('+branch+')',
                    department: branch, branch, year, section, regNo, house,
                    description: name+' is an enrolled student in '+branch+' Department, '+year+' (Reg: '+regNo+').',
                    searchableAliases: [name.toLowerCase(), firstName, lastName, regNo.toLowerCase()],
                    url: 'heroes_of_department.php', ctaText: 'View Student Directory →'
                });
            }
        }
        // Inject faculty (skip if already richly defined in MASTER_PERSON_INDEX)
        for (const [fid,name,dept,hasPhD,email,assignedYears,assignedBranches] of _F) {
            if (!MASTER_PERSON_INDEX.some(p => p.faculty_id === fid)) {
                const tokens = name.toLowerCase().replace(/^(dr\.|mr\.|mrs\.)/i,'').trim().split(/\s+/);
                MASTER_PERSON_INDEX.push({
                    id: 'faculty_'+fid, faculty_id: fid, person_type: 'faculty',
                    fullName: name,
                    firstName: tokens[0]||'', lastName: tokens[tokens.length-1]||'',
                    category: 'Faculty Member ('+dept+')',
                    role: 'Faculty Member ('+dept+')', designation: 'Faculty Member ('+dept+')',
                    department: dept, branch: dept, hasPhD, email,
                    assignedYears, assignedBranches,
                    description: name+' is a faculty member in the '+dept+' Department.',
                    searchableAliases: [name.toLowerCase(), ...(tokens||[]), email.toLowerCase()],
                    url: 'faculty.php', ctaText: 'View Faculty Profile →'
                });
                // Also add to MASTER_FACULTY_ROSTER if not already present
                if (typeof MASTER_FACULTY_ROSTER !== 'undefined' && Array.isArray(MASTER_FACULTY_ROSTER)) {
                    if (!MASTER_FACULTY_ROSTER.some(f => f.faculty_id === fid)) {
                        MASTER_FACULTY_ROSTER.push({
                            id: 'faculty_'+fid, faculty_id: fid,
                            fullName: name, department: dept, branch: dept,
                            role: 'Faculty Member ('+dept+')', designation: 'Faculty Member ('+dept+')',
                            hasPhD, email, assignedYears, assignedBranches,
                            specialization: '', subjects: '', experience: '', experienceYears: 0,
                            grants: '', awards: '', publications: '',
                            url: 'faculty.php'
                        });
                    }
                }
            }
        }
        console.log('[BOOTSTRAP] Pre-loaded '+_S.length+' students + '+_F.length+' faculty into MASTER_PERSON_INDEX');
    })();


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

                // 5. Ingest Enrolled Students (from MySQL students table)
                if (Array.isArray(data.allStudents)) {
                    for (const stu of data.allStudents) {
                        if (stu.name && stu.regNo) {
                            const personExists = MASTER_PERSON_INDEX.some(p => p.regNo === stu.regNo || normalizePersonName(p.fullName) === normalizePersonName(stu.name));
                            if (!personExists) {
                                const tokens = tokenizeName(stu.name);
                                const sDept = stu.department || stu.branch || (stu.regNo.includes('62') ? 'CSD' : 'CSIT');

                                // Normalize year: MySQL year field may be "2", "3", "4" (number) or "2nd Year" etc.
                                let sYear = stu.year || '';
                                if (sYear === '1' || sYear === 1) sYear = '1st Year';
                                else if (sYear === '2' || sYear === 2) sYear = '2nd Year';
                                else if (sYear === '3' || sYear === 3) sYear = '3rd Year';
                                else if (sYear === '4' || sYear === 4) sYear = '4th Year';
                                else if (!sYear) sYear = '3rd Year'; // default fallback

                                // Normalize section: "A" → "Sec A", already "Sec A" stays
                                let sSection = stu.section || 'Sec A';
                                if (sSection.length === 1) sSection = `Sec ${sSection}`;

                                MASTER_PERSON_INDEX.push({
                                    id: `student_${stu.regNo}`,
                                    fullName: stu.name,
                                    person_type: 'student',
                                    firstName: tokens[0] || stu.name.toLowerCase(),
                                    lastName: tokens[tokens.length - 1] || stu.name.toLowerCase(),
                                    category: `Student (${sDept} ${sYear})`,
                                    role: `Student (${sDept})`,
                                    designation: `Student (${sDept})`,
                                    department: sDept,
                                    branch: sDept,
                                    year: sYear,
                                    section: `${sDept} ${sYear} ${sSection}`,
                                    regNo: stu.regNo,
                                    description: `${stu.name} is an enrolled student in ${sDept} Department, ${sYear} (Reg: ${stu.regNo}).`,
                                    searchableAliases: [stu.name.toLowerCase(), tokens[0], tokens[tokens.length - 1], stu.regNo.toLowerCase()],
                                    url: 'heroes_of_department.php',
                                    ctaText: 'View Student Directory →'
                                });
                            }
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

                let studentDept = 'CSD';
                if (m.regNo && m.regNo !== 'N/A') {
                    const regUpper = m.regNo.toUpperCase();
                    if (regUpper.includes('62')) {
                        studentDept = 'CSD';
                    } else if (regUpper.includes('07')) {
                        studentDept = 'CSIT';
                    }
                } else if (m.section) {
                    if (m.section.includes('CSD')) studentDept = 'CSD';
                    else if (m.section.includes('CSIT')) studentDept = 'CSIT';
                }

                const exists = MASTER_PERSON_INDEX.some(p => normalizePersonName(p.fullName) === normName || (m.regNo && p.regNo === m.regNo));
                if (!exists) {
                    MASTER_PERSON_INDEX.push({
                        id: `house_student_${m.name.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase()}`,
                        fullName: m.name,
                        firstName: firstName,
                        lastName: lastName,
                        category: `Student (${studentDept} Department, ${h.name} House Member)`,
                        role: `Student Member — ${h.name} House (${studentDept})`,
                        designation: `Student Member — ${h.name} House (${studentDept})`,
                        department: studentDept,
                        branch: studentDept,
                        year: m.section && m.section.includes('II') ? '2nd Year' : '3rd Year',
                        section: m.section || `${studentDept} II Year Sec A`,
                        regNo: m.regNo !== 'N/A' ? m.regNo : null,
                        points: m.points || 50,
                        description: `${m.name} is a student in ${studentDept} Department (${h.name} House).`,
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

        const stopWords = new Set(['who', 'is', 'are', 'which', 'department', 'dept', 'branch', 'does', 'belong', 'belongs', 'to', 'from', 'what', 'role', 'designation', 'qualification', 'qualifications', 'educational', 'degree', 'degrees', 'specialization', 'specializations', 'subjects', 'teach', 'teaches', 'teaching', 'email', 'contact', 'tell', 'me', 'about', 'can', 'know', 'the', 'a', 'an', 'in', 'of', 'work', 'working', 'studying', 'year', 'section', 'registration', 'number', 'reg', 'no', 'internship', 'internships', 'placements', 'placement', 'house', 'where', 'did', 'she', 'he', 'get', 'got', 'her', 'his', 'their', 'research', 'experience', 'projects', 'project', 'grants', 'grant', 'publications', 'publication', 'papers', 'paper', 'awards', 'award', 'phone', 'mobile', 'details', 'detail', 'info', 'information', 'profile', 'overview']);
        const nameCandidateTokens = queryTokens.filter(t => !stopWords.has(t) && t.length >= 2);

        // Check for pronouns/follow-up query referencing activePerson ONLY if no explicit name candidate token is present
        const isExplicitPronoun = /\b(she|he|her|his|their|this person|that person)\b/i.test(rawQuery);
        const isGenericFollowup = /^(which department|what department|which branch|what branch|what registration number|reg no|where did (she|he) get|what house)\b/i.test(lowerRaw);

        if ((nameCandidateTokens.length === 0 || isExplicitPronoun) && conversationContext.activePerson) {
            // If explicit name candidates exist and don't match activePerson, prioritize candidate search
            if (nameCandidateTokens.length > 0 && !isExplicitPronoun) {
                // Proceed to candidate search below
            } else {
                return { found: true, isMultiple: false, person: conversationContext.activePerson, intent: intent, score: 1.0 };
            }
        }

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

            // E. Department-Filtered Faculty List (CSIT faculty / CSD faculty)
            const deptFilter = /\bcsit\b/i.test(q) ? 'CSIT' : (/\bcsd\b/i.test(q) ? 'CSD' : null);
            if (isFacultyQuery || /\b(faculty list|all faculty|show faculty|faculty members|who are the faculty|tell me about faculty|about faculty|faculty directory)\b/i.test(q)) {
                let filteredFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER);
                if (deptFilter) {
                    filteredFaculty = filteredFaculty.filter(f => (f.department || '').toUpperCase() === deptFilter);
                }
                const deptLabel = deptFilter ? deptFilter : 'CSD & CSIT';
                return {
                    id: deptFilter ? `faculty_dept_${deptFilter}` : 'faculty_all_list',
                    category: 'Faculty Directory',
                    title: `${deptLabel} Department Faculty Members`,
                    content: `Here are all <strong>${filteredFaculty.length} Faculty Members</strong> of the ${deptLabel} Department:<br><br>` +
                             filteredFaculty.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.designation || f.role} (${f.department})`).join('<br>'),
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
        const reg = person.regNo;
        const email = person.email;

        let dept = 'CSD & CSIT';
        if (reg) {
            const cleanReg = reg.toUpperCase();
            if (cleanReg.includes('62')) dept = 'CSD';
            else if (cleanReg.includes('07')) dept = 'CSIT';
        }
        if (dept === 'CSD & CSIT') {
            if (person.department === 'CSD' || person.department === 'CSIT') dept = person.department;
            else if (person.branch === 'CSD' || person.branch === 'CSIT') dept = person.branch;
            else if (person.section && person.section.includes('CSD')) dept = 'CSD';
            else if (person.section && person.section.includes('CSIT')) dept = 'CSIT';
        }

        const role = person.role || person.designation || person.category;

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
     * 13. COMPLETE-LIST STRUCTURED QUERY ENGINE (NO TOP-K RAG)
     * =========================================================================
     * Handles all queries that require COMPLETE results from structured data:
     * "all CSIT faculty", "all 2nd year CSD students", "faculty with PhD",
     * "students in Jal house", "students with internships", "how many X?"
     *
     * BYPASSES RAG / vector top-K entirely. Uses direct index filtering.
     * =========================================================================
     */
    function isCompleteListQuery(q) {
        const lower = q.toLowerCase().trim();
        // Explicit list triggers
        if (/\b(all|everyone|every|complete list|list all|all members|full list|entire list|all of the)\b/i.test(lower)) return true;
        // "how many" counts
        if (/\bhow many\b/i.test(lower)) return true;
        // Faculty list patterns (includes PhD/qualification filters)
        if (/\b(faculty list|all faculty|csit faculty|csd faculty|faculty members|list of faculty|faculty with|faculty who|faculties)\b/i.test(lower)) return true;
        if (/\b(faculty|faculties|teachers?|professors?)\b/i.test(lower) && /\b(phd|ph\.d|doctorate|doctoral|mtech|m\.tech)\b/i.test(lower)) return true;
        // Student list patterns
        if (/\b(student list|all students|csit students|csd students|students list|list of students|students in|students with)\b/i.test(lower)) return true;
        // Year + dept + student/faculty
        if (/\b(1st|2nd|3rd|4th|first|second|third|fourth|i|ii|iii|iv)\s*(year|yr)\b/i.test(lower) && /\b(students?|faculty|faculties|teachers?|professors?)\b/i.test(lower)) return true;
        return false;
    }

    function executeCompleteListQuery(rawQuery) {
        const q = rawQuery.toLowerCase().trim();

        // ── DETECT FILTERS ────────────────────────────────────────────────────
        const filterBranch = /\bcsit\b/i.test(q) ? 'CSIT' : (/\bcsd\b/i.test(q) ? 'CSD' : null);
        const yearMatch = q.match(/\b(1st|first|2nd|second|3rd|third|4th|fourth|i|ii|iii|iv)\s*(year|yr)?\b/i);
        const filterYear = yearMatch ? normalizeYearLabel(yearMatch[1]) : null;
        const isStudent  = /\b(students?|learners?|enrolled)\b/i.test(q);
        const isFaculty  = /\b(faculty|faculties|teachers?|professors?)\b/i.test(q);
        const isHouseQuery = /\b(house|houses|elemental)\b/i.test(q);
        const isInternshipQuery = /\b(internship|internships?|intern)\b/i.test(q);
        const isCountQuery = /\bhow many\b/i.test(q);

        // ── HOUSE MEMBER COMPLETE LIST ────────────────────────────────────────
        const houseNames = { 'jal': 'JAL', 'agni': 'AGNI', 'vayu': 'VAYU', 'akash': 'AAKASH', 'aakash': 'AAKASH', 'prudhvi': 'PRUDHVI', 'pruthvi': 'PRUDHVI' };
        const houseKeyMatch = Object.keys(houseNames).find(h => q.includes(h));
        if (isHouseQuery && houseKeyMatch) {
            const hKey = houseNames[houseKeyMatch];
            const house = MASTER_HOUSE_ROSTER[hKey];
            if (house) {
                const allMembers = [...house.members];
                const displayName = house.name;
                let listHTML = allMembers.map((m, i) =>
                    `${i + 1}. <strong>${m.name}</strong> (${m.regNo || 'N/A'}) — ${m.section || 'Dept Member'} | ${m.points || 0} Points`
                ).join('<br>');
                return {
                    id: `complete_house_${hKey}`,
                    category: `${displayName} House Members`,
                    title: `Complete ${displayName} House Roster (All ${allMembers.length} Members)`,
                    content: `All <strong>${allMembers.length}</strong> verified members of <strong>${displayName} House</strong>:<br><br>${listHTML}<br><br>• <strong>House Total Points:</strong> ${allMembers.reduce((sum, m) => sum + (m.points || 0), 0)} Points`,
                    url: 'house_detail.php',
                    ctaText: `View ${displayName} House →`
                };
            }
        }

        // ── STUDENTS WITH INTERNSHIPS COMPLETE LIST ──────────────────────────
        if (isInternshipQuery && (isStudent || !isFaculty)) {
            const internList = MASTER_INTERNSHIPS_INDEX || [];
            let filtered = [...internList];
            if (filterBranch) filtered = filtered.filter(x => (x.branch || x.department || '').toUpperCase() === filterBranch);
            if (filterYear)   filtered = filtered.filter(x => (x.year || '').includes(filterYear.charAt(0)));
            if (filtered.length > 0) {
                const label = [filterYear, filterBranch, 'Internship Students'].filter(Boolean).join(' ');
                let listHTML = filtered.map((s, i) =>
                    `${i + 1}. <strong>${s.name}</strong> (Reg: ${s.regNo}) — <strong>${s.company || 'N/A'}</strong> | ${s.role || ''}`
                ).join('<br>');
                return {
                    id: `complete_internships_${filterBranch || 'all'}_${filterYear || 'all'}`,
                    category: 'Student Internships',
                    title: `Complete ${label} List`,
                    content: `All <strong>${filtered.length} verified ${label}</strong> from official department records:<br><br>${listHTML}`,
                    url: 'internships.php',
                    ctaText: 'View Internships & Placements →'
                };
            }
        }

        // ── FACULTY COMPLETE LIST ─────────────────────────────────────────────
        if (isFaculty) {
            let pool = MASTER_PERSON_INDEX.filter(p => p.person_type === 'faculty');
            if (filterBranch) pool = pool.filter(p => (p.department || p.branch || '').toUpperCase() === filterBranch);

            // PhD filter
            if (/\b(phd|ph\.d|doctorate|doctoral)\b/i.test(q)) {
                pool = pool.filter(p => p.hasPhD === true);
            }

            // Year filter (faculty assigned to that year)
            if (filterYear) {
                pool = pool.filter(p => {
                    const fid = p.faculty_id;
                    const assign = FACULTY_TEACHING_ASSIGNMENTS[fid];
                    if (assign) return assign.years.includes(filterYear) && (!filterBranch || assign.branches.includes(filterBranch));
                    return (p.assignedYears || []).includes(filterYear);
                });
            }

            pool = deduplicatePeople(pool);
            const label = [filterYear, filterBranch, 'Faculty'].filter(Boolean).join(' ');

            if (isCountQuery) {
                return {
                    id: `count_faculty_${filterBranch || 'all'}_${filterYear || 'all'}`,
                    category: 'Faculty Analytics',
                    title: `${label} Count`,
                    content: `There are <strong>${pool.length} ${label} members</strong> in the department's official records.`,
                    url: 'faculty.php',
                    ctaText: 'View Faculty Directory →'
                };
            }

            if (pool.length > 0) {
                let listHTML = pool.map((f, i) =>
                    `${i + 1}. <strong>${f.fullName}</strong> — ${f.role || f.designation || 'Faculty'} (${f.department})`
                ).join('<br>');
                return {
                    id: `complete_faculty_${filterBranch || 'all'}_${filterYear || 'all'}`,
                    category: 'Faculty Directory',
                    title: `Complete ${label} Members List (${pool.length} Total)`,
                    content: `All <strong>${pool.length} verified ${label} Members</strong> from official department records (Source: MySQL + Faculty Directory):<br><br>${listHTML}`,
                    url: 'faculty.php',
                    ctaText: 'View Complete Faculty Directory →'
                };
            }
        }

        // ── STUDENTS COMPLETE LIST ────────────────────────────────────────────
        if (isStudent || (!isFaculty && !isHouseQuery)) {
            let pool = MASTER_PERSON_INDEX.filter(p => p.person_type === 'student');
            if (filterBranch) pool = pool.filter(p => (p.department || p.branch || '') === filterBranch);
            if (filterYear) {
                pool = pool.filter(p => {
                    const py = (p.year || '').toLowerCase();
                    const ps = (p.section || '').toLowerCase();
                    const yn = filterYear.charAt(0);
                    return py.includes(yn) || ps.includes(yn);
                });
            }

            pool = deduplicatePeople(pool);

            if (!isStudent && pool.length === 0) return null; // nothing matched

            const label = [filterYear, filterBranch, 'Students'].filter(Boolean).join(' ') || 'All Students';

            // DATABASE MATCH COUNT = pool.length (structured, no topK)
            if (isCountQuery) {
                return {
                    id: `count_students_${filterBranch || 'all'}_${filterYear || 'all'}`,
                    category: 'Student Analytics',
                    title: `${label} Count`,
                    content: `Based on verified MySQL enrollment records, there are <strong>${pool.length} ${label}</strong> in the department.<br><br><em>• CSD Total: ${MASTER_PERSON_INDEX.filter(p => p.person_type === 'student' && p.department === 'CSD').length} students<br>• CSIT Total: ${MASTER_PERSON_INDEX.filter(p => p.person_type === 'student' && p.department === 'CSIT').length} students</em>`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Student Directory →'
                };
            }

            const MAX_DISPLAY = 150; // UI limit for readability
            const shown = pool.slice(0, MAX_DISPLAY);
            let listHTML = shown.map((s, i) =>
                `${i + 1}. <strong>${s.fullName}</strong> (${s.regNo}) — ${s.section || s.year || s.role || 'Student'}`
            ).join('<br>');
            const moreNote = pool.length > MAX_DISPLAY ? `<br><br><em>Showing first ${MAX_DISPLAY} of ${pool.length} total ${label}. Visit the Student Dashboard for the complete roster.</em>` : '';
            return {
                id: `complete_students_${filterBranch || 'all'}_${filterYear || 'all'}`,
                category: 'Student Directory',
                title: `Complete ${label} List (${pool.length} Total)`,
                content: `All <strong>${pool.length} verified ${label}</strong> from MySQL enrollment records:<br><br>${listHTML}${moreNote}`,
                url: 'heroes_of_department.php',
                ctaText: 'View Student Directory →'
            };
        }

        return null;
    }

    /**
     * =========================================================================
     * 14. PRIMARY RAG HYBRID DISPATCHER ENFORCING RETRIEVAL SYSTEM
     * =========================================================================
     */
    function searchKnowledgeVector(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        // ── STEP 0: COMPLETE-LIST STRUCTURED QUERY (NO TOP-K RAG) ────────────
        // This MUST run FIRST before any person detection or RAG.
        if (isCompleteListQuery(lower)) {
            const completeResult = executeCompleteListQuery(rawQuery);
            if (completeResult) {
                console.log('[CHATBOT INTENT] Complete-List Structured Query:', completeResult.title);
                return completeResult;
            }
        }

        // Detect if query is a list/overview query (year+dept faculty/students) — skip person detection
        const isListQuery = (
            /\b(faculty|faculties|teachers|professors)\b/i.test(lower) && (/\b(all|list|directory|who are|members|2nd|3rd|4th|1st|second|third|fourth|first|year|csit|csd)\b/i.test(lower))
        ) || (
            /\b(students?)\b/i.test(lower) && /\b(all|list|directory|who are|2nd|3rd|4th|1st|second|third|fourth|first|year)\b/i.test(lower)
        ) || /\bhow many\b/i.test(lower);

        // 1. PERSON / FACULTY / STUDENT LOOKUP FIRST (skipped for list queries)
        const personResult = !isListQuery ? detectPersonInQuery(rawQuery) : null;
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

        // 6. YEAR + DEPARTMENT SPECIFIC FACULTY QUERY HANDLER
        const isFacultyQuery = /\b(faculty|faculties|teachers|professors|teacher|professor|teaches|teaching|who teaches)\b/i.test(lower);
        const reqYearMatch = lower.match(/\b(1st|first|i|2nd|second|ii|3rd|third|iii|4th|fourth|iv)\s*(year|yr)?\b/i);
        const reqBranchCSD = /\bcsd\b/i.test(lower);
        const reqBranchCSIT = /\bcsit\b/i.test(lower);

        if (isFacultyQuery && reqYearMatch) {
            let parsedYear = '2nd Year';
            const yrStr = reqYearMatch[1].toLowerCase();
            if (['1st', 'first', 'i'].includes(yrStr)) parsedYear = '1st Year';
            else if (['2nd', 'second', 'ii'].includes(yrStr)) parsedYear = '2nd Year';
            else if (['3rd', 'third', 'iii'].includes(yrStr)) parsedYear = '3rd Year';
            else if (['4th', 'fourth', 'iv'].includes(yrStr)) parsedYear = '4th Year';

            let targetBranch = reqBranchCSD ? 'CSD' : (reqBranchCSIT ? 'CSIT' : 'CSIT');

            const verifiedFaculty = MASTER_FACULTY_ROSTER.filter(f => {
                const fid = f.faculty_id || f.id;
                const assign = FACULTY_TEACHING_ASSIGNMENTS[fid] || FACULTY_TEACHING_ASSIGNMENTS[parseInt(fid)];
                if (assign) {
                    const yearMatch = assign.years.includes(parsedYear);
                    const branchMatch = assign.branches.includes(targetBranch);
                    return yearMatch && branchMatch;
                }
                // Fallback check on subjects / designation / department string
                const roleStr = (f.role + ' ' + f.department + ' ' + f.subjects).toLowerCase();
                return roleStr.includes(targetBranch.toLowerCase()) && roleStr.includes(parsedYear.toLowerCase());
            });

            const uniqueVerified = deduplicatePeople(verifiedFaculty);

            if (uniqueVerified.length > 0) {
                let listHTML = uniqueVerified.map((f, i) => `${i + 1}. <strong>${f.fullName}</strong> — ${f.role} (${f.department})`).join('<br>');
                return {
                    id: `faculty_year_${parsedYear.replace(/\s+/g, '_')}_${targetBranch}`,
                    category: 'Faculty Teaching Assignments',
                    title: `Verified ${parsedYear} ${targetBranch} Teaching Faculty Members`,
                    content: `Here are the verified faculty members teaching <strong>${parsedYear} ${targetBranch}</strong> classes (Total: ${uniqueVerified.length}):<br><br>${listHTML}`,
                    url: 'faculty.php',
                    ctaText: 'View Complete Faculty Directory →'
                };
            } else {
                return {
                    id: `faculty_year_unverified_${parsedYear.replace(/\s+/g, '_')}_${targetBranch}`,
                    category: 'Faculty Teaching Assignments',
                    title: `${parsedYear} ${targetBranch} Faculty Assignments`,
                    content: `I can find the <strong>${parsedYear} ${targetBranch}</strong> student information and the general ${targetBranch} faculty profiles, but I couldn't verify an explicit ${parsedYear} faculty teaching assignment from current department records.`,
                    url: 'faculty.php',
                    ctaText: 'View Complete Faculty Directory →'
                };
            }
        }

        // 6. GENERAL FACULTY CATEGORY OVERVIEW
        if (isFacultyQuery && /\b(who are the|all|list of|directory|members)\b/i.test(lower)) {
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

        // 7. YEAR + DEPARTMENT SPECIFIC STUDENT QUERY HANDLER
        const isStudentQuery = /\b(students?|learners?|enrolled|batch|class)\b/i.test(lower);
        const { year: qYear, branch: qBranch } = parseYearAndBranchFromQuery(lower);

        if (isStudentQuery && (qYear || qBranch)) {
            let pool = MASTER_PERSON_INDEX.filter(p => p.person_type === 'student' || (p.category && p.category.toLowerCase().includes('student')));

            // Filter by branch if specified
            if (qBranch) pool = pool.filter(p => (p.department || p.branch) === qBranch);

            // Filter by year if specified — match 'year' field or class_id-derived year
            if (qYear) {
                const yearNum = qYear.charAt(0); // e.g. '2' from '2nd Year'
                pool = pool.filter(p => {
                    const py = (p.year || '').toLowerCase();
                    const ps = (p.section || '').toLowerCase();
                    return py.includes(yearNum) || ps.includes(yearNum + 'nd') || ps.includes(yearNum + 'rd') || ps.includes(yearNum + 'th') || ps.includes('year ' + yearNum) || py === (qYear || '').toLowerCase();
                });
            }

            pool = deduplicatePeople(pool);
            const label = [qYear, qBranch].filter(Boolean).join(' ');

            // Check analytics for total count
            const analyticsData = qYear && qBranch ? (STUDENT_YEAR_DEPT_ANALYTICS[qYear] || {})[qBranch] : null;

            if (pool.length > 0) {
                const limit = pool.length > 60 ? 60 : pool.length;
                const shown = pool.slice(0, limit);
                let listHTML = shown.map((s, i) => `${i + 1}. <strong>${s.fullName}</strong> (${s.regNo || 'Reg N/A'}) — ${s.section || s.role || s.category}`).join('<br>');
                const totalNote = analyticsData ? '' : (pool.length > limit ? `<br><br><em>Showing ${limit} of ${pool.length}+ indexed students.</em>` : '');
                return {
                    id: `students_year_dept_${(label).replace(/\s+/g, '_').toLowerCase()}`,
                    category: 'Student Directory',
                    title: `${label} Students — Verified Records`,
                    content: `Here are verified <strong>${label} Students</strong> from department records (Total indexed: ${pool.length}):<br><br>${listHTML}${totalNote}`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Student Directory →'
                };
            } else {
                const sections = analyticsData ? analyticsData.sections.join(', ') : 'Available';
                return {
                    id: `students_year_dept_nodata_${(label).replace(/\s+/g, '_').toLowerCase()}`,
                    category: 'Student Directory',
                    title: `${label} Students`,
                    content: `<strong>${label}</strong> student section exists in the department (Sections: ${sections}), but detailed individual student records for this year have not yet been synced into the knowledge base. Please try the Student Dashboard or visit <strong>heroes_of_department.php</strong> for the full roster.`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Student Directory →'
                };
            }
        }

        // HOW MANY / COUNT QUERY FOR STUDENTS
        if (/\b(how many|count of|number of|total)\s+(students?|learners?)\b/i.test(lower)) {
            const { year: cy, branch: cb } = parseYearAndBranchFromQuery(lower);
            let pool = MASTER_PERSON_INDEX.filter(p => p.person_type === 'student');
            if (cb) pool = pool.filter(p => (p.department || p.branch) === cb);
            if (cy) pool = pool.filter(p => (p.year || '').includes(cy.charAt(0)));
            pool = deduplicatePeople(pool);
            const labelStr = [cy, cb, 'students'].filter(Boolean).join(' ');
            return {
                id: `count_students_${(labelStr).replace(/\s+/g, '_').toLowerCase()}`,
                category: 'Student Analytics',
                title: `${labelStr} Count`,
                content: `Based on indexed department records, there are <strong>${pool.length}</strong> <strong>${labelStr}</strong> in the system. (For official enrollment figures, please check the Student Dashboard or contact the department office.)`,
                url: 'heroes_of_department.php',
                ctaText: 'View Student Directory →'
            };
        }

        // 7b. GENERAL STUDENT CATEGORY OVERVIEW (no year/branch filter)
        if (/\b(who are the students|student body|students list|list of students|student directory|which students belong to|students in|students of|csd students|csit students)\b/i.test(lower)) {
            const isCSD = /\bcsd\b/i.test(lower);
            const isCSIT = /\bcsit\b/i.test(lower);

            let allStudentsPool = MASTER_PERSON_INDEX.filter(p => p.category && p.category.toLowerCase().includes('student'));
            if (isCSD) allStudentsPool = allStudentsPool.filter(p => (p.department || p.branch) === 'CSD');
            if (isCSIT) allStudentsPool = allStudentsPool.filter(p => (p.department || p.branch) === 'CSIT');

            allStudentsPool = deduplicatePeople(allStudentsPool);
            const filterLabel = isCSD ? 'CSD' : (isCSIT ? 'CSIT' : 'CSD & CSIT');

            if (allStudentsPool.length > 0) {
                const limit = Math.min(allStudentsPool.length, 80);
                let listHTML = allStudentsPool.slice(0, limit).map((s, i) => `${i + 1}. <strong>${s.fullName}</strong> (${s.regNo || 'Reg N/A'}) — ${s.role || s.category}`).join('<br>');
                return {
                    id: `students_overview_${filterLabel.toLowerCase()}`,
                    category: 'Student Directory',
                    title: `${filterLabel} Student Roster & Members`,
                    content: `Here are enrolled <strong>${filterLabel} Students</strong> in current department records:<br><br>${listHTML}`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Student Directory & Leadership →'
                };
            }

            return {
                id: 'students_overview',
                category: 'Student Directory',
                title: `${filterLabel} Student Body & Sections`,
                content: `${filterLabel} Student Directory & Academic Sections:<br><br>
• <strong>Total Enrolled Students:</strong> 600+ across 2nd, 3rd, and 4th Years in ${filterLabel}.<br>
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
        const queryTokens = lower.replace(/[\?\!\.\,\;\:\-]/g, ' ').split(/\s+/).filter(t => t.length > 2);
        const hasPersonKeywords = /^\b(who is|about|tell me about|profile of|info on|details of|which department does|which branch is|what is the role of|department of|motupalli|meena|student|faculty)\b/i.test(lower) || queryTokens.length >= 2;

        if (hasPersonKeywords) {
            return {
                id: 'person_not_found',
                category: 'People Search',
                title: 'Person Not Found',
                isNotFound: true,
                content: `I couldn't find any student, faculty, or staff record matching "${rawQuery}" in the official department website records.`,
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
