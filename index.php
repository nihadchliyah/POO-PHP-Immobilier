<?php

require_once __DIR__ . '/autoload.php';

DatabaseConnection::configure('sqlite:' . __DIR__ . '/database/agence.db');

// Migration auto : crée la table annonces si elle n'existe pas encore
DatabaseConnection::getInstance()->getConnection()->exec("
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
    )
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

$page   = $_GET['page']   ?? 'biens';
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

match ($page) {
    'proprietaires' => (new ProprietaireController())->dispatch($action, $id),
    'annonces'      => (new AnnonceController())->dispatch($action, $id),
    default         => (new BienController())->dispatch($action, $id),
};
