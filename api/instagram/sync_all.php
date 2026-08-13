<?php
/**
 * Instagram Sync All Accounts
 * 
 * Iterates all configured Instagram accounts and syncs each one independently.
 * If one account fails, the others continue processing.
 * 
 * Usage:
 *   Browser:  GET /api/instagram/sync_all.php
 *   CLI:      php api/instagram/sync_all.php
 *   Cron:     * /15 * * * * php /path/to/api/instagram/sync_all.php
 */

if (session_status() === PHP_SESSION_NONE) session_start();

include_once __DIR__ . '/../../config/instagram.php';
include_once __DIR__ . '/../../connect.php';
include_once __DIR__ . '/sync.php';

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
}

if (!$conn) {
    $err = "Database connection unavailable.";
    if (!$is_cli) {
        echo json_encode(['success' => false, 'message' => $err]);
    } else {
        echo "[ERROR] {$err}\n";
    }
    exit;
}

@$conn->set_charset("utf8mb4");

// Get all unique Instagram accounts from central config
$accounts = get_all_instagram_accounts();

write_sync_log("==========================================================");
write_sync_log("SYNC ALL: Starting batch sync for " . count($accounts) . " accounts");
write_sync_log("==========================================================");

$results = [];
$success_count = 0;
$fail_count = 0;

foreach ($accounts as $username) {
    write_sync_log("--------------------------------------------------");
    try {
        $result = sync_instagram_account($conn, $username);
        $results[] = $result;
        if ($result['success']) {
            $success_count++;
        } else {
            $fail_count++;
        }
    } catch (Exception $e) {
        write_sync_log("[ERROR] Exception syncing @{$username}: " . $e->getMessage());
        $results[] = [
            'success' => false,
            'username' => $username,
            'error' => $e->getMessage()
        ];
        $fail_count++;
    }
    
    // Small delay between accounts to avoid rate limiting
    usleep(500000); // 500ms
}

$summary = "Batch sync complete. {$success_count} succeeded, {$fail_count} failed out of " . count($accounts) . " accounts.";
write_sync_log("[BATCH COMPLETE] {$summary}");

if ($is_cli) {
    echo "\n{$summary}\n";
    foreach ($results as $r) {
        $status = $r['success'] ? '✓' : '✗';
        $detail = isset($r['total_media_in_db']) ? "{$r['total_media_in_db']} items ({$r['total_reels_in_db']} Reels)" : ($r['error'] ?? 'Unknown error');
        echo "  {$status} @{$r['username']}: {$detail}\n";
    }
} else {
    echo json_encode([
        'success' => $fail_count === 0,
        'message' => $summary,
        'total_accounts' => count($accounts),
        'succeeded' => $success_count,
        'failed' => $fail_count,
        'accounts' => $results,
        'synced_at' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
