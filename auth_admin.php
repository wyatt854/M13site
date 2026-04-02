<?php
session_start();

define('ADMIN_USER', 'Siroxtag');
define('ADMIN_PASS_HASH', password_hash('100124Sw@', PASSWORD_DEFAULT));

// Si soumission du formulaire de login admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if ($u === ADMIN_USER && password_verify($p, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = "IDENTIFIANTS INVALIDES. ACCÈS REFUSÉ.";
    }
}

// Si pas connecté en tant qu'admin, afficher le formulaire de login
if (empty($_SESSION['admin_logged_in'])) {
    $error = $login_error ?? '';
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>M13 // ACCÈS RESTREINT</title>
    <style>
        :root { --green: #00ff41; --dark: #0a0a0a; --red: #ff3333; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--dark);
            color: var(--green);
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(0deg, rgba(0,255,65,0.03) 0px, transparent 1px, transparent 3px);
            pointer-events: none;
        }
        .login-box {
            border: 1px solid var(--green);
            padding: 50px 60px;
            background: rgba(0,255,65,0.03);
            box-shadow: 0 0 40px rgba(0,255,65,0.1);
            width: 400px;
        }
        .login-box h1 {
            font-size: 1rem;
            letter-spacing: 4px;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            font-size: 0.7rem;
            color: #555;
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: 2px;
        }
        label { font-size: 0.75rem; color: #888; display: block; margin-bottom: 6px; letter-spacing: 1px; }
        input[type="text"], input[type="password"] {
            width: 100%;
            background: #000;
            border: 1px solid #333;
            color: var(--green);
            padding: 12px;
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.3s;
        }
        input:focus { border-color: var(--green); }
        button {
            width: 100%;
            background: var(--green);
            color: #000;
            border: none;
            padding: 14px;
            font-family: monospace;
            font-size: 0.9rem;
            font-weight: bold;
            letter-spacing: 3px;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        button:hover { background: #008f11; }
        .error {
            color: var(--red);
            font-size: 0.8rem;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid var(--red);
            padding: 10px;
            background: rgba(255,51,51,0.05);
        }
        .footer-hint { font-size: 0.65rem; color: #333; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div style="font-size:0.7rem; color:#333; letter-spacing:3px;">PROJECT M13</div>
    <div class="login-box">
        <h1>ACCÈS RESTREINT</h1>
        <div class="subtitle">AUTHENTIFICATION REQUISE</div>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>IDENTIFIANT :</label>
            <input type="text" name="username" autocomplete="off" required>
            <label>MOT DE PASSE :</label>
            <input type="password" name="password" required>
            <button type="submit" name="admin_login">CONNEXION SÉCURISÉE</button>
        </form>
        <div class="footer-hint">"On ne peut pas supprimer un souvenir sans en créer une conséquence."</div>
    </div>
</body>
</html>
    <?php
    exit;
}
