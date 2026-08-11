<?php
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
    $media_url = 'public/startups/nutridelight/hero.png';
}

// Generate unique post_id from permalink or timestamp
$post_id = 'CUSTOM_' . md5($permalink . time());
if (preg_match('/instagram\.com\/(?:reel|p)\/([A-Za-z0-9_-]+)/i', $permalink, $matches)) {
    $post_id = 'IG_' . $matches[1];
}

$likes = rand(150, 450);
$comments = rand(15, 60);
$timestamp = date('Y-m-d H:i:s');

if ($conn) {
    @$conn->set_charset("utf8mb4");

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

    try {
        $stmt = $conn->prepare("INSERT INTO instagram_posts (account_name, post_id, media_type, media_url, caption, likes_count, comments_count, permalink, timestamp, fetched_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE media_url = VALUES(media_url), caption = VALUES(caption), media_type = VALUES(media_type), fetched_at = NOW()");
        
        if ($stmt) {
            $stmt->bind_param("sssssiiss", $account_name, $post_id, $media_type, $media_url, $caption, $likes, $comments, $permalink, $timestamp);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'New Instagram ' . $media_type . ' collected & saved successfully to MySQL database!',
                'post' => [
                    'post_id' => $post_id,
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
