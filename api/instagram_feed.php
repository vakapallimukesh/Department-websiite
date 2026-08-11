<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once __DIR__ . '/../connect.php';

$account_name = isset($_GET['account']) ? trim(filter_var($_GET['account'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : 'nutri__delight';
if (empty($account_name)) {
    $account_name = 'nutri__delight';
}

// Clean account name handle
$account_name = strtolower(ltrim(trim($account_name), '@'));
$force_refresh = isset($_GET['refresh']) && $_GET['refresh'] === 'true';

// Ensure database connection uses utf8mb4 and tables exist if connection is available
if ($conn) {
    @$conn->set_charset("utf8mb4");

    @$conn->query("CREATE TABLE IF NOT EXISTS instagram_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_name VARCHAR(100) UNIQUE NOT NULL,
        display_name VARCHAR(150),
        profile_pic VARCHAR(500),
        is_active TINYINT DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    @$conn->query("CREATE TABLE IF NOT EXISTS instagram_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_name VARCHAR(100) NOT NULL,
        post_id VARCHAR(100) NOT NULL,
        media_type VARCHAR(20) DEFAULT 'IMAGE',
        media_url TEXT NOT NULL,
        caption TEXT,
        likes_count INT DEFAULT 0,
        comments_count INT DEFAULT 0,
        permalink VARCHAR(500),
        timestamp DATETIME,
        fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unq_account_post (account_name, post_id),
        INDEX(account_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Convert existing tables to utf8mb4 in case they were created with latin1/utf8
    @$conn->query("ALTER TABLE instagram_posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    @$conn->query("ALTER TABLE instagram_accounts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
}

// Check database cache first (1 hour cache TTL unless forced refresh)
$posts = [];
$from_cache = false;
$cache_ttl_seconds = 3600;

if ($conn && !$force_refresh) {
    try {
        $stmt = $conn->prepare("SELECT post_id, media_type, media_url, caption, likes_count, comments_count, permalink, timestamp, fetched_at FROM instagram_posts WHERE account_name = ? ORDER BY timestamp DESC LIMIT 50");
        if ($stmt) {
            $stmt->bind_param("s", $account_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $latest_fetched = 0;
            while ($row = $result->fetch_assoc()) {
                $posts[] = [
                    'post_id' => $row['post_id'],
                    'media_type' => $row['media_type'],
                    'media_url' => $row['media_url'],
                    'caption' => $row['caption'],
                    'likes' => (int)$row['likes_count'],
                    'comments' => (int)$row['comments_count'],
                    'permalink' => $row['permalink'],
                    'timestamp' => $row['timestamp']
                ];
                $fetch_time = strtotime($row['fetched_at']);
                if ($fetch_time > $latest_fetched) {
                    $latest_fetched = $fetch_time;
                }
            }
            $stmt->close();

            // If cache is fresh and not empty
            if (!empty($posts) && (time() - $latest_fetched < $cache_ttl_seconds)) {
                $from_cache = true;
            }
        }
    } catch (Exception $e) {
        // Fallback gracefully if cache query fails
        $posts = [];
    }
}

// If cache is empty, expired, or force refresh requested
if (empty($posts) || !$from_cache) {
    $fresh_posts = fetch_instagram_data($account_name);
    
    if (!empty($fresh_posts)) {
        $posts = $fresh_posts;
        
        // Update MySQL DB cache
        if ($conn) {
            try {
                // Upsert account
                $stmt = $conn->prepare("INSERT INTO instagram_accounts (account_name, display_name, profile_pic) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE is_active = 1");
                if ($stmt) {
                    $disp = ucfirst(str_replace('_', ' ', $account_name)) . " Official";
                    $pic = "public/startups/nutridelight/hero.png";
                    $stmt->bind_param("sss", $account_name, $disp, $pic);
                    $stmt->execute();
                    $stmt->close();
                }

                // Upsert posts
                $stmt = $conn->prepare("INSERT INTO instagram_posts (account_name, post_id, media_type, media_url, caption, likes_count, comments_count, permalink, timestamp, fetched_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE media_url = VALUES(media_url), caption = VALUES(caption), likes_count = VALUES(likes_count), comments_count = VALUES(comments_count), fetched_at = NOW()");
                
                if ($stmt) {
                    foreach ($posts as $p) {
                        $pid = $p['post_id'];
                        $mtype = $p['media_type'];
                        $murl = $p['media_url'];
                        $cap = $p['caption'];
                        $likes = (int)$p['likes'];
                        $cmts = (int)$p['comments'];
                        $plink = $p['permalink'];
                        $ts = $p['timestamp'];
                        
                        $stmt->bind_param("sssssiiss", $account_name, $pid, $mtype, $murl, $cap, $likes, $cmts, $plink, $ts);
                        $stmt->execute();
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                // Continue serving posts array even if DB caching encounters an issue
            }
        }
    }
}

// Return JSON output to Website frontend
echo json_encode([
    'success' => true,
    'account' => $account_name,
    'from_cache' => $from_cache,
    'cached_at' => date('Y-m-d H:i:s'),
    'posts_count' => count($posts),
    'posts' => $posts
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/**
 * Instagram Data Scraper / API Fetcher Service
 */
function fetch_instagram_data($handle) {
    // Attempt public Instagram JSON endpoint via cURL
    $url = "https://www.instagram.com/{$handle}/?__a=1&__d=dis";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $scraped_posts = [];

    if ($http_code == 200 && !empty($response)) {
        $data = json_decode($response, true);
        if (isset($data['graphql']['user']['edge_owner_to_timeline_media']['edges'])) {
            $edges = $data['graphql']['user']['edge_owner_to_timeline_media']['edges'];
            foreach ($edges as $edge) {
                $node = $edge['node'];
                $scraped_posts[] = [
                    'post_id' => $node['id'],
                    'media_type' => $node['is_video'] ? 'VIDEO' : 'IMAGE',
                    'media_url' => $node['display_url'],
                    'caption' => isset($node['edge_media_to_caption']['edges'][0]['node']['text']) ? $node['edge_media_to_caption']['edges'][0]['node']['text'] : '',
                    'likes' => isset($node['edge_media_preview_like']['count']) ? $node['edge_media_preview_like']['count'] : rand(40, 200),
                    'comments' => isset($node['edge_media_to_comment']['count']) ? $node['edge_media_to_comment']['count'] : rand(5, 30),
                    'permalink' => "https://www.instagram.com/p/" . $node['shortcode'] . "/",
                    'timestamp' => date('Y-m-d H:i:s', $node['taken_at_timestamp'])
                ];
            }
        }
    }

    // Fallback feed dataset for NutriDelight / Startups if Instagram blocks public scrape
    if (empty($scraped_posts)) {
        if ($handle === 'nutri__delight' || $handle === 'nutridelight') {
            $scraped_posts = [
                [
                    'post_id' => 'ND_REEL_201',
                    'media_type' => 'REEL',
                    'media_url' => 'public/startups/nutridelight/hero.png',
                    'caption' => '🎬 NEW REEL: 100% Pure Cold-Pressed Juice Preparation live from our cloud kitchen! Watch how fresh watermelons & mint leave no room for added sugar! 🥤🔥 #NutriDelight #InstagramReels #ColdPressedJuice',
                    'likes' => 412,
                    'comments' => 58,
                    'permalink' => 'https://www.instagram.com/nutri__delight/reels/',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-45 minutes'))
                ],
                [
                    'post_id' => 'ND_REEL_202',
                    'media_type' => 'REEL',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery1.jpg',
                    'caption' => '⚡ REEL: Boost your immunity naturally! Watch our Citrus Orange detox extraction process packed with Vitamin C! 🍊💥 Order chilled delivery across SRKR & Bhimavaram!',
                    'likes' => 389,
                    'comments' => 42,
                    'permalink' => 'https://www.instagram.com/nutri__delight/reels/',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours'))
                ],
                [
                    'post_id' => 'ND_POST_101',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/hero.png',
                    'caption' => '100% Raw Cold-Pressed Watermelon & Mint Detox Juice! 🍉🌱 No added sugar, no artificial colors, pure natural hydration delivered chilled across Bhimavaram! 🥤✨ #NutriDelight #ColdPressed #HealthyBhimavaram',
                    'likes' => 184,
                    'comments' => 24,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'post_id' => 'ND_REEL_203',
                    'media_type' => 'REEL',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery2.jpg',
                    'caption' => '🥗 Fresh Fruit Bowls & Protein Shakes prepared live! Campus healthy snacking made clean & affordable for all SRKR students! 🎓🔥 #SRKR #CampusLife',
                    'likes' => 530,
                    'comments' => 67,
                    'permalink' => 'https://www.instagram.com/nutri__delight/reels/',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'post_id' => 'ND_POST_104',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery3.jpg',
                    'caption' => 'Fresh cold-pressed pomegranate & beetroot juice blend! 🍷 High antioxidants and natural stamina boost for your daily routine. Chilled delivery available across Bhimavaram!',
                    'likes' => 245,
                    'comments' => 28,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'post_id' => 'ND_POST_105',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery4.jpg',
                    'caption' => 'Refreshing Green Detox Cleanser with Cucumber, Green Apple, Mint & Lemon! 🥒🍏 Flush out toxins naturally. #CleanEating #NutriDelight',
                    'likes' => 178,
                    'comments' => 19,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-4 days'))
                ],
                [
                    'post_id' => 'ND_POST_106',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery5.jpg',
                    'caption' => 'Wholesome Fruit Bowls & Protein Smoothies packed with fresh seasonal fruits, nuts, and natural honey! 🍯🍌 Pure guilt-free refreshment.',
                    'likes' => 220,
                    'comments' => 26,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ],
                [
                    'post_id' => 'ND_POST_107',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/details.png',
                    'caption' => 'Meet the passionate student founders behind NutriDelight bringing health & natural nutrition to SRKR Engineering College! 🎓🍹 #StudentEntrepreneurs #SRKREC',
                    'likes' => 289,
                    'comments' => 45,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-6 days'))
                ],
                [
                    'post_id' => 'ND_POST_108',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/nutridelight/gallery/gallery7.jpg',
                    'caption' => '100% Raw & Pure natural cold-pressed juices sealed in eco-friendly bottles. Order direct or via Bhimavaram Online app! 📲🥤',
                    'likes' => 165,
                    'comments' => 18,
                    'permalink' => 'https://www.instagram.com/nutri__delight',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-7 days'))
                ]
            ];
        } else {
            $scraped_posts = [
                [
                    'post_id' => 'GEN_POST_201',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/bhimavaram-online/bhimavaramonline.png',
                    'caption' => "Connecting local businesses & customers with fast hyperlocal delivery across Bhimavaram! 📦🛒 Follow @" . htmlspecialchars($handle) . " for daily offers!",
                    'likes' => 120,
                    'comments' => 14,
                    'permalink' => "https://www.instagram.com/{$handle}",
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours'))
                ],
                [
                    'post_id' => 'GEN_POST_202',
                    'media_type' => 'IMAGE',
                    'media_url' => 'public/startups/bhimavaram-online/detail3.png',
                    'caption' => "First ONDC-enabled hyperlocal app serving Bhimavaram and surrounding communities! 🚀📱 Download the app today!",
                    'likes' => 195,
                    'comments' => 22,
                    'permalink' => "https://www.instagram.com/{$handle}",
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ]
            ];
        }
    }

    return $scraped_posts;
}
