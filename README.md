AI Message Formalizer

A lightweight PHP-based tool that rewrites raw messages into professional, polite,
and human-friendly text.

This tool is designed for founders, support teams, and marketers who frequently
write emails, WhatsApp messages, or support replies and want consistent,
professional tone without chat history clutter.

It supports both OpenAI and DeepSeek, includes a web UI with authentication,
and also works as a Telegram bot using the same backend logic.

--------------------------------------------------
FEATURES
--------------------------------------------------

- Rewrite messages into:
  - Professional Emails
  - Polite WhatsApp messages
  - Support ticket replies

- Choose AI provider:
  - OpenAI
  - DeepSeek

- Clean Bootstrap-based web interface
- Secure login system (session-based)
- Supports multiple users
- Telegram bot support using the same rewrite engine
- Stateless requests (no conversation history)
- Fast and predictable output
- Works on shared hosting or VPS

--------------------------------------------------
TECH STACK
--------------------------------------------------

- PHP 7.4 or higher
- cURL extension enabled
- Bootstrap 5 (CDN)
- OpenAI API (optional)
- DeepSeek API (optional)
- Telegram Bot API (optional)

--------------------------------------------------
INSTALLATION
--------------------------------------------------

1. Clone the repository:

   git clone https://github.com/yourusername/ai-message-formalizer.git

2. Upload the files to your server or VPS.

3. Copy the example config file:

   config.example.php -> config.php

4. Edit config.php:
   - Add API keys
   - Add users with hashed passwords
   - Add Telegram bot token if needed

5. Open the project URL in your browser.

--------------------------------------------------
AUTHENTICATION
--------------------------------------------------

- Session-based authentication
- Passwords stored as hashes
- Secure cookies (HttpOnly, Secure)
- Strict session mode enabled
- rewrite.php protected by auth middleware

--------------------------------------------------
TELEGRAM BOT SETUP
--------------------------------------------------

1. Create a bot using @BotFather on Telegram.
2. Copy the bot token.
3. Add the token to config.php.
4. Upload telegram.php to your server.
5. Set the webhook (one time):

   https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://yourdomain.com/formalizer/telegram.php

6. Send a message to your bot to test.

You can restrict bot usage to specific Telegram user IDs.

--------------------------------------------------
SECURITY NOTES
--------------------------------------------------

- HTTPS is required
- config.php should never be publicly accessible
- rewrite endpoints must always be protected by auth.php
- Restrict Telegram bot access using allowed user IDs
- Do not expose API keys in frontend code

--------------------------------------------------
RECOMMENDED FILES TO IGNORE
--------------------------------------------------

Do not commit these files to GitHub:

- config.php
- .env

Use config.example.php instead.

--------------------------------------------------
LICENSE
--------------------------------------------------

MIT License

Free to use, modify, and distribute.

--------------------------------------------------
AUTHOR
--------------------------------------------------

Nirmal Prajapati
Founder, HostRainbow
