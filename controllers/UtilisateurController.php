<?php
require_once '../models/Utilisateur.php';
require_once '../models/Foodtruck.php';
require_once '../models/Demande.php';

class UtilisateurController {
    private $utilisateur;
    private $foodtruck;
    private $demande;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->utilisateur = new Utilisateur($pdo);
        $this->foodtruck = new FoodTruck($pdo);
        $this->demande = new Demande($pdo);
    }

    public function traiterInscription() {
        session_start();
        try {
            $nom = $_POST['nom'];
            $email = $_POST['email'];
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
            $nom_foodtruck = $_POST['nom_foodtruck'];
            $description = $_POST['description'];
            $type_cuisine = $_POST['type_cuisine'];

            $adressesInput = $_POST['adresses'] ?? [];
            $adressesInput = is_array($adressesInput) ? $adressesInput : [];
            $adressesNettoyees = [];
            foreach ($adressesInput as $adresseInput) {
                $adresseTexte = trim((string)$adresseInput);
                if ($adresseTexte !== '') {
                    $adressesNettoyees[] = $adresseTexte;
                }
            }

            $adressePrincipale = !empty($adressesNettoyees) ? $adressesNettoyees[0] : trim((string)($_POST['adresse'] ?? ''));
            if ($adressePrincipale === '') {
                throw new Exception('Au moins une adresse est obligatoire.');
            }

            $telephone = $_POST['telephone'];
            $logo = '/logos/default.png';

            $id_utilisateur = $this->utilisateur->inscrire($nom, $email, $mot_de_passe);
            $id_foodtruck = $this->foodtruck->creer($id_utilisateur, $nom_foodtruck, $description, $type_cuisine, $adressePrincipale, $telephone, $logo);

            $menuBase = [
                [
                    'nom' => 'Menu du jour',
                    'description' => 'Notre specialite du moment',
                    'prix' => 12.50,
                    'image' => 'https://via.placeholder.com/150?text=Menu+du+jour'
                ],
                [
                    'nom' => 'Boisson',
                    'description' => 'Boisson au choix',
                    'prix' => 2.50,
                    'image' => 'https://via.placeholder.com/150?text=Boisson'
                ],
                [
                    'nom' => 'Dessert',
                    'description' => 'Dessert maison',
                    'prix' => 4.00,
                    'image' => 'https://via.placeholder.com/150?text=Dessert'
                ]
            ];

            $this->foodtruck->updateMenu($id_foodtruck, json_encode($menuBase));
            $this->demande->creer($id_foodtruck);

            if (empty($adressesNettoyees)) {
                $adressesNettoyees[] = $adressePrincipale;
            }

            $adressesPourStockage = [];
            foreach ($adressesNettoyees as $index => $adresseTexte) {
                $adressesPourStockage[] = [
                    'adresse' => $adresseTexte,
                    'latitude' => null,
                    'longitude' => null,
                    'est_present' => $index === 0 ? 1 : 0
                ];
            }
            $this->foodtruck->upsertAdresses((int)$id_foodtruck, $adressesPourStockage);

            header('Location: index.php?action=login&alert=register_success');
            exit();
        } catch (Exception $e) {
            error_log("Erreur d'inscription: " . $e->getMessage());
            header('Location: index.php?action=register&alert=register_error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function traiterConnexion() {
        session_start();

        if (empty($_POST['email']) || empty($_POST['mot_de_passe'])) {
            header('Location: index.php?action=login&alert=empty_fields');
            exit();
        }

        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];
        $user = $this->utilisateur->trouverParEmail($email);

        if (!$user || !password_verify($mot_de_passe, $user['mot_de_passe'])) {
            header('Location: index.php?action=login&alert=login_error');
            exit();
        }

        $roleNormalise = $this->normaliserRole($user['role'] ?? '');

        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['role'] = $roleNormalise;
        $_SESSION['email'] = $user['email'];

        if ($roleNormalise === 'admin') {
            header('Location: index.php?action=admin_demandes');
            exit();
        }

        if ($roleNormalise === 'foodtruck') {
            $stmt = $this->pdo->prepare('SELECT statut FROM foodtrucks WHERE id_utilisateur = ?');
            $stmt->execute([$user['id_utilisateur']]);
            $foodtruckStatut = $stmt->fetchColumn();

            if (!$this->estStatutApprouve($foodtruckStatut)) {
                header('Location: index.php?action=login&alert=compte_non_valide');
                exit();
            }

            header('Location: index.php?action=foodtruck_dashboard');
            exit();
        }

        session_unset();
        session_destroy();
        header('Location: index.php?action=login&alert=invalid_role');
        exit();
    }

    public function dashboard() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();

        $adresses = $this->foodtruck->getAdresses((int)$foodtruckData['id_foodtruck']);
        if (empty($adresses) && !empty($foodtruckData['adresse'])) {
            $adresses[] = [
                'id_adresse' => 0,
                'adresse' => $foodtruckData['adresse'],
                'latitude' => null,
                'longitude' => null,
                'est_present' => 1
            ];
        }

        $horaires = $this->getHorairesParDefaut();
        if (!empty($foodtruckData['horaires'])) {
            $decodedHoraires = json_decode($foodtruckData['horaires'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedHoraires)) {
                foreach ($horaires as $jour => $valeur) {
                    if (!isset($decodedHoraires[$jour]) || !is_array($decodedHoraires[$jour])) {
                        continue;
                    }
                    $horaires[$jour]['ouvert'] = !empty($decodedHoraires[$jour]['ouvert']);
                    $horaires[$jour]['debut'] = $decodedHoraires[$jour]['debut'] ?? $valeur['debut'];
                    $horaires[$jour]['fin'] = $decodedHoraires[$jour]['fin'] ?? $valeur['fin'];
                }
            }
        }

        include '../views/foodtruck.php';
    }

    public function afficherEditionMenu() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();
        include '../views/edit_menu.php';
    }

    public function sauvegarderMenu() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();

        $plats = $_POST['plats'] ?? [];
        $plats = is_array($plats) ? $plats : [];

        $menuNettoye = [];
        foreach ($plats as $plat) {
            $nom = trim((string)($plat['nom'] ?? ''));
            $description = trim((string)($plat['description'] ?? ''));
            $prixRaw = $plat['prix'] ?? '';
            $image = trim((string)($plat['image'] ?? ''));

            if ($nom === '' || $description === '' || $prixRaw === '') {
                continue;
            }

            $prix = (float)$prixRaw;
            if ($prix < 0) {
                continue;
            }

            if ($image === '') {
                $image = 'https://via.placeholder.com/150?text=Plat';
            }

            $menuNettoye[] = [
                'nom' => $nom,
                'description' => $description,
                'prix' => round($prix, 2),
                'image' => $image
            ];
        }

        if (empty($menuNettoye)) {
            header('Location: index.php?action=edit_menu&alert=register_error&detail=' . urlencode('Ajoute au moins un plat valide.'));
            exit();
        }

        try {
            $this->foodtruck->updateMenu((int)$foodtruckData['id_foodtruck'], json_encode($menuNettoye, JSON_UNESCAPED_UNICODE));
            header('Location: index.php?action=foodtruck_dashboard&alert=menu_saved');
            exit();
        } catch (Exception $e) {
            error_log('Erreur sauvegarde menu: ' . $e->getMessage());
            header('Location: index.php?action=edit_menu&alert=register_error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function sauvegarderAdresses() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();

        $adresses = [];
        $adressesInput = $_POST['adresses'] ?? [];
        $latitudes = $_POST['latitudes'] ?? [];
        $longitudes = $_POST['longitudes'] ?? [];

        $existantes = $this->foodtruck->getAdresses((int)$foodtruckData['id_foodtruck']);
        $actives = array_map(
            static fn($a) => trim((string)$a['adresse']),
            array_filter($existantes, static fn($a) => !empty($a['est_present']))
        );

        foreach ($adressesInput as $index => $adresse) {
            $adresseTexte = trim((string)$adresse);
            if ($adresseTexte === '') {
                continue;
            }

            $adresses[] = [
                'adresse' => $adresseTexte,
                'latitude' => $latitudes[$index] ?? null,
                'longitude' => $longitudes[$index] ?? null,
                'est_present' => in_array($adresseTexte, $actives, true) ? 1 : 0
            ];
        }

        if (empty($adresses)) {
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode('Ajoute au moins une adresse.'));
            exit();
        }

        try {
            $this->foodtruck->upsertAdresses((int)$foodtruckData['id_foodtruck'], $adresses);
            header('Location: index.php?action=foodtruck_dashboard&alert=login_success');
        } catch (Exception $e) {
            error_log('Erreur sauvegarde adresses: ' . $e->getMessage());
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode($e->getMessage()));
        }
        exit();
    }

    public function definirPresenceAdresse() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();

        $idAdresse = isset($_POST['id_adresse']) ? (int)$_POST['id_adresse'] : 0;
        if ($idAdresse <= 0) {
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode('Adresse invalide.'));
            exit();
        }

        try {
            $ok = $this->foodtruck->definirAdressePresence((int)$foodtruckData['id_foodtruck'], $idAdresse);
            if (!$ok) {
                header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode('Adresse introuvable pour ce foodtruck.'));
                exit();
            }
            header('Location: index.php?action=foodtruck_dashboard&alert=login_success');
            exit();
        } catch (Exception $e) {
            error_log('Erreur definir presence: ' . $e->getMessage());
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function sauvegarderHoraires() {
        $foodtruckData = $this->getFoodtruckActuelOuRedirect();
        $horairesInput = $_POST['horaires'] ?? [];
        if (!is_array($horairesInput)) {
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode('Format des horaires invalide.'));
            exit();
        }

        $horaires = $this->getHorairesParDefaut();
        foreach ($horaires as $jour => $valeurs) {
            $inputJour = $horairesInput[$jour] ?? [];
            $ouvert = !empty($inputJour['ouvert']);
            $debut = trim((string)($inputJour['debut'] ?? $valeurs['debut']));
            $fin = trim((string)($inputJour['fin'] ?? $valeurs['fin']));

            if (!preg_match('/^\d{2}:\d{2}$/', $debut)) {
                $debut = $valeurs['debut'];
            }
            if (!preg_match('/^\d{2}:\d{2}$/', $fin)) {
                $fin = $valeurs['fin'];
            }

            $horaires[$jour] = [
                'ouvert' => $ouvert,
                'debut' => $debut,
                'fin' => $fin
            ];
        }

        try {
            $this->foodtruck->updateHoraires((int)$foodtruckData['id_foodtruck'], json_encode($horaires, JSON_UNESCAPED_UNICODE));
            header('Location: index.php?action=foodtruck_dashboard&alert=horaires_saved');
            exit();
        } catch (Exception $e) {
            error_log('Erreur sauvegarde horaires: ' . $e->getMessage());
            header('Location: index.php?action=foodtruck_dashboard&alert=register_error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function supprimerCompteConnecte() {
        if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
            header('Location: index.php?action=login&alert=access_denied');
            exit();
        }

        $idUtilisateur = (int)$_SESSION['id_utilisateur'];
        $role = (string)$_SESSION['role'];

        try {
            $this->utilisateur->supprimerCompteConnecte($idUtilisateur, $role);
            session_unset();
            session_destroy();
            header('Location: index.php?action=login&alert=account_deleted');
            exit();
        } catch (Exception $e) {
            error_log('Erreur suppression compte: ' . $e->getMessage());
            header('Location: index.php?alert=register_error&detail=' . urlencode($e->getMessage()));
            exit();
        }
    }

    private function getFoodtruckActuelOuRedirect() {
        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] ?? '') !== 'foodtruck') {
            header('Location: index.php?action=login&alert=access_denied');
            exit();
        }

        $foodtruckData = $this->foodtruck->getFoodtruckByUserId((int)$_SESSION['id_utilisateur']);
        if (empty($foodtruckData)) {
            header('Location: index.php?action=login&alert=register_error&detail=' . urlencode('Foodtruck introuvable.'));
            exit();
        }

        return $foodtruckData;
    }

    private function normaliserRole($role) {
        $role = strtolower(trim((string)$role));

        if (in_array($role, ['admin', 'administrateur', 'role_admin'], true)) {
            return 'admin';
        }

        if (in_array($role, ['foodtruck', 'food_truck', 'truck'], true)) {
            return 'foodtruck';
        }

        return $role;
    }

    private function estStatutApprouve($statut) {
        $statutNormalise = strtolower(trim((string)$statut));
        $statutAscii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $statutNormalise);

        if ($statutAscii !== false && $statutAscii !== '') {
            $statutNormalise = strtolower($statutAscii);
        }

        return strpos($statutNormalise, 'approuv') === 0;
    }

    private function getHorairesParDefaut() {
        return [
            'lundi' => ['ouvert' => true, 'debut' => '11:00', 'fin' => '14:30'],
            'mardi' => ['ouvert' => true, 'debut' => '11:00', 'fin' => '14:30'],
            'mercredi' => ['ouvert' => true, 'debut' => '11:00', 'fin' => '14:30'],
            'jeudi' => ['ouvert' => true, 'debut' => '11:00', 'fin' => '14:30'],
            'vendredi' => ['ouvert' => true, 'debut' => '11:00', 'fin' => '14:30'],
            'samedi' => ['ouvert' => false, 'debut' => '11:00', 'fin' => '14:30'],
            'dimanche' => ['ouvert' => false, 'debut' => '11:00', 'fin' => '14:30'],
        ];
    }
}
?>
