-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 29 sep. 2025 à 18:07
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `truckfinder`
--

-- --------------------------------------------------------

--
-- Structure de la table `demandesinscription`
--

DROP TABLE IF EXISTS `demandesinscription`;
CREATE TABLE IF NOT EXISTS `demandesinscription` (
  `id_demande` int NOT NULL AUTO_INCREMENT,
  `id_foodtruck` int NOT NULL,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','approuvé','rejeté') DEFAULT 'en_attente',
  `commentaire_admin` text,
  PRIMARY KEY (`id_demande`),
  KEY `id_foodtruck` (`id_foodtruck`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `demandesinscription`
--

INSERT INTO `demandesinscription` (`id_demande`, `id_foodtruck`, `date_demande`, `statut`, `commentaire_admin`) VALUES
(1, 1, '2025-02-10 14:25:00', 'approuvé', 'Aucun commentaire.'),
(2, 2, '2025-03-05 11:50:00', 'en_attente', 'En attente de vérification des documents.'),
(3, 3, '2025-04-12 16:15:00', 'rejeté', 'Logo non conforme aux normes.'),
(4, 4, '2025-05-20 09:20:00', 'approuvé', 'Aucun commentaire.');

-- --------------------------------------------------------

--
-- Structure de la table `foodtrucks`
--

DROP TABLE IF EXISTS `foodtrucks`;
CREATE TABLE IF NOT EXISTS `foodtrucks` (
  `id_foodtruck` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `nom_foodtruck` varchar(100) NOT NULL,
  `description` text,
  `type_cuisine` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','approuvé','rejeté') DEFAULT 'en_attente',
  PRIMARY KEY (`id_foodtruck`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `foodtrucks`
--

INSERT INTO `foodtrucks` (`id_foodtruck`, `id_utilisateur`, `nom_foodtruck`, `description`, `type_cuisine`, `adresse`, `telephone`, `logo`, `statut`) VALUES
(1, 2, 'Le Camion Qui Fume', 'Spécialisé en barbecue fumé et viandes marinées.', 'Barbecue', '12 Rue de la Gare, Paris', '0612345678', '/logos/lecamionquifume.png', 'approuvé'),
(2, 3, 'Burger Nomade', 'Burgers artisanaux et frites maison.', 'Burger', '45 Avenue des Champs, Lyon', '0687654321', '/logos/burgernomade.png', 'en_attente'),
(3, 4, 'Tacos El Paso', 'Tacos et burritos authentiques.', 'Mexicaine', '78 Boulevard de la Mer, Nice', '0611223344', '/logos/tacoselpaso.png', 'rejeté'),
(4, 5, 'Wok & Roll', 'Cuisine asiatique rapide et fraîche.', 'Asiatique', '3 Rue du Commerce, Bordeaux', '0655667788', '/logos/wokandroll.png', 'approuvé');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('foodtruck','admin') NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'Admin Principal', 'admin@foodtruckapp.fr', '$2a$10$N9qo8uLOickgx2ZMRZoMy...', 'admin', '2025-01-15 08:30:00'),
(2, 'Le Camion Qui Fume', 'contact@lecamionquifume.fr', '$2a$10$T7f6gB9sLk3jD1pQ8xWv...', 'foodtruck', '2025-02-10 14:20:00'),
(3, 'Burger Nomade', 'burger.nomade@foodtruck.fr', '$2a$10$K5l8mN2oP9qR7sT4uV6w...', 'foodtruck', '2025-03-05 11:45:00'),
(4, 'Tacos El Paso', 'tacos@elpaso.fr', '$2a$10$A1b2C3d4E5f6G7h8I9j0...', 'foodtruck', '2025-04-12 16:10:00'),
(5, 'Wok & Roll', 'wokandroll@foodtruck.fr', '$2a$10$Z9y8X7w6V5u4T3s2R1q0...', 'foodtruck', '2025-05-20 09:15:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
