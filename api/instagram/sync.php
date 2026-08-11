<?php
/**
 * NutriDelight Instagram Background Synchronization Service
 * 
 * Flow:
 * Instagram GraphQL API -> Scheduled Sync -> MySQL DB (instagram_media) -> Website Gallery
 * 
 * Non-destructive strategy:
 * - Upserts new and existing Instagram media records (INSERT ... ON DUPLICATE KEY UPDATE).
 * - Never deletes older records.
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

function write_sync_log($message) {
    $log_dir = __DIR__ . '/../../logs';
    if (!file_exists($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    $log_file = defined('INSTAGRAM_SYNC_LOG') ? INSTAGRAM_SYNC_LOG : ($log_dir . '/instagram_sync.log');
    $timestamp = date('Y-m-d H:i:s');
    @file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
}

write_sync_log("--------------------------------------------------");
write_sync_log("Starting Instagram sync for @" . INSTAGRAM_USERNAME);

if (!$conn) {
    $err = "Database connection unavailable.";
    write_sync_log("[ERROR] {$err}");
    if (!$is_cli) echo json_encode(['success' => false, 'message' => $err]);
    exit;
}

@$conn->set_charset("utf8mb4");

// 1. Ensure table instagram_media exists
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
while ($r = $cols_res->fetch_assoc()) {
    $existing_cols[] = $r['Field'];
}
if (!in_array('video_url', $existing_cols)) {
    @$conn->query("ALTER TABLE instagram_media ADD COLUMN video_url TEXT AFTER media_url");
}
if (!in_array('updated_at', $existing_cols)) {
    @$conn->query("ALTER TABLE instagram_media ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
}

// 2. Fetch latest posts from Instagram GraphQL API
function fetch_latest_instagram_posts($username) {
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
        return [];
    }

    $data = json_decode($response, true);
    $edges = $data['data']['user']['edge_owner_to_timeline_media']['edges'] ?? [];
    
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

$items = fetch_latest_instagram_posts(INSTAGRAM_USERNAME);

write_sync_log("Fetched " . count($items) . " latest Instagram posts via GraphQL API.");

// 3. Upsert into database
$inserted_count = 0;
$updated_count = 0;

if (!empty($items)) {
    // First, clear old stale data for this username so we always show the freshest posts
    $conn->query("DELETE FROM instagram_media WHERE username = '" . $conn->real_escape_string(INSTAGRAM_USERNAME) . "'");
    write_sync_log("Cleared old cache for @" . INSTAGRAM_USERNAME);

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
        write_sync_log("[ERROR] Database upsert failed: " . $e->getMessage());
    }
}

// Query preserved database totals
$total_in_db = 0;
$reels_in_db = 0;
$count_res = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN media_type = 'REEL' OR media_type = 'VIDEO' THEN 1 ELSE 0 END) as reels FROM instagram_media WHERE username = '" . $conn->real_escape_string(INSTAGRAM_USERNAME) . "'");
if ($count_res) {
    $row = $count_res->fetch_assoc();
    $total_in_db = (int)$row['total'];
    $reels_in_db = (int)$row['reels'];
}

$summary = "Instagram sync completed for @" . INSTAGRAM_USERNAME . ". Total media in DB: {$total_in_db} (including {$reels_in_db} Reels). Inserted: {$inserted_count}, Refreshed: {$updated_count}.";
write_sync_log("[SUCCESS] {$summary}");

if ($is_cli) {
    echo "{$summary}\n";
} else {
    echo json_encode([
        'success' => true,
        'message' => $summary,
        'username' => INSTAGRAM_USERNAME,
        'total_media_in_db' => $total_in_db,
        'total_reels_in_db' => $reels_in_db,
        'inserted' => $inserted_count,
        'updated' => $updated_count,
        'synced_at' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
