<?php
/**
 * Secure Backend Gemini API Proxy & RAG Engine
 * SRKREC CSD & CSIT Department Assistant
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to load .env file manually if PHP environment hasn't parsed it
function loadEnv($envPath) {
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0 || empty($line)) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Load .env from project root
loadEnv(__DIR__ . '/../.env');

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$prompt = isset($data['prompt']) ? trim($data['prompt']) : '';
$ragContext = isset($data['context']) ? $data['context'] : null;
$history = isset($data['history']) && is_array($data['history']) ? $data['history'] : [];

$apiKey = getenv('GEMINI_API_KEY');
if (empty($apiKey) && isset($_ENV['GEMINI_API_KEY'])) {
    $apiKey = $_ENV['GEMINI_API_KEY'];
}
if (empty($apiKey) && isset($data['apiKey']) && !empty($data['apiKey'])) {
    $apiKey = trim($data['apiKey']);
}

if (empty($prompt)) {
    echo json_encode(['error' => 'Prompt parameter is required']);
    exit();
}

if (empty($apiKey)) {
    echo json_encode([
        'status' => 'offline_fallback',
        'message' => 'No Gemini API key configured in .env. Falling back to local RAG engine.'
    ]);
    exit();
}

if ($ragContext && !empty($ragContext['isNotFound'])) {
    $requested = isset($ragContext['requestedName']) ? $ragContext['requestedName'] : 'the requested person';
    echo json_encode([
        'status' => 'success',
        'reply' => "I couldn't find a person named {$requested} in the current department records.",
        'source' => 'local_enforcement'
    ]);
    exit();
}

// Build System Prompt
$systemInstruction = "You are the official AI Assistant for the Department of Computer Science & Design (CSD) and Computer Science & Information Technology (CSIT) at SRKR Engineering College, Bhimavaram.\n\n";

if ($ragContext && !empty($ragContext['content'])) {
    $systemInstruction .= "VERIFIED WEBSITE CONTEXT:\n";
    $systemInstruction .= "Title: " . $ragContext['title'] . "\n";
    $systemInstruction .= "Content: " . $ragContext['content'] . "\n\n";
    
    if (!empty($ragContext['isPersonQuery'])) {
        $field = isset($ragContext['requestedField']) ? $ragContext['requestedField'] : 'profile';
        $systemInstruction .= "IMPORTANT INSTRUCTION FOR PERSON QUERY:\n";
        $systemInstruction .= "The user is asking specifically about a person. Use ONLY the verified person record above. Answer the requested field ('{$field}') directly and concisely. DO NOT list unrelated people or substitute another person.\n";
    } else {
        $systemInstruction .= "Instructions: Answer the user question using the verified website context above. Do not invent fake names, fees, or dates.\n";
    }
} else {
    $systemInstruction .= "Instructions: Answer general computer science, programming, placement, or casual conversation questions naturally, politely, and accurately as a helpful department assistant.\n";
}

$contents = [];

// Push System Instruction & Context
$contents[] = [
    "role" => "user",
    "parts" => [["text" => $systemInstruction]]
];
$contents[] = [
    "role" => "model",
    "parts" => [["text" => "Understood. I am ready to assist as the Department Assistant."]]
];

// Append History Turns
foreach ($history as $msg) {
    if (isset($msg['role']) && isset($msg['text'])) {
        $role = ($msg['role'] === 'user') ? 'user' : 'model';
        $contents[] = [
            "role" => $role,
            "parts" => [["text" => $msg['text']]]
        ];
    }
}

// Append Current User Prompt
$contents[] = [
    "role" => "user",
    "parts" => [["text" => $prompt]]
];

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . urlencode($apiKey);

$payload = [
    "contents" => $contents,
    "generationConfig" => [
        "temperature" => 0.4,
        "maxOutputTokens" => 800
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 8);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    $resData = json_decode($response, true);
    $text = '';
    if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $resData['candidates'][0]['content']['parts'][0]['text'];
    }

    if (!empty($text)) {
        echo json_encode([
            'status' => 'success',
            'reply' => $text,
            'source' => 'gemini_api'
        ]);
        exit();
    }
}

echo json_encode([
    'status' => 'api_error',
    'message' => 'Gemini API call returned status ' . $httpCode . '. Falling back to local RAG engine.'
]);
exit();
