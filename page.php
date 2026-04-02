<?php
$file = 'data.json';
$data = ['pages' => []];

if (file_exists($file)) {
    $decoded = json_decode(file_get_contents($file), true);
    if (is_array($decoded)) { $data = $decoded; }
}

$page_requested = isset($_GET['title']) ? $_GET['title'] : '';
$current_page = null;

foreach ($data['pages'] as $page) {
    if ($page['menu'] === $page_requested) {
        $current_page = $page;
        break;
    }
}

if (!$current_page) {
    header("Location: 404.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($current_page['title']); ?> - M13</title>
    <style>
        body { background: #050505; color: #fff; font-family: 'Courier New', Courier, monospace; padding: 20px 50px; margin: 0; }
        
        .m13-nav { display: flex; gap: 20px; border-bottom: 1px solid rgba(51, 255, 0, 0.2); padding-bottom: 20px; margin-bottom: 40px; font-size: 0.9rem; flex-wrap: wrap; }
        .m13-nav a { color: #fff; text-decoration: none; transition: color 0.3s; text-transform: uppercase; }
        .m13-nav a:hover, .m13-nav a.active { color: #33ff00; text-shadow: 0 0 8px rgba(51,255,0,0.5); }

        .page-content h1 { border-bottom: 1px solid #333; padding-bottom: 10px; color: #33ff00; }
        .content-body { line-height: 1.6; font-family: Arial, sans-serif; }
    </style>
</head>
<body>

<header>
    <nav class="m13-nav">
        <a href="index.php">> MONITORING</a>
        <?php foreach($data['pages'] as $page): ?>
            <a href="page.php?title=<?php echo urlencode($page['menu']); ?>" 
               class="<?php echo ($page['menu'] === $page_requested) ? 'active' : ''; ?>">
               > <?php echo htmlspecialchars($page['menu']); ?>
            </a>
        <?php endforeach; ?>
        <a href="blog.php">> LOGS</a>
        <a href="shop.php">> BOUTIQUE</a>
    </nav>
</header>

<main>
    <article class="page-content">
        <h1><?php echo htmlspecialchars($current_page['title']); ?></h1>
        <div class="content-body">
            <?php echo nl2br($current_page['content']); ?>
        </div>
    </article>
</main>

<footer>
    <p style="text-align:center; margin-top:50px; opacity:0.3;">PROTOCOLE M13 - ACCÈS SÉCURISÉ</p>
</footer>

</body>
</html>