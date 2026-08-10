<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include "./head.php"; 

// Data dictionary for startups supporting primaryImage (Hero) and secondaryImage (Details)
$startupsData = [
    'bhimavaram-online' => [
        'id' => 'bhimavaram-online',
        'name' => 'Bhimavaram Online',
        'category' => 'Hyperlocal E-Commerce App',
        'tagline' => '"First ONDC-Enabled Hyperlocal Marketplace in AP & Telangana!"',
        'description' => 'Bhimavaram Online is a hyperlocal e-commerce platform designed to bring a wide range of products and services to customers in Bhimavaram.',
        'aboutTitle' => 'About Bhimavaram Online',
        'about' => 'Bhimavaram Online is a hyperlocal e-commerce platform designed to bring a wide range of products and services to customers in Bhimavaram. The platform provides convenient access to restaurant food, groceries, fruits and vegetables, meat and fish, and other local products through a single digital platform.<br><br>Bhimavaram Online focuses on connecting local businesses with customers and making everyday shopping and food ordering more convenient through technology. It is positioned as an ONDC-enabled platform serving Bhimavaram and the surrounding local community.',
        'primaryImage' => 'public/startups/bhimavaram-online/bhimavaramonline.png',
        'secondaryImage' => 'public/startups/bhimavaram-online/detail3.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/22.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/22.png',
        'founder' => 'Dr. M. Suresh Babu',
        'phone' => '9866600002',
        'appUrl' => 'https://play.google.com/store/apps/details?id=com.bhimavaramonline.androidapp',
        'instagram' => 'https://www.instagram.com/bhimavaram_online?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==',
        'themeColor' => '#2563eb',
        'themeColorDark' => '#1e3a8a',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%)',
    ],
    'bhimavaramonline' => [
        'id' => 'bhimavaram-online',
        'name' => 'Bhimavaram Online',
        'category' => 'Hyperlocal E-Commerce App',
        'tagline' => '"First ONDC-Enabled Hyperlocal Marketplace in AP & Telangana!"',
        'description' => 'Bhimavaram Online is a hyperlocal e-commerce platform designed to bring a wide range of products and services to customers in Bhimavaram.',
        'aboutTitle' => 'About Bhimavaram Online',
        'about' => 'Bhimavaram Online is a hyperlocal e-commerce platform designed to bring a wide range of products and services to customers in Bhimavaram. The platform provides convenient access to restaurant food, groceries, fruits and vegetables, meat and fish, and other local products through a single digital platform.<br><br>Bhimavaram Online focuses on connecting local businesses with customers and making everyday shopping and food ordering more convenient through technology. It is positioned as an ONDC-enabled platform serving Bhimavaram and the surrounding local community.',
        'primaryImage' => 'public/startups/bhimavaram-online/bhimavaramonline.png',
        'secondaryImage' => 'public/startups/bhimavaram-online/detail3.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/22.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/22.png',
        'founder' => 'Dr. M. Suresh Babu',
        'phone' => '9866600002',
        'appUrl' => 'https://play.google.com/store/apps/details?id=com.bhimavaramonline.androidapp',
        'instagram' => 'https://www.instagram.com/bhimavaram_online?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==',
        'themeColor' => '#2563eb',
        'themeColorDark' => '#1e3a8a',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%)',
    ],
    'bhimavaram-digitals' => [
        'id' => 'bhimavaram-digitals',
        'name' => 'Bhimavaram Digitals',
        'category' => 'Digital Marketing Startup',
        'tagline' => '"Creative Digital Strategies & Modern Marketing Approaches"',
        'description' => 'Bhimavaram Digitals is a digital marketing startup focused on helping businesses build a strong and effective presence in the digital world.',
        'about' => 'Bhimavaram Digitals is a digital marketing startup focused on helping businesses build a strong and effective presence in the digital world. The startup provides digital marketing solutions designed to help businesses connect with their audience, improve their online visibility, and grow their brand.<br><br>With a focus on creative digital strategies and modern marketing approaches, Bhimavaram Digitals aims to support businesses in reaching the right customers and creating a stronger presence across digital platforms.',
        'primaryImage' => 'public/startups/bhimavaram-digital/bhimavaram-digitals.png',
        'secondaryImage' => 'public/startups/bhimavaram-digital/detail2.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/20.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/20.png',
        'address' => "2nd Floor, Technology Centre,\nSRKR Engineering College,\nJuvvalapalem Road,\nBhimavaram,\nAndhra Pradesh - 534204",
        'mapUrl' => 'https://maps.app.goo.gl/4cWUkrv93vAtf1278',
        'phone' => '999 222 3542',
        'email' => 'bhimavaramdigitals@gmail.com',
        'instagram' => 'https://www.instagram.com/bhimavaramdigitals?igsh=Z2pxdHd3b3BsbmJu',
        'themeColor' => '#0284c7',
        'themeColorDark' => '#0369a1',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%)',
    ],
    'bhimavaram-digital' => [
        'id' => 'bhimavaram-digital',
        'name' => 'Bhimavaram Digitals',
        'category' => 'Digital Marketing Startup',
        'tagline' => '"Creative Digital Strategies & Modern Marketing Approaches"',
        'description' => 'Bhimavaram Digitals is a digital marketing startup focused on helping businesses build a strong and effective presence in the digital world.',
        'about' => 'Bhimavaram Digitals is a digital marketing startup focused on helping businesses build a strong and effective presence in the digital world. The startup provides digital marketing solutions designed to help businesses connect with their audience, improve their online visibility, and grow their brand.<br><br>With a focus on creative digital strategies and modern marketing approaches, Bhimavaram Digitals aims to support businesses in reaching the right customers and creating a stronger presence across digital platforms.',
        'primaryImage' => 'public/startups/bhimavaram-digital/bhimavaram-digitals.png',
        'secondaryImage' => 'public/startups/bhimavaram-digital/detail2.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/20.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/20.png',
        'address' => "2nd Floor, Technology Centre,\nSRKR Engineering College,\nJuvvalapalem Road,\nBhimavaram,\nAndhra Pradesh - 534204",
        'mapUrl' => 'https://maps.app.goo.gl/4cWUkrv93vAtf1278',
        'phone' => '999 222 3542',
        'email' => 'bhimavaramdigitals@gmail.com',
        'instagram' => 'https://www.instagram.com/bhimavaramdigitals?igsh=Z2pxdHd3b3BsbmJu',
        'themeColor' => '#0284c7',
        'themeColorDark' => '#0369a1',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%)',
    ],
    'smart-wash' => [
        'id' => 'smart-wash',
        'name' => 'Smart Wash',
        'category' => 'Smart Laundry & Fabric Care',
        'tagline' => '"For Smart People"',
        'description' => 'Get the best laundry services in Bhimavaram with doorstep pickup, clean care, and affordable student rates.',
        'about' => '<strong>Smart Wash</strong> is a modern laundry and garment-care service designed to make everyday clothing care simple, convenient, and reliable. We provide professional <strong>laundry, dry wash, steam ironing, stain removal, and saree rolling</strong> services with a focus on quality and customer convenience.<br><br>Our goal is to provide smart, hygienic, and hassle-free laundry solutions for individuals and families in <strong>Bhimavaram</strong>. With convenient doorstep service and professional garment care, Smart Wash helps customers save time while keeping their clothes fresh, clean, and well maintained.<br><br><strong>Smart Wash — For Smart People.</strong>',
        'what_we_do' => 'We offer complete doorstep laundry pickup & delivery, professional steam ironing, delicate dry cleaning, stain treatment, shoe cleaning, and saree rolling at affordable rates.',
        'primaryImage' => 'public/startups/smart-wash/hero.png',
        'secondaryImage' => 'public/startups/smart-wash/detail.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/23.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/23.png',
        'founder' => 'Ch. Ravi Kumar',
        'address' => 'Beside Oils N Oils, Chaitanya College Road, Chinna Amiram, Bhimavaram, West Godavari, Andhra Pradesh, 534204.',
        'mapUrl' => 'https://maps.app.goo.gl/GWtk8XGzpxq8aBnT7',
        'workingHours' => 'Everyday: 7 AM - 8 PM',
        'phone' => '+91 7997034445',
        'phone2' => '+91 7997133445',
        'email' => 'bosmartwash@gmail.com',
        'instagram' => 'https://www.instagram.com/bo_smartwash?igsh=MTI0dG42MGI0dXE0NQ==',
        'services' => [
            'Laundry',
            'Dry Wash',
            'Steam iron',
            'Stain Removal',
            'Saree Rolling'
        ],
        'themeColor' => '#2563eb',
        'themeColorDark' => '#1d4ed8',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%)',
    ],
    'nutridelight' => [
        'id' => 'nutridelight',
        'name' => 'NutriDelight',
        'category' => 'Health Focused Cloud Kitchen & Fresh Juice Startup',
        'tagline' => '"Making Bhimavaram Healthy with 100% Cold-Pressed Fresh Juices & Wholesome Meals!"',
        'description' => 'NutriDelight is an innovative health-focused cloud kitchen startup founded by student entrepreneurs at SRKR Engineering College. Dedicated to culinary excellence, natural wellness, and nutritional balance, NutriDelight prepares 100% natural cold-pressed fresh juices, wholesome smoothies, and calorie-conscious meals using fresh locally-sourced ingredients.',
        'about' => "NutriDelight was established to revolutionize campus dining by offering healthy, delicious, and budget-friendly refreshments and meals. Founded by SRKREC students, our mission is to eliminate the tradeoff between good health and great taste.\n\nWe specialize in 100% raw, cold-pressed fresh juices extracted without added sugar, preservatives, or artificial flavors — preserving vital vitamins, active enzymes, and natural purity. From refreshing citrus mixes, immunity-boosting detox cleanses, and pure herbal elixirs to fresh fruit milkshakes and wholesome food bowls, NutriDelight brings raw natural goodness straight to your doorstep through hygienic preparation and fast local delivery across Bhimavaram.",
        'what_we_do' => 'We operate a modern hygienic cloud kitchen preparing 100% natural cold-pressed fresh juices, detox fruit drinks, energy smoothies, customized meal plans, fresh salads, and balanced thalis. All beverages and meals are prepared daily, chilled, packed in eco-friendly bottles & containers, and delivered fresh to hostels, offices, and homes across Bhimavaram.',
        'primaryImage' => 'public/startups/nutridelight/hero.png',
        'secondaryImage' => 'public/startups/nutridelight/details.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/26.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/26.png',
        'founder' => 'Mangineti Mohan Satya Siva Rohit Kumar',
        'phone' => '7993173229',
        'phone2' => '9010972333',
        'email' => 'rohitkumar3227@gmail.com',
        'instagram' => 'https://www.instagram.com/nutri__delight?igsh=MWdlajZxbmdjZHE2dQ==',
        'address' => 'Bhimavaram, Andhra Pradesh',
        'mapUrl' => 'https://maps.app.goo.gl/P9KTCXvn12TBFcaP9',
        'themeColor' => '#16a34a',
        'themeColorDark' => '#14532d',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #14532d 50%, #16a34a 100%)',
    ],
    'lunch-box' => [
        'id' => 'lunch-box',
        'name' => 'Lunch Box',
        'category' => 'School Lunch Delivery',
        'tagline' => '"Local Lunchbox Delivery Logistic Startup — An Initiative of Bhimavaram Online"',
        'description' => 'Lunch Box is a school lunch delivery startup focused on providing convenient and reliable lunch solutions for students.',
        'aboutTitle' => 'About Lunch Box',
        'about' => 'Lunch Box is a school lunch delivery startup focused on providing convenient and reliable lunch solutions for students. The startup delivers freshly prepared lunch boxes directly from home to school, helping parents provide nutritious and convenient meals for their children.<br><br>Lunch Box follows a monthly subscription-based model and is designed to make everyday school lunch delivery simple and dependable. The service currently delivers 200+ lunch boxes daily.',
        'primaryImage' => 'public/startups/lunch-box/lunch-box.png',
        'secondaryImage' => 'public/startups/lunch-box/detail3.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/25.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/25.png',
        'founder' => 'Sanjay K',
        'phone' => '9848823311',
        'pricing' => 'Starting from ₹499/- per month',
        'instagram' => 'https://www.instagram.com/bo_lunch_box?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==',
        'themeColor' => '#0284c7',
        'themeColorDark' => '#0369a1',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%)',
    ],
    'lunchbox' => [
        'id' => 'lunch-box',
        'name' => 'Lunch Box',
        'category' => 'School Lunch Delivery',
        'tagline' => '"Local Lunchbox Delivery Logistic Startup — An Initiative of Bhimavaram Online"',
        'description' => 'Lunch Box is a school lunch delivery startup focused on providing convenient and reliable lunch solutions for students.',
        'aboutTitle' => 'About Lunch Box',
        'about' => 'Lunch Box is a school lunch delivery startup focused on providing convenient and reliable lunch solutions for students. The startup delivers freshly prepared lunch boxes directly from home to school, helping parents provide nutritious and convenient meals for their children.<br><br>Lunch Box follows a monthly subscription-based model and is designed to make everyday school lunch delivery simple and dependable. The service currently delivers 200+ lunch boxes daily.',
        'primaryImage' => 'public/startups/lunch-box/lunch-box.png',
        'secondaryImage' => 'public/startups/lunch-box/detail3.png',
        'fallbackPrimaryImage' => 'assets/company_logos/logos/25.png',
        'fallbackSecondaryImage' => 'assets/company_logos/logos/25.png',
        'founder' => 'Sanjay K',
        'phone' => '9848823311',
        'pricing' => 'Starting from ₹499/- per month',
        'instagram' => 'https://www.instagram.com/bo_lunch_box?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==',
        'themeColor' => '#0284c7',
        'themeColorDark' => '#0369a1',
        'gradient' => 'linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%)',
    ],
    'campus-online' => [
        'id' => 'campus-online',
        'name' => 'Campus Online',
        'category' => 'Campus E-Commerce, Learning & Communication Platform',
        'eyebrow' => 'CAMPUS E-COMMERCE PLATFORM',
        'tagline' => 'Campus - E-Commerce, Fun & Learning, Communication',
        'description' => 'A campus-focused digital platform combining e-commerce, learning, fun, and communication to create a connected digital campus experience.',
        'aboutTitle' => 'About Us',
        'about' => 'Campus Online is a campus-focused digital platform designed to bring e-commerce, learning, fun, and communication together in one convenient space. It aims to create a connected digital ecosystem for the college community where students can discover useful products and services while engaging with campus-related activities and opportunities.<br><br>The platform combines the convenience of online commerce with learning and communication, helping create a more connected, interactive, and digitally enabled campus experience.',
        'primaryImage' => 'public/startups/campus-online/co.jpg',
        'secondaryImage' => 'public/startups/campus-online/detail4.png',
        'fallbackPrimaryImage' => 'public/startups/campus-online/co.jpg',
        'fallbackSecondaryImage' => 'public/startups/campus-online/detail4.png',
        'keyAreas' => ['Campus E-Commerce', 'Fun & Learning', 'Communication'],
        'services' => ['Campus E-Commerce', 'Fun & Learning', 'Communication'],
        'themeColor' => '#dc2626',
        'themeColorDark' => '#991b1b',
        'gradient' => 'linear-gradient(135deg, #991b1b 0%, #1e3a8a 50%, #2563eb 100%)',
    ],
    'bhimavaram-foods' => [
        'id' => 'bhimavaram-foods',
        'name' => 'Bhimavaram Online Foods',
        'category' => 'ONDC ENABLED E-COMMERCE PLATFORM',
        'tagline' => 'Sweets | Hots | Pickles | Spices Powders',
        'description' => 'An ONDC-enabled e-commerce platform offering authentic Bhimavaram sweets, hots, pickles, and spice powders.',
        'aboutTitle' => 'About Us',
        'about' => 'Bhimavaram Online Foods is an ONDC-enabled e-commerce platform focused on bringing the authentic taste of Bhimavaram to customers through a convenient online marketplace. The platform offers a range of traditional and locally loved food products including sweets, hots, pickles, and spice powders.<br><br>It provides customers with an easy way to discover and order authentic food products from Bhimavaram, helping local food businesses and products reach a wider audience through digital commerce.',
        'primaryImage' => 'public/startups/bhimavaram-foods/hero.png',
        'secondaryImage' => 'public/startups/bhimavaram-foods/detail4.png',
        'fallbackPrimaryImage' => 'public/startups/bhimavaram-foods/hero.png',
        'fallbackSecondaryImage' => 'public/startups/bhimavaram-foods/detail4.png',
        'website' => 'www.bhimavaram.online.com',
        'phone' => '90109 72 333',
        'products' => ['Sweets', 'Hots', 'Pickles', 'Spice Powders'],
        'platform' => 'ONDC Enabled E-Commerce Platform',
        'themeColor' => '#b45309',
        'themeColorDark' => '#451a03',
        'gradient' => 'linear-gradient(135deg, #451a03 0%, #78350f 50%, #b45309 100%)',
    ],
    'bhimavaram-online-foods' => [
        'id' => 'bhimavaram-foods',
        'name' => 'Bhimavaram Online Foods',
        'category' => 'ONDC ENABLED E-COMMERCE PLATFORM',
        'tagline' => 'Sweets | Hots | Pickles | Spices Powders',
        'description' => 'An ONDC-enabled e-commerce platform offering authentic Bhimavaram sweets, hots, pickles, and spice powders.',
        'aboutTitle' => 'About Us',
        'about' => 'Bhimavaram Online Foods is an ONDC-enabled e-commerce platform focused on bringing the authentic taste of Bhimavaram to customers through a convenient online marketplace. The platform offers a range of traditional and locally loved food products including sweets, hots, pickles, and spice powders.<br><br>It provides customers with an easy way to discover and order authentic food products from Bhimavaram, helping local food businesses and products reach a wider audience through digital commerce.',
        'primaryImage' => 'public/startups/bhimavaram-foods/hero.png',
        'secondaryImage' => 'public/startups/bhimavaram-foods/detail4.png',
        'fallbackPrimaryImage' => 'public/startups/bhimavaram-foods/hero.png',
        'fallbackSecondaryImage' => 'public/startups/bhimavaram-foods/detail4.png',
        'website' => 'www.bhimavaram.online.com',
        'phone' => '90109 72 333',
        'products' => ['Sweets', 'Hots', 'Pickles', 'Spice Powders'],
        'platform' => 'ONDC Enabled E-Commerce Platform',
        'themeColor' => '#b45309',
        'themeColorDark' => '#451a03',
        'gradient' => 'linear-gradient(135deg, #451a03 0%, #78350f 50%, #b45309 100%)',
    ],
];

// Determine startup ID from GET parameter or default to bhimavaram-online if requested
$startupId = isset($_GET['id']) ? strtolower(trim($_GET['id'])) : 'smart-wash';
if (!isset($startupsData[$startupId])) {
    $startupId = 'smart-wash';
}
$startup = $startupsData[$startupId];

// Resolve Primary Image (Hero)
$heroImage = '';
if (!empty($startup['primaryImage']) && file_exists(__DIR__ . '/' . $startup['primaryImage'])) {
    $heroImage = $startup['primaryImage'];
} elseif (file_exists(__DIR__ . '/assets/startups/' . $startupId . '/hero.png')) {
    $heroImage = 'assets/startups/' . $startupId . '/hero.png';
} else {
    $heroImage = $startup['fallbackPrimaryImage'];
}

// Resolve Secondary Image (Details)
$detailsImage = '';
if (!empty($startup['secondaryImage']) && file_exists(__DIR__ . '/' . $startup['secondaryImage'])) {
    $detailsImage = $startup['secondaryImage'];
} elseif (file_exists(__DIR__ . '/assets/startups/' . $startupId . '/details.png')) {
    $detailsImage = 'assets/startups/' . $startupId . '/details.png';
} else {
    $detailsImage = $startup['fallbackSecondaryImage'];
}
?>

<style>
body {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    background: #f8fafc;
    color: #334155;
    overflow-x: hidden;
}

/* SECTION 1 — FULL-WIDTH HERO SECTION */
.startup-hero-fullwidth {
    position: relative;
    width: 100%;
    min-height: 82vh;
    display: flex;
    align-items: flex-end;
    justify-content: flex-start;
    padding: 80px 0 65px;
    background: #ffffff;
    overflow: hidden;
}

/* Hero Background Image (Fills Entire Hero) */
.hero-bg-visual {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
}

.hero-full-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.06));
    animation: heroFullEntrance 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    transition: transform 0.6s ease;
}

.startup-hero-fullwidth:hover .hero-full-img {
    transform: scale(1.02);
}

/* Gradient Overlay for Text Legibility */
.hero-overlay-gradient {
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(
        180deg,
        rgba(15, 23, 42, 0.1) 0%,
        rgba(15, 23, 42, 0.35) 45%,
        rgba(15, 23, 42, 0.88) 100%
    );
    pointer-events: none;
}

/* Text Content Overlay */
.hero-content-overlay {
    position: relative;
    z-index: 3;
    width: 100%;
    max-width: 1240px;
    margin: 0 auto;
    padding-left: 15px;
    padding-right: 15px;
}

.hero-text-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 50px;
    background: <?= $startup['themeColor']; ?>;
    color: #ffffff;
    font-weight: 800;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    margin-bottom: 16px;
    backdrop-filter: blur(10px);
}

.hero-title-headline {
    font-family: 'Outfit', sans-serif;
    font-size: 4rem;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.1;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
}

.hero-subtitle-tagline {
    font-size: 1.45rem;
    font-weight: 700;
    color: #93c5fd;
    max-width: 780px;
    margin-bottom: 28px;
    line-height: 1.4;
    text-shadow: 0 3px 15px rgba(0, 0, 0, 0.7);
}

.btn-hero-back-pill {
    background: #ffffff;
    color: #0f172a;
    font-weight: 800;
    padding: 14px 32px;
    border-radius: 50px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    font-size: 1rem;
}

.btn-hero-back-pill:hover {
    background: #ffffff;
    color: <?= $startup['themeColor']; ?>;
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.45);
}

@keyframes heroFullEntrance {
    0% {
        opacity: 0;
        transform: scale(0.96);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* SECTION 2 — DETAILS CONTAINER */
.details-section-wrapper {
    padding: 80px 0;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
}

.details-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 36px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
}

.details-title-lg {
    font-family: 'Outfit', sans-serif;
    font-size: 2.2rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 18px;
}

.details-text-p {
    font-size: 1.08rem;
    line-height: 1.85;
    color: #475569;
    margin-bottom: 24px;
}

.detail-row-item {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.detail-row-item:hover {
    transform: translateY(-4px);
    background: #ffffff !important;
    border-color: <?= $startup['themeColor']; ?> !important;
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.12) !important;
}

/* Secondary Image Card (Image 2 - Details Poster Image) */
.details-image-card {
    background: #ffffff;
    border-radius: 28px;
    padding: 24px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
    border: 2px solid #e2e8f0;
    text-align: center;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.details-image-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 60px rgba(37, 99, 235, 0.15);
    border-color: <?= $startup['themeColor']; ?>;
}

.details-image-card img {
    width: 100%;
    max-height: 480px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
    border-radius: 16px;
}

.details-image-caption {
    margin-top: 18px;
    font-size: 0.95rem;
    font-weight: 700;
    color: <?= $startup['themeColor']; ?>;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 991px) {
    .startup-hero-fullwidth {
        min-height: 65vh;
        padding: 60px 0 45px;
    }
    .hero-title-headline {
        font-size: 2.7rem;
    }
    .hero-subtitle-tagline {
        font-size: 1.15rem;
    }
    .details-image-card {
        margin-top: 40px;
    }
}

/* ==================================================
   NUTRIDELIGHT 3-ROW CONTINUOUS SCROLLING GALLERY
   ================================================== */
.nutridelight-gallery-section {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 50%, #f8fafc 100%);
    border-radius: 36px;
    padding: 60px 0;
    position: relative;
    overflow: hidden;
    margin-top: 50px;
    box-shadow: inset 0 0 100px rgba(22, 163, 74, 0.03);
}

.nd-marquee-container {
    display: flex;
    flex-direction: column;
    gap: 22px;
    width: 100%;
    position: relative;
    overflow: hidden;
    padding: 10px 0;
}

.nd-marquee-row {
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    width: 100%;
}

.nd-marquee-track {
    display: inline-flex;
    gap: 20px;
    width: max-content;
}

.nd-track-left {
    animation: ndMarqueeLeft 35s linear infinite;
}

.nd-track-right {
    animation: ndMarqueeRight 40s linear infinite;
}

.nd-track-left-fast {
    animation: ndMarqueeLeft 28s linear infinite;
}

.nd-marquee-row:hover .nd-marquee-track {
    animation-play-state: paused;
}

@keyframes ndMarqueeLeft {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes ndMarqueeRight {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}

/* Card Styling in Ticker */
.nd-ticker-card {
    width: 320px;
    height: 220px;
    flex-shrink: 0;
    border-radius: 24px;
    overflow: hidden;
    position: relative;
    background: #ffffff;
    border: 3.5px solid #ffffff;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: inline-block;
    vertical-align: top;
}

.nd-ticker-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

.nd-ticker-card:hover {
    transform: scale(1.06) translateY(-6px);
    border-color: #16a34a;
    box-shadow: 0 25px 45px rgba(22, 163, 74, 0.28);
    z-index: 10;
}

.nd-ticker-card:hover img {
    transform: scale(1.1);
}

.nd-ticker-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 35%, rgba(15, 23, 42, 0.88) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nd-ticker-card:hover .nd-ticker-overlay {
    opacity: 1;
}

.nd-ticker-badge {
    align-self: flex-end;
    background: #ffffff;
    color: #16a34a;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 5px 14px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.nd-ticker-caption h6 {
    color: #ffffff;
    font-weight: 800;
    font-size: 1rem;
    margin-bottom: 2px;
    white-space: normal;
}

.nd-ticker-caption p {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.8rem;
    margin: 0;
    white-space: normal;
}

/* Lightbox Modal Styles */
.nd-lightbox-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.nd-lightbox-modal.active {
    display: flex;
}

.nd-lightbox-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.92);
    backdrop-filter: blur(12px);
    animation: fadeIn 0.3s ease;
}

.nd-lightbox-container {
    position: relative;
    z-index: 10000;
    max-width: 1000px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.nd-lightbox-content {
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    padding: 16px;
    width: 100%;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5);
}

.nd-lightbox-image-wrapper {
    width: 100%;
    max-height: 72vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 16px;
    background: #020617;
}

.nd-lightbox-image-wrapper img {
    max-width: 100%;
    max-height: 72vh;
    object-fit: contain;
    border-radius: 12px;
}

.nd-lightbox-meta {
    margin-top: 14px;
    padding: 4px 10px;
}

.nd-lightbox-close-btn,
.nd-lightbox-nav-btn {
    position: absolute;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.25s ease;
    backdrop-filter: blur(8px);
}

.nd-lightbox-close-btn {
    top: -60px;
    right: 0;
}

.nd-lightbox-nav-btn.prev {
    left: -70px;
    top: 50%;
    transform: translateY(-50%);
}

.nd-lightbox-nav-btn.next {
    right: -70px;
    top: 50%;
    transform: translateY(-50%);
}

.nd-lightbox-close-btn:hover,
.nd-lightbox-nav-btn:hover {
    background: #16a34a;
    border-color: #16a34a;
    transform: scale(1.1);
}

.nd-lightbox-nav-btn.prev:hover {
    transform: translateY(-50%) scale(1.1);
}
.nd-lightbox-nav-btn.next:hover {
    transform: translateY(-50%) scale(1.1);
}

@media (max-width: 768px) {
    .nd-ticker-card {
        width: 250px;
        height: 180px;
    }
    .nd-lightbox-nav-btn.prev {
        left: 10px;
        top: 40%;
    }
    .nd-lightbox-nav-btn.next {
        right: 10px;
        top: 40%;
    }
    .nd-lightbox-close-btn {
        top: -50px;
        right: 10px;
    }
}
/* ==================================================
   SMART WASH 3-ROW CONTINUOUS SCROLLING GALLERY
   ================================================== */
.smartwash-gallery-section {
    background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 50%, #f8fafc 100%);
    border-radius: 36px;
    padding: 60px 0;
    position: relative;
    overflow: hidden;
    margin-top: 50px;
    box-shadow: inset 0 0 100px rgba(37, 99, 235, 0.03);
}

.sw-marquee-container {
    display: flex;
    flex-direction: column;
    gap: 22px;
    width: 100%;
    position: relative;
    overflow: hidden;
    padding: 10px 0;
}

.sw-marquee-row {
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    width: 100%;
}

.sw-marquee-track {
    display: inline-flex;
    gap: 20px;
    width: max-content;
}

.sw-track-left {
    animation: swMarqueeLeft 35s linear infinite;
}

.sw-track-right {
    animation: swMarqueeRight 40s linear infinite;
}

.sw-track-left-fast {
    animation: swMarqueeLeft 28s linear infinite;
}

.sw-marquee-row:hover .sw-marquee-track {
    animation-play-state: paused;
}

@keyframes swMarqueeLeft {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes swMarqueeRight {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}

/* Card Styling in Ticker */
.sw-ticker-card {
    width: 320px;
    height: 220px;
    flex-shrink: 0;
    border-radius: 24px;
    overflow: hidden;
    position: relative;
    background: #ffffff;
    border: 3.5px solid #ffffff;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: inline-block;
    vertical-align: top;
}

.sw-ticker-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

.sw-ticker-card:hover {
    transform: scale(1.06) translateY(-6px);
    border-color: #2563eb;
    box-shadow: 0 25px 45px rgba(37, 99, 235, 0.28);
    z-index: 10;
}

.sw-ticker-card:hover img {
    transform: scale(1.1);
}

.sw-ticker-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 35%, rgba(15, 23, 42, 0.88) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sw-ticker-card:hover .sw-ticker-overlay {
    opacity: 1;
}

.sw-ticker-badge {
    align-self: flex-end;
    background: #ffffff;
    color: #2563eb;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 5px 14px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sw-ticker-caption h6 {
    color: #ffffff;
    font-weight: 800;
    font-size: 1rem;
    margin-bottom: 2px;
    white-space: normal;
}

.sw-ticker-caption p {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.8rem;
    margin: 0;
    white-space: normal;
}

/* Lightbox Modal Styles */
.sw-lightbox-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.sw-lightbox-modal.active {
    display: flex;
}

.sw-lightbox-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.92);
    backdrop-filter: blur(12px);
    animation: fadeIn 0.3s ease;
}

.sw-lightbox-container {
    position: relative;
    z-index: 10000;
    max-width: 1000px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.sw-lightbox-content {
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    padding: 16px;
    width: 100%;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5);
}

.sw-lightbox-image-wrapper {
    width: 100%;
    max-height: 72vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 16px;
    background: #020617;
}

.sw-lightbox-image-wrapper img {
    max-width: 100%;
    max-height: 72vh;
    object-fit: contain;
    border-radius: 12px;
}

.sw-lightbox-meta {
    margin-top: 14px;
    padding: 4px 10px;
}

.sw-lightbox-close-btn,
.sw-lightbox-nav-btn {
    position: absolute;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.25s ease;
    backdrop-filter: blur(8px);
}

.sw-lightbox-close-btn {
    top: -60px;
    right: 0;
}

.sw-lightbox-nav-btn.prev {
    left: -70px;
    top: 50%;
    transform: translateY(-50%);
}

.sw-lightbox-nav-btn.next {
    right: -70px;
    top: 50%;
    transform: translateY(-50%);
}

.sw-lightbox-close-btn:hover,
.sw-lightbox-nav-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
    transform: scale(1.1);
}

.sw-lightbox-nav-btn.prev:hover {
    transform: translateY(-50%) scale(1.1);
}
.sw-lightbox-nav-btn.next:hover {
    transform: translateY(-50%) scale(1.1);
}

@media (max-width: 768px) {
    .sw-ticker-card {
        width: 250px;
        height: 180px;
    }
    .sw-lightbox-nav-btn.prev {
        left: 10px;
        top: 40%;
    }
    .sw-lightbox-nav-btn.next {
        right: 10px;
        top: 40%;
    }
    .sw-lightbox-close-btn {
        top: -50px;
        right: 10px;
    }
}
</style>

<body>
    <?php include "nav.php"; ?>

    <!-- SECTION 1 — FULL-WIDTH HERO SECTION -->
    <section class="startup-hero-fullwidth">
        <!-- Main Full Hero Background Image (Image 1) -->
        <div class="hero-bg-visual">
            <img src="<?= htmlspecialchars($heroImage); ?>" alt="<?= htmlspecialchars($startup['name']); ?> Hero Image" class="hero-full-img">
            <div class="hero-overlay-gradient"></div>
        </div>

        <!-- Text Overlay -->
        <div class="hero-content-overlay">
            <div class="hero-text-badge">
                <i class="fas fa-sparkles"></i> <?= !empty($startup['eyebrow']) ? htmlspecialchars($startup['eyebrow']) : htmlspecialchars($startup['category']); ?>
            </div>
            <h1 class="hero-title-headline"><?= htmlspecialchars($startup['name']); ?></h1>
            <p class="hero-subtitle-tagline"><?= htmlspecialchars($startup['tagline']); ?></p>

            <div class="hero-actions-bar">
                <a href="startup_club.php" class="btn-hero-back-pill">
                    <i class="fas fa-arrow-left"></i> Back to Startups
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION 2 — ABOUT US + SECOND IMAGE SECTION & CONTACT & LOCATION DETAILS GRID -->
    <section class="details-section-wrapper">
        <div class="container">
            <!-- SECTION 2 — ABOUT US & DETAIL IMAGE (2 COLUMNS ON DESKTOP) -->
            <div class="row g-5 align-items-stretch mb-5">
                <!-- LEFT COLUMN: Heading "About Us" & Description -->
                <div class="col-lg-7">
                    <div class="details-card h-100">
                        <h2 class="details-title-lg mb-4"><?= !empty($startup['aboutTitle']) ? htmlspecialchars($startup['aboutTitle']) : 'About Us'; ?></h2>
                        <?php if (!empty($startup['about'])): ?>
                            <div class="details-text-p" style="font-size: 1.05rem; line-height: 1.85; color: #475569;">
                                <?= $startup['about']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Supporting Detail Image -->
                <div class="col-lg-5">
                    <div class="details-image-card h-100">
                        <img src="<?= htmlspecialchars($detailsImage); ?>" alt="<?= htmlspecialchars($startup['name']); ?> Details Poster Image">
                        <div class="details-image-caption">
                            <i class="fas fa-certificate me-1"></i> Official <?= htmlspecialchars($startup['name']); ?> Details Poster
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3 — DETAILS GRID -->
            <?php if (!empty($startup['founder']) || !empty($startup['phone']) || !empty($startup['email']) || !empty($startup['instagram']) || !empty($startup['address']) || !empty($startup['services']) || !empty($startup['keyAreas']) || !empty($startup['website']) || $startupId === 'bhimavaram-online' || $startupId === 'bhimavaramonline'): ?>
                <div class="mt-5 pt-3">
                    <h2 class="details-title-lg mb-4"><?= ($startupId === 'bhimavaram-digitals' || $startupId === 'bhimavaram-digital') ? 'Contact & Location Details' : 'Details' ?></h2>
                    
                    <div class="row g-4">
                        <!-- Category Card -->
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                <div class="p-3 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                    <i class="fas fa-tags fs-4"></i>
                                </div>
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Category</span>
                                    <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($startup['category']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Pricing Card if available -->
                        <?php if (!empty($startup['pricing'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-tag fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Subscription Pricing</span>
                                        <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($startup['pricing']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Founder Card if available -->
                        <?php if (!empty($startup['founder'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-user fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Founder</span>
                                        <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($startup['founder']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Services Card if available -->
                        <?php if (!empty($startup['services'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mt-1" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-shopping-bag fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Services</span>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <?php foreach ($startup['services'] as $svc): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;"><?= htmlspecialchars($svc); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Products Card if available -->
                        <?php if (!empty($startup['products'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center mt-1" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-utensils fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Products</span>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <?php foreach ($startup['products'] as $prod): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">• <?= htmlspecialchars($prod); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Platform Card if available -->
                        <?php if (!empty($startup['platform'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-network-wired fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Platform</span>
                                        <span class="fw-bold text-dark fs-6 d-block"><?= htmlspecialchars($startup['platform']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Key Areas Card if available -->
                        <?php if (!empty($startup['keyAreas'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mt-1" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-layer-group fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Key Areas</span>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <?php foreach ($startup['keyAreas'] as $ka): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">• <?= htmlspecialchars($ka); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Android App / Google Play Store Card -->
                        <?php if (!empty($startup['appUrl'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fab fa-google-play fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Android Mobile App</span>
                                        <a href="<?= htmlspecialchars($startup['appUrl']); ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-success text-decoration-none fs-6 d-block mb-1">
                                            Get Bhimavaram Online App on Play Store <i class="fas fa-external-link-alt ms-1 small" style="font-size: 0.75rem;"></i>
                                        </a>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">ONDC-Enabled Hyperlocal App</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Address / Location Card -->
                        <?php if (!empty($startup['address'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mt-1" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-map-marker-alt fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Location</span>
                                        <a href="<?= !empty($startup['mapUrl']) ? htmlspecialchars($startup['mapUrl']) : 'https://maps.google.com/?q=' . urlencode($startup['address']); ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-dark text-decoration-none fs-6 d-block mb-1">
                                            <?= nl2br(htmlspecialchars($startup['address'])); ?> <i class="fas fa-external-link-alt ms-1 text-danger small" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Working Hours if available -->
                        <?php if (!empty($startup['workingHours'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-clock fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Working Hours</span>
                                        <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($startup['workingHours']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Phone Card -->
                        <?php if (!empty($startup['phone'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-phone-alt fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Phone</span>
                                        <span class="fw-bold fs-6">
                                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $startup['phone']); ?>" class="text-decoration-none text-dark hover-blue"><?= htmlspecialchars($startup['phone']); ?></a>
                                            <?php if (!empty($startup['phone2'])): ?>
                                                / <a href="tel:<?= preg_replace('/[^0-9+]/', '', $startup['phone2']); ?>" class="text-decoration-none text-dark hover-blue"><?= htmlspecialchars($startup['phone2']); ?></a>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Email Card -->
                        <?php if (!empty($startup['email'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-envelope fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Email</span>
                                        <a href="mailto:<?= htmlspecialchars($startup['email']); ?>" class="fw-bold text-primary text-decoration-none fs-6"><?= htmlspecialchars($startup['email']); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Official Website Card -->
                        <?php if (!empty($startup['website'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-globe fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Website</span>
                                        <a href="<?= (strpos($startup['website'], 'http') === 0) ? htmlspecialchars($startup['website']) : 'https://' . htmlspecialchars($startup['website']); ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-primary text-decoration-none fs-6">
                                            <?= htmlspecialchars($startup['website']); ?> <i class="fas fa-external-link-alt ms-1 small" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Instagram Card -->
                        <?php if (!empty($startup['instagram'])): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fab fa-instagram fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;"><?= ($startupId === 'bhimavaram-digitals' || $startupId === 'bhimavaram-digital') ? 'Official Website' : 'Instagram' ?></span>
                                        <a href="<?= htmlspecialchars($startup['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-danger text-decoration-none fs-6">
                                            <?php if (strpos($startup['instagram'], 'bo_lunch_box') !== false): ?>
                                                Visit Lunch Box on Instagram →
                                            <?php elseif (strpos($startup['instagram'], 'bhimavaram_online') !== false): ?>
                                                Visit Bhimavaram Online on Instagram →
                                            <?php elseif (strpos($startup['instagram'], 'bhimavaramdigitals') !== false): ?>
                                                Visit Bhimavaram Digitals on Instagram →
                                            <?php elseif (strpos($startup['instagram'], 'bo_smartwash') !== false): ?>
                                                @bo_smartwash <i class="fas fa-external-link-alt ms-1 small" style="font-size: 0.75rem;"></i>
                                            <?php else: ?>
                                                @nutri__delight <i class="fas fa-external-link-alt ms-1 small" style="font-size: 0.75rem;"></i>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Google Maps Location Card (for startups that do not have map link inside address card) -->
                        <?php if (!empty($startup['mapUrl']) && $startupId !== 'bhimavaram-digitals' && $startupId !== 'bhimavaram-digital' && $startupId !== 'bhimavaram-online' && $startupId !== 'bhimavaramonline'): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-center gap-3 detail-row-item h-100">
                                    <div class="p-3 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                                        <i class="fas fa-compass fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Location</span>
                                        <a href="<?= htmlspecialchars($startup['mapUrl']); ?>" target="_blank" rel="noopener noreferrer" class="fw-bold text-primary text-decoration-none fs-6">
                                            View Location on Google Maps →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($startupId === 'bhimavaram-online' || $startupId === 'bhimavaramonline'): ?>
            <!-- ==================================================
                 BHIMAVARAM ONLINE - APP SHOWCASE SECTION
                 ================================================== -->
            <section class="bo-app-showcase-section py-5 my-4 position-relative overflow-hidden" id="app-showcase">
                <style>
                    .bo-app-showcase-section {
                        background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 50%, #f1f5f9 100%);
                        border-radius: 30px;
                        border: 1px solid #e2e8f0;
                    }
                    .bo-app-card {
                        background: #ffffff;
                        border-radius: 28px;
                        border: 1px solid #e2e8f0;
                        padding: 12px;
                        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
                        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                        position: relative;
                    }
                    .bo-app-card:hover {
                        transform: translateY(-12px) scale(1.02);
                        box-shadow: 0 25px 50px rgba(37, 99, 235, 0.2);
                        border-color: #2563eb;
                    }
                    .bo-phone-frame {
                        border-radius: 22px;
                        overflow: hidden;
                        border: 4px solid #1e293b;
                        background: #000;
                        box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
                    }
                    .bo-phone-frame img {
                        width: 100%;
                        height: 380px;
                        object-fit: cover;
                        object-position: top;
                        display: block;
                        transition: transform 0.5s ease;
                    }
                    .bo-app-card:hover .bo-phone-frame img {
                        transform: scale(1.04);
                    }
                    .bo-marquee-track {
                        display: flex;
                        gap: 24px;
                        animation: boMarquee 35s linear infinite;
                    }
                    .bo-marquee-track:hover {
                        animation-play-state: paused;
                    }
                    @keyframes boMarquee {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(calc(-100% / 2)); }
                    }
                </style>

                <!-- SECTION HEADER -->
                <div class="text-center max-w-3xl mx-auto mb-5 px-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-4 py-2 rounded-pill fw-bold text-uppercase tracking-wider shadow-sm mb-3" style="letter-spacing: 1.5px; font-size: 0.85rem;">
                        <i class="fas fa-mobile-alt me-2"></i> MOBILE EXPERIENCE
                    </span>
                    <h2 class="display-5 fw-extrabold text-dark mb-2 font-outfit" style="font-weight: 800;">
                        App Showcase
                    </h2>
                    <p class="lead text-muted mx-auto mb-4" style="max-width: 680px; font-size: 1.1rem; line-height: 1.7;">
                        Explore the sleek interface and real-world features of the Bhimavaram Online Android App — connecting local stores, food, groceries & taxi bookings in one place.
                    </p>
                    <a href="https://play.google.com/store/apps/details?id=com.bhimavaramonline.androidapp" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg rounded-pill px-4 py-2.5 shadow-md fw-bold" style="font-size: 0.95rem;">
                        <i class="fab fa-google-play me-2"></i> Download on Play Store
                    </a>
                </div>

                <!-- SIDE BY SIDE ANIMATED APP SHOWCASE MARQUEE -->
                <div class="overflow-hidden py-3">
                    <div class="bo-marquee-track">
                        <?php 
                        $showcaseScreens = [
                            [
                                'img' => 'public/startups/bhimavaram-online/showcase5.jpg',
                                'title' => 'BO Specials & Services',
                                'desc' => 'Home repairs, news & specials'
                            ],
                            [
                                'img' => 'public/startups/bhimavaram-online/showcase4.jpg',
                                'title' => 'BO Premium Stores',
                                'desc' => 'Top local brands & super discounts'
                            ],
                            [
                                'img' => 'public/startups/bhimavaram-online/showcase3.jpg',
                                'title' => 'All Categories',
                                'desc' => 'Groceries, vegetables, meat & fruits'
                            ],
                            [
                                'img' => 'public/startups/bhimavaram-online/showcase2.jpg',
                                'title' => 'Store & Electronics',
                                'desc' => 'Mobiles, scooters & top products'
                            ],
                            [
                                'img' => 'public/startups/bhimavaram-online/showcase1.jpg',
                                'title' => 'Book Taxi / Auto Online',
                                'desc' => 'Instant local ride & auto bookings'
                            ]
                        ];
                        // Duplicate screens array for seamless infinite marquee loop
                        $allShowcase = array_merge($showcaseScreens, $showcaseScreens);
                        foreach ($allShowcase as $index => $screen):
                        ?>
                            <div class="bo-app-card" style="width: 270px; flex-shrink: 0;">
                                <div class="bo-phone-frame position-relative">
                                    <img src="<?= htmlspecialchars($screen['img']) ?>" alt="<?= htmlspecialchars($screen['title']) ?>" loading="lazy">
                                    <div class="position-absolute top-0 start-0 w-100 p-2 d-flex justify-content-between align-items-center bg-dark bg-opacity-50 text-white" style="font-size: 0.72rem; backdrop-filter: blur(4px);">
                                        <span><i class="fas fa-signal me-1"></i> 5G</span>
                                        <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">LIVE</span>
                                    </div>
                                </div>
                                <div class="p-3 text-center">
                                    <h6 class="fw-extrabold text-dark mb-1 font-outfit" style="font-size: 1rem;"><?= htmlspecialchars($screen['title']) ?></h6>
                                    <p class="text-muted small mb-0" style="font-size: 0.82rem; line-height: 1.4;"><?= htmlspecialchars($screen['desc']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($startupId === 'bhimavaram-digitals' || $startupId === 'bhimavaram-digital'): ?>
            <!-- ==================================================
                 BHIMAVARAM DIGITALS - PROMOTIONAL MEDIA SECTION
                 ================================================== -->
            <section class="bd-promo-media-section py-5 my-4 position-relative overflow-hidden" id="promotional-media">
                <style>
                    .bd-promo-media-section {
                        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
                        border-radius: 30px;
                        color: #ffffff;
                        padding: 40px 24px;
                    }
                    .bd-video-card {
                        background: rgba(255, 255, 255, 0.05);
                        backdrop-filter: blur(12px);
                        border: 1px solid rgba(255, 255, 255, 0.12);
                        border-radius: 24px;
                        overflow: hidden;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
                        transition: all 0.4s ease;
                    }
                    .bd-video-card:hover {
                        border-color: rgba(59, 130, 246, 0.5);
                        box-shadow: 0 30px 60px rgba(59, 130, 246, 0.25);
                    }
                    .bd-video-player {
                        width: 100%;
                        max-height: 480px;
                        border-radius: 18px;
                        background: #000;
                        display: block;
                    }
                    .bd-photo-card {
                        background: rgba(255, 255, 255, 0.06);
                        border-radius: 20px;
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        overflow: hidden;
                        transition: all 0.4s ease;
                        height: 100%;
                    }
                    .bd-photo-card:hover {
                        transform: translateY(-8px);
                        border-color: #3b82f6;
                        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3);
                    }
                    .bd-photo-img {
                        width: 100%;
                        height: 240px;
                        object-fit: cover;
                        display: block;
                        transition: transform 0.5s ease;
                    }
                    .bd-photo-card:hover .bd-photo-img {
                        transform: scale(1.05);
                    }
                </style>

                <!-- SECTION HEADER -->
                <div class="text-center max-w-3xl mx-auto mb-5 px-3">
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-4 py-2 rounded-pill fw-bold text-uppercase tracking-wider shadow-sm mb-3" style="letter-spacing: 1.5px; font-size: 0.85rem;">
                        <i class="fas fa-video me-2"></i> DIGITAL OUTDOOR MEDIA
                    </span>
                    <h2 class="display-5 fw-extrabold text-white mb-2 font-outfit" style="font-weight: 800;">
                        Promotional Media
                    </h2>
                    <p class="lead text-slate-300 mx-auto mb-0" style="max-width: 680px; font-size: 1.1rem; line-height: 1.7; color: #cbd5e1;">
                        Watch our high-resolution outdoor LED billboard displays in action across prime junctions in Bhimavaram, providing maximum visibility for businesses and advertisers.
                    </p>
                </div>

                <!-- MAIN PROMOTIONAL VIDEO -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10">
                        <div class="bd-video-card p-3 p-md-4 text-center">
                            <video class="bd-video-player" controls autoplay muted loop playsinline poster="public/startups/bhimavaram-digitals/promo1.jpg">
                                <source src="public/startups/bhimavaram-digitals/promo.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="mt-3 text-start px-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold text-white mb-1 font-outfit">Bhimavaram Digitals Outdoor Advertising Reel</h5>
                                    <p class="text-slate-400 small mb-0" style="color: #94a3b8;"><i class="fas fa-map-marker-alt text-primary me-1"></i> Live Outdoor LED Display Network — Bhimavaram</p>
                                </div>
                                <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.8rem;"><i class="fas fa-play-circle me-1"></i> HD PROMO VIDEO</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BILLBOARD PHOTO GALLERY GRID -->
                <div class="row g-4">
                    <?php 
                    $bdPromos = [
                        [
                            'img' => 'public/startups/bhimavaram-digitals/promo1.jpg',
                            'title' => 'Prime Junction Billboard',
                            'desc' => 'High-visibility LED screen at major traffic intersection'
                        ],
                        [
                            'img' => 'public/startups/bhimavaram-digitals/promo2.jpg',
                            'title' => 'Standalone High-Brightness Display',
                            'desc' => 'Custom digital billboard located near key commercial hubs'
                        ],
                        [
                            'img' => 'public/startups/bhimavaram-digitals/promo3.jpg',
                            'title' => 'Commercial Complex Screen',
                            'desc' => 'Frontage digital display for brand campaigns & ads'
                        ],
                        [
                            'img' => 'public/startups/bhimavaram-digitals/promo4.jpg',
                            'title' => 'Retail Storefront Outdoor LED',
                            'desc' => 'Storefront digital display for 24/7 promo broadcasting'
                        ]
                    ];
                    foreach ($bdPromos as $item):
                    ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="bd-photo-card">
                                <div class="position-relative overflow-hidden">
                                    <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="bd-photo-img" loading="lazy">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark bg-opacity-75 rounded-pill px-2.5 py-1 text-white border border-white border-opacity-25" style="font-size: 0.7rem; backdrop-filter: blur(4px);">LED DISPLAY</span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h6 class="fw-bold text-white mb-1 font-outfit" style="font-size: 0.95rem;"><?= htmlspecialchars($item['title']) ?></h6>
                                    <p class="text-slate-400 small mb-0" style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4;"><?= htmlspecialchars($item['desc']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($startupId === 'nutridelight'): ?>
            <!-- ==================================================
                 NUTRIDELIGHT 3-ROW CONTINUOUS SCROLLING GALLERY
                 ================================================== -->
            <section class="nutridelight-gallery-section" id="nutridelight-gallery">
                <!-- SECTION INTRODUCTION HEADER -->
                <div class="text-center max-w-3xl mx-auto mb-5 px-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-4 py-2 rounded-pill fw-bold text-uppercase tracking-wider shadow-sm mb-3" style="letter-spacing: 1.5px; font-size: 0.85rem;">
                        <i class="fas fa-leaf me-1"></i> NUTRIDELIGHT
                    </span>
                    <h2 class="display-5 fw-extrabold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
                        NutriDelight Gallery
                    </h2>
                    <h4 class="fw-bold text-success fst-italic mb-3" style="font-size: 1.35rem;">
                        "Making Bhimavaram Healthy"
                    </h4>
                    <p class="lead text-muted mx-auto mb-0" style="max-width: 650px; font-size: 1.1rem; line-height: 1.7;">
                        Explore the moments, people, products and milestones that shaped the NutriDelight journey. A story of nutrition, innovation and community.
                    </p>
                </div>

                <!-- 3-ROW CONTINUOUS SCROLLING MARQUEE -->
                <div class="nd-marquee-container">
                    <!-- ROW 1 (SLIDES LEFT) -->
                    <div class="nd-marquee-row">
                        <div class="nd-marquee-track nd-track-left">
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="NutriDelight Milk & Juices" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Natural Milk & Juices</h6>
                                        <p>Fresh bottles collection</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Inauguration Ceremony</h6>
                                        <p>Ribbon cutting event</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store & Space</h6>
                                        <p>Product shelves display</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Founding Team & Mentors</h6>
                                        <p>SRKR faculty & founders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Community Stall</h6>
                                        <p>Serving healthy drinks</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="NutriDelight Milk & Juices" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Natural Milk & Juices</h6>
                                        <p>Fresh bottles collection</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Inauguration Ceremony</h6>
                                        <p>Ribbon cutting event</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store & Space</h6>
                                        <p>Product shelves display</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Founding Team & Mentors</h6>
                                        <p>SRKR faculty & founders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Community Stall</h6>
                                        <p>Serving healthy drinks</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 (SLIDES RIGHT) -->
                    <div class="nd-marquee-row">
                        <div class="nd-marquee-track nd-track-right">
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Founding Team</h6>
                                        <p>SRKR faculty & founders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Campus Stall</h6>
                                        <p>Serving healthy drinks</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="Bottles Display" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Milk & Juices</h6>
                                        <p>Natural drink collection</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Ribbon Cutting</h6>
                                        <p>Opening ceremony</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store Interior</h6>
                                        <p>Shelves & cold booth</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Founding Team</h6>
                                        <p>SRKR faculty & founders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Campus Stall</h6>
                                        <p>Serving healthy drinks</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="Bottles Display" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Milk & Juices</h6>
                                        <p>Natural drink collection</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Ribbon Cutting</h6>
                                        <p>Opening ceremony</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store Interior</h6>
                                        <p>Shelves & cold booth</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 3 (SLIDES LEFT FAST) -->
                    <div class="nd-marquee-row">
                        <div class="nd-marquee-track nd-track-left-fast">
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store Display</h6>
                                        <p>Fresh product shelves</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Inauguration</h6>
                                        <p>Grand opening moment</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="Bottles Display" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Fresh Bottles</h6>
                                        <p>Nutritious beverages</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Community Service</h6>
                                        <p>Serving drinks on campus</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Our Team</h6>
                                        <p>Dignitaries & founders</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="nd-ticker-card" data-index="2">
                                <img src="public/startups/nutridelight/gallery/gallery3.jpg" alt="Store Space" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Store Display</h6>
                                        <p>Fresh product shelves</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="1">
                                <img src="public/startups/nutridelight/gallery/gallery2.jpg" alt="Launch Event" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Inauguration</h6>
                                        <p>Grand opening moment</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="0">
                                <img src="public/startups/nutridelight/gallery/gallery1.jpg" alt="Bottles Display" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Fresh Bottles</h6>
                                        <p>Nutritious beverages</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="4">
                                <img src="public/startups/nutridelight/gallery/gallery5.jpg" alt="Campus Stall" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Community Service</h6>
                                        <p>Serving drinks on campus</p>
                                    </div>
                                </div>
                            </div>
                            <div class="nd-ticker-card" data-index="3">
                                <img src="public/startups/nutridelight/gallery/gallery4.jpg" alt="Our Team" loading="lazy">
                                <div class="nd-ticker-overlay">
                                    <span class="nd-ticker-badge">View ↗</span>
                                    <div class="nd-ticker-caption">
                                        <h6>Our Team</h6>
                                        <p>Dignitaries & founders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FULLSCREEN LIGHTBOX MODAL -->
            <div id="ndLightboxModal" class="nd-lightbox-modal">
                <div class="nd-lightbox-backdrop"></div>
                <div class="nd-lightbox-container">
                    <button id="ndLightboxClose" class="nd-lightbox-close-btn" aria-label="Close Lightbox">
                        <i class="fas fa-times"></i>
                    </button>
                    <button id="ndLightboxPrev" class="nd-lightbox-nav-btn prev" aria-label="Previous Image">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="ndLightboxNext" class="nd-lightbox-nav-btn next" aria-label="Next Image">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="nd-lightbox-content">
                        <div class="nd-lightbox-image-wrapper">
                            <img id="ndLightboxImage" src="" alt="Full Resolution NutriDelight Photo">
                        </div>
                        <div class="nd-lightbox-meta d-flex justify-content-between align-items-center">
                            <div>
                                <h4 id="ndLightboxTitle" class="mb-1 text-white fw-bold"></h4>
                                <p id="ndLightboxDesc" class="mb-0 text-white-50 small"></p>
                            </div>
                            <div class="nd-lightbox-counter badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill">
                                <span id="ndLightboxCurrent">1</span> / <span id="ndLightboxTotal">5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const galleryItems = [
                    {
                        src: 'public/startups/nutridelight/gallery/gallery1.jpg',
                        title: 'NutriDelight Natural Milk & Juices',
                        desc: 'Signature fresh milk and healthy juice bottles displayed at cold storage booth.'
                    },
                    {
                        src: 'public/startups/nutridelight/gallery/gallery2.jpg',
                        title: 'Ribbon Cutting Inauguration Ceremony',
                        desc: 'Official store opening ceremony with founders, dignitaries and guests.'
                    },
                    {
                        src: 'public/startups/nutridelight/gallery/gallery3.jpg',
                        title: 'NutriDelight Store & Product Display',
                        desc: 'Clean, modern booth layout featuring healthy dry fruits, spices, and cold juices.'
                    },
                    {
                        src: 'public/startups/nutridelight/gallery/gallery4.jpg',
                        title: 'Founding Team & Mentors',
                        desc: 'SRKR Engineering College faculty and startup founders during launch event.'
                    },
                    {
                        src: 'public/startups/nutridelight/gallery/gallery5.jpg',
                        title: 'Outdoor Campus Community Stall',
                        desc: 'Serving healthy beverages and snacks directly to students and visitors.'
                    }
                ];

                const tickerCards = document.querySelectorAll('.nd-ticker-card');
                const modal = document.getElementById('ndLightboxModal');
                const backdrop = document.querySelector('.nd-lightbox-backdrop');
                const closeBtn = document.getElementById('ndLightboxClose');
                const prevBtn = document.getElementById('ndLightboxPrev');
                const nextBtn = document.getElementById('ndLightboxNext');

                const modalImg = document.getElementById('ndLightboxImage');
                const modalTitle = document.getElementById('ndLightboxTitle');
                const modalDesc = document.getElementById('ndLightboxDesc');
                const modalCurrent = document.getElementById('ndLightboxCurrent');
                const modalTotal = document.getElementById('ndLightboxTotal');

                let currentIndex = 0;

                function openLightbox(index) {
                    currentIndex = index;
                    updateLightbox();
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function closeLightbox() {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }

                function updateLightbox() {
                    const item = galleryItems[currentIndex];
                    modalImg.src = item.src;
                    modalTitle.textContent = item.title;
                    modalDesc.textContent = item.desc;
                    modalCurrent.textContent = currentIndex + 1;
                    modalTotal.textContent = galleryItems.length;
                }

                function showPrev() {
                    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
                    updateLightbox();
                }

                function showNext() {
                    currentIndex = (currentIndex + 1) % galleryItems.length;
                    updateLightbox();
                }

                tickerCards.forEach((card) => {
                    card.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        openLightbox(idx);
                    });
                });

                if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
                if (backdrop) backdrop.addEventListener('click', closeLightbox);
                if (prevBtn) prevBtn.addEventListener('click', showPrev);
                if (nextBtn) nextBtn.addEventListener('click', showNext);

                document.addEventListener('keydown', function(e) {
                    if (!modal.classList.contains('active')) return;
                    if (e.key === 'Escape') closeLightbox();
                    if (e.key === 'ArrowLeft') showPrev();
                    if (e.key === 'ArrowRight') showNext();
                });
            });
            </script>
            <?php endif; ?>

            <?php if ($startupId === 'smart-wash' || $startupId === 'smartwash'): ?>
            <!-- ==================================================
                 SMART WASH 3-ROW CONTINUOUS SCROLLING GALLERY
                 ================================================== -->
            <section class="smartwash-gallery-section" id="smartwash-gallery">
                <!-- SECTION INTRODUCTION HEADER -->
                <div class="text-center max-w-3xl mx-auto mb-5 px-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-4 py-2 rounded-pill fw-bold text-uppercase tracking-wider shadow-sm mb-3" style="letter-spacing: 1.5px; font-size: 0.85rem;">
                        <i class="fas fa-tshirt me-1"></i> BO SMART WASH
                    </span>
                    <h2 class="display-5 fw-extrabold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-weight: 800;">
                        Smart Wash Gallery
                    </h2>
                    <h4 class="fw-bold text-primary fst-italic mb-3" style="font-size: 1.35rem;">
                        "For Smart People — Laundry & Fabric Care"
                    </h4>
                    <p class="lead text-muted mx-auto mb-0" style="max-width: 650px; font-size: 1.1rem; line-height: 1.7;">
                        Explore the inauguration moments, founding team, state-of-the-art fabric care setup, and campus community milestones of BO Smart Wash.
                    </p>
                </div>

                <!-- 3-ROW CONTINUOUS SCROLLING MARQUEE -->
                <div class="sw-marquee-container">
                    <!-- ROW 1 (SLIDES LEFT) -->
                    <div class="sw-marquee-row">
                        <div class="sw-marquee-track sw-track-left">
                            <div class="sw-ticker-card" data-index="0">
                                <img src="public/startups/smartwash/gallery/gallery1.jpg" alt="Smart Wash Opening" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Storefront Inauguration</h6>
                                        <p>Founders & dignitaries at grand opening</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="1">
                                <img src="public/startups/smartwash/gallery/gallery2.jpg" alt="Bouquet Presentation" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Inauguration Ceremony</h6>
                                        <p>Bouquet presentation to dignitaries</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="2">
                                <img src="public/startups/smartwash/gallery/gallery3.jpg" alt="Product Setup" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Fabric Care Setup</h6>
                                        <p>Hygienic detergent & care products</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="5">
                                <img src="public/startups/smartwash/gallery/gallery6.jpg" alt="Tumble Drier" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Tumble Drier Unit</h6>
                                        <p>IFB RTD 30 drier inauguration</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="6">
                                <img src="public/startups/smartwash/gallery/gallery7.jpg" alt="Industrial Washer" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Industrial Washer</h6>
                                        <p>Ribbon cutting ceremony</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="sw-ticker-card" data-index="0">
                                <img src="public/startups/smartwash/gallery/gallery1.jpg" alt="Smart Wash Opening" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Storefront Inauguration</h6>
                                        <p>Founders & dignitaries at grand opening</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="1">
                                <img src="public/startups/smartwash/gallery/gallery2.jpg" alt="Bouquet Presentation" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Inauguration Ceremony</h6>
                                        <p>Bouquet presentation to dignitaries</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="2">
                                <img src="public/startups/smartwash/gallery/gallery3.jpg" alt="Product Setup" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Fabric Care Setup</h6>
                                        <p>Hygienic detergent & care products</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="5">
                                <img src="public/startups/smartwash/gallery/gallery6.jpg" alt="Tumble Drier" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Tumble Drier Unit</h6>
                                        <p>IFB RTD 30 drier inauguration</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="6">
                                <img src="public/startups/smartwash/gallery/gallery7.jpg" alt="Industrial Washer" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Industrial Washer</h6>
                                        <p>Ribbon cutting ceremony</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 (SLIDES RIGHT) -->
                    <div class="sw-marquee-row">
                        <div class="sw-marquee-track sw-track-right">
                            <div class="sw-ticker-card" data-index="3">
                                <img src="public/startups/smartwash/gallery/gallery4.jpg" alt="Team Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Smart Wash Team</h6>
                                        <p>Student team & founders at store</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="4">
                                <img src="public/startups/smartwash/gallery/gallery5.jpg" alt="Campus Discussion" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Campus Interactive Session</h6>
                                        <p>Team discussion & planning meeting</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="7">
                                <img src="public/startups/smartwash/gallery/gallery8.jpg" alt="Dignitaries Interaction" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Management Walkthrough</h6>
                                        <p>Dignitaries inspecting store space</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="8">
                                <img src="public/startups/smartwash/gallery/gallery9.jpg" alt="Plant Inspection" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Garment Ironing Plant</h6>
                                        <p>Commercial steam finishing line</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="9">
                                <img src="public/startups/smartwash/gallery/gallery10.jpg" alt="Grand Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Grand Group Photo</h6>
                                        <p>Dignitaries, founders & full team</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="sw-ticker-card" data-index="3">
                                <img src="public/startups/smartwash/gallery/gallery4.jpg" alt="Team Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Smart Wash Team</h6>
                                        <p>Student team & founders at store</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="4">
                                <img src="public/startups/smartwash/gallery/gallery5.jpg" alt="Campus Discussion" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Campus Interactive Session</h6>
                                        <p>Team discussion & planning meeting</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="7">
                                <img src="public/startups/smartwash/gallery/gallery8.jpg" alt="Dignitaries Interaction" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Management Walkthrough</h6>
                                        <p>Dignitaries inspecting store space</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="8">
                                <img src="public/startups/smartwash/gallery/gallery9.jpg" alt="Plant Inspection" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Garment Ironing Plant</h6>
                                        <p>Commercial steam finishing line</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="9">
                                <img src="public/startups/smartwash/gallery/gallery10.jpg" alt="Grand Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Grand Group Photo</h6>
                                        <p>Dignitaries, founders & full team</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 3 (SLIDES LEFT FAST) -->
                    <div class="sw-marquee-row">
                        <div class="sw-marquee-track sw-track-left-fast">
                            <div class="sw-ticker-card" data-index="8">
                                <img src="public/startups/smartwash/gallery/gallery9.jpg" alt="Plant Inspection" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Garment Ironing Plant</h6>
                                        <p>Commercial steam finishing line</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="9">
                                <img src="public/startups/smartwash/gallery/gallery10.jpg" alt="Grand Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Inauguration Group</h6>
                                        <p>Entire BO Smart Wash team</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="10">
                                <img src="public/startups/smartwash/gallery/gallery11.jpg" alt="Laundry Flyers" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Doorstep Laundry Flyers</h6>
                                        <p>Smart Wash service menu cards</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="11">
                                <img src="public/startups/smartwash/gallery/gallery12.jpg" alt="Ribbon Cutting" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Official Ribbon Cutting</h6>
                                        <p>Store opening by chief guest</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="12">
                                <img src="public/startups/smartwash/gallery/gallery13.jpg" alt="Poster Launch" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Brand Poster Launch</h6>
                                        <p>Official poster unveiling ceremony</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="13">
                                <img src="public/startups/smartwash/gallery/gallery14.jpg" alt="Team at Counter" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Founders & Mentors</h6>
                                        <p>Team members inside store</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicated items for seamless loop -->
                            <div class="sw-ticker-card" data-index="8">
                                <img src="public/startups/smartwash/gallery/gallery9.jpg" alt="Plant Inspection" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Garment Ironing Plant</h6>
                                        <p>Commercial steam finishing line</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="9">
                                <img src="public/startups/smartwash/gallery/gallery10.jpg" alt="Grand Group Photo" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Inauguration Group</h6>
                                        <p>Entire BO Smart Wash team</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="10">
                                <img src="public/startups/smartwash/gallery/gallery11.jpg" alt="Laundry Flyers" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Doorstep Laundry Flyers</h6>
                                        <p>Smart Wash service menu cards</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="11">
                                <img src="public/startups/smartwash/gallery/gallery12.jpg" alt="Ribbon Cutting" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Official Ribbon Cutting</h6>
                                        <p>Store opening by chief guest</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="12">
                                <img src="public/startups/smartwash/gallery/gallery13.jpg" alt="Poster Launch" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Brand Poster Launch</h6>
                                        <p>Official poster unveiling ceremony</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sw-ticker-card" data-index="13">
                                <img src="public/startups/smartwash/gallery/gallery14.jpg" alt="Team at Counter" loading="lazy">
                                <div class="sw-ticker-overlay">
                                    <span class="sw-ticker-badge">View ↗</span>
                                    <div class="sw-ticker-caption">
                                        <h6>Founders & Mentors</h6>
                                        <p>Team members inside store</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FULLSCREEN LIGHTBOX MODAL FOR SMART WASH -->
            <div id="swLightboxModal" class="sw-lightbox-modal">
                <div class="sw-lightbox-backdrop"></div>
                <div class="sw-lightbox-container">
                    <button id="swLightboxClose" class="sw-lightbox-close-btn" aria-label="Close Lightbox">
                        <i class="fas fa-times"></i>
                    </button>
                    <button id="swLightboxPrev" class="sw-lightbox-nav-btn prev" aria-label="Previous Image">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="swLightboxNext" class="sw-lightbox-nav-btn next" aria-label="Next Image">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="sw-lightbox-content">
                        <div class="sw-lightbox-image-wrapper">
                            <img id="swLightboxImage" src="" alt="Full Resolution Smart Wash Photo">
                        </div>
                        <div class="sw-lightbox-meta d-flex justify-content-between align-items-center">
                            <div>
                                <h4 id="swLightboxTitle" class="mb-1 text-white fw-bold"></h4>
                                <p id="swLightboxDesc" class="mb-0 text-white-50 small"></p>
                            </div>
                            <div class="sw-lightbox-counter badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                                <span id="swLightboxCurrent">1</span> / <span id="swLightboxTotal">14</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const swGalleryItems = [
                    {
                        src: 'public/startups/smartwash/gallery/gallery1.jpg',
                        title: 'BO Smart Wash Storefront Inauguration',
                        desc: 'Founders, faculty, and dignitaries gathered outside the decorated Smart Wash outlet during grand opening.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery2.jpg',
                        title: 'Inauguration Bouquet Presentation',
                        desc: 'Felicitation ceremony presenting flower bouquets to dignitaries inside Smart Wash.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery3.jpg',
                        title: 'Smart Wash Fabric Care & Products Setup',
                        desc: 'Hygienic detergent supplies, garment care products, and professional laundry equipment.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery4.jpg',
                        title: 'BO Smart Wash Student Team & Founders',
                        desc: 'Large group photo of student team members and founders in front of the main BO Smart Wash store.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery5.jpg',
                        title: 'Campus Interactive Team Session',
                        desc: 'Outdoor team meeting discussing operations and student fabric care services.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery6.jpg',
                        title: 'Tumble Drier Control Unit Inauguration',
                        desc: 'Dignitary commissioning the IFB RTD 30 heavy-duty tumble drier machine.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery7.jpg',
                        title: 'Industrial Washer Ribbon-Cutting',
                        desc: 'Official ribbon cutting for the high-capacity commercial washing machine unit.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery8.jpg',
                        title: 'Management & Dignitaries Walkthrough',
                        desc: 'SRKR dignitaries and guests discussing operations inside the Smart Wash facility.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery9.jpg',
                        title: 'Flatwork Ironer & Finishing Plant Inspection',
                        desc: 'Inspection of commercial steam ironer and heavy-duty garment finishing line.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery10.jpg',
                        title: 'Grand Inaugural Group Photo',
                        desc: 'Full inaugural group photo with SRKR management, founders, and entire Smart Wash team.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery11.jpg',
                        title: 'Smart Wash Doorstep Laundry Flyers',
                        desc: 'Official promotional pamphlets and service menu cards for student laundry and dry clean services.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery12.jpg',
                        title: 'Official Ribbon Cutting Ceremony',
                        desc: 'Chief guest cutting the ribbon during the grand opening of Smart Wash store outlet.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery13.jpg',
                        title: 'BO Smart Wash Official Poster Launch',
                        desc: 'Dignitaries and student team holding the official BO Smart Wash branding poster.'
                    },
                    {
                        src: 'public/startups/smartwash/gallery/gallery14.jpg',
                        title: 'Smart Wash Team & Faculty Photo',
                        desc: 'Founders and faculty members gathered near the detergent and fabric care counter.'
                    }
                ];

                const swTickerCards = document.querySelectorAll('.sw-ticker-card');
                const swModal = document.getElementById('swLightboxModal');
                const swBackdrop = document.querySelector('.sw-lightbox-backdrop');
                const swCloseBtn = document.getElementById('swLightboxClose');
                const swPrevBtn = document.getElementById('swLightboxPrev');
                const swNextBtn = document.getElementById('swLightboxNext');

                const swModalImg = document.getElementById('swLightboxImage');
                const swModalTitle = document.getElementById('swLightboxTitle');
                const swModalDesc = document.getElementById('swLightboxDesc');
                const swModalCurrent = document.getElementById('swLightboxCurrent');
                const swModalTotal = document.getElementById('swLightboxTotal');

                let swCurrentIndex = 0;

                function openSwLightbox(index) {
                    swCurrentIndex = index;
                    updateSwLightbox();
                    swModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function closeSwLightbox() {
                    swModal.classList.remove('active');
                    document.body.style.overflow = '';
                }

                function updateSwLightbox() {
                    const item = swGalleryItems[swCurrentIndex];
                    swModalImg.src = item.src;
                    swModalTitle.textContent = item.title;
                    swModalDesc.textContent = item.desc;
                    swModalCurrent.textContent = swCurrentIndex + 1;
                    swModalTotal.textContent = swGalleryItems.length;
                }

                function showSwPrev() {
                    swCurrentIndex = (swCurrentIndex - 1 + swGalleryItems.length) % swGalleryItems.length;
                    updateSwLightbox();
                }

                function showSwNext() {
                    swCurrentIndex = (swCurrentIndex + 1) % swGalleryItems.length;
                    updateSwLightbox();
                }

                swTickerCards.forEach((card) => {
                    card.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        openSwLightbox(idx);
                    });
                });

                if (swCloseBtn) swCloseBtn.addEventListener('click', closeSwLightbox);
                if (swBackdrop) swBackdrop.addEventListener('click', closeSwLightbox);
                if (swPrevBtn) swPrevBtn.addEventListener('click', showSwPrev);
                if (swNextBtn) swNextBtn.addEventListener('click', showSwNext);

                document.addEventListener('keydown', function(e) {
                    if (!swModal.classList.contains('active')) return;
                    if (e.key === 'Escape') closeSwLightbox();
                    if (e.key === 'ArrowLeft') showSwPrev();
                    if (e.key === 'ArrowRight') showSwNext();
                });
            });
            </script>
            <?php endif; ?>

            <!-- BACK BUTTON BOTTOM -->
            <div class="mt-5 text-center text-md-start">
                <a href="startup_club.php" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-semibold">
                    <i class="fas fa-arrow-left me-2"></i> Back to All Startups
                </a>
            </div>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>
</html>
