<?php
session_start();

if (!empty($_SESSION['user_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // pseudo ou email
    $password   = $_POST['password'] ?? '';

    $usersFile = 'users.json';
    $users = [];
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true) ?: [];
    }

    $found = null;
    foreach ($users as $u) {
        if (strtolower($u['username']) === strtolower($identifier) || strtolower($u['email']) === strtolower($identifier)) {
            if (password_verify($password, $u['password'])) {
                $found = $u;
                break;
            }
        }
    }

    if ($found) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user'] = [
            'username' => $found['username'],
            'email'    => $found['email'],
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M13 // CONNEXION</title>
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
            max-width: 420px;
        }
        .logo { font-size: 0.75rem; color: #333; letter-spacing: 4px; text-align: center; margin-bottom: 30px; }
        h1 { font-size: 1rem; letter-spacing: 4px; text-align: center; margin-bottom: 5px; color: var(--green); }
        .sub { font-size: 0.65rem; color: #444; text-align: center; margin-bottom: 40px; letter-spacing: 2px; }

        label { font-size: 0.7rem; color: #666; display: block; margin-bottom: 5px; letter-spacing: 1px; text-transform: uppercase; }
        input {
            width: 100%;
            background: #000;
            border: 1px solid #222;
            color: var(--green);
            padding: 11px 14px;
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
            font-size: 0.85rem;
            font-weight: bold;
            letter-spacing: 3px;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        button:hover { background: #29cc00; }
        .msg-error { color: var(--red); background: rgba(255,51,51,0.06); border: 1px solid var(--red); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; text-align: center; }
        .footer-link { text-align: center; margin-top: 25px; font-size: 0.75rem; color: #444; }
        .footer-link a { color: var(--green); text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
        .quote { font-size: 0.65rem; color: #222; text-align: center; margin-top: 35px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="logo">PROJECT M13</div>
        <h1>CONNEXION</h1>
        <div class="sub">AUTHENTIFICATION DU SUJET</div>

        <?php if ($error): ?><div class="msg-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <label>Pseudo ou Email :</label>
            <input type="text" name="identifier" placeholder="Ton identifiant" autocomplete="username" value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>" required>
            <label>Mot de passe :</label>
            <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
            <button type="submit">SE CONNECTER</button>
        </form>

        <div class="footer-link">Pas encore de compte ? <a href="register.php">[ S'INSCRIRE ]</a></div>
        <div class="quote">"On ne peut pas supprimer un souvenir sans en créer une conséquence."</div>
    </div>
</body>
</html>
