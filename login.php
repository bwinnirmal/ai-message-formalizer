<?php
/*
--------------------------------------------------------------------------
| AI Message Formalizer
--------------------------------------------------------------------------
*/
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset(APP_USERS[$username]) && password_verify($password, APP_USERS[$username])) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • AI Message Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2f6cf4;
            --primary-dark: #1f52c8;
            --border: #e4e7ec;
            --muted: #667085;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, "Segoe UI", system-ui, sans-serif;
            background: radial-gradient(circle at top left, #eef4ff, #f8f9fb 35%, #f3f4f7 100%);
            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .login-shell {
            width: min(960px, 100%);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            box-shadow: 0 20px 40px rgba(16, 24, 40, 0.06);
        }

        .promo {
            background: linear-gradient(135deg, #1f52c8, #2f6cf4);
            color: #fff;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .promo h1 {
            font-size: 1.6rem;
            margin-bottom: 0.8rem;
            font-weight: 700;
        }

        .promo p {
            opacity: 0.95;
            line-height: 1.6;
            margin-bottom: 1.2rem;
        }

        .promo ul {
            margin: 0;
            padding-left: 1rem;
            opacity: 0.95;
        }

        .form-side {
            padding: 2rem;
        }

        .form-title {
            margin-bottom: 0.2rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #101828;
        }

        .form-sub {
            color: var(--muted);
            margin-bottom: 1.25rem;
            font-size: 0.92rem;
        }

        .form-control {
            border-color: var(--border);
            border-radius: 11px;
            min-height: 44px;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 11px;
            min-height: 44px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .security-note {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 0.9rem;
        }

        @media (max-width: 860px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .promo {
                padding: 1.3rem;
            }

            .promo h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <section class="promo">
            <h1>AI Message Studio</h1>
            <p>Professional message rewriting for support, sales, and operations teams.</p>
            <ul>
                <li>Multiple AI providers & models</li>
                <li>Action, tone, language controls</li>
                <li>No-database history for quick reuse</li>
            </ul>
        </section>

        <section class="form-side">
            <h2 class="form-title">Sign in</h2>
            <p class="form-sub">Use your account credentials to continue.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <label class="form-label small text-secondary">Username</label>
                <input class="form-control mb-3" name="username" placeholder="Enter username" required autofocus>

                <label class="form-label small text-secondary">Password</label>
                <input class="form-control mb-3" type="password" name="password" placeholder="Enter password" required>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            <p class="security-note mb-0">Session security is enabled (strict mode + HttpOnly cookie settings).</p>
        </section>
    </div>
</body>
</html>
