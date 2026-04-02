<?php
require_once 'auth_admin.php'; // Bloque l'accès si non connecté

// On lit les données actuelles pour les afficher dans l'onglet Gestion
$dataFile = 'data.json';
$data = ['pages' => [], 'personnages' => [], 'articles' => []];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true) ?: $data;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CENTRAL DE CONTRÔLE - PROJECT M13</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <style>
        :root {
            --m13-green: #00ff41;
            --m13-dark: #0a0a0a;
            --m13-grey: #1a1a1a;
            --m13-text: #d1d1d1;
            --m13-danger: #ff3333;
        }

        body { font-family: 'Courier New', Courier, monospace; background-color: var(--m13-dark); color: var(--m13-text); margin: 0; padding: 20px; }
        header { border-bottom: 2px solid var(--m13-green); padding-bottom: 10px; margin-bottom: 30px; text-align: center; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1000px; margin: auto; }

        .ui-tabs { background: transparent; border: none; }
        .ui-tabs-nav { background: var(--m13-grey); border: 1px solid var(--m13-green); }
        .ui-state-default, .ui-widget-content .ui-state-default { background: #333; border: 1px solid #444; }
        .ui-state-active, .ui-widget-content .ui-state-active { background: var(--m13-green); border: 1px solid var(--m13-green); }
        .ui-state-active a { color: #000 !important; }
        .ui-tabs-panel { background: var(--m13-grey); border: 1px solid var(--m13-green); margin-top: 5px; color: var(--m13-text); }

        label { display: block; margin-top: 15px; color: var(--m13-green); font-weight: bold; }
        input[type="text"], input[type="color"], textarea, select { 
            width: 100%; padding: 12px; margin-top: 5px; 
            background: #000; border: 1px solid #333; 
            color: var(--m13-green); box-sizing: border-box;
        }
        input:focus, textarea:focus { border-color: var(--m13-green); outline: none; }
        
        .btn-submit { background: var(--m13-green); color: #000; padding: 15px; border: none; margin-top: 25px; cursor: pointer; font-weight: bold; width: 100%; text-transform: uppercase; }
        .btn-submit:hover { background: #008f11; }
        
        .btn-delete { background: var(--m13-danger); color: #fff; padding: 5px 10px; text-decoration: none; font-size: 0.8rem; border-radius: 3px; }
        .btn-delete:hover { background: #cc0000; }

        .gestion-list { list-style: none; padding: 0; }
        .gestion-list li { background: #000; border: 1px solid #333; padding: 10px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center; }

        .hint { font-size: 0.85em; color: #666; margin-top: 5px; }
        footer { margin-top: 40px; text-align: center; font-size: 0.9em; opacity: 0.6; }

        .logout-btn { 
            background: var(--m13-danger); color: #fff; padding: 8px 16px; 
            text-decoration: none; font-size: 0.8rem; border: none; cursor: pointer;
            font-family: monospace; text-transform: uppercase; letter-spacing: 1px;
        }
        .logout-btn:hover { background: #cc0000; }

        /* Gestion utilisateurs */
        .users-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .users-table th, .users-table td { border: 1px solid #333; padding: 10px; text-align: left; font-size: 0.85rem; }
        .users-table th { background: #000; color: var(--m13-green); }
        .users-table tr:nth-child(even) td { background: #111; }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1 style="margin:0; font-size:1.1rem;">M13 // SYSTÈME D'ADMINISTRATION</h1>
            <p style="margin:5px 0 0; font-size:0.75rem; color:#555;">Session active : <?= htmlspecialchars(ADMIN_USER) ?></p>
        </div>
        <a href="logout_admin.php" class="logout-btn">[ DÉCONNEXION ]</a>
    </header>

    <?php if(isset($_GET['success'])): ?><p style="color:var(--m13-green); text-align:center;">[!] DONNÉES ENREGISTRÉES AVEC SUCCÈS</p><?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?><p style="color:var(--m13-danger); text-align:center;">[!] ENTRÉE SUPPRIMÉE DE LA MÉMOIRE</p><?php endif; ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab-gestion">Gestion (Supprimer)</a></li>
            <li><a href="#tab-page">Nouvelle Page</a></li>
            <li><a href="#tab-perso">Nouveau Personnage</a></li>
            <li><a href="#tab-article">Nouvel Article</a></li>
            <li><a href="#tab-users">Utilisateurs</a></li>
        </ul>

        <div id="tab-gestion">
            <h2>Pages Actives</h2>
            <ul class="gestion-list">
                <?php foreach($data['pages'] as $index => $item): ?>
                    <li>
                        <span><?= htmlspecialchars($item['menu']) ?> (<?= htmlspecialchars($item['title']) ?>)</span>
                        <a href="process.php?action=delete&type=pages&index=<?= $index ?>" class="btn-delete" onclick="return confirm('Effacer ce souvenir ?');">SUPPRIMER</a>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($data['pages'])) echo "<li>Aucune page.</li>"; ?>
            </ul>

            <h2>Sujets / Personnages</h2>
            <ul class="gestion-list">
                <?php foreach($data['personnages'] as $index => $item): ?>
                    <li>
                        <span><?= htmlspecialchars($item['nom']) ?> [<?= htmlspecialchars($item['tag'] ?? 'N/A') ?>]</span>
                        <a href="process.php?action=delete&type=personnages&index=<?= $index ?>" class="btn-delete" onclick="return confirm('Effacer ce souvenir ?');">SUPPRIMER</a>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($data['personnages'])) echo "<li>Aucun sujet.</li>"; ?>
            </ul>

            <h2>Articles / Logs</h2>
            <ul class="gestion-list">
                <?php foreach($data['articles'] as $index => $item): ?>
                    <li>
                        <span><?= htmlspecialchars($item['titre'] ?? 'Sans titre') ?> (<?= htmlspecialchars($item['date'] ?? '') ?>)</span>
                        <a href="process.php?action=delete&type=articles&index=<?= $index ?>" class="btn-delete" onclick="return confirm('Effacer ce souvenir ?');">SUPPRIMER</a>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($data['articles'])) echo "<li>Aucun log.</li>"; ?>
            </ul>
        </div>

        <div id="tab-page">
            <h2>Configuration de Page</h2>
            <p class="hint">La page apparaîtra automatiquement dans le menu de navigation.</p>
            <form action="process.php?type=page" method="POST">
                <label>Nom dans le menu :</label>
                <input type="text" name="page_menu" placeholder="Ex: Archives, Laboratoire..." required>
                <label>Titre de la page :</label>
                <input type="text" name="page_title" placeholder="Titre complet affiché en haut de page">
                <label>Contenu de la page :</label>
                <textarea name="page_content" rows="10" placeholder="Contenu HTML ou texte..."></textarea>
                <button type="submit" class="btn-submit">Déployer la page</button>
            </form>
        </div>

        <div id="tab-perso">
            <h2>Générateur de Sujet</h2>
            <form action="process.php?type=personnage" method="POST">
                <label>Identité (Nom) :</label>
                <input type="text" name="nom" placeholder="Ex: DARIUS" required>

                <label>Tag (Identifiant visuel) :</label>
                <input type="text" name="tag" placeholder="Ex: ID_01" required>

                <label>Couleur du profil :</label>
                <input type="color" name="color" value="#33ff00" style="height: 50px; cursor: pointer;">

                <label>Nom de l'image (doit être dans le même dossier) :</label>
                <input type="text" name="img" placeholder="Ex: darius.png" required>

                <label>Stabilité mémorielle : <span id="val-memoire">50%</span></label>
                <div id="slider-memoire" style="margin: 15px 0;"></div>
                <input type="hidden" name="etat_memoire" id="input-memoire" value="50">

                <label>Grand titre / Rôle :</label>
                <input type="text" name="title" placeholder="Ex: LE PORTEUR">

                <label>Statut :</label>
                <input type="text" name="status" placeholder="Ex: ANOMALIE MÉMORIELLE">

                <label>Description / Analyse (paragraphe 1) :</label>
                <textarea name="desc1" rows="4" placeholder="Analyse du sujet..."></textarea>

                <label>Conséquences mémorielles (paragraphe 2) :</label>
                <textarea name="desc2" rows="4" placeholder="Conséquences..."></textarea>

                <label>Scénario (Format Netflix INT/EXT) :</label>
                <textarea name="description" rows="6" placeholder="EXT. RUE - NUIT..."></textarea>

                <button type="submit" class="btn-submit">Inscrire le sujet dans M13</button>
            </form>
        </div>

        <div id="tab-article">
            <h2>Rédaction d'Article</h2>
            <form action="process.php?type=article" method="POST">
                <label>Titre de l'article :</label>
                <input type="text" name="art_title" placeholder="Titre de l'actualité" required>
                <label>Date de publication :</label>
                <input type="text" name="art_date" value="<?php echo date('d/m/Y'); ?>">
                <label>Corps de l'article :</label>
                <textarea name="art_content" rows="10" placeholder="Écrivez votre article ici..."></textarea>
                <button type="submit" class="btn-submit">Publier l'article</button>
            </form>
        </div>

        <div id="tab-users">
            <h2>Utilisateurs Inscrits</h2>
            <?php
            $usersFile = 'users.json';
            $users = [];
            if (file_exists($usersFile)) {
                $users = json_decode(file_get_contents($usersFile), true) ?: [];
            }
            ?>
            <?php if(empty($users)): ?>
                <p>Aucun utilisateur inscrit.</p>
            <?php else: ?>
                <table class="users-table">
                    <tr>
                        <th>#</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Inscrit le</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach($users as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['created_at'] ?? 'N/A') ?></td>
                        <td><a href="process.php?action=delete_user&index=<?= $i ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?');">SUPPRIMER</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>"On ne peut pas supprimer un souvenir sans en créer une conséquence."</footer>
</div>

<script>
    $(document).ready(function() {
        $("#tabs").tabs();
        $("#slider-memoire").slider({
            range: "min", value: 50, min: 0, max: 100,
            slide: function(event, ui) {
                $("#val-memoire").text(ui.value + "%");
                $("#input-memoire").val(ui.value);
            }
        });
    });
</script>

</body>
</html>
