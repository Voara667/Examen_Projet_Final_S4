CREATE TABLE promotion_transfert (
    id  INTERGER PRIMARY KEY AUTOINCREMENT,
    montant_min INTEGER NOT NULL,
    montant_max INTEGER NOT NULL,
    pourcentage_promotion REAL NOT NULL DEFAULT 2,
    actif INTEGER NNOT NULL DEFAULT 1
);