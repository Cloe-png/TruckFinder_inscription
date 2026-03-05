# TruckFinder

Application web PHP (MVC léger) pour la gestion de foodtrucks :
- inscription foodtruck,
- validation admin,
- gestion des adresses de présence,
- gestion des horaires,
- gestion du menu.

## Fonctionnalités

### Côté foodtruck
- Inscription avec une ou plusieurs adresses.
- Connexion sécurisée (mot de passe hashé).
- Dashboard foodtruck :
  - gestion des adresses,
  - sélection de l'adresse de présence (`Je serai ici`),
  - carte interactive (Leaflet/OpenStreetMap),
  - gestion des horaires d'ouverture,
  - création/modification du menu.
- Suppression du compte.

### Côté admin
- Connexion admin.
- Liste des demandes en attente.
- Validation/rejet des inscriptions.
- Vue globale des foodtrucks.
- Suppression d'un compte foodtruck.
- Suppression de son propre compte admin (impossible de supprimer le dernier admin).

## Stack technique
- PHP 8+
- MySQL/MariaDB
- WAMP (en local)
- Bootstrap 5
- Leaflet + OpenStreetMap

## Structure du projet

```txt
TruckFinder/
  config/
    database.php
    truckfinder.sql
  controllers/
    UtilisateurController.php
    AdminController.php
  models/
    Utilisateur.php
    Foodtruck.php
    Demande.php
  views/
    index.php
    layout.php
    home.php
    login.php
    register.php
    foodtruck.php
    edit_menu.php
    admin.php
```

## Installation locale

1. Cloner le projet et placer le dossier dans `c:\wamp64\www\`.
2. Créer la base `truckfinder`.
3. Importer le schéma complet : `config/truckfinder.sql`.
4. Vérifier la connexion DB dans `config/database.php`.
5. Ouvrir l'app via WAMP.

## Base de données

### Tables principales
- `utilisateurs`
- `foodtrucks`
- `demandesinscription`
- `foodtruck_adresses`

### Points importants
- Les statuts utilisent : `en_attente`, `approuve`, `rejete`.
- Les adresses multiples sont stockées dans `foodtruck_adresses`.
- L'adresse active (présence) est marquée par `est_present = 1`.

## Auteur
Projet réalisé et maintenu pour TruckFinder.
