<?php
session_start();
$dataFile = 'data.json';
$data = ['personnages' => []];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true) ?: $data;
}

// Récupération de l'ID (slug en minuscule)
$id = isset($_GET['id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']) : null;

// Recherche du personnage dans data.json par slug (nom en minuscule)
$entity = null;
foreach ($data['personnages'] as $p) {
    $slug = strtolower(str_replace(' ', '_', $p['nom'] ?? $p['name'] ?? ''));
    if ($slug === $id) {
        $entity = $p;
        break;
    }
}

if (!$id || !$entity) {
    die("<div style='background:#050505; color:#ff3333; font-family:monospace; padding:50px; text-align:center;'><h1>ERREUR 404</h1><p>DONNÉES MÉMORIELLES INTROUVABLES OU CORROMPUES.</p><a href='index.php' style='color:#fff;'>RETOUR AU MONITORING</a></div>");
}

// Normalisation des champs (compatibilité admin ancien/nouveau)
$name    = $entity['nom']    ?? $entity['name']   ?? 'INCONNU';
$tag     = $entity['tag']    ?? 'ID_??';
$color   = $entity['color']  ?? '#4A90E2';
$img     = $entity['img']    ?? '';
$status  = $entity['status'] ?? 'SUJET ACTIF';
$title   = $entity['title']  ?? strtoupper($name);
$desc1   = $entity['desc1']  ?? $entity['description'] ?? '';
$desc2   = $entity['desc2']  ?? '';
$scenario = $entity['description'] ?? '';
// Si desc1/desc2 existent, le scénario est dans 'scenario' ou 'description'
if (!empty($entity['desc1'])) {
    $scenario = $entity['scenario'] ?? $entity['description'] ?? '';
}

$nav_pages = $data['pages'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dossier analytique du Sujet <?= htmlspecialchars($name) ?>. Surveillance mémorielle - Project M13.">
    
    <link rel="preload" as="image" href="<?= htmlspecialchars($img) ?>" fetchpriority="high">
    
    <title>DOSSIER : <?= htmlspecialchars($name) ?> | PROJECT M13</title>
    
    <style>
        :root {
            --bg: #050505;
            --primary: #e0e0e0;
            --accent: #ff3333;
            --entity-color: <?= htmlspecialchars($color) ?>;
            --glass: rgba(255, 255, 255, 0.03);
        }

        body {
            background-color: var(--bg);
            color: var(--primary);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            line-height: 1.6;
            overflow-x: hidden;
        }

        body::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), 
                        linear-gradient(90deg, rgba(255, 51, 51, 0.02), rgba(0, 0, 0, 0), rgba(255, 51, 51, 0.02));
            z-index: 1000;
            background-size: 100% 4px, 3px 100%;
            pointer-events: none;
            will-change: auto;
        }

        header {
            padding: 20px 50px;
            border-bottom: 1px solid var(--entity-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(5, 5, 5, 0.95);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-btn {
            color: #fff;
            text-decoration: none;
            font-family: monospace;
            border: 1px solid #fff;
            padding: 5px 15px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: var(--entity-color);
            border-color: var(--entity-color);
            color: #000;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 50px;
            margin-bottom: 80px;
        }

        .profile-image {
            width: 400px;
            height: 533px;
            border: 1px solid var(--entity-color);
            background: #111;
            position: relative;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: top;
            filter: saturate(1.2) contrast(1.1);
        }

        .status-tag {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: var(--entity-color);
            color: #000;
            padding: 8px 0;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-sizing: border-box;
        }

        h1 { font-size: 4rem; margin: 0; color: #fff; text-shadow: 0 0 20px color-mix(in srgb, var(--entity-color) 40%, transparent); }

        .data-section {
            margin-top: 30px;
            padding: 30px;
            background: var(--glass);
            border-left: 4px solid var(--entity-color);
        }

        .scenario-box {
            background: #000;
            padding: 40px;
            border: 1px solid #222;
            font-family: "Courier New", Courier, monospace;
            border-top: 4px solid var(--entity-color);
            color: #ccc;
            white-space: pre-wrap;
        }

        .glitch-text {
            color: var(--entity-color);
            font-family: monospace;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .memoire-bar {
            height: 4px;
            background: #222;
            margin-top: 15px;
            position: relative;
        }
        .memoire-fill {
            height: 100%;
            background: var(--entity-color);
            transition: width 1s ease;
        }

        footer {
            padding: 40px;
            text-align: center;
            font-family: monospace;
            color: #666;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .profile-grid { grid-template-columns: 1fr; }
            .profile-image { width: 100%; height: auto; aspect-ratio: 3/4; }
            h1 { font-size: 3rem; }
            header { padding: 15px 20px; }
        }
    </style>
</head>
<body>

    <header role="banner">
        <nav aria-label="Retour">
            <a href="index.php" class="back-btn">< RETOUR AU MONITORING</a>
        </nav>
        <div style="text-align: right;">
            <div style="color: var(--entity-color); font-weight: bold; font-family: monospace;"><?= htmlspecialchars($tag) ?>_<?= rand(10000, 99999) ?></div>
            <div style="font-size: 0.7rem; color: #888;">CHARGEMENT DES DONNÉES MÉMOIRES...</div>
        </div>
    </header>

    <main>
        <div class="profile-grid">
            <section class="profile-image">
                <?php if ($img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Sujet <?= htmlspecialchars($name) ?> - Project M13" width="400" height="533" fetchpriority="high">
                <?php endif; ?>
                <div class="status-tag"><?= htmlspecialchars($status) ?></div>
            </section>

            <section class="bio">
                <h1><?= htmlspecialchars($name) ?></h1>
                <p class="glitch-text">> GRAND TITRE : <?= htmlspecialchars($title) ?></p>
                
                <?php $mem = intval($entity['memoire'] ?? 50); ?>
                <p class="glitch-text" style="margin-top:15px;">> STABILITÉ MÉMORIELLE : <?= $mem ?>%</p>
                <div class="memoire-bar"><div class="memoire-fill" style="width: <?= $mem ?>%;"></div></div>

                <?php if ($desc1): ?>
                <div class="data-section">
                    <h2>ANALYSE DU SUJET</h2>
                    <p><?= nl2br(htmlspecialchars($desc1)) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($desc2): ?>
                <div class="data-section">
                    <h2>CONSÉQUENCES MÉMORIELLES</h2>
                    <p><?= nl2br(htmlspecialchars($desc2)) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!$desc1 && !$desc2 && $scenario): ?>
                <div class="data-section">
                    <h2>ANALYSE DU SUJET</h2>
                    <p><?= nl2br(htmlspecialchars($scenario)) ?></p>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <?php if ($scenario && ($desc1 || $desc2)): ?>
        <section class="scenario-box"><?= htmlspecialchars($scenario) ?></section>
        <?php endif; ?>
    </main>

    <footer role="contentinfo">
        PROPRIÉTÉ EXCLUSIVE DU PROJECT M13 - ACCÈS RESTREINT - 2026<br>
        "On ne peut pas supprimer un souvenir sans en créer une conséquence."
    </footer>

</body>
</html>
