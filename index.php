<?php

require_once __DIR__ . '/autoload.php';

DatabaseConnection::configure('sqlite:' . __DIR__ . '/database/agence.db');

$pdo = DatabaseConnection::getInstance()->getConnection();
$pdo->exec("
    CREATE TABLE IF NOT EXISTS proprietaires (
        id             INTEGER PRIMARY KEY AUTOINCREMENT,
        nom            TEXT    NOT NULL,
        email          TEXT    NOT NULL UNIQUE,
        type           TEXT    NOT NULL DEFAULT 'particulier',
        telephone      TEXT,
        siret          TEXT,
        raison_sociale TEXT,
        created_at     TEXT    NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS biens (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        reference  TEXT    NOT NULL UNIQUE,
        type       TEXT    NOT NULL,
        ville      TEXT    NOT NULL,
        prix       REAL    NOT NULL,
        surface    REAL    NOT NULL,
        statut     TEXT    NOT NULL DEFAULT 'disponible',
        details    TEXT    NOT NULL DEFAULT '{}',
        created_at TEXT    NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS proprietaires_biens (
        proprietaire_id INTEGER NOT NULL REFERENCES proprietaires(id) ON DELETE CASCADE,
        bien_id         INTEGER NOT NULL REFERENCES biens(id)         ON DELETE CASCADE,
        PRIMARY KEY (proprietaire_id, bien_id)
    );

    CREATE TABLE IF NOT EXISTS annonces (
        id                INTEGER PRIMARY KEY AUTOINCREMENT,
        bien_id           INTEGER NOT NULL REFERENCES biens(id) ON DELETE CASCADE,
        titre             TEXT    NOT NULL,
        description       TEXT    NOT NULL DEFAULT '',
        type_transaction  TEXT    NOT NULL DEFAULT 'vente',
        prix_demande      REAL,
        contact_nom       TEXT    NOT NULL,
        contact_email     TEXT    NOT NULL,
        contact_telephone TEXT,
        statut            TEXT    NOT NULL DEFAULT 'active',
        created_at        TEXT    NOT NULL DEFAULT (datetime('now'))
    );
");

BienFactory::enregistrerType('appartement', fn($data) => new Appartement(
    $data['ville'], (float)$data['prix'], (float)$data['surface'],
    (int)$data['etage'], (bool)($data['ascenseur'] ?? false), (string)($data['typeAppartement'] ?? '')
));
BienFactory::enregistrerType('maison', fn($data) => new Maison(
    $data['ville'], (float)$data['prix'], (float)$data['surface'],
    (int)$data['nbChambres'], (bool)($data['jardin'] ?? false), (float)($data['surfaceJardin'] ?? 0), (string)($data['garage'] ?? '')
));
BienFactory::enregistrerType('local', fn($data) => new Local(
    $data['ville'], (float)$data['prix'], (float)$data['surface'], (string)$data['activite']
));

// Seeding automatique si la base est vide
if ((int)$pdo->query("SELECT COUNT(*) FROM biens")->fetchColumn() === 0) {
    $data = json_decode(file_get_contents(__DIR__ . '/data/data.json'), true);

    $stmtBien = $pdo->prepare("
        INSERT INTO biens (reference, type, ville, prix, surface, statut, details)
        VALUES (:reference, :type, :ville, :prix, :surface, 'disponible', :details)
    ");

    $bienIds = [];
    foreach ($data['biens'] as $i => $d) {
        $bien = BienFactory::creerDepuisTableau($d);
        $details = match($d['type']) {
            'appartement' => json_encode(['etage' => $d['etage'], 'ascenseur' => $d['ascenseur'] ?? false, 'typeAppartement' => $d['typeAppartement'] ?? '']),
            'maison'      => json_encode(['nbChambres' => $d['nbChambres'], 'jardin' => $d['jardin'] ?? false, 'surfaceJardin' => $d['surfaceJardin'] ?? 0, 'garage' => $d['garage'] ?? '']),
            'local'       => json_encode(['activite' => $d['activite']]),
            default       => '{}',
        };
        $stmtBien->execute([':reference' => $bien->reference, ':type' => $d['type'], ':ville' => $bien->getVille(), ':prix' => $bien->getPrix(), ':surface' => $bien->getSurface(), ':details' => $details]);
        $bienIds[$i] = (int)$pdo->lastInsertId();
    }

    $stmtProprio = $pdo->prepare("INSERT INTO proprietaires (nom, email, type, telephone, siret, raison_sociale) VALUES (:nom, :email, :type, :telephone, :siret, :raison_sociale)");
    $stmtLien    = $pdo->prepare("INSERT INTO proprietaires_biens (proprietaire_id, bien_id) VALUES (:proprio_id, :bien_id)");

    foreach ($data['proprietaires'] as $p) {
        $stmtProprio->execute([':nom' => $p['nom'], ':email' => $p['email'], ':type' => $p['type'], ':telephone' => $p['telephone'] ?? null, ':siret' => $p['siret'] ?? null, ':raison_sociale' => $p['raisonSociale'] ?? null]);
        $proprioId = (int)$pdo->lastInsertId();
        foreach ($p['biens'] as $index) {
            if (isset($bienIds[$index])) {
                $stmtLien->execute([':proprio_id' => $proprioId, ':bien_id' => $bienIds[$index]]);
            }
        }
    }

    $stmtAnnonce = $pdo->prepare("
        INSERT INTO annonces (bien_id, titre, description, type_transaction, prix_demande, contact_nom, contact_email, contact_telephone, statut)
        VALUES (:bien_id, :titre, :description, :type_transaction, :prix_demande, :contact_nom, :contact_email, :contact_telephone, :statut)
    ");

    $annonces = [
        ['bien' => 0, 'titre' => 'Bel appartement T3 à Lyon', 'description' => 'Appartement lumineux au 3ème étage avec ascenseur, proche transports.', 'transaction' => 'vente', 'prix' => 185000, 'nom' => 'Alice Martin', 'email' => 'alice@immo.fr', 'tel' => '06 12 34 56 78', 'statut' => 'active'],
        ['bien' => 1, 'titre' => 'T2 Paris centre — à vendre', 'description' => 'Superbe T2 au 7ème étage avec vue dégagée, ascenseur, quartier prisé.', 'transaction' => 'vente', 'prix' => 325000, 'nom' => 'Bob Dupont', 'email' => 'bob@immo.fr', 'tel' => '07 98 76 54 32', 'statut' => 'active'],
        ['bien' => 2, 'titre' => 'Studio Bordeaux à louer', 'description' => 'Studio cosy idéal étudiant, 1er étage sans ascenseur, charges incluses.', 'transaction' => 'location', 'prix' => 550, 'nom' => 'Alice Martin', 'email' => 'alice@immo.fr', 'tel' => '06 12 34 56 78', 'statut' => 'active'],
        ['bien' => 3, 'titre' => 'Grande maison Marseille avec jardin', 'description' => 'Magnifique maison 4 chambres, jardin 200m², double garage, quartier calme.', 'transaction' => 'vente', 'prix' => 460000, 'nom' => 'Bob Dupont', 'email' => 'bob@immo.fr', 'tel' => '07 98 76 54 32', 'statut' => 'active'],
        ['bien' => 4, 'titre' => 'Maison Nantes 3 chambres — location', 'description' => 'Maison familiale avec jardin 80m² et garage, proche écoles.', 'transaction' => 'location', 'prix' => 1200, 'nom' => 'Alice Martin', 'email' => 'alice@immo.fr', 'tel' => '06 12 34 56 78', 'statut' => 'active'],
        ['bien' => 6, 'titre' => 'Local commercial Paris — Restaurant', 'description' => 'Local 200m² aménagé restauration, emplacement idéal Paris centre, forte affluence.', 'transaction' => 'vente', 'prix' => 860000, 'nom' => 'Bob Dupont', 'email' => 'bob@immo.fr', 'tel' => '07 98 76 54 32', 'statut' => 'active'],
        ['bien' => 7, 'titre' => 'Local pharmacie Lyon à louer', 'description' => 'Local 80m² adapté pharmacie, vitrine commerciale, quartier résidentiel.', 'transaction' => 'location', 'prix' => 2500, 'nom' => 'Alice Martin', 'email' => 'alice@immo.fr', 'tel' => '06 12 34 56 78', 'statut' => 'archivee'],
    ];

    foreach ($annonces as $a) {
        if (isset($bienIds[$a['bien']])) {
            $stmtAnnonce->execute([
                ':bien_id'          => $bienIds[$a['bien']],
                ':titre'            => $a['titre'],
                ':description'      => $a['description'],
                ':type_transaction' => $a['transaction'],
                ':prix_demande'     => $a['prix'],
                ':contact_nom'      => $a['nom'],
                ':contact_email'    => $a['email'],
                ':contact_telephone'=> $a['tel'],
                ':statut'           => $a['statut'],
            ]);
        }
    }
}

$page   = $_GET['page']   ?? 'biens';
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

match ($page) {
    'proprietaires' => (new ProprietaireController())->dispatch($action, $id),
    'annonces'      => (new AnnonceController())->dispatch($action, $id),
    default         => (new BienController())->dispatch($action, $id),
};
