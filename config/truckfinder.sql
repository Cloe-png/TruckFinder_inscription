-- TruckFinder SQL schema (single-file install)
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+00:00';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `foodtruck_adresses`;
DROP TABLE IF EXISTS `demandesinscription`;
DROP TABLE IF EXISTS `foodtrucks`;
DROP TABLE IF EXISTS `utilisateurs`;

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('foodtruck','admin') NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `foodtrucks` (
  `id_foodtruck` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `nom_foodtruck` varchar(100) NOT NULL,
  `description` text,
  `type_cuisine` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `menu` longtext,
  `horaires` longtext,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (`id_foodtruck`),
  KEY `idx_foodtrucks_user` (`id_utilisateur`),
  CONSTRAINT `fk_foodtrucks_utilisateur`
    FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `demandesinscription` (
  `id_demande` int NOT NULL AUTO_INCREMENT,
  `id_foodtruck` int NOT NULL,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  `commentaire_admin` text,
  PRIMARY KEY (`id_demande`),
  KEY `idx_demande_foodtruck` (`id_foodtruck`),
  CONSTRAINT `fk_demande_foodtruck`
    FOREIGN KEY (`id_foodtruck`) REFERENCES `foodtrucks` (`id_foodtruck`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `foodtruck_adresses` (
  `id_adresse` int NOT NULL AUTO_INCREMENT,
  `id_foodtruck` int NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `est_present` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_adresse`),
  KEY `idx_foodtruck_adresses_foodtruck` (`id_foodtruck`),
  CONSTRAINT `fk_adresse_foodtruck`
    FOREIGN KEY (`id_foodtruck`) REFERENCES `foodtrucks` (`id_foodtruck`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'Admin Principal', 'admin@truckfinder.local', '$2y$10$replace_with_real_hash', 'admin', '2025-01-15 08:30:00'),
(2, 'Le Camion Qui Fume', 'contact@lecamionquifume.fr', '$2y$10$replace_with_real_hash', 'foodtruck', '2025-02-10 14:20:00');

INSERT INTO `foodtrucks` (`id_foodtruck`, `id_utilisateur`, `nom_foodtruck`, `description`, `type_cuisine`, `adresse`, `telephone`, `logo`, `menu`, `horaires`, `statut`) VALUES
(1, 2, 'Le Camion Qui Fume', 'Spécialisé en barbecue fumé et viandes marinées.', 'Barbecue', '12 Rue de la Gare, Paris', '0612345678', '/logos/lecamionquifume.png',
 '[{"nom":"Menu du jour","description":"Notre spécialité du moment","prix":12.5,"image":"https://via.placeholder.com/150?text=Menu+du+jour"}]',
 '{"lundi":{"ouvert":true,"debut":"11:00","fin":"14:30"},"mardi":{"ouvert":true,"debut":"11:00","fin":"14:30"},"mercredi":{"ouvert":true,"debut":"11:00","fin":"14:30"},"jeudi":{"ouvert":true,"debut":"11:00","fin":"14:30"},"vendredi":{"ouvert":true,"debut":"11:00","fin":"14:30"},"samedi":{"ouvert":false,"debut":"11:00","fin":"14:30"},"dimanche":{"ouvert":false,"debut":"11:00","fin":"14:30"}}',
 'approuve');

INSERT INTO `demandesinscription` (`id_demande`, `id_foodtruck`, `date_demande`, `statut`, `commentaire_admin`) VALUES
(1, 1, '2025-02-10 14:25:00', 'approuve', 'Compte validé.');

INSERT INTO `foodtruck_adresses` (`id_adresse`, `id_foodtruck`, `adresse`, `latitude`, `longitude`, `est_present`) VALUES
(1, 1, '12 Rue de la Gare, Paris', 48.8566000, 2.3522000, 1),
(2, 1, '1 Place Paul Lemagny, Paris', NULL, NULL, 0);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
