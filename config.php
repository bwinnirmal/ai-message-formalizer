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

// ===== AUTH =====
// ===== USERS =====

define('APP_USERS', [
    'nirmal' => '$2y$10$p/8oF94uuUpweYUMMnFHieri3b0OAgdalw.0Ruv/LOV4t3fMT5CRm',
    'support' => '$2y$10$p/8oF94uuUpweYUMMnFHieri3b0OAgdalw.0Ruv/LOV4t3fMT5CRm',
    'admin' => '$2y$10$p/8oF94uuUpweYUMMnFHieri3b0OAgdalw.0Ruv/LOV4t3fMT5CRm'
]);


// Generate hashes once using: generatePasshash.php
// password_hash('YourPasswordHere', PASSWORD_DEFAULT);

// ===== API KEYS =====
define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY');
define('DEEPSEEK_API_KEY', 'YOUR_DEEPSEEK_API_KEY');

// ===== API ENDPOINTS =====
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
define('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions');

// ===== MODELS =====
define('OPENAI_MODEL', 'gpt-4o-mini');
define('DEEPSEEK_MODEL', 'deepseek-chat');

// ===== TELEGRAM =====
define('TELEGRAM_BOT_TOKEN', 'YOUR_TELEGRAM_BOT_TOKEN');
