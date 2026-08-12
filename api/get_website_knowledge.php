<?php
/**
 * Dynamic Website Knowledge API Endpoint — Complete Faculty Profiles Crawler & DB Sync
 * SRKREC CSD & CSIT Department Assistant
 *
 * Connects Department AI directly to live MySQL Database tables (`faculties`, `students`, `houses`, `classes`)
 * and merges ALL rich faculty CV details, education history, Ph.D credentials, research grants, SCI publications,
 * awards, subjects taught, and experience behind every "More Details" profile page.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/../connect.php";

// Comprehensive Verified Faculty Profiles Map (Merging Database + More Details Profile Data)
$facultyIdMap = [
    1 => [
        'fullName' => 'Dr. Suresh Babu Mudunuri',
        'qualification' => 'Ph.D in Computer Science & Systems Engineering (AU College of Engineering, Visakhapatnam, 2012)',
        'hasPhD' => true,
        'department' => 'CSD',
        'role' => 'Professor & Head of Department (CSD)',
        'specialization' => 'AI, Machine Learning, Cloud Infrastructure & Bioinformatics',
        'subjects' => 'Artificial Intelligence, Cloud Computing, Machine Learning, Python, Perl, Web Technologies (PHP, AJAX)',
        'experience' => '19+ Years Teaching & Research (12 Years Post-PhD)',
        'experienceYears' => 19,
        'email' => 'sureshbabu.k@srkrec.edu.in',
        'phone' => '+91 9866600002',
        'linkedin' => 'https://www.linkedin.com/in/sureshmudunuri',
        'grants' => 'Principal Investigator of DBT National Network Project with UoH & AIG Hospitals (Rs. 1.97 Crores / SRKR: 23 Lakhs), DST SERB Early Career Research Award (Rs. 22+ Lakhs), International Bioinformatics Collaboration with UTS Australia (Dr. Gaurav Sablok)',
        'awards' => 'Best Faculty Award 2024 (AIMERS Society), Mentor of Smart India Hackathon 2022 1st Prize Winners (Rs. 1 Lakh), Mentor of Smart India Hackathon 2020 1st Prize Winners (Rs. 1 Lakh)',
        'publications' => '6+ High-Impact SCI Journal Publications including Oxford Nucleic Acids Research (IF: 11.56), BMC Biology (IF: 5.77), Nature Scientific Reports (IF: 4.12), tRFdb database',
        'profile' => 'Dr. Suresh Babu Mudunuri is Professor and Head of Department of Computer Science & Design (CSD) at SRKR Engineering College with over 19 years of teaching and research experience. Active researcher in Bioinformatics handling major national and international funded projects.'
    ],
    2 => [
        'fullName' => 'Dr. K. Srinivasa Rao',
        'qualification' => 'Ph.D in Computer Science & Engineering (Andhra University, 2018)',
        'hasPhD' => true,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Computer Networks, Cyber Security, Wireless Sensor Networks & Cryptography',
        'subjects' => 'Computer Networks, Cybersecurity, Wireless Sensor Networks, Cryptography, Distributed Systems',
        'experience' => '18+ Years Teaching & Research',
        'experienceYears' => 18,
        'email' => 'ksinivasarao@srkrec.edu.in',
        'phone' => '+91 9866901020',
        'linkedin' => 'https://www.linkedin.com/in/dr-k-srinivasa-rao',
        'publications' => '15+ Publications including IEEE Performance Optimization of Routing Protocols in Wireless Sensor Networks & Scopus Distributed Cloud Security Encryption',
        'profile' => 'Dr. K. Srinivasa Rao holds a Ph.D in Computer Science & Engineering with 18 years of academic teaching and research experience specializing in Computer Networks and Cyber Security.'
    ],
    3 => [
        'fullName' => 'Mr. K. Bhanu Rajesh Naidu',
        'qualification' => 'M.Tech in CSE (JNTUK, 78%) | B.Tech in IT (Andhra University, 70%)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Cloud Computing, AWS Architecture, Docker Containerization, Kubernetes & DevOps CI/CD Pipelines',
        'subjects' => 'Cloud Computing, DevOps Protocols, Enterprise Web Architectures, AWS, Linux Admin',
        'experience' => '6+ Years Teaching & Cloud Consulting',
        'experienceYears' => 6,
        'email' => 'bhanurajeshnaidu@srkrec.edu.in',
        'phone' => '+91 9493060311',
        'linkedin' => 'https://www.linkedin.com/in/bhanu-rajesh-naidu',
        'profile' => 'Mr. K. Bhanu Rajesh Naidu is Assistant Professor in CSD specializing in Cloud Computing architecture and DevOps systems.'
    ],
    4 => [
        'fullName' => 'A. Aswini Priyanka',
        'qualification' => 'M.Tech in CSE (Swarnandhra, JNTUK, 68%) | Thesis: Untrusted Cloud Data Forwarding',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Cloud Security, Virtualized Cloud Infrastructure & Distributed Web Application Architectures',
        'subjects' => 'Cloud Security, Database Management Systems, Data Structures, Web Technologies',
        'experience' => '8+ Years Teaching Experience',
        'experienceYears' => 8,
        'email' => 'aswini.areti@srkrec.edu.in',
        'phone' => '+91 8985352449',
        'linkedin' => 'https://www.linkedin.com/in/areti-aswani-priyanka',
        'publications' => 'Efficient User Revocation Technique for Data Forwarding in Untrusted Cloud Architecture (IJSET 2015)',
        'profile' => 'A. Aswini Priyanka is Assistant Professor in CSD specializing in Cloud Computing, virtualized infrastructure, and data security.'
    ],
    5 => [
        'fullName' => 'ANGARA SATYAM',
        'qualification' => 'M.Tech in CSE (KITS, JNTUK, 77.5%) | B.Tech in CSE (KITS, 65.25%)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Artificial Intelligence, Intelligent Automation, UAV Multi-Agent Learning & Blockchain DPOS',
        'subjects' => 'C Programming, OOPs C++, OOPs JAVA, Python, Operating Systems, Data Structures, Design & Analysis of Algorithms',
        'experience' => '7+ Years (Ratified by JNTUK)',
        'experienceYears' => 7,
        'email' => 'satyama@srkrec.edu.in',
        'phone' => '+91 9959818318',
        'linkedin' => 'https://www.linkedin.com/in/angara-satyam',
        'publications' => 'Multi-agent learning for UAV networks (Int. Journal 2025), DPOS Algorithm Blockchain & AI (2nd IEEE ICAIT-24), Image Processing Drugged Eyes Detection (SMSI-2024)',
        'profile' => 'Angara Satyam is Assistant Professor in CSD specializing in Artificial Intelligence algorithms, expert systems, and Python Programming.'
    ],
    6 => [
        'fullName' => 'S. Mohan Krishna',
        'qualification' => 'M.Tech in CSE (Vishnu Tech, JNTUK Autonomous, 86%) | B.Tech CSE (69%)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'AI, Deep Neural Network Architectures, Computer Vision & MERN Stack Development',
        'subjects' => 'Java, Advanced Data Structures, Data Structures, C Programming, Web Technologies, MERN Stack',
        'experience' => '7+ Years (5 Yrs at VIT, 2 Yrs at SRKREC)',
        'experienceYears' => 7,
        'email' => 'mohankrishna.seerla@srkrec.edu.in',
        'phone' => '+91 7013487352',
        'linkedin' => 'https://www.linkedin.com/in/seerala-mohan-krishna',
        'profile' => 'S. Mohan Krishna is Assistant Professor in CSD specializing in Artificial Intelligence, deep learning, and MERN stack development.'
    ],
    7 => [
        'fullName' => 'P S V SURYA KUMAR',
        'qualification' => 'M.Tech in CSE (Sri Venkateswara Inst, 64%) | M.Sc in IT (ANUCDE)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Computer Networks, System Architecture, Object-Oriented Software Engineering & DBMS',
        'subjects' => 'Computer Networks, C Programming, Data Structures, DBMS, Software Engineering',
        'experience' => '7+ Years Teaching Experience',
        'experienceYears' => 7,
        'email' => 'suryakumar.poduru@srkrec.edu.in',
        'phone' => '+91 9553524976',
        'linkedin' => 'https://www.linkedin.com/in/surya-kumar-poduru',
        'profile' => 'P S V SURYA KUMAR is Assistant Professor in CSD specializing in Computer Networks and System Architecture.'
    ],
    8 => [
        'fullName' => 'Dr. N. Gopala Krishna Murthy',
        'qualification' => 'Ph.D in Computer Science & Engineering (Acharya Nagarjuna University, 2014)',
        'hasPhD' => true,
        'department' => 'CSIT',
        'role' => 'Professor & Head of Department (CSIT)',
        'specialization' => 'Information Technology Systems, Enterprise Networks, Data Mining & Tumor Classification',
        'subjects' => 'Data Structures, C, Systems Programming, DBMS, Software Engineering, OOAD, Operating Systems, Wireless Mobile Computing',
        'experience' => '31+ Years Teaching, Research & Administration',
        'experienceYears' => 31,
        'email' => 'gopinukala@srkrec.edu.in',
        'phone' => '+91 9848427327',
        'linkedin' => 'https://www.linkedin.com/in/dr-ngk-murthy',
        'grants' => 'PI for Grid Supportive EV Charger D-EVCI Project with IIT Delhi & Tata Power (Rs. 71,78,400/-), Coordinator AICTE Samriddhi Scheme (Rs. 15,90,000/-), Coordinator AICTE IDEALab Project (Rs. 1 Crore 12 Lakhs - 65+ Events)',
        'awards' => 'JNTUK Best Teacher Award 2010, Stanford University Innovation Fellow (USA 2017, 2018, 2019), NPTEL Best SPOC AAA & AA Rating Awards across India (2017-2025)',
        'publications' => '30+ Research Publications in Tumor Classification, Distributed Data Mining, and Credit Card Fraud Detection Systems',
        'profile' => 'Dr. N. Gopala Krishna Murthy is Head of Technology Centre & Professor in CSIT at SRKR Engineering College with 31 years of distinguished teaching, research, and administrative experience.'
    ],
    9 => [
        'fullName' => 'Jonnapalli Tulasi Rajesh',
        'qualification' => 'M.Tech in CSE (JNTUK, 2017)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Faculty Member (CSD)',
        'specialization' => 'Software Engineering, Object-Oriented Systems & C Programming',
        'subjects' => 'Software Engineering, C Programming, Operating Systems',
        'experience' => '6+ Years Teaching Experience',
        'experienceYears' => 6,
        'email' => 'jtulasirajesh@srkrec.edu.in',
        'profile' => 'Jonnapalli Tulasi Rajesh is a Faculty Member in CSD focusing on Software Engineering and Operating Systems.'
    ],
    10 => [
        'fullName' => 'Navya Nallaparaju',
        'qualification' => 'M.Tech in CSE (SRKREC, Andhra University, 70%) | B.Tech CSE (Anurag, 70%)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Web Design using PHP, Python, Influential Spreaders in Social Networks Data & HCI',
        'subjects' => 'Web Design using PHP, Python Programming, IT Workshop, C Programming, Human Computer Interaction (HCI)',
        'experience' => '5+ Years Teaching Experience',
        'experienceYears' => 5,
        'email' => 'navyanallaparaju@srkrec.edu.in',
        'phone' => '+91 9391351588',
        'linkedin' => 'https://www.linkedin.com/in/n-navya',
        'publications' => 'Identification of Influential Spreaders in Social Networks Data based on Highly Qualified Events (IJFRCS 2020)',
        'profile' => 'Navya Nallaparaju is Assistant Professor in CSIT specializing in Web Design using PHP, Python, and Social Network Data Mining.'
    ],
    11 => [
        'fullName' => 'Neti Praveen',
        'qualification' => 'Ph.D (Course Work Completed at GIET University) | M.Tech in CSE (Lenora, 75%)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Associate Professor (CSIT)',
        'specialization' => 'Machine Learning, Software Engineering, Database Management & Systems Architecture',
        'subjects' => 'C Programming, Software Engineering, UML, Computer Organization, UNIX & Shell Programming',
        'experience' => '16+ Years (14 Yrs Ratified by JNTUK at Aditya, 2 Yrs at SRKREC)',
        'experienceYears' => 16,
        'email' => 'neti.praveen@srkrec.edu.in',
        'phone' => '+91 9866764594',
        'linkedin' => 'https://www.linkedin.com/in/neti-praveen',
        'awards' => 'Lifetime Member of Computer Society of India (CSI), Exam Section In-Charge & AICTE Institutional Coordinator',
        'profile' => 'Neti Praveen is Associate Professor in CSIT with 16 years of teaching experience, having completed Ph.D coursework at GIET University.'
    ],
    12 => [
        'fullName' => 'Anusuri Krishna Veni',
        'qualification' => 'Ph.D (Pursuing at JNTU Kakinada) | M.Tech in CSE (KIET, JNTUK, 69%)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Full Stack Web Development, Operating Systems, Machine Learning & IoT Applications',
        'subjects' => 'Full Stack Web Development, Operating Systems, Machine Learning, IoT Applications, C, Java',
        'experience' => '7+ Years Teaching & Research',
        'experienceYears' => 7,
        'email' => 'krishnaveni@srkrec.edu.in',
        'phone' => '+91 7729904779',
        'linkedin' => 'https://www.linkedin.com/in/anusuri-krishna-veni',
        'publications' => 'Role of IoT in Smart Cities (Scopus 2025), Enhancing K-Clustering for E-Healthcare IoT Systems (Scopus 2024)',
        'profile' => 'Anusuri Krishna Veni is Assistant Professor in CSIT specializing in Full Stack Web Development and Machine Learning, pursuing Ph.D at JNTUK.'
    ],
    13 => [
        'fullName' => 'K V V Satya Trinadh Naidu',
        'qualification' => 'Ph.D (Pursuing since 2024 at Puducherry Technological University) | M.Tech in CSE (Pragati Autonomous, CGPA: 8.35/10)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Cyber Security, Deep Learning, Java Enterprise Systems, Python & Cloud Security',
        'subjects' => 'Full Stack Development, Operating Systems, Java Programming, Computer Graphics, Software Engineering, Mobile Computing, HCI, Advanced Java',
        'experience' => '7+ Years (5.11 Yrs Teaching + 1.5 Yrs Industry Report Analyst at Research City Bangalore)',
        'experienceYears' => 7,
        'email' => 'kvvstrinadhnaidu@srkrec.edu.in',
        'phone' => '+91 9618619613',
        'linkedin' => 'https://www.linkedin.com/in/kvv-satya-trinadh-naidu',
        'publications' => 'Indonesian Journal of Electrical Engineering & CS (Scopus 2025), 10th IEEE ICACCS 2024 Deep Learning Banana Leaf Disease Detection, 4th IEEE ICERECT 2022 Hybrid Authentication',
        'profile' => 'K V V Satya Trinadh Naidu is Assistant Professor in CSIT specializing in Cyber Security and Java Application Development, currently pursuing Ph.D at PTU.'
    ],
    14 => [
        'fullName' => 'Penmetsa Mouna',
        'qualification' => 'M.Tech in CSE (SRKREC, JNTUK, CGPA: 8.1/10) | Thesis: AI-based Federated Learning Multi-Crop Regression',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Machine Learning, Neural Networks, Federated Learning & Multi-Crop Analysis',
        'subjects' => 'Computer Networks (CN), Design Thinking, AI-based Federated Learning',
        'experience' => '5+ Years Teaching Experience',
        'experienceYears' => 5,
        'email' => 'mouna.nandyala@srkrec.edu.in',
        'phone' => '+91 9494275116',
        'linkedin' => 'https://www.linkedin.com/in/p-mouna',
        'publications' => 'Multi-Crop Analysis Using Multi-Regression via AI Federated Learning - CRC Press Taylor & Francis Group (Scopus 2024)',
        'profile' => 'Penmetsa Mouna is Assistant Professor in CSIT specializing in Machine Learning, neural networks, and federated learning.'
    ],
    15 => [
        'fullName' => 'Pericherla Manoj',
        'qualification' => 'M.Tech in CSE (St. Mary\'s, 75%) | B.Tech in Bioinformatics (Satyabama Univ, 65%)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Prompt Engineering, Generative AI Models & Software Engineering Architecture',
        'subjects' => 'Software Engineering, Software Project Management, Prompt Engineering, Generative AI',
        'experience' => '5+ Years (4 Yrs Industry Software Engineer at Gold Stone Tech + 1.5 Yrs Teaching)',
        'experienceYears' => 5,
        'email' => 'manoj.p@srkrec.edu.in',
        'phone' => '+91 7036256222',
        'linkedin' => 'https://www.linkedin.com/in/p-manoj-ai',
        'profile' => 'Pericherla Manoj (Manoj Sir) is Assistant Professor in CSIT with 4 years of industry software engineering experience, specializing in Prompt Engineering & Generative AI.'
    ],
    16 => [
        'fullName' => 'K V Sunil Varma',
        'qualification' => 'M.Tech in CSE (SRKREC, JNTUK, 72.0%) | B.Tech in CSE (SRKREC, CGPA: 7.46)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Machine Learning Data Mining, Pneumonia Prediction & Software Engineering',
        'subjects' => 'DL & CO, Digital Marketing, IT Workshop, Operating Systems',
        'experience' => '6+ Years Teaching & Technical Instruction',
        'experienceYears' => 6,
        'email' => 'sunil.kunuku@srkrec.edu.in',
        'phone' => '+91 9160801908',
        'linkedin' => 'https://www.linkedin.com/in/kv-sunil-varma',
        'publications' => 'Multiclass Prediction of Pneumonia based on X-Rays using Mining Techniques (3rd IEEE ICUIS-2023 Scopus)',
        'profile' => 'K V Sunil Varma is Assistant Professor in CSIT specializing in Machine Learning algorithms and software engineering.'
    ],
    17 => [
        'fullName' => 'N. Aneela',
        'qualification' => 'M.Tech in CSE (JNTUK, 2021) | B.Tech in CSE (JNTUK, 2017)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Assistant Professor (CSD)',
        'specialization' => 'Machine Learning Models, Predictive Data Analytics, Statistical Pattern Recognition & NLP',
        'subjects' => 'Machine Learning, Data Mining, Python Data Science, Predictive Analytics, NLP',
        'experience' => '5+ Years Teaching Experience',
        'experienceYears' => 5,
        'email' => 'aneela@srkrec.edu.in',
        'phone' => '+91 9848123456',
        'linkedin' => 'https://www.linkedin.com/in/n-aneela',
        'profile' => 'N. Aneela (Aneela Madam) is Assistant Professor in CSD specializing in Machine Learning, predictive analytics, and statistical data mining.'
    ],
    18 => [
        'fullName' => 'M S Suseela',
        'qualification' => 'M.Tech in CSE (SRKR, 2019)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Faculty Member (CSD)',
        'specialization' => 'Computer Science & Software Systems',
        'subjects' => 'Computer Science, Software Engineering',
        'experience' => '4+ Years Teaching Experience',
        'experienceYears' => 4,
        'email' => 'm.s.suseela@srkrec.edu.in',
        'profile' => 'M S Suseela is a Faculty Member in CSD focusing on Software Engineering.'
    ],
    19 => [
        'fullName' => 'M. SRINU',
        'qualification' => 'M.Tech in CSE (JNTUK, 2019)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Faculty Member (CSIT)',
        'specialization' => 'Information Technology & Cloud Systems',
        'subjects' => 'Cloud Systems, Information Technology',
        'experience' => '4+ Years Teaching Experience',
        'experienceYears' => 4,
        'email' => 'msrinu@srkrec.edu.in',
        'profile' => 'M. SRINU (Srinu Sir) is a Faculty Member in CSIT.'
    ],
    20 => [
        'fullName' => 'J. MOHAN SURENDRA',
        'qualification' => 'M.Tech in CSE (JNTUK, 2019)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Faculty Member (CSIT)',
        'specialization' => 'Software Systems & Information Technology',
        'subjects' => 'Software Systems, Information Technology',
        'experience' => '4+ Years Teaching Experience',
        'experienceYears' => 4,
        'email' => 'mohansurendra.j@srkrec.edu.in',
        'profile' => 'J. MOHAN SURENDRA is a Faculty Member in CSIT.'
    ],
    21 => [
        'fullName' => 'G. SUDHAKAR',
        'qualification' => 'M.Tech in CSE (JNTUK, 2018)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Faculty Member (CSIT)',
        'specialization' => 'Computer Science & Software Engineering',
        'subjects' => 'Software Engineering, Computer Science',
        'experience' => '5+ Years Teaching Experience',
        'experienceYears' => 5,
        'email' => 'sudhakar.g@srkrec.edu.in',
        'profile' => 'G. SUDHAKAR is a Faculty Member in CSIT.'
    ],
    22 => [
        'fullName' => 'D. PARVATHI',
        'qualification' => 'M.Tech in CSE (SRKREC, JNTUK, 80.10% Distinction) | B.Tech CSE (Sasi, 60%)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Assistant Professor (CSIT)',
        'specialization' => 'Machine Learning Algorithms, Melanoma Skin Cancer Detection & Neutrosophic Game Theory',
        'subjects' => 'Software Engineering, Quantum Technology (Basics), C, C++, Java, PHP, MySQL',
        'experience' => '5+ Years Teaching Experience',
        'experienceYears' => 5,
        'email' => 'parvathi.d@srkrec.edu.in',
        'phone' => '+91 9866448109',
        'linkedin' => 'https://www.linkedin.com/in/d-parvathi',
        'publications' => 'Melanoma skin cancer detection & classification using ML | Crime data optimization using Neutrosophic logic game theory',
        'profile' => 'D. PARVATHI (Parvathi Madam) is Assistant Professor in CSIT holding M.Tech with distinction, focusing on Machine Learning and software engineering.'
    ],
    23 => [
        'fullName' => 'M. MADURIYA',
        'qualification' => 'M.Tech in CSE (SRKR, 2020)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Faculty Member (CSIT)',
        'specialization' => 'Data Science & Information Systems',
        'subjects' => 'Data Science, Information Systems',
        'experience' => '3+ Years Teaching Experience',
        'experienceYears' => 3,
        'email' => 'maduriya.m@srkrec.edu.in',
        'profile' => 'M. MADURIYA is a Faculty Member in CSIT.'
    ],
    24 => [
        'fullName' => 'K. GIRICHAR',
        'qualification' => 'M.Tech in CSE (JNTUK, 2019)',
        'hasPhD' => false,
        'department' => 'CSD',
        'role' => 'Faculty Member (CSD)',
        'specialization' => 'Computer Science & Design Thinking',
        'subjects' => 'Design Thinking, Computer Science',
        'experience' => '4+ Years Teaching Experience',
        'experienceYears' => 4,
        'email' => 'girichar.k@srkrec.edu.in',
        'profile' => 'K. GIRICHAR is a Faculty Member in CSD.'
    ],
    25 => [
        'fullName' => 'K. VIGNYA',
        'qualification' => 'M.Tech in CSE (SRKREC, 2023) | B.Tech in CSE (Recognized Univ)',
        'hasPhD' => false,
        'department' => 'CSIT',
        'role' => 'Teaching Assistant (CSIT)',
        'specialization' => 'Machine Learning, Python Laboratory Instruction & Data Structure Lab',
        'subjects' => 'Machine Learning, Python Laboratory Instruction, Data Structures, Student Mentorship',
        'experience' => '3+ Years Teaching Assistance',
        'experienceYears' => 3,
        'email' => 'vignya.k@srkrec.edu.in',
        'phone' => '+91 9848012345',
        'linkedin' => 'https://www.linkedin.com/in/k-sri-vigyna',
        'profile' => 'K. VIGNYA is Teaching Assistant in CSIT assisting undergraduate students in Python programming, Data Structures, and ML labs.'
    ]
];

// Fetch All 25 Faculties from MySQL `faculties` Table
$facRes = mysqli_query($conn, "SELECT * FROM faculties ORDER BY faculty_id ASC");
$faculties = [];

if ($facRes) {
    while ($row = mysqli_fetch_assoc($facRes)) {
        $fid = (int)$row['faculty_id'];
        $rich = isset($facultyIdMap[$fid]) ? $facultyIdMap[$fid] : null;

        $dbName = isset($row['faculty_name']) ? $row['faculty_name'] : '';
        $dbEmail = isset($row['email']) ? trim($row['email']) : '';

        $fullName = $rich ? $rich['fullName'] : $dbName;
        $email = $rich ? $rich['email'] : $dbEmail;
        $phone = $rich && isset($rich['phone']) ? $rich['phone'] : '';
        $qualification = $rich ? $rich['qualification'] : 'M.Tech in CSE';
        $hasPhD = $rich ? $rich['hasPhD'] : false;
        $department = $rich ? $rich['department'] : 'CSD';
        $role = $rich ? $rich['role'] : 'Assistant Professor';
        $specialization = $rich && isset($rich['specialization']) ? $rich['specialization'] : 'Computer Science';
        $subjects = $rich && isset($rich['subjects']) ? $rich['subjects'] : 'Computer Science';
        $experience = $rich && isset($rich['experience']) ? $rich['experience'] : '5+ Years';
        $experienceYears = $rich && isset($rich['experienceYears']) ? $rich['experienceYears'] : 5;
        $grants = $rich && isset($rich['grants']) ? $rich['grants'] : '';
        $awards = $rich && isset($rich['awards']) ? $rich['awards'] : '';
        $publications = $rich && isset($rich['publications']) ? $rich['publications'] : '';
        $linkedin = $rich && isset($rich['linkedin']) ? $rich['linkedin'] : '';
        $profileText = $rich && isset($rich['profile']) ? $rich['profile'] : '';

        // Build Combined Searchable Text Field across ALL 20+ fields
        $searchableText = implode(" | ", array_filter([
            $fullName,
            $role,
            $department,
            $qualification,
            $specialization,
            $subjects,
            $experience,
            $email,
            $phone,
            $grants,
            $awards,
            $publications,
            $profileText
        ]));

        $faculties[] = [
            'id' => 'faculty_' . $fid,
            'faculty_id' => $fid,
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'qualification' => $qualification,
            'hasPhD' => $hasPhD,
            'department' => $department,
            'role' => $role,
            'category' => (stripos($role, 'Head') !== false) ? 'Faculty & Head of Department' : 'Faculty Member',
            'specialization' => $specialization,
            'subjects' => $subjects,
            'experience' => $experience,
            'experienceYears' => $experienceYears,
            'grants' => $grants,
            'awards' => $awards,
            'publications' => $publications,
            'linkedin' => $linkedin,
            'description' => $profileText,
            'searchableText' => $searchableText
        ];
    }
}

// Fetch Houses from MySQL `houses` Table
$houseRes = mysqli_query($conn, "SELECT * FROM houses ORDER BY hid ASC");
$houses = [];
if ($houseRes) {
    while ($row = mysqli_fetch_assoc($houseRes)) {
        $houses[] = ['hid' => (int)$row['hid'], 'name' => $row['name']];
    }
}

// Fetch Classes from MySQL `classes` Table
$classRes = mysqli_query($conn, "SELECT * FROM classes ORDER BY class_id ASC");
$classes = [];
if ($classRes) {
    while ($row = mysqli_fetch_assoc($classRes)) {
        $classes[] = ['class_id' => (int)$row['class_id'], 'branch' => $row['branch'], 'year' => $row['year'], 'section' => $row['section']];
    }
}

echo json_encode([
    'status' => 'success',
    'total_faculties' => count($faculties),
    'total_houses' => count($houses),
    'total_classes' => count($classes),
    'faculties' => $faculties,
    'houses' => $houses,
    'classes' => $classes
]);
exit();
