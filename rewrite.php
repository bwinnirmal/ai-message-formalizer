<?php
require 'auth.php';
require 'config.php';

header('Content-Type: application/json; charset=utf-8');


function saveToHistory(array $entry): void
{
    $username = $_SESSION['username'] ?? 'user';
    $safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
    $dir = __DIR__ . '/storage';
    $file = $dir . '/history_' . $safeUser . '.json';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $history = [];
    if (file_exists($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            $history = $decoded;
        }
    }

    array_unshift($history, $entry);
    $history = array_slice($history, 0, 50);

    file_put_contents($file, json_encode($history, JSON_UNESCAPED_UNICODE));
}

$text   = trim($_POST['text'] ?? '');
$mode   = $_POST['mode'] ?? 'email';
$length = $_POST['length'] ?? 'short';
$api    = $_POST['api'] ?? 'deepseek';
$model  = trim($_POST['model'] ?? '');
$apiKey = trim($_POST['api_key'] ?? '');
$customCommand = trim($_POST['custom_command'] ?? '');
$action = $_POST['action'] ?? 'rewrite';
$tone = $_POST['tone'] ?? 'professional';
$language = trim($_POST['language'] ?? 'English');

if ($text === '') {
    echo json_encode(['error' => 'Empty input']);
    exit;
}

$prompts = [
    'email' =>
        'Rewrite this as a clear, professional email.
         Use polite and confident language.
         Keep it structured and human, not robotic.',

    'whatsapp' =>
        'Rewrite this as a polite and professional WhatsApp message.
         Keep it short, natural, and friendly.
         Avoid sounding formal or robotic.',

    'ticket' =>
        'Rewrite this as a professional support ticket reply.
         Be clear, calm, and helpful.
         Keep the tone respectful and solution-focused.'
];

$actions = [
    'rewrite' => 'Rewrite and improve the original message while preserving intent.',
    'summarize' => 'Summarize the message into a concise version with key points only.',
    'bulletize' => 'Convert the message into clean bullet points for quick readability.',
    'next_step' => 'Rewrite and include a clear next-step or call to action at the end.'
];

$tones = [
    'professional' => 'Use a professional, business-friendly tone.',
    'friendly' => 'Use a friendly, warm, and approachable tone.',
    'assertive' => 'Use an assertive and confident tone while staying respectful.',
    'empathetic' => 'Use an empathetic, customer-care tone.'
];

$systemPrompt =
    ($prompts[$mode] ?? $prompts['email']) .
    ($length === 'detailed'
        ? ' Provide a clear and structured version.'
        : ' Keep it short and concise.') .
    ' ' . ($actions[$action] ?? $actions['rewrite']) .
    ' ' . ($tones[$tone] ?? $tones['professional']) .
    ' Respond in ' . ($language !== '' ? $language : 'English') . '.' .
    ' Do not add extra information.';

if ($customCommand !== '') {
    $systemPrompt .= ' Additional instruction from user: ' . $customCommand;
}

if ($api === 'openai') {
    $url = OPENAI_API_URL;
    $key = $apiKey !== '' ? $apiKey : OPENAI_API_KEY;
    $defaultModel = OPENAI_MODEL;
} else {
    $url = DEEPSEEK_API_URL;
    // DeepSeek docs primarily use /chat/completions; normalize in case /v1/chat/completions is configured.
    $url = preg_replace('#/v1/chat/completions$#', '/chat/completions', $url);
    $key = $apiKey !== '' ? $apiKey : DEEPSEEK_API_KEY;
    $defaultModel = DEEPSEEK_MODEL;
}

if (!function_exists('curl_init')) {
    echo json_encode(['error' => 'cURL extension is not enabled on this server.']);
    exit;
}

$selectedModel = $model !== '' ? $model : $defaultModel;

$payload = [
    'model' => $selectedModel,
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $text]
    ],
    'temperature' => 0.3
];

$ch = curl_init($url);
if ($ch === false) {
    echo json_encode(['error' => 'Unable to initialize request.']);
    exit;
}

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    echo json_encode(['error' => 'Request failed: ' . $curlError]);
    exit;
}

if ($response === false || $response === '') {
    echo json_encode(['error' => 'Empty response from AI provider.']);
    exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {
    $snippet = mb_substr(trim(strip_tags($response)), 0, 220);
    echo json_encode([
        'error' => 'Provider returned a non-JSON response (HTTP ' . (int)$httpCode . '). ' . $snippet
    ]);
    exit;
}

if (!isset($data['choices'][0]['message']['content'])) {
    $errorMessage = $data['error']['message'] ?? 'Provider response did not include output text.';
    echo json_encode(['error' => $errorMessage]);
    exit;
}

$resultText = $data['choices'][0]['message']['content'];

saveToHistory([
    'id' => bin2hex(random_bytes(8)),
    'created_at' => date('c'),
    'mode' => $mode,
    'action' => $action,
    'tone' => $tone,
    'length' => $length,
    'language' => $language,
    'provider' => $api,
    'model' => $selectedModel,
    'input' => $text,
    'output' => $resultText
]);

echo json_encode([
    'result' => $resultText
]);
