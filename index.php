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
require 'auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Universal Message Formalizer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
#loader { display: none; }
footer {
    background-color: #1a1a1a;
    color: #fff;
    margin-top: auto;
    padding: 2rem 0;
    border-top: 1px solid #333;
    font-size: 0.9rem;
}

footer a {
    color: #0d6efd;
    text-decoration: none;
    transition: color 0.3s;
}

footer a:hover {
    color: #0a58ca;
    text-decoration: underline;
}

html, body {
    height: 100%;
}

body {
    display: flex;
    flex-direction: column;
}

.container:last-of-type {
    flex: 1;
}
</style>
</head>
<body class="bg-light">
<div class="container">
    <div class="wrapper text-center bg-body rounded-3 shadow mt-2 mb-4 p-3">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
    </div>
</div>
<div class="container py-5">
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between mb-3">
            <h4>Professional Message Rewriter</h4>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <select id="api" class="form-select">
                    <option value="deepseek">DeepSeek</option>
                    <option value="openai">OpenAI</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="mode" class="form-select">
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="ticket">Support Ticket</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="length" class="form-select">
                    <option value="short">Short</option>
                    <option value="detailed">Detailed</option>
                </select>
            </div>
        </div>

        <textarea id="text" class="form-control mb-3" rows="5" placeholder="Paste your message here"></textarea>

        <div class="d-flex gap-2">
            <button onclick="rewrite()" class="btn btn-primary">Rewrite</button>
            <button onclick="copyText()" class="btn btn-outline-secondary">Copy</button>
        </div>

        <div id="loader" class="mt-3">
            <div class="spinner-border text-primary"></div> Processing...
        </div>

        <div id="output" class="mt-3 border rounded p-3 bg-white"></div>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="fw-bold mb-2">AI Message Formalizer</h6>
                <p class="small  mb-0">Professional message rewriting tool powered by AI</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="https://hostrainbow.in" class="text-decoration-none text-primary me-3">HostRainbow</a>
                <a href="https://bwinnirmal.github.io/Nirmal-Prajapati/" class="text-decoration-none text-primary">Portfolio</a>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0 small ">&copy; 2026 Nirmal Prajapati. All rights reserved.</p>
            <p class="mb-0 small ">HostRainbow &mdash; Rainbow Web Solutions</p>
        </div>
    </div>
</footer>

<script>
function rewrite() {
    document.getElementById('loader').style.display = 'block';
    document.getElementById('output').innerText = '';

    const data = new URLSearchParams({
        text: text.value,
        api: api.value,
        mode: mode.value,
        length: length.value
    });

    fetch('rewrite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: data.toString()
    })
    .then(r => r.json())
    .then(d => {
        loader.style.display = 'none';
        output.innerText = d.result || d.error;
    });
}

function copyText() {
    navigator.clipboard.writeText(output.innerText);
}
</script>

</body>
</html>
