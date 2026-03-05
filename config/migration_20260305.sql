-- Migration TruckFinder 2026-03-05
-- 1) Normalise les statuts pour supprimer les accents (approuve/rejete)
-- 2) Ajoute la table des adresses multiples et la notion de presence

START TRANSACTION;

UPDATE foodtrucks
SET statut = 'approuve'
WHERE statut IN ('approuve', 'approuvé', 'approuvÃ©');

UPDATE foodtrucks
SET statut = 'rejete'
WHERE statut IN ('rejete', 'rejeté', 'rejetÃ©');

UPDATE demandesinscription
SET statut = 'approuve'
WHERE statut IN ('approuve', 'approuvé', 'approuvÃ©');

UPDATE demandesinscription
SET statut = 'rejete'
WHERE statut IN ('rejete', 'rejeté', 'rejetÃ©');

ALTER TABLE foodtrucks
MODIFY statut ENUM('en_attente', 'approuve', 'rejete') NOT NULL DEFAULT 'en_attente';

ALTER TABLE demandesinscription
MODIFY statut ENUM('en_attente', 'approuve', 'rejete') NOT NULL DEFAULT 'en_attente';

CREATE TABLE IF NOT EXISTS foodtruck_adresses (
    id_adresse INT NOT NULL AUTO_INCREMENT,
    id_foodtruck INT NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    est_present TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_adresse),
    INDEX idx_foodtruck_adresses_foodtruck (id_foodtruck)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO foodtruck_adresses (id_foodtruck, adresse, est_present)
SELECT ft.id_foodtruck, ft.adresse, 1
FROM foodtrucks ft
LEFT JOIN foodtruck_adresses fa ON fa.id_foodtruck = ft.id_foodtruck AND fa.adresse = (ft.adresse COLLATE utf8mb4_0900_ai_ci)
WHERE ft.adresse IS NOT NULL
  AND ft.adresse <> ''
  AND fa.id_adresse IS NULL;

COMMIT;
