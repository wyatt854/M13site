<?php
session_start();

if (!empty($_SESSION['user_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password) {
        $error = "Tous les champs sont requis.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Le pseudo doit faire entre 3 et 20 caractères.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $usersFile = 'users.json';
        $users = [];
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true) ?: [];
        }
        // Vérifier doublon
        foreach ($users as $u) {
            if (strtolower($u['username']) === strtolower($username)) { $error = "Ce pseudo est déjà pris."; break; }
            if (strtolower($u['email']) === strtolower($email)) { $error = "Cet email est déjà utilisé."; break; }
        }
        if (!$error) {
            $users[] = [
                'username'   => $username,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('d/m/Y H:i'),
            ];
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = "Compte créé ! Tu peux maintenant te connecter.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M13 // INSCRIPTION</title>
    <style>
        :root { --green: #33ff00; --dark: #050505; --red: #ff3333; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--dark);
            color: #fff;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                linear-gradient(rgba(51,255,0,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(51,255,0,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }
        .box {
            position: relative;
            z-index: 1;
            border: 1px solid var(--green);
            padding: 50px 60px;
            background: rgba(0,0,0,0.8);
            box-shadow: 0 0 40px rgba(51,255,0,0.08);
            width: 100%;
            max-width: 440px;
        }
        .logo { font-size: 0.75rem; color: #333; letter-spacing: 4px; text-align: center; margin-bottom: 30px; }
        h1 { font-size: 1rem; letter-spacing: 4px; text-align: center; margin-bottom: 5px; color: var(--green); }
        .sub { font-size: 0.65rem; color: #444; text-align: center; margin-bottom: 35px; letter-spacing: 2px; }
        
        /* Preview avatar */
        .avatar-preview { text-align: center; margin-bottom: 25px; }
        .avatar-preview img { width: 64px; height: 64px; image-rendering: pixelated; border: 2px solid var(--green); border-radius: 6px; background: #111; }
        .avatar-preview p { font-size: 0.65rem; color: #444; margin-top: 8px; }

        label { font-size: 0.7rem; color: #666; display: block; margin-bottom: 5px; letter-spacing: 1px; text-transform: uppercase; }
        input {
            width: 100%;
            background: #000;
            border: 1px solid #222;
            color: var(--green);
            padding: 11px 14px;
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 18px;
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
            font-size: 0.85rem;
            font-weight: bold;
            letter-spacing: 3px;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
            margin-top: 5px;
        }
        button:hover { background: #29cc00; }
        .msg-error { color: var(--red); background: rgba(255,51,51,0.06); border: 1px solid var(--red); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; text-align: center; }
        .msg-success { color: var(--green); background: rgba(51,255,0,0.05); border: 1px solid var(--green); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; text-align: center; }
        .footer-link { text-align: center; margin-top: 25px; font-size: 0.75rem; color: #444; }
        .footer-link a { color: var(--green); text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <div class="logo">PROJECT M13</div>
        <h1>CRÉER UN COMPTE</h1>
        <div class="sub">ENREGISTREMENT DANS LE SYSTÈME</div>

        <div class="avatar-preview">
            <img id="avatar-img" src="https://api.dicebear.com/9.x/pixel-art/svg?seed=utilisateur" alt="Avatar">
            <p>Ton avatar pixel sera généré depuis ton pseudo</p>
        </div>

        <?php if ($error): ?><div class="msg-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?>
            <div class="msg-success"><?= htmlspecialchars($success) ?></div>
            <div class="footer-link"><a href="login.php">[ SE CONNECTER ]</a></div>
        <?php else: ?>
        <form method="POST">
            <label>Pseudo :</label>
            <input type="text" name="username" id="username-input" placeholder="Ton identifiant M13" maxlength="20" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            <label>Email :</label>
            <input type="email" name="email" placeholder="ton@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <label>Mot de passe :</label>
            <input type="password" name="password" placeholder="Minimum 6 caractères" required>
            <label>Confirmer le mot de passe :</label>
            <input type="password" name="confirm" placeholder="Répète le mot de passe" required>
            <button type="submit">S'INSCRIRE</button>
        </form>
        <div class="footer-link">Déjà un compte ? <a href="login.php">[ SE CONNECTER ]</a></div>
        <?php endif; ?>
    </div>

    <script>
        const usernameInput = document.getElementById('username-input');
        const avatarImg = document.getElementById('avatar-img');
        let timeout;
        usernameInput?.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                const val = usernameInput.value.trim() || 'utilisateur';
                avatarImg.src = 'https://api.dicebear.com/9.x/pixel-art/svg?seed=' + encodeURIComponent(val);
            }, 400);
        });
    </script>
</body>
</html>
