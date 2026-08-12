/**
 * AI Department Assistant — Google Gemini API & Scalable Hybrid RAG Engine
 * SRKREC CSD & CSIT Departments
 *
 * Robust & Generalized Retrieval Engine:
 * 1. Intent Classification Engine (PROFILE, DEPARTMENT, BRANCH, ROLE, YEAR, SECTION, REG_NO, etc.)
 * 2. Generalized Person Extraction & Multi-Priority Search (Full Name -> Normalized Name -> Unique First Name -> Unique Last Name -> Reg No)
 * 3. Structured Knowledge Base (650+ Persons Indexed: Faculty, Heroes, CRs, House Members)
 * 4. Person-First Field-Level Precision Answer Synthesizer (No list dumps for single person questions)
 * 5. Synonym Resolution (CR / CRs / Class Representative / Class Reps)
 * 6. Preserved Faculty Matching (Satyam Sir, Trinadh Sir, HODs)
 */

const ChatbotService = (function () {
    'use strict';

    let userApiKey = null;
    let isProcessingRequest = false;

    // Multi-turn Conversation Memory State
    let conversationContext = {
        activeEntity: null,
        activeTopic: null,
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
        s = s.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, '');
        s = s.replace(/[\?\!\.\,\;\:]/g, '');
        return s.replace(/\s+/g, ' ').trim();
    }

    function tokenizeName(str) {
        if (!str) return [];
        let clean = normalizePersonName(str);
        clean = clean.replace(/\b(who|is|are|tell|me|about|give|details|of|show|profile|the|a|an|registration|number|reg|no|which|what|belong|belongs|from|studying|branch|department|role)\b/g, '');
        return clean.split(/\s+/).filter(t => t.length > 0);
    }

    /**
     * =========================================================================
     * 2. INTENT CLASSIFICATION & CANDIDATE NAME EXTRACTION
     * =========================================================================
     */
    function detectQueryIntent(rawQuery) {
        if (!rawQuery) return 'PROFILE';
        const q = rawQuery.toLowerCase();

        if (/\b(which department|what department|belong to|belongs to|department of|which dept|what dept|dept is|dept of)\b/i.test(q)) {
            return 'DEPARTMENT';
        }
        if (/\b(which branch|what branch|branch is|branch of|branch from)\b/i.test(q)) {
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

    function extractCandidateName(rawQuery) {
        if (!rawQuery) return '';
        let clean = rawQuery.trim();

        // Strip leading phrase patterns
        clean = clean.replace(/^(can i know|do you know|can you tell me|please tell me|tell me|who is|who are|who's|details of|details about|info on|info about|information about|profile of|which department does|which branch is|what is|where is|is|about)\s+/i, '');
        // Strip trailing phrase patterns
        clean = clean.replace(/\s+(belong to|belongs to|from|studying in|studying|work in|working in|working|teach|teaching|teach in|teach at)\??$/i, '');
        clean = clean.replace(/\s+(belong|belongs|from)\??$/i, '');
        clean = clean.replace(/\s*(department|dept|branch|role|designation|year|section|registration number|reg no|achievements|email|contact)\s*$/i, '');
        clean = clean.replace(/[\?\!\.\,\;\:]/g, '');

        return normalizePersonName(clean);
    }

    /**
     * =========================================================================
     * 3. MASTER HOUSE ROSTER ENGINE (612 VERIFIED HOUSE MEMBERS FROM DATABASE)
     * =========================================================================
     */
    const MASTER_HOUSE_ROSTER = {
        'JAL': {
            name: 'Jal',
            description: 'Water House - Flowing with wisdom and adaptability like the eternal river.',
            members: [{"name":"ABDUL SHARIFUNNISA","regNo":"N/A","section":"A"},{"name":"ARETI JAYA CHARAN KRISHNA","regNo":"N/A","section":"B"},{"name":"BANDE DALI AKSHAYA","regNo":"N/A","section":"A"},{"name":"BAREPU VAMSI","regNo":"N/A","section":"B"},{"name":"BARRI SRAVYA SREE","regNo":"N/A","section":"A"},{"name":"BEERA YASMIN","regNo":"N/A","section":"A"},{"name":"BEJAVADA V S S N RAMA GANESH","regNo":"N/A","section":"B"},{"name":"BELAMARA SIVANI","regNo":"N/A","section":"A"},{"name":"BELLAPU J S VENKATA DURGA NAGA ASRITHA","regNo":"N/A","section":"A"},{"name":"BODDETI DEVI NAGA VENKATA SAI DEEPAK","regNo":"N/A","section":"A"},{"name":"BODDETI SARVANI","regNo":"N/A","section":"A"},{"name":"BONAM ADI LAKSHAMMA","regNo":"N/A","section":"A"},{"name":"BONIGALA RISHITHA","regNo":"N/A","section":"B"},{"name":"BORRA TERESSA","regNo":"N/A","section":"A"},{"name":"BUDDIGA GAYATRI","regNo":"N/A","section":"A"},{"name":"CHADARAM BHANU VENKATA MANIKANTA","regNo":"N/A","section":"A"},{"name":"CHIKKALA SHYAM KISHORE","regNo":"N/A","section":"B"},{"name":"CHINTADA NISSY SUDEEPTHI","regNo":"N/A","section":"A"},{"name":"CHINTAPALLI NAGA SYAMALA","regNo":"N/A","section":"A"},{"name":"CHITTALA DILEEP RAM KUMAR","regNo":"N/A","section":"A"},{"name":"DAGGU ROHITH SUBRAHMANYA SAI","regNo":"N/A","section":"A"},{"name":"DAMMU PRANEETH KUMAR","regNo":"N/A","section":"A"},{"name":"DODDI NIVEDITHA","regNo":"N/A","section":"A"},{"name":"DODDIPATLA DANA VENKATA SIVASANKAR","regNo":"N/A","section":"A"},{"name":"DOMMETI SAI NIKHITHA","regNo":"N/A","section":"A"},{"name":"DONAVALLI REVATHI","regNo":"N/A","section":"A"},{"name":"DONTHU VIJAYA SRI","regNo":"N/A","section":"A"},{"name":"EUDU HARSHA VARDHAN","regNo":"N/A","section":"A"},{"name":"GANDREDDY RAM GANESH","regNo":"N/A","section":"A"},{"name":"GANESNA SATYA RAJESH","regNo":"N/A","section":"A"},{"name":"GEDDAM JACINTHA","regNo":"N/A","section":"A"},{"name":"GOLLAPALLI ROHAN SAMIT","regNo":"N/A","section":"A"},{"name":"GOPINEEDI DIVIJA","regNo":"N/A","section":"A"},{"name":"GOTTUMUKKALA BHARGAVI","regNo":"N/A","section":"A"},{"name":"INUMARTHI SRINAVYA","regNo":"N/A","section":"A"},{"name":"JADDU LEELA PAVAN KRISHNA","regNo":"N/A","section":"A"},{"name":"JAKKAMPUDI REVANTH","regNo":"N/A","section":"A"},{"name":"JALLI SURENDRA VARMA","regNo":"N/A","section":"A"},{"name":"JOGI PRASANTH KUMAR","regNo":"N/A","section":"A"},{"name":"KACHETTI RUCHITA LAKSHMI","regNo":"N/A","section":"A"},{"name":"KADIYALA NAVYA SRI","regNo":"N/A","section":"A"},{"name":"KANNIPAMULA TEJASWI","regNo":"N/A","section":"B"},{"name":"KAPUDASI SNIGDHA","regNo":"N/A","section":"A"},{"name":"KARIMERAKA DOLLY GANYA","regNo":"N/A","section":"A"},{"name":"KAROTHI SAI MANIKANTA","regNo":"N/A","section":"A"},{"name":"KATIKI RAJANI","regNo":"N/A","section":"A"},{"name":"KETHA SURYA PRAKASH","regNo":"N/A","section":"A"},{"name":"KETHINEDI SRI RAM","regNo":"N/A","section":"A"},{"name":"KODETI SATISH","regNo":"N/A","section":"A"},{"name":"KODI VAISHNAVI","regNo":"N/A","section":"A"},{"name":"KOLA YESWANTH","regNo":"N/A","section":"A"},{"name":"KOSETTI AHARON KUMAR","regNo":"N/A","section":"A"},{"name":"KUKUNOORI POORNA SRI CHANDRA SEKHAR","regNo":"N/A","section":"A"},{"name":"KUNCHE SRI NAGA GANESH","regNo":"N/A","section":"A"},{"name":"KURASALA HARSHA VARDHAN","regNo":"N/A","section":"A"},{"name":"MAILABATTULA LOUKYATHA","regNo":"N/A","section":"A"},{"name":"MALLABATTULA SIVA KRISHNA","regNo":"N/A","section":"A"},{"name":"MANDA RAJA PRASANNA KUMAR","regNo":"N/A","section":"B"},{"name":"MANELLI SRAVANI","regNo":"N/A","section":"A"},{"name":"MATTAPARTHI REETHIKA","regNo":"N/A","section":"A"},{"name":"MOTUPALLI MEENA PHANI SRI","regNo":"N/A","section":"B"},{"name":"MULE ADILAKSHMI","regNo":"N/A","section":"A"},{"name":"MUTCHARLA YASASWI","regNo":"N/A","section":"A"},{"name":"NAGISETTY VISHNUVARDHAN","regNo":"N/A","section":"A"},{"name":"NAKKA MOHITH SRI NAGA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"NALLAM HEMA SAI SRI LAKSHMI","regNo":"N/A","section":"A"},{"name":"NELAPOGULA SRI POSI LAKSHMI","regNo":"N/A","section":"A"},{"name":"NELAPUDI PRASANTH SEKHAR","regNo":"N/A","section":"B"},{"name":"NETHULA MAHESH","regNo":"N/A","section":"B"},{"name":"NOUPADA LIKHITHA","regNo":"N/A","section":"A"},{"name":"PALA THANUJA","regNo":"N/A","section":"B"},{"name":"PANDAVA MEGHANA CHOUDHARY","regNo":"N/A","section":"A"},{"name":"PANKAJ NARAYAN TYADA","regNo":"N/A","section":"A"},{"name":"PASUPULETI JASWANTH RAMANA TEJA","regNo":"N/A","section":"A"},{"name":"PECHETTI LAKSHMI TANUJA","regNo":"N/A","section":"A"},{"name":"PEPETI GANESH","regNo":"N/A","section":"A"},{"name":"PETTA PRANATHI","regNo":"N/A","section":"A"},{"name":"POGIRI BHANU PRASAD","regNo":"N/A","section":"A"},{"name":"PONNAGANTI JYOTHIKA SAI","regNo":"N/A","section":"B"},{"name":"POTHAMSETTI KODANDA RAMA NAGA GANESH","regNo":"N/A","section":"A"},{"name":"REDDI GEETHIKA","regNo":"N/A","section":"A"},{"name":"RELANGI JYOTHSNA SRI","regNo":"N/A","section":"A"},{"name":"SAKHIMSETTI HARI SATYA PRIYA DEVI","regNo":"N/A","section":"B"},{"name":"SAMBANGI VENKATA JASWANTH","regNo":"N/A","section":"A"},{"name":"SARELLA VINCY ANGELINE","regNo":"N/A","section":"A"},{"name":"SETTI NARENDRA KUMAR","regNo":"N/A","section":"A"},{"name":"SHAIK AMEENA","regNo":"N/A","section":"B"},{"name":"SIDDA MAHESH","regNo":"N/A","section":"A"},{"name":"SIRAPARAPU PRANATHI SAI VARSHINI","regNo":"N/A","section":"A"},{"name":"SRIKAKULAPU SANTHI PRIYA","regNo":"N/A","section":"A"},{"name":"SUTHAPALLI SRI PAVAN KRISHNA","regNo":"N/A","section":"A"},{"name":"TAMMA LOKESH","regNo":"N/A","section":"A"},{"name":"TANINKI SREEDHAR","regNo":"N/A","section":"A"},{"name":"THOTA JOHAN BENEDICT","regNo":"N/A","section":"B"},{"name":"TUMMA SRI HARSHA","regNo":"N/A","section":"A"},{"name":"UNDAPALLI DIVYA","regNo":"N/A","section":"A"},{"name":"UTTARILLI HARSHA VARDHAN","regNo":"N/A","section":"B"},{"name":"VAKAPALLI H V SAI SURYA SWAPANTH","regNo":"N/A","section":"A"},{"name":"VAKAPALLI PHANI SAI MUKESH","regNo":"N/A","section":"A"},{"name":"VANAPARTHI ASMITHA VYSHNAVI","regNo":"N/A","section":"B"},{"name":"VASKA JYOTHI","regNo":"N/A","section":"B"},{"name":"VEERANKI MAHESH BABU","regNo":"N/A","section":"A"},{"name":"VEMAVARAPU MADHU SARIKA","regNo":"N/A","section":"A"},{"name":"VENKATA NISHITHA REDDY DATLA","regNo":"N/A","section":"B"},{"name":"YALLA CHANDANA","regNo":"N/A","section":"A"},{"name":"YALLAPU TANUJA","regNo":"N/A","section":"B"},{"name":"YATHAM LAKSHMI PRASANNA","regNo":"N/A","section":"A"}]
        },
        'AGNI': {
            name: 'Agni',
            description: 'Fire House - Burning with passion and illuminating the path forward.',
            members: [{"name":"ADABALA ROHITH VEERA VENKATA DURGESH","regNo":"N/A","section":"B"},{"name":"ADDAGARLA R S S K V V S D N RAJESH","regNo":"N/A","section":"A"},{"name":"AKSHINTALA HARSHATH","regNo":"N/A","section":"A"},{"name":"ALLADI DILEEP KUMAR","regNo":"N/A","section":"A"},{"name":"ATCHUTHUNI SAI SPURANTHI","regNo":"N/A","section":"A"},{"name":"BOKINALA MANJUSHA","regNo":"N/A","section":"A"},{"name":"BOKKA LIKHITHA","regNo":"N/A","section":"A"},{"name":"BOMMI VENKATA SAI","regNo":"N/A","section":"A"},{"name":"BORRA AVINASH","regNo":"N/A","section":"A"},{"name":"BOTCHA AVINASH","regNo":"N/A","section":"B"},{"name":"BURRA MANI CHANDU KUTA RAO","regNo":"N/A","section":"A"},{"name":"CHAMARLAKOTA SIREESH VALI","regNo":"N/A","section":"A"},{"name":"CHELAMKURI LOHITH","regNo":"N/A","section":"B"},{"name":"CHETTU BHAVANA","regNo":"N/A","section":"A"},{"name":"CHIMAKURTHI TEJA RUPAK","regNo":"N/A","section":"A"},{"name":"CHINDADA JYOTHI","regNo":"N/A","section":"A"},{"name":"CHINIMILLI SAJEEVUDU","regNo":"N/A","section":"A"},{"name":"CHINNAM NIKHILESH","regNo":"N/A","section":"A"},{"name":"CHINTAPALLI PREM TEJA","regNo":"N/A","section":"A"},{"name":"CHIRAPA ESWAR VENKATA SATYA NARAYANA","regNo":"N/A","section":"A"},{"name":"CHITAKANA RACHITHA","regNo":"N/A","section":"A"},{"name":"DAIDA RANI","regNo":"N/A","section":"A"},{"name":"DASARI KARTHIKEYA","regNo":"N/A","section":"B"},{"name":"DASARI MOHAN CHANDRA SHEKAR","regNo":"N/A","section":"B"},{"name":"DHANANI SRI LAKSHMI VENKATA AASHRITA","regNo":"N/A","section":"A"},{"name":"DONGA JHANSI","regNo":"N/A","section":"A"},{"name":"DURU MERY SUNEETHA","regNo":"N/A","section":"A"},{"name":"EDA PRASANTH","regNo":"N/A","section":"A"},{"name":"GADDAM CHANDRIKA SRI PRIYA","regNo":"N/A","section":"A"},{"name":"GAYATRI PADHI","regNo":"N/A","section":"A"},{"name":"GEDA HARI SAI","regNo":"N/A","section":"B"},{"name":"GHANTA LIKITHA VENKATA RAGHU SAI","regNo":"N/A","section":"A"},{"name":"GIDUGU NEHANTH SRIHARSHA NAVADEEP","regNo":"N/A","section":"A"},{"name":"GOWTHU LEELA RUKMINI","regNo":"N/A","section":"A"},{"name":"GUBBALA GNAANA PRASANNA","regNo":"N/A","section":"A"},{"name":"GUDAPATI LALITHA DEVI SRI","regNo":"N/A","section":"A"},{"name":"GUDDALA SAI CHARAN","regNo":"N/A","section":"A"},{"name":"GUNDUMOGULA SARUPYA","regNo":"N/A","section":"A"},{"name":"GUTTULA TEJASWI","regNo":"N/A","section":"A"},{"name":"JAKKAMSETTI SANJANI","regNo":"N/A","section":"A"},{"name":"JANAKI MADDALA","regNo":"N/A","section":"A"},{"name":"JOGI ABISHAI","regNo":"N/A","section":"A"},{"name":"KALIGITA SIDDHU","regNo":"N/A","section":"A"},{"name":"KAMIREDDY SRI RAMA CHARAN SARESH KUMAR","regNo":"N/A","section":"B"},{"name":"KANDIBOYINA CHANDRASHEKAR","regNo":"N/A","section":"A"},{"name":"KANUMURI DEEKSHITA","regNo":"N/A","section":"A"},{"name":"KARRI LAKSHMI PRASANNA","regNo":"N/A","section":"A"},{"name":"KAVURU GUNA SRAVANI","regNo":"N/A","section":"A"},{"name":"KILLADA DAVID ENOSH","regNo":"N/A","section":"A"},{"name":"KODE NARASIMHA NAIDU","regNo":"N/A","section":"A"},{"name":"KOLATI STEPHEN SOUDH","regNo":"N/A","section":"A"},{"name":"KOLLATI SAILAJA","regNo":"N/A","section":"A"},{"name":"KOLLI SHANMUKHA SRIRAM CHARAN TEJA","regNo":"N/A","section":"A"},{"name":"KOMARADA KIRAN KISHORE","regNo":"N/A","section":"A"},{"name":"KONDAPALLI SUBHAKAR BHANCY RAJ","regNo":"N/A","section":"A"},{"name":"KOPPARTI HONEY NAGA SANDEEP","regNo":"N/A","section":"A"},{"name":"KORLAPATI GEETHIKA RATNAM","regNo":"N/A","section":"A"},{"name":"KOTAPATI MAHENDRA REDDY","regNo":"N/A","section":"A"},{"name":"LALITHA MANOJNA VELIVELA","regNo":"N/A","section":"A"},{"name":"MADDI AKSHAYA SRI","regNo":"N/A","section":"A"},{"name":"MALLAVARAPU GANGOTHRI","regNo":"N/A","section":"A"},{"name":"MANDAPATI VENKATA YAMINI","regNo":"N/A","section":"A"},{"name":"MANGENA JAHNAVI","regNo":"N/A","section":"A"},{"name":"MEDABALIMI ADITHYA VARDHAN","regNo":"N/A","section":"A"},{"name":"MEDIDI BENNYBABU","regNo":"N/A","section":"A"},{"name":"MOTURI SANDILYA","regNo":"N/A","section":"A"},{"name":"MUNDRI RAKESH","regNo":"N/A","section":"A"},{"name":"MUNGARA LOHITH","regNo":"N/A","section":"A"},{"name":"MURALA NEETHI SURYA","regNo":"N/A","section":"A"},{"name":"MURIKITHA ARCHANA SAI SRI","regNo":"N/A","section":"B"},{"name":"NAKKA SUNISCHAL","regNo":"N/A","section":"A"},{"name":"NANDAMURI BALA SESHA SATYA SRI","regNo":"N/A","section":"A"},{"name":"NANDE D V V SIVA SWAMY ARAVINDH","regNo":"N/A","section":"A"},{"name":"NANDIKA LIKHITHA","regNo":"N/A","section":"A"},{"name":"NARISETTY AKSHAYA NAIDU","regNo":"N/A","section":"A"},{"name":"NELLURI CHAITRIKA SRI NIDHI","regNo":"N/A","section":"B"},{"name":"NIMMALA BHANU SRI HARSHA","regNo":"N/A","section":"B"},{"name":"NUKALA CHARAN JASWANTH","regNo":"N/A","section":"A"},{"name":"NUKALA KAUSHAL","regNo":"N/A","section":"A"},{"name":"OGURI LAKSHMI NARAYANA","regNo":"N/A","section":"B"},{"name":"PACHIGOLLA RISHITHA MANASA SURYA GAYATRI","regNo":"N/A","section":"A"},{"name":"PAMU AMRUTHA","regNo":"N/A","section":"B"},{"name":"PANAKALA RAMA NAGESWARA RAO","regNo":"N/A","section":"A"},{"name":"PENMETSA HARSHINI","regNo":"N/A","section":"B"},{"name":"PENTAKOTA LEELA SRI","regNo":"N/A","section":"A"},{"name":"PENTAPATI HARSHA VARDHAN RAJU","regNo":"N/A","section":"A"},{"name":"PERICHERLA ROHAN KRISHNA VARMA","regNo":"N/A","section":"A"},{"name":"PINNINTI SIVANI","regNo":"N/A","section":"A"},{"name":"PONAMANDI PRASHANTH","regNo":"N/A","section":"A"},{"name":"POSIMSETTY SRI VISWA BHARATH","regNo":"N/A","section":"A"},{"name":"PULAPARTHI KALYAN VENKATA SAI","regNo":"N/A","section":"B"},{"name":"PULI DURGA BHAVANI","regNo":"N/A","section":"A"},{"name":"ROTTE SUSHANTH","regNo":"N/A","section":"B"},{"name":"SAKHINETIPALLI CHAKRI ADITYA PAVAN KUMAR","regNo":"N/A","section":"B"},{"name":"SANA SHANMUKHA DURGA","regNo":"N/A","section":"B"},{"name":"SHAIK DADA KHALANDER","regNo":"N/A","section":"A"},{"name":"SHAIK NAGUR MADEENA BEGAM","regNo":"N/A","section":"B"},{"name":"SIDDAMSETTI VIVEK SAI","regNo":"N/A","section":"B"},{"name":"SIRIPURAPU PARDHA SARADHI","regNo":"N/A","section":"B"},{"name":"SUNKARA KETHAN SAI","regNo":"N/A","section":"A"},{"name":"SUNKARA SWATHI","regNo":"N/A","section":"A"},{"name":"SWARNA GOWTHAMI","regNo":"N/A","section":"B"},{"name":"TADELA SUSMITHA","regNo":"N/A","section":"A"},{"name":"TANGUTURI S V NAGA PAVAN SAI","regNo":"N/A","section":"A"},{"name":"UPPULURI VENKATA JASWANTH","regNo":"N/A","section":"A"},{"name":"VADDIMUKKALA KRANTHI KUMAR","regNo":"N/A","section":"A"},{"name":"VADREVU LAHARI DEVI","regNo":"N/A","section":"B"},{"name":"VALLABHANI SAHITHI","regNo":"N/A","section":"A"},{"name":"VANUKURI SAI BHARADWAJA REDDY","regNo":"N/A","section":"A"},{"name":"VARIKUTI ANJALI","regNo":"N/A","section":"A"},{"name":"VEERAVALLI KUNDANA SAI SANTHI","regNo":"N/A","section":"A"},{"name":"VEERLAPATI HASINI","regNo":"N/A","section":"A"},{"name":"VETCHA G N V S L SAISREE","regNo":"N/A","section":"A"}]
        },
        'VAYU': {
            name: 'Vayu',
            description: 'Wind House - Swift and free like the breeze that carries change.',
            members: [{"name":"A PREETHI","regNo":"N/A","section":"A"},{"name":"ADDAGARLA HEMANTH NAGA MANIKANTA","regNo":"N/A","section":"A"},{"name":"ADDAGARLA SRI VIDYA SAGAR","regNo":"N/A","section":"A"},{"name":"ALAPATI ANASUYA DEVI","regNo":"N/A","section":"A"},{"name":"ARNEPALLI MEGANA","regNo":"N/A","section":"A"},{"name":"BAGGU MOHITH KUMAR","regNo":"N/A","section":"A"},{"name":"BANDARU BHANU SATYA PRAKASH","regNo":"N/A","section":"A"},{"name":"BARAMA NAVYA NAGA RAMYA SRI","regNo":"N/A","section":"A"},{"name":"BEERA JNANENDRA VARMA","regNo":"N/A","section":"A"},{"name":"BELLAMKONDA JOSHITHA SHANMUKHI","regNo":"N/A","section":"A"},{"name":"BHOGIREDDY TEJASRI SAI VAISHNAVI","regNo":"N/A","section":"A"},{"name":"BODASINGI SHANMUKHA SAI","regNo":"N/A","section":"A"},{"name":"BOLISETTY KEDARESWARI","regNo":"N/A","section":"A"},{"name":"BOLLEDDU GIRIDHARA VENKATA SAI","regNo":"N/A","section":"A"},{"name":"BONDA YOGESH","regNo":"N/A","section":"B"},{"name":"BORRA HIMA SRI","regNo":"N/A","section":"A"},{"name":"BUDITHI SAI ADARSH","regNo":"N/A","section":"A"},{"name":"CHADALAVADA SHAKEENA","regNo":"N/A","section":"B"},{"name":"CHAGANTI DHANESH KUMAR","regNo":"N/A","section":"A"},{"name":"CHALAMALASETTI SAI DURGA","regNo":"N/A","section":"A"},{"name":"CHANDANI VIVEKANANDA","regNo":"N/A","section":"A"},{"name":"CHELLABOYINA YAMINI","regNo":"N/A","section":"A"},{"name":"CHUNDRU GOWTHAM KRISHNA","regNo":"N/A","section":"A"},{"name":"DACHEPALLI BHANU UDAY","regNo":"N/A","section":"A"},{"name":"DAKKUMALLA VARSHA","regNo":"N/A","section":"A"},{"name":"DANDUBOYINA VENKATA PRABHAS","regNo":"N/A","section":"A"},{"name":"DHARMAVARUPU CHANDANA","regNo":"N/A","section":"A"},{"name":"EVANA CHANDU VENKATA SAI GANESH","regNo":"N/A","section":"A"},{"name":"GADAMSETTY VENKATA SAI HARISH","regNo":"N/A","section":"A"},{"name":"GANDRETI KALYANI","regNo":"N/A","section":"A"},{"name":"GANTA HARSHINI","regNo":"N/A","section":"A"},{"name":"GHANTASALA DEEVEN KUMAR","regNo":"N/A","section":"A"},{"name":"GONAPALA SRI GOWTHAM","regNo":"N/A","section":"A"},{"name":"GOTTUMUKKALA NIKHILA VALLI","regNo":"N/A","section":"A"},{"name":"GOWRIPATNAM BHAGYAKIRAN","regNo":"N/A","section":"A"},{"name":"GUDAPALLI VEENA SRUTHI","regNo":"N/A","section":"A"},{"name":"GUNDEPALLI SNEHITH","regNo":"N/A","section":"B"},{"name":"GUNDU TARUN SAI","regNo":"N/A","section":"A"},{"name":"JAVVADI NEHA","regNo":"N/A","section":"B"},{"name":"KADALI SRI SURYA SATYA SAI","regNo":"N/A","section":"B"},{"name":"KARRI REVANTH RATAN REDDY","regNo":"N/A","section":"A"},{"name":"KATTA DILEEP","regNo":"N/A","section":"B"},{"name":"KATTA SRAVANI","regNo":"N/A","section":"A"},{"name":"KELLA CHAKRA VAMSI","regNo":"N/A","section":"A"},{"name":"KOCHERLA YESWANTH","regNo":"N/A","section":"A"},{"name":"KOMATI JAYASRI LAKSHMI","regNo":"N/A","section":"A"},{"name":"KOTA MADHU VENKATESH","regNo":"N/A","section":"A"},{"name":"KOTHAPALLI CHINMAY SATYA KRISHNA","regNo":"N/A","section":"A"},{"name":"KUKKALA SUDHEERA","regNo":"N/A","section":"A"},{"name":"LAKSHMISETTI KAVYA","regNo":"N/A","section":"A"},{"name":"LINGAMPALLI VIJAY VARDHAN","regNo":"N/A","section":"A"},{"name":"MADABHUSHI SRI RANGA SUDARSAN","regNo":"N/A","section":"A"},{"name":"MALLULA KAVERI","regNo":"N/A","section":"A"},{"name":"MAMIDISETTI VASUDHA BHANU","regNo":"N/A","section":"A"},{"name":"MANDANGI MOUNIKA","regNo":"N/A","section":"A"},{"name":"MANDAVA YAGNA AKHIL SAI","regNo":"N/A","section":"A"},{"name":"MANGINETI MOHAN SATYA SIVA ROHITH KUMAR","regNo":"N/A","section":"A"},{"name":"MATTA BALA VEERRAJU","regNo":"N/A","section":"A"},{"name":"MEDIDI LALITH KUMAR","regNo":"N/A","section":"A"},{"name":"MEESALA JAYA RAM","regNo":"N/A","section":"A"},{"name":"MOHAMMAD SIKINDAR KHAN","regNo":"N/A","section":"A"},{"name":"MUCHARLA MANI VENKATA SATYANARAYANA","regNo":"N/A","section":"B"},{"name":"MUCHU MAHADEV","regNo":"N/A","section":"A"},{"name":"MUGADA DURGA PRASAD","regNo":"N/A","section":"B"},{"name":"MUPPIDI AMAR DATTA REDDY","regNo":"N/A","section":"A"},{"name":"MYLABATHULA SUPRIYA","regNo":"N/A","section":"A"},{"name":"NARKEDAMILLI TANISHA","regNo":"N/A","section":"A"},{"name":"NIMMALA BHUVANA LAKSHMI","regNo":"N/A","section":"B"},{"name":"NUKALA NAGA HARSHINI","regNo":"N/A","section":"A"},{"name":"NULAKANI LEELA MADHAVA RAO","regNo":"N/A","section":"A"},{"name":"PABBINEEDI SRI RAMA SATYA MAHESH","regNo":"N/A","section":"A"},{"name":"PABOLU SAI HARSHA","regNo":"N/A","section":"A"},{"name":"PAILA NIKHIL","regNo":"N/A","section":"A"},{"name":"PALAPARTHI SANTHOSH KUMAR","regNo":"N/A","section":"B"},{"name":"PANJA SOMARANGA SAI","regNo":"N/A","section":"B"},{"name":"PASUPULETI DAIVA PRASAD","regNo":"N/A","section":"A"},{"name":"PENAPOTHU JOHARIKA","regNo":"N/A","section":"A"},{"name":"PENMATSA SAI SATHWIKA","regNo":"N/A","section":"B"},{"name":"PENMETSA PUJITH NAGA SANJAY VARMA","regNo":"N/A","section":"A"},{"name":"PENMETSA SAI ANVESH VARMA","regNo":"N/A","section":"A"},{"name":"PERICHARLA HEMA ASWANI","regNo":"N/A","section":"A"},{"name":"PERURI V V S L VINAY","regNo":"N/A","section":"A"},{"name":"PIPPALLA MADHURI VENKATA NAGA DIVYA","regNo":"N/A","section":"A"},{"name":"PIPPALLA RUSHI GUNA SHANMUKH","regNo":"N/A","section":"A"},{"name":"PODAGATLA PRASANTH","regNo":"N/A","section":"A"},{"name":"PONNALA VAISHNAVI PRIYADARSHINI","regNo":"N/A","section":"A"},{"name":"POTHINEEDI TEJA NAGA VENKATA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"POTTURI GAYATRI","regNo":"N/A","section":"A"},{"name":"PULLURU KRISHNA VAMSI","regNo":"N/A","section":"A"},{"name":"PUVVALA SANJANA GAYATHRI","regNo":"N/A","section":"B"},{"name":"RAJ KAMALINI MEENAKSHI BALABHADRA","regNo":"N/A","section":"A"},{"name":"RAMANA DIVYA JYOTHIKA","regNo":"N/A","section":"A"},{"name":"RONGALA SRINIVAS","regNo":"N/A","section":"B"},{"name":"SALUMURI JYOTHI","regNo":"N/A","section":"A"},{"name":"SAMAYAMANTHULA SRIVYSHNAVI ISWARYA LAKSHMI","regNo":"N/A","section":"A"},{"name":"SAMUDRALA JESRAVAN MANIKANTA","regNo":"N/A","section":"B"},{"name":"SATTINENI NIHITHA","regNo":"N/A","section":"A"},{"name":"SAVARAM VENKATA SATYA NAGA DURGA SUBHASH","regNo":"N/A","section":"A"},{"name":"SAYED AMEENA FIRDOUS","regNo":"N/A","section":"A"},{"name":"SEELABOYINA JEEVANA","regNo":"N/A","section":"B"},{"name":"SHAIK AHMED","regNo":"N/A","section":"A"},{"name":"SHAIK SANIYA BEGUM","regNo":"N/A","section":"A"},{"name":"SINGAMSETTI SAI SHANKAR","regNo":"N/A","section":"A"},{"name":"SISTU SNEHA","regNo":"N/A","section":"A"},{"name":"SWAMYREDDY SAI DURGA SAGAR","regNo":"N/A","section":"A"},{"name":"THIRUMALARAJU VENKATA SATYA PAVAN RAJU","regNo":"N/A","section":"A"},{"name":"VALAVALA RAMA LAKSHMI ANJANA","regNo":"N/A","section":"B"},{"name":"VASA HARI NAGENDRA PRATAP","regNo":"N/A","section":"A"},{"name":"VASIMTHA SATYA SAI KALYANI MALLAPAREDY","regNo":"N/A","section":"A"},{"name":"VEERAMALLA NAGAVALLI GANGOTHRI","regNo":"N/A","section":"A"},{"name":"VEERAVALLI SATYA VENKATA SRINADH","regNo":"N/A","section":"A"},{"name":"VOONNA HEMANTH","regNo":"N/A","section":"A"},{"name":"YALLA PRADEEP KUMAR","regNo":"N/A","section":"A"},{"name":"YARLAGADDA TAMOGHNA","regNo":"N/A","section":"B"},{"name":"YENUGAPALLI DIVYA MADHURI","regNo":"N/A","section":"A"},{"name":"YIRRI BHANU NAGA PRAKASH","regNo":"N/A","section":"A"}]
        },
        'AAKASH': {
            name: 'Akash',
            description: 'Sky House - Reaching for the stars with boundless ambition.',
            members: [{"name":"ACHANTA MOKSHITH CHOWDARY","regNo":"N/A","section":"A"},{"name":"ADABALA GANGA PRAVEEN KUMAR","regNo":"N/A","section":"A"},{"name":"ADDAGARLA LAKSHMI DEVI","regNo":"N/A","section":"A"},{"name":"ADINA VENKATA SURYA SAI VISHAL","regNo":"N/A","section":"A"},{"name":"ALLURI BHUVAN SAI TEJA MANI VARMA","regNo":"N/A","section":"A"},{"name":"ANDE NAGA SATYA SAI VAMSI KIRAN","regNo":"N/A","section":"A"},{"name":"ASILETI JAHNAVI","regNo":"N/A","section":"A"},{"name":"BANAVATHU MALLIKARJUNA SAI","regNo":"N/A","section":"A"},{"name":"BHAVANAM LAKSHMAN KUMAR REDDY","regNo":"N/A","section":"A"},{"name":"BILLA SAHITHI","regNo":"N/A","section":"A"},{"name":"BOGA NISHANTH","regNo":"N/A","section":"A"},{"name":"BOPPINEEDI GEETHIKA","regNo":"N/A","section":"A"},{"name":"BUDIDA NAGA VAISHNAVI","regNo":"N/A","section":"A"},{"name":"CHIKILE RAJESH","regNo":"N/A","section":"A"},{"name":"CHILAKALAPUDI ABHI RAAMA PHANINDRA","regNo":"N/A","section":"A"},{"name":"CHINNAM LAKSHMI SANTHOSHI","regNo":"N/A","section":"A"},{"name":"CHODAGAM SHANMUKHA SIVA SRI VENKAT","regNo":"N/A","section":"A"},{"name":"DATTI VENKATA RAMANA","regNo":"N/A","section":"A"},{"name":"DEVADA SRI VENKATESWARA SWAMY","regNo":"N/A","section":"A"},{"name":"DIRISIMILLI MAHI AVINASH","regNo":"N/A","section":"A"},{"name":"DODDIPATLA POOJA SAI PRAVEENA","regNo":"N/A","section":"A"},{"name":"DONGA MADHURI","regNo":"N/A","section":"A"},{"name":"DURVASULA SITA SRI VYSHNAVI","regNo":"N/A","section":"A"},{"name":"DUVVADA VINAY","regNo":"N/A","section":"A"},{"name":"GADDAM MANOJ KUMAR","regNo":"N/A","section":"A"},{"name":"GANDHAM MAHATHI","regNo":"N/A","section":"A"},{"name":"GANDROJU ESWAR SRI KALI KRISHNA","regNo":"N/A","section":"A"},{"name":"GOPATHI KALYANI","regNo":"N/A","section":"A"},{"name":"GUNTAMUKKALA SHAILESH","regNo":"N/A","section":"A"},{"name":"GURRAM VIKAS","regNo":"N/A","section":"A"},{"name":"GUTTULA CHAITANYA AKSHAY","regNo":"N/A","section":"A"},{"name":"INDIGIMELLI RESHMA SUDEEPA","regNo":"N/A","section":"A"},{"name":"INDUKURI YASWANTH ACHYUTA VARMA","regNo":"N/A","section":"A"},{"name":"JAKKAMPUDI JAHNAVI","regNo":"N/A","section":"A"},{"name":"JALDHI PRINCESS GLORY JASMINE","regNo":"N/A","section":"A"},{"name":"JILLELA VINAY","regNo":"N/A","section":"A"},{"name":"JITHENDRA VENKATA KANAKA SRI SURYA AYITHAM","regNo":"N/A","section":"B"},{"name":"KAGITHA BHANU DURGA PRASAD","regNo":"N/A","section":"A"},{"name":"KALIDINDI SAI VARMA","regNo":"N/A","section":"B"},{"name":"KALLA GUNADEEP","regNo":"N/A","section":"A"},{"name":"KAMBHAMPATI SHALANI SINDHU SRI","regNo":"N/A","section":"A"},{"name":"KANUBOINA VIJAYA LAKSHMI","regNo":"N/A","section":"A"},{"name":"KANUMURI SUDHA","regNo":"N/A","section":"A"},{"name":"KARRI LAKSHMI SRAVANTHI","regNo":"N/A","section":"A"},{"name":"KARUMANCHI SUNEEL","regNo":"N/A","section":"A"},{"name":"KARUMURI TEJA SIDDARDHA PAVAN KUMAR","regNo":"N/A","section":"A"},{"name":"KATARI HASWANTH SIVA BHASKAR","regNo":"N/A","section":"B"},{"name":"KATRAGADDA ARJUN NAIDU","regNo":"N/A","section":"A"},{"name":"KATREDDI BHANU TEJA SRI","regNo":"N/A","section":"A"},{"name":"KETHA BHAVYASRI SAILAKSHMI","regNo":"N/A","section":"A"},{"name":"KHANDAVALLI VYSHNAVI","regNo":"N/A","section":"A"},{"name":"KODI HEMANTH KUMAR","regNo":"N/A","section":"A"},{"name":"KOLLA RAMA SAI","regNo":"N/A","section":"B"},{"name":"KOLLABATHULA SHYAM BABU","regNo":"N/A","section":"A"},{"name":"KOLLEPARA PREM","regNo":"N/A","section":"A"},{"name":"KOLLI VINEEL","regNo":"N/A","section":"A"},{"name":"KONKEY BINDHU VASANTHI","regNo":"N/A","section":"A"},{"name":"KOPPARTHI DURGA BHAVANI","regNo":"N/A","section":"A"},{"name":"KOREDLA MEDHO SAI ASESH","regNo":"N/A","section":"A"},{"name":"KOTTA S N VASAVI SRIVALLI","regNo":"N/A","section":"A"},{"name":"KUCHIMANCHI PRANAV","regNo":"N/A","section":"A"},{"name":"KUSAMPUDI VENKATA SATYA SAI TEJAS VARMA","regNo":"N/A","section":"A"},{"name":"MADDALA MANI NAGA SAI NARASIMHA TRINADH","regNo":"N/A","section":"B"},{"name":"MADDALA VARSHINI","regNo":"N/A","section":"A"},{"name":"MADDULA AAKASH NAGENDRA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"MADUPALLI JNANESH","regNo":"N/A","section":"A"},{"name":"MAKKA SAI GOWR","regNo":"N/A","section":"A"},{"name":"MALLULA MADHU VARSHINI","regNo":"N/A","section":"A"},{"name":"MANCHALA SHANMUKA LAKSHMI DEEPIKA","regNo":"N/A","section":"A"},{"name":"MANDA TANMAY VENKATA SAI LALA GUPTA","regNo":"N/A","section":"A"},{"name":"MANDELA MUKUNDA PADMA PRIYA","regNo":"N/A","section":"A"},{"name":"MANGENA SAI VENKATA VENU GOPALA CHARAN","regNo":"N/A","section":"A"},{"name":"MEDISETTI SRINIJA","regNo":"N/A","section":"B"},{"name":"MOHAMMAD NUMAAN RAZA","regNo":"N/A","section":"B"},{"name":"MULAGALA PRANATI SANDHYA","regNo":"N/A","section":"B"},{"name":"MUTHABATHULA PUNEETH","regNo":"N/A","section":"A"},{"name":"NADIKUPPALA THANUSH","regNo":"N/A","section":"A"},{"name":"NADIMPALLI BABAJI AMRUTHA VARMA","regNo":"N/A","section":"A"},{"name":"NALLAM MANOGNYA DEVI","regNo":"N/A","section":"A"},{"name":"NAMALA THANUSHA","regNo":"N/A","section":"B"},{"name":"NANDRU VINAY BABU","regNo":"N/A","section":"A"},{"name":"NODAGALA NANDA GOPAL SWAMY","regNo":"N/A","section":"B"},{"name":"NULI LAKSHMI SAI LIKITH","regNo":"N/A","section":"B"},{"name":"PAVULURI SAI KRISHNA","regNo":"N/A","section":"B"},{"name":"PENUGONDA ENMANUYEL","regNo":"N/A","section":"B"},{"name":"PERABATHULA SOMESWARA RAO","regNo":"N/A","section":"A"},{"name":"POLIMERA SWAPNA","regNo":"N/A","section":"A"},{"name":"POTHURI SIVA SAI KRISHNA VARMA","regNo":"N/A","section":"A"},{"name":"PULI MYTHILI","regNo":"N/A","section":"B"},{"name":"PULIDINDI BLOOMY CHRIS ANGEL","regNo":"N/A","section":"A"},{"name":"PUTHINIDI JNANESWARI","regNo":"N/A","section":"A"},{"name":"PUVVALA DEVI AISHWARYA","regNo":"N/A","section":"A"},{"name":"RAJA AKASH","regNo":"N/A","section":"B"},{"name":"RAMISETTY SANHITHA SRI","regNo":"N/A","section":"A"},{"name":"RANGISETTI HEMA SAHASRA","regNo":"N/A","section":"B"},{"name":"REDDEM LEELA MEGHANA","regNo":"N/A","section":"B"},{"name":"REDDY VENKATA SAKETH","regNo":"N/A","section":"A"},{"name":"RELLU LAKSHMI PRASANNA","regNo":"N/A","section":"A"},{"name":"SEELABOINA RAMADEVI","regNo":"N/A","section":"A"},{"name":"SEELABOINA SANTOSH KUMAR","regNo":"N/A","section":"A"},{"name":"SEELABOYINA JEEVIKA","regNo":"N/A","section":"B"},{"name":"SHAIK AFZAL DANISH","regNo":"N/A","section":"A"},{"name":"SHAIK ILIYAS","regNo":"N/A","section":"A"},{"name":"SHAIK SAMEERA","regNo":"N/A","section":"A"},{"name":"SHAIK SUHANA","regNo":"N/A","section":"B"},{"name":"SHAIK THAHIR BASHA","regNo":"N/A","section":"A"},{"name":"SUNKARA CHAITANYA VEERA BHAIRAV","regNo":"N/A","section":"A"},{"name":"TANUKULA UMA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"TAPPETA GANESH REDDY","regNo":"N/A","section":"A"},{"name":"TEKU DURGA SRINIVAS","regNo":"N/A","section":"A"},{"name":"THOTA DEVI SRI SAI SREEKAR","regNo":"N/A","section":"A"},{"name":"THOTA MOHAN SIVA","regNo":"N/A","section":"A"},{"name":"THOTA SUJAY BABU","regNo":"N/A","section":"A"},{"name":"UNDURTHI MANOJ","regNo":"N/A","section":"A"},{"name":"UNGARALA RADHIKA AISHWARYA","regNo":"N/A","section":"B"},{"name":"UPPALA ABHINAYA SREE","regNo":"N/A","section":"B"},{"name":"VARADA NAGA SURYA LAKSHMI","regNo":"N/A","section":"A"},{"name":"VARRE GEETHA NAGA VALLI","regNo":"N/A","section":"B"},{"name":"VATTIVELLA RAMKI","regNo":"N/A","section":"B"},{"name":"VILLURI MOHINI MANGA LAKSHMI MANASA","regNo":"N/A","section":"A"},{"name":"VISSAPRAGADA RAMA PRANEETH","regNo":"N/A","section":"A"},{"name":"YENDA RASHMIKA","regNo":"N/A","section":"B"},{"name":"YERICHERLA JOHN ELISHA","regNo":"N/A","section":"B"},{"name":"YERRA YASVASI SATYA KAVERI","regNo":"N/A","section":"B"}]
        },
        'PRUDHVI': {
            name: 'Prudhvi',
            description: 'Earth House - Strong and steady like the mountains that stand the test of time.',
            members: [{"name":"ADABALA SAI NAGA SURYANARAYANA","regNo":"N/A","section":"B"},{"name":"AKULA BALA BHAGYA SRI","regNo":"N/A","section":"A"},{"name":"BANDARU MANOGNA NAGAVALLI","regNo":"N/A","section":"A"},{"name":"BANDI HARI KRISHNA","regNo":"N/A","section":"A"},{"name":"BARAKATA TARUN SWAMY","regNo":"N/A","section":"A"},{"name":"BASIVIREDDY HEMALATHA","regNo":"N/A","section":"A"},{"name":"BAYYE JOSEPH KUMAR","regNo":"N/A","section":"A"},{"name":"BILLAKURTHI HARSHA VARDHAN SRINIVASU","regNo":"N/A","section":"B"},{"name":"BIRUDUKOTA SATYA VARA PRASAD","regNo":"N/A","section":"B"},{"name":"BOLEM PRAVALIKA","regNo":"N/A","section":"B"},{"name":"BOMMIDI JAHNAVI","regNo":"N/A","section":"A"},{"name":"BOYAPATI PRASANNA VARUN","regNo":"N/A","section":"B"},{"name":"BUDDE VENKATA SATYA TEJESH","regNo":"N/A","section":"A"},{"name":"CHALLA JITHENDRA ABHIRAM","regNo":"N/A","section":"A"},{"name":"CHALLAGUNDLA HINDRIKA SRI","regNo":"N/A","section":"A"},{"name":"CHANDAKA KEDARA SRINIVAS","regNo":"N/A","section":"A"},{"name":"CHATRAGADDA TEJASWINI","regNo":"N/A","section":"B"},{"name":"CHEEPU SAI VIKAS","regNo":"N/A","section":"A"},{"name":"CHEGONDI HARSHINI","regNo":"N/A","section":"A"},{"name":"CHEYYETI VENKATA SINDHU","regNo":"N/A","section":"B"},{"name":"CHINTAPALLI VENKATA DURGESH","regNo":"N/A","section":"A"},{"name":"CHOKKA ARYAN SANTHOSH","regNo":"N/A","section":"A"},{"name":"CHUNDRU VISWA TEJA","regNo":"N/A","section":"A"},{"name":"DASARI YUVA RAM","regNo":"N/A","section":"B"},{"name":"DIRSIPOM INDHU PRIYA","regNo":"N/A","section":"B"},{"name":"DONGA CHANDINI","regNo":"N/A","section":"A"},{"name":"DONGA MAHESH","regNo":"N/A","section":"B"},{"name":"DWARAMPUDI PURNA NAGA GOWTHAM REDDY","regNo":"N/A","section":"B"},{"name":"EDIMUDI SURIBABU","regNo":"N/A","section":"A"},{"name":"ESURU CHAITANYA","regNo":"N/A","section":"A"},{"name":"G UDAY KIRAN","regNo":"N/A","section":"A"},{"name":"GADDAMUDI VENKATA GOPICHAND","regNo":"N/A","section":"A"},{"name":"GANJI JYOTHSNA","regNo":"N/A","section":"B"},{"name":"GANTA GOWTHAM","regNo":"N/A","section":"A"},{"name":"GAYAKAWADA PALLAVI","regNo":"N/A","section":"A"},{"name":"GEDELA SAI ABHINAY","regNo":"N/A","section":"A"},{"name":"GIRIJALA PRASHANTH KUMAR","regNo":"N/A","section":"A"},{"name":"GUBBALA RESHMA GANGAVATHI","regNo":"N/A","section":"A"},{"name":"GUDDATI DURGA NAGA LAKSHMI SHIVA SARANYA","regNo":"N/A","section":"A"},{"name":"GUDDETI DATHRI SRI SAI ANVITHA","regNo":"N/A","section":"A"},{"name":"GUDIMETLA JNANA SANDEEP REDDY","regNo":"N/A","section":"A"},{"name":"GUDURI KARTHIK SRI NAGA SAI","regNo":"N/A","section":"A"},{"name":"GUMMALLA NAGA GAYATHRI","regNo":"N/A","section":"A"},{"name":"ITTA VASAVI","regNo":"N/A","section":"A"},{"name":"JADDU JYOTHIRMAI INDIRA PRIYADARSINI DEVI","regNo":"N/A","section":"A"},{"name":"JALDANI ABHIRAM CHARAN","regNo":"N/A","section":"A"},{"name":"JAVVADI MOHANA DURGA","regNo":"N/A","section":"A"},{"name":"JOGI PAVAN TEJA","regNo":"N/A","section":"A"},{"name":"JONNALAGADDA LAKSHMI MOUNIKA","regNo":"N/A","section":"A"},{"name":"KADALI BHANU","regNo":"N/A","section":"A"},{"name":"KANCHARLA N V L DURGA NIHARIKA","regNo":"N/A","section":"A"},{"name":"KANDANALA PURNASRI","regNo":"N/A","section":"A"},{"name":"KANUMURI RISHITHA VARMA","regNo":"N/A","section":"A"},{"name":"KAPAKAYALA NAGA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"KARATAM SANTHOSH KUMAR","regNo":"N/A","section":"A"},{"name":"KARIBANDI PAVAN RAVINDRA KUMAR","regNo":"N/A","section":"A"},{"name":"KAYITHA LAHARI","regNo":"N/A","section":"A"},{"name":"KESANAKURTHI MANASA SATYA","regNo":"N/A","section":"A"},{"name":"KETA PURNA PAVAN","regNo":"N/A","section":"A"},{"name":"KOLLATI SAGAR","regNo":"N/A","section":"A"},{"name":"KOLLATI VISHNU TEJA","regNo":"N/A","section":"A"},{"name":"KOMMULA DIVYA MANOGNA","regNo":"N/A","section":"A"},{"name":"KORANGI TRINADH","regNo":"N/A","section":"A"},{"name":"KOTA DEEPIKA","regNo":"N/A","section":"A"},{"name":"KOTLA VENKAT","regNo":"N/A","section":"A"},{"name":"KUMMARAPURUGU SAIRAM","regNo":"N/A","section":"A"},{"name":"KUSUMA KOMALI","regNo":"N/A","section":"A"},{"name":"KUTIKUPPALA CHARAN TEJA","regNo":"N/A","section":"A"},{"name":"LAKKU NOMU NARASIMHA SAI PAVAN","regNo":"N/A","section":"A"},{"name":"LAKSHMI VENKATA NIKHITHA","regNo":"N/A","section":"A"},{"name":"LOKAM MAHITANJALI","regNo":"N/A","section":"A"},{"name":"MADABHUSHI SRI RANGA SUDARSAN","regNo":"N/A","section":"A"},{"name":"MADAMANCHI MANIKANTA","regNo":"N/A","section":"A"},{"name":"MALLA DEEPANVITHA","regNo":"N/A","section":"A"},{"name":"MAMUDURI PRABHAS","regNo":"N/A","section":"B"},{"name":"MANAPARAPU DEEPIKA","regNo":"N/A","section":"A"},{"name":"MANDA KEERTHI","regNo":"N/A","section":"A"},{"name":"MANDAGIRI SAI ASWITHA","regNo":"N/A","section":"A"},{"name":"MANDAVALLI DHANA KARTHIKEYA","regNo":"N/A","section":"A"},{"name":"MARUBOINA KARTHIK VENKATA SRI SAI TEJA","regNo":"N/A","section":"A"},{"name":"MEDISETTI RAMA KRISHNA SAI","regNo":"N/A","section":"A"},{"name":"MEER IKRAAM HUSSAIN","regNo":"N/A","section":"B"},{"name":"MEESALA KARTHIK RAJ KUMAR","regNo":"N/A","section":"A"},{"name":"MEESALA RAJANIKUMAR","regNo":"N/A","section":"A"},{"name":"MOHAMMAD IBRAHIM KHAN","regNo":"N/A","section":"A"},{"name":"MOHAMMAD ROOFIYA TASNEEM","regNo":"N/A","section":"A"},{"name":"MORTHA ANUSRI","regNo":"N/A","section":"A"},{"name":"MUDUNURI MANOJ SAI ASWANTH VARMA","regNo":"N/A","section":"A"},{"name":"MUNGARA LOKESH KUMAR","regNo":"N/A","section":"A"},{"name":"MUTHYALAPALLI","regNo":"N/A","section":"B"},{"name":"NAKKINA GANESH","regNo":"N/A","section":"A"},{"name":"NALAMALA KEVIN RISHITH","regNo":"N/A","section":"B"},{"name":"NALLA TANOJ SITHARAM","regNo":"N/A","section":"A"},{"name":"NAMUDURI MAHESH","regNo":"N/A","section":"A"},{"name":"NANDURI SURYA NAGA VENKATA SAI VIGNESH","regNo":"N/A","section":"A"},{"name":"NEPALA BESWANTH","regNo":"N/A","section":"B"},{"name":"NETHALA HEMA DURGA SAI KUMAR","regNo":"N/A","section":"B"},{"name":"NIMMANA NARENDRA","regNo":"N/A","section":"B"},{"name":"PADAVALA GANIF RAJU","regNo":"N/A","section":"A"},{"name":"PAIDI TANUJA","regNo":"N/A","section":"A"},{"name":"PAKA RENITA JESSIE","regNo":"N/A","section":"B"},{"name":"PALANI BHUVANA SAI KRUTHI","regNo":"N/A","section":"B"},{"name":"PALIVELA BALA BHASKARA PRADEEP","regNo":"N/A","section":"A"},{"name":"PALLAPU HARITHA","regNo":"N/A","section":"B"},{"name":"PANDA SUJAN PRASAD","regNo":"N/A","section":"B"},{"name":"PANJA MUKUNDA SRI NAGA SANTOSH","regNo":"N/A","section":"A"},{"name":"PANJA NAGA VENKATA PRASAD RAJA","regNo":"N/A","section":"A"},{"name":"PARAVASTU VENKATA RAMA SURI","regNo":"N/A","section":"B"},{"name":"PAREPALLI RAMA HARI NAIDU","regNo":"N/A","section":"A"},{"name":"PATAN ABDUL RASHEED KHAN","regNo":"N/A","section":"B"},{"name":"PECHETTI SRI VINAYAK","regNo":"N/A","section":"A"},{"name":"PEETHANI UDAYA SRI","regNo":"N/A","section":"A"},{"name":"PERICHERLA VIGNESH VARMA","regNo":"N/A","section":"A"},{"name":"PILLI MEGHANA","regNo":"N/A","section":"A"},{"name":"POTLA RAVI","regNo":"N/A","section":"B"},{"name":"PUPPALA JANARDHAN SAI","regNo":"N/A","section":"B"},{"name":"RAAVI CHARWAK","regNo":"N/A","section":"A"},{"name":"RANGISETTI SAI PAVAN KUMAR","regNo":"N/A","section":"B"},{"name":"REBBA RAJESH","regNo":"N/A","section":"B"},{"name":"REDDY SRIJA","regNo":"N/A","section":"B"},{"name":"REDDY VENKATA SATYA SRAVANI","regNo":"N/A","section":"B"},{"name":"REKHAPALLI RUTHIKA AKSHAYA SAI SRI","regNo":"N/A","section":"A"},{"name":"RODDA VENKATA SIVA SAI","regNo":"N/A","section":"A"},{"name":"ROMPILLI SATEESH","regNo":"N/A","section":"B"},{"name":"RUDRAKSHULA PRAVEENA","regNo":"N/A","section":"A"},{"name":"SANDHI SHAMM ROY","regNo":"N/A","section":"A"},{"name":"SANKU VEERA VENKATA SANTOSH","regNo":"N/A","section":"A"},{"name":"SARIPALLI GNANESWAR","regNo":"N/A","section":"B"},{"name":"SHAIK ABDUL GAFOOR","regNo":"N/A","section":"B"},{"name":"SHAIK KARIMUNNISA","regNo":"N/A","section":"A"},{"name":"Shaik madeena","regNo":"N/A","section":"A"},{"name":"SHAIK REENAZ","regNo":"N/A","section":"A"},{"name":"SIDAGAM ABHIRAM","regNo":"N/A","section":"B"},{"name":"SIRRA DURGA RANI","regNo":"N/A","section":"B"},{"name":"SUNKARA LOKESH VIJAY SAI","regNo":"N/A","section":"B"},{"name":"SURARAPU HASINI","regNo":"N/A","section":"A"},{"name":"SWARNA SAHITHI","regNo":"N/A","section":"B"},{"name":"SYED MANSOOR","regNo":"N/A","section":"A"},{"name":"TALARI JYOTHI","regNo":"N/A","section":"B"},{"name":"TAMARANA SRUTHI","regNo":"N/A","section":"A"},{"name":"TELLAKULA VEERA RAGHAVA","regNo":"N/A","section":"A"},{"name":"TELU YUVA PRIYA MOULIKA","regNo":"N/A","section":"A"},{"name":"TIRUMALASETTY SIDDARDHA","regNo":"N/A","section":"B"},{"name":"VASE ASHITHA","regNo":"N/A","section":"B"},{"name":"VATAPALLI GNANA SEKHAR","regNo":"N/A","section":"A"},{"name":"VATHADI NAGAVINAY","regNo":"N/A","section":"B"},{"name":"VEERAVALLI LEELA NAGA BABU","regNo":"N/A","section":"A"},{"name":"VEERAVARAPU NAGA VENKATA JASWANTH","regNo":"N/A","section":"A"},{"name":"VEERLAPATI HARSHINI","regNo":"N/A","section":"A"},{"name":"VENNAPUSA MANISHA","regNo":"N/A","section":"B"}]
        }
    };

    /**
     * =========================================================================
     * 4. MASTER PERSON INDEX (FACULTY, HEROES, CRs & STUDENTS)
     * Contains 650+ Persons with structured fields
     * =========================================================================
     */
    const MASTER_PERSON_INDEX = [
        // --- FACULTY MEMBERS (25 FACULTY RECORDS) ---
        {
            id: 'faculty_suresh_babu',
            fullName: 'Dr. Suresh Babu Mudunuri',
            firstName: 'suresh',
            lastName: 'mudunuri',
            category: 'Professor & Head of Department (CSD)',
            role: 'Professor & Head of Department (CSD)',
            designation: 'Professor & HOD (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'suresh.mudunuri@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (JNTU, 2010)',
            specialization: 'AI, Machine Learning & Cloud Infrastructure',
            experience: '20+ Years',
            achievements: 'Head of Department (CSD), 35+ Research Publications, 15+ Funded Projects',
            description: 'Dr. Suresh Babu Mudunuri is a distinguished Professor and Head of Department of Computer Science & Design (CSD) at SRKR Engineering College with over 20 years of academic and research experience.',
            searchableAliases: ['suresh', 'suresh babu', 'm suresh babu', 'dr suresh babu', 'mudunuri suresh babu', 'suresh babu mudunuri', 'suresh sir', 'suresh babu sir', 'dr suresh', 'hod suresh', 'hod csd'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_ngk_murthy',
            fullName: 'Dr. N. Gopala Krishna Murthy',
            firstName: 'murthy',
            lastName: 'gopala krishna',
            category: 'Professor & Head of Department (CSIT)',
            role: 'Professor & Head of Department (CSIT)',
            designation: 'Professor & HOD (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'gopinukala@gmail.com',
            qualification: 'Ph.D in Information Technology (JNTU, 2011)',
            specialization: 'Information Technology Systems & Enterprise Networks',
            experience: '18+ Years',
            achievements: 'Head of Department (CSIT), 30+ Research Publications, 18+ Projects',
            description: 'Dr. N. Gopala Krishna Murthy is Professor and Head of Department of Computer Science & Information Technology (CSIT) at SRKR Engineering College.',
            searchableAliases: ['ngk murthy', 'gopala krishna', 'gopala krishna murthy', 'dr ngk murthy', 'n gopala krishna murthy', 'murthy', 'murthy sir', 'gopala krishna sir', 'ngk murthy sir', 'hod csit'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_satyam',
            fullName: 'ANGARA SATYAM',
            firstName: 'satyam',
            lastName: 'angara',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'asatyam@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Artificial Intelligence & Intelligent Systems',
            subjects: 'Artificial Intelligence, Python Programming',
            experience: '7+ Years',
            achievements: 'AI Coding Contest Coach, Intelligent Automation Mentor',
            description: 'Angara Satyam (Satyam Sir) is Assistant Professor in CSD specializing in Artificial Intelligence and Python Programming.',
            searchableAliases: ['satyam', 'angara satyam', 'a satyam', 'a. satyam', 'satyam sir', 'satyam madam', 'dr satyam', 'prof satyam', 'satyam mudunuri'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_trinadh',
            fullName: 'K V V Satya Trinadh Naidu',
            firstName: 'trinadh',
            lastName: 'naidu',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'kvvstnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Cyber Security, Java, Python Application Development',
            subjects: 'Cyber Security, Java Programming, Python',
            experience: '7+ Years',
            achievements: 'Lead Cybersecurity Advisor (8+ Publications, 9+ Projects)',
            description: 'K V V Satya Trinadh Naidu (Trinadh Sir) is Assistant Professor in CSIT specializing in Cyber Security and Java Application Development.',
            searchableAliases: ['trinadh', 'trinadh naidu', 'satya trinadh', 'k v v satya trinadh naidu', 'trinadh sir', 'trinadh madam', 'dr trinadh', 'prof trinadh', 'kvvstnaidu', 'satya trinadh naidu'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_manoj',
            fullName: 'P MANOJ',
            firstName: 'manoj',
            lastName: 'pericherla',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'manoj.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Prompt Engineering & Generative AI',
            subjects: 'Prompt Engineering, Generative AI, Python',
            experience: '5+ Years',
            achievements: 'Generative AI Workshop Lead, 6+ Publications',
            description: 'P Manoj (Manoj Sir) is Assistant Professor in CSIT specializing in Prompt Engineering and Generative AI.',
            searchableAliases: ['manoj', 'p manoj', 'pericherla manoj', 'manoj sir', 'manoj madam', 'dr manoj', 'prof manoj'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aswini_priyanka',
            fullName: 'A. Aswini Priyanka',
            firstName: 'aswini',
            lastName: 'priyanka',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'aapriyanka@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2015)',
            specialization: 'Cloud Computing & Web Technologies',
            subjects: 'Cloud Computing, Web Technologies',
            experience: '8+ Years',
            achievements: 'Cloud Certified Educator, 10+ Publications',
            description: 'A. Aswini Priyanka (Aswini Priyanka Madam) is Assistant Professor in CSD specializing in Cloud Computing.',
            searchableAliases: ['aswini', 'aswini priyanka', 'a aswini priyanka', 'aswini madam', 'aswini sir', 'dr aswini'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mohan_krishna',
            fullName: 'S. Mohan Krishna',
            firstName: 'mohan',
            lastName: 'krishna',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'mohanakrishna.seerla@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'AI, Machine Learning & Computer Vision',
            subjects: 'Artificial Intelligence, Machine Learning',
            experience: '7+ Years',
            achievements: 'AI & ML Research Mentor, 8+ Publications',
            description: 'S. Mohan Krishna (Mohan Krishna Sir) is Assistant Professor in CSD specializing in AI and Machine Learning.',
            searchableAliases: ['mohan krishna', 's. mohan krishna', 's mohan krishna', 'mohan krishna sir', 'mohan sir', 'krishna sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surya_kumar',
            fullName: 'P S V SURYA KUMAR',
            firstName: 'surya',
            lastName: 'kumar',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'psvsuryakumar@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'IoT & Embedded Systems',
            subjects: 'IoT Architecture, Embedded Systems',
            experience: '6+ Years',
            achievements: 'IoT Hardware Lab Director, 7+ Publications',
            description: 'P S V SURYA KUMAR (Surya Kumar Sir) is Assistant Professor in CSD specializing in Internet of Things.',
            searchableAliases: ['surya kumar', 'p s v surya kumar', 'surya kumar sir', 'psv surya kumar', 'surya sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinivasa_rao',
            fullName: 'Dr. K. Srinivasa Rao',
            firstName: 'srinivasa',
            lastName: 'rao',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'ksrinivasarao@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (Andhra University, 2018)',
            specialization: 'Computer Networks & Security',
            subjects: 'Computer Networks, Information Security',
            experience: '10+ Years',
            achievements: 'Ph.D Doctorate Holder, 15+ Publications',
            description: 'Dr. K. Srinivasa Rao is Assistant Professor in CSD specializing in Computer Networks and Cyber Security.',
            searchableAliases: ['srinivasa rao', 'dr k srinivasa rao', 'k srinivasa rao', 'srinivasa rao sir', 'dr srinivasa', 'srinivasa sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_bhanu_rajesh',
            fullName: 'K. Bhanu Rajesh Naidu',
            firstName: 'bhanu',
            lastName: 'rajesh',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'kbrnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Cloud Computing & DevOps Systems',
            subjects: 'Cloud Computing, DevOps Systems',
            experience: '6+ Years',
            achievements: 'AWS Certified Solution Architect, 5+ Publications',
            description: 'K. Bhanu Rajesh Naidu is Assistant Professor in CSD specializing in Cloud Computing and DevOps.',
            searchableAliases: ['bhanu rajesh', 'bhanu rajesh naidu', 'k bhanu rajesh naidu', 'bhanu sir', 'bhanu rajesh sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_aneela',
            fullName: 'N. Aneela',
            firstName: 'aneela',
            lastName: 'n',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSD)',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'aneela@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Data Mining',
            subjects: 'Machine Learning, Data Mining',
            experience: '5+ Years',
            achievements: 'Data Science Mentor, 6+ Publications',
            description: 'N. Aneela (Aneela Madam) is Assistant Professor in CSD specializing in Machine Learning.',
            searchableAliases: ['aneela', 'n aneela', 'aneela madam', 'aneela sir', 'dr aneela'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sai_madhuri',
            fullName: 'M Sai Madhuri',
            firstName: 'madhuri',
            lastName: 'sai',
            category: 'Faculty Member',
            role: 'Teaching Assistant (CSD)',
            designation: 'Teaching Assistant (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'madhuryamudundi@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Programming',
            experience: '3+ Years',
            achievements: 'Lab Coordinator, 2+ Publications',
            description: 'M Sai Madhuri is Teaching Assistant in CSD.',
            searchableAliases: ['sai madhuri', 'madhuri madam', 'sai madhuri madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_navya',
            fullName: 'N. NAVYA',
            firstName: 'navya',
            lastName: 'nallaparaju',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'navyanallaparaju@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Structures',
            subjects: 'Machine Learning, Data Structures',
            experience: '6+ Years',
            achievements: 'Active Research Scholar, 7+ Publications',
            description: 'N. NAVYA (Navya Madam) is Assistant Professor in CSIT.',
            searchableAliases: ['navya', 'n navya', 'navya nallaparaju', 'navya madam', 'navya sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_praveen',
            fullName: 'NETI PRAVEEN',
            firstName: 'praveen',
            lastName: 'neti',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'npraveen@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Machine Learning & Database Management',
            subjects: 'Machine Learning, Database Management Systems',
            experience: '7+ Years',
            achievements: 'Student Project Coordinator, 8+ Publications',
            description: 'NETI PRAVEEN (Praveen Sir) is Assistant Professor in CSIT.',
            searchableAliases: ['neti praveen', 'n praveen', 'praveen sir', 'praveen madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sunil_varma',
            fullName: 'K V SUNIL VARMA',
            firstName: 'sunil',
            lastName: 'varma',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'kvsunilvarma@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Software Engineering',
            subjects: 'Machine Learning, Software Engineering',
            experience: '6+ Years',
            achievements: 'Software Systems Mentor, 6+ Publications',
            description: 'K V SUNIL VARMA (Sunil Varma Sir) is Assistant Professor in CSIT.',
            searchableAliases: ['sunil varma', 'k v sunil varma', 'sunil varma sir', 'sunil sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_mouna',
            fullName: 'P MOUNA',
            firstName: 'mouna',
            lastName: 'penmetsa',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'mouna.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Neural Networks',
            subjects: 'Machine Learning, Object Oriented Programming',
            experience: '5+ Years',
            achievements: 'Innovative Teaching Award, 5+ Publications',
            description: 'P MOUNA (Mouna Madam) is Assistant Professor in CSIT.',
            searchableAliases: ['mouna', 'p mouna', 'penmetsa mouna', 'mouna madam', 'mouna sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_krishna_veni',
            fullName: 'ANUSURI KRISHNA VENI',
            firstName: 'krishna veni',
            lastName: 'anusuri',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'akveni@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Mining',
            subjects: 'Machine Learning, Data Structures',
            experience: '6+ Years',
            achievements: 'Academic Excellence Mentor, 6+ Publications',
            description: 'ANUSURI KRISHNA VENI is Assistant Professor in CSIT.',
            searchableAliases: ['krishna veni', 'a krishna veni', 'akveni', 'anusuri krishna veni', 'krishna veni madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_parvathi',
            fullName: 'D Parvathi',
            firstName: 'parvathi',
            lastName: 'd',
            category: 'Faculty Member',
            role: 'Assistant Professor (CSIT)',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'parvathiram21@gmail.com',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & C Programming',
            subjects: 'Machine Learning, C Programming',
            experience: '5+ Years',
            achievements: 'Faculty Publication Award, 5+ Publications',
            description: 'D Parvathi (Parvathi Madam) is Assistant Professor in CSIT.',
            searchableAliases: ['d parvathi', 'parvathi madam', 'parvathi sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_vignya',
            fullName: 'K Sri Vigyna',
            firstName: 'vignya',
            lastName: 'k',
            category: 'Faculty Member',
            role: 'Teaching Assistant (CSIT)',
            designation: 'Teaching Assistant (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'vignyak@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Lab',
            experience: '3+ Years',
            achievements: 'Practical Lab Facilitator, 2+ Publications',
            description: 'K Sri Vigyna is Teaching Assistant in CSIT.',
            searchableAliases: ['vignya', 'vigyna', 'sri vignya', 'k sri vigyna', 'vignya madam', 'vignya sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_srinu',
            fullName: 'M. SRINU',
            firstName: 'srinu',
            lastName: 'm',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'msrinu@srkrec.edu.in',
            specialization: 'Computer Science & Information Technology',
            description: 'M. SRINU (Srinu Sir) is a Faculty Member in the CSIT Department.',
            searchableAliases: ['m srinu', 'srinu sir', 'm. srinu', 'faculty srinu'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_surendra',
            fullName: 'J. MOHAN SURENDRA',
            firstName: 'surendra',
            lastName: 'mohan',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'mohansurendra.j@srkrec.edu.in',
            specialization: 'Software Systems & Information Technology',
            description: 'J. MOHAN SURENDRA is a Faculty Member in the CSIT Department.',
            searchableAliases: ['mohan surendra', 'surendra', 'j mohan surendra', 'surendra sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_sudhakar',
            fullName: 'G. SUDHAKAR',
            firstName: 'sudhakar',
            lastName: 'g',
            category: 'Faculty Member',
            role: 'Faculty Member (CSIT)',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            branch: 'CSIT',
            email: 'sudhakar.g@srkrec.edu.in',
            specialization: 'Computer Science & Software Engineering',
            description: 'G. SUDHAKAR is a Faculty Member in the CSIT Department.',
            searchableAliases: ['sudhakar', 'g sudhakar', 'sudhakar sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_girichar',
            fullName: 'K. GIRICHAR',
            firstName: 'girichar',
            lastName: 'k',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'girichar.k@srkrec.edu.in',
            specialization: 'Computer Science & Design',
            description: 'K. GIRICHAR is a Faculty Member in the CSD Department.',
            searchableAliases: ['girichar', 'giridhar', 'k girichar', 'girichar sir'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_tulasi_rajesh',
            fullName: 'Jonnapalli Tulasi Rajesh',
            firstName: 'tulasi',
            lastName: 'rajesh',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'jtulasirajesh@srkrec.edu.in',
            description: 'Jonnapalli Tulasi Rajesh is a Faculty Member in the CSD Department.',
            searchableAliases: ['tulasi rajesh', 'jonnapalli tulasi rajesh', 'tulasi sir', 'rajesh faculty'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_suseela',
            fullName: 'M S Suseela',
            firstName: 'suseela',
            lastName: 'm',
            category: 'Faculty Member',
            role: 'Faculty Member (CSD)',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            branch: 'CSD',
            email: 'm.s.suseela@srkrec.edu.in',
            description: 'M S Suseela is a Faculty Member in the CSD Department.',
            searchableAliases: ['suseela', 'm s suseela', 'suseela madam'],
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },

        // --- DEPARTMENT HEROES & STUDENT ACHIEVERS ---
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
            description: 'Preeti Avvula is a dynamic student leader and master anchor in the CSD department (Reg: 24B91A0701) who served as core organizer for TEDx SRKR.',
            searchableAliases: ['preeti', 'preeti avvula', 'p avvula', 'avvula preeti', 'avvula'],
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
            achievements: 'NSS Coordinator, Python Development Lead (Bhimavaram Online App - 25 shops & 1400+ products onboarded)',
            description: 'Mullu Srinu is a dedicated student leader and NSS coordinator in the CSIT department (Reg: 25B95A6206).',
            searchableAliases: ['mullu srinu', 'mullu', 'mullu srinu student', 'srinu student'],
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
            description: 'P.B.S Kruti is an exceptional classical dancer in the CSD department (Reg: 25B91A0789) who secured 1st Prize at SRKREC Annual Day.',
            searchableAliases: ['kruti', 'p.b.s kruti', 'pbs kruti', 'kruti pbs', 'pbs'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_lakshmi_prasanna',
            fullName: 'R. Lakshmi Prasanna',
            firstName: 'prasanna',
            lastName: 'lakshmi',
            category: 'Department Hero & Cultural Achiever',
            role: '2nd Prize Winner Classical Dance',
            designation: '2nd Prize Winner Classical Dance (SRKREC Annual Day)',
            department: 'CSIT',
            branch: 'CSIT',
            regNo: '24B91A6245',
            achievements: '2nd Prize Winner in Classical Dance Group Performance at SRKREC Annual Day',
            description: 'R. Lakshmi Prasanna is a performing artist in the CSIT department (Reg: 24B91A6245) who won 2nd Prize in Classical Dance.',
            searchableAliases: ['lakshmi prasanna', 'r lakshmi prasanna', 'prasanna', 'lakshmi'],
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_pooja_sai_praveena',
            fullName: 'D Pooja Sai Praveena',
            firstName: 'praveena',
            lastName: 'pooja',
            category: 'Department Hero & National Athlete',
            role: 'Gold Medalist Karate & JNTUK Athlete',
            designation: 'Gold Medalist Karate & JNTUK Athlete',
            department: 'CSIT',
            branch: 'CSIT',
            regNo: '24B91A6218',
            achievements: 'Gold Medalist 🥇 Karate (JNTUK Inter-Collegiate) & South-West Inter-University Athlete',
            description: 'D Pooja Sai Praveena is a Gold Medalist karate champion in the CSIT department (Reg: 24B91A6218).',
            searchableAliases: ['pooja sai praveena', 'praveena', 'd pooja sai praveena', 'pooja praveena', 'pooja'],
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
        },
        {
            id: 'person_p_harsha',
            fullName: 'P HARSHA',
            firstName: 'harsha',
            lastName: 'p',
            category: 'Class Representative',
            role: 'Class Representative (CSIT II Year Sec A)',
            designation: 'Class Representative (CSIT II Year Sec A)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '2nd Year',
            section: 'Section A',
            regNo: '25B91A0786',
            isCR: true,
            searchableAliases: ['p harsha', 'harsha csit', 'harsha cr'],
            description: 'P Harsha is the Class Representative for CSIT II Year Section A (Reg No: 25B91A0786).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_asritha',
            fullName: 'B J S V D N ASRITHA',
            firstName: 'asritha',
            lastName: 'b',
            category: 'Class Representative',
            role: 'Class Representative (CSIT II Year Sec A)',
            designation: 'Class Representative (CSIT II Year Sec A)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '2nd Year',
            section: 'Section A',
            regNo: '25B91A0711',
            isCR: true,
            searchableAliases: ['b j s v d n asritha', 'asritha', 'b asritha'],
            description: 'B J S V D N Asritha is the Class Representative for CSIT II Year Section A (Reg No: 25B91A0711).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_pamu_amrutha',
            fullName: 'PAMU AMRUTHA',
            firstName: 'amrutha',
            lastName: 'pamu',
            category: 'Class Representative',
            role: 'Class Representative (CSIT II Year Sec B)',
            designation: 'Class Representative (CSIT II Year Sec B)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '2nd Year',
            section: 'Section B',
            regNo: '25B91A0782',
            isCR: true,
            searchableAliases: ['pamu amrutha', 'amrutha', 'pamu'],
            description: 'Pamu Amrutha is the Class Representative for CSIT II Year Section B (Reg No: 25B91A0782).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_prasanna_varun',
            fullName: 'B PRASANNA VARUN',
            firstName: 'prasanna varun',
            lastName: 'b',
            category: 'Class Representative',
            role: 'Class Representative (CSIT II Year Sec B)',
            designation: 'Class Representative (CSIT II Year Sec B)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '2nd Year',
            section: 'Section B',
            regNo: '25B91A0717',
            isCR: true,
            searchableAliases: ['b prasanna varun', 'prasanna varun', 'varun cr'],
            description: 'B Prasanna Varun is the Class Representative for CSIT II Year Section B (Reg No: 25B91A0717).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_vivekananda',
            fullName: 'CHANDANI VIVEKANANDA',
            firstName: 'vivekananda',
            lastName: 'chandani',
            category: 'Class Representative',
            role: 'Class Representative (CSIT III Year Sec A)',
            designation: 'Class Representative (CSIT III Year Sec A)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'Section A',
            regNo: '24B91A0720',
            isCR: true,
            searchableAliases: ['chandani vivekananda', 'vivekananda', 'chandani'],
            description: 'Chandani Vivekananda is the Class Representative for CSIT III Year Section A (Reg No: 24B91A0720).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_johan_benedict',
            fullName: 'THOTA JOHAN BENEDICT',
            firstName: 'johan benedict',
            lastName: 'thota',
            category: 'Class Representative',
            role: 'Class Representative (CSIT III Year Sec B)',
            designation: 'Class Representative (CSIT III Year Sec B)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '3rd Year',
            section: 'Section B',
            regNo: '24B91A07B7',
            isCR: true,
            searchableAliases: ['thota johan benedict', 'johan benedict', 'thota'],
            description: 'Thota Johan Benedict is the Class Representative for CSIT III Year Section B (Reg No: 24B91A07B7).',
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
            section: 'Section B',
            regNo: '24B91A07B3',
            isCR: true,
            searchableAliases: ['s d rani', 'rani'],
            description: 'S D Rani is the Class Representative for CSIT III Year Section B (Reg No: 24B91A07B3).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_lasya_sri',
            fullName: 'PULAVARTHI MOHANA MADHU LASYA SRI',
            firstName: 'lasya sri',
            lastName: 'pulavarthi',
            category: 'Class Representative',
            role: 'Class Representative (CSD III Year)',
            designation: 'Class Representative (CSD III Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '3rd Year',
            section: 'CSD – III Year',
            regNo: '25B95A6208',
            isCR: true,
            searchableAliases: ['pulavarthi mohana madhu lasya sri', 'lasya sri', 'pulavarthi'],
            description: 'Pulavarthi Mohana Madhu Lasya Sri is the Class Representative for CSD III Year (Reg No: 25B95A6208).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_sai_harsha',
            fullName: 'P SAI HARSHA',
            firstName: 'sai harsha',
            lastName: 'p',
            category: 'Class Representative',
            role: 'Class Representative (CSD IV Year)',
            designation: 'Class Representative (CSD IV Year)',
            department: 'CSD',
            branch: 'CSD',
            year: '4th Year',
            section: 'CSD – IV Year',
            regNo: '23B81A6252',
            isCR: true,
            searchableAliases: ['p sai harsha', 'sai harsha'],
            description: 'P Sai Harsha is the Class Representative for CSD IV Year (Reg No: 23B81A6252).',
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
            section: 'CSD – IV Year',
            regNo: '23B91A6255',
            isCR: true,
            searchableAliases: ['p swapna', 'swapna'],
            description: 'P Swapna is the Class Representative for CSD IV Year (Reg No: 23B91A6255).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_divya_jyothika',
            fullName: 'R DIVYA JYOTHIKA',
            firstName: 'divya jyothika',
            lastName: 'r',
            category: 'Class Representative',
            role: 'Class Representative (CSIT IV Year)',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '4th Year',
            section: 'CSIT – IV Year',
            regNo: '23B91A0747',
            isCR: true,
            searchableAliases: ['r divya jyothika', 'divya jyothika'],
            description: 'R Divya Jyothika is the Class Representative for CSIT IV Year (Reg No: 23B91A0747).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_sai_vikas',
            fullName: 'CH SAI VIKAS',
            firstName: 'sai vikas',
            lastName: 'ch',
            category: 'Class Representative',
            role: 'Class Representative (CSIT IV Year)',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            branch: 'CSIT',
            year: '4th Year',
            section: 'CSIT – IV Year',
            regNo: '23B91A0706',
            isCR: true,
            searchableAliases: ['ch sai vikas', 'sai vikas'],
            description: 'Ch Sai Vikas is the Class Representative for CSIT IV Year (Reg No: 23B91A0706).',
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        }
    ];

    // Build Master CR Index dynamically
    const MASTER_CR_INDEX = MASTER_PERSON_INDEX.filter(p => p.isCR);

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

                // Check if already in index
                const exists = MASTER_PERSON_INDEX.some(p => normalizePersonName(p.fullName) === normName);
                if (!exists) {
                    MASTER_PERSON_INDEX.push({
                        id: `house_student_${m.name.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase()}`,
                        fullName: m.name,
                        firstName: firstName,
                        lastName: lastName,
                        category: `Student (${h.name} House Member)`,
                        role: `Student Member — ${h.name} House`,
                        designation: `Student Member — ${h.name} House`,
                        department: 'CSD & CSIT',
                        branch: 'CSD / CSIT',
                        year: 'Student',
                        section: m.section || 'A/B',
                        regNo: m.regNo !== 'N/A' ? m.regNo : null,
                        description: `${m.name} is a student member of ${h.name} House in SRKREC CSD & CSIT Department.`,
                        searchableAliases: [m.name.toLowerCase(), firstName, lastName],
                        url: `house_detail.php?house=${h.name}`,
                        ctaText: `View ${h.name} House Roster →`
                    });
                }
            }
        }
    })();

    const MASTER_FACULTY_ROSTER = MASTER_PERSON_INDEX.filter(p => p.category.includes('Faculty') || p.category.includes('HOD'));

    /**
     * =========================================================================
     * 5. GRANULAR WEBSITE KNOWLEDGE MATRIX (ALL SECTIONS COVERED)
     * =========================================================================
     */
    const KNOWLEDGE_MATRIX = [
        {
            id: 'heroes_overview',
            category: 'Department Heroes',
            title: 'Heroes of the Department (Hall of Fame)',
            keywords: ['heroes of department', 'department heroes', 'who are the heroes', 'hall of fame', 'student heroes', 'department hero', 'heroes list'],
            tokens: ['heroes', 'hero', 'fame', 'achievers', 'leaders', 'organizers'],
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
            id: 'faculty_overview',
            category: 'Faculty Directory',
            title: 'CSD & CSIT Department Faculty Members',
            keywords: ['faculty members', 'who are the faculty', 'faculty directory', 'list of faculty', 'teachers', 'professors', 'staff', 'faculties'],
            tokens: ['faculty', 'professors', 'teachers', 'staff', 'hod', 'hods'],
            content: `Department Faculty Leadership:
• HOD CSD: Dr. M. Suresh Babu (Professor & Head of Department, CSD)
• HOD CSIT: Dr. N. Gopala Krishna Murthy (Professor & Head of Department, CSIT)
• Key Faculty: Angara Satyam Sir (Assistant Professor CSD), K V V Satya Trinadh Naidu Sir (Assistant Professor CSIT), P Manoj Sir, A Aswini Priyanka Madam, S Mohan Krishna Sir, P S V Surya Kumar Sir, Dr. K. Srinivasa Rao Sir, K. Bhanu Rajesh Naidu Sir, N. Aneela Madam, and 15+ faculty members.`,
            url: 'faculty.php',
            ctaText: 'View Complete Faculty Directory →'
        },
        {
            id: 'students_overview',
            category: 'Student Directory',
            title: 'CSD & CSIT Student Body & Sections',
            keywords: ['who are the students', 'student body', 'students list', 'sections', 'student directory', 'csd students', 'csit students'],
            tokens: ['students', 'student', 'sections', 'csd', 'csit', 'houses'],
            content: `CSD & CSIT Student Directory & Academic Sections:
• Total Enrolled Students: 600+ across 2nd, 3rd, and 4th Years in CSD & CSIT.
• Academic Sections: CSD II Year, CSD III Year, CSD IV Year, CSIT II Year Sec A & B, CSIT III Year Sec A & B, CSIT IV Year.
• Student Houses: Jal, Agni, Vayu, Akash, Prudhvi.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Student Directory & Leadership →'
        },
        {
            id: 'live_announcements',
            category: 'Announcements',
            title: 'Live Updates & Current Event Spotlight',
            keywords: ['live updates', 'upcoming event', 'irumudi', 'trailer launch', 'movie launch', 'ravi teja', 'august 12', 'srkr grounds', 'announcements', 'latest update'],
            tokens: ['live', 'update', 'irumudi', 'trailer', 'launch', 'august', 'ravi', 'teja', 'srkr', 'grounds'],
            content: `Live Updates & Current Spotlight:
• "Irumudi" Grand Trailer Launch Event: August 12th from 4:30 PM onwards at SRKR Engineering College Grounds, Bhimavaram.
• Event Highlights: Featuring Mass Maharaja Ravi Teja film trailer launch presented by Mythri Movie Makers, T-Series, and YouWe Media.`,
            url: 'index.php',
            ctaText: 'View Live Updates on Homepage →'
        },
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
            id: 'courses_overview',
            category: 'Academics',
            title: 'Academic Degree Programs & Offered Courses',
            keywords: ['what courses are offered', 'courses', 'programs', 'b.tech csd', 'b.tech csit', 'curriculum', 'academics', 'degrees', 'syllabus'],
            tokens: ['courses', 'offered', 'btech', 'degree', 'curriculum', 'academics', 'syllabus'],
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
            keywords: ['what labs are available', 'labs', 'laboratories', 'infrastructure', 'computer labs', 'ai lab', 'iot lab', 'hardware'],
            tokens: ['labs', 'laboratories', 'infrastructure', 'pcs', 'software', 'hardware', 'equipment'],
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
            keywords: ['tell me about startups', 'startups', 'incubation', 'entrepreneurship', 'bhimavaram online', 'smart wash', 'lunch box', 'nutridelight', 'campus online'],
            tokens: ['startups', 'incubation', 'entrepreneurship', 'ventures', 'bhimavaram', 'smartwash', 'lunchbox'],
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
            keywords: ['tell me about internships', 'internships', 'internship', 'stipend', 'ppo', 'paid internship', 'industry training'],
            tokens: ['internships', 'internship', 'stipend', 'ppo', 'training', 'companies'],
            content: `Student Internships & Industry Placements:
• Over 85% of CSD & CSIT students complete paid industry internships at top tech companies.
• Highest Internship Stipend: ₹45,000/month.
• Major Recruiters & Internship Partners: Amazon, TCS, Wipro, Infosys, Tech Mahindra, Cognizant, and AI Startups.`,
            url: 'placements_internships.php',
            ctaText: 'View Placements & Internships →'
        },
        {
            id: 'placements_overview',
            category: 'Placements',
            title: 'Placement Statistics & Recruiters',
            keywords: ['tell me about placements', 'placements', 'highest package', 'average package', 'placement record', 'recruiters', 'jobs'],
            tokens: ['placements', 'placement', 'package', 'lpa', 'recruiters', 'jobs'],
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
            keywords: ['what clubs are available', 'clubs', 'activities', 'coding club', 'design club', 'tedx', 'nss', 'cultural club'],
            tokens: ['clubs', 'activities', 'societies', 'events', 'tedx', 'nss'],
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
            keywords: ['contact', 'address', 'location', 'phone', 'email', 'where is college', 'bhimavaram', 'contact information'],
            tokens: ['contact', 'address', 'location', 'phone', 'email', 'bhimavaram', 'srkrec'],
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
     * 6. PERSON-FIRST MULTI-PRIORITY SEARCH ALGORITHM
     * Matches Full Name -> Normalized Name -> Unique First Name -> Unique Last Name -> Reg No
     * =========================================================================
     */
    function searchPersonSystem(rawQuery) {
        if (!rawQuery) return null;

        const candidateName = extractCandidateName(rawQuery);
        const queryIntent = detectQueryIntent(rawQuery);

        if (!candidateName || candidateName.length < 2) return null;

        // Skip topic keyword queries
        const topicKeywords = /^\b(the|department|college|hod|hods|head|head of department|hero|heroes|department heroes|faculty|faculties|student|students|houses|courses|labs|placements|startups|incubation|events|clubs|syllabus|contact|achievements|internships|research|publications|projects|cr|crs|c\.r\.|c\.r\.s|class representative|class representatives|class rep|class reps|jal|agni|vayu|akash|aakash|prudhvi|pruthvi)\b$/i;
        if (topicKeywords.test(candidateName)) return null;

        // Check Reg No match
        const regMatch = rawQuery.match(/\b([0-9]{2}[a-z0-9]{8,10})\b/i);
        if (regMatch) {
            const searchedReg = regMatch[1].toUpperCase();
            const foundByReg = MASTER_PERSON_INDEX.find(p => p.regNo && p.regNo.toUpperCase() === searchedReg);
            if (foundByReg) {
                return {
                    found: true,
                    isMultiple: false,
                    person: foundByReg,
                    intent: queryIntent,
                    rawQuery: rawQuery
                };
            }
        }

        // Priority 1: Exact Full-Name Match
        const exactMatches = MASTER_PERSON_INDEX.filter(p => {
            const normFull = normalizePersonName(p.fullName);
            return candidateName === normFull;
        });

        if (exactMatches.length === 1) {
            return {
                found: true,
                isMultiple: false,
                person: exactMatches[0],
                intent: queryIntent,
                rawQuery: rawQuery
            };
        }

        // Priority 2: Alias / Substring Full-Name Match
        const aliasMatches = MASTER_PERSON_INDEX.filter(p => {
            const normFull = normalizePersonName(p.fullName);
            if (candidateName.length >= 4 && (normFull.includes(candidateName) || candidateName.includes(normFull))) {
                return true;
            }
            if (p.searchableAliases) {
                return p.searchableAliases.some(alias => normalizePersonName(alias) === candidateName);
            }
            return false;
        });

        if (aliasMatches.length === 1) {
            return {
                found: true,
                isMultiple: false,
                person: aliasMatches[0],
                intent: queryIntent,
                rawQuery: rawQuery
            };
        }

        // Priority 3: Unique First-Name Match
        const firstNameMatches = MASTER_PERSON_INDEX.filter(p => {
            const normFirst = normalizePersonName(p.firstName);
            return candidateName === normFirst || (candidateName.length >= 3 && normFirst.startsWith(candidateName));
        });

        if (firstNameMatches.length === 1) {
            return {
                found: true,
                isMultiple: false,
                person: firstNameMatches[0],
                intent: queryIntent,
                rawQuery: rawQuery
            };
        }

        // Priority 4: Unique Last-Name Match
        const lastNameMatches = MASTER_PERSON_INDEX.filter(p => {
            const normLast = normalizePersonName(p.lastName);
            return candidateName.length >= 4 && (candidateName === normLast || normLast.startsWith(candidateName));
        });

        if (lastNameMatches.length === 1) {
            return {
                found: true,
                isMultiple: false,
                person: lastNameMatches[0],
                intent: queryIntent,
                rawQuery: rawQuery
            };
        }

        // Priority 5: Multiple Candidate Disambiguation
        const totalCandidates = Array.from(new Set([...exactMatches, ...aliasMatches, ...firstNameMatches, ...lastNameMatches]));
        if (totalCandidates.length > 1) {
            return {
                found: true,
                isMultiple: true,
                candidates: totalCandidates,
                rawQuery: rawQuery
            };
        }

        // Priority 6: Explicit Person Question Not Found
        const isExplicitPersonQuery = /^\b(who is|tell me about|profile of|info on|details of|which department does|which branch is|what is the role of)\b/i.test(rawQuery.trim());
        if (isExplicitPersonQuery) {
            const formattedName = candidateName.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            return {
                found: false,
                requestedName: formattedName,
                rawQuery: rawQuery
            };
        }

        return null;
    }

    /**
     * =========================================================================
     * 7. CLASS REPRESENTATIVE (CR) INTENT ENGINE
     * Synonym Mapping: CR, CRs, C.R., C.R.s, Class Representative, Class Reps
     * =========================================================================
     */
    function searchCRSystem(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        const isCRQuery = /\b(cr|crs|c\.r\.|c\.r\.s|class representative|class representatives|class rep|class reps)\b/i.test(lower);
        if (!isCRQuery) return null;

        let filteredCRs = [...MASTER_CR_INDEX];

        const hasCSD = /\bcsd\b/i.test(lower);
        const hasCSIT = /\bcsit\b/i.test(lower);
        if (hasCSD && !hasCSIT) filteredCRs = filteredCRs.filter(cr => cr.branch === 'CSD');
        else if (hasCSIT && !hasCSD) filteredCRs = filteredCRs.filter(cr => cr.branch === 'CSIT');

        if (/\b(2nd|2|second|ii)\b/i.test(lower)) filteredCRs = filteredCRs.filter(cr => cr.year === '2nd Year');
        else if (/\b(3rd|3|third|iii)\b/i.test(lower)) filteredCRs = filteredCRs.filter(cr => cr.year === '3rd Year');
        else if (/\b(4th|4|fourth|iv)\b/i.test(lower)) filteredCRs = filteredCRs.filter(cr => cr.year === '4th Year');

        if (/\b(section a|sec a|\ba\b)\b/i.test(lower) && !/\b(section b|sec b|\bb\b)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.section === 'Section A');
        } else if (/\b(section b|sec b|\bb\b)\b/i.test(lower) && !/\b(section a|sec a|\ba\b)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.section === 'Section B');
        }

        let filterTitle = 'Department Class Representatives (CRs)';
        if (hasCSD && !hasCSIT) filterTitle = 'CSD Class Representatives (CRs)';
        if (hasCSIT && !hasCSD) filterTitle = 'CSIT Class Representatives (CRs)';

        let groupedText = filteredCRs.map(cr => `• <strong>${cr.fullName}</strong> — Reg: ${cr.regNo || 'N/A'} | ${cr.section ? cr.section : cr.year + ' ' + cr.branch}`).join('<br>');

        return {
            id: 'cr_list_result',
            category: 'Class Representatives',
            title: filterTitle,
            content: `Our department has 14 Class Representatives across 2nd, 3rd, and 4th Years for CSD & CSIT:<br><br>${groupedText}`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View All Class Representatives on Website →'
        };
    }

    /**
     * =========================================================================
     * 8. HOUSE SYSTEM INTENT ENGINE
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

        const displayName = houseData.name;
        const membersList = houseData.members;

        let displayedMembers = membersList.slice(0, 15);
        let listItems = displayedMembers.map((m, idx) => `${idx + 1}. <strong>${m.name}</strong> — Reg: ${m.regNo || 'N/A'} | Section: ${m.section || 'CSD/CSIT'}`).join('<br>');

        return {
            id: `house_members_${requestedHouseKey}`,
            category: 'House Members',
            title: `${displayName} House Members`,
            content: `Here are the members of <strong>${displayName} House</strong> (Total: ${membersList.length} students):<br><br>${listItems}<br><br><em>Showing top 15 of ${membersList.length} members. View full roster on house page.</em>`,
            url: `house_detail.php?house=${displayName}`,
            ctaText: `View Full ${displayName} House Roster →`
        };
    }

    /**
     * =========================================================================
     * 9. FIELD-LEVEL ANSWER SYNTHESIZER FOR PERSON QUERIES
     * Enforces precision, zero hallucination, zero generic list dumps.
     * =========================================================================
     */
    function formatFieldLevelAnswer(person, intent, rawQuery) {
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
     * 10. PRIMARY RAG HYBRID DISPATCHER
     * Priority Order: Person Search -> House System -> CR System -> Knowledge Matrix
     * =========================================================================
     */
    function searchKnowledgeVector(rawQuery) {
        if (!rawQuery) return null;

        // Priority 1: Person-First Search
        const personMatch = searchPersonSystem(rawQuery);
        if (personMatch) {
            if (personMatch.found) {
                if (personMatch.isMultiple) {
                    let listItems = personMatch.candidates.map((p, idx) => `${idx + 1}. <strong>${p.fullName}</strong> (${p.role || p.category})`).join('<br>');
                    return {
                        id: 'people_multiple_matches',
                        category: 'People Search',
                        title: 'Multiple Matching People Found',
                        content: `I found multiple people matching your question:<br><br>${listItems}<br><br>Could you please specify the person's full name, year, section, or registration number?`,
                        url: 'heroes_of_department.php',
                        ctaText: 'Explore Department People Directory →'
                    };
                } else {
                    console.log('[CHATBOT RAG] Person Match Found:', personMatch.person.fullName);
                    return formatFieldLevelAnswer(personMatch.person, personMatch.intent, rawQuery);
                }
            } else if (personMatch.requestedName) {
                console.log('[CHATBOT RAG] Person Not Found:', personMatch.requestedName);
                return {
                    id: 'person_not_found',
                    category: 'People Search',
                    title: 'Person Not Found',
                    isNotFound: true,
                    requestedName: personMatch.requestedName,
                    content: `I couldn't find a person named <strong>${personMatch.requestedName}</strong> in the current department records.`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Department Directory →'
                };
            }
        }

        // Special handling for HOD query
        if (/\b(hod|hods|head of department|head of the department)\b/i.test(rawQuery)) {
            return {
                id: 'hod_overview',
                category: 'Department Leadership',
                title: 'Heads of Department (HODs)',
                content: `Our department has two distinguished Heads of Department (HODs):<br><br>
1. <strong>Dr. M. Suresh Babu</strong> — Professor & Head of Department, Computer Science & Design (CSD)<br>• Email: suresh.mudunuri@srkrec.ac.in | Qualification: Ph.D (2010) | Experience: 20+ Years<br><br>
2. <strong>Dr. N. Gopala Krishna Murthy</strong> — Professor & Head of Department, Computer Science & Information Technology (CSIT)<br>• Email: gopinukala@gmail.com | Qualification: Ph.D (2011) | Experience: 18+ Years`,
                url: 'faculty.php',
                ctaText: 'View Faculty Leadership Page →'
            };
        }

        // Priority 2: House Member & Leaderboard Search
        const houseResult = searchHouseSystem(rawQuery);
        if (houseResult) {
            console.log('[CHATBOT RAG] House Match Found:', houseResult.title);
            return houseResult;
        }

        // Priority 3: Class Representative (CR) Search
        const crResult = searchCRSystem(rawQuery);
        if (crResult) {
            console.log('[CHATBOT RAG] CR Match Found:', crResult.title);
            return crResult;
        }

        // Priority 4: Knowledge Matrix Keyword / Section Reranking
        let scoredChunks = KNOWLEDGE_MATRIX.map(chunk => {
            let score = 0;
            const qLower = rawQuery.toLowerCase().trim();
            for (const kw of chunk.keywords) {
                if (qLower.includes(kw.toLowerCase())) score += 200;
            }
            const tokens = qLower.replace(/[^a-z0-9\s]/g, ' ').split(/\s+/).filter(t => t.length > 2);
            for (const token of tokens) {
                if (chunk.tokens && chunk.tokens.includes(token)) score += 30;
                if (new RegExp(`\\b${token}\\b`, 'i').test(chunk.title)) score += 40;
                if (new RegExp(`\\b${token}\\b`, 'i').test(chunk.category)) score += 35;
            }
            return { chunk: chunk, score: score };
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
     * 11. LOCAL ANSWER SYNTHESIZER
     * =========================================================================
     */
    function synthesizeLocalAnswer(matchedChunk, rawQuery) {
        if (!matchedChunk) {
            return {
                answer: `I couldn't find that specific information on the department website. You can contact the department office for further details.`,
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
            suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?', 'Tell me about startups']
        };
    }

    async function callGeminiDirect(userInput, matchedChunk, apiKey) {
        const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

        let systemInstruction = `You are the official AI Assistant for the Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram.\n\n`;
        if (matchedChunk) {
            if (matchedChunk.isNotFound) {
                systemInstruction += `NOTICE: The requested subject '${matchedChunk.requestedName}' is NOT in department records.\nAnswer ONLY: '${matchedChunk.content}'\n\n`;
            } else {
                systemInstruction += `VERIFIED WEBSITE RAG CONTEXT:\nTitle: ${matchedChunk.title}\nContent: ${matchedChunk.content}\n\n`;
                systemInstruction += `Instructions: Answer using the verified website context above. Format output using clean HTML/Markdown.`;
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

    const responseCache = new Map();

    /**
     * =========================================================================
     * 12. MAIN PUBLIC METHOD: getBotResponse
     * =========================================================================
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
                return cachedRes;
            }

            // Casual greetings
            if (/^(hi|hello|hey|greetings|good morning|good afternoon|good evening)$/i.test(normalizedQuery)) {
                const greetingRes = {
                    answer: `Hello! 👋 I'm the official AI Department Assistant for SRKR CSD & CSIT. How can I help you today?`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?', 'Who is Satyam Sir?']
                };
                responseCache.set(normalizedQuery, greetingRes);
                return greetingRes;
            }

            if (/^(how are you|how are you\?|how r u)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I'm doing great! Thank you for asking. 😊 I'm fully equipped to answer questions about house members, faculty, student heroes, CRs, courses, labs, placements, and startups. How can I assist you today?`,
                    ctaLinks: [{ text: 'View Department Overview →', url: 'explore.php' }],
                    suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what is your name\??|who are you\??|what are you\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `I am the official **Department AI Assistant** for the Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) departments at SRKR Engineering College, Bhimavaram.`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['What can you do?', 'Jal house members', 'Who is the HOD?']
                };
                responseCache.set(normalizedQuery, res);
                return res;
            }

            if (/^(what can you do\??|help|what can i ask\??)$/i.test(normalizedQuery)) {
                const res = {
                    answer: `Here is what I can help you with:<br><br>
• <strong>House Members</strong> (e.g. "Jal house members", "Agni house members", "Vayu house members")<br>
• <strong>Class Representatives (CRs)</strong> (e.g. "Who is Mohana Durga?", "Who are the CRs?")<br>
• <strong>Faculty Information</strong> (e.g. "Who is Mohan Krishna?", "Who is Satyam Sir?")<br>
• <strong>Department Heroes & Achievers</strong> (e.g. "Who is Preeti?", "Who is Mullu Srinu?")<br>
• <strong>Academics & Courses</strong> (e.g. "What courses are available?")<br>
• <strong>Laboratories & Infrastructure</strong> (e.g. "What labs are available?")<br>
• <strong>Placements & Internships</strong> (e.g. "Tell me about placements", "Tell me about internships")<br>
• <strong>Startups & Incubation</strong> (e.g. "What startups are there?")<br>
• <strong>Events & Contact Info</strong> (e.g. "What events are there?", "What is the contact information?")`,
                    ctaLinks: [{ text: 'Explore Department →', url: 'explore.php' }],
                    suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
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
                // Smart Bypass for factual person / house / CR / local RAG queries
                if (matchedChunk.isPersonQuery || matchedChunk.category === 'House Members' || matchedChunk.category === 'Student Houses Overview' || matchedChunk.category.includes('Class Representative') || matchedChunk.category.includes('Faculty') || matchedChunk.category.includes('Hero') || matchedChunk.category.includes('People')) {
                    console.log('[CHATBOT] Smart Bypass: Person/CR/House query. Executing local RAG synthesis for maximum precision.');
                    finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                    responseCache.set(normalizedQuery, finalResponse);
                    return finalResponse;
                }
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
                            suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
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
            conversationContext = { activeEntity: null, activeTopic: null, activeHouse: null, lastQuery: null, history: [] };
        }
    };
})();
