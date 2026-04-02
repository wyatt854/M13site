<?php
session_start();

if (empty($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$username = $user['username'];
$email    = $user['email'];

// Changement de mot de passe
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPw  = $_POST['current_password'] ?? '';
    $newPw      = $_POST['new_password'] ?? '';
    $confirmPw  = $_POST['confirm_password'] ?? '';

    $usersFile = 'users.json';
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
    $userIndex = null;
    foreach ($users as $i => $u) {
        if (strtolower($u['username']) === strtolower($username)) { $userIndex = $i; break; }
    }

    if ($userIndex === null) {
        $error = "Utilisateur introuvable.";
    } elseif (!password_verify($currentPw, $users[$userIndex]['password'])) {
        $error = "Mot de passe actuel incorrect.";
    } elseif (strlen($newPw) < 6) {
        $error = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    } elseif ($newPw !== $confirmPw) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        $users[$userIndex]['password'] = password_hash($newPw, PASSWORD_DEFAULT);
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $success = "Mot de passe mis à jour.";
    }
}

$avatarUrl = "https://api.dicebear.com/9.x/pixel-art/svg?seed=" . urlencode($username);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M13 // PROFIL : <?= htmlspecialchars($username) ?></title>
    <style>
        :root { --green: #33ff00; --dark: #050505; --red: #ff3333; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--dark);
            color: #e0e0e0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                linear-gradient(rgba(51,255,0,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(51,255,0,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }
        header {
            padding: 20px 40px;
            border-bottom: 1px solid var(--green);
            background: rgba(0,0,0,0.9);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .back-btn {
            color: #fff;
            text-decoration: none;
            font-family: monospace;
            border: 1px solid #444;
            padding: 6px 15px;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .back-btn:hover { border-color: var(--green); color: var(--green); }
        .logout-link { color: var(--red); font-family: monospace; font-size: 0.75rem; text-decoration: none; border: 1px solid var(--red); padding: 6px 12px; transition: 0.3s; }
        .logout-link:hover { background: var(--red); color: #000; }

        main {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .profile-card {
            display: flex;
            align-items: center;
            gap: 40px;
            border: 1px solid #222;
            border-left: 4px solid var(--green);
            padding: 40px;
            background: rgba(0,0,0,0.5);
            margin-bottom: 40px;
        }
        .avatar-wrap {
            flex-shrink: 0;
            width: 120px;
            height: 120px;
            border: 2px solid var(--green);
            background: #0a0a0a;
            image-rendering: pixelated;
            overflow: hidden;
            border-radius: 8px;
        }
        .avatar-wrap img {
            width: 100%;
            height: 100%;
            display: block;
            image-rendering: pixelated;
        }
        .profile-info h1 { font-size: 2rem; color: #fff; margin-bottom: 8px; }
        .profile-info .tag { font-family: monospace; color: var(--green); font-size: 0.85rem; margin-bottom: 5px; }
        .profile-info .email { font-family: monospace; color: #555; font-size: 0.8rem; }

        .section-title { font-family: monospace; color: var(--green); font-size: 0.8rem; letter-spacing: 3px; margin-bottom: 20px; border-bottom: 1px solid #1a1a1a; padding-bottom: 10px; text-transform: uppercase; }
        
        .pw-form { background: rgba(0,0,0,0.5); border: 1px solid #1a1a1a; padding: 30px; }
        label { font-size: 0.75rem; color: #555; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; font-family: monospace; }
        input[type="password"] {
            width: 100%;
            background: #000;
            border: 1px solid #222;
            color: var(--green);
            padding: 12px;
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 18px;
            outline: none;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus { border-color: var(--green); }
        button {
            background: var(--green);
            color: #000;
            border: none;
            padding: 13px 30px;
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: bold;
            letter-spacing: 2px;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        button:hover { background: #29cc00; }
        .msg-error { color: var(--red); background: rgba(255,51,51,0.06); border: 1px solid var(--red); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; }
        .msg-success { color: var(--green); background: rgba(51,255,0,0.05); border: 1px solid var(--green); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; }
        footer { text-align: center; font-family: monospace; color: #222; font-size: 0.65rem; padding: 40px; }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="back-btn">< MONITORING</a>
        <div style="font-family:monospace; font-size:0.7rem; color:#333;">PROJECT M13 // DOSSIER PERSONNEL</div>
        <a href="logout.php" class="logout-link">DÉCONNEXION</a>
    </header>

    <main>
        <div class="profile-card">
            <div class="avatar-wrap">
                <img src="<?= $avatarUrl ?>" alt="Avatar pixel de <?= htmlspecialchars($username) ?>">
            </div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($username) ?></h1>
                <div class="tag">> SUJET ENREGISTRÉ</div>
                <div class="email"><?= htmlspecialchars($email) ?></div>
            </div>
        </div>

        <div class="section-title">// Modifier le mot de passe</div>
        <div class="pw-form">
            <?php if ($error): ?><div class="msg-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="msg-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <form method="POST">
                <label>Mot de passe actuel :</label>
                <input type="password" name="current_password" placeholder="••••••••" required>
                <label>Nouveau mot de passe :</label>
                <input type="password" name="new_password" placeholder="Minimum 6 caractères" required>
                <label>Confirmer le nouveau mot de passe :</label>
                <input type="password" name="confirm_password" placeholder="Répète le nouveau mot de passe" required>
                <button type="submit">METTRE À JOUR</button>
            </form>
        </div>
    </main>

    <footer>"On ne peut pas supprimer un souvenir sans en créer une conséquence." — PROJECT M13</footer>
</body>
</html>
