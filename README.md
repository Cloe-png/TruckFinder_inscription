# TruckFinder

Application web PHP (MVC léger) pour la gestion de foodtrucks:
- inscription foodtruck,
- validation admin,
- gestion des adresses de présence,
- gestion des horaires,
- gestion du menu.

## Fonctionnalités

### Côté foodtruck
- Inscription avec une ou plusieurs adresses.
- Connexion sécurisée (mot de passe hashé).
- Dashboard foodtruck:
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
- Suppression de son propre compte admin (protégée: impossible de supprimer le dernier admin).

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
    migration_20260305.sql
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
3. Importer le schéma initial:
   - `config/truckfinder.sql`
4. Appliquer la migration adresses/statuts:
   - `config/migration_20260305.sql`
5. Vérifier la connexion DB dans `config/database.php`:
   - host: `localhost`
   - user: `root`
   - password: `''` (par défaut WAMP)
6. Ouvrir l'app via WAMP.

## Base de données

### Tables principales
- `utilisateurs`
- `foodtrucks`
- `demandesinscription`
- `foodtruck_adresses`

### Points importants
- Les statuts utilisent: `en_attente`, `approuve`, `rejete`.
- Les adresses multiples sont stockées dans `foodtruck_adresses`.
- L'adresse active (présence) est marquée par `est_present = 1`.

## Compte admin

Un compte admin peut être créé directement en base.
Exemple (mot de passe hashé avec `password_hash`):

```sql
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES ('Admin TruckFinder', 'admin@truckfinder.local', '<hash_bcrypt>', 'admin');
```

## Routes principales (`views/index.php`)

### Public
- `action=home`
- `action=login`
- `action=register`
- `action=do_login`
- `action=do_register`

### Foodtruck
- `action=foodtruck_dashboard`
- `action=save_adresses`
- `action=set_presence_adresse`
- `action=save_horaires`
- `action=edit_menu`
- `action=save_menu`
- `action=delete_account`

### Admin
- `action=admin_demandes`
- `action=admin_foodtrucks`
- `action=valider_demande`
- `action=delete_foodtruck`
- `action=delete_account`

## Sécurité / validations
- Contrôle de rôle sur chaque action sensible.
- Suppression de compte via `POST`.
- Vérification du propriétaire foodtruck pour les actions dashboard/menu.
- Blocage de la suppression du dernier admin.

## État actuel
- Flux principal opérationnel (inscription, validation, dashboard, adresses, présence, horaires, menu, suppressions).
- Interface améliorée sur vues admin/foodtruck.

## Auteur
Projet réalisé et maintenu pour TruckFinder.
