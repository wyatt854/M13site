<?php
session_start();
$dataFile = 'data.json';
$entities = [];
$nav_pages = [];

if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
    if (isset($data['personnages'])) { $entities = $data['personnages']; }
    if (isset($data['pages'])) { $nav_pages = $data['pages']; }
}

// Infos utilisateur connecté
$currentUser = null;
if (!empty($_SESSION['user_logged_in'])) {
    $currentUser = $_SESSION['user'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T29H3VJD');</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interface de monitoring du PROJECT M13.">
    <title>PROJECT M13 | MONITORING SYSTEM</title>
    
    <style>
        :root { --bg: #050505; --primary: #ffffff; --accent: #ff3333; --secondary: #33ff00; }
        body { background-color: var(--bg); color: var(--primary); font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; overflow-x: hidden; min-height: 100vh; display: flex; flex-direction: column; }
        .grid-bg { position: fixed; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(rgba(51, 255, 0, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(51, 255, 0, 0.05) 1px, transparent 1px); background-size: 60px 60px; z-index: -1; transform: perspective(500px) rotateX(60deg); animation: grid-move 40s linear infinite; }
        @keyframes grid-move { from { transform: perspective(500px) rotateX(60deg) translateY(0); } to { transform: perspective(500px) rotateX(60deg) translateY(60px); } }
        
        header { padding: 20px 30px; background: rgba(0,0,0,0.9); z-index: 10; display: flex; flex-direction: column; gap: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .header-top { display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        h1 { font-size: 1.2rem; letter-spacing: 2px; text-transform: uppercase; margin: 0; color: #fff; }
        
        /* USER BADGE */
        .user-zone { display: flex; align-items: center; gap: 12px; font-family: monospace; font-size: 0.75rem; }
        .user-avatar { width: 36px; height: 36px; border-radius: 4px; border: 1px solid var(--secondary); image-rendering: pixelated; }
        .user-name { color: var(--secondary); }
        .user-links a { color: #888; text-decoration: none; margin-left: 10px; transition: color 0.2s; }
        .user-links a:hover { color: #fff; }
        .btn-login { 
            border: 1px solid var(--secondary); color: var(--secondary); 
            padding: 6px 14px; text-decoration: none; font-family: monospace; 
            font-size: 0.75rem; transition: 0.3s; 
        }
        .btn-login:hover { background: var(--secondary); color: #000; }

        /* MENU DYNAMIQUE */
        .m13-nav { display: flex; gap: 20px; border-top: 1px solid rgba(51, 255, 0, 0.2); padding-top: 15px; font-family: monospace; font-size: 0.85rem; flex-wrap: wrap; }
        .m13-nav a { color: #fff; text-decoration: none; transition: color 0.3s; text-transform: uppercase; }
        .m13-nav a:hover, .m13-nav a.active { color: var(--secondary); text-shadow: 0 0 8px rgba(51,255,0,0.5); }
        
        main { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 20px; z-index: 5; }
        .main-nav { display: flex; gap: 15px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; justify-content: center; }
        
        /* CARTES PERSONNAGES */
        .char-card { 
            width: 180px; height: 450px; border: 1px solid rgba(255,255,255,0.1); 
            position: relative; overflow: hidden; text-decoration: none !important; 
            color: #ffffff !important; display: flex; flex-direction: column; 
            justify-content: flex-end; padding: 15px; transition: all 0.3s; 
            background-size: cover; background-position: center top; background-color: #111; 
        }
        .char-card::after { 
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            background: linear-gradient(to top, rgba(0,0,0,0.85) 10%, transparent 70%); z-index: 1; 
        }
        .char-card h2 { 
            font-size: 1.8rem; margin: 0; z-index: 2; writing-mode: vertical-rl; 
            transform: rotate(180deg); color: #ffffff; text-shadow: 0 0 10px rgba(0,0,0,0.5); transition: color 0.3s; 
        }
        .char-card span { font-size: 0.7rem; z-index: 2; font-family: monospace; margin-bottom: 5px; transition: color 0.3s; }
        .char-card:hover { transform: scale(1.02); z-index: 10; }

        /* MESSAGE si aucun personnage */
        .empty-state { text-align: center; font-family: monospace; color: #333; }
        .empty-state h2 { font-size: 1.5rem; margin-bottom: 10px; }

        @media (max-width: 900px) { 
            .main-nav { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; } 
            .char-card { width: auto; height: 250px; } 
            .char-card h2 { writing-mode: horizontal-tb; transform: none; font-size: 1.5rem; } 
        }
        footer { padding: 15px; text-align: center; font-family: monospace; font-size: 0.6rem; color: rgba(255,255,255,0.3); border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.8); }
    </style>
</head>
<body>
    <div class="grid-bg" aria-hidden="true"></div>

    <header role="banner">
        <div class="header-top">
            <h1>M13 // MONITORING SYSTEM</h1>
            <div class="user-zone">
                <?php if ($currentUser): ?>
                    <img 
                        class="user-avatar" 
                        src="https://api.dicebear.com/9.x/pixel-art/svg?seed=<?= urlencode($currentUser['username']) ?>" 
                        alt="Avatar de <?= htmlspecialchars($currentUser['username']) ?>"
                    >
                    <div>
                        <div class="user-name"><?= htmlspecialchars($currentUser['username']) ?></div>
                        <div class="user-links">
                            <a href="profile.php">profil</a>
                            <a href="logout.php">déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn-login">[ CONNEXION ]</a>
                    <a href="register.php" class="btn-login" style="border-color:#fff; color:#fff;">[ S'INSCRIRE ]</a>
                <?php endif; ?>
                <div style="font-family: monospace; font-size: 0.7rem; color: var(--secondary);" aria-live="polite">
                    LIAISON_ÉTABLIE // ACCÈS_NIVEAU_4
                </div>
            </div>
        </div>
        <nav class="m13-nav">
            <a href="index.php" class="active">> MONITORING</a>
            <?php foreach ($nav_pages as $p): ?>
                <a href="page.php?title=<?= urlencode($p['menu']) ?>">> <?= htmlspecialchars($p['menu']) ?></a>
            <?php endforeach; ?>
            <a href="blog.php">> LOGS</a>
            <a href="shop.php">> BOUTIQUE</a>
        </nav>
    </header>

    <main>
        <?php if (empty($entities)): ?>
            <div class="empty-state">
                <h2 style="color:#333;">// AUCUN SUJET DÉTECTÉ</h2>
                <p style="color:#222;">Le système M13 n'a archivé aucune entité.</p>
            </div>
        <?php else: ?>
            <nav class="main-nav">
                <?php foreach ($entities as $entity): 
                    // Compatibilité : la clé peut être 'nom' (data.json) ou 'name' (entite.php)
                    $displayName = $entity['nom'] ?? $entity['name'] ?? 'INCONNU';
                    $displayTag  = $entity['tag'] ?? 'ID_??';
                    $displayImg  = $entity['img'] ?? '';
                    $displayColor = $entity['color'] ?? '#33ff00';
                    // L'ID pour entite.php est la position dans le tableau (index numérique)
                    // On utilise le nom en minuscule comme slug
                    $slug = strtolower(str_replace(' ', '_', $displayName));
                ?>
                <a href="entite.php?id=<?= urlencode($slug) ?>" class="char-card" 
                   style="background-image: url('<?= htmlspecialchars($displayImg) ?>');"
                   onmouseover="this.style.borderColor='<?= $displayColor ?>'; this.style.boxShadow='0 0 25px <?= $displayColor ?>40'; this.querySelector('h2').style.color='<?= $displayColor ?>';" 
                   onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.boxShadow='none'; this.querySelector('h2').style.color='#ffffff';">
                    <span style="color: <?= htmlspecialchars($displayColor) ?>;"><?= htmlspecialchars($displayTag) ?></span>
                    <h2><?= htmlspecialchars($displayName) ?></h2>
                </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
    </main>

    <footer role="contentinfo">
        <p>© 2026 PROJECT M13 - SURVEILLANCE BIOMÉTRIQUE ET MÉMORIELLE. TOUTE INTERFÉRENCE SERA PUNIE.</p>
        <p style="color: #666; margin-top: 5px;">"On ne peut pas supprimer un souvenir sans en créer une conséquence."</p>
    </footer>
</body>
</html>
