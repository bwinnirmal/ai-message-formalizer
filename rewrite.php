<?php
require 'auth.php';
require 'config.php';

$text   = trim($_POST['text'] ?? '');
$mode   = $_POST['mode'] ?? 'email';
$length = $_POST['length'] ?? 'short';
$api    = $_POST['api'] ?? 'deepseek';

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


$systemPrompt =
    ($prompts[$mode] ?? $prompts['email']) .
    ($length === 'detailed'
        ? ' Provide a clear and structured version.'
        : ' Keep it short and concise.') .
    ' Do not add extra information.';

if ($api === 'openai') {
    $url = OPENAI_API_URL;
    $key = OPENAI_API_KEY;
    $model = OPENAI_MODEL;
} else {
    $url = DEEPSEEK_API_URL;
    $key = DEEPSEEK_API_KEY;
    $model = DEEPSEEK_MODEL;
}

$payload = [
    'model' => $model,
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
curl_close($ch);

$data = json_decode($response, true);

echo json_encode([
    'result' => $data['choices'][0]['message']['content'] ?? 'Failed'
]);
