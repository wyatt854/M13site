<?php
$data = json_decode(file_get_contents('data.json'), true);
$nav_pages = isset($data['pages']) ? $data['pages'] : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>M13 // ARCHIVES DU BLOG</title>
    <style>
        body { background: #050505; color: #fff; font-family: 'Courier New', Courier, monospace; padding: 20px 50px; margin: 0; }
        
        /* MENU DYNAMIQUE (Identique à l'index) */
        .m13-nav { display: flex; gap: 20px; border-bottom: 1px solid rgba(51, 255, 0, 0.2); padding-bottom: 20px; margin-bottom: 40px; font-size: 0.9rem; flex-wrap: wrap; }
        .m13-nav a { color: #fff; text-decoration: none; transition: color 0.3s; text-transform: uppercase; }
        .m13-nav a:hover, .m13-nav a.active { color: #33ff00; text-shadow: 0 0 8px rgba(51,255,0,0.5); }

        .article { border-left: 2px solid #33ff00; padding: 20px; margin-bottom: 40px; background: rgba(51, 255, 0, 0.02); }
        .date { color: #33ff00; font-size: 0.8rem; }
        h1 { border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 0; }
    </style>
</head>
<body>
    
    <nav class="m13-nav">
        <a href="index.php">> MONITORING</a>
        <?php foreach ($nav_pages as $p): ?>
            <a href="page.php?title=<?= urlencode($p['menu']) ?>">> <?= htmlspecialchars($p['menu']) ?></a>
        <?php endforeach; ?>
        <a href="blog.php" class="active">> LOGS</a>
        <a href="shop.php">> BOUTIQUE</a>
    </nav>

    <h1>M13 // LOGS_MÉMORIELS</h1>

    <?php if(empty($data['articles'])): ?>
        <p style="opacity: 0.5;">Aucune archive trouvée. Le système est vide.</p>
    <?php else: ?>
        <?php foreach(array_reverse($data['articles']) as $art): ?>
            <div class="article">
                <div class="date">[ DATE: <?php echo htmlspecialchars($art['date']); ?> ]</div>
                <h2><?php echo htmlspecialchars($art['titre']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($art['contenu'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>