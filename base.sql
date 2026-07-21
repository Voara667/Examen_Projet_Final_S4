PRAGMA foreign_keys = ON;

CREATE TABLE admins (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        TEXT NOT NULL,
    email      TEXT NOT NULL UNIQUE,
    password   TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE type_transaction (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    code       TEXT NOT NULL UNIQUE,
    libelle    TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE operateurs_externes (
    id                      INTEGER PRIMARY KEY AUTOINCREMENT,
    nom                     TEXT NOT NULL UNIQUE,
    commission_pourcentage  REAL NOT NULL DEFAULT 0,
    created_at              DATETIME,
    updated_at              DATETIME
);

CREATE TABLE prefixes (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe               TEXT NOT NULL UNIQUE,
    actif                 INTEGER NOT NULL DEFAULT 1,
    operateur_externe_id  INTEGER NULL,
    created_at            DATETIME,
    updated_at            DATETIME,
    FOREIGN KEY (operateur_externe_id) REFERENCES operateurs_externes(id)
);

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

CREATE TABLE clients (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone       TEXT NOT NULL UNIQUE,
    solde                  INTEGER NOT NULL DEFAULT 0,
    credit_frais_retrait   INTEGER NOT NULL DEFAULT 0,
    created_at             DATETIME,
    updated_at             DATETIME
);

CREATE TABLE transactions (
    id                            INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id                     INTEGER NOT NULL,
    client_destinataire_id        INTEGER,
    type_transaction_id           INTEGER NOT NULL,
    montant                       INTEGER NOT NULL,
    frais                         INTEGER NOT NULL DEFAULT 0,
    nouveau_solde                 INTEGER NOT NULL,
    operateur_externe_id          INTEGER NULL,
    numero_destinataire_externe   TEXT NULL,
    commission                    INTEGER NOT NULL DEFAULT 0,
    frais_retrait_inclus          INTEGER NOT NULL DEFAULT 0,
    groupe_envoi                  TEXT NULL,
    created_at                    DATETIME,
    updated_at                    DATETIME,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (client_destinataire_id) REFERENCES clients(id),
    FOREIGN KEY (type_transaction_id) REFERENCES type_transaction(id),
    FOREIGN KEY (operateur_externe_id) REFERENCES operateurs_externes(id)
);

CREATE INDEX idx_transactions_client_id ON transactions(client_id);
CREATE INDEX idx_baremes_type ON baremes_frais(type_transaction_id);
CREATE INDEX idx_prefixes_operateur_externe ON prefixes(operateur_externe_id);
CREATE INDEX idx_transactions_operateur_externe ON transactions(operateur_externe_id);
CREATE INDEX idx_transactions_groupe_envoi ON transactions(groupe_envoi);

CREATE VIEW vue_situation_comptes_clients AS
SELECT
    c.id,
    c.numero_telephone,
    c.solde,
    c.credit_frais_retrait,
    c.created_at
FROM clients c;

CREATE VIEW vue_situation_gains AS
SELECT
    tt.code                                  AS type_transaction,
    tt.libelle                                AS libelle_type,
    CASE WHEN t.operateur_externe_id IS NULL
         THEN 'interne' ELSE 'externe' END    AS portee,
    oe.id                                     AS operateur_externe_id,
    oe.nom                                    AS operateur_externe_nom,
    COUNT(t.id)                               AS nombre_transactions,
    SUM(t.frais)                              AS total_frais
FROM transactions t
JOIN type_transaction tt ON tt.id = t.type_transaction_id
LEFT JOIN operateurs_externes oe ON oe.id = t.operateur_externe_id
GROUP BY tt.id, portee, oe.id;

CREATE VIEW vue_montants_a_envoyer_operateurs AS
SELECT
    oe.id                          AS operateur_externe_id,
    oe.nom                         AS operateur_externe_nom,
    COUNT(t.id)                    AS nombre_transferts,
    SUM(t.montant)                 AS total_montant,
    SUM(t.commission)              AS total_commission,
    SUM(t.montant + t.commission)  AS total_a_envoyer
FROM transactions t
JOIN operateurs_externes oe ON oe.id = t.operateur_externe_id
WHERE t.operateur_externe_id IS NOT NULL
GROUP BY oe.id;

INSERT INTO type_transaction (code, libelle, created_at, updated_at) VALUES
('depot',     'Dépôt',     datetime('now'), datetime('now')),
('retrait',   'Retrait',   datetime('now'), datetime('now')),
('transfert', 'Transfert', datetime('now'), datetime('now'));

INSERT INTO operateurs_externes (nom, commission_pourcentage, created_at, updated_at) VALUES
('Orange', 1.0, datetime('now'), datetime('now')),
('Airtel', 1.5, datetime('now'), datetime('now'));

INSERT INTO prefixes (prefixe, actif, operateur_externe_id, created_at, updated_at) VALUES
('034', 1, NULL, datetime('now'), datetime('now')),
('038', 1, NULL, datetime('now'), datetime('now'));

INSERT INTO prefixes (prefixe, actif, operateur_externe_id, created_at, updated_at) VALUES
('032', 1, 1, datetime('now'), datetime('now')),
('031', 1, 2, datetime('now'), datetime('now'));

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

INSERT INTO admins (nom, email, password, created_at, updated_at) VALUES
('Admin', 'admin@mobilemoney.mg', '$2b$10$yCV3iPTYs3R4fEfkI8bMbujEEF9IUgN/avzAkY9i8dxX3U5oupmma', datetime('now'), datetime('now'));
