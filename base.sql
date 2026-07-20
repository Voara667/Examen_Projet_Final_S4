-- ============================================================
-- base.sql — Mobile Money Simulator — Version 1
-- SQLite3
-- ============================================================

PRAGMA foreign_keys = ON;

-- ============================================================
-- TABLES
-- ============================================================

-- Administrateurs (côté opérateur)
CREATE TABLE admins (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        TEXT NOT NULL,
    email      TEXT NOT NULL UNIQUE,
    password   TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- Préfixes valables de l'opérateur
CREATE TABLE prefixes (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe    TEXT NOT NULL UNIQUE,
    actif      INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

-- Types de transaction : depot / retrait / transfert
CREATE TABLE type_transaction (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    code       TEXT NOT NULL UNIQUE,
    libelle    TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- Barèmes de frais par tranche de montant, par type de transaction
CREATE TABLE baremes_frais (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    type_transaction_id INTEGER NOT NULL,
    montant_min         INTEGER NOT NULL,
    montant_max         INTEGER NOT NULL,
    frais               INTEGER NOT NULL,
    created_at          DATETIME,
    updated_at          DATETIME,
    FOREIGN KEY (type_transaction_id) REFERENCES type_transaction(id)
);

-- Clients (créés à la volée à la première connexion)
CREATE TABLE clients (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone  TEXT NOT NULL UNIQUE,
    solde             INTEGER NOT NULL DEFAULT 0,
    created_at        DATETIME,
    updated_at        DATETIME
);

-- Historique des transactions
CREATE TABLE transactions (
    id                      INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id               INTEGER NOT NULL,
    client_destinataire_id   INTEGER,
    type_transaction_id     INTEGER NOT NULL,
    montant                 INTEGER NOT NULL,
    frais                   INTEGER NOT NULL DEFAULT 0,
    nouveau_solde           INTEGER NOT NULL,
    created_at              DATETIME,
    updated_at               DATETIME,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (client_destinataire_id) REFERENCES clients(id),
    FOREIGN KEY (type_transaction_id) REFERENCES type_transaction(id)
);

-- Index utiles
CREATE INDEX idx_transactions_client_id ON transactions(client_id);
CREATE INDEX idx_baremes_type ON baremes_frais(type_transaction_id);

-- ============================================================
-- VUES
-- ============================================================

-- Situation des comptes clients
CREATE VIEW vue_situation_comptes_clients AS
SELECT
    c.id,
    c.numero_telephone,
    c.solde,
    c.created_at
FROM clients c;

-- Situation des gains via les différents frais (par type de transaction)
CREATE VIEW vue_situation_gains AS
SELECT
    tt.code           AS type_transaction,
    tt.libelle        AS libelle_type,
    COUNT(t.id)       AS nombre_transactions,
    SUM(t.frais)      AS total_frais
FROM transactions t
JOIN type_transaction tt ON tt.id = t.type_transaction_id
GROUP BY tt.id;

-- ============================================================
-- DONNÉES PAR DÉFAUT
-- ============================================================

-- Préfixes de l'opérateur
INSERT INTO prefixes (prefixe, actif, created_at, updated_at) VALUES
('034', 1, datetime('now'), datetime('now')),
('038', 1, datetime('now'), datetime('now'));

-- Types de transaction
INSERT INTO type_transaction (code, libelle, created_at, updated_at) VALUES
('depot',     'Dépôt',     datetime('now'), datetime('now')),
('retrait',   'Retrait',   datetime('now'), datetime('now')),
('transfert', 'Transfert', datetime('now'), datetime('now'));

-- Barème de frais — appliqué à RETRAIT (type_transaction_id = 2)
INSERT INTO baremes_frais (type_transaction_id, montant_min, montant_max, frais, created_at, updated_at) VALUES
(2, 100,      1000,     50,   datetime('now'), datetime('now')),
(2, 1001,     5000,     50,   datetime('now'), datetime('now')),
(2, 5001,     10000,    100,  datetime('now'), datetime('now')),
(2, 10001,    25000,    200,  datetime('now'), datetime('now')),
(2, 25001,    50000,    400,  datetime('now'), datetime('now')),
(2, 50001,    100000,   800,  datetime('now'), datetime('now')),
(2, 100001,   250000,   1500, datetime('now'), datetime('now')),
(2, 250001,   500000,   1500, datetime('now'), datetime('now')),
(2, 500001,   1000000,  2500, datetime('now'), datetime('now')),
(2, 1000001,  2000000,  3000, datetime('now'), datetime('now'));

-- Barème de frais — appliqué à TRANSFERT (type_transaction_id = 3)
INSERT INTO baremes_frais (type_transaction_id, montant_min, montant_max, frais, created_at, updated_at) VALUES
(3, 100,      1000,     50,   datetime('now'), datetime('now')),
(3, 1001,     5000,     50,   datetime('now'), datetime('now')),
(3, 5001,     10000,    100,  datetime('now'), datetime('now')),
(3, 10001,    25000,    200,  datetime('now'), datetime('now')),
(3, 25001,    50000,    400,  datetime('now'), datetime('now')),
(3, 50001,    100000,   800,  datetime('now'), datetime('now')),
(3, 100001,   250000,   1500, datetime('now'), datetime('now')),
(3, 250001,   500000,   1500, datetime('now'), datetime('now')),
(3, 500001,   1000000,  2500, datetime('now'), datetime('now')),
(3, 1000001,  2000000,  3000, datetime('now'), datetime('now'));

-- Admin par défaut (mot de passe : "admin123" — à changer en prod)
-- Hash bcrypt valide, vérifiable avec password_verify('admin123', ...) en PHP
INSERT INTO admins (nom, email, password, created_at, updated_at) VALUES
('Admin', 'admin@mobilemoney.mg', '$2b$10$yCV3iPTYs3R4fEfkI8bMbujEEF9IUgN/avzAkY9i8dxX3U5oupmma', datetime('now'), datetime('now'));
