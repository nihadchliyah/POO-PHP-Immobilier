-- Agence Immobilière — schéma SQLite

CREATE TABLE IF NOT EXISTS proprietaires (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    nom            TEXT    NOT NULL,
    email          TEXT    NOT NULL UNIQUE,
    type           TEXT    NOT NULL DEFAULT 'particulier', -- particulier | professionnel
    telephone      TEXT,
    siret          TEXT,
    raison_sociale TEXT,
    created_at     TEXT    NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS biens (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    reference TEXT    NOT NULL UNIQUE,
    type      TEXT    NOT NULL, -- appartement | maison | local
    ville     TEXT    NOT NULL,
    prix      REAL    NOT NULL,
    surface   REAL    NOT NULL,
    statut    TEXT    NOT NULL DEFAULT 'disponible', -- disponible | loue | vendu
    details   TEXT    NOT NULL DEFAULT '{}',          -- JSON champs spécifiques au type
    created_at TEXT   NOT NULL DEFAULT (datetime('now'))
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
    type_transaction  TEXT    NOT NULL DEFAULT 'vente',    -- vente | location
    prix_demande      REAL,
    contact_nom       TEXT    NOT NULL,
    contact_email     TEXT    NOT NULL,
    contact_telephone TEXT,
    statut            TEXT    NOT NULL DEFAULT 'active',   -- active | archivee
    created_at        TEXT    NOT NULL DEFAULT (datetime('now'))
);
