<?php

require_once __DIR__ . '/../autoload.php';

$dbPath = __DIR__ . '/agence.db';

if (file_exists($dbPath)) {
    echo "Base déjà existante : {$dbPath}" . PHP_EOL;
    echo "Supprimez-la manuellement pour réinitialiser." . PHP_EOL;
    exit(0);
}

DatabaseConnection::configure('sqlite:' . $dbPath);
$pdo = DatabaseConnection::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$schema = file_get_contents(__DIR__ . '/schema.sql');
$pdo->exec($schema);
echo "Tables créées." . PHP_EOL;


$source = new JsonDataSource(__DIR__ . '/../data/data.json');
$data   = $source->recupererDonnees();

BienFactory::enregistrerType('appartement', fn($d) => new Appartement(
    $d['ville'], (float)$d['prix'], (float)$d['surface'],
    (int)$d['etage'], (bool)($d['ascenseur'] ?? false), (string)($d['typeAppartement'] ?? '')
));
BienFactory::enregistrerType('maison', fn($d) => new Maison(
    $d['ville'], (float)$d['prix'], (float)$d['surface'],
    (int)$d['nbChambres'], (bool)($d['jardin'] ?? false), (float)($d['surfaceJardin'] ?? 0), (string)($d['garage'] ?? '')
));
BienFactory::enregistrerType('local', fn($d) => new Local(
    $d['ville'], (float)$d['prix'], (float)$d['surface'], (string)$d['activite']
));


$stmtBien = $pdo->prepare("
    INSERT INTO biens (reference, type, ville, prix, surface, details)
    VALUES (:reference, :type, :ville, :prix, :surface, :details)
");

$bienIds = [];
foreach ($data['biens'] as $i => $bienData) {
    $bien = BienFactory::creerDepuisTableau($bienData);

    $details = match($bienData['type']) {
        'appartement' => json_encode([
            'etage'           => $bienData['etage'],
            'ascenseur'       => $bienData['ascenseur'] ?? false,
            'typeAppartement' => $bienData['typeAppartement'] ?? '',
        ]),
        'maison' => json_encode([
            'nbChambres'    => $bienData['nbChambres'],
            'jardin'        => $bienData['jardin'] ?? false,
            'surfaceJardin' => $bienData['surfaceJardin'] ?? 0,
            'garage'        => $bienData['garage'] ?? '',
        ]),
        'local' => json_encode([
            'activite' => $bienData['activite'],
        ]),
        default => '{}',
    };

    $stmtBien->execute([
        ':reference' => $bien->reference,
        ':type'      => $bienData['type'],
        ':ville'     => $bien->getVille(),
        ':prix'      => $bien->getPrix(),
        ':surface'   => $bien->getSurface(),
        ':details'   => $details,
    ]);

    $bienIds[$i] = (int)$pdo->lastInsertId();
    echo "  Bien inséré : {$bien->reference} — {$bien->getVille()}" . PHP_EOL;
}


$stmtProprio = $pdo->prepare("
    INSERT INTO proprietaires (nom, email, type, telephone, siret, raison_sociale)
    VALUES (:nom, :email, :type, :telephone, :siret, :raison_sociale)
");

$stmtLien = $pdo->prepare("
    INSERT INTO proprietaires_biens (proprietaire_id, bien_id)
    VALUES (:proprio_id, :bien_id)
");

foreach ($data['proprietaires'] as $proprioData) {
    $stmtProprio->execute([
        ':nom'           => $proprioData['nom'],
        ':email'         => $proprioData['email'],
        ':type'          => $proprioData['type'],
        ':telephone'     => $proprioData['telephone']    ?? null,
        ':siret'         => $proprioData['siret']        ?? null,
        ':raison_sociale'=> $proprioData['raisonSociale'] ?? null,
    ]);

    $proprioId = (int)$pdo->lastInsertId();
    echo "  Propriétaire inséré : {$proprioData['nom']}" . PHP_EOL;

    foreach ($proprioData['biens'] as $index) {
        if (isset($bienIds[$index])) {
            $stmtLien->execute([
                ':proprio_id' => $proprioId,
                ':bien_id'    => $bienIds[$index],
            ]);
        }
    }
}

echo PHP_EOL . "Base de données créée : {$dbPath}" . PHP_EOL;
echo "Biens insérés    : " . count($bienIds) . PHP_EOL;
echo "Propriétaires    : " . count($data['proprietaires']) . PHP_EOL;
