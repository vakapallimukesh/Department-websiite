<?php
/**
 * Manually Collect Instagram Post / Reel
 * 
 * Writes directly to the instagram_media table (the same table used by feed.php)
 * so that manually collected posts appear alongside auto-synced posts.
 * 
 * This is especially important for accounts where the Instagram API fails
 * (e.g. @bhimavaram_online has a broken business category schema on Instagram's end).
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Only POST allowed.']);
    exit;
}

$account_name = isset($_POST['account_name']) ? strtolower(ltrim(trim($_POST['account_name']), '@')) : 'nutri__delight';
$permalink = isset($_POST['permalink']) ? trim($_POST['permalink']) : '';
$caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';
$media_type = isset($_POST['media_type']) ? strtoupper(trim($_POST['media_type'])) : 'REEL';
$media_url = isset($_POST['media_url']) ? trim($_POST['media_url']) : '';

if (empty($permalink)) {
    echo json_encode(['success' => false, 'message' => 'Instagram Reel or Post link is required.']);
    exit;
}

if (empty($account_name)) {
    $account_name = 'nutri__delight';
}

if (empty($media_url)) {
    // Dynamic fallback image based on account/startup
    $startupImageMap = [
        'nutri__delight' => 'public/startups/nutridelight/hero.png',
        'bhimavaram_online' => 'public/startups/bhimavaram-online/bhimavaramonline.png',
        'bo_lunch_box' => 'public/startups/lunch-box/lunch-box.png',
        'bhimavaramdigitals' => 'public/startups/bhimavaram-digital/bhimavaram-digitals.png',
        'bo_smartwash' => 'public/startups/smart-wash/hero.png',
    ];
    $media_url = isset($startupImageMap[$account_name]) ? $startupImageMap[$account_name] : 'public/startups/nutridelight/hero.png';
}

// Generate unique media_id from permalink or timestamp
$media_id = 'CUSTOM_' . md5($permalink . time());
if (preg_match('/instagram\.com\/(?:reel|p)\/([A-Za-z0-9_-]+)/i', $permalink, $matches)) {
    $media_id = 'IG_' . $matches[1];
}

$timestamp = date('Y-m-d H:i:s');

if ($conn) {
    @$conn->set_charset("utf8mb4");

    // Ensure instagram_media table exists (same table used by sync.php and feed.php)
    @$conn->query("CREATE TABLE IF NOT EXISTS instagram_media (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    try {
        // Insert into instagram_media (same table that feed.php reads from)
        $stmt = $conn->prepare("INSERT INTO instagram_media 
            (instagram_media_id, username, media_type, media_url, video_url, thumbnail_url, permalink, caption, published_at, fetched_at, updated_at)
            VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                media_url = VALUES(media_url), 
                thumbnail_url = VALUES(thumbnail_url),
                caption = VALUES(caption), 
                media_type = VALUES(media_type), 
                updated_at = NOW()");
        
        if ($stmt) {
            $stmt->bind_param("ssssssss", $media_id, $account_name, $media_type, $media_url, $media_url, $permalink, $caption, $timestamp);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'New Instagram ' . $media_type . ' collected & saved successfully!',
                'post' => [
                    'post_id' => $media_id,
                    'account_name' => $account_name,
                    'media_type' => $media_type,
                    'media_url' => $media_url,
                    'caption' => $caption,
                    'permalink' => $permalink,
                    'timestamp' => $timestamp
                ]
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
