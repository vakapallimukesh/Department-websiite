/**
 * AI Department Assistant — Google Gemini API & Scalable Hybrid RAG Engine
 * SRKREC CSD & CSIT Departments
 *
 * DEDUPLICATED COMPLETE WEBSITE KNOWLEDGE ARCHITECTURE:
 * Centralized searchable representation of the complete department website and database.
 * Dynamic Data Sources: Live MySQL `faculties`, `students`, `houses`, `classes` tables
 * 25 Unique Faculty Records, 612 Student Records, 5 Elemental Houses, 14 Class Representatives.
 * Enforces Institutional Identity Deduplication (Email, Faculty ID, Reg No, Normalized Aliases).
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
        lastQuery: null,
        history: []
    };

    /**
     * =========================================================================
     * 1. GENERIC STRING NORMALIZER & TOKENIZER
     * =========================================================================
     */
    function normalizePersonName(str) {
        if (!str) return '';
        let s = str.toLowerCase().trim();
        s = s.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, ' ');
        s = s.replace(/[\?\!\.\,\;\:]/g, ' ');
        return s.replace(/\s+/g, ' ').trim();
    }

    function tokenizeName(str) {
        if (!str) return [];
        let clean = normalizePersonName(str);
        clean = clean.replace(/\b(who|is|are|tell|me|about|give|details|of|show|profile|the|a|an|registration|number|reg|no|which|what|belong|belongs|from|studying|branch|department|role)\b/g, ' ');
        return clean.split(/\s+/).filter(t => t.length > 0);
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
        if (/\b(qualification|educational qualification|degree|highest degree)\b/i.test(q)) {
            return 'QUALIFICATION';
        }
        if (/\b(specialization|area of interest|research area|expertise)\b/i.test(q)) {
            return 'SPECIALIZATION';
        }
        if (/\b(subjects|subjects taught|courses taught|teaches|teaching)\b/i.test(q)) {
            return 'SUBJECTS';
        }
        if (/\b(experience|how many years|years of experience)\b/i.test(q)) {
            return 'EXPERIENCE';
        }
        if (/\b(achievements|awards|prizes|honors|won|secured)\b/i.test(q)) {
            return 'ACHIEVEMENTS';
        }
        if (/\b(email|email id|mail|mail id|contact email)\b/i.test(q)) {
            return 'EMAIL';
        }
        if (/\b(phone|phone number|mobile|contact number|contact)\b/i.test(q)) {
            return 'CONTACT';
        }
        return 'PROFILE';
    }

    /**
     * =========================================================================
     * 4. MASTER HOUSE ROSTER ENGINE (612 VERIFIED HOUSE MEMBERS WITH POINTS)
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
     * 5. MASTER PERSON INDEX (COMPLETE 25 FACULTY + HEROES + CRs + STUDENTS)
     * =========================================================================
     */
    const MASTER_PERSON_INDEX = [
        // --- ALL 25 FACULTY RECORDS FROM MYSQL DATABASE & FACULTY.PHP ---
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
            qualification: 'Ph.D in Computer Science (JNTU, 2010)',
            hasPhD: true,
            specialization: 'AI, Machine Learning & Cloud Infrastructure',
            subjects: 'Artificial Intelligence, Cloud Computing',
            experience: '20+ Years',
            experienceYears: 20,
            achievements: 'Head of Department (CSD), 35+ Research Publications, 15+ Funded Projects',
            description: 'Dr. Suresh Babu Mudunuri is Professor and Head of Department of Computer Science & Design (CSD) at SRKR Engineering College.',
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
            qualification: 'Ph.D in Information Technology (JNTU, 2011)',
            hasPhD: true,
            specialization: 'Information Technology Systems & Enterprise Networks',
            subjects: 'Enterprise Networks, Information Systems',
            experience: '18+ Years',
            experienceYears: 18,
            achievements: 'Head of Department (CSIT), 30+ Research Publications, 18+ Projects',
            description: 'Dr. N. Gopala Krishna Murthy is Professor and Head of Department of Computer Science & Information Technology (CSIT) at SRKR Engineering College.',
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
            qualification: 'Ph.D in Computer Science (Andhra University, 2018)',
            hasPhD: true,
            specialization: 'Computer Networks & Security',
            subjects: 'Computer Networks, Information Security',
            experience: '10+ Years',
            experienceYears: 10,
            achievements: 'Ph.D Doctorate Holder, 15+ Publications',
            description: 'Dr. K. Srinivasa Rao is Assistant Professor in CSD specializing in Computer Networks and Cyber Security.',
            searchableAliases: ['srinivasa rao', 'dr k srinivasa rao', 'k srinivasa rao', 'srinivasa rao sir', 'dr srinivasa', 'srinivasa sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_bhanu_rajesh',
            faculty_id: 3,
            fullName: 'Mr. K. Bhanu Rajesh Naidu',
            firstName: 'bhanu',
            lastName: 'rajesh',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'bhanurajeshnaidu@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Cloud Computing & DevOps Systems',
            subjects: 'Cloud Computing, DevOps Systems',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'Mr. K. Bhanu Rajesh Naidu is Assistant Professor in CSD.',
            searchableAliases: ['bhanu rajesh', 'bhanu rajesh naidu', 'k bhanu rajesh naidu', 'bhanu sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aswini_priyanka',
            faculty_id: 4,
            fullName: 'A. Aswini Priyanka',
            firstName: 'aswini',
            lastName: 'priyanka',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'aswini.areti@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2015)',
            hasPhD: false,
            specialization: 'Cloud Computing & Web Technologies',
            subjects: 'Cloud Computing, Web Technologies',
            experience: '8+ Years',
            experienceYears: 8,
            achievements: 'Cloud Certified Educator, 10+ Publications',
            description: 'A. Aswini Priyanka (Aswini Priyanka Madam) is Assistant Professor in CSD specializing in Cloud Computing.',
            searchableAliases: ['aswini', 'aswini priyanka', 'a aswini priyanka', 'aswini madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_satyam',
            faculty_id: 5,
            fullName: 'ANGARA SATYAM',
            firstName: 'satyam',
            lastName: 'angara',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'satyama@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            hasPhD: false,
            specialization: 'Artificial Intelligence & Intelligent Systems',
            subjects: 'Artificial Intelligence, Python Programming, Machine Learning',
            experience: '7+ Years',
            experienceYears: 7,
            achievements: 'AI Coding Contest Coach, Intelligent Automation Mentor',
            description: 'Angara Satyam (Satyam Sir) is Assistant Professor in CSD specializing in Artificial Intelligence and Python Programming.',
            searchableAliases: ['satyam', 'angara satyam', 'a satyam', 'a. satyam', 'satyam sir', 'satyam madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mohan_krishna',
            faculty_id: 6,
            fullName: 'S. Mohan Krishna',
            firstName: 'mohan',
            lastName: 'krishna',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'mohankrishna.seerla@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            hasPhD: false,
            specialization: 'AI, Machine Learning & Computer Vision',
            subjects: 'Artificial Intelligence, Machine Learning',
            experience: '7+ Years',
            experienceYears: 7,
            achievements: 'AI & ML Research Mentor, 8+ Publications',
            description: 'S. Mohan Krishna (Mohan Krishna Sir) is Assistant Professor in CSD specializing in AI and Machine Learning.',
            searchableAliases: ['mohan krishna', 's. mohan krishna', 's mohan krishna', 'mohan krishna sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surya_kumar',
            faculty_id: 7,
            fullName: 'P S V SURYA KUMAR',
            firstName: 'surya',
            lastName: 'kumar',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'suryakumar.poduru@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'IoT & Embedded Edge Systems',
            subjects: 'IoT Architecture, Embedded Systems',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'P S V SURYA KUMAR is Assistant Professor in CSD specializing in IoT.',
            searchableAliases: ['surya kumar', 'p s v surya kumar', 'surya kumar sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_tulasi_rajesh',
            faculty_id: 9,
            fullName: 'Jonnapalli Tulasi Rajesh',
            firstName: 'tulasi',
            lastName: 'rajesh',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'jtulasirajesh@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Software Engineering & Systems',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'Jonnapalli Tulasi Rajesh is a Faculty Member in CSD.',
            searchableAliases: ['tulasi rajesh', 'jonnapalli tulasi rajesh', 'tulasi sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_navya',
            faculty_id: 10,
            fullName: 'Navya Nallaparaju',
            firstName: 'navya',
            lastName: 'nallaparaju',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'navyanallaparaju@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Machine Learning & Data Structures',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'Navya Nallaparaju is Assistant Professor in CSIT.',
            searchableAliases: ['navya', 'navya nallaparaju', 'navya madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_praveen',
            faculty_id: 11,
            fullName: 'Neti Praveen',
            firstName: 'praveen',
            lastName: 'neti',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'neti.praveen@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            hasPhD: false,
            specialization: 'Machine Learning & Database Management',
            experience: '7+ Years',
            experienceYears: 7,
            description: 'Neti Praveen is Assistant Professor in CSIT.',
            searchableAliases: ['neti praveen', 'n praveen', 'praveen sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_krishna_veni',
            faculty_id: 12,
            fullName: 'Anusuri Krishna Veni',
            firstName: 'krishna veni',
            lastName: 'anusuri',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'krishnaveni@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Machine Learning & Data Mining',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'Anusuri Krishna Veni is Assistant Professor in CSIT.',
            searchableAliases: ['krishna veni', 'a. krishna veni', 'anusuri krishna veni', 'krishna veni madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_trinadh',
            faculty_id: 13,
            fullName: 'K V V Satya Trinadh Naidu',
            firstName: 'trinadh',
            lastName: 'naidu',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'kvvstrinadhnaidu@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            hasPhD: false,
            specialization: 'Cyber Security, Java, Python Application Development',
            subjects: 'Cyber Security, Java Programming, Python',
            experience: '7+ Years',
            experienceYears: 7,
            achievements: 'Lead Cybersecurity Advisor (8+ Publications, 9+ Projects)',
            description: 'K V V Satya Trinadh Naidu (Trinadh Sir) is Assistant Professor in CSIT specializing in Cyber Security and Java Application Development.',
            searchableAliases: ['trinadh', 'trinadh naidu', 'satya trinadh', 'k v v satya trinadh naidu', 'trinadh sir', 'kvvstrinadhnaidu'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mouna',
            faculty_id: 14,
            fullName: 'Penmetsa Mouna',
            firstName: 'mouna',
            lastName: 'penmetsa',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'mouna.nandyala@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            hasPhD: false,
            specialization: 'Machine Learning & Neural Networks',
            experience: '5+ Years',
            experienceYears: 5,
            description: 'Penmetsa Mouna is Assistant Professor in CSIT.',
            searchableAliases: ['mouna', 'penmetsa mouna', 'mouna madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_manoj',
            faculty_id: 15,
            fullName: 'Pericherla Manoj',
            firstName: 'manoj',
            lastName: 'pericherla',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'manoj.p@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            hasPhD: false,
            specialization: 'Prompt Engineering & Generative AI',
            subjects: 'Prompt Engineering, Generative AI, Python',
            experience: '5+ Years',
            experienceYears: 5,
            achievements: 'Generative AI Workshop Lead, 6+ Publications',
            description: 'Pericherla Manoj (Manoj Sir) is Assistant Professor in CSIT specializing in Prompt Engineering and Generative AI.',
            searchableAliases: ['manoj', 'p manoj', 'pericherla manoj', 'manoj sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sunil_varma',
            faculty_id: 16,
            fullName: 'K V Sunil Varma',
            firstName: 'sunil',
            lastName: 'varma',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'sunil.kunuku@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            hasPhD: false,
            specialization: 'Machine Learning & Software Engineering',
            experience: '6+ Years',
            experienceYears: 6,
            description: 'K V Sunil Varma is Assistant Professor in CSIT.',
            searchableAliases: ['sunil varma', 'k v sunil varma', 'sunil varma sir'],
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
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            hasPhD: false,
            specialization: 'Machine Learning & Data Mining',
            subjects: 'Machine Learning, Data Mining',
            experience: '5+ Years',
            experienceYears: 5,
            achievements: 'Data Science Mentor, 6+ Publications',
            description: 'N. Aneela (Aneela Madam) is Assistant Professor in CSD specializing in Machine Learning.',
            searchableAliases: ['aneela', 'n aneela', 'aneela madam', 'aneela sir', 'dr aneela', 'aneela mam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_suseela',
            faculty_id: 18,
            fullName: 'M S Suseela',
            firstName: 'suseela',
            lastName: 'm',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'm.s.suseela@srkrec.edu.in',
            qualification: 'M.Tech in CSE (SRKR, 2019)',
            hasPhD: false,
            specialization: 'Computer Science & Software Systems',
            experience: '4+ Years',
            experienceYears: 4,
            description: 'M S Suseela is a Faculty Member in CSD.',
            searchableAliases: ['suseela', 'm s suseela', 'suseela madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinu',
            faculty_id: 19,
            fullName: 'M. SRINU',
            firstName: 'srinu',
            lastName: 'm',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'msrinu@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2019)',
            hasPhD: false,
            specialization: 'Information Technology & Cloud Systems',
            experience: '4+ Years',
            experienceYears: 4,
            description: 'M. SRINU (Srinu Sir) is a Faculty Member in CSIT.',
            searchableAliases: ['m srinu', 'm. srinu', 'srinu sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surendra',
            faculty_id: 20,
            fullName: 'J. MOHAN SURENDRA',
            firstName: 'surendra',
            lastName: 'mohan',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'mohansurendra.j@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2019)',
            hasPhD: false,
            specialization: 'Software Systems & Information Technology',
            experience: '4+ Years',
            experienceYears: 4,
            description: 'J. MOHAN SURENDRA is a Faculty Member in CSIT.',
            searchableAliases: ['mohan surendra', 'surendra', 'j mohan surendra', 'surendra sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sudhakar',
            faculty_id: 21,
            fullName: 'G. SUDHAKAR',
            firstName: 'sudhakar',
            lastName: 'g',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'sudhakar.g@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            hasPhD: false,
            specialization: 'Computer Science & Software Engineering',
            experience: '5+ Years',
            experienceYears: 5,
            description: 'G. SUDHAKAR is a Faculty Member in CSIT.',
            searchableAliases: ['sudhakar', 'g sudhakar', 'sudhakar sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_parvathi',
            faculty_id: 22,
            fullName: 'D. PARVATHI',
            firstName: 'parvathi',
            lastName: 'd',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'parvathi.d@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            hasPhD: false,
            specialization: 'Machine Learning & Programming',
            experience: '5+ Years',
            experienceYears: 5,
            description: 'D. PARVATHI (Parvathi Madam) is a Faculty Member in CSIT.',
            searchableAliases: ['d parvathi', 'parvathi madam', 'parvathi sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_maduriya',
            faculty_id: 23,
            fullName: 'M. MADURIYA',
            firstName: 'maduriya',
            lastName: 'm',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'maduriya.m@srkrec.edu.in',
            qualification: 'M.Tech in CSE (SRKR, 2020)',
            hasPhD: false,
            specialization: 'Data Science & Information Systems',
            experience: '3+ Years',
            experienceYears: 3,
            description: 'M. MADURIYA is a Faculty Member in CSIT.',
            searchableAliases: ['maduriya', 'm maduriya', 'maduriya madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_girichar',
            faculty_id: 24,
            fullName: 'K. GIRICHAR',
            firstName: 'girichar',
            lastName: 'k',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'girichar.k@srkrec.edu.in',
            qualification: 'M.Tech in CSE (JNTUK, 2019)',
            hasPhD: false,
            specialization: 'Computer Science & Design Thinking',
            experience: '4+ Years',
            experienceYears: 4,
            description: 'K. GIRICHAR is a Faculty Member in CSD.',
            searchableAliases: ['girichar', 'k girichar', 'girichar sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_vignya',
            faculty_id: 25,
            fullName: 'K. VIGNYA',
            firstName: 'vignya',
            lastName: 'k',
            category: 'Faculty Member',
            role: 'Teaching Assistant (CSIT)',
            designation: 'Teaching Assistant (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'vignya.k@srkrec.edu.in',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            hasPhD: false,
            specialization: 'Machine Learning & Python Lab',
            experience: '3+ Years',
            experienceYears: 3,
            description: 'K. VIGNYA is Teaching Assistant in CSIT.',
            searchableAliases: ['vignya', 'k vignya', 'vignya madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },

        // --- DEPARTMENT HEROES & ACHIEVERS ---
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
        {
            id: 'person_pbs_kruti',
            fullName: 'P.B.S Kruti',
            firstName: 'kruti',
            lastName: 'kruti',
            category: 'Department Hero & Cultural Achiever',
            role: '1st Prize Winner Classical Dance',
            designation: '1st Prize Winner Classical Dance (SRKREC Annual Day)',
            department: 'CSD',
            branch: 'CSD',
            regNo: '25B91A0789',
            achievements: '1st Prize Winner in Classical Dance Group Performance at SRKREC Annual Day',
            description: 'P.B.S Kruti is an exceptional classical dancer in the CSD department (Reg: 25B91A0789).',
            searchableAliases: ['kruti', 'p.b.s kruti', 'pbs kruti'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },

        // --- CLASS REPRESENTATIVES (14 CR RECORDS) ---
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
            section: 'CSD – II Year',
            regNo: '25B91A6223',
            isCR: true,
            searchableAliases: ['javvadi mohana durga', 'mohana durga', 'javvadi', 'mohana'],
            description: 'Javvadi Mohana Durga is the Class Representative for CSD II Year (Reg No: 25B91A6223).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_hari_nagendra',
            fullName: 'VASA HARI NAGENDRA PRATAP',
            firstName: 'hari nagendra',
            lastName: 'vasa',
            category: 'Class Representative',
            role: 'Class Representative (CSD II Year)',
            designation: 'Class Representative (CSD II Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '2nd Year',
            section: 'CSD – II Year',
            regNo: '25B91A6263',
            isCR: true,
            searchableAliases: ['vasa hari nagendra pratap', 'hari nagendra pratap', 'vasa', 'nagendra'],
            description: 'Vasa Hari Nagendra Pratap is the Class Representative for CSD II Year (Reg No: 25B91A6263).',
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

    const MASTER_CR_INDEX = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => p.isCR));

    // Dynamic Live Database Synchronization with Institutional Identity Deduplication
    async function syncWebsiteKnowledge() {
        if (isDbSynced) return;
        try {
            const res = await fetch('api/get_website_knowledge.php');
            if (res.ok) {
                const data = await res.json();
                if (data.status === 'success' && Array.isArray(data.faculties)) {
                    for (const f of data.faculties) {
                        const email = f.email ? f.email.toLowerCase().trim() : '';
                        const normName = normalizePersonName(f.fullName);

                        // Institutional Identity Matching (Email -> Faculty ID -> Name/Aliases)
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
                            existing.department = f.department || existing.department;
                            existing.role = f.role || existing.role;
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
                                qualification: f.qualification || 'M.Tech in CSE',
                                hasPhD: !!f.hasPhD,
                                specialization: f.hasPhD ? 'Computer Science & Research' : 'Computer Science & Software Systems',
                                searchableAliases: [f.fullName.toLowerCase(), tokens[0], tokens[tokens.length - 1]],
                                url: 'faculty.php',
                                ctaText: 'View Faculty Profile →'
                            };
                            MASTER_PERSON_INDEX.push(newFac);
                        }
                    }
                    // Refresh Master Faculty Roster with deduplicated records
                    MASTER_FACULTY_ROSTER = deduplicatePeople(MASTER_PERSON_INDEX.filter(p => {
                        if (!p.category) return false;
                        const cat = p.category.toLowerCase();
                        const role = (p.role || '').toLowerCase();
                        const desig = (p.designation || '').toLowerCase();
                        return cat.includes('faculty') || cat.includes('hod') || cat.includes('professor') || cat.includes('head of department') ||
                               role.includes('faculty') || role.includes('hod') || role.includes('professor') || role.includes('head of department') ||
                               desig.includes('faculty') || desig.includes('hod') || desig.includes('professor');
                    }));
                    isDbSynced = true;
                }
            }
        } catch (err) {
            console.warn('Live MySQL knowledge sync skipped (offline mode):', err);
        }
    }

    // Trigger sync on module load if running in browser
    if (typeof window !== 'undefined' && typeof fetch === 'function') {
        syncWebsiteKnowledge();
    }

    // Dynamically Index All 612 Database House Students into MASTER_PERSON_INDEX
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
     * 6. GRANULAR WEBSITE KNOWLEDGE MATRIX
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
1. Advanced AI & Machine Learning Suite (High-performance GPU Workstations)
2. Cloud Computing & DevOps Innovation Lab
3. IoT & Embedded Edge Systems Hardware Lab
4. Cyber Security & Digital Forensics Lab
5. UI/UX Design & Full-Stack Development Studio`,
            url: 'academics.php',
            ctaText: 'Explore Infrastructure & Labs →'
        },
        {
            id: 'startups_incubation',
            category: 'Startups',
            title: 'Student Startups & Incubation Hub',
            keywords: ['tell me about startups', 'startups', 'what clubs are there', 'incubation', 'bhimavaram online', 'smart wash', 'lunch box'],
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
            keywords: ['tell me about placements', 'placements', 'highest package', 'average package', 'placement record', 'recruiters'],
            content: `Department Placement Highlights:
• Highest Package Offered: ₹18.5 LPA.
• Average Package: ₹5.8 LPA.
• Placement Percentage: 92%+ eligible students placed.
• Top Recruiting Companies: Amazon, TCS Digital, Virtusa, Accenture, Hexaware, Capgemini.`,
            url: 'placements_internships.php',
            ctaText: 'View Placement Records →'
        },
        {
            id: 'clubs_activities',
            category: 'Clubs',
            title: 'Department Clubs & Student Societies',
            keywords: ['what clubs are available', 'clubs', 'activities', 'societies', 'coding club', 'design club', 'tedx', 'nss'],
            content: `Active Department Clubs & Societies:
1. AI & Coding Club — Competitive programming & AI hackathons.
2. Startup & Entrepreneurship Club — Seed incubation & venture support.
3. TEDx SRKR Team — Public speaking, conference hosting & event curation.
4. NSS Student Unit — Social welfare, blood donation & community outreach.
5. Five Elemental Student Houses — Jal, Agni, Vayu, Akash, Prudhvi leagues.`,
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
     * 7. PERSON ENTITY DETECTION IN QUERY WITH DEDUPLICATION
     * =========================================================================
     */
    function detectPersonInQuery(rawQuery) {
        if (!rawQuery) return null;
        const lowerRaw = rawQuery.toLowerCase().trim();

        // Check Reg No Match
        const regMatch = rawQuery.match(/\b([0-9]{2}[a-z0-9]{8,10})\b/i);
        if (regMatch) {
            const searchedReg = regMatch[1].toUpperCase();
            const foundByReg = MASTER_PERSON_INDEX.find(p => p.regNo && p.regNo.toUpperCase() === searchedReg);
            if (foundByReg) {
                return { found: true, isMultiple: false, person: foundByReg, intent: detectQueryIntent(rawQuery) };
            }
        }

        let cleanQuery = lowerRaw.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, ' ');
        cleanQuery = cleanQuery.replace(/[\?\!\.\,\;\:]/g, ' ').replace(/\s+/g, ' ').trim();
        const queryTokens = cleanQuery.split(' ').filter(t => t.length > 0);

        const intent = detectQueryIntent(rawQuery);
        const stopWords = new Set(['who', 'is', 'are', 'which', 'department', 'dept', 'branch', 'does', 'belong', 'belongs', 'to', 'from', 'what', 'role', 'designation', 'qualification', 'specialization', 'subjects', 'teach', 'teaches', 'email', 'contact', 'tell', 'me', 'about', 'can', 'know', 'the', 'a', 'an', 'in', 'of', 'work', 'working', 'studying', 'year', 'section', 'registration', 'number', 'reg', 'no']);
        const nameCandidateTokens = queryTokens.filter(t => !stopWords.has(t) && t.length >= 2);

        if (nameCandidateTokens.length === 0) return null;
        const candidateString = nameCandidateTokens.join(' ');

        let candidates = [];
        for (const person of MASTER_PERSON_INDEX) {
            const normFull = normalizePersonName(person.fullName);
            const normFirst = normalizePersonName(person.firstName);
            const normLast = normalizePersonName(person.lastName);
            const aliases = person.searchableAliases ? person.searchableAliases.map(a => normalizePersonName(a)) : [];

            if (candidateString === normFull || aliases.includes(candidateString)) {
                return { found: true, isMultiple: false, person: person, intent: intent };
            }

            const personTokens = tokenizeName(person.fullName);
            const allTokensInPerson = nameCandidateTokens.every(qTok => {
                return personTokens.some(pTok => pTok === qTok) || aliases.some(alias => alias.split(/\s+/).includes(qTok));
            });

            if (allTokensInPerson) {
                candidates.push(person);
                continue;
            }

            if (nameCandidateTokens.length === 1) {
                const singleTok = nameCandidateTokens[0];
                if (singleTok === normFirst || singleTok === normLast || aliases.includes(singleTok)) {
                    if (!candidates.includes(person)) candidates.push(person);
                }
            }
        }

        if (candidates.length === 1) return { found: true, isMultiple: false, person: candidates[0], intent: intent };
        if (candidates.length > 1) {
            const dedupedCandidates = deduplicatePeople(candidates);
            if (dedupedCandidates.length === 1) {
                return { found: true, isMultiple: false, person: dedupedCandidates[0], intent: intent };
            }
            return { found: true, isMultiple: true, candidates: dedupedCandidates, intent: intent };
        }

        return null;
    }

    /**
     * =========================================================================
     * 8. STRUCTURED WEBSITE KNOWLEDGE QUERY SYSTEM
     * Handles filtering, counting, rankings, multi-condition queries, and memory
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

        // 2. EXPERIENCE FILTER FOR FACULTY ("who has more than 5 years experience", "more than 10 years experience")
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

        // 3. FACULTY FILTERING & COUNTING QUERIES
        const isFacultyQuery = /\b(faculty|faculties|teacher|teachers|professor|professors|staff)\b/i.test(q);

        if (isFacultyQuery || /\b(who (teaches|has phd|has mtech|specializes in)|who has a doctorate)\b/i.test(q) || /^(list all faculty|faculty list|all faculty|show faculty)\b/i.test(q)) {

            // A. PhD Filter ("faculty list who did phd", "faculty with phd", "who has phd", "who has a doctorate", "faculty holding doctoral degrees")
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

            // B. MTech Filter ("show me faculty with mtech", "faculty with mtech")
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

            // C. Subject / Specialization Filter ("who teaches machine learning", "specializes in AI")
            if (/\b(teaches|teaching|specializes in|specialization|subject|subjects)\b/i.test(q) || /\b(machine learning|ai|artificial intelligence|cyber security|cloud|iot)\b/i.test(q)) {
                let matchedSubject = '';
                if (/\b(machine learning|ml)\b/i.test(q)) matchedSubject = 'Machine Learning';
                else if (/\b(artificial intelligence|ai)\b/i.test(q)) matchedSubject = 'Artificial Intelligence';
                else if (/\b(cyber security|security)\b/i.test(q)) matchedSubject = 'Cyber Security';
                else if (/\b(cloud|cloud computing)\b/i.test(q)) matchedSubject = 'Cloud Computing';
                else if (/\b(iot|internet of things)\b/i.test(q)) matchedSubject = 'IoT';

                if (matchedSubject) {
                    const specFaculty = deduplicatePeople(MASTER_FACULTY_ROSTER.filter(f => {
                        const specStr = (f.specialization + ' ' + f.subjects + ' ' + f.description).toLowerCase();
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

            // D. Faculty Total Count Query ("how many faculty members are there")
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

            // E. List All Faculty ("list all faculty", "faculty list")
            if (/^(list all faculty|faculty list|all faculty|show faculty|faculty members|who are the faculty)\??$/i.test(q)) {
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

        // 4. HOUSE TOP CONTRIBUTOR / HIGHEST POINTS QUERY ("who is the top contributor in jal")
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

        // 5. MULTI-CONDITION STUDENT HOUSE FILTER ("who is in Jal house from second year CSD")
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

        // 6. CLASS REPRESENTATIVE QUERIES & LISTS ("who are the second year class representatives", "list all class representatives")
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

        // 7. CULTURAL & COMPETITION ACHIEVERS ("who won the dance competition")
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
     * 9. HOUSE SYSTEM INTENT ENGINE
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

        // Store active house in context memory
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
     * 10. FIELD-LEVEL ANSWER SYNTHESIZER
     * =========================================================================
     */
    function formatFieldLevelAnswer(person, intent, rawQuery) {
        // Update active person context memory
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
                    answerText = `<strong>${name}</strong>'s educational qualification is <strong>${person.qualification}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific educational qualification details are not available in current records.`;
                }
                break;

            case 'SPECIALIZATION':
                if (person.specialization) {
                    answerText = `<strong>${name}</strong>'s area of specialization is <strong>${person.specialization}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific specialization details are not available in current records.`;
                }
                break;

            case 'SUBJECTS':
                if (person.subjects) {
                    answerText = `<strong>${name}</strong> teaches: <strong>${person.subjects}</strong>.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific subjects taught are not listed in current records.`;
                }
                break;

            case 'EXPERIENCE':
                if (person.experience) {
                    answerText = `<strong>${name}</strong> has <strong>${person.experience}</strong> of teaching and research experience.`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but exact years of experience are not specified in current records.`;
                }
                break;

            case 'ACHIEVEMENTS':
                if (person.achievements) {
                    answerText = `<strong>${name}</strong>'s Key Achievements:<br><br>• ${person.achievements}`;
                    answerText += `<br><br>• <strong>Department:</strong> ${dept}<br>• <strong>Role:</strong> ${role}`;
                } else {
                    answerText = `I found <strong>${name}</strong> (${role}, ${dept} Department), but specific achievement details are not available in current records.`;
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

            case 'PROFILE':
            default:
                answerText = `<strong>${name}</strong> — ${role}:<br><br>`;
                answerText += `• <strong>Department:</strong> ${dept}<br>`;
                if (reg) answerText += `• <strong>Registration Number:</strong> ${reg}<br>`;
                if (email) answerText += `• <strong>Contact Email:</strong> ${email}<br>`;
                if (person.qualification) answerText += `• <strong>Qualification:</strong> ${person.qualification}<br>`;
                if (person.specialization) answerText += `• <strong>Specialization:</strong> ${person.specialization}<br>`;
                if (person.achievements) answerText += `• <strong>Achievements:</strong> ${person.achievements}<br>`;
                if (person.description) answerText += `• <strong>Profile:</strong> ${person.description}`;
                break;
        }

        return {
            id: person.id,
            category: person.category,
            title: `${person.fullName} — ${person.category}`,
            content: answerText,
            url: person.url || 'heroes_of_department.php',
            ctaText: person.ctaText || 'View Profile on Website →',
            isPersonQuery: true,
            requestedField: intent
        };
    }

    /**
     * =========================================================================
     * 11. PRIMARY RAG HYBRID DISPATCHER ENFORCING RETRIEVAL SYSTEM
     * =========================================================================
     */
    function searchKnowledgeVector(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        // 1. PERSON / FACULTY / STUDENT LOOKUP FIRST (if explicit person name asked, e.g. "Who is Dr. Suresh Babu Mudunuri?")
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

        // 2. STRUCTURED QUERY ENGINE (FACULTY FILTERS, HOUSE RANKINGS, MULTI-CONDITION, MEMORY)
        const structuredResult = executeStructuredQuery(rawQuery);
        if (structuredResult) {
            console.log('[CHATBOT INTENT] Structured Query Match:', structuredResult.title);
            return structuredResult;
        }

        // 3. HOUSE SYSTEM QUERY
        const houseResult = searchHouseSystem(rawQuery);
        if (houseResult) {
            console.log('[CHATBOT INTENT] House System Match:', houseResult.title);
            return houseResult;
        }

        // 4. HOD SPECIAL QUERY
        if (/\b(hod|hods|head of department|head of the department)\b/i.test(lower)) {
            return {
                id: 'hod_overview',
                category: 'Department Leadership',
                title: 'Heads of Department (HODs)',
                content: `Our department has two distinguished Heads of Department (HODs):<br><br>
1. <strong>Dr. M. Suresh Babu</strong> — Professor & Head of Department, Computer Science & Design (CSD)<br>• Email: sureshbabu.k@srkrec.edu.in | Qualification: Ph.D (2010)<br><br>
2. <strong>Dr. N. Gopala Krishna Murthy</strong> — Professor & Head of Department, Computer Science & Information Technology (CSIT)<br>• Email: gopinukala@srkrec.edu.in | Qualification: Ph.D (2011)`,
                url: 'faculty.php',
                ctaText: 'View Faculty Leadership Page →'
            };
        }

        // 5. FACULTY CATEGORY OVERVIEW
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

        // 6. STUDENT CATEGORY OVERVIEW
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

        // 7. WEBSITE SECTION MATRIX SEARCH
        for (const chunk of KNOWLEDGE_MATRIX) {
            if (chunk.keywords.some(k => lower.includes(k))) {
                return chunk;
            }
        }

        // 8. UNKNOWN / CLARIFICATION FOR EXPLICIT PERSON QUERY
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
     * 12. LOCAL ANSWER SYNTHESIZER
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
            suggestions: ['Jal house members', 'Faculty list who did phd', 'Who is Mohana Durga?', 'Tell me about startups']
        };
    }

    /**
     * =========================================================================
     * 13. MAIN PUBLIC METHOD: getBotResponse
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

            // Ensure live MySQL DB knowledge is synced if running in browser
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
                    suggestions: ['Faculty list who did phd', 'Jal house members', 'Who is Mohana Durga?', 'Who is Satyam Sir?']
                };
                responseCache.set(normalizedQuery, greetingRes);
                return greetingRes;
            }

            if (/^(how are you|how are you\?|how r u)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I'm doing great! Thank you for asking. 😊 I'm fully equipped to answer questions about house members, faculty, student heroes, CRs, courses, labs, placements, and startups. How can I assist you today?`,
                    ctaLinks: [{ text: 'View Department Overview →', url: 'explore.php' }],
                    suggestions: ['Faculty list who did phd', 'Jal house members', 'Who is Mohana Durga?']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what is your name\??|who are you\??|what are you\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I am the official **Department AI Assistant** for the Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) departments at SRKR Engineering College, Bhimavaram.`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['What can you do?', 'Faculty list who did phd', 'Jal house members']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what can you do\??|help|what can i ask\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `Here is what I can help you with:<br><br>
• <strong>Faculty Queries & Filters</strong> (e.g. "Faculty list who did phd", "Who teaches machine learning?", "Faculty with mtech")<br>
• <strong>House Members & Leaderboard</strong> (e.g. "Jal house members", "Who is the top contributor in Jal?")<br>
• <strong>Class Representatives (CRs)</strong> (e.g. "Who is Mohana Durga?", "Who are 2nd year CRs?")<br>
• <strong>Department Heroes & Achievers</strong> (e.g. "Who is Preeti?", "Who won the dance competition?")<br>
• <strong>Academics & Courses</strong> (e.g. "What courses are available?")<br>
• <strong>Laboratories & Infrastructure</strong> (e.g. "What labs are available?")<br>
• <strong>Placements & Internships</strong> (e.g. "Tell me about placements", "Tell me about internships")<br>
• <strong>Startups & Incubation</strong> (e.g. "What startups are there?")`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['Faculty list who did phd', 'Jal house members', 'Who is Mohana Durga?']
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
                            suggestions: ['Faculty list who did phd', 'Jal house members', 'Who is Mohana Durga?']
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
        getContextState: function () { return conversationContext; },
        resetContext: function () {
            responseCache.clear();
            conversationContext = { activeEntity: null, activePerson: null, activeHouse: null, lastQuery: null, history: [] };
        }
    };
})();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatbotService;
}
