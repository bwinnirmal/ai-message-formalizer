<?php
require 'auth.php';
require 'config.php';

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

// $prompts = [
//     'email' => 'Rewrite as a formal professional email.',
//     'whatsapp' => 'Rewrite as a polite professional WhatsApp message.',
//     'ticket' => 'Rewrite as a professional support ticket reply.'
// ];

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
    $key = $apiKey !== '' ? $apiKey : DEEPSEEK_API_KEY;
    $defaultModel = DEEPSEEK_MODEL;
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
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($curlError) {
    echo json_encode(['error' => 'Request failed: ' . $curlError]);
    exit;
}

if (!isset($data['choices'][0]['message']['content'])) {
    $errorMessage = $data['error']['message'] ?? 'Failed';
    echo json_encode(['error' => $errorMessage]);
    exit;
}

echo json_encode([
    'result' => $data['choices'][0]['message']['content']
]);
