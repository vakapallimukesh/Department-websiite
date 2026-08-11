<?php
/**
 * Instagram Media Proxy
 * 
 * Proxies Instagram CDN images/videos through our server to bypass
 * hotlink protection and CORS restrictions. Uses caching and supports
 * HTTP Range requests for seamless video streaming in Safari/Chrome.
 */

$url = isset($_GET['url']) ? $_GET['url'] : '';

if (empty($url)) {
    http_response_code(400);
    exit('Missing url parameter');
}

// Only allow Instagram CDN URLs for security
if (strpos($url, 'cdninstagram.com') === false && strpos($url, 'fbcdn.net') === false) {
    http_response_code(403);
    exit('Only Instagram CDN URLs are allowed');
}

// Generate a cache key from the URL (strip query params that change for the same media)
$cache_dir = __DIR__ . '/../../cache/instagram';
if (!file_exists($cache_dir)) {
    @mkdir($cache_dir, 0755, true);
}

$cache_key = md5($url);
// Detect file type from URL
$is_video = (strpos($url, '/v/t16/') !== false || strpos($url, 'video') !== false || strpos($url, '/o1/') !== false);
$ext = $is_video ? '.mp4' : '.jpg';
$cache_file = $cache_dir . '/' . $cache_key . $ext;

// If file not in cache or older than 6 hours, fetch it
if (!file_exists($cache_file) || (time() - filemtime($cache_file)) >= 21600) {
    // Fetch from Instagram CDN
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: image/avif,image/webp,image/apng,image/*,video/*,*/*;q=0.8',
        'Referer: https://www.instagram.com/',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($http_code === 200 && !empty($data)) {
        @file_put_contents($cache_file, $data);
    }
}

if (!file_exists($cache_file)) {
    http_response_code(502);
    exit('Failed to fetch media from Instagram CDN');
}

// Handle Range Requests for Video Streaming
$filesize = filesize($cache_file);
$offset = 0;
$length = $filesize;
$mime = $is_video ? 'video/mp4' : 'image/jpeg';

if (isset($_SERVER['HTTP_RANGE'])) {
    preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
    $offset = intval($matches[1]);
    $length = (isset($matches[2]) && $matches[2] !== '') ? intval($matches[2]) - $offset + 1 : $filesize - $offset;
    
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $filesize);
} else {
    header('HTTP/1.1 200 OK');
}

header('Content-Type: ' . $mime);
header('Accept-Ranges: bytes');
header('Cache-Control: public, max-age=21600');
header('Content-Length: ' . $length);

$file = fopen($cache_file, 'rb');
fseek($file, $offset);
echo fread($file, $length);
fclose($file);
