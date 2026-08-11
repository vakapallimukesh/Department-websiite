<?php
/**
 * NutriDelight Instagram Integration Configuration
 * 
 * IMPORTANT: Keep credentials and API keys server-side in this file.
 * Do NOT expose API keys or access tokens to client-side HTML or JavaScript.
 */

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
