<?php
/*
--------------------------------------------------------------------------
| AI Message Formalizer
--------------------------------------------------------------------------
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
--------------------------------------------------------------------------
*/
require 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Universal Message Formalizer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root {
    --bg: #f3f4f7;
    --surface: #ffffff;
    --surface-soft: #f8fafc;
    --text: #101828;
    --muted: #667085;
    --border: #e4e7ec;
    --primary: #2f6cf4;
    --primary-dark: #1f52c8;
}

* {
    box-sizing: border-box;
}

html,
body {
    min-height: 100%;
}

body {
    margin: 0;
    background: linear-gradient(180deg, #f8f9fb 0%, var(--bg) 100%);
    color: var(--text);
    font-family: Inter, "Segoe UI", system-ui, -apple-system, sans-serif;
    display: flex;
    flex-direction: column;
}

.app-shell {
    width: min(1100px, 94vw);
    margin: 1rem auto 2rem;
}

.topbar,
.main-card,
footer {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
}

.topbar {
    padding: 1rem 1.2rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.brand-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
}

.brand-sub {
    margin: 0;
    color: var(--muted);
    font-size: 0.85rem;
}

.main-card {
    padding: 1rem;
}

.grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 0.75rem;
}

.field {
    grid-column: span 3;
}

.field.wide {
    grid-column: span 6;
}

.field.full {
    grid-column: 1 / -1;
}

label {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 0.35rem;
    display: block;
}

.form-control,
.form-select {
    border-radius: 10px;
    border-color: var(--border);
    font-size: 0.92rem;
    min-height: 42px;
}

textarea.form-control {
    min-height: 170px;
    resize: vertical;
}

.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
}

.btn {
    border-radius: 10px;
}

.btn-primary {
    background: var(--primary);
    border-color: var(--primary);
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

.quick-tags {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.quick-tag {
    border: 1px solid var(--border);
    background: var(--surface-soft);
    color: var(--muted);
    border-radius: 100px;
    padding: 0.32rem 0.7rem;
    font-size: 0.75rem;
    cursor: pointer;
}

.quick-tag:hover {
    border-color: var(--primary);
    color: var(--primary);
}

#output {
    white-space: pre-wrap;
    min-height: 140px;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.9rem;
    background: #fff;
}

#loader {
    display: none;
    color: var(--muted);
    align-items: center;
    gap: 0.5rem;
}

footer {
    width: min(1100px, 94vw);
    margin: 0 auto 1.25rem;
    padding: 1rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 0.7rem;
    color: var(--muted);
}

footer a {
    text-decoration: none;
}

@media (max-width: 992px) {
    .field,
    .field.wide {
        grid-column: span 6;
    }
}

@media (max-width: 640px) {
    .app-shell {
        width: 96vw;
    }

    .field,
    .field.wide {
        grid-column: 1 / -1;
    }

    .main-card {
        padding: 0.9rem;
    }

    .brand-sub {
        font-size: 0.8rem;
    }
}
</style>
</head>
<body>
<main class="app-shell">
    <section class="topbar">
        <div>
            <h1 class="brand-title">AI Message Studio</h1>
            <p class="brand-sub">Minimal, responsive and multi-model writing assistant</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small text-secondary">Signed in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </section>

    <section class="main-card">
        <div class="grid">
            <div class="field">
                <label for="api">Provider</label>
                <select id="api" class="form-select" onchange="syncModels()">
                    <option value="deepseek">DeepSeek</option>
                    <option value="openai">OpenAI</option>
                </select>
            </div>

            <div class="field">
                <label for="model">Model</label>
                <select id="model" class="form-select"></select>
            </div>

            <div class="field">
                <label for="mode">Use case</label>
                <select id="mode" class="form-select">
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="ticket">Support Ticket</option>
                </select>
            </div>

            <div class="field">
                <label for="action">Action</label>
                <select id="action" class="form-select">
                    <option value="rewrite">Rewrite</option>
                    <option value="summarize">Summarize</option>
                    <option value="bulletize">Bullet Points</option>
                    <option value="next_step">Add Next Step</option>
                </select>
            </div>

            <div class="field">
                <label for="tone">Tone</label>
                <select id="tone" class="form-select">
                    <option value="professional">Professional</option>
                    <option value="friendly">Friendly</option>
                    <option value="assertive">Assertive</option>
                    <option value="empathetic">Empathetic</option>
                </select>
            </div>

            <div class="field">
                <label for="length">Length</label>
                <select id="length" class="form-select">
                    <option value="short">Short</option>
                    <option value="detailed">Detailed</option>
                </select>
            </div>

            <div class="field">
                <label for="language">Language</label>
                <input id="language" class="form-control" value="English" placeholder="English, Hindi, etc.">
            </div>

            <div class="field wide">
                <label for="api_key">Use your own API key (optional)</label>
                <input id="api_key" type="password" class="form-control" placeholder="Leave empty to use server-configured key">
            </div>

            <div class="field full">
                <label for="custom_command">Custom command (optional)</label>
                <input id="custom_command" class="form-control" placeholder="Example: Keep legal-safe wording and add a positive close.">
            </div>

            <div class="field full">
                <label for="text">Input message</label>
                <textarea id="text" class="form-control" placeholder="Paste your rough draft or customer message here..."></textarea>
            </div>

            <div class="field full">
                <div class="quick-tags" role="group" aria-label="quick prompts">
                    <button class="quick-tag" onclick="insertTemplate('followup')" type="button">Follow-up template</button>
                    <button class="quick-tag" onclick="insertTemplate('apology')" type="button">Apology template</button>
                    <button class="quick-tag" onclick="insertTemplate('sales')" type="button">Sales reply template</button>
                </div>
            </div>

            <div class="field full">
                <div class="actions">
                    <button onclick="rewrite()" class="btn btn-primary">Generate</button>
                    <button onclick="copyText()" class="btn btn-outline-secondary">Copy</button>
                    <button onclick="swapText()" class="btn btn-outline-secondary">Use Output as Input</button>
                    <button onclick="clearAll()" class="btn btn-outline-danger">Clear</button>
                </div>
            </div>

            <div class="field full">
                <div id="loader">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <span>Generating professional output...</span>
                </div>
            </div>

            <div class="field full">
                <label for="output">Output</label>
                <div id="output"></div>
            </div>
        </div>
    </section>
</main>

<footer>
    <span>&copy; 2026 Nirmal Prajapati • HostRainbow</span>
    <span>
        <a href="https://hostrainbow.in" class="me-3">HostRainbow</a>
        <a href="https://bwinnirmal.github.io/Nirmal-Prajapati/">Portfolio</a>
    </span>
</footer>

<script>
const modelCatalog = {
    deepseek: ['deepseek-chat', 'deepseek-reasoner'],
    openai: ['gpt-4o-mini', 'gpt-4.1-mini', 'gpt-4o']
};

const templates = {
    followup: 'Hi [Name], just following up on this request. Please let me know the latest update and expected timeline.',
    apology: 'Hi [Name], we sincerely apologize for the inconvenience. We are actively working on a fix and will share an update shortly.',
    sales: 'Hi [Name], thanks for reaching out. Here is a concise overview of how our solution can help your team and next steps to get started.'
};

function syncModels() {
    const provider = document.getElementById('api').value;
    const modelSelect = document.getElementById('model');
    modelSelect.innerHTML = '';

    (modelCatalog[provider] || []).forEach((modelName) => {
        const option = document.createElement('option');
        option.value = modelName;
        option.textContent = modelName;
        modelSelect.appendChild(option);
    });
}

function insertTemplate(type) {
    if (!templates[type]) return;
    const input = document.getElementById('text');
    input.value = templates[type];
    input.focus();
}

function clearAll() {
    document.getElementById('text').value = '';
    document.getElementById('output').innerText = '';
    document.getElementById('custom_command').value = '';
}

function swapText() {
    const output = document.getElementById('output').innerText.trim();
    if (!output) return;
    document.getElementById('text').value = output;
}

function rewrite() {
    const text = document.getElementById('text').value.trim();
    if (!text) {
        document.getElementById('output').innerText = 'Please add input text first.';
        return;
    }

    document.getElementById('loader').style.display = 'inline-flex';
    document.getElementById('output').innerText = '';

    const data = new URLSearchParams({
        text,
        api: document.getElementById('api').value,
        model: document.getElementById('model').value,
        mode: document.getElementById('mode').value,
        action: document.getElementById('action').value,
        tone: document.getElementById('tone').value,
        length: document.getElementById('length').value,
        language: document.getElementById('language').value,
        api_key: document.getElementById('api_key').value,
        custom_command: document.getElementById('custom_command').value
    });

    fetch('rewrite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: data.toString()
    })
    .then(async (response) => {
        const raw = await response.text();
        let payload;

        try {
            payload = JSON.parse(raw);
        } catch (_) {
            payload = { error: `Unexpected server response (HTTP ${response.status}). ${raw.slice(0, 220)}` };
        }

        document.getElementById('output').innerText = payload.result || payload.error || 'Unknown response';
    })
    .catch((err) => {
        document.getElementById('output').innerText = `Request failed: ${err.message}`;
    })
    .finally(() => {
        document.getElementById('loader').style.display = 'none';
    });
}

function copyText() {
    const out = document.getElementById('output').innerText;
    if (!out) return;

    navigator.clipboard.writeText(out)
        .then(() => {
            const original = document.getElementById('output').innerText;
            document.getElementById('output').innerText = 'Copied to clipboard.\n\n' + original;
        })
        .catch(() => {
            document.getElementById('output').innerText = 'Unable to copy automatically. Please select and copy manually.\n\n' + out;
        });
}

syncModels();
</script>
</body>
</html>
