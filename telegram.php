<?php
/*
|--------------------------------------------------------------------------
| AI Message Formalizer
|--------------------------------------------------------------------------
| Copyright (c) 2026 Nirmal Prajapati
|
| Developed by: Nirmal Prajapati
| Company: HostRainbow (Rainbow Web Solutions)
| Website: https://hostrainbow.in
|
| Description:
| A lightweight PHP-based tool to rewrite emails, WhatsApp messages,
| and support replies into a professional and human-friendly tone
| using AI (OpenAI / DeepSeek).
|
| License: MIT
| This project is free to use, modify, and distribute.
|--------------------------------------------------------------------------
*/

require 'config.php';

// Read webhook payload
$update = json_decode(file_get_contents('php://input'), true);
if (!$update || !isset($update['message'])) {
    exit;
}

// Extract user and message
$userId = $update['message']['from']['id'] ?? 0;
$text   = trim($update['message']['text'] ?? '');
$chatId = $update['message']['chat']['id'] ?? 0;

// Allow only specific Telegram users
$allowedUsers = [
    123456789 // replace with your Telegram user ID
];

if (!in_array($userId, $allowedUsers, true)) {
    exit;
}

// Ignore empty messages or commands like /start
if ($text === '' || strpos($text, '/') === 0) {
    exit;
}

// Build AI request
$payload = [
    'model' => DEEPSEEK_MODEL,
    'messages' => [
        [
            'role' => 'system',
            'content' =>
                'Rewrite the message into a professional, polite, and clear tone.
                 Keep it human.
                 Do not add extra information.
                 Output only the rewritten message.'
        ],
        [
            'role' => 'user',
            'content' => $text
        ]
    ],
    'temperature' => 0.3
];

// Send to DeepSeek
$ch = curl_init(DEEPSEEK_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . DEEPSEEK_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 20
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$reply = $data['choices'][0]['message']['content'] ?? 'Unable to rewrite message.';

// Send reply back to Telegram
file_get_contents(
    'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN .
    '/sendMessage?chat_id=' . urlencode($chatId) .
    '&text=' . urlencode($reply)
);

exit;
