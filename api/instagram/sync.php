<?php
/**
 * Instagram Background Synchronization Service
 * 
 * Flow:
 * Instagram GraphQL API -> Scheduled Sync -> MySQL DB (instagram_media) -> Website Gallery
 * 
 * Supports syncing any Instagram account via ?username=<handle> parameter.
 * Defaults to INSTAGRAM_USERNAME (nutri__delight) if no parameter provided.
 * 
 * Non-destructive strategy:
 * - Upserts new and existing Instagram media records (INSERT ... ON DUPLICATE KEY UPDATE).
 * - NEVER deletes cached posts when the API returns 0 results (preserves cache during outages).
 * - Only replaces old data when fresh data is successfully fetched.
 * - Extracts live CDN image URLs, video URLs, captions, and permalinks directly from Instagram.
 */

if (session_status() === PHP_SESSION_NONE) session_start();

include_once __DIR__ . '/../../config/instagram.php';
include_once __DIR__ . '/../../connect.php';

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
}

// ============================================================
//  LOGGING
// ============================================================

function write_sync_log($message) {
    $log_dir = __DIR__ . '/../../logs';
    if (!file_exists($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    $log_file = defined('INSTAGRAM_SYNC_LOG') ? INSTAGRAM_SYNC_LOG : ($log_dir . '/instagram_sync.log');
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
}

// ============================================================
//  TABLE SETUP (runs once per request)
// ============================================================

function ensure_instagram_table($conn) {
    static $table_ready = false;
    if ($table_ready) return;

    $create_table_sql = "CREATE TABLE IF NOT EXISTS instagram_media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        instagram_media_id VARCHAR(100) NOT NULL UNIQUE,
        username VARCHAR(100) NOT NULL,
        media_type VARCHAR(20) NOT NULL,
        media_url TEXT NOT NULL,
        video_url TEXT,
        thumbnail_url TEXT,
        permalink VARCHAR(500) NOT NULL,
        caption TEXT,
        published_at DATETIME NOT NULL,
        fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_username_published (username, published_at DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    @$conn->query($create_table_sql);
    @$conn->query("ALTER TABLE instagram_media CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Verify required columns exist
    $existing_cols = [];
    $cols_res = $conn->query("SHOW COLUMNS FROM instagram_media");
    if ($cols_res) {
        while ($r = $cols_res->fetch_assoc()) {
            $existing_cols[] = $r['Field'];
        }
    }
    if (!in_array('video_url', $existing_cols)) {
        @$conn->query("ALTER TABLE instagram_media ADD COLUMN video_url TEXT AFTER media_url");
    }
    if (!in_array('updated_at', $existing_cols)) {
        @$conn->query("ALTER TABLE instagram_media ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    $table_ready = true;
}

// ============================================================
//  FETCH FROM INSTAGRAM GRAPHQL API
// ============================================================

/**
 * Parse Instagram GraphQL edges into a standardized items array.
 */
function parse_instagram_edges($edges, $username) {
    $items = [];
    foreach ($edges as $edge) {
        $node = $edge['node'];
        $shortcode = $node['shortcode'];
        $is_video = $node['is_video'] ?? false;
        $display_url = $node['display_url'] ?? '';
        $thumbnail_url = $node['thumbnail_src'] ?? $display_url;
        $video_url = $node['video_url'] ?? null;
        $timestamp = $node['taken_at_timestamp'] ?? time();
        
        // Extract caption
        $caption = '';
        if (isset($node['edge_media_to_caption']['edges'][0]['node']['text'])) {
            $caption = $node['edge_media_to_caption']['edges'][0]['node']['text'];
        }
        
        // Determine post type and correct permalink
        if ($is_video) {
            $media_type = 'REEL';
            $permalink = "https://www.instagram.com/reel/{$shortcode}/";
        } else {
            $media_type = 'IMAGE';
            $permalink = "https://www.instagram.com/p/{$shortcode}/";
        }
        
        $items[] = [
            'instagram_media_id' => 'IG_' . $shortcode,
            'username' => $username,
            'media_type' => $media_type,
            'media_url' => $display_url,
            'video_url' => $video_url,
            'thumbnail_url' => $thumbnail_url,
            'permalink' => $permalink,
            'caption' => $caption,
            'published_at' => date('Y-m-d H:i:s', $timestamp)
        ];
    }
    return $items;
}

/**
 * Primary method: Fetch posts via Instagram GraphQL API.
 */
function fetch_via_graphql_api($username) {
    $graphql_url = "https://www.instagram.com/api/v1/users/web_profile_info/?username={$username}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $graphql_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: */*',
        'X-IG-App-ID: 936619743392459',
        'X-Requested-With: XMLHttpRequest',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
        'Referer: https://www.instagram.com/' . $username . '/',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code !== 200 || empty($response)) {
        write_sync_log("[API] GraphQL API returned HTTP {$http_code} for @{$username}");
        return [];
    }

    $data = json_decode($response, true);
    
    // Check for Instagram internal errors (e.g. deleted asset schema)
    if (isset($data['status']) && $data['status'] === 'fail') {
        $msg = $data['message'] ?? 'Unknown API error';
        write_sync_log("[API] GraphQL API returned error for @{$username}: {$msg}");
        return [];
    }

    $edges = $data['data']['user']['edge_owner_to_timeline_media']['edges'] ?? [];
    return parse_instagram_edges($edges, $username);
}

/**
 * Fallback method: Scrape the profile page HTML for embedded post data.
 * Used when the GraphQL API fails (e.g. business account schema errors).
 */
function fetch_via_html_scrape($username) {
    write_sync_log("[FALLBACK] Attempting HTML scrape for @{$username}");

    $profile_url = "https://www.instagram.com/{$username}/";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $profile_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code !== 200 || empty($html)) {
        write_sync_log("[FALLBACK] Profile page returned HTTP {$http_code} for @{$username}");
        return [];
    }

    $items = [];

    // Method 1: Try to extract JSON data from <script type="application/ld+json">
    if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $ld_matches)) {
        foreach ($ld_matches[1] as $ld_json) {
            $ld_data = json_decode($ld_json, true);
            if (isset($ld_data['mainEntity']['interactionStatistic'])) {
                write_sync_log("[FALLBACK] Found LD+JSON data for @{$username}");
            }
        }
    }

    // Method 2: Try to find the shared data JSON embedded in the page
    if (preg_match('/window\._sharedData\s*=\s*({.+?});\s*<\/script>/s', $html, $shared_match)) {
        $shared_data = json_decode($shared_match[1], true);
        if ($shared_data) {
            $user_data = $shared_data['entry_data']['ProfilePage'][0]['graphql']['user'] ?? null;
            if ($user_data) {
                $edges = $user_data['edge_owner_to_timeline_media']['edges'] ?? [];
                $items = parse_instagram_edges($edges, $username);
                write_sync_log("[FALLBACK] Extracted " . count($items) . " posts from _sharedData for @{$username}");
                return $items;
            }
        }
    }

    // Method 3: Try to extract from __additionalData or require scripts
    if (preg_match_all('/\"shortcode\":\"([A-Za-z0-9_-]+)\"/', $html, $sc_matches)) {
        $shortcodes = array_unique($sc_matches[1]);
        write_sync_log("[FALLBACK] Found " . count($shortcodes) . " shortcodes in HTML for @{$username}");
        
        foreach ($shortcodes as $shortcode) {
            // Extract display_url near this shortcode if available
            $display_url = '';
            if (preg_match('/"shortcode":"' . preg_quote($shortcode) . '".*?"display_url":"([^"]+)"/s', $html, $url_match)) {
                $display_url = stripcslashes($url_match[1]);
            }
            
            $is_video = false;
            if (preg_match('/"shortcode":"' . preg_quote($shortcode) . '".*?"is_video":(true|false)/s', $html, $vid_match)) {
                $is_video = $vid_match[1] === 'true';
            }

            $caption = '';
            if (preg_match('/"shortcode":"' . preg_quote($shortcode) . '".*?"text":"([^"]*?)"/s', $html, $cap_match)) {
                $caption = stripcslashes($cap_match[1]);
            }

            $timestamp = time();
            if (preg_match('/"shortcode":"' . preg_quote($shortcode) . '".*?"taken_at_timestamp":(\d+)/s', $html, $ts_match)) {
                $timestamp = (int)$ts_match[1];
            }
            
            $media_type = $is_video ? 'REEL' : 'IMAGE';
            $permalink = $is_video 
                ? "https://www.instagram.com/reel/{$shortcode}/" 
                : "https://www.instagram.com/p/{$shortcode}/";

            $items[] = [
                'instagram_media_id' => 'IG_' . $shortcode,
                'username' => $username,
                'media_type' => $media_type,
                'media_url' => $display_url,
                'video_url' => null,
                'thumbnail_url' => $display_url,
                'permalink' => $permalink,
                'caption' => $caption,
                'published_at' => date('Y-m-d H:i:s', $timestamp)
            ];
        }
    }

    write_sync_log("[FALLBACK] HTML scrape yielded " . count($items) . " items for @{$username}");
    return $items;
}

/**
 * Parse Instagram feed items format from API/v1 endpoint into standardized items array.
 */
function parse_instagram_feed_items($items, $username) {
    $parsed = [];
    foreach ($items as $item) {
        $shortcode = $item['code'] ?? '';
        if (empty($shortcode)) continue;

        $media_type_raw = $item['media_type'] ?? 1;
        $taken_at = $item['taken_at'] ?? time();
        $is_video = ($media_type_raw === 2);
        
        $display_url = '';
        $candidates = $item['image_versions2']['candidates'] ?? [];
        if (!empty($candidates)) {
            $display_url = $candidates[0]['url'] ?? '';
        }
        
        $video_url = null;
        $video_versions = $item['video_versions'] ?? [];
        if ($is_video && !empty($video_versions)) {
            $video_url = $video_versions[0]['url'] ?? null;
        }
        
        $caption = '';
        if (isset($item['caption']['text'])) {
            $caption = $item['caption']['text'];
        }
        
        $media_type = $is_video ? 'REEL' : 'IMAGE';
        $permalink = $is_video 
            ? "https://www.instagram.com/reel/{$shortcode}/" 
            : "https://www.instagram.com/p/{$shortcode}/";
            
        $parsed[] = [
            'instagram_media_id' => 'IG_' . $shortcode,
            'username' => $username,
            'media_type' => $media_type,
            'media_url' => $display_url,
            'video_url' => $video_url,
            'thumbnail_url' => $display_url,
            'permalink' => $permalink,
            'caption' => $caption,
            'published_at' => date('Y-m-d H:i:s', $taken_at)
        ];
    }
    return $parsed;
}

/**
 * Alternative API method: Fetch posts via Instagram mobile feed user endpoint.
 */
function fetch_via_user_feed_api($username) {
    write_sync_log("[API] Attempting user feed API for @{$username}");
    
    $cookie_file = tempnam(sys_get_temp_dir(), 'ig_cookies_');
    
    // Step 1: Visit main page to initialize session / cookies
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_exec($ch);
    
    // Step 2: Query feed API
    $feed_url = "https://www.instagram.com/api/v1/feed/user/{$username}/username/?count=12";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $feed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-IG-App-ID: 936619743392459',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
        'Referer: https://www.instagram.com/' . $username . '/',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (file_exists($cookie_file)) {
        @unlink($cookie_file);
    }
    
    if ($http_code !== 200 || empty($response)) {
        write_sync_log("[API] User feed API returned HTTP {$http_code} for @{$username}");
        return [];
    }
    
    $data = json_decode($response, true);
    $items = $data['items'] ?? [];
    
    if (empty($items)) {
        write_sync_log("[API] User feed API returned 0 items for @{$username}");
        return [];
    }
    
    write_sync_log("[API] User feed API successfully fetched " . count($items) . " items for @{$username}");
    return parse_instagram_feed_items($items, $username);
}

/**
 * Main fetch function: tries GraphQL API first, falls back to User Feed API, then to HTML scraping.
 */
function fetch_latest_instagram_posts($username) {
    // Try primary GraphQL API method
    $items = fetch_via_graphql_api($username);
    
    // Try user feed API fallback
    if (empty($items)) {
        $items = fetch_via_user_feed_api($username);
    }
    
    // If both failed, try HTML scraping fallback
    if (empty($items)) {
        $items = fetch_via_html_scrape($username);
    }
    
    return $items;
}

// ============================================================
//  SYNC A SINGLE ACCOUNT (reusable function)
// ============================================================

/**
 * Sync a single Instagram account into the database.
 * 
 * NON-DESTRUCTIVE: If the API returns 0 items (e.g. temporary failure),
 * the existing cached posts are PRESERVED — never deleted.
 * Old cache is only cleared when fresh data is successfully fetched.
 * 
 * @param mysqli $conn  Database connection
 * @param string $username  Instagram handle to sync
 * @return array  Summary with counts
 */
function sync_instagram_account($conn, $username) {
    ensure_instagram_table($conn);

    write_sync_log("Starting Instagram sync for @{$username}");

    $items = fetch_latest_instagram_posts($username);
    $fetched_count = count($items);

    write_sync_log("Fetched {$fetched_count} latest Instagram posts via GraphQL API for @{$username}");

    $inserted_count = 0;
    $updated_count = 0;

    if (!empty($items)) {
        // Only clear old stale data AFTER confirming we have fresh data.
        // This ensures we never delete cached posts when the API temporarily fails.
        $conn->query("DELETE FROM instagram_media WHERE username = '" . $conn->real_escape_string($username) . "'");
        write_sync_log("Cleared old cache for @{$username} (replacing with {$fetched_count} fresh items)");

        try {
            $stmt = $conn->prepare("INSERT INTO instagram_media 
                (instagram_media_id, username, media_type, media_url, video_url, thumbnail_url, permalink, caption, published_at, fetched_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    media_type = VALUES(media_type),
                    media_url = VALUES(media_url),
                    video_url = VALUES(video_url),
                    thumbnail_url = VALUES(thumbnail_url),
                    permalink = VALUES(permalink),
                    caption = VALUES(caption),
                    published_at = VALUES(published_at),
                    updated_at = NOW()");

            if ($stmt) {
                foreach ($items as $item) {
                    $mid = $item['instagram_media_id'];
                    $uname = $item['username'];
                    $mtype = $item['media_type'];
                    $murl = $item['media_url'];
                    $vurl = isset($item['video_url']) ? $item['video_url'] : null;
                    $turl = isset($item['thumbnail_url']) ? $item['thumbnail_url'] : $item['media_url'];
                    $plink = $item['permalink'];
                    $cap = $item['caption'];
                    $pub_at = $item['published_at'];

                    $stmt->bind_param("sssssssss", $mid, $uname, $mtype, $murl, $vurl, $turl, $plink, $cap, $pub_at);
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows === 1) {
                            $inserted_count++;
                        } else if ($stmt->affected_rows === 2) {
                            $updated_count++;
                        }
                    }
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            write_sync_log("[ERROR] Database upsert failed for @{$username}: " . $e->getMessage());
        }
    } else {
        write_sync_log("[WARN] API returned 0 items for @{$username} — preserving existing cache (non-destructive)");
    }

    // Query database totals for this account
    $total_in_db = 0;
    $reels_in_db = 0;
    $count_res = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN media_type = 'REEL' OR media_type = 'VIDEO' THEN 1 ELSE 0 END) as reels FROM instagram_media WHERE username = '" . $conn->real_escape_string($username) . "'");
    if ($count_res) {
        $row = $count_res->fetch_assoc();
        $total_in_db = (int)$row['total'];
        $reels_in_db = (int)$row['reels'];
    }

    $summary = "Sync completed for @{$username}. DB: {$total_in_db} items ({$reels_in_db} Reels). Inserted: {$inserted_count}, Refreshed: {$updated_count}.";
    write_sync_log("[SUCCESS] {$summary}");

    return [
        'success' => true,
        'username' => $username,
        'total_media_in_db' => $total_in_db,
        'total_reels_in_db' => $reels_in_db,
        'fetched' => $fetched_count,
        'inserted' => $inserted_count,
        'updated' => $updated_count
    ];
}

// ============================================================
//  MAIN: Run when called directly (not included by other files)
// ============================================================

// Only execute sync when called directly (not when included by feed.php or sync_all.php)
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'sync.php' || $is_cli) {

    // Determine which Instagram account to sync
    if ($is_cli && isset($argv[1])) {
        $sync_username = strtolower(ltrim(trim($argv[1]), '@'));
    } else {
        $sync_username = isset($_GET['username']) ? strtolower(ltrim(trim(filter_var($_GET['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)), '@')) : '';
    }
    if (empty($sync_username)) {
        $sync_username = INSTAGRAM_USERNAME;
    }

    write_sync_log("--------------------------------------------------");

    if (!$conn) {
        $err = "Database connection unavailable.";
        write_sync_log("[ERROR] {$err}");
        if (!$is_cli) echo json_encode(['success' => false, 'message' => $err]);
        exit;
    }

    @$conn->set_charset("utf8mb4");

    $result = sync_instagram_account($conn, $sync_username);

    if ($is_cli) {
        echo $result['username'] . ": " . $result['total_media_in_db'] . " items in DB ({$result['total_reels_in_db']} Reels). Inserted: {$result['inserted']}, Refreshed: {$result['updated']}.\n";
    } else {
        $result['synced_at'] = date('Y-m-d H:i:s');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
