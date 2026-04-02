<?php
$file = 'data.json';

// Initialisation du fichier s'il n'existe pas
if (!file_exists($file)) {
    file_put_contents($file, json_encode(['pages' => [], 'personnages' => [], 'articles' => []]));
}

$current_data = json_decode(file_get_contents($file), true);

// === CRÉATION (POST) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['type'])) {
    $type = $_GET['type'];

    if ($type === 'page') {
        $current_data['pages'][] = [
            'menu'    => htmlspecialchars($_POST['page_menu']),
            'title'   => htmlspecialchars($_POST['page_title']),
            'content' => $_POST['page_content']
        ];
    } 
    elseif ($type === 'personnage') {
        $nom = htmlspecialchars($_POST['nom']);
        $current_data['personnages'][] = [
            'nom'         => $nom,
            'tag'         => htmlspecialchars($_POST['tag']),
            'color'       => htmlspecialchars($_POST['color']),
            'img'         => htmlspecialchars($_POST['img']),
            'memoire'     => intval($_POST['etat_memoire']),
            'title'       => htmlspecialchars($_POST['title'] ?? ''),
            'status'      => htmlspecialchars($_POST['status'] ?? 'SUJET ACTIF'),
            'desc1'       => $_POST['desc1'] ?? '',
            'desc2'       => $_POST['desc2'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];
    } 
    elseif ($type === 'article') {
        $current_data['articles'][] = [
            'titre'   => htmlspecialchars($_POST['art_title']),
            'date'    => $_POST['art_date'],
            'contenu' => $_POST['art_content']
        ];
    }

    file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: admin.php?success=1');
    exit;
}

// === SUPPRESSION (GET) ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    
    // Suppression utilisateur
    if ($_GET['action'] === 'delete_user') {
        $usersFile = 'users.json';
        $users = [];
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true) ?: [];
        }
        $index = intval($_GET['index']);
        if (isset($users[$index])) {
            array_splice($users, $index, 1);
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        header('Location: admin.php?deleted=1');
        exit;
    }
    
    // Suppression contenu standard
    if ($_GET['action'] === 'delete') {
        $type  = $_GET['type'];
        $index = intval($_GET['index']);
        if (isset($current_data[$type]) && isset($current_data[$type][$index])) {
            array_splice($current_data[$type], $index, 1);
            file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        header('Location: admin.php?deleted=1');
        exit;
    }
}
?>
