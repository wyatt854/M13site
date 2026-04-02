<?php
// On récupère les réglages depuis la mémoire du système (data.json)
$dataFile = 'data.json';
$shop_status = "construction"; // Statut par défaut

if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
    if (isset($data['settings']['shop_status'])) {
        $shop_status = $data['settings']['shop_status'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M13 // BOUTIQUE_ERROR</title>
    <style>
        :root {
            --bg: #000;
            --error: #ff3366;
            --glitch-color: #33ff00;
        }

        body {
            background: var(--bg);
            color: var(--error);
            font-family: 'Courier New', Courier, monospace;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        /* Effet de scanline (ligne qui descend) */
        body::after {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 2px;
            background: rgba(255, 51, 102, 0.2);
            animation: scan 3s linear infinite;
        }

        @keyframes scan {
            from { top: 0%; }
            to { top: 100%; }
        }

        .glitch-title {
            font-size: 3.5rem;
            font-weight: bold;
            position: relative;
            text-transform: uppercase;
            animation: shake 0.4s infinite;
        }

        @keyframes shake {
            0% { transform: translate(0); text-shadow: 2px 2px var(--glitch-color); }
            25% { transform: translate(-3px, 2px); }
            50% { transform: translate(3px, -2px); text-shadow: -2px -2px var(--glitch-color); }
            100% { transform: translate(0); }
        }

        .container {
            border: 1px solid var(--error);
            padding: 40px;
            max-width: 600px;
            background: rgba(255, 51, 102, 0.05);
            box-shadow: 0 0 20px rgba(255, 51, 102, 0.2);
            z-index: 10;
        }

        .status-code {
            background: var(--error);
            color: #000;
            padding: 5px 10px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-link {
            margin-top: 30px;
            display: block;
            color: #fff;
            text-decoration: none;
            border: 1px solid #fff;
            padding: 10px 20px;
            transition: 0.3s;
        }

        .back-link:hover {
            background: #fff;
            color: #000;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="status-code">STATUS_CODE: <?php echo strtoupper($shop_status); ?></div>
        
        <div class="glitch-title">ACCÈS RÉDUIT</div>
        
        <p style="margin-top: 20px;">
            LA BOUTIQUE DU PROJECT M13 NE PEUT PAS ÊTRE MATÉRIALISÉE. 
            LES SOUVENIRS DES OBJETS ONT ÉTÉ SUPPRIMÉS DU SERVEUR CENTRAL.
        </p>

        <?php if ($shop_status == "construction"): ?>
            <p style="color: var(--glitch-color);">RECONSTRUCTION EN COURS... 14% COMPLÉTÉ.</p>
        <?php else: ?>
            <p style="color: var(--glitch-color);">ALERTE : LA RÉALITÉ EST TROP INSTABLE POUR LE COMMERCE.</p>
        <?php endif; ?>

        <a href="index.php" class="back-link">RETOURNER AU MONITORING</a>
    </div>

    <div style="position: fixed; bottom: 10px; font-size: 0.7rem; opacity: 0.3;">
        ERR_MEM_VOID_04 // <?php echo date('Y'); ?> © PROJECT M13
    </div>

</body>
</html>