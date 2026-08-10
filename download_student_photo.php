<?php
// Disable error reporting to prevent header corruption
ini_set('display_errors', 0);
error_reporting(0);

$student_id = isset($_GET['student_id']) ? preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($_GET['student_id']))) : '';
$name = isset($_GET['name']) ? preg_replace('/[^A-Za-z0-9_ -]/', '', trim($_GET['name'])) : '';

if (empty($student_id)) {
    http_response_code(400);
    echo "Student ID is required.";
    exit;
}

$photo_url = "https://srkrexams.in/SRKR/photo/" . $student_id . ".jpg";

// Fetch image via cURL with browser headers
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $photo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, layout Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$imageData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || empty($imageData)) {
    header("Location: " . $photo_url);
    exit;
}

// Generate clean filename
$clean_name = !empty($name) ? preg_replace('/\s+/', '_', $name) : 'Student';
$filename = $student_id . '_' . $clean_name . '.jpg';

// Clear any output buffer to ensure clean binary download
if (ob_get_level()) {
    ob_end_clean();
}

// Send HTTP attachment disposition headers forcing direct browser download
header('Content-Description: File Transfer');
header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($imageData));

echo $imageData;
exit;
