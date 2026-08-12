/**
 * AI Department Assistant — Google Gemini API & Scalable Hybrid RAG Engine
 * SRKREC CSD & CSIT Departments
 *
 * Strict Person, CR & House Member Search Architecture:
 * 1. Tokenized Exact Word Equality Matcher (Prevents "Mohana" matching "Mohan" substring bleed)
 * 2. Master House Roster Engine (All 612 verified house members for Jal, Agni, Vayu, Akash, Prudhvi)
 * 3. Master Class Representatives (CR) Engine (14 Verified CRs from website)
 * 4. Priority Retrieval Pipeline:
 *    - Priority 1: House Member / House Overview / House Follow-up Search
 *    - Priority 2: Class Representatives (CR) Intent & Filtering Engine
 *    - Priority 3: Exact Registration Number & Tokenized Person Search
 *    - Priority 4: Granular Website Knowledge Matrix (22 Section Chunks)
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
        history: [] // Array of { role: 'user'|'model', text: string }
    };

    /**
     * =========================================================================
     * 1. GENERIC NAME NORMALIZER & TOKENIZER
     * =========================================================================
     */
    function normalizePersonName(str) {
        if (!str) return '';
        let s = str.toLowerCase().trim();
        s = s.replace(/\b(dr\.|dr|prof\.|prof|professor|mr\.|mr|mrs\.|mrs|ms\.|ms|miss|sir|madam|ma'am|mam|teacher|faculty)\b/g, '');
        s = s.replace(/^(who is|tell me about|information about|info on|details of|details about|profile of|who|tell me|about|give details of|show profile of|is|a cr|a class representative)/g, '');
        s = s.replace(/[\?\!\.\,]/g, '');
        return s.replace(/\s+/g, ' ').trim();
    }

    function tokenizeName(str) {
        if (!str) return [];
        let clean = str.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').trim();
        clean = clean.replace(/\b(who|is|tell|me|about|give|details|of|show|profile|the|a|an|registration|number|reg|no)\b/g, '');
        return clean.split(/\s+/).filter(t => t.length > 0);
    }

    /**
     * =========================================================================
     * 2. MASTER HOUSE ROSTER ENGINE (612 VERIFIED HOUSE MEMBERS FROM DATABASE)
     * Data Source: house_detail.php & Database houses/students tables
     * =========================================================================
     */
    const MASTER_HOUSE_ROSTER = {
        'JAL': {
            name: 'Jal',
            description: 'Water House - Flowing with wisdom and adaptability like the eternal river.',
            members: [{"name":"ABDUL SHARIFUNNISA","regNo":"N\/A","section":"A","points":0},{"name":"ARETI JAYA CHARAN KRISHNA","regNo":"N\/A","section":"B","points":0},{"name":"BANDE DALI AKSHAYA","regNo":"N\/A","section":"A","points":0},{"name":"BAREPU VAMSI","regNo":"N\/A","section":"B","points":0},{"name":"BARRI SRAVYA SREE","regNo":"N\/A","section":"A","points":0},{"name":"BEERA YASMIN","regNo":"N\/A","section":"A","points":0},{"name":"BEJAVADA V S S N RAMA GANESH","regNo":"N\/A","section":"B","points":0},{"name":"BELAMARA SIVANI","regNo":"N\/A","section":"A","points":0},{"name":"BELLAPU J S VENKATA DURGA NAGA ASRITHA","regNo":"N\/A","section":"A","points":0},{"name":"BODDETI DEVI NAGA VENKATA SAI DEEPAK","regNo":"N\/A","section":"A","points":0},{"name":"BODDETI SARVANI","regNo":"N\/A","section":"A","points":0},{"name":"BONAM ADI LAKSHAMMA","regNo":"N\/A","section":"A","points":0},{"name":"BONIGALA RISHITHA","regNo":"N\/A","section":"B","points":0},{"name":"BORRA TERESSA","regNo":"N\/A","section":"A","points":0},{"name":"BUDDIGA GAYATRI","regNo":"N\/A","section":"A","points":0},{"name":"CHADARAM BHANU VENKATA MANIKANTA","regNo":"N\/A","section":"A","points":0},{"name":"CHIKKALA SHYAM KISHORE","regNo":"N\/A","section":"B","points":0},{"name":"CHINTADA NISSY SUDEEPTHI","regNo":"N\/A","section":"A","points":0},{"name":"CHINTAPALLI NAGA SYAMALA","regNo":"N\/A","section":"A","points":0},{"name":"CHITTALA DILEEP RAM KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"DAGGU ROHITH SUBRAHMANYA SAI","regNo":"N\/A","section":"A","points":0},{"name":"DAMMU PRANEETH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"DODDI NIVEDITHA","regNo":"N\/A","section":"A","points":0},{"name":"DODDIPATLA DANA VENKATA SIVASANKAR","regNo":"N\/A","section":"A","points":0},{"name":"DOMMETI SAI NIKHITHA","regNo":"N\/A","section":"A","points":0},{"name":"DONAVALLI REVATHI","regNo":"N\/A","section":"A","points":0},{"name":"DONTHU VIJAYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"EUDU HARSHA VARDHAN","regNo":"N\/A","section":"A","points":0},{"name":"GANDREDDY RAM GANESH","regNo":"N\/A","section":"A","points":0},{"name":"GANESNA SATYA RAJESH","regNo":"N\/A","section":"A","points":0},{"name":"GEDDAM JACINTHA","regNo":"N\/A","section":"A","points":0},{"name":"GOLLAPALLI ROHAN SAMIT","regNo":"N\/A","section":"A","points":0},{"name":"GOPINEEDI DIVIJA","regNo":"N\/A","section":"A","points":0},{"name":"GOTTUMUKKALA BHARGAVI","regNo":"N\/A","section":"A","points":0},{"name":"INUMARTHI SRINAVYA","regNo":"N\/A","section":"A","points":0},{"name":"JADDU LEELA PAVAN KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"JAKKAMPUDI REVANTH","regNo":"N\/A","section":"A","points":0},{"name":"JALLI SURENDRA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"JOGI PRASANTH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KACHETTI RUCHITA LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"KADIYALA NAVYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"KANNIPAMULA TEJASWI","regNo":"N\/A","section":"B","points":0},{"name":"KAPUDASI SNIGDHA","regNo":"N\/A","section":"A","points":0},{"name":"KARIMERAKA DOLLY GANYA","regNo":"N\/A","section":"A","points":0},{"name":"KAROTHI SAI MANIKANTA","regNo":"N\/A","section":"A","points":0},{"name":"KATIKI RAJANI","regNo":"N\/A","section":"A","points":0},{"name":"KETHA SURYA PRAKASH","regNo":"N\/A","section":"A","points":0},{"name":"KETHINEDI SRI RAM","regNo":"N\/A","section":"A","points":0},{"name":"KODETI SATISH","regNo":"N\/A","section":"A","points":0},{"name":"KODI VAISHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"KOLA YESWANTH","regNo":"N\/A","section":"A","points":0},{"name":"KOSETTI AHARON KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KUKUNOORI POORNA SRI CHANDRA SEKHAR","regNo":"N\/A","section":"A","points":0},{"name":"KUNCHE SRI NAGA GANESH","regNo":"N\/A","section":"A","points":0},{"name":"KURASALA HARSHA VARDHAN","regNo":"N\/A","section":"A","points":0},{"name":"MAILABATTULA LOUKYATHA","regNo":"N\/A","section":"A","points":0},{"name":"MALLABATTULA SIVA KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"MANDA RAJA PRASANNA KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"MANELLI SRAVANI","regNo":"N\/A","section":"A","points":0},{"name":"MATTAPARTHI REETHIKA","regNo":"N\/A","section":"A","points":0},{"name":"MOTUPALLI MEENA PHANI SRI","regNo":"N\/A","section":"B","points":0},{"name":"MULE ADILAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"MUTCHARLA YASASWI","regNo":"N\/A","section":"A","points":0},{"name":"NAGISETTY VISHNUVARDHAN","regNo":"N\/A","section":"A","points":0},{"name":"NAKKA MOHITH SRI NAGA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"NALLAM HEMA SAI SRI LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"NELAPOGULA SRI POSI LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"NELAPUDI PRASANTH SEKHAR","regNo":"N\/A","section":"B","points":0},{"name":"NETHULA MAHESH","regNo":"N\/A","section":"B","points":0},{"name":"NOUPADA LIKHITHA","regNo":"N\/A","section":"A","points":0},{"name":"PALA THANUJA","regNo":"N\/A","section":"B","points":0},{"name":"PANDAVA MEGHANA CHOUDHARY","regNo":"N\/A","section":"A","points":0},{"name":"PANKAJ NARAYAN TYADA","regNo":"N\/A","section":"A","points":0},{"name":"PASUPULETI JASWANTH RAMANA TEJA","regNo":"N\/A","section":"A","points":0},{"name":"PECHETTI LAKSHMI TANUJA","regNo":"N\/A","section":"A","points":0},{"name":"PEPETI GANESH","regNo":"N\/A","section":"A","points":0},{"name":"PETTA PRANATHI","regNo":"N\/A","section":"A","points":0},{"name":"POGIRI BHANU PRASAD","regNo":"N\/A","section":"A","points":0},{"name":"PONNAGANTI JYOTHIKA SAI","regNo":"N\/A","section":"B","points":0},{"name":"POTHAMSETTI KODANDA RAMA NAGA GANESH","regNo":"N\/A","section":"A","points":0},{"name":"REDDI GEETHIKA","regNo":"N\/A","section":"A","points":0},{"name":"RELANGI JYOTHSNA SRI","regNo":"N\/A","section":"A","points":0},{"name":"SAKHIMSETTI HARI SATYA PRIYA DEVI","regNo":"N\/A","section":"B","points":0},{"name":"SAMBANGI VENKATA JASWANTH","regNo":"N\/A","section":"A","points":0},{"name":"SARELLA VINCY ANGELINE","regNo":"N\/A","section":"A","points":0},{"name":"SETTI NARENDRA KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK AMEENA","regNo":"N\/A","section":"B","points":0},{"name":"SIDDA MAHESH","regNo":"N\/A","section":"A","points":0},{"name":"SIRAPARAPU PRANATHI SAI VARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"SRIKAKULAPU SANTHI PRIYA","regNo":"N\/A","section":"A","points":0},{"name":"SUTHAPALLI SRI PAVAN KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"TAMMA LOKESH","regNo":"N\/A","section":"A","points":0},{"name":"TANINKI SREEDHAR","regNo":"N\/A","section":"A","points":0},{"name":"THOTA JOHAN BENEDICT","regNo":"N\/A","section":"B","points":0},{"name":"TUMMA SRI HARSHA","regNo":"N\/A","section":"A","points":0},{"name":"UNDAPALLI DIVYA","regNo":"N\/A","section":"A","points":0},{"name":"UTTARILLI HARSHA VARDHAN","regNo":"N\/A","section":"B","points":0},{"name":"VAKAPALLI H V SAI SURYA SWAPANTH","regNo":"N\/A","section":"A","points":0},{"name":"VAKAPALLI PHANI SAI MUKESH","regNo":"N\/A","section":"A","points":0},{"name":"VANAPARTHI ASMITHA VYSHNAVI","regNo":"N\/A","section":"B","points":0},{"name":"VASKA JYOTHI","regNo":"N\/A","section":"B","points":0},{"name":"VEERANKI MAHESH BABU","regNo":"N\/A","section":"A","points":0},{"name":"VEMAVARAPU MADHU SARIKA","regNo":"N\/A","section":"A","points":0},{"name":"VENKATA NISHITHA REDDY DATLA","regNo":"N\/A","section":"B","points":0},{"name":"YALLA CHANDANA","regNo":"N\/A","section":"A","points":0},{"name":"YALLAPU TANUJA","regNo":"N\/A","section":"B","points":0},{"name":"YATHAM LAKSHMI PRASANNA","regNo":"N\/A","section":"A","points":0}]
        },
        'AGNI': {
            name: 'Agni',
            description: 'Fire House - Burning with passion and illuminating the path forward.',
            members: [{"name":"ADABALA ROHITH VEERA VENKATA DURGESH","regNo":"N\/A","section":"B","points":0},{"name":"ADDAGARLA R S S K V V S D N RAJESH","regNo":"N\/A","section":"A","points":0},{"name":"AKSHINTALA HARSHATH","regNo":"N\/A","section":"A","points":0},{"name":"ALLADI DILEEP KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"ATCHUTHUNI SAI SPURANTHI","regNo":"N\/A","section":"A","points":0},{"name":"BOKINALA MANJUSHA","regNo":"N\/A","section":"A","points":0},{"name":"BOKKA LIKHITHA","regNo":"N\/A","section":"A","points":0},{"name":"BOMMI VENKATA SAI","regNo":"N\/A","section":"A","points":0},{"name":"BORRA AVINASH","regNo":"N\/A","section":"A","points":0},{"name":"BOTCHA AVINASH","regNo":"N\/A","section":"B","points":0},{"name":"BURRA MANI CHANDU KUTA RAO","regNo":"N\/A","section":"A","points":0},{"name":"CHAMARLAKOTA SIREESH VALI","regNo":"N\/A","section":"A","points":0},{"name":"CHELAMKURI LOHITH","regNo":"N\/A","section":"B","points":0},{"name":"CHETTU BHAVANA","regNo":"N\/A","section":"A","points":0},{"name":"CHIMAKURTHI TEJA RUPAK","regNo":"N\/A","section":"A","points":0},{"name":"CHINDADA JYOTHI","regNo":"N\/A","section":"A","points":0},{"name":"CHINIMILLI SAJEEVUDU","regNo":"N\/A","section":"A","points":0},{"name":"CHINNAM NIKHILESH","regNo":"N\/A","section":"A","points":0},{"name":"CHINTAPALLI PREM TEJA","regNo":"N\/A","section":"A","points":0},{"name":"CHIRAPA ESWAR VENKATA SATYA NARAYANA","regNo":"N\/A","section":"A","points":0},{"name":"CHITAKANA RACHITHA","regNo":"N\/A","section":"A","points":0},{"name":"DAIDA RANI","regNo":"N\/A","section":"A","points":0},{"name":"DASARI KARTHIKEYA","regNo":"N\/A","section":"B","points":0},{"name":"DASARI MOHAN CHANDRA SHEKAR","regNo":"N\/A","section":"B","points":0},{"name":"DHANANI SRI LAKSHMI VENKATA AASHRITA","regNo":"N\/A","section":"A","points":0},{"name":"DONGA JHANSI","regNo":"N\/A","section":"A","points":0},{"name":"DURU MERY SUNEETHA","regNo":"N\/A","section":"A","points":0},{"name":"EDA PRASANTH","regNo":"N\/A","section":"A","points":0},{"name":"GADDAM CHANDRIKA SRI PRIYA","regNo":"N\/A","section":"A","points":0},{"name":"GAYATRI PADHI","regNo":"N\/A","section":"A","points":0},{"name":"GEDA HARI SAI","regNo":"N\/A","section":"B","points":0},{"name":"GHANTA LIKITHA VENKATA RAGHU SAI","regNo":"N\/A","section":"A","points":0},{"name":"GIDUGU NEHANTH SRIHARSHA NAVADEEP","regNo":"N\/A","section":"A","points":0},{"name":"GOWTHU LEELA RUKMINI","regNo":"N\/A","section":"A","points":0},{"name":"GUBBALA GNAANA PRASANNA","regNo":"N\/A","section":"A","points":0},{"name":"GUDAPATI LALITHA DEVI SRI","regNo":"N\/A","section":"A","points":0},{"name":"GUDDALA SAI CHARAN","regNo":"N\/A","section":"A","points":0},{"name":"GUNDUMOGULA SARUPYA","regNo":"N\/A","section":"A","points":0},{"name":"GUTTULA TEJASWI","regNo":"N\/A","section":"A","points":0},{"name":"JAKKAMSETTI SANJANI","regNo":"N\/A","section":"A","points":0},{"name":"JANAKI MADDALA","regNo":"N\/A","section":"A","points":0},{"name":"JOGI ABISHAI","regNo":"N\/A","section":"A","points":0},{"name":"KALIGITA SIDDHU","regNo":"N\/A","section":"A","points":0},{"name":"KAMIREDDY SRI RAMA CHARAN SARESH KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"KANDIBOYINA CHANDRASHEKAR","regNo":"N\/A","section":"A","points":0},{"name":"KANUMURI DEEKSHITA","regNo":"N\/A","section":"A","points":0},{"name":"KARRI LAKSHMI PRASANNA","regNo":"N\/A","section":"A","points":0},{"name":"KAVURU GUNA SRAVANI","regNo":"N\/A","section":"A","points":0},{"name":"KILLADA DAVID ENOSH","regNo":"N\/A","section":"A","points":0},{"name":"KODE NARASIMHA NAIDU","regNo":"N\/A","section":"A","points":0},{"name":"KOLATI STEPHEN SOUDH","regNo":"N\/A","section":"A","points":0},{"name":"KOLLATI SAILAJA","regNo":"N\/A","section":"A","points":0},{"name":"KOLLI SHANMUKHA SRIRAM CHARAN TEJA","regNo":"N\/A","section":"A","points":0},{"name":"KOMARADA KIRAN KISHORE","regNo":"N\/A","section":"A","points":0},{"name":"KONDAPALLI SUBHAKAR BHANCY RAJ","regNo":"N\/A","section":"A","points":0},{"name":"KOPPARTI HONEY NAGA SANDEEP","regNo":"N\/A","section":"A","points":0},{"name":"KORLAPATI GEETHIKA RATNAM","regNo":"N\/A","section":"A","points":0},{"name":"KOTAPATI MAHENDRA REDDY","regNo":"N\/A","section":"A","points":0},{"name":"LALITHA MANOJNA VELIVELA","regNo":"N\/A","section":"A","points":0},{"name":"MADDI AKSHAYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"MALLAVARAPU GANGOTHRI","regNo":"N\/A","section":"A","points":0},{"name":"MANDAPATI VENKATA YAMINI","regNo":"N\/A","section":"A","points":0},{"name":"MANGENA JAHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"MEDABALIMI ADITHYA VARDHAN","regNo":"N\/A","section":"A","points":0},{"name":"MEDIDI BENNYBABU","regNo":"N\/A","section":"A","points":0},{"name":"MOTURI SANDILYA","regNo":"N\/A","section":"A","points":0},{"name":"MUNDRI RAKESH","regNo":"N\/A","section":"A","points":0},{"name":"MUNGARA LOHITH","regNo":"N\/A","section":"A","points":0},{"name":"MURALA NEETHI SURYA","regNo":"N\/A","section":"A","points":0},{"name":"MURIKITHA ARCHANA SAI SRI","regNo":"N\/A","section":"B","points":0},{"name":"NAKKA SUNISCHAL","regNo":"N\/A","section":"A","points":0},{"name":"NANDAMURI BALA SESHA SATYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"NANDE D V V SIVA SWAMY ARAVINDH","regNo":"N\/A","section":"A","points":0},{"name":"NANDIKA LIKHITHA","regNo":"N\/A","section":"A","points":0},{"name":"NARISETTY AKSHAYA NAIDU","regNo":"N\/A","section":"A","points":0},{"name":"NELLURI CHAITRIKA SRI NIDHI","regNo":"N\/A","section":"B","points":0},{"name":"NIMMALA BHANU SRI HARSHA","regNo":"N\/A","section":"B","points":0},{"name":"NUKALA CHARAN JASWANTH","regNo":"N\/A","section":"A","points":0},{"name":"NUKALA KAUSHAL","regNo":"N\/A","section":"A","points":0},{"name":"OGURI LAKSHMI NARAYANA","regNo":"N\/A","section":"B","points":0},{"name":"PACHIGOLLA RISHITHA MANASA SURYA GAYATRI","regNo":"N\/A","section":"A","points":0},{"name":"PAMU AMRUTHA","regNo":"N\/A","section":"B","points":0},{"name":"PANAKALA RAMA NAGESWARA RAO","regNo":"N\/A","section":"A","points":0},{"name":"PENMETSA HARSHINI","regNo":"N\/A","section":"B","points":0},{"name":"PENTAKOTA LEELA SRI","regNo":"N\/A","section":"A","points":0},{"name":"PENTAPATI HARSHA VARDHAN RAJU","regNo":"N\/A","section":"A","points":0},{"name":"PERICHERLA ROHAN KRISHNA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"PINNINTI SIVANI","regNo":"N\/A","section":"A","points":0},{"name":"PONAMANDI PRASHANTH","regNo":"N\/A","section":"A","points":0},{"name":"POSIMSETTY SRI VISWA BHARATH","regNo":"N\/A","section":"A","points":0},{"name":"PULAPARTHI KALYAN VENKATA SAI","regNo":"N\/A","section":"B","points":0},{"name":"PULI DURGA BHAVANI","regNo":"N\/A","section":"A","points":0},{"name":"ROTTE SUSHANTH","regNo":"N\/A","section":"B","points":0},{"name":"SAKHINETIPALLI CHAKRI ADITYA PAVAN KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"SANA SHANMUKHA DURGA","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK DADA KHALANDER","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK NAGUR MADEENA BEGAM","regNo":"N\/A","section":"B","points":0},{"name":"SIDDAMSETTI VIVEK SAI","regNo":"N\/A","section":"B","points":0},{"name":"SIRIPURAPU PARDHA SARADHI","regNo":"N\/A","section":"B","points":0},{"name":"SUNKARA KETHAN SAI","regNo":"N\/A","section":"A","points":0},{"name":"SUNKARA SWATHI","regNo":"N\/A","section":"A","points":0},{"name":"SWARNA GOWTHAMI","regNo":"N\/A","section":"B","points":0},{"name":"TADELA SUSMITHA","regNo":"N\/A","section":"A","points":0},{"name":"TANGUTURI S V NAGA PAVAN SAI","regNo":"N\/A","section":"A","points":0},{"name":"UPPULURI VENKATA JASWANTH","regNo":"N\/A","section":"A","points":0},{"name":"VADDIMUKKALA KRANTHI KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"VADREVU LAHARI DEVI","regNo":"N\/A","section":"B","points":0},{"name":"VALLABHANI SAHITHI","regNo":"N\/A","section":"A","points":0},{"name":"VANUKURI SAI BHARADWAJA REDDY","regNo":"N\/A","section":"A","points":0},{"name":"VARIKUTI ANJALI","regNo":"N\/A","section":"A","points":0},{"name":"VEERAVALLI KUNDANA SAI SANTHI","regNo":"N\/A","section":"A","points":0},{"name":"VEERLAPATI HASINI","regNo":"N\/A","section":"A","points":0},{"name":"VETCHA G N V S L SAISREE","regNo":"N\/A","section":"A","points":0}]
        },
        'VAYU': {
            name: 'Vayu',
            description: 'Wind House - Swift and free like the breeze that carries change.',
            members: [{"name":"A PREETHI","regNo":"N\/A","section":"A","points":0},{"name":"ADDAGARLA HEMANTH NAGA MANIKANTA","regNo":"N\/A","section":"A","points":0},{"name":"ADDAGARLA SRI VIDYA SAGAR","regNo":"N\/A","section":"A","points":0},{"name":"ALAPATI ANASUYA DEVI","regNo":"N\/A","section":"A","points":0},{"name":"ARNEPALLI MEGANA","regNo":"N\/A","section":"A","points":0},{"name":"BAGGU MOHITH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"BANDARU BHANU SATYA PRAKASH","regNo":"N\/A","section":"A","points":0},{"name":"BARAMA NAVYA NAGA RAMYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"BEERA JNANENDRA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"BELLAMKONDA JOSHITHA SHANMUKHI","regNo":"N\/A","section":"A","points":0},{"name":"BHOGIREDDY TEJASRI SAI VAISHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"BODASINGI SHANMUKHA SAI","regNo":"N\/A","section":"A","points":0},{"name":"BOLISETTY KEDARESWARI","regNo":"N\/A","section":"A","points":0},{"name":"BOLLEDDU GIRIDHARA VENKATA SAI","regNo":"N\/A","section":"A","points":0},{"name":"BONDA YOGESH","regNo":"N\/A","section":"B","points":0},{"name":"BORRA HIMA SRI","regNo":"N\/A","section":"A","points":0},{"name":"BUDITHI SAI ADARSH","regNo":"N\/A","section":"A","points":0},{"name":"CHADALAVADA SHAKEENA","regNo":"N\/A","section":"B","points":0},{"name":"CHAGANTI DHANESH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"CHALAMALASETTI SAI DURGA","regNo":"N\/A","section":"A","points":0},{"name":"CHANDANI VIVEKANANDA","regNo":"N\/A","section":"A","points":0},{"name":"CHELLABOYINA YAMINI","regNo":"N\/A","section":"A","points":0},{"name":"CHUNDRU GOWTHAM KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"DACHEPALLI BHANU UDAY","regNo":"N\/A","section":"A","points":0},{"name":"DAKKUMALLA VARSHA","regNo":"N\/A","section":"A","points":0},{"name":"DANDUBOYINA VENKATA PRABHAS","regNo":"N\/A","section":"A","points":0},{"name":"DHARMAVARUPU CHANDANA","regNo":"N\/A","section":"A","points":0},{"name":"EVANA CHANDU VENKATA SAI GANESH","regNo":"N\/A","section":"A","points":0},{"name":"GADAMSETTY VENKATA SAI HARISH","regNo":"N\/A","section":"A","points":0},{"name":"GANDRETI KALYANI","regNo":"N\/A","section":"A","points":0},{"name":"GANTA HARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"GHANTASALA DEEVEN KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"GONAPALA SRI GOWTHAM","regNo":"N\/A","section":"A","points":0},{"name":"GOTTUMUKKALA NIKHILA VALLI","regNo":"N\/A","section":"A","points":0},{"name":"GOWRIPATNAM BHAGYAKIRAN","regNo":"N\/A","section":"A","points":0},{"name":"GUDAPALLI VEENA SRUTHI","regNo":"N\/A","section":"A","points":0},{"name":"GUNDEPALLI SNEHITH","regNo":"N\/A","section":"B","points":0},{"name":"GUNDU TARUN SAI","regNo":"N\/A","section":"A","points":0},{"name":"JAVVADI NEHA","regNo":"N\/A","section":"B","points":0},{"name":"KADALI SRI SURYA SATYA SAI","regNo":"N\/A","section":"B","points":0},{"name":"KARRI REVANTH RATAN REDDY","regNo":"N\/A","section":"A","points":0},{"name":"KATTA DILEEP","regNo":"N\/A","section":"B","points":0},{"name":"KATTA SRAVANI","regNo":"N\/A","section":"A","points":0},{"name":"KELLA CHAKRA VAMSI","regNo":"N\/A","section":"A","points":0},{"name":"KOCHERLA YESWANTH","regNo":"N\/A","section":"A","points":0},{"name":"KOMATI JAYASRI LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"KOTA MADHU VENKATESH","regNo":"N\/A","section":"A","points":0},{"name":"KOTHAPALLI CHINMAY SATYA KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"KUKKALA SUDHEERA","regNo":"N\/A","section":"A","points":0},{"name":"LAKSHMISETTI KAVYA","regNo":"N\/A","section":"A","points":0},{"name":"LINGAMPALLI VIJAY VARDHAN","regNo":"N\/A","section":"A","points":0},{"name":"MADABHUSHI SRI RANGA SUDARSAN","regNo":"N\/A","section":"A","points":0},{"name":"MALLULA KAVERI","regNo":"N\/A","section":"A","points":0},{"name":"MAMIDISETTI VASUDHA BHANU","regNo":"N\/A","section":"A","points":0},{"name":"MANDANGI MOUNIKA","regNo":"N\/A","section":"A","points":0},{"name":"MANDAVA YAGNA AKHIL SAI","regNo":"N\/A","section":"A","points":0},{"name":"MANGINETI MOHAN SATYA SIVA ROHITH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"MATTA BALA VEERRAJU","regNo":"N\/A","section":"A","points":0},{"name":"MEDIDI LALITH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"MEESALA JAYA RAM","regNo":"N\/A","section":"A","points":0},{"name":"MOHAMMAD SIKINDAR KHAN","regNo":"N\/A","section":"A","points":0},{"name":"MUCHARLA MANI VENKATA SATYANARAYANA","regNo":"N\/A","section":"B","points":0},{"name":"MUCHU MAHADEV","regNo":"N\/A","section":"A","points":0},{"name":"MUGADA DURGA PRASAD","regNo":"N\/A","section":"B","points":0},{"name":"MUPPIDI AMAR DATTA REDDY","regNo":"N\/A","section":"A","points":0},{"name":"MYLABATHULA SUPRIYA","regNo":"N\/A","section":"A","points":0},{"name":"NARKEDAMILLI TANISHA","regNo":"N\/A","section":"A","points":0},{"name":"NIMMALA BHUVANA LAKSHMI","regNo":"N\/A","section":"B","points":0},{"name":"NUKALA NAGA HARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"NULAKANI LEELA MADHAVA RAO","regNo":"N\/A","section":"A","points":0},{"name":"PABBINEEDI SRI RAMA SATYA MAHESH","regNo":"N\/A","section":"A","points":0},{"name":"PABOLU SAI HARSHA","regNo":"N\/A","section":"A","points":0},{"name":"PAILA NIKHIL","regNo":"N\/A","section":"A","points":0},{"name":"PALAPARTHI SANTHOSH KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"PANJA SOMARANGA SAI","regNo":"N\/A","section":"B","points":0},{"name":"PASUPULETI DAIVA PRASAD","regNo":"N\/A","section":"A","points":0},{"name":"PENAPOTHU JOHARIKA","regNo":"N\/A","section":"A","points":0},{"name":"PENMATSA SAI SATHWIKA","regNo":"N\/A","section":"B","points":0},{"name":"PENMETSA PUJITH NAGA SANJAY VARMA","regNo":"N\/A","section":"A","points":0},{"name":"PENMETSA SAI ANVESH VARMA","regNo":"N\/A","section":"A","points":0},{"name":"PERICHARLA HEMA ASWANI","regNo":"N\/A","section":"A","points":0},{"name":"PERURI V V S L VINAY","regNo":"N\/A","section":"A","points":0},{"name":"PIPPALLA MADHURI VENKATA NAGA DIVYA","regNo":"N\/A","section":"A","points":0},{"name":"PIPPALLA RUSHI GUNA SHANMUKH","regNo":"N\/A","section":"A","points":0},{"name":"PODAGATLA PRASANTH","regNo":"N\/A","section":"A","points":0},{"name":"PONNALA VAISHNAVI PRIYADARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"POTHINEEDI TEJA NAGA VENKATA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"POTTURI GAYATRI","regNo":"N\/A","section":"A","points":0},{"name":"PULLURU KRISHNA VAMSI","regNo":"N\/A","section":"A","points":0},{"name":"PUVVALA SANJANA GAYATHRI","regNo":"N\/A","section":"B","points":0},{"name":"RAJ KAMALINI MEENAKSHI BALABHADRA","regNo":"N\/A","section":"A","points":0},{"name":"RAMANA DIVYA JYOTHIKA","regNo":"N\/A","section":"A","points":0},{"name":"RONGALA SRINIVAS","regNo":"N\/A","section":"B","points":0},{"name":"SALUMURI JYOTHI","regNo":"N\/A","section":"A","points":0},{"name":"SAMAYAMANTHULA SRIVYSHNAVI ISWARYA LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"SAMUDRALA JESRAVAN MANIKANTA","regNo":"N\/A","section":"B","points":0},{"name":"SATTINENI NIHITHA","regNo":"N\/A","section":"A","points":0},{"name":"SAVARAM VENKATA SATYA NAGA DURGA SUBHASH","regNo":"N\/A","section":"A","points":0},{"name":"SAYED AMEENA FIRDOUS","regNo":"N\/A","section":"A","points":0},{"name":"SEELABOYINA JEEVANA","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK AHMED","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK SANIYA BEGUM","regNo":"N\/A","section":"A","points":0},{"name":"SINGAMSETTI SAI SHANKAR","regNo":"N\/A","section":"A","points":0},{"name":"SISTU SNEHA","regNo":"N\/A","section":"A","points":0},{"name":"SWAMYREDDY SAI DURGA SAGAR","regNo":"N\/A","section":"A","points":0},{"name":"THIRUMALARAJU VENKATA SATYA PAVAN RAJU","regNo":"N\/A","section":"A","points":0},{"name":"VALAVALA RAMA LAKSHMI ANJANA","regNo":"N\/A","section":"B","points":0},{"name":"VASA HARI NAGENDRA PRATAP","regNo":"N\/A","section":"A","points":0},{"name":"VASIMTHA SATYA SAI KALYANI MALLAPAREDY","regNo":"N\/A","section":"A","points":0},{"name":"VEERAMALLA NAGAVALLI GANGOTHRI","regNo":"N\/A","section":"A","points":0},{"name":"VEERAVALLI SATYA VENKATA SRINADH","regNo":"N\/A","section":"A","points":0},{"name":"VOONNA HEMANTH","regNo":"N\/A","section":"A","points":0},{"name":"YALLA PRADEEP KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"YARLAGADDA TAMOGHNA","regNo":"N\/A","section":"B","points":0},{"name":"YENUGAPALLI DIVYA MADHURI","regNo":"N\/A","section":"A","points":0},{"name":"YIRRI BHANU NAGA PRAKASH","regNo":"N\/A","section":"A","points":0}]
        },
        'AAKASH': {
            name: 'Akash',
            description: 'Sky House - Reaching for the stars with boundless ambition.',
            members: [{"name":"ACHANTA MOKSHITH CHOWDARY","regNo":"N\/A","section":"A","points":0},{"name":"ADABALA GANGA PRAVEEN KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"ADDAGARLA LAKSHMI DEVI","regNo":"N\/A","section":"A","points":0},{"name":"ADINA VENKATA SURYA SAI VISHAL","regNo":"N\/A","section":"A","points":0},{"name":"ALLURI BHUVAN SAI TEJA MANI VARMA","regNo":"N\/A","section":"A","points":0},{"name":"ANDE NAGA SATYA SAI VAMSI KIRAN","regNo":"N\/A","section":"A","points":0},{"name":"ASILETI JAHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"BANAVATHU MALLIKARJUNA SAI","regNo":"N\/A","section":"A","points":0},{"name":"BHAVANAM LAKSHMAN KUMAR REDDY","regNo":"N\/A","section":"A","points":0},{"name":"BILLA SAHITHI","regNo":"N\/A","section":"A","points":0},{"name":"BOGA NISHANTH","regNo":"N\/A","section":"A","points":0},{"name":"BOPPINEEDI GEETHIKA","regNo":"N\/A","section":"A","points":0},{"name":"BUDIDA NAGA VAISHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"CHIKILE RAJESH","regNo":"N\/A","section":"A","points":0},{"name":"CHILAKALAPUDI ABHI RAAMA PHANINDRA","regNo":"N\/A","section":"A","points":0},{"name":"CHINNAM LAKSHMI SANTHOSHI","regNo":"N\/A","section":"A","points":0},{"name":"CHODAGAM SHANMUKHA SIVA SRI VENKAT","regNo":"N\/A","section":"A","points":0},{"name":"DATTI VENKATA RAMANA","regNo":"N\/A","section":"A","points":0},{"name":"DEVADA SRI VENKATESWARA SWAMY","regNo":"N\/A","section":"A","points":0},{"name":"DIRISIMILLI MAHI AVINASH","regNo":"N\/A","section":"A","points":0},{"name":"DODDIPATLA POOJA SAI PRAVEENA","regNo":"N\/A","section":"A","points":0},{"name":"DONGA MADHURI","regNo":"N\/A","section":"A","points":0},{"name":"DURVASULA SITA SRI VYSHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"DUVVADA VINAY","regNo":"N\/A","section":"A","points":0},{"name":"GADDAM MANOJ KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"GANDHAM MAHATHI","regNo":"N\/A","section":"A","points":0},{"name":"GANDROJU ESWAR SRI KALI KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"GOPATHI KALYANI","regNo":"N\/A","section":"A","points":0},{"name":"GUNTAMUKKALA SHAILESH","regNo":"N\/A","section":"A","points":0},{"name":"GURRAM VIKAS","regNo":"N\/A","section":"A","points":0},{"name":"GUTTULA CHAITANYA AKSHAY","regNo":"N\/A","section":"A","points":0},{"name":"INDIGIMELLI RESHMA SUDEEPA","regNo":"N\/A","section":"A","points":0},{"name":"INDUKURI YASWANTH ACHYUTA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"JAKKAMPUDI JAHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"JALDHI PRINCESS GLORY JASMINE","regNo":"N\/A","section":"A","points":0},{"name":"JILLELA VINAY","regNo":"N\/A","section":"A","points":0},{"name":"JITHENDRA VENKATA KANAKA SRI SURYA AYITHAM","regNo":"N\/A","section":"B","points":0},{"name":"KAGITHA BHANU DURGA PRASAD","regNo":"N\/A","section":"A","points":0},{"name":"KALIDINDI SAI VARMA","regNo":"N\/A","section":"B","points":0},{"name":"KALLA GUNADEEP","regNo":"N\/A","section":"A","points":0},{"name":"KAMBHAMPATI SHALANI SINDHU SRI","regNo":"N\/A","section":"A","points":0},{"name":"KANUBOINA VIJAYA LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"KANUMURI SUDHA","regNo":"N\/A","section":"A","points":0},{"name":"KARRI LAKSHMI SRAVANTHI","regNo":"N\/A","section":"A","points":0},{"name":"KARUMANCHI SUNEEL","regNo":"N\/A","section":"A","points":0},{"name":"KARUMURI TEJA SIDDARDHA PAVAN KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KATARI HASWANTH SIVA BHASKAR","regNo":"N\/A","section":"B","points":0},{"name":"KATRAGADDA ARJUN NAIDU","regNo":"N\/A","section":"A","points":0},{"name":"KATREDDI BHANU TEJA SRI","regNo":"N\/A","section":"A","points":0},{"name":"KETHA BHAVYASRI SAILAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"KHANDAVALLI VYSHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"KODI HEMANTH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KOLLA RAMA SAI","regNo":"N\/A","section":"B","points":0},{"name":"KOLLABATHULA SHYAM BABU","regNo":"N\/A","section":"A","points":0},{"name":"KOLLEPARA PREM","regNo":"N\/A","section":"A","points":0},{"name":"KOLLI VINEEL","regNo":"N\/A","section":"A","points":0},{"name":"KONKEY BINDHU VASANTHI","regNo":"N\/A","section":"A","points":0},{"name":"KOPPARTHI DURGA BHAVANI","regNo":"N\/A","section":"A","points":0},{"name":"KOREDLA MEDHO SAI ASESH","regNo":"N\/A","section":"A","points":0},{"name":"KOTTA S N VASAVI SRIVALLI","regNo":"N\/A","section":"A","points":0},{"name":"KUCHIMANCHI PRANAV","regNo":"N\/A","section":"A","points":0},{"name":"KUSAMPUDI VENKATA SATYA SAI TEJAS VARMA","regNo":"N\/A","section":"A","points":0},{"name":"MADDALA MANI NAGA SAI NARASIMHA TRINADH","regNo":"N\/A","section":"B","points":0},{"name":"MADDALA VARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"MADDULA AAKASH NAGENDRA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"MADUPALLI JNANESH","regNo":"N\/A","section":"A","points":0},{"name":"MAKKA SAI GOWR","regNo":"N\/A","section":"A","points":0},{"name":"MALLULA MADHU VARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"MANCHALA SHANMUKA LAKSHMI DEEPIKA","regNo":"N\/A","section":"A","points":0},{"name":"MANDA TANMAY VENKATA SAI LALA GUPTA","regNo":"N\/A","section":"A","points":0},{"name":"MANDELA MUKUNDA PADMA PRIYA","regNo":"N\/A","section":"A","points":0},{"name":"MANGENA SAI VENKATA VENU GOPALA CHARAN","regNo":"N\/A","section":"A","points":0},{"name":"MEDISETTI SRINIJA","regNo":"N\/A","section":"B","points":0},{"name":"MOHAMMAD NUMAAN RAZA","regNo":"N\/A","section":"B","points":0},{"name":"MULAGALA PRANATI SANDHYA","regNo":"N\/A","section":"B","points":0},{"name":"MUTHABATHULA PUNEETH","regNo":"N\/A","section":"A","points":0},{"name":"NADIKUPPALA THANUSH","regNo":"N\/A","section":"A","points":0},{"name":"NADIMPALLI BABAJI AMRUTHA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"NALLAM MANOGNYA DEVI","regNo":"N\/A","section":"A","points":0},{"name":"NAMALA THANUSHA","regNo":"N\/A","section":"B","points":0},{"name":"NANDRU VINAY BABU","regNo":"N\/A","section":"A","points":0},{"name":"NODAGALA NANDA GOPAL SWAMY","regNo":"N\/A","section":"B","points":0},{"name":"NULI LAKSHMI SAI LIKITH","regNo":"N\/A","section":"B","points":0},{"name":"PAVULURI SAI KRISHNA","regNo":"N\/A","section":"B","points":0},{"name":"PENUGONDA ENMANUYEL","regNo":"N\/A","section":"B","points":0},{"name":"PERABATHULA SOMESWARA RAO","regNo":"N\/A","section":"A","points":0},{"name":"POLIMERA SWAPNA","regNo":"N\/A","section":"A","points":0},{"name":"POTHURI SIVA SAI KRISHNA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"PULI MYTHILI","regNo":"N\/A","section":"B","points":0},{"name":"PULIDINDI BLOOMY CHRIS ANGEL","regNo":"N\/A","section":"A","points":0},{"name":"PUTHINIDI JNANESWARI","regNo":"N\/A","section":"A","points":0},{"name":"PUVVALA DEVI AISHWARYA","regNo":"N\/A","section":"A","points":0},{"name":"RAJA AKASH","regNo":"N\/A","section":"B","points":0},{"name":"RAMISETTY SANHITHA SRI","regNo":"N\/A","section":"A","points":0},{"name":"RANGISETTI HEMA SAHASRA","regNo":"N\/A","section":"B","points":0},{"name":"REDDEM LEELA MEGHANA","regNo":"N\/A","section":"B","points":0},{"name":"REDDY VENKATA SAKETH","regNo":"N\/A","section":"A","points":0},{"name":"RELLU LAKSHMI PRASANNA","regNo":"N\/A","section":"A","points":0},{"name":"SEELABOINA RAMADEVI","regNo":"N\/A","section":"A","points":0},{"name":"SEELABOINA SANTOSH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"SEELABOYINA JEEVIKA","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK AFZAL DANISH","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK ILIYAS","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK SAMEERA","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK SUHANA","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK THAHIR BASHA","regNo":"N\/A","section":"A","points":0},{"name":"SUNKARA CHAITANYA VEERA BHAIRAV","regNo":"N\/A","section":"A","points":0},{"name":"TANUKULA UMA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"TAPPETA GANESH REDDY","regNo":"N\/A","section":"A","points":0},{"name":"TEKU DURGA SRINIVAS","regNo":"N\/A","section":"A","points":0},{"name":"THOTA DEVI SRI SAI SREEKAR","regNo":"N\/A","section":"A","points":0},{"name":"THOTA MOHAN SIVA","regNo":"N\/A","section":"A","points":0},{"name":"THOTA SUJAY BABU","regNo":"N\/A","section":"A","points":0},{"name":"UNDURTHI MANOJ","regNo":"N\/A","section":"A","points":0},{"name":"UNGARALA RADHIKA AISHWARYA","regNo":"N\/A","section":"B","points":0},{"name":"UPPALA ABHINAYA SREE","regNo":"N\/A","section":"B","points":0},{"name":"VARADA NAGA SURYA LAKSHMI","regNo":"N\/A","section":"A","points":0},{"name":"VARRE GEETHA NAGA VALLI","regNo":"N\/A","section":"B","points":0},{"name":"VATTIVELLA RAMKI","regNo":"N\/A","section":"B","points":0},{"name":"VILLURI MOHINI MANGA LAKSHMI MANASA","regNo":"N\/A","section":"A","points":0},{"name":"VISSAPRAGADA RAMA PRANEETH","regNo":"N\/A","section":"A","points":0},{"name":"YENDA RASHMIKA","regNo":"N\/A","section":"B","points":0},{"name":"YERICHERLA JOHN ELISHA","regNo":"N\/A","section":"B","points":0},{"name":"YERRA YASVASI SATYA KAVERI","regNo":"N\/A","section":"B","points":0}]
        },
        'PRUDHVI': {
            name: 'Prudhvi',
            description: 'Earth House - Strong and steady like the mountains that stand the test of time.',
            members: [{"name":"ADABALA SAI NAGA SURYANARAYANA","regNo":"N\/A","section":"B","points":0},{"name":"AKULA BALA BHAGYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"BANDARU MANOGNA NAGAVALLI","regNo":"N\/A","section":"A","points":0},{"name":"BANDI HARI KRISHNA","regNo":"N\/A","section":"A","points":0},{"name":"BARAKATA TARUN SWAMY","regNo":"N\/A","section":"A","points":0},{"name":"BASIVIREDDY HEMALATHA","regNo":"N\/A","section":"A","points":0},{"name":"BAYYE JOSEPH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"BILLAKURTHI HARSHA VARDHAN SRINIVASU","regNo":"N\/A","section":"B","points":0},{"name":"BIRUDUKOTA SATYA VARA PRASAD","regNo":"N\/A","section":"B","points":0},{"name":"BOLEM PRAVALIKA","regNo":"N\/A","section":"B","points":0},{"name":"BOMMIDI JAHNAVI","regNo":"N\/A","section":"A","points":0},{"name":"BOYAPATI PRASANNA VARUN","regNo":"N\/A","section":"B","points":0},{"name":"BUDDE VENKATA SATYA TEJESH","regNo":"N\/A","section":"A","points":0},{"name":"CHALLA JITHENDRA ABHIRAM","regNo":"N\/A","section":"A","points":0},{"name":"CHALLAGUNDLA HINDRIKA SRI","regNo":"N\/A","section":"A","points":0},{"name":"CHANDAKA KEDARA SRINIVAS","regNo":"N\/A","section":"A","points":0},{"name":"CHATRAGADDA TEJASWINI","regNo":"N\/A","section":"B","points":0},{"name":"CHEEPU SAI VIKAS","regNo":"N\/A","section":"A","points":0},{"name":"CHEGONDI HARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"CHEYYETI VENKATA SINDHU","regNo":"N\/A","section":"B","points":0},{"name":"CHINTAPALLI VENKATA DURGESH","regNo":"N\/A","section":"A","points":0},{"name":"CHOKKA ARYAN SANTHOSH","regNo":"N\/A","section":"A","points":0},{"name":"CHUNDRU VISWA TEJA","regNo":"N\/A","section":"A","points":0},{"name":"DASARI YUVA RAM","regNo":"N\/A","section":"B","points":0},{"name":"DIRSIPOM INDHU PRIYA","regNo":"N\/A","section":"B","points":0},{"name":"DONGA CHANDINI","regNo":"N\/A","section":"A","points":0},{"name":"DONGA MAHESH","regNo":"N\/A","section":"B","points":0},{"name":"DWARAMPUDI PURNA NAGA GOWTHAM REDDY","regNo":"N\/A","section":"B","points":0},{"name":"EDIMUDI SURIBABU","regNo":"N\/A","section":"A","points":0},{"name":"ESURU CHAITANYA","regNo":"N\/A","section":"A","points":0},{"name":"G UDAY KIRAN","regNo":"N\/A","section":"A","points":0},{"name":"GADDAMUDI VENKATA GOPICHAND","regNo":"N\/A","section":"A","points":0},{"name":"GANJI JYOTHSNA","regNo":"N\/A","section":"B","points":0},{"name":"GANTA GOWTHAM","regNo":"N\/A","section":"A","points":0},{"name":"GAYAKAWADA PALLAVI","regNo":"N\/A","section":"A","points":0},{"name":"GEDELA SAI ABHINAY","regNo":"N\/A","section":"A","points":0},{"name":"GIRIJALA PRASHANTH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"GUBBALA RESHMA GANGAVATHI","regNo":"N\/A","section":"A","points":0},{"name":"GUDDATI DURGA NAGA LAKSHMI SHIVA SARANYA","regNo":"N\/A","section":"A","points":0},{"name":"GUDDETI DATHRI SRI SAI ANVITHA","regNo":"N\/A","section":"A","points":0},{"name":"GUDIMETLA JNANA SANDEEP REDDY","regNo":"N\/A","section":"A","points":0},{"name":"GUDURI KARTHIK SRI NAGA SAI","regNo":"N\/A","section":"A","points":0},{"name":"GUMMALLA NAGA GAYATHRI","regNo":"N\/A","section":"A","points":0},{"name":"ITTA VASAVI","regNo":"N\/A","section":"A","points":0},{"name":"JADDU JYOTHIRMAI INDIRA PRIYADARSINI DEVI","regNo":"N\/A","section":"A","points":0},{"name":"JALDANI ABHIRAM CHARAN","regNo":"N\/A","section":"A","points":0},{"name":"JAVVADI MOHANA DURGA","regNo":"N\/A","section":"A","points":0},{"name":"JOGI PAVAN TEJA","regNo":"N\/A","section":"A","points":0},{"name":"JONNALAGADDA LAKSHMI MOUNIKA","regNo":"N\/A","section":"A","points":0},{"name":"KADALI BHANU","regNo":"N\/A","section":"A","points":0},{"name":"KANCHARLA N V L DURGA NIHARIKA","regNo":"N\/A","section":"A","points":0},{"name":"KANDANALA PURNASRI","regNo":"N\/A","section":"A","points":0},{"name":"KANUMURI RISHITHA VARMA","regNo":"N\/A","section":"A","points":0},{"name":"KAPAKAYALA NAGA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"KARATAM SANTHOSH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KARIBANDI PAVAN RAVINDRA KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"KAYITHA LAHARI","regNo":"N\/A","section":"A","points":0},{"name":"KESANAKURTHI MANASA SATYA","regNo":"N\/A","section":"A","points":0},{"name":"KETA PURNA PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"KOLLATI SAGAR","regNo":"N\/A","section":"A","points":0},{"name":"KOLLATI VISHNU TEJA","regNo":"N\/A","section":"A","points":0},{"name":"KOMMULA DIVYA MANOGNA","regNo":"N\/A","section":"A","points":0},{"name":"KORANGI TRINADH","regNo":"N\/A","section":"A","points":0},{"name":"KOTA DEEPIKA","regNo":"N\/A","section":"A","points":0},{"name":"KOTLA VENKAT","regNo":"N\/A","section":"A","points":0},{"name":"KUMMARAPURUGU SAIRAM","regNo":"N\/A","section":"A","points":0},{"name":"KUSUMA KOMALI","regNo":"N\/A","section":"A","points":0},{"name":"KUTIKUPPALA CHARAN TEJA","regNo":"N\/A","section":"A","points":0},{"name":"LAKKU NOMU NARASIMHA SAI PAVAN","regNo":"N\/A","section":"A","points":0},{"name":"LAKSHMI VENKATA NIKHITHA","regNo":"N\/A","section":"A","points":0},{"name":"LOKAM MAHITANJALI","regNo":"N\/A","section":"A","points":0},{"name":"MADABHUSHI SRI RANGA SUDARSAN ","regNo":"N\/A","section":"A","points":0},{"name":"MADAMANCHI MANIKANTA","regNo":"N\/A","section":"A","points":0},{"name":"MALLA DEEPANVITHA","regNo":"N\/A","section":"A","points":0},{"name":"MAMUDURI PRABHAS","regNo":"N\/A","section":"B","points":0},{"name":"MANAPARAPU DEEPIKA","regNo":"N\/A","section":"A","points":0},{"name":"MANDA KEERTHI","regNo":"N\/A","section":"A","points":0},{"name":"MANDAGIRI SAI ASWITHA","regNo":"N\/A","section":"A","points":0},{"name":"MANDAVALLI DHANA KARTHIKEYA","regNo":"N\/A","section":"A","points":0},{"name":"MARUBOINA KARTHIK VENKATA SRI SAI TEJA","regNo":"N\/A","section":"A","points":0},{"name":"MEDISETTI RAMA KRISHNA SAI","regNo":"N\/A","section":"A","points":0},{"name":"MEER IKRAAM HUSSAIN","regNo":"N\/A","section":"B","points":0},{"name":"MEESALA KARTHIK RAJ KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"MEESALA RAJANIKUMAR","regNo":"N\/A","section":"A","points":0},{"name":"MOHAMMAD IBRAHIM KHAN","regNo":"N\/A","section":"A","points":0},{"name":"MOHAMMAD ROOFIYA TASNEEM","regNo":"N\/A","section":"A","points":0},{"name":"MORTHA ANUSRI","regNo":"N\/A","section":"A","points":0},{"name":"MUDUNURI MANOJ SAI ASWANTH VARMA","regNo":"N\/A","section":"A","points":0},{"name":"MUNGARA LOKESH KUMAR","regNo":"N\/A","section":"A","points":0},{"name":"MUTHYALAPALLI","regNo":"N\/A","section":"B","points":0},{"name":"NAKKINA GANESH","regNo":"N\/A","section":"A","points":0},{"name":"NALAMALA KEVIN RISHITH","regNo":"N\/A","section":"B","points":0},{"name":"NALLA TANOJ SITHARAM","regNo":"N\/A","section":"A","points":0},{"name":"NAMUDURI MAHESH","regNo":"N\/A","section":"A","points":0},{"name":"NANDURI SURYA NAGA VENKATA SAI VIGNESH","regNo":"N\/A","section":"A","points":0},{"name":"NEPALA BESWANTH","regNo":"N\/A","section":"B","points":0},{"name":"NETHALA HEMA DURGA SAI KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"NIMMANA NARENDRA","regNo":"N\/A","section":"B","points":0},{"name":"PADAVALA GANIF RAJU","regNo":"N\/A","section":"A","points":0},{"name":"PAIDI TANUJA","regNo":"N\/A","section":"A","points":0},{"name":"PAKA RENITA JESSIE","regNo":"N\/A","section":"B","points":0},{"name":"PALANI BHUVANA SAI KRUTHI","regNo":"N\/A","section":"B","points":0},{"name":"PALIVELA BALA BHASKARA PRADEEP","regNo":"N\/A","section":"A","points":0},{"name":"PALLAPU HARITHA","regNo":"N\/A","section":"B","points":0},{"name":"PANDA SUJAN PRASAD","regNo":"N\/A","section":"B","points":0},{"name":"PANJA MUKUNDA SRI NAGA SANTOSH","regNo":"N\/A","section":"A","points":0},{"name":"PANJA NAGA VENKATA PRASAD RAJA","regNo":"N\/A","section":"A","points":0},{"name":"PARAVASTU VENKATA RAMA SURI","regNo":"N\/A","section":"B","points":0},{"name":"PAREPALLI RAMA HARI NAIDU","regNo":"N\/A","section":"A","points":0},{"name":"PATAN ABDUL RASHEED KHAN","regNo":"N\/A","section":"B","points":0},{"name":"PECHETTI SRI VINAYAK","regNo":"N\/A","section":"A","points":0},{"name":"PEETHANI UDAYA SRI","regNo":"N\/A","section":"A","points":0},{"name":"PERICHERLA VIGNESH VARMA","regNo":"N\/A","section":"A","points":0},{"name":"PILLI MEGHANA","regNo":"N\/A","section":"A","points":0},{"name":"POTLA RAVI","regNo":"N\/A","section":"B","points":0},{"name":"PUPPALA JANARDHAN SAI","regNo":"N\/A","section":"B","points":0},{"name":"RAAVI CHARWAK","regNo":"N\/A","section":"A","points":0},{"name":"RANGISETTI SAI PAVAN KUMAR","regNo":"N\/A","section":"B","points":0},{"name":"REBBA RAJESH","regNo":"N\/A","section":"B","points":0},{"name":"REDDY SRIJA","regNo":"N\/A","section":"B","points":0},{"name":"REDDY VENKATA SATYA SRAVANI","regNo":"N\/A","section":"B","points":0},{"name":"REKHAPALLI RUTHIKA AKSHAYA SAI SRI","regNo":"N\/A","section":"A","points":0},{"name":"RODDA VENKATA SIVA SAI","regNo":"N\/A","section":"A","points":0},{"name":"ROMPILLI SATEESH","regNo":"N\/A","section":"B","points":0},{"name":"RUDRAKSHULA PRAVEENA","regNo":"N\/A","section":"A","points":0},{"name":"SANDHI SHAMM ROY","regNo":"N\/A","section":"A","points":0},{"name":"SANKU VEERA VENKATA SANTOSH","regNo":"N\/A","section":"A","points":0},{"name":"SARIPALLI GNANESWAR","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK ABDUL GAFOOR","regNo":"N\/A","section":"B","points":0},{"name":"SHAIK KARIMUNNISA","regNo":"N\/A","section":"A","points":0},{"name":"Shaik madeena","regNo":"N\/A","section":"A","points":0},{"name":"SHAIK REENAZ","regNo":"N\/A","section":"A","points":0},{"name":"SIDAGAM ABHIRAM","regNo":"N\/A","section":"B","points":0},{"name":"SIRRA DURGA RANI","regNo":"N\/A","section":"B","points":0},{"name":"SUNKARA LOKESH VIJAY SAI","regNo":"N\/A","section":"B","points":0},{"name":"SURARAPU HASINI","regNo":"N\/A","section":"A","points":0},{"name":"SWARNA SAHITHI","regNo":"N\/A","section":"B","points":0},{"name":"SYED MANSOOR","regNo":"N\/A","section":"A","points":0},{"name":"TALARI JYOTHI","regNo":"N\/A","section":"B","points":0},{"name":"TAMARANA SRUTHI","regNo":"N\/A","section":"A","points":0},{"name":"TELLAKULA VEERA RAGHAVA","regNo":"N\/A","section":"A","points":0},{"name":"TELU YUVA PRIYA MOULIKA","regNo":"N\/A","section":"A","points":0},{"name":"TIRUMALASETTY SIDDARDHA","regNo":"N\/A","section":"B","points":0},{"name":"VASE ASHITHA","regNo":"N\/A","section":"B","points":0},{"name":"VATAPALLI GNANA SEKHAR","regNo":"N\/A","section":"A","points":0},{"name":"VATHADI NAGAVINAY","regNo":"N\/A","section":"B","points":0},{"name":"VEERAVALLI LEELA NAGA BABU","regNo":"N\/A","section":"A","points":0},{"name":"VEERAVARAPU NAGA VENKATA JASWANTH","regNo":"N\/A","section":"A","points":0},{"name":"VEERLAPATI HARSHINI","regNo":"N\/A","section":"A","points":0},{"name":"VENNAPUSA MANISHA","regNo":"N\/A","section":"B","points":0},{"name":"VUNNAM RAVINDRA BABU","regNo":"N\/A","section":"A","points":0},{"name":"YARAMALA MOHAN BHAGAVAN NARASIMHA","regNo":"N\/A","section":"A","points":0}]
        }
    };

    /**
     * =========================================================================
     * 3. MASTER CLASS REPRESENTATIVES (CR) INDEX (14 VERIFIED CRs FROM WEBSITE)
     * Data Source: heroes_of_department.php (#class-representatives)
     * =========================================================================
     */
    const MASTER_CR_INDEX = [
        { id: 'cr-5', fullName: 'JAVVADI MOHANA DURGA', firstName: 'mohana durga', lastName: 'javvadi', regNo: '25B91A6223', className: 'CSD - II Year', branch: 'CSD', year: 'II Year', yearNum: 2, section: '' },
        { id: 'cr-6', fullName: 'VASA HARI NAGENDRA PRATAP', firstName: 'hari nagendra pratap', lastName: 'vasa', regNo: '25B91A6263', className: 'CSD - II Year', branch: 'CSD', year: 'II Year', yearNum: 2, section: '' },
        { id: 'cr-3', fullName: 'P HARSHA', firstName: 'harsha', lastName: 'p', regNo: '25B91A0786', className: 'CSIT - II Year - Section A', branch: 'CSIT', year: 'II Year', yearNum: 2, section: 'Section A' },
        { id: 'cr-4', fullName: 'B J S V D N ASRITHA', firstName: 'asritha', lastName: 'b', regNo: '25B91A0711', className: 'CSIT - II Year - Section A', branch: 'CSIT', year: 'II Year', yearNum: 2, section: 'Section A' },
        { id: 'cr-1', fullName: 'PAMU AMRUTHA', firstName: 'amrutha', lastName: 'pamu', regNo: '25B91A0782', className: 'CSIT - II Year - Section B', branch: 'CSIT', year: 'II Year', yearNum: 2, section: 'Section B' },
        { id: 'cr-2', fullName: 'B PRASANNA VARUN', firstName: 'prasanna varun', lastName: 'b', regNo: '25B91A0717', className: 'CSIT - II Year - Section B', branch: 'CSIT', year: 'II Year', yearNum: 2, section: 'Section B' },
        { id: 'cr-7', fullName: 'CHANDANI VIVEKANANDA', firstName: 'vivekananda', lastName: 'chandani', regNo: '24B91A0720', className: 'CSIT - III Year - Section A', branch: 'CSIT', year: 'III Year', yearNum: 3, section: 'Section A' },
        { id: 'cr-8', fullName: 'THOTA JOHAN BENEDICT', firstName: 'johan benedict', lastName: 'thota', regNo: '24B91A07B7', className: 'CSIT - III Year - Section B', branch: 'CSIT', year: 'III Year', yearNum: 3, section: 'Section B' },
        { id: 'cr-9', fullName: 'S D RANI', firstName: 'rani', lastName: 's d', regNo: '24B91A07B3', className: 'CSIT - III Year - Section B', branch: 'CSIT', year: 'III Year', yearNum: 3, section: 'Section B' },
        { id: 'cr-14', fullName: 'PULAVARTHI MOHANA MADHU LASYA SRI', firstName: 'mohana madhu lasya sri', lastName: 'pulavarthi', regNo: '25B95A6208', className: 'CSD - III Year', branch: 'CSD', year: 'III Year', yearNum: 3, section: '' },
        { id: 'cr-10', fullName: 'P SAI HARSHA', firstName: 'sai harsha', lastName: 'p', regNo: '23B81A6252', className: 'CSD - IV Year', branch: 'CSD', year: 'IV Year', yearNum: 4, section: '' },
        { id: 'cr-11', fullName: 'P SWAPNA', firstName: 'swapna', lastName: 'p', regNo: '23B91A6255', className: 'CSD - IV Year', branch: 'CSD', year: 'IV Year', yearNum: 4, section: '' },
        { id: 'cr-12', fullName: 'R DIVYA JYOTHIKA', firstName: 'divya jyothika', lastName: 'r', regNo: '23B91A0747', className: 'CSIT - IV Year', branch: 'CSIT', year: 'IV Year', yearNum: 4, section: '' },
        { id: 'cr-13', fullName: 'CH SAI VIKAS', firstName: 'sai vikas', lastName: 'ch', regNo: '23B91A0706', className: 'CSIT - IV Year', branch: 'CSIT', year: 'IV Year', yearNum: 4, section: '' }
    ];

    /**
     * =========================================================================
     * 4. MASTER PERSON INDEX (FACULTY + HEROES + CLASS REPRESENTATIVES)
     * =========================================================================
     */
    const MASTER_PERSON_INDEX = [
        {
            id: 'person_mohana_durga',
            fullName: 'JAVVADI MOHANA DURGA',
            firstName: 'mohana durga',
            lastName: 'javvadi',
            category: 'Class Representative',
            designation: 'Class Representative (CSD II Year)',
            department: 'CSD',
            regNo: '25B91A6223',
            isCR: true,
            searchableAliases: ['javvadi mohana durga', 'mohana durga', 'javvadi', 'durga'],
            content: `Class Representative — JAVVADI MOHANA DURGA:
• Role: Class Representative
• Year: 2nd Year
• Section: CSD – II Year
• Registration Number: 25B91A6223`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_hari_pratap',
            fullName: 'VASA HARI NAGENDRA PRATAP',
            firstName: 'hari nagendra pratap',
            lastName: 'vasa',
            category: 'Class Representative',
            designation: 'Class Representative (CSD II Year)',
            department: 'CSD',
            regNo: '25B91A6263',
            isCR: true,
            searchableAliases: ['vasa hari nagendra pratap', 'hari nagendra pratap', 'vasa', 'pratap'],
            content: `Class Representative — VASA HARI NAGENDRA PRATAP:
• Role: Class Representative
• Year: 2nd Year
• Section: CSD – II Year
• Registration Number: 25B91A6263`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_p_harsha',
            fullName: 'P HARSHA',
            firstName: 'harsha',
            lastName: 'p',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT II Year Sec A)',
            department: 'CSIT',
            regNo: '25B91A0786',
            isCR: true,
            searchableAliases: ['p harsha', 'harsha', 'p. harsha'],
            content: `Class Representative — P HARSHA:
• Role: Class Representative
• Year: 2nd Year
• Section: CSIT - II Year - Section A
• Registration Number: 25B91A0786`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_asritha',
            fullName: 'B J S V D N ASRITHA',
            firstName: 'asritha',
            lastName: 'b',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT II Year Sec A)',
            department: 'CSIT',
            regNo: '25B91A0711',
            isCR: true,
            searchableAliases: ['asritha', 'b j s v d n asritha'],
            content: `Class Representative — B J S V D N ASRITHA:
• Role: Class Representative
• Year: 2nd Year
• Section: CSIT - II Year - Section A
• Registration Number: 25B91A0711`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_amrutha',
            fullName: 'PAMU AMRUTHA',
            firstName: 'amrutha',
            lastName: 'pamu',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT II Year Sec B)',
            department: 'CSIT',
            regNo: '25B91A0782',
            isCR: true,
            searchableAliases: ['pamu amrutha', 'amrutha', 'pamu'],
            content: `Class Representative — PAMU AMRUTHA:
• Role: Class Representative
• Year: 2nd Year
• Section: CSIT - II Year - Section B
• Registration Number: 25B91A0782`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_prasanna_varun',
            fullName: 'B PRASANNA VARUN',
            firstName: 'prasanna varun',
            lastName: 'b',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT II Year Sec B)',
            department: 'CSIT',
            regNo: '25B91A0717',
            isCR: true,
            searchableAliases: ['b prasanna varun', 'prasanna varun', 'varun'],
            content: `Class Representative — B PRASANNA VARUN:
• Role: Class Representative
• Year: 2nd Year
• Section: CSIT - II Year - Section B
• Registration Number: 25B91A0717`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_vivekananda',
            fullName: 'CHANDANI VIVEKANANDA',
            firstName: 'vivekananda',
            lastName: 'chandani',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT III Year Sec A)',
            department: 'CSIT',
            regNo: '24B91A0720',
            isCR: true,
            searchableAliases: ['chandani vivekananda', 'vivekananda', 'chandani'],
            content: `Class Representative — CHANDANI VIVEKANANDA:
• Role: Class Representative
• Year: 3rd Year
• Section: CSIT - III Year - Section A
• Registration Number: 24B91A0720`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_johan_benedict',
            fullName: 'THOTA JOHAN BENEDICT',
            firstName: 'johan benedict',
            lastName: 'thota',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT III Year Sec B)',
            department: 'CSIT',
            regNo: '24B91A07B7',
            isCR: true,
            searchableAliases: ['thota johan benedict', 'johan benedict', 'thota'],
            content: `Class Representative — THOTA JOHAN BENEDICT:
• Role: Class Representative
• Year: 3rd Year
• Section: CSIT - III Year - Section B
• Registration Number: 24B91A07B7`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_s_d_rani',
            fullName: 'S D RANI',
            firstName: 'rani',
            lastName: 's d',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT III Year Sec B)',
            department: 'CSIT',
            regNo: '24B91A07B3',
            isCR: true,
            searchableAliases: ['s d rani', 'rani'],
            content: `Class Representative — S D RANI:
• Role: Class Representative
• Year: 3rd Year
• Section: CSIT - III Year - Section B
• Registration Number: 24B91A07B3`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_lasya_sri',
            fullName: 'PULAVARTHI MOHANA MADHU LASYA SRI',
            firstName: 'lasya sri',
            lastName: 'pulavarthi',
            category: 'Class Representative',
            designation: 'Class Representative (CSD III Year)',
            department: 'CSD',
            regNo: '25B95A6208',
            isCR: true,
            searchableAliases: ['pulavarthi mohana madhu lasya sri', 'lasya sri', 'pulavarthi'],
            content: `Class Representative — PULAVARTHI MOHANA MADHU LASYA SRI:
• Role: Class Representative
• Year: 3rd Year
• Section: CSD – III Year
• Registration Number: 25B95A6208`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_sai_harsha',
            fullName: 'P SAI HARSHA',
            firstName: 'sai harsha',
            lastName: 'p',
            category: 'Class Representative',
            designation: 'Class Representative (CSD IV Year)',
            department: 'CSD',
            regNo: '23B81A6252',
            isCR: true,
            searchableAliases: ['p sai harsha', 'sai harsha'],
            content: `Class Representative — P SAI HARSHA:
• Role: Class Representative
• Year: 4th Year
• Section: CSD – IV Year
• Registration Number: 23B81A6252`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_p_swapna',
            fullName: 'P SWAPNA',
            firstName: 'swapna',
            lastName: 'p',
            category: 'Class Representative',
            designation: 'Class Representative (CSD IV Year)',
            department: 'CSD',
            regNo: '23B91A6255',
            isCR: true,
            searchableAliases: ['p swapna', 'swapna'],
            content: `Class Representative — P SWAPNA:
• Role: Class Representative
• Year: 4th Year
• Section: CSD – IV Year
• Registration Number: 23B91A6255`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_divya_jyothika',
            fullName: 'R DIVYA JYOTHIKA',
            firstName: 'divya jyothika',
            lastName: 'r',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            regNo: '23B91A0747',
            isCR: true,
            searchableAliases: ['r divya jyothika', 'divya jyothika'],
            content: `Class Representative — R DIVYA JYOTHIKA:
• Role: Class Representative
• Year: 4th Year
• Section: CSIT – IV Year
• Registration Number: 23B91A0747`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },
        {
            id: 'person_sai_vikas',
            fullName: 'CH SAI VIKAS',
            firstName: 'sai vikas',
            lastName: 'ch',
            category: 'Class Representative',
            designation: 'Class Representative (CSIT IV Year)',
            department: 'CSIT',
            regNo: '23B91A0706',
            isCR: true,
            searchableAliases: ['ch sai vikas', 'sai vikas'],
            content: `Class Representative — CH SAI VIKAS:
• Role: Class Representative
• Year: 4th Year
• Section: CSIT – IV Year
• Registration Number: 23B91A0706`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View Class Representatives →'
        },

        // --- DEPARTMENT HEROES & STUDENT ACHIEVERS ---
        {
            id: 'person_preeti_avvula',
            fullName: 'Preeti Avvula',
            firstName: 'preeti',
            lastName: 'avvula',
            category: 'Department Hero & Student Leader',
            designation: 'TEDx SRKR Core Organizer & Master Anchor',
            department: 'CSD',
            regNo: '24B91A0701',
            isCR: false,
            searchableAliases: ['preeti', 'preeti avvula', 'p avvula', 'avvula preeti', 'avvula'],
            content: `Department Hero & Student Leader — Preeti Avvula:
• Role: TEDx SRKR Core Organizer & Master Anchor
• Reg No: 24B91A0701 (CSD Department)
• Category: Department Hero & Student Leader
• Profile & Achievements: Preeti Avvula is a dynamic student leader and master anchor who served as a core organizer for TEDx SRKR. Known for her powerful stage presence, eloquence, and exceptional event coordination, she led the independently organized TED event with distinction. Her leadership, public speaking, and conference hosting inspire students across the department as a true Department Hero.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_mullu_srinu',
            fullName: 'Mullu Srinu',
            firstName: 'srinu',
            lastName: 'mullu',
            category: 'Department Hero & Student Achiever',
            designation: 'NSS Coordinator & Ecom Hackathon MVP',
            department: 'CSIT',
            regNo: '25B95A6206',
            isCR: false,
            searchableAliases: ['mullu srinu', 'mullu', 'mullu srinu student', 'srinu student'],
            content: `Department Hero & Student Achiever — Mullu Srinu:
• Role: NSS Coordinator & Ecom Hackathon MVP (Python Lead)
• Reg No: 25B95A6206 (CSIT Department)
• Category: Department Hero & Student Achiever
• Profile & Achievements: Mullu Srinu is a dedicated student leader, NSS coordinator, and Python development lead. In the Internal Ecom Hackathon 2022, he spearheaded the application development for Bhimavaram Online app, successfully onboarding 25 local shops and 1400+ products in a single day.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_pbs_kruti',
            fullName: 'P.B.S Kruti',
            firstName: 'kruti',
            lastName: 'kruti',
            category: 'Department Hero & Cultural Achiever',
            designation: '1st Prize Classical Dance Winner & Top Academic Achiever',
            department: 'CSD',
            regNo: '25B91A0789',
            isCR: false,
            searchableAliases: ['kruti', 'p.b.s kruti', 'pbs kruti', 'kruti pbs', 'pbs'],
            content: `Department Hero — P.B.S Kruti:
• Role: 1st Prize Winner in Classical Dance Group Performance (45th SRKREC Annual Day)
• Reg No: 25B91A0789 (CSD Department)
• Category: Department Hero & Cultural Achiever
• Profile: P.B.S Kruti is an exceptional classical dancer who secured 1st Prize at SRKREC Annual Day Celebrations. Renowned for her mesmerizing expressions, mudras, and devotion to traditional arts, she balances high academic standards with cultural leadership.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_lakshmi_prasanna',
            fullName: 'R. Lakshmi Prasanna',
            firstName: 'prasanna',
            lastName: 'lakshmi',
            category: 'Department Hero & Cultural Achiever',
            designation: '2nd Prize Classical Dance Winner & Performing Artist',
            department: 'CSIT',
            regNo: '24B91A6245',
            isCR: false,
            searchableAliases: ['lakshmi prasanna', 'r lakshmi prasanna', 'prasanna', 'lakshmi'],
            content: `Department Hero — R. Lakshmi Prasanna:
• Role: 2nd Prize Winner in Classical Dance Group Performance (45th SRKREC Annual Day)
• Reg No: 24B91A6245 (CSIT Department)
• Category: Department Hero & Cultural Achiever
• Profile: R. Lakshmi Prasanna is a passionate performing artist who won 2nd Prize in Classical Dance Group Performance at SRKREC. Celebrated for her technical precision, graceful stage presence, and expressive mudras.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },
        {
            id: 'person_pooja_sai_praveena',
            fullName: 'D Pooja Sai Praveena',
            firstName: 'praveena',
            lastName: 'pooja',
            category: 'Department Hero & National Athlete',
            designation: 'Gold Medalist Karate & JNTUK Athlete',
            department: 'CSIT',
            regNo: '24B91A6218',
            isCR: false,
            searchableAliases: ['pooja sai praveena', 'praveena', 'd pooja sai praveena', 'pooja praveena', 'pooja'],
            content: `Department Hero — D Pooja Sai Praveena:
• Role: Gold Medalist Karate (JNTUK Inter-Collegiate Tournament) & University Athlete
• Reg No: 24B91A6218 (CSIT Department)
• Category: Department Hero & National Sports Achiever
• Profile: D Pooja Sai Praveena is a Gold Medalist 🥇 in the JNTUK Inter-Collegiate Karate Tournament and represented JNTUK University at the South-West Inter-University Karate Championship in Chennai.`,
            url: 'heroes_of_department.php',
            ctaText: 'View Department Heroes →'
        },

        // --- FACULTY MEMBERS (25 FACULTY) ---
        {
            id: 'faculty_satyam',
            fullName: 'ANGARA SATYAM',
            firstName: 'satyam',
            lastName: 'angara',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'asatyam@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Artificial Intelligence & Intelligent Systems',
            searchableAliases: ['satyam', 'angara satyam', 'a satyam', 'a. satyam', 'satyam sir', 'satyam madam', 'dr satyam', 'prof satyam', 'satyam mudunuri'],
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
            fullName: 'K V V Satya Trinadh Naidu',
            firstName: 'trinadh',
            lastName: 'naidu',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'kvvstnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Cyber Security, Java, Python Application Development',
            searchableAliases: ['trinadh', 'trinadh naidu', 'satya trinadh', 'k v v satya trinadh naidu', 'trinadh sir', 'trinadh madam', 'dr trinadh', 'prof trinadh', 'kvvstnaidu', 'satya trinadh naidu'],
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
            fullName: 'Dr. Suresh Babu Mudunuri',
            firstName: 'suresh',
            lastName: 'mudunuri',
            category: 'Professor & Head of Department (CSD)',
            designation: 'Professor & HOD (CSD)',
            department: 'CSD',
            email: 'suresh.mudunuri@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (JNTU, 2010)',
            specialization: 'AI, Machine Learning & Cloud Infrastructure',
            searchableAliases: ['suresh', 'suresh babu', 'm suresh babu', 'dr suresh babu', 'mudunuri suresh babu', 'suresh babu mudunuri', 'suresh sir', 'suresh babu sir', 'dr suresh'],
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
            fullName: 'Dr. N. Gopala Krishna Murthy',
            firstName: 'murthy',
            lastName: 'gopala krishna',
            category: 'Professor & Head of Department (CSIT)',
            designation: 'Professor & HOD (CSIT)',
            department: 'CSIT',
            email: 'gopinukala@gmail.com',
            qualification: 'Ph.D in Information Technology (JNTU, 2011)',
            specialization: 'Information Technology Systems & Enterprise Networks',
            searchableAliases: ['ngk murthy', 'gopala krishna', 'gopala krishna murthy', 'dr ngk murthy', 'n gopala krishna murthy', 'murthy', 'murthy sir', 'gopala krishna sir', 'ngk murthy sir'],
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
            fullName: 'P MANOJ',
            firstName: 'manoj',
            lastName: 'pericherla',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'manoj.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Prompt Engineering & Generative AI',
            searchableAliases: ['manoj', 'p manoj', 'pericherla manoj', 'manoj sir', 'manoj madam', 'dr manoj', 'prof manoj'],
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
            fullName: 'A. Aswini Priyanka',
            firstName: 'aswini',
            lastName: 'priyanka',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'aapriyanka@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2015)',
            specialization: 'Cloud Computing & Web Technologies',
            searchableAliases: ['aswini', 'aswini priyanka', 'a aswini priyanka', 'aswini madam', 'aswini sir', 'dr aswini'],
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
            fullName: 'S. Mohan Krishna',
            firstName: 'mohan',
            lastName: 'krishna',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'mohanakrishna.seerla@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'AI, Machine Learning & Computer Vision',
            searchableAliases: ['mohan krishna', 's. mohan krishna', 's mohan krishna', 'mohan krishna sir', 'mohan sir', 'krishna sir'],
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
            fullName: 'P S V SURYA KUMAR',
            firstName: 'surya',
            lastName: 'kumar',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'psvsuryakumar@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'IoT (Internet of Things) & Embedded Systems',
            searchableAliases: ['surya kumar', 'p s v surya kumar', 'surya kumar sir', 'psv surya kumar', 'surya sir'],
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
            fullName: 'Dr. K. Srinivasa Rao',
            firstName: 'srinivasa',
            lastName: 'rao',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'ksrinivasarao@srkrec.ac.in',
            qualification: 'Ph.D in Computer Science (Andhra University, 2018)',
            specialization: 'Computer Networks & Security',
            searchableAliases: ['srinivasa rao', 'dr k srinivasa rao', 'k srinivasa rao', 'srinivasa rao sir', 'dr srinivasa', 'srinivasa sir'],
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
            fullName: 'K. Bhanu Rajesh Naidu',
            firstName: 'bhanu',
            lastName: 'rajesh',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'kbrnaidu@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Cloud Computing & DevOps Systems',
            searchableAliases: ['bhanu rajesh', 'bhanu rajesh naidu', 'k bhanu rajesh naidu', 'bhanu sir', 'bhanu rajesh sir'],
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
            fullName: 'N. Aneela',
            firstName: 'aneela',
            lastName: 'n',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSD)',
            department: 'CSD',
            email: 'aneela@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Data Mining',
            searchableAliases: ['aneela', 'n aneela', 'aneela madam', 'aneela sir', 'dr aneela'],
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
            fullName: 'M Sai Madhuri',
            firstName: 'madhuri',
            lastName: 'sai',
            category: 'Faculty Member',
            designation: 'Teaching Assistant (CSD)',
            department: 'CSD',
            email: 'madhuryamudundi@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Programming',
            searchableAliases: ['sai madhuri', 'madhuri madam', 'sai madhuri madam'],
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
            fullName: 'N. NAVYA',
            firstName: 'navya',
            lastName: 'nallaparaju',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'navyanallaparaju@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Structures',
            searchableAliases: ['navya', 'n navya', 'navya nallaparaju', 'navya madam', 'navya sir'],
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
            fullName: 'NETI PRAVEEN',
            firstName: 'praveen',
            lastName: 'neti',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'npraveen@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2016)',
            specialization: 'Machine Learning & Database Management',
            searchableAliases: ['neti praveen', 'n praveen', 'praveen sir', 'praveen madam'],
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
            fullName: 'K V SUNIL VARMA',
            firstName: 'sunil',
            lastName: 'varma',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'kvsunilvarma@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Software Engineering',
            searchableAliases: ['sunil varma', 'k v sunil varma', 'sunil varma sir', 'sunil sir'],
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
            fullName: 'P MOUNA',
            firstName: 'mouna',
            lastName: 'penmetsa',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'mouna.p@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & Neural Networks',
            searchableAliases: ['mouna', 'p mouna', 'penmetsa mouna', 'mouna madam', 'mouna sir'],
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
            fullName: 'ANUSURI KRISHNA VENI',
            firstName: 'krishna veni',
            lastName: 'anusuri',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'akveni@srkrec.ac.in',
            qualification: 'M.Tech in CSE (JNTUK, 2017)',
            specialization: 'Machine Learning & Data Mining',
            searchableAliases: ['krishna veni', 'a krishna veni', 'akveni', 'anusuri krishna veni', 'krishna veni madam'],
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
            fullName: 'D Parvathi',
            firstName: 'parvathi',
            lastName: 'd',
            category: 'Faculty Member',
            designation: 'Assistant Professor (CSIT)',
            department: 'CSIT',
            email: 'parvathiram21@gmail.com',
            qualification: 'M.Tech in CSE (JNTUK, 2018)',
            specialization: 'Machine Learning & C Programming',
            searchableAliases: ['d parvathi', 'parvathi madam', 'parvathi sir'],
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
            fullName: 'K Sri Vigyna',
            firstName: 'vignya',
            lastName: 'k',
            category: 'Faculty Member',
            designation: 'Teaching Assistant (CSIT)',
            department: 'CSIT',
            email: 'vignyak@gmail.com',
            qualification: 'M.Tech in CSE (SRKR, 2021)',
            specialization: 'Machine Learning & Python Lab',
            searchableAliases: ['vignya', 'vigyna', 'sri vignya', 'k sri vigyna', 'vignya madam', 'vignya sir'],
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
            fullName: 'M. SRINU',
            firstName: 'srinu',
            lastName: 'm',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            email: 'msrinu@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Computer Science & Information Technology',
            searchableAliases: ['m srinu', 'srinu sir', 'm. srinu'],
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
            fullName: 'J. MOHAN SURENDRA',
            firstName: 'surendra',
            lastName: 'mohan',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            email: 'mohansurendra.j@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Software Systems & Information Technology',
            searchableAliases: ['mohan surendra', 'surendra', 'j mohan surendra', 'surendra sir'],
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
            fullName: 'G. SUDHAKAR',
            firstName: 'sudhakar',
            lastName: 'g',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSIT)',
            department: 'CSIT',
            email: 'sudhakar.g@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSIT',
            specialization: 'Computer Science & Software Engineering',
            searchableAliases: ['sudhakar', 'g sudhakar', 'sudhakar sir'],
            content: `Faculty Profile — G. SUDHAKAR (Sudhakar Sir):
• Designation: Faculty Member (CSIT)
• Department: Computer Science & Software Engineering.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_girichar',
            fullName: 'K. GIRICHAR',
            firstName: 'girichar',
            lastName: 'k',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            email: 'girichar.k@srkrec.edu.in',
            qualification: 'B.Tech / M.Tech in CSD',
            specialization: 'Computer Science & Design',
            searchableAliases: ['girichar', 'giridhar', 'k girichar', 'girichar sir'],
            content: `Faculty Profile — K. GIRICHAR (Girichar Sir):
• Designation: Faculty Member (CSD)
• Department: Computer Science & Design (CSD)
• Contact Email: girichar.k@srkrec.edu.in
• Specialization: Computer Science & Design Thinking.`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_tulasi_rajesh',
            fullName: 'Jonnapalli Tulasi Rajesh',
            firstName: 'tulasi',
            lastName: 'rajesh',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            email: 'jtulasirajesh@srkrec.edu.in',
            searchableAliases: ['tulasi rajesh', 'jonnapalli tulasi rajesh', 'tulasi sir', 'rajesh faculty'],
            content: `Faculty Profile — Jonnapalli Tulasi Rajesh:
• Designation: Faculty Member (CSD)
• Department: Computer Science & Design (CSD)
• Contact Email: jtulasirajesh@srkrec.edu.in`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        },
        {
            id: 'faculty_suseela',
            fullName: 'M S Suseela',
            firstName: 'suseela',
            lastName: 'm',
            category: 'Faculty Member',
            designation: 'Faculty Member (CSD)',
            department: 'CSD',
            email: 'm.s.suseela@srkrec.edu.in',
            searchableAliases: ['suseela', 'm s suseela', 'suseela madam'],
            content: `Faculty Profile — M S Suseela:
• Designation: Faculty Member (CSD)
• Department: Computer Science & Design (CSD)
• Contact Email: m.s.suseela@srkrec.edu.in`,
            url: 'faculty.php',
            ctaText: 'View Faculty Profile →'
        }
    ];

    const MASTER_FACULTY_ROSTER = MASTER_PERSON_INDEX.filter(p => p.category.includes('Faculty') || p.category.includes('HOD'));

    /**
     * =========================================================================
     * 5. GRANULAR WEBSITE KNOWLEDGE MATRIX (22 SECTIONS)
     * =========================================================================
     */
    const KNOWLEDGE_MATRIX = [
        {
            id: 'live_announcements',
            category: 'Announcements',
            title: 'Live Updates & Current Event Spotlight',
            keywords: ['live updates', 'upcoming event', 'irumudi', 'trailer launch', 'movie launch', 'ravi teja', 'august 12', 'srkr grounds', 'announcements', 'latest update', 'current update'],
            tokens: ['live', 'update', 'irumudi', 'trailer', 'launch', 'august', 'ravi', 'teja', 'srkr', 'grounds', 'bhimavaram'],
            content: `Live Updates & Current Spotlight:
• "Irumudi" Grand Trailer Launch Event: August 12th from 4:30 PM onwards at SRKR Engineering College Grounds, Bhimavaram.
• Event Highlights: Featuring Mass Maharaja Ravi Teja film trailer launch presented by Mythri Movie Makers, T-Series, and YouWe Media.
• Open for all SRKR CSD & CSIT students and faculty.`,
            url: 'index.php',
            ctaText: 'View Live Updates on Homepage →'
        },
        {
            id: 'dept_overview',
            category: 'About',
            title: 'Department Overview & Establishment',
            keywords: ['about department', 'tell me about the department', 'department overview', 'department history', 'what is this department', 'about csd', 'about csit', 'tell me about this department'],
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
            keywords: ['hod', 'hods', 'who is hod', 'who are hods', 'who is the hod', 'head of department', 'department head', 'head of dept', 'in charge', 'who heads'],
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
            keywords: ['faculty', 'faculties', 'who are faculty members', 'who are the faculty members', 'tell me about the faculty', 'who teaches', 'who works', 'professors', 'teachers', 'faculty list', 'teaching staff'],
            tokens: ['faculty', 'faculties', 'professors', 'teachers', 'staff', 'teaching staff', 'who teaches', 'who works', 'faculty list'],
            content: `Department Faculty Directory:
Our department has 25 highly qualified faculty members across CSD and CSIT led by HODs Dr. M. Suresh Babu and Dr. NGK Murthy. Key faculty members include Trinadh Sir, Satyam Sir, Manoj Sir, Aswini Priyanka Madam, S. Mohan Krishna Sir, Dr. K. Srinivasa Rao Sir, N. Navya Madam, Neti Praveen Sir, and D. Parvathi Madam.`,
            url: 'faculty.php',
            ctaText: 'View Complete Faculty Directory →'
        },
        {
            id: 'courses_offered',
            category: 'Academics',
            title: 'B.Tech Degree Programs Offered',
            keywords: ['courses', 'programs', 'btech', 'degree', 'curriculum', 'syllabus', 'what courses are offered', 'what courses are available', 'branches', 'csd', 'csit'],
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
            keywords: ['facilities', 'labs', 'laboratory', 'infrastructure', 'ai ml lab', 'mac lab', 'cloud lab', 'library', 'computers', 'what facilities are available', 'what labs do we have', 'what labs are available'],
            tokens: ['facilities', 'labs', 'laboratory', 'infrastructure', 'ai ml lab', 'mac lab', 'cloud lab', 'library', 'computers'],
            content: `Department Facilities & Labs:
• AI & ML Specialized Lab: High-performance computing workstations configured for deep learning, NLP, and Computer Vision.
• Cloud & IoT Lab: Equipped with smart hardware kits, Raspberry Pi, and AWS cloud suites.
• Modern Computer Labs: 200+ high-end systems connected with gigabit fiber internet.
• Department Library: Collection of textbooks, IEEE digital subscriptions, and research journals.`,
            url: 'ai-ml-lab.php',
            ctaText: 'Explore AI & ML Lab →'
        },
        {
            id: 'events_overview',
            category: 'Events',
            title: 'Department Events, Hackathons & Workshops',
            keywords: ['events', 'hackathons', 'workshops', 'seminars', 'fest', 'jaitra', 'pitchathon', 'competitions', 'what events are there', 'latest event', 'potluck'],
            tokens: ['events', 'hackathons', 'workshops', 'seminars', 'fest', 'jaitra', 'pitchathon', 'competitions', 'potluck'],
            content: `Department Events & Activities:
• "Irumudi" Grand Trailer Launch Event: August 12 from 4:30 PM at SRKR Grounds (Mass Maharaja Ravi Teja movie trailer launch).
• Jaitra 2k26 (Annual Fest): March 15, 2026 | Main Campus Auditorium.
• National Level Technical Hackathon 2026: March 20, 2026 (24-Hour Overnight Hackathon).
• Ethical Hacking Workshop: February 28, 2026 (Led by Trinadh Sir).
• Department Potluck Event.`,
            url: 'events_overview.php',
            ctaText: 'View Events Overview Page →'
        },
        {
            id: 'student_houses',
            category: 'Student Houses',
            title: 'Five Student Houses (Jal, Agni, Vayu, Akash, Prudhvi)',
            keywords: ['houses', 'jal', 'agni', 'vayu', 'akash', 'prudhvi', 'student houses', 'what houses are there', 'five houses', 'house leaderboard'],
            tokens: ['houses', 'jal', 'agni', 'vayu', 'akash', 'prudhvi', 'student houses', 'house leaderboard'],
            content: `Five Student Houses & Elemental Leagues:
• 💧 Jal — Water Element (Adaptability & Analytics)
• 🔥 Agni — Fire Element (Passion & Innovation)
• 💨 Vayu — Air Element (Agile Development & Speed)
• 🌌 Akash — Ether/Sky Element (Vision & AI/Cloud)
• 🌍 Prudhvi — Earth Element (Ethics & Discipline)
Students compete in continuous hackathons, coding contests, sports, and cultural battles for the Annual Championship Shield.`,
            url: 'houses_dashboard.php',
            ctaText: 'House Dashboard & Standings →'
        },
        {
            id: 'student_achievements',
            category: 'Achievements',
            title: 'Student Achievements & Innovation Champions',
            keywords: ['student achievements', 'achievements', 'mullu srinu', 'preeti avvula', 'ecom hackathon', 'bhimavaram online', 'hackathon winners', 'awards', 'tell me about student achievements'],
            tokens: ['achievements', 'champions', 'mullu', 'srinu', 'preeti', 'avvula', 'hackathon', 'bhimavaram', 'online'],
            content: `Student Achievements & Innovation Champions:
• Internal Ecom Hackathon 2022: 2nd-year CSD & CSIT students competed across 4 houses (Agni, Vayu, Prithvi, Aakash), onboarding 25 shops and 1400+ products for Bhimavaram Online app on a single day.
• Featured Champions: Mullu Srinu (Ecom Hackathon MVP & Python Lead), Preeti Avvula (UI/UX & Mobile App Lead), Agni House Champions.`,
            url: 'student_achievements.php',
            ctaText: 'View Student Achievements →'
        },
        {
            id: 'heroes_overview',
            category: 'Department Heroes',
            title: 'Heroes of the Department',
            keywords: ['heroes', 'department heroes', 'who are the department heroes', 'hall of fame', 'student heroes'],
            tokens: ['heroes', 'department heroes', 'hall of fame', 'achievers'],
            content: `Heroes of the Department:
Honoring exceptional student achievers, TEDx organizers, martial arts champions, classical dancers, and leaders representing CSD & CSIT departments:
1. P.B.S Kruti — 1st Prize Winner Classical Dance
2. R. Lakshmi Prasanna — 2nd Prize Winner Classical Dance
3. D Pooja Sai Praveena — Gold Medalist Karate & JNTUK Athlete
4. Preeti Avvula — TEDx SRKR Core Organizer & Master Anchor
5. Mullu Srinu — NSS Coordinator & Ecom Hackathon MVP`,
            url: 'heroes_of_department.php',
            ctaText: 'Explore Department Heroes →'
        },
        {
            id: 'research_publications',
            category: 'Research',
            title: 'Department Research, Patents & Publications',
            keywords: ['research', 'publications', 'patents', 'papers', 'ieee', 'springer', 'scopus', 'research papers', 'research centers', 'tell me about research', 'projects', 'tell me about projects'],
            tokens: ['research', 'publications', 'patents', 'papers', 'scopus', 'ieee', 'springer', 'journals', 'projects'],
            content: `Department Research, Projects & Publications:
• 100+ Total Research Publications in Scopus, IEEE, Springer, and Elsevier indexed journals.
• Research Specializations: Artificial Intelligence, Machine Learning, Cyber Security Vulnerabilities, Cloud Resource Optimization, IoT Embedded Sensor Nodes, and Generative AI Prompt Engineering.
• Research & Project Labs: AI & ML Research Center led by Dr. M. Suresh Babu and Enterprise Systems Center led by Dr. NGK Murthy.
• Student Projects: AI Automation bots, Smart Laundry (Smart Wash), Nutrition Tech (NutriDelight), and E-commerce platforms.`,
            url: 'faculty_achievements.php',
            ctaText: 'View Research & Publications →'
        },
        {
            id: 'startup_ecosystem',
            category: 'Startups & Innovation',
            title: 'Startup Club, Incubator & Student Ventures',
            keywords: ['startups', 'startup', 'startup club', 'innovation', 'entrepreneurship', 'companies', 'business', 'ecosystem', 'nutridelight', 'smart wash', 'tell me about startups', 'what startups are there', 'tell me about incubation', 'incubation'],
            tokens: ['startups', 'startup', 'innovation', 'entrepreneur', 'entrepreneurship', 'hub', 'business', 'incubator', 'seed', 'nutridelight', 'smart wash', 'incubation'],
            content: `Startup Club & Innovation Ecosystem:
The SRKREC Startup Club empowers student entrepreneurs to build innovative solutions and launch real-world ventures. 
• Incubation Hub: Workspace incubation, prototype funding, seed funding guidance, and business mentorship.
• Featured Startups:
  - NutriDelight: Healthy food delivery & nutrition tech platform.
  - Smart Wash: IoT-enabled automated laundry service for campus students.
• Impact: 5+ active student startups, 200+ daily customers served, 3+ industry sectors.`,
            url: 'startup_club.php',
            ctaText: 'Explore Startup Ecosystem →'
        },
        {
            id: 'clubs_all',
            category: 'Clubs',
            title: 'Department Student Clubs',
            keywords: ['clubs', 'what clubs are available', 'student clubs', 'coding club', 'cybersecurity club', 'swecha club', 'sdc club', 'startup club'],
            tokens: ['clubs', 'coding', 'cybersecurity', 'swecha', 'sdc', 'startup'],
            content: `Department Student Clubs:
• Coding Club: 500+ active members, competitive programming (CodeChef, HackerRank), ACM ICPC training, 24-48 hr hackathons.
• Cybersecurity Club: Ethical hacking, network vulnerability assessment, CTF (Capture The Flag) competitions.
• Swecha Club: Free & Open Source Software (FOSS), Linux administration, open-source contribution camps.
• Skill Development Center (SDC): APSSDC certification courses and industry technical bootcamps.
• Startup Club: Innovation incubation and venture creation.`,
            url: 'coding-club.php',
            ctaText: 'View Student Clubs →'
        },
        {
            id: 'students_overview',
            category: 'Students',
            title: 'Student Activities & Section Faculty',
            keywords: ['students', 'student activities', 'who are the students', 'who are students', 'tell me about students', 'what do students do', 'student life', 'student portal', 'student dashboard', 'sections', 'designated faculty'],
            tokens: ['students', 'student', 'activities', 'dashboard', 'portal', 'life', 'sections'],
            content: `Students Overview & Section Designated Faculty:
Our students are actively engaged in rigorous academics, technical clubs, and the 5 House leagues.
• Class Section Designated Faculty:
  - CSD Section A: A. Aswini Priyanka, M. Srinu, Angara Satyam, D. Parvathi, Dr. M. Suresh Babu
  - CSIT Section A: A. Aswini Priyanka, D. Parvathi, K. Bhanu Rajesh Naidu, Angara Satyam, N. Aneela, Dr. NGK Murthy
  - CSIT Section B: A. Aswini Priyanka, K. Bhanu Rajesh Naidu, Mr. K.V.V.S. Trinadh Naidu, N. Aneela, Dr. NGK Murthy`,
            url: 'students_overview.php',
            ctaText: 'View Students Overview →'
        },
        {
            id: 'internships_overview',
            category: 'Internships',
            title: 'Student Internships & Industrial Stipends',
            keywords: ['internships', 'internship', 'intern', 'interns', 'stipend', 'ppo', 'training', 'pre-placement', 'where can students get internships', 'internship opportunities', 'internship programs', 'tell me about internships'],
            tokens: ['internships', 'internship', 'intern', 'interns', 'stipend', 'ppo', 'training', 'corporate', 'opportunities'],
            content: `Student Internships & Training:
We build real-world engineering skills through corporate internships, paid industrial stipends, and pre-placement training programs.
• Stats: 120+ Students Interning, 85% PPO (Pre-Placement Offer) Conversion, 45+ Corporate Partners. Highest stipend is ₹50K/month.
• Featured Opportunities: Software Development Intern (Amazon, ₹45k/mo), Full Stack Web Developer (TCS, ₹35k/mo), Data Science & AI Intern (Wipro, ₹50k/mo).`,
            url: 'internships.php',
            ctaText: 'View Internship Opportunities →'
        },
        {
            id: 'contact_info',
            category: 'Contact',
            title: 'Contact Information & Campus Address',
            keywords: ['contact', 'address', 'location', 'phone', 'email', 'where is college', 'reach out', 'bhimavaram', 'what is the department contact information', 'contact information'],
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
     * 6. HOUSE MEMBER & LEAGUE INTENT ENGINE
     * Handles specific house member lookup ("jal house members", "members of agni")
     * vs general 5-house overview ("what are the five houses") vs follow-ups.
     * =========================================================================
     */
    function searchHouseSystem(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        // 1. GENERAL 5-HOUSES OVERVIEW QUERY
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

Students compete in continuous hackathons, coding contests, sports, and cultural battles for the Annual Championship Shield. Ask for "Jal house members", "Agni house members", "Vayu house members", "Akash house members", or "Prudhvi house members" to view specific house rosters!`,
                url: 'houses_dashboard.php',
                ctaText: 'View House Leaderboard →'
            };
        }

        // 2. DETECT SPECIFIC HOUSE NAME (Jal, Agni, Vayu, Akash/Aakash, Prudhvi/Prithvi)
        let requestedHouseKey = null;
        if (/\b(jal|water|beta)\b/i.test(lower)) requestedHouseKey = 'JAL';
        else if (/\b(agni|fire|epsilon)\b/i.test(lower)) requestedHouseKey = 'AGNI';
        else if (/\b(vayu|wind|gamma)\b/i.test(lower)) requestedHouseKey = 'VAYU';
        else if (/\b(akash|aakash|sky|alpha)\b/i.test(lower)) requestedHouseKey = 'AAKASH';
        else if (/\b(prudhvi|pruthvi|earth|delta)\b/i.test(lower)) requestedHouseKey = 'PRUDHVI';

        // 3. DETECT FOLLOW-UP CONVERSATIONAL INTENTS ("show their registration numbers", "who has highest points?")
        const isFollowUpRegQuery = /\b(registration number|registration numbers|reg no|reg number|reg numbers|show reg|show registration)\b/i.test(lower);
        const isFollowUpTopQuery = /\b(highest points|top points|top performer|top member|captain|who has highest points)\b/i.test(lower);

        if (!requestedHouseKey && (isFollowUpRegQuery || isFollowUpTopQuery) && conversationContext.activeHouse) {
            requestedHouseKey = conversationContext.activeHouse;
        }

        if (!requestedHouseKey) return null;

        // Save active house in conversation context for follow-up questions
        conversationContext.activeHouse = requestedHouseKey;

        const houseData = MASTER_HOUSE_ROSTER[requestedHouseKey];
        if (!houseData) return null;

        const displayName = houseData.name;
        const membersList = houseData.members;

        // Sub-case 3.1: Follow-up question for Registration Numbers
        if (isFollowUpRegQuery) {
            let regItems = membersList.slice(0, 15).map((m, idx) => `${idx + 1}. <strong>${m.name}</strong> — Reg: ${m.regNo || 'N/A'} (${m.section || 'CSD/CSIT'})`).join('<br>');
            return {
                id: `house_reg_${requestedHouseKey}`,
                category: 'House Members',
                title: `${displayName} House Members — Registration Numbers`,
                content: `Here are the registration numbers for ${displayName} House members (Total: ${membersList.length} students):<br><br>${regItems}<br><br><em>Showing top 15 members. View full roster on house page.</em>`,
                url: `house_detail.php?house=${displayName}`,
                ctaText: `View Complete ${displayName} House Page →`
            };
        }

        // Sub-case 3.2: Follow-up question for Top Points / Captain
        if (isFollowUpTopQuery) {
            let sortedByPoints = [...membersList].sort((a, b) => (b.points || 0) - (a.points || 0));
            let topMember = sortedByPoints[0] || membersList[0];
            return {
                id: `house_top_${requestedHouseKey}`,
                category: 'House Members',
                title: `${displayName} House — Top Performer`,
                content: `🏆 <strong>Top Performer in ${displayName} House:</strong><br><br>• <strong>Name:</strong> ${topMember.name}<br>• <strong>Registration Number:</strong> ${topMember.regNo || 'N/A'}<br>• <strong>Section:</strong> ${topMember.section || 'CSD/CSIT'}<br>• <strong>House Points:</strong> ${topMember.points || 0} pts`,
                url: `house_detail.php?house=${displayName}`,
                ctaText: `View ${displayName} House Details →`
            };
        }

        // Sub-case 3.3: Primary House Members Query ("jal house members", "members of agni", "vayu students", "jal")
        let displayedMembers = membersList.slice(0, 15);
        let listItems = displayedMembers.map((m, idx) => `${idx + 1}. <strong>${m.name}</strong> — Reg: ${m.regNo || 'N/A'} | Section: ${m.section || 'CSD/CSIT'}`).join('<br>');

        return {
            id: `house_members_${requestedHouseKey}`,
            category: 'House Members',
            title: `${displayName} House Members`,
            content: `Here are the members of <strong>${displayName} House</strong> (Total: ${membersList.length} students):<br><br>${listItems}<br><br><em>Showing top 15 of ${membersList.length} members. You can ask "show their registration numbers" or "who has highest points?".</em>`,
            url: `house_detail.php?house=${displayName}`,
            ctaText: `View Full ${displayName} House Roster →`
        };
    }

    /**
     * =========================================================================
     * 7. CLASS REPRESENTATIVE (CR) INTENT & RETRIEVAL ENGINE
     * =========================================================================
     */
    function searchCRSystem(rawQuery) {
        if (!rawQuery) return null;
        const lower = rawQuery.toLowerCase().trim();

        const isCRQuery = /\b(cr|crs|c\.r\.|c\.r\.s|class representative|class representatives|class rep|class reps)\b/i.test(lower);
        if (!isCRQuery) return null;

        // Sub-case A: "Is [Person] a CR?" Verification
        const isPersonVerification = /\b(is|tell me about the cr)\b/i.test(lower);
        if (isPersonVerification && !/\b(who is the cr|who are the crs|who is our cr|who are our crs|who is the class representative|who are the class representatives|who is the class rep|who are the class reps)\b/i.test(lower)) {
            let queryName = lower.replace(/\b(is|a|the|cr|crs|c\.r\.|c\.r\.s|class representative|class representatives|class rep|class reps|details|about)\b/g, '').trim();
            queryName = normalizePersonName(queryName);

            if (queryName && queryName.length >= 2) {
                const matchedCR = MASTER_CR_INDEX.find(cr => {
                    const normFull = normalizePersonName(cr.fullName);
                    const normFirst = normalizePersonName(cr.firstName);
                    return queryName === normFull || queryName === normFirst;
                });

                if (matchedCR) {
                    return {
                        id: 'cr_person_confirmed',
                        category: 'Class Representative',
                        title: `${matchedCR.fullName} — Class Representative`,
                        content: `Yes! <strong>${matchedCR.fullName}</strong> is a verified Class Representative (CR) for <strong>${matchedCR.className}</strong> (Reg No: ${matchedCR.regNo}).`,
                        url: 'heroes_of_department.php#class-representatives',
                        ctaText: 'View Class Representatives →'
                    };
                }

                const matchedPerson = MASTER_PERSON_INDEX.find(p => {
                    const normFull = normalizePersonName(p.fullName);
                    const normFirst = normalizePersonName(p.firstName);
                    return queryName === normFull || queryName === normFirst;
                });

                if (matchedPerson) {
                    return {
                        id: 'cr_person_not_cr',
                        category: 'Class Representatives',
                        title: `${matchedPerson.fullName} — Role Clarification`,
                        content: `No, <strong>${matchedPerson.fullName}</strong> is registered as a <strong>${matchedPerson.category}</strong> (${matchedPerson.designation || matchedPerson.department}), but is not listed as a designated Class Representative (CR).`,
                        url: matchedPerson.url || 'heroes_of_department.php',
                        ctaText: matchedPerson.ctaText || 'View Profile →'
                    };
                }
            }
        }

        // Sub-case B: Branch, Year, or Section Filters
        let filteredCRs = [...MASTER_CR_INDEX];

        const hasCSD = /\bcsd\b/i.test(lower);
        const hasCSIT = /\bcsit\b/i.test(lower);
        if (hasCSD && !hasCSIT) {
            filteredCRs = filteredCRs.filter(cr => cr.branch === 'CSD');
        } else if (hasCSIT && !hasCSD) {
            filteredCRs = filteredCRs.filter(cr => cr.branch === 'CSIT');
        }

        if (/\b(2nd|2|second|ii)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.yearNum === 2);
        } else if (/\b(3rd|3|third|iii)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.yearNum === 3);
        } else if (/\b(4th|4|fourth|iv)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.yearNum === 4);
        }

        if (/\b(section a|sec a|\ba\b)\b/i.test(lower) && !/\b(section b|sec b|\bb\b)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.section === 'Section A');
        } else if (/\b(section b|sec b|\bb\b)\b/i.test(lower) && !/\b(section a|sec a|\ba\b)\b/i.test(lower)) {
            filteredCRs = filteredCRs.filter(cr => cr.section === 'Section B');
        }

        let filterTitle = 'Department Class Representatives (CRs)';
        if (hasCSD && !hasCSIT) filterTitle = 'CSD Class Representatives (CRs)';
        if (hasCSIT && !hasCSD) filterTitle = 'CSIT Class Representatives (CRs)';

        if (filteredCRs.length === 0) {
            return {
                id: 'cr_none_found',
                category: 'Class Representatives',
                title: filterTitle,
                content: `I couldn't find any Class Representatives matching your specific filter in the department records.`,
                url: 'heroes_of_department.php#class-representatives',
                ctaText: 'View Class Representatives →'
            };
        }

        let groupedText = filteredCRs.map(cr => {
            return `• <strong>${cr.fullName}</strong> — Reg: ${cr.regNo} | ${cr.className}`;
        }).join('<br>');

        let subtitle = `Here are the official Class Representatives for ${filterTitle}:`;
        if (/\b(who is the cr|who is our cr|who is the class representative|who is the class rep)\b/i.test(lower) && !hasCSD && !hasCSIT && !/\b(2nd|3rd|4th|ii|iii|iv)\b/i.test(lower)) {
            subtitle = `Our department has 14 Class Representatives across 2nd, 3rd, and 4th Years for CSD & CSIT. Which year or branch would you like to view (e.g. CSD, CSIT, 2nd Year, 3rd Year, 4th Year)?<br><br>Here is the full directory:`;
        }

        return {
            id: 'cr_list_result',
            category: 'Class Representatives',
            title: filterTitle,
            content: `${subtitle}<br><br>${groupedText}`,
            url: 'heroes_of_department.php#class-representatives',
            ctaText: 'View All Class Representatives on Website →'
        };
    }

    /**
     * =========================================================================
     * 8. STRICT TOKENIZED PERSON SEARCH ENGINE (SOLVES MOHANA DURGA BUG)
     * =========================================================================
     */
    function searchPersonSystem(rawQuery) {
        if (!rawQuery) return null;

        const normQuery = normalizePersonName(rawQuery);
        if (!normQuery || normQuery.length < 2) return null;

        const nonPersonTopicKeywords = /^\b(the|department|college|hod|hods|head|head of department|hero|heroes|department heroes|faculty|faculties|student|students|houses|courses|labs|placements|startups|incubation|events|clubs|syllabus|contact|achievements|internships|research|publications|projects|cr|crs|c\.r\.|c\.r\.s|class representative|class representatives|class rep|class reps|jal|agni|vayu|akash|aakash|prudhvi|pruthvi)\b$/i;
        if (nonPersonTopicKeywords.test(normQuery) || /\b(hod|hods|head of department|department heroes|faculty members|who are the faculty|who are the students|who are the heroes|who is the cr|who are the crs|class representative|class rep|jal house|agni house|vayu house|akash house|prudhvi house|members of jal|members of agni|members of vayu|members of akash|members of prudhvi)\b/i.test(rawQuery)) return null;

        const regMatch = rawQuery.match(/\b([0-9]{2}[a-z0-9]{8,10})\b/i);
        if (regMatch) {
            const searchedReg = regMatch[1].toUpperCase();
            const foundByReg = MASTER_PERSON_INDEX.find(p => p.regNo && p.regNo.toUpperCase() === searchedReg);
            if (foundByReg) {
                return {
                    found: true,
                    isMultiple: false,
                    person: foundByReg,
                    chunk: {
                        id: foundByReg.id,
                        category: foundByReg.category,
                        title: `${foundByReg.fullName} — ${foundByReg.category}`,
                        content: foundByReg.content,
                        url: foundByReg.url,
                        ctaText: foundByReg.ctaText
                    }
                };
            }
        }

        const queryTokens = tokenizeName(rawQuery);
        if (queryTokens.length === 0) return null;

        let exactFullMatches = [];
        let allTokensMatchedCandidates = [];

        for (const person of MASTER_PERSON_INDEX) {
            const normFullName = normalizePersonName(person.fullName);
            const personTokens = tokenizeName(person.fullName);

            if (normQuery === normFullName) {
                exactFullMatches.push(person);
                continue;
            }

            let allTokensMatched = queryTokens.every(qTok => {
                const matchInName = personTokens.some(pTok => pTok === qTok);
                const matchInAlias = person.searchableAliases && person.searchableAliases.some(alias => tokenizeName(alias).includes(qTok));
                return matchInName || matchInAlias;
            });

            let hasSubstringBleed = queryTokens.some(qTok => {
                return personTokens.some(pTok => pTok !== qTok && (pTok.startsWith(qTok) || qTok.startsWith(pTok)));
            });

            if (allTokensMatched && !hasSubstringBleed) {
                allTokensMatchedCandidates.push(person);
            }
        }

        if (exactFullMatches.length === 1) {
            const p = exactFullMatches[0];
            return {
                found: true,
                isMultiple: false,
                person: p,
                chunk: {
                    id: p.id,
                    category: p.category,
                    title: `${p.fullName} — ${p.category}`,
                    content: p.content,
                    url: p.url,
                    ctaText: p.ctaText
                }
            };
        }

        if (allTokensMatchedCandidates.length === 1) {
            const p = allTokensMatchedCandidates[0];
            return {
                found: true,
                isMultiple: false,
                person: p,
                chunk: {
                    id: p.id,
                    category: p.category,
                    title: `${p.fullName} — ${p.category}`,
                    content: p.content,
                    url: p.url,
                    ctaText: p.ctaText
                }
            };
        }

        const totalMatches = [...exactFullMatches, ...allTokensMatchedCandidates];
        const uniqueMatches = Array.from(new Set(totalMatches.map(p => p.id))).map(id => totalMatches.find(p => p.id === id));

        if (uniqueMatches.length > 1) {
            let listItems = uniqueMatches.map((p, idx) => `${idx + 1}. <strong>${p.fullName}</strong> (${p.designation || p.category})`).join('<br>');
            return {
                found: true,
                isMultiple: true,
                chunk: {
                    id: 'people_multiple_matches',
                    category: 'People Search',
                    title: 'Multiple Matching People Found',
                    content: `I found multiple people with similar names:<br><br>${listItems}<br><br>Could you provide the person's full name, year, section, or registration number?`,
                    url: 'heroes_of_department.php',
                    ctaText: 'Explore Department People Directory →'
                }
            };
        }

        const isExplicitPersonQuestion = /^\b(who is|tell me about|profile of|info on)\b/i.test(rawQuery.trim());
        if (isExplicitPersonQuestion) {
            const formattedName = normQuery.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            return {
                found: false,
                requestedName: formattedName,
                chunk: {
                    id: 'person_not_found',
                    category: 'People Search',
                    title: 'Person Not Found',
                    isNotFound: true,
                    content: `I couldn't find an exact match. Please provide the person's full name, registration number, year, or role.`,
                    url: 'heroes_of_department.php',
                    ctaText: 'View Department Directory →'
                }
            };
        }

        return null;
    }

    function extractFacultyQueryName(rawQuery) {
        const lower = rawQuery.toLowerCase().trim();
        const explicitFacultyHonorifics = ['sir', 'madam', 'ma\'am', 'mam', 'dr.', 'dr ', 'prof.', 'prof ', 'faculty', 'teacher'];
        let hasFacultyHonorific = explicitFacultyHonorifics.some(h => lower.includes(h));
        return {
            isFacultyQuery: hasFacultyHonorific,
            extractedName: normalizePersonName(rawQuery),
            rawQuery: lower
        };
    }

    function matchFacultyFromRoster(extractedInfo) {
        return searchPersonSystem(extractedInfo.rawQuery);
    }

    /**
     * =========================================================================
     * 9. RELEVANCE VECTOR RERANKING ENGINE & HYBRID SEARCH
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

        if (/\b(internship|internships|stipend|ppo|intern)\b/i.test(queryLower)) {
            if (chunk.id === 'internships_overview') score += 250;
        }

        if (/\b(section|sections|designated faculty|csit section|csd section)\b/i.test(queryLower)) {
            if (chunk.id === 'students_overview') score += 250;
        }

        if (/\b(heroes|department heroes|hero)\b/i.test(queryLower)) {
            if (chunk.id === 'heroes_overview') score += 250;
        }

        if (/\b(achievements|student achievements|hackathon winners)\b/i.test(queryLower)) {
            if (chunk.id === 'student_achievements') score += 250;
        }

        if (/\b(research|publications|patents|papers|projects)\b/i.test(queryLower)) {
            if (chunk.id === 'research_publications') score += 250;
        }

        if (/\b(about department|tell me about department|tell me about this department|overview)\b/i.test(queryLower) && !/\b(faculty|events|houses|courses|facilities|placements|achievements|research|publications|internships|startups)\b/i.test(queryLower)) {
            if (chunk.id === 'dept_overview') score += 150;
        }

        return score;
    }

    function searchKnowledgeVector(rawQuery) {
        const houseResult = searchHouseSystem(rawQuery);
        if (houseResult) {
            console.log('[CHATBOT RAG] House Match Found:', houseResult.title);
            return houseResult;
        }

        const crResult = searchCRSystem(rawQuery);
        if (crResult) {
            console.log('[CHATBOT RAG] CR Match Found:', crResult.title);
            return crResult;
        }

        const personResult = searchPersonSystem(rawQuery);
        if (personResult) {
            if (personResult.found) {
                console.log('[CHATBOT RAG] Person Match Found:', personResult.chunk.title);
                return personResult.chunk;
            } else if (personResult.requestedName) {
                console.log('[CHATBOT RAG] Person Not Found:', personResult.requestedName);
                return personResult.chunk;
            }
        }

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

    function verifyAndEnforceFacultyResponse(userInput, matchedChunk, rawAnswer) {
        return rawAnswer;
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
                ctaLinks: [{ text: matchedChunk.ctaText || 'View Directory →', url: matchedChunk.url || 'heroes_of_department.php' }],
                suggestions: ['Who is Mohana Durga?', 'Who is Mohan Krishna?', 'Who is Satyam Sir?', 'Who is Trinadh Sir?']
            };
        }

        return {
            answer: `<strong>${matchedChunk.title}:</strong><br><br>${matchedChunk.content.replace(/\n/g, '<br>')}`,
            ctaLinks: [{ text: matchedChunk.ctaText, url: matchedChunk.url }],
            suggestions: ['Jal house members', 'Agni house members', 'Vayu house members', 'Akash house members', 'Prudhvi house members']
        };
    }

    const responseCache = new Map();

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
                    answer: `I'm doing great! Thank you for asking. 😊 I'm fully equipped to answer questions about house members (Jal, Agni, Vayu, Akash, Prudhvi), faculty, student heroes, CRs, courses, labs, placements, and startups. How can I assist you today?`,
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

            let timeRetrievalStart = performance.now();
            let matchedChunk = searchKnowledgeVector(userInput);
            const timeRetrievalEnd = performance.now();
            console.log(`[CHATBOT] Retrieval: ${(timeRetrievalEnd - timeRetrievalStart).toFixed(2)} ms`);

            if (matchedChunk && matchedChunk.isNotFound) {
                const notFoundRes = synthesizeLocalAnswer(matchedChunk, userInput);
                responseCache.set(normalizedQuery, notFoundRes);
                return notFoundRes;
            }

            let finalResponse = null;

            if (matchedChunk) {
                const wordCount = normalizedQuery.split(/\s+/).length;
                const requiresReasoning = /\b(why|how|difference|compare|explain|give|example|skills|need|help)\b/i.test(normalizedQuery);

                if (matchedChunk.category === 'House Members' || matchedChunk.category === 'Student Houses Overview' || matchedChunk.category === 'Class Representative' || matchedChunk.category === 'Class Representatives' || matchedChunk.category === 'Faculty' || matchedChunk.category.includes('Hero') || matchedChunk.category.includes('People') || (wordCount <= 6 && !requiresReasoning)) {
                    console.log('[CHATBOT] Smart Bypass: Factual/Person/CR/House query. Skipping Gemini to save API quota.');
                    finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                    finalResponse.answer = verifyAndEnforceFacultyResponse(userInput, matchedChunk, finalResponse.answer);
                    responseCache.set(normalizedQuery, finalResponse);

                    const timeEnd = performance.now();
                    console.log(`[CHATBOT] Total: ${(timeEnd - timeStart).toFixed(2)} ms`);
                    return finalResponse;
                }
            }

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
                            suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
                        };
                    } else if (proxyData.status === 'api_error' && proxyData.message && proxyData.message.includes('429')) {
                        finalResponse = {
                            answer: `I am currently receiving too many requests (API Rate Limit). Please wait a moment, or ask me a direct question about the department which I can answer from my local database!`,
                            ctaLinks: [],
                            suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
                        };
                    }
                }
            } catch (err) {
                console.warn('Backend proxy check failed, checking direct Gemini client key...', err);
            }

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
                            suggestions: ['Jal house members', 'Agni house members', 'Who is Mohana Durga?']
                        };
                    } catch (err) {
                        console.warn('Client Gemini call failed, executing local RAG Engine:', err);
                    }
                }
            }

            const timeGeminiEnd = performance.now();
            console.log(`[CHATBOT] Gemini: ${(timeGeminiEnd - timeGeminiStart).toFixed(2)} ms`);

            if (!finalResponse) {
                finalResponse = synthesizeLocalAnswer(matchedChunk, userInput);
                finalResponse.answer = verifyAndEnforceFacultyResponse(userInput, matchedChunk, finalResponse.answer);

                conversationContext.history.push({ role: 'user', text: userInput });
                conversationContext.history.push({ role: 'model', text: finalResponse.answer });
            }

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
        getMasterPersonIndex: function () { return MASTER_PERSON_INDEX; },
        getMasterCRIndex: function () { return MASTER_CR_INDEX; },
        getMasterHouseRoster: function () { return MASTER_HOUSE_ROSTER; },
        getContextState: function () { return conversationContext; },
        resetContext: function () {
            conversationContext = { activeEntity: null, activeTopic: null, activeHouse: null, lastQuery: null, history: [] };
        }
    };
})();
