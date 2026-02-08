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

// Session hardening
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $users = APP_USERS;

    if (
        isset($users[$username]) &&
        password_verify($password, $users[$username])
    ) {
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $username;

        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card p-4 shadow" style="width: 360px;">
    <h4 class="mb-3 text-center">Formalizer Login</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input
            class="form-control mb-3"
            name="username"
            placeholder="Username"
            required
            autofocus
        >
        <input
            class="form-control mb-3"
            type="password"
            name="password"
            placeholder="Password"
            required
        >
        <button class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>
</html>
