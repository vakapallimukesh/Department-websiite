<?php
/**
 * Instagram Integration Configuration
 * 
 * Central configuration for multi-account Instagram feed system.
 * All startup-to-Instagram-handle mappings are defined here.
 * 
 * IMPORTANT: Keep credentials and API keys server-side in this file.
 * Do NOT expose API keys or access tokens to client-side HTML or JavaScript.
 */

// Default Instagram username (backward compatibility)
if (!defined('INSTAGRAM_USERNAME')) {
    define('INSTAGRAM_USERNAME', 'nutri__delight');
}

if (!defined('INSTAGRAM_API_KEY')) {
    define('INSTAGRAM_API_KEY', 'YOUR_API_KEY_HERE');
}

if (!defined('INSTAGRAM_CACHE_TTL')) {
    define('INSTAGRAM_CACHE_TTL', 900); // 15 minutes cache TTL
}

if (!defined('INSTAGRAM_SYNC_LOG')) {
    define('INSTAGRAM_SYNC_LOG', __DIR__ . '/../logs/instagram_sync.log');
}

/**
 * Multi-Account Instagram Configuration
 * 
 * Maps startup page identifiers to their Instagram handles.
 * This is the SINGLE SOURCE OF TRUTH for all Instagram usernames.
 * Do NOT hardcode Instagram usernames in other files.
 * 
 * Format: 'startup-id' => 'instagram_handle'
 */
$INSTAGRAM_ACCOUNTS = [
    'nutridelight'        => 'nutri__delight',
    'smart-wash'          => 'bo_smartwash',
    'smartwash'           => 'bo_smartwash',
    'bhimavaram-online'   => 'bhimavaram_online',
    'bhimavaramonline'    => 'bhimavaram_online',
    'bhimavaram-digitals' => 'bhimavaramdigitals',
    'bhimavaram-digital'  => 'bhimavaramdigitals',
];

/**
 * Helper: Get Instagram handle for a given startup ID.
 * Returns the configured handle or null if not found.
 */
function get_instagram_handle($startup_id) {
    global $INSTAGRAM_ACCOUNTS;
    $id = strtolower(trim($startup_id));
    return isset($INSTAGRAM_ACCOUNTS[$id]) ? $INSTAGRAM_ACCOUNTS[$id] : null;
}

/**
 * Helper: Get all unique Instagram accounts for batch sync.
 * Returns an array of unique handles (no duplicates from alias entries).
 */
function get_all_instagram_accounts() {
    global $INSTAGRAM_ACCOUNTS;
    return array_unique(array_values($INSTAGRAM_ACCOUNTS));
}
