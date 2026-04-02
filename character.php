<?php
$data = json_decode(file_get_contents('data.json'), true);
// On imagine qu'on passe un ID ou un nom en paramètre
$name = $_GET['name'] ?? '';
$perso = null;

foreach($data['personnages'] as $p) {
    if($p['nom'] === $name) { $perso = $p; break; }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>M13 - Dossier : <?php echo $name; ?></title>
    <style>
        .scenario { background: #eee; color: #111; padding: 40px; font-family: "Courier", monospace; line-height: 1.2; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>FICHE SUJET : <?php echo $name; ?></h1>
    
    <?php if($perso): ?>
        <div class="scenario">
            <p><strong>STABILITÉ MÉMOIRE :</strong> <?php echo $perso['memoire']; ?>%</p>
            <hr>
            <?php echo nl2br($perso['description']); ?>
        </div>
    <?php else: ?>
        <p>Sujet introuvable dans la base M13.</p>
    <?php endif; ?>
    
    <a href="index.php">Retour</a>
</body>
</html>