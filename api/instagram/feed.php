<?php
/**
 * Instagram Media Feed API Endpoint
 * 
 * Serves cached Instagram posts & Reels from local MySQL table (instagram_media).
 * Supports multi-account queries via ?username=<handle> parameter.
 * Zero external calls to Instagram during normal visitor page loads.
 * 
 * If the database has no records for the requested account, triggers
 * a one-time sync for that specific account, then re-queries.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once __DIR__ . '/../../config/instagram.php';
include_once __DIR__ . '/../../connect.php';

$username = isset($_GET['username']) ? strtolower(ltrim(trim(filter_var($_GET['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)), '@')) : INSTAGRAM_USERNAME;
if (empty($username)) {
    $username = INSTAGRAM_USERNAME;
}

$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;
$media_type_filter = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : 'ALL';

$media_list = [];

if ($conn) {
    @$conn->set_charset("utf8mb4");

    // Build query based on media type filter
    $sql_base = "SELECT instagram_media_id, username, media_type, media_url, video_url, thumbnail_url, permalink, caption, published_at, fetched_at, updated_at 
                FROM instagram_media WHERE username = ?";

    if ($media_type_filter === 'REEL' || $media_type_filter === 'VIDEO') {
        $sql = $sql_base . " AND (media_type = 'REEL' OR media_type = 'VIDEO') ORDER BY published_at DESC LIMIT ?";
    } else if ($media_type_filter === 'IMAGE' || $media_type_filter === 'PHOTO') {
        $sql = $sql_base . " AND media_type != 'REEL' AND media_type != 'VIDEO' ORDER BY published_at DESC LIMIT ?";
    } else {
        $sql = $sql_base . " ORDER BY published_at DESC LIMIT ?";
    }

    try {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $username, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $media_list[] = format_media_row($row);
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $media_list = [];
    }

    // Auto-sync fallback: if database returns 0 records for this account,
    // trigger a one-time sync for THIS SPECIFIC username, then re-query.
    if (empty($media_list)) {
        try {
            include_once __DIR__ . '/sync.php';
            // Call the reusable sync function with the CORRECT username
            sync_instagram_account($conn, $username);

            // Re-query after sync
            $stmt = $conn->prepare("SELECT instagram_media_id, username, media_type, media_url, video_url, thumbnail_url, permalink, caption, published_at, fetched_at, updated_at 
                    FROM instagram_media 
                    WHERE username = ? 
                    ORDER BY published_at DESC LIMIT ?");
            if ($stmt) {
                $stmt->bind_param("si", $username, $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $media_list[] = format_media_row($row);
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            $media_list = [];
        }
    }
}

// Return JSON response to website frontend
echo json_encode([
    'success' => true,
    'username' => $username,
    'count' => count($media_list),
    'media' => $media_list,
    'cached_from_mysql' => true,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/**
 * Format a database row into a standardized media response object.
 */
function format_media_row($row) {
    return [
        'id' => $row['instagram_media_id'],
        'instagram_media_id' => $row['instagram_media_id'],
        'username' => $row['username'],
        'media_type' => $row['media_type'],
        'media_url' => $row['media_url'],
        'video_url' => !empty($row['video_url']) ? $row['video_url'] : null,
        'thumbnail_url' => !empty($row['thumbnail_url']) ? $row['thumbnail_url'] : $row['media_url'],
        'permalink' => $row['permalink'],
        'caption' => $row['caption'],
        'published_at' => $row['published_at'],
        'fetched_at' => $row['fetched_at'],
        'updated_at' => isset($row['updated_at']) ? $row['updated_at'] : $row['fetched_at']
    ];
}
